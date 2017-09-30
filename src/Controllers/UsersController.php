<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Illuminate\Http\Request;
use Tokenly\PlatformAdmin\Controllers\ResourceController;

class UsersController extends ResourceController
{

    protected $view_prefix      = 'platformAdmin::user';
    protected $repository_class = 'Tokenly\LaravelApiProvider\Contracts\APIUserRepositoryContract';
    protected $add_pagination_data = true;


    protected function modifyVarsBeforeCreate($create_vars) {
        $create_vars['privileges'] = json_decode($create_vars['privileges'], true);
        return $create_vars;
    }

    protected function modifyVarsBeforeUpdate($update_vars) {
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

        return $update_vars;
    }


    // ------------------------------------------------------------------------
    

    protected function buildIndexRequestFilterDefinition($meta=null) {
        return [
            'fields' => [
                'name'       => ['field' => 'name',     'allowLike' => true, 'assumeLike' => true,],
                'username'   => ['field' => 'username', 'allowLike' => true, 'assumeLike' => true,],
                'email'      => ['field' => 'email',    'allowLike' => true, 'assumeLike' => true,],
                'created_at' => ['sortField' => 'created_at', 'defaultSortDirection' => 'desc'],
            ],
            'limit' => [
                'field'       => 'limit',
                'max'         => 20, 
                'pagingField' => 'pg',
            ],
            'defaults' => ['sort' => 'created_at',]
        ];
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
