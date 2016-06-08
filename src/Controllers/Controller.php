<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;


    // ------------------------------------------------------------------------
    

    protected function validateAndReturn(Request $request, $rules) {
        $this->validate($request, $rules);
        return $request->only(array_keys($rules));
    }

}
