<?php

namespace NavJobs\Transmit\Transmitters;

use NavJobs\Transmit\Controller;

trait Destroy
{
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
