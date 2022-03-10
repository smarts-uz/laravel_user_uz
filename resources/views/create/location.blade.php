@extends('layouts.app')

@include('layouts.fornewtask')

@section('content')
<script>
  let userAddress;
  var myMap;
  var multiRoute;
  var place, place1="", place2="", place3="", place4="", place5="", place6="", place7="", place8="", place9="";
</script>
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>


{{--<script id="map_api" src="https://api-maps.yandex.ru/2.1/?apikey=f4b34baa-cbd1-432b-865b-9562afa3fcdb&lang={{__('ru_RU')}}&onload=onLoad" type="text/javascript"></script>--}}
<script src="https://api-maps.yandex.ru/2.1/?apikey=f4b34baa-cbd1-432b-865b-9562afa3fcdb&lang={{__('ru_RU')}}" type="text/javascript"></script>

<!-- Information section -->

<form action="{{route("task.create.address.store", $task->id)}}" method="post" >
  @csrf
<div class="mx-auto w-9/12  my-16">
<div class="grid grid-cols-3 gap-x-20">
  <div class="md:col-span-2 col-span-3">
    <div class="w-full text-center text-2xl">
      {{__('Ищем исполнителя для задания')}} "{{$task->name}}"
    </div>
    <div class="w-full text-center my-4 text-gray-400">
      {{__('Задание заполнено на 55%')}}
    </div>
    <div class=" pt-1">
      <div class="overflow-hidden h-1 text-xs flex rounded bg-gray-200  mx-auto ">
        <div style="width: 55%" class="shadow-none  flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"></div>
      </div>
    </div>
    <div class="shadow-2xl w-full md:p-16 p-4 mx-auto my-4 rounded-2xl	w-full">

      <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
        {{__('Где выполнить задание?')}}
      </div>
      <div class="py-4 mx-auto px-auto text-center text-sm texl-bold">
        {{__('Укажите адрес или место, чтобы найти исполнителя рядом с вами.')}}
      </div>

      <div class="py-4 mx-auto  text-left ">
        <div class="mb-4">
          <div id="formulario" class="flex flex-col gap-y-4">

            <div class="flex items-center rounded-lg border py-">
                <button class="flex-shrink-0 border-transparent text-teal-500 text-md py-1 px-2 rounded focus:outline-none" type="button">
                  A
                </button>
                <input autocomplete="off" oninput="myFunction()"  id="suggest0"  class="appearance-none bg-transparent w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none focus:border-yellow-500" type="search" placeholder="{{__('Город, Улица, Дом')}}" value="{{session('location2')}}" name="location0" required>
                <button id="getlocal" class="flex-shrink-0 border-transparent border-4 text-teal-500 hover:text-teal-800 text-sm py-1 px-2 rounded" type="button">   <svg class="h-4 w-4 text-purple-500"  width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M21 3L14.5 21a.55 .55 0 0 1 -1 0L10 14L3 10.5a.55 .55 0 0 1 0 -1L21 3" /></svg>  </button>
              </div>
              <input name="coordinates0" type="hidden" id="coordinate">
            <div id="addinput" class="flex gap-y-2 flex-col">


            </div>
          </div>

          <div class="mt-4">
            <button id="addbtn" type="button"  class="w-full border-dashed border border-black rounded-lg py-2 text-center flex justify-center items-center gap-2" name="button">
              <svg class="h-4 w-4 text-gray-500 "  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span >{{__('Добавить ещё адрес')}}</span>
             </button>
             <div id="map" class="h-60 mt-4 rounded-lg w-full" ></div>
              @foreach($task->category->customFieldsInAddress as $data)
                  @include('create.custom-fields')
              @endforeach
             <div class="flex w-full gap-x-4 mt-4">
             <a onclick="backfunctionlocation()" class="w-1/3 cursor-pointer  border border-black-700 hover:border-yellow-400 transition-colors rounded-lg py-2  text-lg text-center flex justify-center items-center gap-2">
                                            <!-- <button type="button"> -->
                                            {{__('Назад')}}
                                            <!-- </button> -->
                                            <script>
                                                function backfunctionlocation() {
                                                    window.history.back();
                                                }
                                            </script>
                                        </a>

               <input type="submit" class="bg-green-500 hover:bg-green-600 w-2/3 cursor-pointer text-white font-bold py-5 px-5 rounded" name="" value="{{__('Далее')}}">
             </div>


          </div>
        </div>
      </div>


    </div>
  </div>
    <x-faq/>
</div>
</div>


</form>

<script src="{{ asset('js/location.js') }}"></script>

@endsection
