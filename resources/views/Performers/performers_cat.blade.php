@extends('layouts.app')

@section('content')
<style>
    select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}
</style>
    <div class="flex flex-row container mx-auto mx-40 my-8">

{{-----------------------------------------------------------------------------------}}
{{--                             Left column                                       --}}
{{-----------------------------------------------------------------------------------}}

        <div class="flex flex-col basis-1/3 px-8">
            <div class="flex flex-row shadow-lg rounded-lg mb-8">
                <div class="basis-1/2 h-24 bg-contain bg-no-repeat bg-center" style="background-image: url(images/like.png);">
                </div>
                <div class="basis-1/2 text-xs text-gray-700 text-left my-auto">
                    Станьте исполнителем <br> U-ser. И начните <br> зарабатывать.
                </div>
            </div>

            <div>
                <div class="max-w-xl mx-auto">
                    @foreach($categories as $category)
                        @if ($category->parent_id == NULL)
                            <label for="categories"></label>
                            <select name="categories" id="categories" class="relative z-1 focus:outline-none mt-4 text-[#4099fb] cursor-pointer text-lg active:text-red-600">
                                <option value="{{ $category->id }}">
                                    <div class="flex items-center justify-between mb-4 ">
                                        <span class="text-left font-bold text-blue-500  ">
                                            {{$category->name}}
                                        </span>
                                    </div>
                                </option>

                                @foreach($child_categories as $child_category)
                                    @if ($category->id == $child_category->parent_id)
                                        <option value="{{ $child_category->id }}">
                                            <div>
                                                <div class="ml-4 text-blue-500">
                                                    <a href="/perf-ajax/{{ $child_category->id }}" class="hover:text-[#ff0000]">{{ $child_category->name }}</a>
                                                </div>
                                            </div>
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

{{-----------------------------------------------------------------------------------}}
{{--                             Right column                                      --}}
{{-----------------------------------------------------------------------------------}}

        <div class="flex flex-col basis-2/3">
            <div class="bg-gray-100 flex flex-row h-40 mb-10" style="width: 700px;">
                <div class="flex flex-col relative">
                    <div class="flex flex-row font-bold text-2xl m-4">
                        <p>Курьерские услуги: рейтинг исполнителей</p>
                    </div>
                    <div class="flex flex-row m-4 absolute bottom-0 left-0">
                        <div class="form-check flex flex-row mr-6">
                            <input class="form-check-input h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-yellow-600 checked:border-yellow-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="checkbox" value="" id="verified">
                            <label class="form-check-label inline-block text-gray-800" for="verified">
                                Только проверенные
                            </label>
                        </div>
                        <div class="form-check flex flex- mr-6">
                            <input class="form-check-input h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-black-600 checked:border-black-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="checkbox" value="" id="online">
                            <label class="form-check-label inline-block text-gray-800" for="online">
                                Сейчас на сайте
                            </label>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col m-4 relative">
                    <div>
                        <p>Город: <a href="#"> Москва </a> </p>
                    </div>
                    <div class="absolute bottom-0 right-0">
                        <p>Указать метро</p>
                    </div>
                </div>
            </div>
            @foreach($users as $user)
@php
 $cat_arr = explode(",",$user->category_id);
 $res_c_arr = array_search($cf_id,$cat_arr);
 //dd($res_c_arr);
@endphp
@if($res_c_arr !== false)
            <div class="flex flex-row">
                <div class="m-10">
                    <img class="rounded-lg w-40 h-40" src="{{asset($user->avatar)}}" alt="user">
                    <div class="flex flex-row">
                        <p>Отзывы:</p>
                        <i class="far fa-thumbs-up m-1 text-gray-400"></i>    5128
                        <i class="far fa-thumbs-down m-1 text-gray-400"></i>  21
                    </div>
                    <div class="flex flex-row">
                        <i class="fas fa-star text-yellow-500"></i>
                        <i class="fas fa-star text-yellow-500"></i>
                        <i class="fas fa-star text-yellow-500"></i>
                        <i class="fas fa-star text-yellow-500"></i>
                        <i class="fas fa-star text-yellow-500"></i>
                    </div>
                </div>
                <div class="my-10">
                    <div class="flex flex-row">
                       <a href="/performers/{{$user->id}}"> <p class="text-3xl underline text-blue-500 hover:text-red-500">{{$user->name}}</p></a>
                        <!-- <img class="h-8 ml-2" src="{{ asset('images/icon_year.svg') }}">
                        <img class="h-8 ml-2" src="{{ asset('images/icon_shield.png') }}">
                        <img class="h-8 ml-2" src="{{ asset('images/icon_bag.png') }}"> -->
                    </div>
                    <div>
                      @if($user->active_status == 1)
                        <p class="text-sm text-gray-500 my-3">Онлайн</p>
                        @else
                        <p class="text-sm text-gray-500 my-3">Офлайн</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-base" style="width: 500px;">
                          {{$user->description}}
                        </p>
                    </div>
                    <div>
                      <a href="#" onclick="toggleModal12('modal-id12')">  <button class="rounded-lg py-2 px-3 font-bold bg-yellow-500 text-white mt-3">Предложить задание</button></a>
                    </div>
                </div>
            </div>
@endif
            @endforeach

            {{ $users->links() }}

        </div>
    </div>

     {{-- Modal start --}}
     <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="modal-id12">
        <div class="relative w-auto my-6 mx-auto max-w-3xl"  id="modal-id12">
          <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
            <div class=" text-center p-12  rounded-t">
                  <button type="submit"  onclick="toggleModal12('modal-id12')" class="rounded-md w-100 h-16 absolute top-1 right-4">
                    <i class="fas fa-times  text-slate-400 hover:text-slate-600 text-xl w-full"></i>
                  </button>
                <h3 class="font-medium text-4xl block mt-4">
                    У вас пока нет опубликованных <br> заданий
                </h3>
            </div>
            <!--body-->
            <div class="relative p-6 flex-auto">
              <p class="my-4  text-lg  text-center">
                Создайте задание, после чего вы сможете предложить <br> выполнить его исполнителям.
              </p>
            </div>
            <!--footer-->
            <div class="flex mx-auto items-center justify-end p-6 rounded-b mb-8">
                <div class="mt-4 ">
                    <a class="px-10 py-4 text-center font-sans  text-xl  font-semibold bg-lime-500 text-[#fff] hover:bg-lime-600  h-12 rounded-md text-xl" href="/categories/1" >Создать задание</a>
                </div>
            </div>
          </div>
        </div>
      </div>
      <div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id12-backdrop"></div>
      <script type="text/javascript">
        function toggleModal12(modalID12){
          document.getElementById(modalID12).classList.toggle("hidden");
          document.getElementById(modalID12 + "-backdrop").classList.toggle("hidden");
          document.getElementById(modalID12).classList.toggle("flex");
          document.getElementById(modalID12 + "-backdrop").classList.toggle("flex");
        }
      </script>
    {{-- Modal end --}}

@endsection

@section('javasript')
    <script src="//unpkg.com/alpinejs" defer></script>
@endsection
