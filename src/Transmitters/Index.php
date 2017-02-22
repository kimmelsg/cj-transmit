<?php

namespace NavJobs\Transmit\Transmitters;

use NavJobs\Transmit\Controller;

trait Index
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function index()
     {
         return $this->respondWithPaginatedCollection($this->model);
     }
}
