@extends("layouts.app")

@section('content')

<div class="container w-4/5 mx-auto mt-12">

        <div class="flex lg:flex-row flex-col justify-center mt-6">
            @include('components.footerpage')
            <div class="lg:w-4/5 w-full lg:mt-0 mt-4">
                <div class="w-full">
                    <h1 class="font-semibold text-4xl">
                        {{__('Как это работает?')}}
                    </h1>
                    {!! App\Services\CustomService::getContentText('geotaskshint', 'geotaskshint_text') !!}
                    <h1 class="font-medium text-4xl mt-10 text-center">
                        {{__('Как создать задание на Universal Services?')}}
                    </h1>
                    <h3 class="mt-10 font-medium text-2xl text-center mb-2">
                        {!! App\Services\CustomService::getContentText('geotaskshint', 'geotaskshint_category') !!}
                    </h3>
                    <img src="{{ App\Services\CustomService::getContentImage('geotaskshint', 'geotaskshint_category') }}" class="mx-auto"/>

                    <p class="mt-10 font-medium text-2xl text-center mb-2">
                        {!! App\Services\CustomService::getContentText('geotaskshint', 'geotaskshint_second') !!}
                    </p>
                    <img src="{{ App\Services\CustomService::getContentImage('geotaskshint', 'geotaskshint_second') }}" class="mx-auto"/>
               </div>


                <div class="w-full mx-auto my-10">
                    <hr>
                </div>

                <div class="flex lg:flex-row flex-col mt-10 sm:w-4/5 w-full mx-auto">
                    <div class="lg:w-1/2 w-full my-auto text-center">
                        {!! App\Services\CustomService::getContentText('geotaskshint', 'geotaskshint_ispolnitel') !!}
                    </div>
                    <div class="lg:w-1/2 w-full">
                        <img src="{{ App\Services\CustomService::getContentImage('geotaskshint', 'geotaskshint_ispolnitel') }}" class="mx-auto w-80 h-64"/>
                    </div>
                </div>

                <div class="flex lg:flex-row flex-col mt-4 sm:w-4/5 w-full mx-auto">
                    <div class="lg:w-1/2 w-full lg:block hidden">
                        <img src="{{ App\Services\CustomService::getContentImage('geotaskshint', 'geotaskshint_4') }}" class="mx-auto w-80 h-52"/>
                    </div>
                    <div class="lg:w-1/2 w-full text-center ml-4 mt-4 lg:block hidden">
                        {!! App\Services\CustomService::getContentText('geotaskshint', 'geotaskshint_4') !!}
                    </div>

                    <div class="lg:w-1/2 w-full text-center ml-4 mt-4 lg:hidden block">
                        {!! App\Services\CustomService::getContentText('geotaskshint', 'geotaskshint_4') !!}
                    </div>
                    <div class="lg:w-1/2 w-full lg:hidden block">
                        <img src="{{ App\Services\CustomService::getContentImage('geotaskshint', 'geotaskshint_4') }}" class="mx-auto w-80 h-52"/>
                    </div>
                </div>
                <div class="flex lg:flex-row flex-col mt-10 sm:w-4/5 w-full mx-auto">
                    <div class="lg:w-1/2 w-full my-auto text-center">
                        {!! App\Services\CustomService::getContentText('geotaskshint', 'geotaskshint_5') !!}
                    </div>
                    <div class="lg:w-1/2 w-full ml-4 lg:mt-0 mt-8">
                        <img src="{{ App\Services\CustomService::getContentImage('geotaskshint', 'geotaskshint_5') }}" class="mx-auto w-72 h-52"/>
                    </div>
                </div>
                <div class="sm:w-4/5 w-full mx-auto mt-20">
                    <hr>
                </div>
                <div class="flex lg:flex-row flex-col mt-10 sm:w-4/5 w-full mx-auto gap-x-2">
                    <div class="lg:w-1/2 w-full lg:text-left text-center">
                         <a href="categories/1">
                            <button  class="font-sans  text-2xl  font-medium bg-green-600 text-white hover:bg-green-500 px-10 py-4 rounded">
                               {{__('Создать задание')}}
                            </button>
                        </a>
                    </div>
                    <div class="lg:w-1/2 w-full mx-auto lg:mt-0 mt-6">
                        <a href="/verification" class="hover:text-red-600">
                            {{__('Может быть вы хотите стать исполнителем Universal Services?')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>

</div>


@endsection
