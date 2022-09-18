@extends('layouts.app')


@section('content')
    <div class="container w-4/5 mx-auto mt-12">
        <div class="flex lg:flex-row flex-col justify-center mt-6">
            @include('components.footerpage')
            <div class="lg:w-4/5 w-full text-base lg:mt-0 mt-4">
                <h1 class="text-normal lg:text-2xl pb-2 font-semibold">
                    {{__('Отзывы заказчиков о Universal Services')}}
                </h1>
                <p class="pb-5 md:text-base leading-lg">
                    {!! getContentText('customer_reviews', 'customer_reviews') !!}
                </p>
                @foreach($customer_reviews as $customer_review)
                    <div class="border border-solid border-2 rounded-md bg-pink-50">
                        <div class="px-8 py-6">
                            <a href="#" target="_blank" class="block float-left align-top w-16 h-16 overflow-hidden rounded-full shadow-lg border-b-0 mr-4">
                                <img class="rounded-full" src="{{ asset('storage/'.$customer_review->image) }}">
                            </a>
                            <a href="#" target="_blank" class="font-semibold">
                                {{$customer_review->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale')}}
                            </a>
                            <p class="text-base">
                                {{$customer_review->getTranslatedAttribute('text',Session::get('lang') , 'fallbackLocale')}}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


@endsection
