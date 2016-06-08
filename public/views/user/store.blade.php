@extends('platformAdmin::layouts.app')

@section('title_name') User Created @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>User Created</h1>
    </div>

    <p>{{ successInterjection() }} User {{ $model['username'] }} was created.</p>

    <p>
      <a class="button" href="{{ route('platform.admin.user.index') }}">Return to Users</a>
    </p>
</div>

@endsection

