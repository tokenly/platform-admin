@extends('platformAdmin::layouts.base')

@section('htmltitle')Platform Admin |@yield('title_name', 'Admin')@endsection

@section('body')

<header>

    @include('platformAdmin::includes.nav')

</header>

@yield('body_content')

@endsection

