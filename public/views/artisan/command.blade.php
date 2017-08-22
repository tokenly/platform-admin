@extends('platformAdmin::layouts.app')

@section('title_name') Artisan Command @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>Run an Artisan Command</h1>
    </div>

    @include('platformAdmin::includes.errors')

    {!! Form::open([
        'method' => 'POST',
        'route' => ['platform.admin.artisan.command'],
    ]) !!}


    <div class="row">
        <div class="twelve columns">
            {!! Form::label('command', 'Artisan Command') !!}
            {!! Form::textarea('command', $command, ['class' => 'u-full-width', 'style' => 'height: 60px;', 'placeholder' => 'route:list -h', ]) !!}
        </div>
    </div>

    <div class="row" style="margin-top: 3%;">
        <div class="three columns">
            {!! Form::submit('Execute', ['class' => 'button-primary u-full-width']) !!}
        </div>
        <div class="six columns">&nbsp;</div>
        <div class="three columns">
            <a class="button u-full-width" href="{{ route('platform.admin.user.index') }}">Cancel</a>
        </div>
    </div>

    {!! Form::close() !!}


    @if ($posted)
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <h4>Command Response</h4>
        <div><strong>Exit Status:</strong> {{$status}}</div>
        <pre style="border: 1px solid #eee; border-radius: 4px; padding: 8px 12px; background: #fcfcfc;">{{$response}}</pre>
    @endif

</div>

@endsection

