@extends('layouts.app2')

@section('content')


    <section class="bg-gray-500 py-8 mb-7">
        <div class="lg:w-8/12 mx-auto w-10/12">
                <div class="sm:block lg:flex flex-column justify-between ">
                    <a href="/"> <img class="lg:w-32 md:w-24 sm:w-20 w-16 mb-4 lg:mb-0" src="/storage/{!!str_replace("\\","/",setting('site.logo'))!!}"></a>

                    <a href="/" class="lg:md:text-base sm:text-sm text-xs text-white hover:text-yellow-400">
                        <i class="fa fa-link"></i>
                        {{__('Перейти на сайт Universal Services')}}
                    </a>
                </div>
                <h1 class="text-white lg:text-3xl md:text-2xl sm:text-xl font-light  my-6">
                    {{__('Ответы на частые вопросы и рекомендации от Universal Services')}}</h1>
            <form action="{{ route('faq.index') }}" method="GET">
                @csrf
                <div class="flex relative mx-auto w-full">
                    <button type="submit" class="absolute left-5 top-5">
                        <svg class="text-white lg:h-6 lg:w-6 md:h-5 md:w-5 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;" xml:space="preserve" width="512px" height="512px">
                            <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z" />
                        </svg>
                    </button>
                    <input id="inp" class="bg-gray-400 border-none outline-none transition h-16 pl-16 pr-6 rounded-md focus:outline-none focus:border-yellow-500 focus:bg-white w-full text-black lg:md:text-base text-base hover:bg-gray-400" type="search" name="search" placeholder="Поиск ответов..." />
                    <input type="submit" class="rounded-md bg-gray-400 cursor-pointer ml-4 px-5 text-xl hover:bg-white" value="{{__('Отправить')}}">
                </div>
            </form>
        </div>
    </section>

     <section class="mt-7">
        <div class="lg:w-10/12 md:w-8/12 mx-auto md:flex flex flex-col justify-start bg-slate-100 py-5 px-8 rounded-md shadow-lg">
            <div class="md:flex flex flex-row">
                <img src="{{asset('storage/'.$fc->logo)}}" alt="" class="lg:h-20 md:h-16 sm:h-14 h-10 md:m-5 mx-auto lg:mt-8 md:mt-10 mt-10">
                    <div class="px-6 py-3">
                        <h4 class="text-gray-500 mb-1">{{$fc->getTranslatedAttribute('title',Session::get('lang') , 'fallbackLocale')}}</h4>
                        <p class=" text-gray-400 mb-3 pr-3">{{$fc->getTranslatedAttribute('description',Session::get('lang') , 'fallbackLocale')}} </p>
                    </div>
            </div>
        </div>
    </section>

@endsection
