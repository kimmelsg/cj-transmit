<?php

namespace NavJobs\Transmit\Traits;

trait Index
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
     {
         if ($this->shouldAuthorize) {
             $this->authorize('index', get_class($this->model));
         }

         return $this->respondWithPaginatedCollection($this->model);
     }
}
