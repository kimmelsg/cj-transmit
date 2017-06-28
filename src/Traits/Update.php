<?php

namespace NavJobs\Transmit\Traits;

trait Update
{
    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->respondWithItem($this->model, function ($model) use ($id) {
            $item = $model->findOrFail($id);

            if ($this->shouldAuthorize) {
                $this->authorize('update', $item);
            }

            $item->fill(request()->all());
            $item->save();
            return $item;
        });
    }
}
