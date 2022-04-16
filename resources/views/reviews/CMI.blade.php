@extends('layouts.app')

@section('content')
    <div class="container w-4/5 mx-auto mt-12">
        <div class="flex lg:flex-row flex-col justify-center mt-6">
            @include('components.footerpage')
            <div class="lg:w-4/5 w-full text-base lg:mt-0 mt-4">
                @foreach ($medias as $media)
                <div class="mb-12">
                    @php
                        \Carbon\Carbon::setLocale('ru');
                    @endphp
                    <div class="italic text-gray-600">

                        {{ $media->created_at->format('d.m.Y') }} {{__('г')}}.
                    </div>
                    <h1 class="text-base md:text-lg">
                        <span class="text-red-500"> {{ $media->getTranslatedAttribute('title',Session::get('lang') , 'fallbackLocale') }}
                    </h1>
                    <p class="mt-4 text-base">
                        {{__('Совместно с Яндекс.Про провели')}}

                        <a class="text-blue-500 hover:text-black" href="/"> {{ $media->getTranslatedAttribute('description',Session::get('lang') , 'fallbackLocale') }}</a>

                        {{__('и узнали уровень дохода самозанятых, причины, по которым люди переходят на этот режим, а также основные плюсы и минусы, по мнению исполнителей. Кроме того, узнали главные факторы, благодаря которым самозанятые делают выбор в пользу платформенной занятости в России.')}}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection
