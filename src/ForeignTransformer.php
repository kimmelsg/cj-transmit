<?php

namespace NavJobs\LaravelApi;

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
        return json_decode($json, true);
    }
}