@extends('platformAdmin::layouts.app')

@section('title_name') XChain Settings Updated @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>XChain Settings Updated</h1>
    </div>

    <p>That's right. The settings were saved.</p>

    <p style="margin-top: 6%;">
      <a class="button" href="{{ route('platform.admin.xchain') }}">Return to XChain Admin</a>
    </p>
</div>

@endsection

