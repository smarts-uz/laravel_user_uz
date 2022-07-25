@extends('layouts.app')
<!-- test -->
@section('content')
    <link rel="stylesheet" href="{{ asset ('/css/header.css') }}">


    @include('homepage.header')
    <main>
{{--        categories section--}}
        @include('homepage.categories')

{{--        cards section--}}
        @include('homepage.blogs')

{{--        How is this possible section--}}
        @include('homepage.home_economy')

{{--        advantages section--}}
        @include('homepage.advantages')


{{--        What is being ordered at Universal Service right now--}}
        @include('homepage.posts_section')
    </main>




    <script src="{{ asset('js/home.js') }}"></script>
@endsection
