<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Tokenly\LaravelApiProvider\Repositories\APIRepository;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;


    // ------------------------------------------------------------------------
    
    protected function requireModelByID($id, APIRepository $repository) {
        $model = $repository->findById($id);
        if (!$model) { throw new HttpResponseException(response('Resource not found', 404)); }
        return $model;
    }

    protected function validateAndReturn(Request $request, $rules) {
        $this->validate($request, $rules);
        return $request->only(array_keys($rules));
    }

}
