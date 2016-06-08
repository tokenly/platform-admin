<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use TKAccounts\Repositories\ProvisionalRepository;
use Tokenly\CurrencyLib\CurrencyUtil;
use Tokenly\PlatformAdmin\Controllers\Controller;
use Tokenly\PlatformAdmin\Meta\PlatformAdminMeta;

class PromisesController extends Controller
{


    public function index(ProvisionalRepository $promise_repository)
    {
        return view('platformAdmin::promise.index', [
            'promises' => $promise_repository->findAll(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $empty_promise = array_fill_keys(array_keys($this->createPromiseValidationRules()), '');
        Log::debug("\$empty_promise=".json_encode($empty_promise, 192));
        return view('platformAdmin::promise.create', ['promise' => $empty_promise]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ProvisionalRepository $promise_repository)
    {
        $request_attributes = $this->validateAndReturn($request, $this->createPromiseValidationRules());
        $create_vars = $request_attributes;

        // create the promise
        //setup the actual provisional transaction
        $date = date('Y-m-d H:i:s');
        $insert_data = [];
        $insert_data['source'] = $request_attributes['source'];
        $insert_data['destination'] = $request_attributes['destination'];
        $insert_data['asset'] = $request_attributes['asset'];
        $insert_data['quantity'] = CurrencyUtil::valueToSatoshis($request_attributes['quantity']);
        $insert_data['created_at'] = $date;
        $insert_data['updated_at'] = $date;
        $insert_data['pseudo'] = 0; //implement pseudo-tokens later

        // $insert_data['client_id'] = $valid_client->id;

        $promise = $promise_repository->create($insert_data);
        if (!$promise) {
            return $this->buildFailedValidationResponse($request, ['promise' => "Failed to create promise."]);
        }

        return view('platformAdmin::promise.store', ['promise' => $promise]);
    }

    public function edit($id)
    {
        return view('platformAdmin::promise.edit', [
            'model' => $this->requirePromiseByID($id),
        ]);
    }

    public function update($id, Request $request, ProvisionalRepository $promise_repository)
    {
        $promise = $this->requirePromiseByID($id);

        $request_attributes = $this->validateAndReturn($request, $this->createPromiseValidationRules());
        $update_vars = $request_attributes;
        $update_vars['quantity'] = CurrencyUtil::valueToSatoshis($update_vars['quantity']);

        // update
        $promise_repository->update($promise, $update_vars);

        return view('platformAdmin::promise.update', ['model' => $promise]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, ProvisionalRepository $promise_repository)
    {
        // delete
        $promise = $this->requirePromiseByID($id);

        // refresh all balance
        $promise_repository->delete($promise);

        return view('platformAdmin::promise.destroy', []);
    }
    // ------------------------------------------------------------------------


    protected function requirePromiseByID($id) {
        $promise_repository = app('TKAccounts\Repositories\ProvisionalRepository');

        $promise = $promise_repository->findById($id);
        if (!$promise) {
            throw new HttpResponseException(response('Promise not found', 404));
        }
        return $promise;
    }

    protected function createPromiseValidationRules() {
        return [
            'source'      => 'required|max:255',
            'destination' => 'required|max:255',
            'asset'       => 'required|max:255',
            'quantity'    => 'required|numeric',
        ];
    }    
}
