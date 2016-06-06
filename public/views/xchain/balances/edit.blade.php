@extends('platformAdmin::layouts.app')

@section('title_name') Edit Balance @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>Edit Balance</h1>
    </div>

    @include('platformAdmin::includes.errors')


    {!! Form::open([
        'method' => 'POST',
        'route' => ['platform.admin.xchain.balances.update', $balance_entry['id']],
    ]) !!}

    <div class="row">
        <div class="six columns">
            {!! Form::label('address', 'Address') !!}
            <p>{{ $balance_entry['address'] }}</p>
        </div>
    </div>

    <div class="row">
        <div class="twelve columns">
            {!! Form::label('balances', 'Balances') !!}
            {!! Form::textarea('balances', json_encode($balance_entry['balances'], 192), ['class' => 'u-full-width', 'style' => 'height: 200px;']) !!}
        </div>
    </div>



    <div class="row" style="margin-top: 3%;">
        <div class="three columns">
            {!! Form::submit('Update', ['class' => 'button-primary u-full-width']) !!}
        </div>
        <div class="six columns">&nbsp;</div>
        <div class="three columns">
            <a class="button u-full-width" href="{{ route('platform.admin.xchain.balances.index') }}">Cancel</a>
        </div>
    </div>

    {!! Form::close() !!}

</div>

@endsection

