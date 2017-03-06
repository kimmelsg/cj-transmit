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
        $item = $this->model->findOrFail($id);

        if ($this->shouldAuthorize) {
            $this->authorize('delete', $item);
        }

        $item->delete();
        return $this->respondWithNoContent();
    }
}
