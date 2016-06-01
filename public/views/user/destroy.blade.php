@extends('platformAdmin::layouts.app')

@section('title_name') Users @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>User Deleted</h1>
    </div>

    <p>This user was deleted.</p>

    <p>
      <a class="button" href="{{ route('platform.admin.user.index') }}">Return to Users</a>
    </p>
</div>

@endsection

