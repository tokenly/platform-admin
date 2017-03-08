<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Exception;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tokenly\LaravelApiProvider\Filter\IndexRequestFilter;
use Tokenly\PlatformAdmin\Controllers\Controller;

abstract class ResourceController extends Controller
{

    protected $view_prefix      = null; // 'viewname';
    protected $repository_class = null; // 'App\Repositories\MyRepository';


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = null;
        $filter_definition = $this->buildIndexRequestFilterDefinition();
        if ($filter_definition) {
            $filter = IndexRequestFilter::createFromRequest($request, $filter_definition);
        }

        return view('platformadmin.'.$this->view_prefix.'.index', $this->modifyViewData([
            'models' => $this->resourceRepository()->findAll($filter),
        ], __FUNCTION__, ['filter' => $filter]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $empty_model = array_fill_keys(array_keys($this->getValidationRules()), '');
        return view('platformadmin.'.$this->view_prefix.'.create', $this->modifyViewData([
            'model' => $empty_model
        ], __FUNCTION__));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request_attributes = $this->validateAndReturn($request, $this->getValidationRules());

        // modify
        $create_vars = $this->modifyVarsBeforeCreate($request_attributes);

        $model = $this->resourceRepository()->create($create_vars);
        if (!$model) {
            return $this->buildFailedValidationResponse($request, ['model' => "Failed to create model."]);
        }

        return view('platformadmin.'.$this->view_prefix.'.store', $this->modifyViewData(['model' => $model], __FUNCTION__));
    }

    public function edit($id)
    {
        return view('platformadmin.'.$this->view_prefix.'.edit', $this->modifyViewData([
            'model' => $this->requireModelByID($id),
        ], __FUNCTION__));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $model = $this->requireModelByID($id);

        $request_attributes = $this->validateAndReturn($request, $this->getValidationRules());

        // modify
        $update_vars = $this->modifyVarsBeforeUpdate($request_attributes);

        // update
        $this->resourceRepository()->update($model, $update_vars);

        return view('platformadmin.'.$this->view_prefix.'.update', $this->modifyViewData(['model' => $model], __FUNCTION__));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // delete
        $model = $this->requireModelByID($id);

        // refresh all balance
        $this->resourceRepository()->delete($model);

        return view('platformadmin.'.$this->view_prefix.'.destroy', $this->modifyViewData([], __FUNCTION__));
    }

    // ------------------------------------------------------------------------
    // override validation rules

    protected function createValidationRules() { return $this->getValidationRules(); }
    protected function editValidationRules() { return $this->getValidationRules(); }
    abstract protected function getValidationRules();


    // ------------------------------------------------------------------------
    // override view variables

    protected function modifyViewData($view_data, $method_name, $meta=null) {
        $method_name = "modifyViewData_{$method_name}";
        if (method_exists($this, $method_name)) {
            return call_user_func([$this, $method_name], $view_data, $meta);
        }
        return $view_data;
    }

/*
    protected function modifyViewData_index($view_data, $meta=null) { }
    protected function modifyViewData_create($view_data, $meta=null) { }
    protected function modifyViewData_store($view_data, $meta=null) { }
    protected function modifyViewData_edit($view_data, $meta=null) { }
    protected function modifyViewData_update($view_data, $meta=null) { }
    protected function modifyViewData_delete($view_data, $meta=null) { }
*/

    protected function buildIndexRequestFilterDefinition() {
        return null;
    }

    // ------------------------------------------------------------------------
    // override create/update vars

    protected function modifyVarsBeforeCreate($create_vars) {
        return $create_vars;
    }

    protected function modifyVarsBeforeUpdate($update_vars) {
        return $update_vars;
    }



    // ------------------------------------------------------------------------

    protected function resourceRepository() {
        if (!isset($this->resource_repository)) {
            $this->resource_repository = app($this->repository_class);
        }
        return $this->resource_repository;
    }

    protected function requireModelByID($id) {
        $model = $this->resourceRepository()->findById($id);
        if (!$model) { throw new HttpResponseException(response('Resource not found', 404)); }
        return $model;
    }


    protected function addPaginationDataToViewData($view_data, $meta) {
        if ($meta AND isset($meta['filter'])) {
            $view_data['pagination'] = $this->buildPaginationData($meta['filter']);
        }
        return $view_data;
    }


    protected function buildPaginationData(IndexRequestFilter $filter) {
        $total_count = $filter->query->toBase()->getCountForPagination();

        return [
            'count'          => $total_count,
            'count_per_page' => $filter->used_limit,
            'offset'         => $filter->used_page_offset,
            'pages_count'    => ceil($total_count / $filter->used_limit),
        ];

        
    }



}
