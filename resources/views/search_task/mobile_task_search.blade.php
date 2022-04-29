@extends("layouts.app")

@section("content")


<div class="w-full my-5 rounded-md md:hidden block">
    <div class="inline-flex block w-full">
        <input id="filter2" type="text"
               class="col-span-3 focus:outline-none focus:border-yellow-500 focus:placeholder-transparent text-base md:w-10/12 px-4 py-1 text-black border-2 rounded-md border-neutral-400 focus:shadow-sm focus:shadow-sky-500 md:mr-4 mr-0 bg-gray-200"
               placeholder="{{__('Поиск по ключевым словам')}}">

        <button
            id="findBut2" class="md:w-4/12 w-2/3 md:mt-0 mt-2 bg-green-500 hover:bg-green-600 rounded-md text-white">{{__('Найти')}}</button>
        <div class="col-span-1 flex justify-evenly inline-block md:mt-0 mt-2">
            <button id="show_2" class=" w-10 md:ml-2  focus:outline-none">
                <i class="fas fa-bars fa-2x ml-1.5 text-gray-500"></i>
            </button>
            <button id="hide_2" class=" w-10 md:ml-2  focus:outline-none" style="display: none">
                <i class="fas fa-times fa-2x ml-1.5 text-gray-500"></i>
            </button>
            <button id="show" class="w-10 md:ml-2 focus:outline-none">
                <i class="far fa-map fa-2x text-gray-500"></i>
            </button>
            <button id="hide" class="w-10 md:ml-2  focus:outline-none" style="display: none">
                <i class="fas fa-list fa-2x text-gray-500"></i>
            </button>
        </div>

    </div>
</div>


@endsection