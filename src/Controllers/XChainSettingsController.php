<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tokenly\PlatformAdmin\Controllers\Controller;
use Tokenly\PlatformAdmin\Meta\PlatformAdminMeta;

class XChainSettingsController extends Controller
{


    public function edit()
    {
        return view('platformAdmin::xchain.settings.edit', [
            'form_vars' => PlatformAdminMeta::getMulti(array_keys($this->getValidationRules())),
        ]);
    }

    public function update(Request $request)
    {
        $request_attributes = $this->validateAndReturn($request, $this->getValidationRules());
        Log::debug("\$request_attributes=".json_encode($request_attributes, 192));

        PlatformAdminMeta::setMulti($request_attributes);

        return view('platformAdmin::xchain.settings.update', [
        ]);
    }

    // ------------------------------------------------------------------------

    protected function getValidationRules() {
        return [
            'xchainMockActive' => 'boolean',
        ];
    }    
}
