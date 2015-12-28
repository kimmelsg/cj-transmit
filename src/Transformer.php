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
    protected $validParameters;

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