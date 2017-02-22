<?php

namespace NavJobs\Transmit\Transmitters;

use NavJobs\Transmit\Controller;

trait Show
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->respondWithItem($this->model, function ($model) use ($id) {
            return $model->findOrFail($id);
        });
    }
}
