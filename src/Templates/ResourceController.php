<?php

namespace NavJobs\Transmit\Templates;

use NavJobs\Transmit\Controller;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->respondWithPaginatedCollection();
    }
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
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->model->findOrFail($id)->delete();
        return $this->respondWithNoContent();
    }
}
