<?php

namespace NavJobs\Transmit;

use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use League\Fractal\TransformerAbstract;

abstract class Transformer extends TransformerAbstract
{
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
            }, explode(',', $requestedIncludes));
        }

        $availableRequestedIncludes = array_intersect($this->getAvailableIncludes(), $requestedIncludes);
        $defaultIncludes = $this->getDefaultIncludes();

        return array_merge($availableRequestedIncludes, $defaultIncludes);
    }

    /**
     * Create a new item resource object.
     *
     * @param mixed                        $data
     * @param TransformerAbstract|callable $transformer
     * @param string                       $resourceKey
     *
     * @return Item
     */
    protected function item($data, $transformer, $resourceKey = null)
    {
        if (!$data) {
            return new NullResource();
        }

        return new Item($data, $transformer, $resourceKey);
    }
}
