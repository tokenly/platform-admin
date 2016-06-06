@extends('platformAdmin::layouts.app')

@section('title_name') Edit XChain Settings @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>Edit XChain Settings</h1>
    </div>

    @include('platformAdmin::includes.errors')

    {!! Form::open([
        'method' => 'POST',
        'route' => ['platform.admin.xchain.settings'],
    ]) !!}


    <div class="row">
        <div class="six columns">
            <label for="xchainMockActive">
                {!! Form::checkbox('xchainMockActive', 1, $form_vars['xchainMockActive'], ['id' => 'xchainMockActive']) !!}
                Enable Mock XChain Methods
            </label>
        </div>
    </div>


    <div class="row" style="margin-top: 3%;">
        <div class="three columns">
            {!! Form::submit('Save', ['class' => 'button-primary u-full-width']) !!}
        </div>
    </div>

    {!! Form::close() !!}

</div>

@endsection

