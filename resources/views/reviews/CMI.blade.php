@extends('layouts.app')

@section('content')
    <div class="container w-4/5 mx-auto mt-12">
        <div class="flex lg:flex-row flex-col justify-center mt-6">
            @include('components.footerpage')
            <div class="lg:w-4/5 w-full text-base lg:mt-0 mt-4">
                @foreach ($medias as $media)
                <div class="mb-12">
                    <div class="italic text-gray-600">
                        {{ $media->created_at->format('d.m.Y') }} {{__('г')}}.
                    </div>
                    <a target="_blank" href="{{ $media->link }}" class="text-red-500 hover:text-blue-500 text-base md:text-lg">
                        {{ $media->getTranslatedAttribute('title',Session::get('lang') , 'fallbackLocale') }}
                    </a>
                    <p class="mt-4 text-base text-black">
                        {{ $media->getTranslatedAttribute('description',Session::get('lang') , 'fallbackLocale') }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection
