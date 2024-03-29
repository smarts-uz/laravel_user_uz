@extends('layouts.app')


@section('content')
        {{--  Header  --}}
    <div style="background-image: url({{ App\Services\CustomService::getContentImage('verification', 'verification_header') }})"
         class="h-screen bg-no-repeat bg-cover mb-32">
        <div class="text-center my-auto pt-48">
            {!! App\Services\CustomService::getContentText('verification', 'verification_header') !!}
            @if(auth()->user()->role_id === \App\Models\User::ROLE_PERFORMER)
                <button  class="px-10 py-4 font-sans  text-lg mt-8 font-semibold bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-xl">
                    {{__('СТАТЬ ИСПОЛНИТЕЛЕМ')}}
                </button>
            @else
                <a href="{{ route('profile.verificationInfo') }}">
                    <button  class="px-10 py-4 font-sans  text-lg mt-8 font-semibold bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-xl">
                        {{__('СТАТЬ ИСПОЛНИТЕЛЕМ')}}
                    </button>
                </a>
            @endif
        </div>
    </div>

        {{--    Advantages    --}}
    <div class="container mx-auto px-2">
        <div class="w-10/12 mx-auto text-center mb-16">
            {!! App\Services\CustomService::getContentText('verification', 'Advantages_title') !!}
            <div class="grid md:grid-cols-4 grid-cols-1 gap-4 pt-16 container mx-auto font-bold text-xl">
                <div>
                    <img class="mx-auto h-36" src="{{ App\Services\CustomService::getContentImage('verification', 'Advantages_img_1') }}" alt="#">
                    <p class="text-xl">{!! App\Services\CustomService::getContentText('verification', 'Advantages_img_1') !!}</p>
                </div>
                <div>
                    <img class="mx-auto h-36" src="{{ App\Services\CustomService::getContentImage('verification', 'Advantages_img_2') }}" alt="#">
                    <p class="text-xl">{!! App\Services\CustomService::getContentText('verification', 'Advantages_img_2') !!}</p>
                </div>
                <div>
                    <img class="mx-auto h-36" src="{{ App\Services\CustomService::getContentImage('verification', 'Advantages_img_3') }}" alt="#">
                    <p class="text-xl">{!! App\Services\CustomService::getContentText('verification', 'Advantages_img_3') !!}</p>
                </div>
                <div>
                    <img class="mx-auto h-36" src="{{ App\Services\CustomService::getContentImage('verification', 'Advantages_img_4') }}" alt="#">
                    <p class="text-xl">{!! App\Services\CustomService::getContentText('verification', 'Advantages_img_4') !!}</p>
                </div>
            </div>
        </div>
            {{--   to order process   --}}
        <div class="w-9/12 mx-auto text-center font-serif my-32">
            <div class="info">
                {!! App\Services\CustomService::getContentText('verification', 'to_order_process') !!}
                <div class="grid lg:grid-cols-5 grid-cols-1 items-center">
                    <div>
                        {!! App\Services\CustomService::getContentText('verification', 'to_order_1') !!}
                    </div>
                    <div>
                        <img class="lg:block hidden  w-10/12 shrink" src="{{asset('images/arrow.svg')}}" alt="">
                    </div>
                    <div>
                        {!! App\Services\CustomService::getContentText('verification', 'to_order_2') !!}
                    </div>
                    <div>
                        <img class="lg:block hidden  w-10/12 shrink" src="{{asset('images/arrow.svg')}}" alt="">
                    </div>
                    <div>
                        {!! App\Services\CustomService::getContentText('verification', 'to_order_3') !!}
                    </div>
                </div>
            </div>
            @if(auth()->user()->role_id === \App\Models\User::ROLE_PERFORMER)
                <button  class="px-10 py-4 font-sans  text-lg mt-8 font-semibold bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-xl">
                    {{__('СТАТЬ ИСПОЛНИТЕЛЕМ')}}
                </button>
            @else
                <a href="{{ route('profile.verificationInfo') }}">
                    <button  class="px-10 py-4 font-sans  text-lg mt-8 font-semibold bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-xl">
                        {{__('СТАТЬ ИСПОЛНИТЕЛЕМ')}}
                    </button>
                </a>
            @endif
        </div>


        {{-- first performer --}}
{{--        <div class="flex lg:flex-row flex-col container mx-auto">--}}
{{--            <div class="lg:w-3/5 w-full">--}}
{{--                <img class="lg:mx-0 mx-auto h-9/12 w-full" src="{{ getContentImage('verification', 'first_performer') }}" alt="#">--}}
{{--            </div>--}}
{{--            <div class="lg:w-2/5 w-full lg:text-left text-center lg:mt-0 mt-4 lg:ml-8">--}}
{{--                {!! getContentText('verification', 'first_performer') !!}--}}
{{--            </div>--}}
{{--        </div>--}}

        {{-- second performer --}}
{{--        <div class="flex lg:flex-row flex-col container mx-auto my-16">--}}
{{--            <div class="lg:w-2/5 w-full lg:block hidden lg:text-left text-center">--}}
{{--                {!! getContentText('verification', 'second_performer') !!}--}}
{{--            </div>--}}
{{--            <div class="lg:w-3/5 w-full lg:block hidden">--}}
{{--                <img class="ml-4 xl:float-right float-none h-9/12 w-full" src="{{getContentImage('verification', 'second_performer')}}" alt="#">--}}
{{--            </div>--}}

