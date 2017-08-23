@extends('platformAdmin::layouts.base')

@section('htmltitle')@yield('title_name', 'Admin') | Platform Admin @endsection

@section('body')

<header>

    @include('platformAdmin::includes.nav')

</header>

@yield('body_content')

@endsection

