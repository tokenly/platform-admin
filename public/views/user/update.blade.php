@extends('platformAdmin::layouts.app')

@section('title_name') User Updated @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>User Updated</h1>
    </div>

    <p>User {{ $model['username'] }} was updated.</p>

    <p>
      <a class="button" href="{{ route('platform.admin.user.index') }}">Return to Users</a>
    </p>
</div>

@endsection

