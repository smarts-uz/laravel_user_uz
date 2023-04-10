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
                    <ul id="tabs"
                        class="nav nav-tabs flex text-center flex-wrap list-none border-b-0 pl-0 mb-2 justify-center">
                        <li class="bg-white text-xl px-12 text-gray-800 font-semibold hover:bg-gray-200 py-2  text-yellow-500 border-b-2 border-yellow-500 ">
                            <a id="default-tab" href="#first">{{__('Регистрация')}}</a>
                        </li>
                        <li class="px-12 text-xl text-gray-800 hover:bg-gray-200 font-semibold py-2">
                            <a href="#second">{{__('Вход')}}</a>
                        </li>
                    </ul>

                    <div id="tab-contents" class="flex justify-center sm:w-9/12 w-11/12 mx-auto">
                        <div id="first" class="p-2 ">
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
                        </div>
                        <div id="second" class="p-2 hidden">
                            <form action="{{route('task.create.contact.store.login', $task->id)}}" method="POST">
                                @csrf
                                <label>
                                    <span class="text-gray-500 text-sm">
                                        {{__('Телефонный номер')}}
                                    </span>
                                    <input type="text"  name="phone_number" placeholder="{{__('Номер телефона')}}" id="phone2"
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
                    </div>
                </div>
            </div>
            <x-faq/>
        </div>
    </div>
    <script>
        //tab content script start
        let tabsContainer = document.querySelector("#tabs");
        let tabTogglers = tabsContainer.querySelectorAll("#tabs a");
        console.log(tabTogglers);
        tabTogglers.forEach(function (toggler) {
            toggler.addEventListener("click", function (e) {
                e.preventDefault();
                let tabName = this.getAttribute("href");
                let tabContents = document.querySelector("#tab-contents");
                for (let i = 0; i < tabContents.children.length; i++) {
                    tabTogglers[i].parentElement.classList.remove("text-yellow-500", "border-b-2", "border-yellow-500");
                    tabContents.children[i].classList.remove("hidden");
                    if ("#" + tabContents.children[i].id === tabName) {
                        continue;
                    }
                    tabContents.children[i].classList.add("hidden");
                }
                e.target.parentElement.classList.add("text-yellow-500", "border-b-2", "border-yellow-500");
            });
        });

        $('#second').click(function () {
            $(this).removeClass('hidden');
        })
        //tab content script end
    </script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js'></script>
    <script>
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
