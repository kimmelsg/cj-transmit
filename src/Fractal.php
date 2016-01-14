<?php

namespace NavJobs\Transmit;

use League\Fractal\Manager;
use League\Fractal\ParamBag;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Serializer\SerializerAbstract;
use NavJobs\Transmit\Exceptions\InvalidTransformation;
use NavJobs\Transmit\Exceptions\NoTransformerSpecified;

class Fractal
{
    /**
     * @var \League\Fractal\Manager
     */
    protected $manager;

    /**
     * @var \League\Fractal\Serializer\SerializerAbstract
     */
    protected $serializer;

    /**
     * @var \League\Fractal\TransformerAbstract|Callable
     */
    protected $transformer;

    /**
     * @var \League\Fractal\Pagination\PaginatorInterface
     */
    protected $paginator;

    /**
     * @var array
     */
    protected $includes = [];

    /**
     * Array containing modifiers as keys and an array value of params.
     *
     * @var array
     */
    protected $includeParams = [];

    /**
     * @var string
     */
    protected $dataType;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @param \League\Fractal\Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Set the collection data that must be transformed.
     *
     * @param mixed                                             $data
     * @param \League\Fractal\TransformerAbstract|Callable|null $transformer
     * @param string|null                                       $resourceName
     *
     * @return $this
     */
    public function collection($data, $transformer = null, $resourceName = null)
    {
        $this->resourceName = $resourceName;

        if ($transformer) {
            $this->transformWith($transformer);
        }

        return $this->data('collection', $data, $transformer);
    }

    /**
     * Set the item data that must be transformed.
     *
     * @param mixed                                             $data
     * @param \League\Fractal\TransformerAbstract|Callable|null $transformer
     * @param string|null                                       $resourceName
     *
     * @return $this
     */
    public function item($data, $transformer = null, $resourceName = null)
    {
        $this->resourceName = $resourceName;

        if ($transformer) {
            $this->transformWith($transformer);
        }

        return $this->data('item', $data, $transformer);
    }

    /**
     * Set the data that must be transformed.
     *
     * @param string                                            $dataType
     * @param mixed                                             $data
     * @param \League\Fractal\TransformerAbstract|Callable|null $transformer
     *
     * @return $this
     */
    protected function data($dataType, $data, $transformer = null)
    {
        $this->dataType = $dataType;

        $this->data = $data;

        if (!is_null($transformer)) {
            $this->transformer = $transformer;
        }

        return $this;
    }

    /**
     * Set the class or function that will perform the transform.
     *
     * @param \League\Fractal\TransformerAbstract|Callable $transformer
     *
     * @return $this
     */
    public function transformWith($transformer)
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * Set a Fractal paginator for the data.
     *
     * @param \League\Fractal\Pagination\PaginatorInterface $paginator
     *
     * @return $this
     */
    public function paginateWith(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * Specify the includes.
     *
     * @param array|string $includes Array or csv string of resources to include
     *
     * @return $this
     */
    public function parseIncludes($includes)
    {
        if (!$includes) {
            return $this;
        }

        if (is_string($includes)) {
            $includes = array_map(function ($value) {
                return trim($value);
            },  explode(',', $includes));
        }

        $this->includes = array_merge($this->includes, (array)$includes);
        $this->manager->parseIncludes($this->includes);
        $this->parseIncludeParams();

        return $this;
    }

    /**
     * @return $this|void
     */
    public function parseIncludeParams()
    {
        if (!$this->includes) {
            return $this;
        }

        foreach ($this->includes as $include) {
            list($includeName, $allModifiersStr) = array_pad(explode(':', $include, 2), 2, null);

            // No Params? Bored
            if ($allModifiersStr === null) {
                continue;
            }

            // Matches multiple instances of 'something(foo|bar|baz)' in the string
            // I guess it ignores : so you could use anything, but probably don't do that
            preg_match_all('/([\w]+)(\(([^\)]+)\))?/', $allModifiersStr, $allModifiersArr);

            // [0] is full matched strings...
            $modifierCount = count($allModifiersArr[0]);

            $modifierArr = [];

            for ($modifierIt = 0; $modifierIt < $modifierCount; $modifierIt++) {
                // [1] is the modifier
                $modifierName = $allModifiersArr[1][$modifierIt];

                // and [3] is delimited params
                // Make modifier array key with the parameter as the value
                $modifierArr[$modifierName] = $allModifiersArr[3][$modifierIt];
            }

            $this->includeParams[$includeName] = $modifierArr;
        }

        return $this;
    }

    /**
     * Get Include Params.
     *
     * @param string $include
     *
     * @return \League\Fractal\ParamBag|null
     */
    public function getIncludeParams($include)
    {
        if (! isset($this->includeParams[$include])) {
            return;
        }

        $params = $this->includeParams[$include];

        return new ParamBag($params);
    }

    /**
     * Support for magic methods to included data.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call($name, array $arguments)
    {
        if (method_exists($this->manager, $name)) {
            return call_user_func_array([$this->manager, $name], $arguments);
        }

        if (!starts_with($name, 'include')) {
            trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);
        }

        $includeName = lcfirst(substr($name, strlen('include')));

        return $this->parseIncludes($includeName);
    }

    /**
     * Set the serializer to be used.
     *
     * @param \League\Fractal\Serializer\SerializerAbstract $serializer
     *
     * @return $this
     */
    public function serializeWith(SerializerAbstract $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * Set the meta data.
     * @return $this
     * @internal param $array
     *
     */
    public function addMeta()
    {
        foreach (func_get_args() as $meta) {
            if (is_array($meta)) {
                $this->meta += $meta;
            }
        }

        return $this;
    }

    /**
     * Set the resource name, to replace 'data' as the root of the collection or item.
     *
     * @param string $resourceName
     *
     * @return $this
     */
    public function resourceName($resourceName)
    {
        $this->resourceName = $resourceName;

        return $this;
    }

    /**
     * Perform the transformation to json.
     *
     * @return string
     */
    public function toJson()
    {
        return $this->transform('toJson');
    }

    /**
     * Perform the transformation to array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->transform('toArray');
    }

    /**
     *  Perform the transformation.
     *
     * @param string $conversionMethod
     *
     * @return string|array
     */
    protected function transform($conversionMethod)
    {
        $fractalData = $this->createData();

        return $fractalData->$conversionMethod();
    }

    /**
     * Create fractal data.
     */
    public function createData()
    {
        if (is_null($this->transformer)) {
            throw new NoTransformerSpecified();
        }

        if (!is_null($this->serializer)) {
            $this->manager->setSerializer($this->serializer);
        }

        $resource = $this->getResource();

        return $this->manager->createData($resource);
    }

    /**
     * Get the resource.
     */
    public function getResource()
    {
        $resourceClass = 'League\\Fractal\\Resource\\'.ucfirst($this->dataType);

        if (!class_exists($resourceClass)) {
            throw new InvalidTransformation();
        }

        $resource = new $resourceClass($this->data, $this->transformer, $this->resourceName);

        $resource->setMeta($this->meta);

        if (!is_null($this->paginator)) {
            $resource->setPaginator($this->paginator);
        }

        return $resource;
    }
}
