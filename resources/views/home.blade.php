@extends('layouts.app')
<!-- test -->
@section('content')
    <link rel="stylesheet" href="{{ asset ('/css/header.css') }}">

    @include('homepage.header')

    <main>

        @include('homepage.categories')

{{--        @include('homepage.blogs')--}}

{{--        @include('homepage.home_economy')--}}

{{--        @include('homepage.advantages')--}}

        @include('homepage.posts_section')

    </main>


    <script src="{{ asset('js/home.js') }}"></script>
@endsection
