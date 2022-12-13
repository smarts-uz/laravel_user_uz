@extends("layouts.app")

@section("content")

{{-- web search_task start--}}
@include('search_task.web_task_search')
{{-- web search_task end--}}
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<script src="https://api-maps.yandex.ru/2.1/?apikey=f4b34baa-cbd1-432b-865b-9562afa3fcdb&lang={{__('ru_RU')}}"
    type="text/javascript"></script>

@include('search_task.script')

@endsection
