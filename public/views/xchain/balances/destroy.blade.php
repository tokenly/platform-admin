@extends('platformAdmin::layouts.app')

@section('title_name') XChain Balance Deleted @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>XChain Balance Deleted</h1>
    </div>

    <p>{{ goodbyeInterjection() }} This balance entry was deleted.</p>

    <p style="margin-top: 6%;">
      <a class="button" href="{{ route('platform.admin.xchain.balances.index') }}">Return to Balances</a>
    </p>
</div>

@endsection

