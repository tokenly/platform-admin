<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Illuminate\Http\Request;
use Tokenly\LaravelApiProvider\Contracts\APIUserRepositoryContract;
use Tokenly\PlatformAdmin\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(APIUserRepositoryContract $users_repository)
    {
        return view($this->view_base.'user.index', [
            'users' => $users_repository->findAll(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->view_base.'user.create', []);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, APIUserRepositoryContract $users_repository)
    {
        $rules = $this->createValidationRules();

        $request_attributes = $this->validateAndReturn($request, $rules);
        $create_vars = $request_attributes;
        $user = $users_repository->create($create_vars);

        return view($this->view_base.'user.store', [
            'model' => $user,
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, APIUserRepositoryContract $users_repository)
    {
        $user = $this->requireModelByID($id, $users_repository);

        \Illuminate\Support\Facades\Log::debug("\$this->view_base=".json_encode($this->view_base, 192));
        return view($this->view_base.'user.edit', [
            'model' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request, APIUserRepositoryContract $users_repository)
    {
        $user = $this->requireModelByID($id, $users_repository);

        $rules = $this->updateValidationRules();

        $request_attributes = $this->validateAndReturn($request, $rules);
        $update_vars = $request_attributes;

        // handle new password
        unset($update_vars['new_password']);
        if (isset($request_attributes['new_password']) AND strlen($request_attributes['new_password'])) {
            // this will be hashed by the repository
            $update_vars['password'] = $request_attributes['new_password'];
        }

        $update_vars['privileges'] = json_decode($update_vars['privileges'], true);

        // make confirmed_email null if blank
        if (isset($update_vars['confirmed_email']) AND !strlen($update_vars['confirmed_email'])) {
            unset($update_vars['confirmed_email']);
        }

        // update
        $users_repository->update($user, $update_vars);

        return view($this->view_base.'user.update', [
            'model' => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, APIUserRepositoryContract $users_repository)
    {
        $user = $this->requireModelByID($id, $users_repository);

        // delete
        $users_repository->delete($user);

        return view($this->view_base.'user.destroy', []);
    }

    // ------------------------------------------------------------------------
    
    protected function requireModelByID($id, APIUserRepositoryContract $repository) {
        $model = $repository->findById($id);
        if (!$model) { throw new HttpResponseException(response('Resource not found', 404)); }
        return $model;
    }

    protected function createValidationRules() {
        return [
            'name'       => 'sometimes|max:255',
            'username'   => 'required|max:255',
            'email'      => 'required|email|max:255',
            'password'   => 'required|max:255',
            'privileges' => 'sometimes|json|max:255',
        ];
    }

    protected function updateValidationRules() {
        return [
            'name'            => 'sometimes|max:255',
            'username'        => 'required|max:255',
            'email'           => 'required|email|max:255',
            'confirmed_email' => 'sometimes|email|max:255',
            'new_password'    => 'sometimes',
            'privileges'      => 'sometimes|json|max:255',
        ];
    }

}
