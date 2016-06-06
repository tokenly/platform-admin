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
        return view('platformAdmin::user.index', [
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
        return view('platformAdmin::user.create', []);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, APIUserRepositoryContract $users_repository)
    {
        $rules = [
            'name'     => 'sometimes|max:255',
            'username' => 'required|max:255',
            'email'    => 'required|email|max:255',
            'password' => 'required|max:255',
        ];

        $request_attributes = $this->validateAndReturn($request, $rules);
        $create_vars = $request_attributes;

        $user = $users_repository->create($create_vars);

        return view('platformAdmin::user.store', [
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

        return view('platformAdmin::user.edit', [
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

        $rules = [
            'name'            => 'sometimes|max:255',
            'username'        => 'required|max:255',
            'email'           => 'required|email|max:255',
            'confirmed_email' => 'sometimes|email|max:255',
            'new_password'    => 'sometimes',
        ];

        $request_attributes = $this->validateAndReturn($request, $rules);
        $update_vars = $request_attributes;

        // handle new password
        unset($update_vars['new_password']);
        if (isset($request_attributes['new_password']) AND strlen($request_attributes['new_password'])) {
            // this will be hashed by the repository
            $update_vars['password'] = $request_attributes['new_password'];
        }

        // update
        $users_repository->update($user, $update_vars);

        return view('platformAdmin::user.update', [
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

        return view('platformAdmin::user.destroy', []);
    }

    // ------------------------------------------------------------------------
    
}
