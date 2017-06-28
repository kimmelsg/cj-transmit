<?php

namespace NavJobs\Transmit;

use Illuminate\Support\Facades\Input;
use Illuminate\Database\Eloquent\Builder;
use NavJobs\Transmit\Traits\QueryHelperTrait;
use NavJobs\Transmit\Traits\ErrorResponsesTrait;
use Illuminate\Routing\Controller as BaseController;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

abstract class Controller extends BaseController
{
    use QueryHelperTrait, ErrorResponsesTrait;

    protected $statusCode = 200;
    protected $resourceKey = null;
    protected $fractal, $transformer;

    public function __construct()
    {
        $this->fractal = app(Fractal::class);

        $this->parseIncludes();
    }

    /**
     * Sets the fractal transformer
     *
     * @param $transformer
     * @return mixed
     */
    public function setTransformer($transformer)
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * Sets resource key for fractal
     *
     * @param $resourceKey
     * @return mixed
     */
    public function setResourceKey($resourceKey)
    {
        $this->resourceKey = $resourceKey;

        return $this;
    }

    /**
     * Parses includes from either the header or query string.
     *
     * @return mixed
     */
    protected function parseIncludes()
    {
        if (Input::header('include')) {
            return $this->fractal->parseIncludes(Input::header('include'));
        }

        if (Input::get('include')) {
            return $this->fractal->parseIncludes(Input::get('include'));
        }

        return null;
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
     * Eager load any available includes and apply query parameters.
     *
     * @param $builder
     * @return mixed
     */
    protected function withIncludes($builder)
    {
        $includes = $this->transformer->getEagerLoads($this->fractal->getRequestedIncludes());
        $includedItems = $this->eagerLoadIncludes($builder, $includes);
        $this->applyParameters($includedItems, request()->query);

        return $builder;
    }

    /**
     * Returns a json response that contains the specified resource
     * passed through fractal and optionally a transformer.
     *
     * @param $item
     * @param null $callback
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithItem($item, $callback = null)
    {
        if ($callback) {
            $builder = $this->withIncludes($item);
            $item = $callback($builder);
        }

        $rootScope = $this->fractal->item($item, $this->transformer, $this->resourceKey);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Returns a json response that indicates the resource was successfully created also
     * returns the resource passed through fractal and optionally a transformer.
     *
     * @param $item
     * @param null $callback
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithItemCreated($item, $callback = null)
    {
        if ($callback) {
            $builder = $this->withIncludes($item);
            $item = $callback($builder);
        }

        $this->setStatusCode(201);
        $rootScope = $this->fractal->item($item, $this->transformer, $this->resourceKey);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Returns a json response that contains the specified collection
     * passed through fractal and optionally a transformer.
     *
     * @param $collection
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithCollection($collection)
    {
        if (is_a($collection, Builder::class)) {
            $collection = $this->withIncludes($collection);
        }

        $rootScope = $this->fractal->collection($collection, $this->transformer, $this->resourceKey);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Returns a json response that contains the specified paginated collection
     * passed through fractal and optionally a transformer.
     *
     * @param $builder
     * @param int $perPage
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithPaginatedCollection($builder, $perPage = 10)
    {
        $builder = $this->withIncludes($builder);

        $paginator = $builder->paginate($perPage);
        $paginator->appends($this->getQueryParameters());

        $rootScope = $this->fractal
            ->collection($paginator->getCollection(), $this->transformer, $this->resourceKey)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator));

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Returns an array of Query Parameters, not including pagination.
     *
     * @return array
     */
    protected function getQueryParameters()
    {
        return array_diff_key($_GET, array_flip(['page']));
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
        return response()->json('', 204);
    }
}
