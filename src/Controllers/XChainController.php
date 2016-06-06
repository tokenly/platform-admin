<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tokenly\PlatformAdmin\Controllers\Controller;
use Tokenly\PlatformAdmin\Meta\PlatformAdminMeta;

class XChainController extends Controller
{


    public function index()
    {
        return view('platformAdmin::xchain.index');
    }
}
