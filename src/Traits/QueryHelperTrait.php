<?php

namespace NavJobs\Transmit\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;

trait QueryHelperTrait {

    /**
     * Eager loads the provided includes on the specified model.
     * If the class has a fractal instance, will also use include params.
     *
     * @param $model
     * @param $includes
     * @return mixed
     */
    protected function eagerLoadIncludes($model, $includes)
    {
        $builder = $model;

        foreach ($includes as $include) {
            if (!$model->$include) {
                continue;
            }

            $builder = $builder->with([
                $include => function ($query) use ($include) {
                    $parameters = $this->fractal ? $this->fractal->getIncludeParams($include) : null;
                    $this->applyParameters($query, $parameters);
                }
            ]);
        }

        return $builder;
    }

    /**
     * Apply query parameters to the supplied query builder.
     *
     * @param $builder
     * @param Symfony\Component\HttpFoundation\ParameterBag|League\Fractal\ParamBag $parameters
     * @return mixed
     */
    protected function applyParameters($builder, $parameters = null)
    {
        if (!$parameters) {
            return $builder;
        }

        if ($parameters->get('sort')) {
            $builder = $this->sortBuilder($builder, $parameters);
        }


        if ($parameters->get('limit')) {
            $builder = $this->limitBuilder($builder, $parameters);
        }

        return $builder;
    }

    /**
     * Applies sorts to the Builder.
     *
     * @param $builder
     * @param $parameters
     */
    protected function sortBuilder($builder, $parameters)
    {
        $sorts = explode(',', str_replace('|', ',', $parameters->get('sort')));

        foreach ($sorts as $sort) {
            $sortDirection = str_contains($sort, '-') ? 'desc' : 'asc';
            $sortColumn = ltrim($sort, '-');

            $builder = $builder->orderBy($sortColumn, $sortDirection);
        }

        return $builder;
    }

    /**
     * Applies limits to the Builder.
     *
     * @param $builder
     * @param $parameters
     */
    public function limitBuilder($builder, $parameters)
    {
        if (is_a($builder, Relation::class)) {
            return $builder;
        }

        return $builder->take($parameters->get('limit'))->skip($parameters->get('offset'));
    }
}