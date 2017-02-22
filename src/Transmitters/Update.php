<?php

namespace NavJobs\Transmit\Transmitters;

use NavJobs\Transmit\Controller;
use Illuminate\Http\Request;

trait Update
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        return $this->respondWithItem($this->model, function ($model) use ($id, $request) {
            $item = $model->findOrFail($id);
            $item->fill($request->all());
            $item->save();
            return $item;
        });
    }
}
