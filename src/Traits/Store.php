<?php

namespace NavJobs\Transmit\Traits;

trait Store
{
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        if ($this->shouldAuthorize) {
            $this->authorize('create', get_class($this->model));
        }

        $item = $this->model->create(request()->all());
        return $this->respondWithItem($item);
    }
}
