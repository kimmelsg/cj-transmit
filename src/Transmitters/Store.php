<?php

namespace NavJobs\Transmit\Transmitters;

use NavJobs\Transmit\Controller;

trait Store
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $item = $this->model->create(request()->all());
        return $this->respondWithItem($item);
    }
}
