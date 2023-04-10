@extends('layouts.app')

@include('layouts.fornewtask')

@section('content')
    <!-- Information section -->
    <div class="mx-auto sm:w-9/12 w-11/12 my-16">
        <div class="grid md:grid-cols-3 lg:gap-x-20 md:gap-x-14">
            <div class="lg:col-span-2 col-span-3">
                <div class="w-full text-center text-2xl">
                    @if(session('lang')==='ru')
                        {{__('Ищем исполнителя для задания')}} "{{$task->name}}"
                    @else
                        "{{$task->name}}" {{__('Ищем исполнителя для задания')}}
                    @endif
                </div>
                <div class="w-full text-center my-4 text-gray-400">
                    {{__('Задание заполнено на')}} 99%
                </div>
                <div class="relative pt-1">
                    <div class="overflow-hidden h-1  flex rounded bg-gray-200  mx-auto ">
                        <div style="width: 99%" class="shadow-none  flex flex-col text-center
                        whitespace-nowrap text-white justify-center bg-yellow-500"></div>
                    </div>
                </div>
                <div class="shadow-xl w-full mx-auto mt-7 rounded-2xl w-full p-2 md:p-6 px-8">

                    <div class="py-4 mx-auto px-auto text-center text-3xl font-semibold">
                        {{__('Ваши контакты')}}
                    </div>

                    <div class="mb-3 sm:w-9/12 w-11/12 mx-auto">
                        <form action="{{route('task.create.contact.store.phone', $task->id)}}" method="post">
                        @csrf
                        <label class="text-sm text-gray-500 mb-2" for="phone">{{__('Номер телефона')}}</label>
                        <input type="text"  autofocus="autofocus" name="phone_number"
                               value="{{auth()->user()->phone_number}}" placeholder="+998(00)000-00-00" id="phone"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500"/>
                        @error('phone_number')
                        <p class="text-red-500">{{$message}}</p>
                        @enderror

                        <div class="mt-4">
                            <div class="flex w-full mt-4">
                                <a onclick="myFunction()"
                                   class="bg-white my-4 cursor-pointer hover:border-yellow-500 text-center text-gray-600 hover:text-yellow-500 transition duration-300 font-normal text-base py-3 sm:px-8 px-6 rounded-2xl border border-2">
                                    <!-- <button type="button"> -->
                                    {{__('Назад')}}
                                    <!-- </button> -->
                                    <script>
                                        function myFunction() {
                                            window.history.back();
                                        }
                                    </script>
                                </a>
                                <input type="submit" style="background: linear-gradient(164.22deg, #FDC4A5 4.2%, #FE6D1D 87.72%);"
                                       class="bg-yellow-500 hover:bg-yellow-600 m-4 cursor-pointer text-white font-normal text-xl py-3 sm:px-14 px-8 rounded-2xl "
                                       name="" value="{{__('Отправить')}}">
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <x-faq/>
        </div>
    </div>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js'></script>
    <script>
        var element = document.getElementById('phone');
        var maskOptions = {
            mask: '+998(00)000-00-00',
            lazy: false
        }
        var mask = new IMask(element, maskOptions);
    </script>

@endsection
