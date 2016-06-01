@extends('platformAdmin::layouts.app')

@section('title_name') New User @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>Create a New User</h1>
    </div>

    @include('platformAdmin::includes.errors')

    {!! Form::open([
        'method' => 'POST',
        'route' => ['platform.admin.user.store'],
    ]) !!}


    <div class="row">
        <div class="six columns">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', null, ['placeholder' => 'Name', 'class' => 'u-full-width']) !!}
        </div>
        <div class="six columns">
            {!! Form::label('email', 'Email') !!}
            {!! Form::email('email', null, ['placeholder' => 'Email', 'class' => 'u-full-width']) !!}
        </div>
    </div>

    <div class="row">
        <div class="six columns">
            {!! Form::label('username', 'Username') !!}
            {!! Form::text('username', null, ['placeholder' => 'Username', 'class' => 'u-full-width']) !!}
        </div>
        <div class="six columns">
            {!! Form::label('password', 'Password') !!}
            {!! Form::password('password', ['placeholder' => 'Password', 'class' => 'u-full-width']) !!}
        </div>
    </div>



    <div class="row" style="margin-top: 3%;">
        <div class="three columns">
            {!! Form::submit('Create', ['class' => 'button-primary u-full-width']) !!}
        </div>
        <div class="six columns">&nbsp;</div>
        <div class="three columns">
            <a class="button u-full-width" href="{{ route('platform.admin.user.index') }}">Cancel</a>
        </div>
    </div>

    {!! Form::close() !!}

</div>

@endsection

