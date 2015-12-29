<?php

namespace NavJobs\LaravelApi;

use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;
use NavJobs\LaravelApi\Exceptions\InvalidParameters;

abstract class Transformer extends TransformerAbstract
{
    /**
     * An array of allowed parameters.
     *
     * @var array
     */
    protected $validParameters = ['limit', 'sort'];

    /**
     * Apply valid parameters to the query builder.
     *
     * @param Illuminate\Database\Eloquent\Builder|Illuminate\Database\Eloquent\Relations\Relation $builder
     * @param ParamBag $parameters
     * @return mixed
     * @throws InvalidParameters
     */
    public function applyParameters($builder, ParamBag $parameters)
    {
        $this->validateParameters($parameters);

        if ($parameters['limit']) {
            list($limit, $offset) = $parameters->limit;
            $builder->take($limit)->skip($offset);
        }

        if ($parameters['sort']) {
            list($sort, $order) = $parameters->sort;
            $builder->orderBy($sort, $order);
        }

        return $builder;
    }

    /**
     * Checks if the provided parameters are valid.
     *
     * @param ParamBag $parameters
     * @return bool
     * @throws InvalidParameters
     */
    public function validateParameters(ParamBag $parameters)
    {
        $usedParameters = array_keys(iterator_to_array($parameters));
        if ($invalidParams = array_diff($usedParameters, $this->validParameters)) {
            throw new InvalidParameters(
                sprintf('Invalid param(s): "%s". Valid param(s): "%s"',
                    implode(',', $usedParameters),
                    implode(',', $this->validParameters)
                ));
        }

        return true;
    }

    /**
     * Returns the includes that are available for eager loading.
     *
     * @param array|string $requestedIncludes Array of csv string
     *
     * @return $this
     */
    public function getEagerLoads($requestedIncludes)
    {
        if (is_string($requestedIncludes)) {
            $requestedIncludes = array_map(function ($value) {
                return trim($value);
            },  explode(',', $requestedIncludes));
        }

        $availableRequestedIncludes = array_intersect($this->getAvailableIncludes(), $requestedIncludes);
        $defaultIncludes = $this->getDefaultIncludes();

        return array_merge($availableRequestedIncludes, $defaultIncludes);
    }
}