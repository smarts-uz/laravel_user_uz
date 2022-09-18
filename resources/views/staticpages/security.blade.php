@extends("layouts.app")

@section('content')

    <div class="container w-4/5 mx-auto mt-12">

        <div class="flex lg:flex-row flex-col justify-center mt-6">
            @include('components.footerpage')

            <div class="lg:w-4/5 w-full text-base lg:mt-0 mt-4">
                    <div class="">
                        <h1 class="font-medium text-4xl">{{__('Безопасность и гарантии')}}</h1>
                        {!! getContentText('security', 'security_1') !!}
                    </div>
                    <div class="flex lg:flex-row flex-col mt-4  mx-auto">
                        <div class="lg:w-1/2 w-full lg:block hidden">
                            <img class="w-96 h-60 mt-16" src="{{ getContentImage('security', 'security_2') }}" alt="">
                        </div>
                        <div class="lg:w-1/2 w-full text-left ml-4 mt-4 lg:block hidden w-4/5">
                            {!! getContentText('security', 'security_2') !!}
                        </div>

                        <div class="lg:w-1/2 w-full text-left mt-4 lg:hidden block">
                            {!! getContentText('security', 'security_2') !!}
                        </div>
                        <div class="lg:w-1/2 w-full lg:hidden block">
                            <img class="w-76 h-64 mx-auto" src="{{ getContentImage('security', 'security_2') }}" alt="">
                        </div>
                    </div>
                    <div class="w-full mt-10">
                        <div>
                            {!! getContentText('security', 'security_3') !!}
                        </div>
                        <div class="flex mt-10">
                            <div class="sm:w-1/2 w-full">
                                <a target='blank' href="{{ setting('site.telegram_url') }}">
                                    <button  class="font-sans  text-2xl mx-2 font-medium bg-green-400 text-white hover:bg-green-300 px-10 py-4 rounded">
                                        {{__('Написать в поддержку')}}
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
