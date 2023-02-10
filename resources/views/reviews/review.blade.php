@extends('layouts.app')

@section('content')
    <div class="container w-4/5 mx-auto mt-12">
        <div class="flex lg:flex-row flex-col justify-center mt-6">
            @include('components.footerpage')
            <div class="lg:w-4/5 w-full text-base lg:mt-0 mt-4">
                <h1 class="text-3xl pb-2 font-semibold">
                    {{__('Отзывы исполнителей о Universal Services')}}
                </h1>
                <div class="text-base">
                    {!! App\Services\CustomService::getContentText('performer_review', 'performer_review') !!}
                </div>
                @foreach($performer_reviews as $performer_review)
                    <div class="border border-solid border-2 rounded-md bg-pink-50 my-3">
                        <div class="px-8 py-6">
                            <div class="block float-left align-top w-16 h-16 overflow-hidden rounded-full shadow-lg border-b-0 mr-4">
                                <img class="rounded-full" src="{{ asset('storage/'.$performer_review->image) }}">
                            </div>
                            <a target="_blank" href="{{$performer_review->site_link}}">
                                <div class="font-semibold">
                                    {{$performer_review->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale')}}
                                </div>
                                <p class="text-base">
                                    {{$performer_review->getTranslatedAttribute('text',Session::get('lang') , 'fallbackLocale')}}
                                </p>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection
