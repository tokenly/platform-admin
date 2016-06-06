@extends('platformAdmin::layouts.app')

@section('title_name') XChain Admin @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>XChain Admin</h1>
    </div>

    <p style="margin-top: 3%">
        <a href="{{ route('platform.admin.xchain.settings' )}}" class="button">Manage XChain Settings</a>
    </p>
    <p>
        <a href="{{ route('platform.admin.xchain.balances.index')}}" class="button">Manage XChain Balances</a>
    </p>
</div>


@endsection

