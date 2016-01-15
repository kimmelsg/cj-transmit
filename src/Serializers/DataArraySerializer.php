<?php

namespace NavJobs\Transmit\Serializers;

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
        return ['data' => $data];
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
        return ['data' => $data];
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
