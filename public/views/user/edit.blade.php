@extends('platformAdmin::layouts.app')

@section('title_name') Edit User @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>Edit User {{ $model['username'] }}</h1>
    </div>

    @include('platformAdmin::includes.errors')


    {!! Form::model($model, [
        'method' => 'PATCH',
        'route' => ['platform.admin.user.update', $model['id']],
    ]) !!}

    <div class="row">
        <div class="six columns">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', null, ['placeholder' => 'Name', 'class' => 'u-full-width']) !!}
        </div>
    </div>

    <div class="row">
        <div class="six columns">
            {!! Form::label('email', 'Email') !!}
            {!! Form::text('email', null, ['class' => 'u-full-width']) !!}
        </div>
        <div class="six columns">
            {!! Form::label('confirmed_email', 'Confirmed Email') !!}
            {!! Form::text('confirmed_email', null, ['placeholder' => 'Copy the email here to mark confirmed', 'class' => 'u-full-width']) !!}
        </div>
    </div>


    <div class="row">
        <div class="six columns">
            {!! Form::label('username', 'Username') !!}
            {!! Form::text('username', null, ['class' => 'u-full-width']) !!}
        </div>
        <div class="six columns">
            {!! Form::label('new_password', 'Reset Password') !!}
            {!! Form::text('new_password', null, ['placeholder' => 'Enter a new password here to reset', 'class' => 'u-full-width']) !!}
        </div>
    </div>



    <div class="row" style="margin-top: 3%;">
        <div class="three columns">
            {!! Form::submit('Update', ['class' => 'button-primary u-full-width']) !!}
        </div>
        <div class="six columns">&nbsp;</div>
        <div class="three columns">
            <a class="button u-full-width" href="{{ route('platform.admin.user.index') }}">Cancel</a>
        </div>
    </div>

    {!! Form::close() !!}

</div>

@endsection

