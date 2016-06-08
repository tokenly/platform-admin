@extends('platformAdmin::layouts.app')

@section('title_name') XChain Balances Updated @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>XChain Balances Updated</h1>
    </div>

    <p>{{ successInterjection() }} The balances were updated.</p>

    <p style="margin-top: 6%;">
      <a class="button" href="{{ route('platform.admin.xchain.balances.index') }}">Return to Balances</a>
    </p>
</div>

@endsection

