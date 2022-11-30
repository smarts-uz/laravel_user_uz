@extends('layouts.app')

@section('content')

<div class="w-9/12 mx-auto mt-32 mb-64">

    <h1 class="text-4xl text-black font-bold">{{__('Правила сервиса')}}</h1>
    <div class="mt-4">
        <p>
            {!! getContentText('terms', 'terms_text') !!}
        </p>
    </div>
</div>

@endsection
