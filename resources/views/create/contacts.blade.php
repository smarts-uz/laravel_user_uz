@extends('layouts.app')

@include('layouts.fornewtask')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/tabs.css') }}">
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
                        <div style="width: 99%"
                             class="shadow-none  flex flex-col text-center whitespace-nowrap text-white justify-center bg-yellow-500"></div>
                    </div>
                </div>
                <div class="shadow-xl w-full mx-auto mt-7 rounded-2xl	w-full p-2 md:p-6 px-8">
                    <div class="py-4 mx-auto px-auto text-center text-3xl font-semibold">
                        @auth()
                            {{__('Ваши контакты')}}
                        @endauth
                        @guest()
                            {{__('Авторизация')}}
                        @endguest
                    </div>
                    <div class="sm:w-9/12 w-11/12 mx-auto">
                        @guest()
                            <ul class="nav nav-tabs flex flex-col md:flex-row text-center flex-wrap list-none border-b-0 pl-0 mb-4 justify-center"
                                id="tabs-tab3" role="tablist">
                                <li class="nav-item w-1/2" role="presentation">
                                    <a href="#tabs-home3" class="nav-link w-full block font-medium text-xs tab-name
                                        @if(session("phone_another")) error @endif
                                       leading-tight uppercase border-x-0 border-t-0 border-b-2 border-transparent px-6 py-3 my-2 hover:border-transparent hover:bg-gray-100 focus:border-transparent active"
                                       id="tabs-home-tab3" data-bs-toggle="pill" data-bs-target="#tabs-home3" role="tab" aria-controls="tabs-home3"
                                       aria-selected="true">{{__('Регистрация')}}
                                    </a>
                                </li>
                                <li class="nav-item w-1/2" role="presentation">
                                    <a href="#tabs-profile3" class="nav-link w-full block font-medium text-xs leading-tight
                                       @if(session("phone"))  error @endif
                                        uppercase border-x-0 border-t-0 border-b-2 border-transparent px-6 py-3 my-2 hover:border-transparent
                                         hover:bg-gray-100 focus:border-transparent tab-name"
                                       id="tabs-profile-tab3" data-bs-toggle="pill" data-bs-target="#tabs-profile3" role="tab"
                                       aria-controls="tabs-profile3" aria-selected="false">{{__('Вход')}}
                                    </a>
                                </li>
                            </ul>
                        @endguest
                        <div class="tab-content" id="tabs-tabContent3">
                            <div class="tab-pane fade show active @if(session()->has('phone_another') ) error @endif" id="tabs-home3" role="tabpanel"
                                 aria-labelledby="tabs-home-tab3">
                                <div class="py-4 mx-auto text-left">
                                    <div class="mb-4">
                                        <div id="formulario" class="flex flex-col gap-y-4">
                                            <div>
                                                <div class="mb-3 xl:w-full">
                                                    @auth()
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
                                                                    <input type="submit"
                                                                           style="background: linear-gradient(164.22deg, #FDC4A5 4.2%, #FE6D1D 87.72%);"
                                                                           class="bg-yellow-500 hover:bg-yellow-600 m-4 cursor-pointer text-white font-normal text-xl py-3 sm:px-14 px-8 rounded-2xl "
                                                                           name="" value="{{__('Отправить')}}">
                                                                </div>
                                                            </div>
                                                        </form>
                                                    @endauth

                                                    @guest

                                                        <form action="{{route("task.create.contact.store.register", $task->id)}}" method="post">
                                                            @csrf
                                                            <label class="text-sm text-gray-500 mb-2" for="name">
                                                                {{__('Имя')}}
                                                            </label>

                                                            <input type="text" name="name" autofocus="autofocus"
                                                                   placeholder="{{__('Имя')}}" value="{{old('name')}}"
                                                                   class="mb-5 shadow appearance-none border   focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500 "/>
                                                            @error('name')
                                                            <p class="text-red-500">{{$message}}</p>
                                                            @enderror
                                                            <label class="text-sm text-gray-500 mb-2"
                                                                   for="email">E-mail</label>
                                                            <input type="email" name="email" placeholder="E-mail"
                                                                   value="{!! old('email') !!}"
                                                                   class="mb-5 shadow appearance-none border focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500"/>
                                                            @error('email')
                                                            <p class="text-red-500">{{$message}}</p>
                                                            @enderror
                                                            <label class="text-sm text-gray-500 mb-2"
                                                                   for="phone">{{__('Номер телефона')}}</label>
                                                            <input type="text"  name="phone_number" value="{{old('phone_number')}}" id="phone"
                                                                   class="mb-5 shadow appearance-none border phone focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500 "/>
                                                            @error('phone_number')
                                                            <p class="text-red-500">{{$message}}</p>
                                                            @enderror
                                                            <label class="text-sm text-gray-500 mb-2" for="password">{{__('Пароль')}}</label>
                                                            <input type="password"  name="password" value="{{old('password')}}" id="password" placeholder="{{__('Пароль')}}"
                                                                   class="mb-5 shadow appearance-none border phone focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500 "/>
                                                            @error('password')
                                                            <p class="text-red-500">{{$message}}</p>
                                                            @enderror
                                                            <label class="text-sm text-gray-500 mb-2" for="password">{{__('Подтвердите пароль')}}</label>
                                                            <input type="password"  name="password_confirmation" value="{{old('password')}}" id="password_confirmation" placeholder="{{__('Подтвердите пароль')}}"
                                                                   class="mb-5 shadow appearance-none border phone focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500 "/>
                                                            @error('password_confirmation')
                                                            <p class="text-red-500">{{$message}}</p>
                                                            @enderror

                                                            <div class="mt-4">
                                                                <div class="flex w-full mt-4">
                                                                    <a onclick="myFunction()"
                                                                       class="bg-white my-4 cursor-pointer hover:border-yellow-500 text-gray-600 hover:text-yellow-500 transition duration-300 font-normal text-base py-3 sm:px-8 px-6 rounded-2xl  border border-2">
                                                                        {{__('Назад')}}
                                                                        <script>
                                                                            function myFunction() {
                                                                                window.history.back();
                                                                            }
                                                                        </script>
                                                                    </a>
                                                                    <input type="submit" style="background: linear-gradient(164.22deg, #FDC4A5 4.2%, #FE6D1D 87.72%);"
                                                                           class="bg-yellow-500 hover:bg-yellow-600 m-4 cursor-pointer text-white font-normal text-2xl py-3 sm:px-14 px-8 rounded-2xl "
                                                                           name="" value="{{__('Отправить')}}">
                                                                </div>
                                                            </div>
                                                        </form>
                                                    @endguest
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            @guest()
                                <div class="tab-pane fade   @if(session()->has('phone') ) error @endif "
                                     id="tabs-profile3" role="tabpanel" aria-labelledby="tabs-profile-tab3">
                                    <form action="{{route('task.create.contact.store.login', $task->id)}}"
                                          method="POST">
                                        @csrf

                                        <label>
                                            <span class="text-gray-500 text-sm">
                                                {{__('Телефонный номер')}}
                                            </span>
                                            <input type="text"  name="phone_number"
                                                   placeholder="{{__('Номер телефона')}}" id="phone2"
                                                   value="{{ old('phone_number') }}"
                                                   class="mt-2 shadow appearance-none phone border focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500"/>
                                        </label>
                                        @error('phone_number')
                                        <p class="text-red-500">{{ $message }}</p>
                                        @enderror

                                        <div class="mt-4">
                                            <div class="flex w-full mt-4">
                                                <a onclick="myFunction()"
                                                   class="bg-white my-4 cursor-pointer hover:border-yellow-500 text-gray-600 hover:text-yellow-500 transition duration-300 font-normal text-base py-3 sm:px-6 px-2 rounded-2xl border border-2">
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
                                                       class="bg-yellow-500 hover:bg-yellow-600 m-4 cursor-pointer text-white font-normal text-2xl py-3 sm:px-14 px-10 rounded-2xl "
                                                       value="{{__('Отправить')}}">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
            <x-faq/>
        </div>
    </div>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js'></script>
    <script>
        if ($('.tab-content').children(".error").length) {
            $('.tab-content').children('.tab-pane').removeClass('show active')
            $('.tab-name').removeClass('active')
            $('.error').addClass('show active')
        }

        var element = document.getElementById('phone');
        var element2 = document.getElementById('phone2');
        var maskOptions = {
            mask: '+998(00)000-00-00',
            lazy: false
        }
        var mask = new IMask(element, maskOptions);

        if (element2)
        {
            var mask2 = new IMask(element2, maskOptions);
        }
    </script>

@endsection
