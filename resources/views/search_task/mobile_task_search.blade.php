@extends("layouts.app")

@section("content")
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<script src="https://api-maps.yandex.ru/2.1/?apikey=f4b34baa-cbd1-432b-865b-9562afa3fcdb&lang={{__('ru_RU')}}"
    type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

    <div class="w-11/12 mx-auto my-5 rounded-md">
        <form id="search_form" method="post" action="{{route('searchTask.ajax_tasks')}}"  autocomplete="off">
            <div class="flex flex-col block w-full gap-4">
                <input id="filter" name="filter" type="text"
                    class="form-input focus:outline-none px-2 py-3 focus:border-yellow-500 text-base border-2 rounded-md bg-gray-100"
                    placeholder="{{__('Поиск по ключевым словам')}}">

                <div class="flex flex-wrap items-center gap-x-2">
                    <div class="ml-2 flex items-center">
                        <button id="findBut"
                            class="px-3 py-2 bg-green-500 hover:bg-green-600 rounded-md text-white">{{__('Найти')}}
                        </button>
                        <button id="show_2" class=" w-10 ml-3  focus:outline-none">
                            <i class="fas fa-bars fa-2x ml-1.5 text-gray-500"></i>
                        </button>
                        <button id="hide_2" class=" w-10 ml-3  focus:outline-none hidden">
                            <i class="fas fa-times fa-2x ml-1.5 text-gray-500"></i>
                        </button>
                        <button id="show" class="w-10 ml-3 focus:outline-none">
                            <i class="far fa-map fa-2x text-gray-500"></i>
                        </button>
                        <button id="hide" class="w-10 ml-3  focus:outline-none hidden">
                            <i class="fas fa-list fa-2x text-gray-500"></i>
                        </button>
                    </div>
                    <button id="categories_block" class="m-2 bg-yellow-500 px-3 py-2 rounded-lg text-white cursor-pointer hover:bg-yellow-600">
                        {{__('Все категории')}}
                    </button>
                </div>

            </div>
            <div id="mobile_bar" class="w-full hidden">
                <div class="bg-yellow-50 pb-4">
                    <div class="w-full">
                        <div class="w-full relative">
                            <label class="text-xs mb-1 text-neutral-400">{{__('Город, адрес, метро, район...')}}</label>
                            <div
                                class="disalable bg-white address py-1 px-3 text-black-700 border-2 rounded-md focus:shadow-sm flex w-full text-black-700">
                                <input
                                    class="form-input float-left bg-transparent border-0 w-full h-full focus:outline-none focus:border-yellow-500"
                                    type="text" id="suggest" name="suggest" placeholder="{{__('Город, адрес...')}}">
                                <svg class="h-4 w-4 text-purple-500 mt-1" id="geoBut" width="12" height="12"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" />
                                    <path d="M21 3L14.5 21a.55 .55 0 0 1 -1 0L10 14L3 10.5a.55 .55 0 0 1 0 -1L21 3" />
                                </svg>
                                <img src="images/close.png" class=" cursor-pointer h-5 mt-0.5" id="closeBut" hidden>
                                <input type="hidden" name="user_lat" id="user_lat" value="">
                                <input type="hidden" name="user_long" id="user_long" value="">
                            </div>
                        </div>
                        <div class="w-full disalable">
                            <label class="text-xs mb-1 text-neutral-400">{{__('Радиус поиска')}}</label>
                            <select name="radius" id="selectGeo"
                                class="w-full py-1 px-2 border-2 rounded-md focus:shadow-sm text-lg-left text-black-700 rounded" onchange="">
                                <option value="">{{__('Без ограничений')}}</option>
                                <option value="1.5">1.5 {{__('км')}}</option>
                                <option value="3">3 {{__('км')}}</option>
                                <option value="5">5 {{__('км')}}</option>
                                <option value="10">10 {{__('км')}}</option>
                                <option value="15" >15 {{__('км')}}</option>
                                <option value="20">20 {{__('км')}}</option>
                                <option value="30">30 {{__('км')}}</option>
                                <option value="50" selected="selected">50 {{__('км')}}</option>
                                <option value="75">75 {{__('км')}}</option>
                                <option value="100">100 {{__('км')}}</option>
                                <option value="200">200 {{__('км')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class=" w-11/12 mx-auto border-b pb-4">
                    <label class="text-xs mb-1 text-neutral-400">{{__('Стоимость заданий')}}</label>
                    <input type="number" min="1" max="999999999" name="price" id="price"
                        class="w-full focus:placeholder-transparent border-md py-1 px-2 text-black-700 border-2 rounded-md focus:outline-none focus:border-yellow-500 focus:shadow-sm  text-black-700"
                        placeholder="UZS" onkeypress='validate(event)'>
                    <img src="images/close.png" class="absolute right-2 bottom-2.5 cursor-pointer" id="prcClose" hidden>
                </div>

                <div class="w-11/12 mx-auto">
                    <label class="block w-full border-b pb-4 items-center mt-3">
                        <input type="checkbox" id="remjob" name="remjob"
                            class="form-checkbox checkboxByAs mr-4  h-5 w-5 text-orange-400"><span
                            class="sm:ml-2 ml-0.5 text-gray-700 lg:text-sm">{{__('Удалённая работа')}}</span>
                    </label>
                    <label class="block w-full border-b pb-4 items-center mt-3">
                        <input type="checkbox" id="noresp" name="noresp"
                            class="form-checkbox mr-4  h-5 w-5 text-orange-400"><span
                            class="sm:ml-2  ml-0.5 text-gray-700 lg:text-sm">{{__('Задания без откликов')}}</span>
                    </label>
                </div>
            </div>
            <div class="w-full h-full hidden" id="categories_hidden">
                <div class="max-w-lg mx-auto">
                    <label class="inline-flex items-center mt-3">
                        <input type="checkbox" class="form-checkbox all_cat ml-5 h-5 w-5 text-orange-400">
                        <span class="ml-2 text-gray-700 text-sm">{{__('Все категории')}}</span>
                    </label>
                    <div class="w-full my-1 for_check text-sm">
                        @foreach ($categories as $category)
                        <div x-data={show:false} class="rounded-sm">
                            <div class=" mb-2" id="headingOne">
                                <button @click="show=!show"
                                    class="underline text-gray-500 hover:text-blue-700 focus:outline-none"
                                    type="button">
                                    <svg class="w-4 h-4 rotate -rotate-90" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <label class="inline-flex items-center mt-3 hover:cursor-pointer">
                                    <input type="checkbox"
                                        class="form-checkbox par_cat mr-1 h-5 w-5 text-orange-400 hover:cursor-pointer"
                                        name="{{$category->id}}" id="par{{$category->id}}"><span
                                        class="ml-2 text-gray-700">{{$category->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale')}}</span>
                                </label>
                            </div>
                            <div x-show="show" class="border-b-0 px-8 py-0">
                                @foreach ($categories2 as $category2)
                                    @if($category2->parent_id == $category->id)
                                        <div class="par{{$category->id}}">
                                            <label class="inline-flex items-center mt-3 hover:cursor-pointer">
                                                <input type="checkbox"
                                                    class="form-checkbox chi_cat mr-1 h-5 w-5 text-orange-400 hover:cursor-pointer"
                                                    name="{{$category2->id}}" id="par{{$category->id}}"><span
                                                    class="ml-2 text-gray-700">{{$category2->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale')}}</span>
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="hidden" id="mobile_map">
                <div class="small-map static"></div>
            </div>
            <div class="w-full">
                <div class="flex flex-row gap-x-3 items-center my-5 text-sm">
                    <span class="title__994cd">{{__('Сортировать:')}}</span>
                    <button id="byDate" class="mx-5 font-bold">{{__('по дате публикации')}}
                    </button>
                    <button id="bySearch" class="mx-5 ">
                        {{__('по срочности')}}
                    </button>
                    <input type="checkbox" name="sortBySearch" id="sortBySearch" style="display: none">
                </div>

                <div id="dataPlace">
                    @include('search_task.tasks')
                </div>
                <div id="loader" style="display: none">
                    <iframe src="https://giphy.com/embed/3oEjI6SIIHBdRxXI40" class="mx-auto my-auto w-72 h-72"  frameBorder="0" class="giphy-embed" allowFullScreen></iframe>
                </div>
            </div>
        </form>
    </div>

    <style>
        [class*="copyrights-pane"]
        {display: none !important;}

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@include('search_task.script')
@endsection
