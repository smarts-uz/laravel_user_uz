@extends("layouts.app")

@section("content")

{{-- web search_task start--}}
@include('search_task.web_task_search')
{{-- web search_task end--}}

@include('search_task.mobile_task_search')


@endsection