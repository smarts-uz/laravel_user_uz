@extends('layouts.app')

@section('content')

    <div class="text-sm w-full bg-gray-200 py-3">
        <p class="w-8/12 mx-auto text-gray-500 font-normal">{{__('Вы находитесь в разделе исполнителей USer.Uz')}} <br>
            {{__("Чтобы предложить работу выбранному исполнителю, нужно нажать на кнопку «Предложить задание» в его профиле.")}}</p>
    </div>
    <div class="xl:w-9/12 container mx-auto mt-8 text-base">
        <div class="grid grid-cols-3 ">

            {{-----------------------------------------------------------------------------------}}
            {{--                             Left column                                       --}}
            {{-----------------------------------------------------------------------------------}}

            <div class="lg:col-span-1 col-span-3 px-8">
                <a href="/verification" class="flex flex-row shadow-lg rounded-lg mb-8">
                    <div class="w-1/2 h-24 bg-contain bg-no-repeat bg-center"
                         style="background-image: url({{asset('images/like.png')}});">
                    </div>
                    <div class="font-bold text-xs text-gray-700 text-left my-auto">
                        {!!__('Станьте исполнителем <br> USer.Uz. И начните  <br> зарабатывать')!!}
                    </div>
                </a>
                <div>
                    <div class="max-w-md mx-left">
                        @foreach ($categories as $category)
                            <div x-data={show:false} class="rounded-sm">
                                <div class="flex flex-row my-1 text-blue-500 hover:text-red-500 cursor-pointer" id="{{ preg_replace('/[ ,]+/', '', $category->name) }}">
                                    <div class="mr-2 cursor-pointer" @click="show=!show">
                                        <i class="fas fa-chevron-down text-sm" x-show="!show"></i>
                                        <i class="fas fa-chevron-up text-sm" x-show="show"></i>
                                    </div>
                                    <div @click="show=!show">{{ $category->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}</div>
                                </div>
                                <div id="{{$category->slug}}" class="pl-8 py-1 hidden">
                                    @foreach ($categories2 as $category2)
                                        @if($category2->parent_id === $category->id)
                                            <div>
                                                <a href="/perf-ajax/{{ $category2->id }}" class="text-blue-500 cursor-pointer hover:text-red-500 my-1 send-request" data-id="{{$category2->id}}">
                                                    {{ $category2->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 col-span-3 lg:mt-0 mt-16">
                <div class="bg-gray-100 rounded-xl w-full sm:mx-0 mx-auto">
                    <div class="font-bold text-2xl mx-8 py-4">
                        <p>{{__('Все исполнители')}}</p>
                    </div>
                    <div class="flex lg:flex-row flex-col pb-2">
                        <div class="font-sans text-black flex ml-6 mb-3">
                            <form action="{{route('performers.service')}}" method="GET">
                                <div class="rounded-l-lg overflow-hidden flex">
                                    <input type="search" name="search" class="sm:w-80 w-72 px-4 py-2 focus:outline-none rounded-l-lg" placeholder="{{__('Поиск')}}..." value="{{request('search')}}">
                                    <button type="submit" class="flex items-center justify-center px-4 border-l bg-yellow-500 text-white rounded-r-lg">
                                        <svg class="h-4 w-4 text-grey-dark" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M16.32 14.9l5.39 5.4a1 1 0 0 1-1.42 1.4l-5.38-5.38a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"/></svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="form-check flex flex-row mx-8 pb-4 my-auto">
                            <input class="focus:outline-none  form-check-input h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-black-600 checked:border-black-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer"
                                   type="checkbox" id="online">
                            <label class="form-check-label inline-block text-gray-800 cursor-pointer" for="online">
                                {{__('Сейчас на сайте')}}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="sortable">
                    @foreach($users as $user)
                        @include('performers.performers_figure')
                    @endforeach
                    {{ $users->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>

    @include('performers.performers_modal')
    @include('performers.performer_script')
@endsection