{{--            <div class=" lg:hidden block ">--}}
{{--                <img class="lg:mx-0 mx-auto h-9/12 w-full" src="{{getContentImage('verification', 'second_performer')}}" alt="#">--}}
{{--            </div>--}}
{{--            <div class="lg:hidden block lg:text-left text-center lg:mt-0 mt-4">--}}
{{--                {!! getContentText('verification', 'second_performer') !!}--}}
{{--            </div>--}}
{{--        </div>--}}

        {{-- third performer --}}
{{--        <div class="flex lg:flex-row flex-col container mx-auto">--}}
{{--            <div class="lg:w-3/5 w-full">--}}
{{--                <img class="lg:mx-0 mx-auto h-9/12 w-full" src="{{getContentImage('verification', 'third_performer')}}" alt="#">--}}
{{--            </div>--}}
{{--            <div class="lg:w-2/5 w-full lg:text-left text-center lg:mt-0 mt-4 lg:ml-8">--}}
{{--                {!! getContentText('verification', 'third_performer') !!}--}}
{{--            </div>--}}
{{--        </div>--}}

        {{-- fourth performer --}}
{{--        <div class="flex lg:flex-row flex-col container mx-auto my-16">--}}
{{--            <div class="lg:w-2/5 w-full lg:block hidden lg:text-left text-center">--}}
{{--                {!! getContentText('verification', 'fourth_performer') !!}--}}
{{--            </div>--}}
{{--            <div class="lg:w-3/5 w-full lg:block hidden">--}}
{{--                <img class="ml-4 xl:float-right float-none h-9/12 w-full" src="{{getContentImage('verification', 'fourth_performer')}}" alt="#">--}}
{{--            </div>--}}

{{--            <div class="lg:hidden block">--}}
{{--                <img class="lg:mx-0 mx-auto h-9/12 w-full" src="{{getContentImage('verification', 'fourth_performer')}}" alt="#">--}}
{{--            </div>--}}
{{--            <div class="lg:hidden block lg:text-left text-center lg:mt-0 mt-4">--}}
{{--                {!! getContentText('verification', 'fourth_performer') !!}--}}
{{--            </div>--}}
{{--        </div>--}}


            {{--     video section  first  --}}
{{--        <div class="text-center my-16">--}}
{{--            {!! getContentText('verification', 'video_section_title') !!}--}}
{{--        </div>--}}

{{--        <div class="flex lg:flex-row flex-col container mx-auto">--}}
{{--            <div class="lg:w-1/2 w-full h-96">--}}
{{--                {!! getContentText('verification', 'youtobe_video_1') !!}--}}
{{--            </div>--}}
{{--            <div class="lg:w-1/2 w-full lg:mt-0 mt-8 lg:text-left text-center">--}}
{{--                {!! getContentText('verification', 'video_section_text1') !!}--}}
{{--            </div>--}}
{{--        </div>--}}

            {{--  modal section --}}
{{--        <div class="grid md:grid-cols-3 grid-cols-1 container mx-auto  md:my-32 my-16">--}}
{{--            <div class="grid-cols-1 shadow-2xl p-8 rounded-lg m-4 ">--}}
{{--                <p class="text-base">--}}
{{--                    {{substr( getContentText('verification', 'modal_section_1'),0,100)}}--}}
{{--                </p>--}}
{{--                <button onclick="toggleModal8('modal-id8')" class="text-base text-gray-500 hover:text-yellow-400 hover:border-yellow-400 border-solid border-b-2">{{__('Читать дальше')}}</button>--}}
{{--            </div>--}}
{{--            <div class="grid-cols-1 shadow-2xl p-8 rounded-lg m-4">--}}
{{--                <p class="text-base">--}}
{{--                    {{substr( getContentText('verification', 'modal_section_2'),0,100)}}--}}
{{--                </p>--}}
{{--                <button onclick="toggleModal9('modal-id9')" class="text-base text-gray-500 hover:text-yellow-400 hover:border-yellow-400 border-solid border-b-2">{{__('Читать дальше')}}</button>--}}
{{--            </div>--}}
{{--            <div class="grid-cols-1 shadow-2xl p-8 rounded-lg m-4">--}}
{{--                <p class="text-base">--}}
{{--                    {{substr( getContentText('verification', 'modal_section_3'),0,100)}}--}}
{{--                </p>--}}
{{--                <button onclick="toggleModal10('modal-id10')" class="text-base text-gray-500 hover:text-yellow-400 hover:border-yellow-400 border-solid border-b-2">{{__('Читать дальше')}}</button>--}}
{{--            </div>--}}
{{--        </div>--}}

        {{--     video section  second    --}}
{{--        <div class="flex lg:flex-row flex-col container mx-auto my-16">--}}
{{--            <div class="lg:w-1/2 w-full lg:block hidden lg:text-left text-center xl:ml-0 ml-4">--}}
{{--                {!! getContentText('verification', 'video_section_text2') !!}--}}
{{--            </div>--}}
{{--            <div class="lg:w-1/2 w-full h-96 lg:block hidden ml-8">--}}
{{--                {!! getContentText('verification', 'youtobe_video_2') !!}--}}
{{--            </div>--}}

{{--            <div class="lg:hidden block mx-auto w-full h-96">--}}
{{--                {!! getContentText('verification', 'youtobe_video_2') !!}--}}
{{--            </div>--}}
{{--            <div class="lg:col-span-1 lg:mt-0 mt-8 lg:hidden block lg:text-left text-center mb-12">--}}
{{--                {!! getContentText('verification', 'video_section_text2') !!}--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>

@endsection
