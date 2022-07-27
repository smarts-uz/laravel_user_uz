@extends('layouts.app')

@section('content')

<div class="w-9/12 mx-auto mt-32 mb-64">

    <h1 class="text-4xl text-black font-bold">{{__('Правила сервиса')}}</h1>
    <div class="mt-24">
        <a target="_blank" href="{{ asset('storage/' . $filePath)}}" class="text-gray-500 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">
            {{__('Правила сервиса Universal Services')}}
        </a>
    </div>
</div>

@endsection
