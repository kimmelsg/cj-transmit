<?php

namespace NavJobs\LaravelApi\Traits;

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
        foreach ($includes as $include) {
            if (!$model->include) {
                continue;
            }

            $model = $model->with([
                $include => function ($query) use ($include) {
                    $parameters = $this->fractal ? $this->fractal->getIncludeParams($include) : null;
                    $query = $this->applyParameters($query, $parameters);

                    return $query;
                }
            ]);
        }

        return $model;
    }

    /**
     * Apply query parameters to the supplied query builder.
     *
     * @param $builder
     * @param Symfony\Component\HttpFoundation\ParameterBag|League\Fractal\ParamBag $parameters
     * @return mixed
     */
    protected function applyParameters($builder, $parameters)
    {
        if (!$parameters) {
            return $builder;
        }

        if ($parameters->get('sort')) {
            $builder = $this->sortBuilder($builder, $parameters);
        }

        if ($parameters->get('limit')) {
            $builder = $builder->take($parameters->get('limit'))->skip($parameters->get('offset'));
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
}