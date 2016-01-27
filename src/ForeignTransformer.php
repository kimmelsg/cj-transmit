<?php

namespace NavJobs\Transmit;

class ForeignTransformer extends Transformer
{
    /**
     * Turn this item object into a generic array
     *
     * @param $json
     * @return array
     */
    public function transform($json)
    {
        $data = json_decode($json, true);

        if (isset($data['data'])) {
            return $data['data'];
        }

        return $data;
    }
}