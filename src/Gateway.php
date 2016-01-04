<?php

namespace NavJobs\LaravelApi;

use Illuminate\Support\Facades\App;
use NavJobs\LaravelApi\Traits\QueryHelperTrait;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class Gateway
{
    use QueryHelperTrait;

    public function __construct()
    {
        $this->fractal = App::make(Fractal::class);
    }

    /**
     * Returns a json response that contains the specified resource
     * passed through fractal and optionally a transformer.
     *
     * @param $item
     * @param null $callback
     * @param null $resourceKey
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithItem($item, $callback = null, $resourceKey = null)
    {
        $rootScope = $this->fractal
            ->item($item, $callback, $resourceKey)
            ->serializeWith(new ArraySerializer());

        return json_encode($rootScope->toArray());
    }


    /**
     * Parses the incoming json parameters into a ParameterBag object.
     *
     * @param $parameters
     * @return ParameterBag
     */
    protected function parseParameters($parameters)
    {
        $parameters = new ParameterBag(json_decode($parameters, true));

        if ($parameters->get('include')) {
            $this->fractal->parseIncludes($parameters->get('include'));
        }

        return $parameters;
    }
}