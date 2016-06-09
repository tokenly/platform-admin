<?php

namespace Tokenly\PlatformAdmin\TestHelper;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use \PHPUnit_Framework_Assert as PHPUnit;

class PlatformAdminTestHelper {

    public $url_base = '';
    public $route_prefix = '';

    protected $user = null;


    function __construct(Application $app) {
        $this->app = $app;
    }

    function setRoutePrefix($route_prefix) {
        $this->route_prefix = $route_prefix;
        return $this;
    }

    public function setRepository($repository) {
        $this->repository = $repository;
        return $this;
    }

    public function setCreateFunction($create_fn) {
        $this->create_fn = $create_fn;
        return $this;
    }

    public function beAuthorizedUser() {
        $user = app('UserHelper')->createNewUser([
            'username' => 'platformadmin',
            'privileges' => ['platformAdmin' => true],
        ]);
        $this->be($user);
        return $this;
    }

    public function be($user) {
        $this->user = $user;
        return $this;
    }

    public function getUser() {
        return $this->user;
    }

    public function testCreate($post_vars, $expected_vars=null) {
        $this->callHTTPRoute('GET', route('platform.admin.'.$this->route_prefix.'.create'));
        $result = $this->callHTTPRoute('POST', route('platform.admin.'.$this->route_prefix.'.store'), $post_vars);

        // get the last item
        $last_resource = collect($this->repository->findAll()->last()->first()->toArray());
        $expected_vars = $expected_vars === null ? $post_vars : $expected_vars;
        PHPUnit::assertEquals($expected_vars, $last_resource->only(array_keys($expected_vars))->toArray());
    }

    public function testUpdate($post_vars, $expected_vars=null) {
        if (!$this->create_fn) { throw new Exception("No create function defined", 1); }
        $item = $this->create_fn->__invoke();

        $this->callHTTPRoute('GET', route('platform.admin.'.$this->route_prefix.'.edit', [$item['id']]));
        $result = $this->callHTTPRoute('PATCH', route('platform.admin.'.$this->route_prefix.'.update', [$item['id']]), $post_vars);

        // get the last item
        $last_resource = collect($this->repository->findById($item['id'])->toArray());
        $expected_vars = $expected_vars === null ? $post_vars : $expected_vars;
        PHPUnit::assertEquals($expected_vars, $last_resource->only(array_keys($expected_vars))->toArray());
    }

    public function testDelete() {
        if (!$this->create_fn) { throw new Exception("No create function defined", 1); }
        $item = $this->create_fn->__invoke();

        $result = $this->callHTTPRoute('DELETE', route('platform.admin.'.$this->route_prefix.'.destroy', [$item['id']]), []);

        // get the last item
        PHPUnit::assertEmpty($this->repository->findById($item['id']));
    }

    public function callHTTPRoute($method, $uri_or_url_extension, $parameters = [], $expected_response_code=200, $cookies = [], $files = [], $server = [], $content = null) {
        if (substr($uri_or_url_extension, 0, 1) == '/' or substr($uri_or_url_extension, 0, 7) == 'http://' ) {
            $uri = $uri_or_url_extension;
        } else {
            $uri = $this->extendURL($this->url_base, $uri_or_url_extension);
        }

        if ($this->user) {
            Auth::setUser($this->user);
        }

        $request = $this->createRequest($method, $uri, $parameters, $cookies, $files, $server, $content);
        $response = $this->runRequest($request);

        $errors = [];
        $error_bag = Session::get('errors');
        if ($error_bag) {
            $errors = $error_bag->all();
        }

        PHPUnit::assertEquals($expected_response_code, $response->getStatusCode(), "Errors were: ".json_encode($errors, 192)."\nResponse was: ".$response->getContent());

        return $response->getContent();
    }


    protected function createRequest($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null) {
        return Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);
    }


    ////////////////////////////////////////////////////////////////////////
    

    protected function extendURL($base_url, $url_extension) {
        return $base_url.(strlen($url_extension) ? '/'.ltrim($url_extension, '/') : '');
    }

    protected function runRequest($request) {
        $kernel = $this->app->make('Illuminate\Contracts\Http\Kernel');
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);
        return $response;
    }

}
