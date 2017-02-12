<?php

namespace NavJobs\Transmit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use NavJobs\Transmit\Traits\ErrorResponsesTrait;
use NavJobs\Transmit\Traits\QueryHelperTrait;
use Illuminate\Routing\Controller as BaseController;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

abstract class Controller extends BaseController
{
    use QueryHelperTrait, ErrorResponsesTrait;

    protected $statusCode = 200;
    protected $fractal;

    public function __construct()
    {
        $this->fractal = App::make(Fractal::class);

        $this->parseIncludes();
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

    private function prepareBuilder($builder)
    {
        $model = $builder ?: $this->model;

        $includes = $this->transformer->getEagerLoads($this->fractal->getRequestedIncludes());
        $includedItems = $this->eagerLoadIncludes($this->model, $includes);
        return $this->applyParameters($includedItems, request()->query);
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
    protected function respondWithItem($builder, $callback, $resourceKey = null)
    {
        $builder = $this->prepareBuilder($builder);

        $item = $callback($builder);

        $rootScope = $this->fractal->item($item, $this->transformer, $resourceKey);

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
     * Returns a json response that contains the specified paginated collection
     * passed through fractal and optionally a transformer.
     *
     * @param $builder
     * @param $callback
     * @param int $perPage
     * @param null $resourceKey
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithPaginatedCollection($builder = null, $perPage = 10, $resourceKey = null)
    {
        $builder = $this->prepareBuilder($builder);

        $paginator = $builder->paginate($perPage);
        $paginator->appends($this->getQueryParameters());

        $rootScope = $this->fractal
            ->collection($paginator->getCollection(), $this->transformer, $resourceKey)
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
        return response()->make('', 204);
    }
}
