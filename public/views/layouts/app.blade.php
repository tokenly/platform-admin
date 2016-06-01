@extends('platformAdmin::layouts.base')

@section('htmltitle')Platform Admin |@yield('title_name', 'Admin')@endsection

@section('body')

<header>
    <div>Tokenly Platform Administration</div>
</header>

@include('platformAdmin::includes.nav')

@yield('body_content')

@endsection

