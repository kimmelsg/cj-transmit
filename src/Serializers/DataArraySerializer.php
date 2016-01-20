<?php

namespace NavJobs\Transmit\Serializers;

use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\ArraySerializer as BaseArraySerializer;

class DataArraySerializer extends BaseArraySerializer
{
    /**
     * Serialize a collection.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
        if ($resourceKey === false) {
            return $data;
        }

        return $resourceKey ? [$resourceKey => $data] : ['data' => $data];
    }

    /**
     * Serialize an item.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function item($resourceKey, array $data)
    {
        if ($resourceKey === false) {
            return $data;
        }

        return $resourceKey ? [$resourceKey => $data] : ['data' => $data];
    }

    /**
     * @return array
     */
    public function null()
    {
        return [
            'data' => null,
        ];
    }
}
