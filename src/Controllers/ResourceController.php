<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tokenly\LaravelApiProvider\Filter\IndexRequestFilter;
use Tokenly\LaravelEventLog\Facade\EventLog;
use Tokenly\PlatformAdmin\Controllers\Controller;

abstract class ResourceController extends Controller
{

    protected $view_prefix      = null; // 'viewname' or 'platformAdmin::'
    protected $repository_class = null; // 'App\Repositories\MyRepository'

    protected $add_pagination_data = false;

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

        $resolved_view_prefix = $this->resolveViewPrefix($this->view_prefix);
        return view($resolved_view_prefix.'.index', $this->modifyViewData([
            'view_prefix'  => $resolved_view_prefix,
            'models'       => $this->resourceRepository()->findAll($filter),
        ], __FUNCTION__, ['filter' => $filter]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $empty_model = array_fill_keys(array_keys($this->createValidationRules()), '');
        $resolved_view_prefix = $this->resolveViewPrefix($this->view_prefix);
        return view($resolved_view_prefix.'.create', $this->modifyViewData([
            'view_prefix' => $resolved_view_prefix,
            'model'       => $empty_model,
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
        $request_attributes = $this->validateAndReturn($request, $this->createValidationRules());

        // modify
        $create_vars = $this->modifyVarsBeforeCreate($request_attributes);

        $model = $this->resourceRepository()->create($create_vars);
        if (!$model) {
            return $this->buildFailedValidationResponse($request, ['model' => "Failed to create model."]);
        }

        EventLog::debug('platformadmin.modelCreated', ['userId' => Auth::id(), 'modelType' => (new \ReflectionClass($model))->getShortName(), 'modelId' => $model['id'],]);

        $resolved_view_prefix = $this->resolveViewPrefix($this->view_prefix);
        return view($resolved_view_prefix.'.store', $this->modifyViewData(['view_prefix' => $resolved_view_prefix, 'model' => $model], __FUNCTION__));
    }

    public function edit($id)
    {
        $resolved_view_prefix = $this->resolveViewPrefix($this->view_prefix);
        return view($resolved_view_prefix.'.edit', $this->modifyViewData([
            'view_prefix' => $resolved_view_prefix,
            'model' => $this->requireModelByID($id),
        ], __FUNCTION__));
    }

    public function show($id)
    {
        $resolved_view_prefix = $this->resolveViewPrefix($this->view_prefix);
        return view($resolved_view_prefix.'.show', $this->modifyViewData([
            'view_prefix' => $resolved_view_prefix,
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

        $request_attributes = $this->validateAndReturn($request, $this->updateValidationRules());

        // modify
        $update_vars = $this->modifyVarsBeforeUpdate($request_attributes);

        // update
        $this->resourceRepository()->update($model, $update_vars);

        EventLog::debug('platformadmin.modelUpdated', ['userId' => Auth::id(), 'modelType' => (new \ReflectionClass($model))->getShortName(), 'modelId' => $model['id'], 'keys' => implode(',', array_keys($update_vars))]);

        $resolved_view_prefix = $this->resolveViewPrefix($this->view_prefix);
        return view($resolved_view_prefix.'.update', $this->modifyViewData(['view_prefix' => $resolved_view_prefix, 'model' => $model], __FUNCTION__));
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
        $this->resourceRepository()->delete($model);

        EventLog::debug('platformadmin.modelDestroyed', ['userId' => Auth::id(), 'modelType' => (new \ReflectionClass($model))->getShortName(), 'modelId' => $id,]);

        $resolved_view_prefix = $this->resolveViewPrefix($this->view_prefix);
        return view($resolved_view_prefix.'.destroy', $this->modifyViewData([], __FUNCTION__));
    }

    // ------------------------------------------------------------------------
    // override validation rules

    protected function createValidationRules() { return $this->getValidationRules(); }
    protected function updateValidationRules() { return $this->getValidationRules(); }
    protected function getValidationRules() {
        return [];
    }


    // ------------------------------------------------------------------------
    // override view variables

    protected function modifyViewData($view_data, $method_name, $meta=null) {
        // add pagination for index
        if ($method_name == 'index' AND $this->add_pagination_data) {
            $view_data = $this->addPaginationDataToViewData($view_data, $meta);
        }

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
    protected function modifyViewData_show($view_data, $meta=null) { }
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

    protected function resolveViewPrefix($view_prefix) {
        if (strpos($view_prefix, '::') !== false) {
            return $view_prefix;
        }
        return 'platformadmin.'.$view_prefix;
    }

    protected function resolveRoutePrefix($view_prefix) {
        $str_pos = strpos($view_prefix, '::');
        if ($str_pos !== false) {
            return 'platform.admin.'.substr($view_prefix, $str_pos + 2);
        }
        return 'platform.admin.'.$view_prefix;
    }

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
            'route_prefix'   => $this->resolveRoutePrefix($this->view_prefix),
        ];

        
    }



}
