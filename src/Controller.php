<?php

namespace NavJobs\LaravelApi;

use Illuminate\Support\Facades\Input;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    const CODE_WRONG_ARGS = 'GEN-WRONG-ARGS';
    const CODE_NOT_FOUND = 'GEN-NOT-FOUND';
    const CODE_INTERNAL_ERROR = 'GEN-INTERNAL-ERROR';
    const CODE_UNAUTHORIZED = 'GEN-UNAUTHORIZED';
    const CODE_FORBIDDEN = 'GEN-FORBIDDEN';

    protected $statusCode = 200;

    public function __construct()
    {
        $this->fractal = App::make(Fractal::class);

        if (Input::get('include')) {
            $this->fractal->parseIncludes(Input::get('include'));
        }
    }

    /**
     * Returns the current status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets the current status code.
     *
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
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
        $rootScope = $this->fractal->item($item, $callback, $resourceKey);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Returns a json response that indicates the resource was successfully created also
     * returns the resource passed through fractal and optionally a transformer.
     *
     * @param $item
     * @param null $callback
     * @param null $resourceKey
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithItemCreated($item, $callback = null, $resourceKey = null)
    {
        $this->setStatusCode(201);
        $rootScope = $this->fractal->item($item, $callback, $resourceKey);

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
     * Returns a json response that contains the specified array,
     * the current status code and optional headers.
     *
     * @param array $array
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithArray(array $array, array $headers = [])
    {
        return response()->json($array, $this->statusCode, $headers);
    }

    /**
     * Returns a response that indicates success but no content returned.
     *
     * @return \Illuminate\Http\Response
     */
    protected function respondWithNoContent()
    {
        return response()->make('', 204);
    }

    /**
     * Returns a response that indicates an an error occurred.
     *
     * @param $message
     * @param $errorCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithError($message, $errorCode)
    {
        return $this->respondWithArray([
            'error' => [
                'code'      => $errorCode,
                'http_code' => $this->statusCode,
                'message'   => $message,
            ]
        ]);
    }

    /**
     * Returns a response that indicates a 403 Forbidden.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->respondWithError($message, self::CODE_FORBIDDEN);
    }

    /**
     * Returns a response that indicates an Internal Error has occurred.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)->respondWithError($message, self::CODE_INTERNAL_ERROR);
    }

    /**
     * Returns a response that indicates a 404 Not Found.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorNotFound($message = 'Resource Not Found')
    {
        return $this->setStatusCode(404)->respondWithError($message, self::CODE_NOT_FOUND);
    }

    /**
     * Returns a response that indicates a 401 Unauthorized.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->respondWithError($message, self::CODE_UNAUTHORIZED);
    }

    /**
     * Returns a response that indicates the wrong arguments were specified.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorWrongArgs($message = 'Wrong Arguments')
    {
        return $this->setStatusCode(400)->respondWithError($message, self::CODE_WRONG_ARGS);
    }
}