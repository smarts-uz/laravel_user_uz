<form id="#search_form1" method="post" action="{{route('searchTask.search_new2')}}">
    <div class="w-11/12 mx-auto my-5 rounded-md lg:hidden block">
        <div class="flex flex-col block w-full gap-4">
            <input id="filter1" name="filter1" type="text"
                class=" focus:outline-none px-2 py-3 focus:border-yellow-500 text-base border-2 rounded-md bg-gray-100"
                placeholder="{{__('Поиск по ключевым словам')}}">

            <div class="flex flex-row gap-x-2">
                <button id="findBut1"
                    class="sm:w-48 w-36 bg-green-500 hover:bg-green-600 rounded-md text-white">{{__('Найти')}}</button>
                <div class="ml-4">
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
            </div>

        </div>
    </div>
    <div id="mobile_bar" class="w-full hidden">
        <div class="bg-yellow-50 pb-4">
            <div class=" w-11/12 mx-auto ">
                <div class="w-full relative">
                    <label class="text-xs mb-1 text-neutral-400">{{__('Город, адрес, метро, район...')}}</label>
                    <div
                        class="bg-white address py-1 px-3 text-black-700 border-2 rounded-md focus:shadow-sm flex w-full text-black-700">
                        <input
                            class="float-left bg-transparent border-0 w-11/12 h-full focus:outline-none focus:border-yellow-500"
                            type="text" id="suggest1" name="suggest1" placeholder="Mobile">
                        <svg class="h-4 w-4 text-purple-500 mt-1" id="geobut2" width="12" height="12"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" />
                            <path d="M21 3L14.5 21a.55 .55 0 0 1 -1 0L10 14L3 10.5a.55 .55 0 0 1 0 -1L21 3" />
                        </svg>
                        <img src="images/close.png" class=" cursor-pointer h-5 mt-0.5" id="closeBut2" hidden>
                        <input type="hidden" name="user_lat1" id="user_lat1" value="">
                        <input type="hidden" name="user_long1" id="user_long1" value="">
                    </div>
                </div>
                <div class="w-full">
                    <label class="text-xs mb-1 text-neutral-400">{{__('Радиус поиска')}}</label>
                    <select name="radius1" id="selectGeo1"
                        class="w-full py-1 px-2 border-2 rounded-md focus:shadow-sm text-lg-left text-black-700 rounded">
                        <option value="0">{{__('Без ограничений')}}</option>
                        <option value="1.5">1.5 km</option>
                        <option value="3">3 km</option>
                        <option value="5">5 km</option>
                        <option value="10">10 km</option>
                        <option value="15">15 km</option>
                        <option value="20">20 km</option>
                        <option value="30">30 km</option>
                        <option value="50">50 km</option>
                        <option value="75">75 km</option>
                        <option value="100">100 km</option>
                        <option value="200">200 km</option>
                    </select>
                </div>
            </div>
        </div>
        <div class=" w-11/12 mx-auto border-b pb-4">
            <label class="text-xs mb-1 text-neutral-400">{{__('Стоимость заданий')}}</label>
            <input type="number" min="1" max="999999999" name="price1"
                class="w-full focus:placeholder-transparent border-md py-1 px-2 text-black-700 border-2 rounded-md focus:outline-none focus:border-yellow-500 focus:shadow-sm  text-black-700"
                placeholder="UZS">
            <img src="images/close.png" class="absolute right-2 bottom-2.5 cursor-pointer" id="prcClose2" hidden>
        </div>

        <div class="w-11/12 mx-auto">
            <label class="block w-full border-b pb-4 items-center mt-3">
                <input type="checkbox" id="remjob1" name="remjob1"
                    class="form-checkbox checkboxByAs mr-4  h-5 w-5 text-orange-400"><span
                    class="sm:ml-2 ml-0.5 text-gray-700 lg:text-sm">{{__('Удалённая работа')}}</span>
            </label>
            <label class="block w-full border-b pb-4 items-center mt-3">
                <input type="checkbox" id="noresp1" name="noresp1"
                    class="form-checkbox mr-4  h-5 w-5 text-orange-400"><span
                    class="sm:ml-2  ml-0.5 text-gray-700 lg:text-sm">{{__('Задания без откликов')}}</span>
            </label>
        </div>

        <div id="mobile-map" class="h-full my-5 rounded-lg w-full static"></div>
    </div>
</form>


{{-- tasks --}}
<div class="w-11/12 mx-auto lg:hidden block">
    <div class="dataPlace">
        @include('search_task.tasks')
    </div>
    <div class="loader" style="display: none">
        @include('search_task.loader')
    </div>
</div>
{{-- tasks --}}
