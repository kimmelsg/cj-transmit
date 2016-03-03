<?php

namespace NavJobs\Transmit;

use League\Fractal\ParamBag;
use Illuminate\Support\Facades\App;
use NavJobs\Transmit\Traits\QueryHelperTrait;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class Gateway
{
    use QueryHelperTrait;

    protected $statusCode = 200;
    protected $fractal;

    public function __construct()
    {
        $this->fractal = App::make(Fractal::class);
    }

    /**
     * Returns the current status code.
     *
     * @return int
     */
    protected function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets the current status code.
     *
     * @param $statusCode
     * @return $this
     */
    protected function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
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
            ->item($item, $callback, $resourceKey);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Returns a json response that contains the specified collection
     * passed through fractal and optionally a transformer.
     *
     * @param $collection
     * @param $callback
     * @param null $resourceKey
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithCollection($collection, $callback, $resourceKey = null)
    {
        $rootScope = $this->fractal->collection($collection, $callback, $resourceKey);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Parses the incoming json parameters into a ParameterBag object.
     *
     * @param $parameters
     * @return ParameterBag
     */
    protected function parseParameters($parameters)
    {
        if (!($parameters instanceof ParameterBag) && !($parameters instanceof ParamBag)) {
            $parameters = new ParameterBag(json_decode($parameters, true));
        }

        if ($parameters->get('with')) {
            $this->fractal->parseIncludes($parameters->get('with'));
        }

        return $parameters;
    }

    /**
     * Returns a json response that contains the specified array,
     * and the current status code.
     *
     * @param array $array
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithArray(array $array)
    {
        return json_encode(array_merge($array, ['status_code' => $this->statusCode]));
    }

    /**
     * Returns a response that indicates a 404 Not Found.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorNotFound($message = 'Resource Not Found')
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }

    /**
     * Returns a response that indicates an an error occurred.
     *
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithError($message)
    {
        return $this->respondWithArray([
            'error' => [
                'status_code' => $this->statusCode,
                'message'   => $message,
            ]
        ]);
    }
}