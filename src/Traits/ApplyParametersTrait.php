<?php

namespace NavJobs\LaravelApi\Traits;

trait ApplyParametersTrait {

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
            $this->sortBuilder($builder, $parameters);
        }

        if ($parameters->get('limit')) {
            $builder->take($parameters->get('limit'))->skip($parameters->get('offset'));
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

            $builder->orderBy($sortColumn, $sortDirection);
        }
    }
}