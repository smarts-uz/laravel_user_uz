<div class="mx-auto w-11/12 xl:w-9/12 my-8">
    <form id="search_form" method="post" action="{{route('searchTask.ajax_tasks')}}"  autocomplete="off">
        <div class="grid md:grid-cols-5 grid-cols-3 container mx-auto gap-x-2">
            {{-- left start --}}
            <div class="col-span-3">
                    <div class="w-full bg-yellow-100 my-5 rounded-md text-sm">
                        <div class="px-5 py-5">
                            <div class="grid grid-cols-4 gap-2 mb-3">
                                <div class="sm:inline-flex block w-full col-span-4 relative">
                                    <input id="filter" name="filter" type="text"
                                        class="form-input focus:border-yellow-500 focus:placeholder-transparent w-4/5 py-1 px-3 text-black-700 border-2 rounded-md border-neutral-400 focus:shadow-sm focus:shadow-sky-500 mr-4"
                                        placeholder="{{__('Поиск по ключевым словам')}}">
                                    <img src="images/close.png" class="fill-current absolute left-3/4 top-2 cursor-pointer"
                                        id="svgClose" hidden>
                                    <button
                                        class="sm:w-2/12 w-4/12 bg-green-500 hover:bg-green-600 ml-1 py-1 px-1 rounded-md sm:mt-0 text-white"
                                        id="findBut">{{__('Найти')}}</button>
                                </div>
                                <div class="md:inline-flex  block w-full col-span-4 disalable">
                                    <div class="w-8/12 md:w-4/5 relative">
                                        <label class="mb-1">{{__('Город, адрес, метро, район...')}}</label>
                                        <div class="">
                                            <input class="form-input bg-white address float-left py-1 px-2 text-black-700 border-2 rounded-md focus:shadow-sm w-full text-black-700 focus:border-yellow-500 focus:outline-none  float-left bg-transparent border-0 mr-3.5 h-full "
                                                type="text" id="suggest" name="suggest">
                                            <svg class="absolute right-2 bottom-1.5 h-4 w-4 text-purple-500" id="geoBut"
                                                width="12" height="12" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" />
                                                <path
                                                    d="M21 3L14.5 21a.55 .55 0 0 1 -1 0L10 14L3 10.5a.55 .55 0 0 1 0 -1L21 3" />
                                            </svg>
                                            <img src="images/close.png" class="absolute right-2 bottom-1.5 cursor-pointer"
                                                id="closeBut" hidden>
                                            <input type="hidden" name="user_lat" id="user_lat" value="">
                                            <input type="hidden" name="user_long" id="user_long" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="md:inline-flex  block w-full col-span-4 ">
                                    <div class="md:w-2/5 pr-5 disalable">
                                        <label
                                            class="mb-1">{{__('Радиус поиска')}}</label>
                                        <select name="radius" id="selectGeo"
                                            class="form-select py-1 px-2 w-full text-gray-700 border-2 rounded-md focus:shadow-sm focus:border-yellow-500 text-lg-left text-black-700 rounded"
                                            onchange="">
                                            <option value="">{{__('Без ограничений')}}</option>
                                            <option value="1.5|13">1.5 {{__('км')}}</option>
                                            <option value="3|12">3 {{__('км')}}</option>
                                            <option value="5|11">5 {{__('км')}}</option>
                                            <option value="10|10">10 {{__('км')}}</option>
                                            <option value="15|9">15 {{__('км')}}</option>
                                            <option value="20|9">20 {{__('км')}}</option>
                                            <option value="30|8">30 {{__('км')}}</option>
                                            <option value="50|7.5" selected="selected">50 {{__('км')}}</option>
                                            <option value="75|7">75 {{__('км')}}</option>
                                            <option value="100|6.5">100 {{__('км')}}</option>
                                            <option value="200|6">200 {{__('км')}}</option>
                                        </select>
                                    </div>
                                    <div class="relative pl-5 md:w-2/5">
                                        <label
                                            class="mb-1">{{__('Стоимость заданий')}}</label>
                                        <input type="text" min="1" max="999999999" name="price"
                                            class="form-input focus:border-yellow-500 focus:placeholder-transparent w-full border-md py-1 px-2 text-black-700 border-2 rounded-md focus:shadow-sm text-black-700"
                                            placeholder="UZS" onkeypress="validate(event)" id="price">
                                        <img src="images/close.png" class="absolute right-2 bottom-2.5 cursor-pointer"
                                            id="prcClose" hidden>
                                    </div>
                                </div>
                                <div class="inline-flex  block w-full col-span-4">
                                    <label class="inline-flex items-center mt-1">
                                        <input type="checkbox" id="remjob" name="remjob"
                                            class="focus:outline-none form-checkbox checkboxByAs h-5 w-5 text-orange-400">
                                        <span class="sm:ml-2 ml-0.5 text-gray-700 cursor-pointer">{{__('Удалённая работа')}}</span>
                                    </label>
                                    <label class="inline-flex items-center mt-1 xl:ml-3 sm:ml-2 ml-0.5">
                                        <input type="checkbox" id="noresp" name="noresp"
                                            class="focus:outline-none form-checkbox h-5 w-5 text-orange-400">
                                        <span class="sm:ml-2 ml-0.5 text-gray-700 cursor-pointer">{{__('Задания без откликов')}}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 lg:col-span-1 md:block hidden mx-4 lg:mt-0 mt-32">
                        <div class="big-map static">

                        </div>
                    </div>
                    <div class="flex sm:flex-row flex-col gap-x-3 items-center my-5 text-lg">
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
                        @include('search_task.loader')
                    </div>
            </div>
            {{-- left end --}}



            {{-- right start --}}
            <div class="col-span-2 ml-8 mt-2 md:block hidden">
                <div class="small-map static">
                    {{--Map2 show --}}
                </div>
                <div class="w-full h-full">
                    <div class="max-w-lg mx-auto">
                        <label class="inline-flex items-center mt-3">
                            <input type="checkbox" class="form-checkbox all_cat ml-5 h-5 w-5 text-orange-400">
                            <span class="ml-2 text-gray-700 text-sm">{{__('Все категории')}}</span>
                        </label>
                        <div class="w-full my-1 for_check text-sm">
                            @foreach ($categories as $category)
                            <div x-data={show:false} class="rounded-sm">
                                <div class="" id="headingOne">
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
                                <div x-show="show" class="border-b-0 px-8">
                                    @foreach ($categories2 as $category2)
                                        @if($category2->parent_id === $category->id)
                                            <div class="par{{$category->id}}">
                                                <label class="inline-flex items-center my-1 hover:cursor-pointer">
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
            </div>
            {{-- right end --}}
        </div>
    </form>
</div>
