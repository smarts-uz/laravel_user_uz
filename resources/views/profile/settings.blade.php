@extends("layouts.app")

@section("content")
    <style>.flatpickr-calendar {
            max-width: 300px;
            width: 100%;
        } </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/ru.js"></script>
    <div class="w-11/12  mx-auto text-base mt-4">
        <div class="grid lg:grid-cols-3 grid-cols-2 lg:w-5/6 w-full mx-auto">
            {{-- user ma'lumotlari --}}
            <div class="md:col-span-2 col-span-3 px-2 mx-3">
                @include('components.profileFigure')
                {{-- user ma'lumotlari tugashi --}}
                <div class="content mt-20 ">
                    <div class="grid grid-cols-10">
                        <ul class=" md:col-span-9 col-span-10 md:items-left items-center">
                            <li class="inline md:mr-5 mr-1"><a href="/profile"
                                                               class="text-lg text-gray-600">{{__('Обо мне')}}</a>
                            </li>
                            <li class="inline md:mr-5 mr-1"><a href="/profile/cash"
                                                               class="text-lg text-gray-600 md:mx-0 mx-3">{{__('Счет')}}</a>
                            </li>
                            <li class="inline md:mr-5 mr-1 md:hidden block"><a href="/profile/settings"
                                                                               class="text-lg border-b-4 border-green-500 pb-3 text-gray-700"
                                                                               id="settingsText">{{__('Настройки')}}</a>
                            </li>

                        </ul>
                        <div class="md:col-span-1 md:block hidden ml-4" id="settingsIcon"><a href="/profile/settings"><i
                                    class="fas fa-cog text-2xl border-b-4 border-green-500 pb-3"></i></a></div>
                    </div>

                    <hr class="md:mt-0 mt-3">


                    {{-- settings start --}}
                    <div class="w-full text-base">
                        <!-- settings form TABS -->
                        <div class="w-full mx-auto mt-4  rounded">
                            <!-- Tabs -->
                            <ul id="tabs" class="md:inline-flex block w-full flex-center px-1 pt-2">
                                <li class="xl:px-4 md:px-2 py-2 tab-name md:ring-0 w-full md:w-inherit font-semibold text-gray-800 border-b-2 border-blue-400 opacity-50">
                                    <a id="default-tab"
                                       href="#first">{{__('Общие настройки')}}</a></li>
                                <li class="xl:px-4 md:px-2 py-2  tab-name md:ring-0 w-full md:w-inherit font-semibold text-gray-800 opacity-50">
                                    <a href="#second">{{__('Уведомления')}}</a></li>
                                <li class="xl:px-2 md:px-2 py-2 tab-name md:ring-0 w-full md:w-inherit font-semibold text-gray-800 opacity-50">
                                    <a href="#third">{{__('Подписка на задания')}}</a></li>
                                <li class="xl:px-4 md:px-2 tab-name py-2  @if($errors->has('password')) error  @endif  md:ring-0 w-full md:w-inherit font-semibold text-gray-800 opacity-50">
                                    <a href="#fourth">{{__('Безопасность')}}</a></li>
                            </ul>
                            <!-- Tab Contents -->
                            <div id="tab-contents" class="w-full">
                                <div id="first" class="p-4 tab-pane w-full">
                                    <div class="flex justify-left w-full">
                                        <div class="md:w-3/5 w-full md:m-4 m-0">
                                            <h1 class="block w-3/5 text-left text-gray-800 text-3xl font-bold mb-6">
                                                {{__('Личные данные')}}</h1>
                                            <form action="{{route('profile.updateData')}}" class="w-full" method="POST">
                                                @csrf
                                                <div class="w-full mb-4">
                                                    <label class="mb-2 text-md md:block text-gray-400"
                                                           for="name">{{__('Имя')}}</label>
                                                    <div
                                                        class="focus:outline-none w-full focus:border-yellow-500 rounded-xl border py-2 px-3 w-full text-grey-900">
                                                        <p>{{$user->name}}</p>
                                                    </div>
                                                </div>
                                                <div class="w-full block w-full mb-4">
                                                    <label class="mb-2 text-md md:block text-gray-400"
                                                           for="email">Email</label>
                                                    <input
                                                        class="focus:outline-none focus:border-yellow-500  rounded-xl border py-2 px-3 w-full text-grey-900"
                                                        type="email" name="email" id="email"
                                                        value="{{ $user->is_email_verified?$user->email??old('email'):$user->email_old}}">
                                                    @error('email')
                                                    <p class="text-red-500">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="w-full block w-full mb-4">
                                                    <label class="mb-2 text-md md:block text-gray-400"
                                                           for="phone_number">{{__('Телефон')}}</label>
                                                    <input
                                                        class="focus:outline-none focus:border-yellow-500 rounded-xl border py-2 px-3 w-full text-grey-900"
                                                        type="text" id="phone_number" name="phone_number"
                                                        @if (!$user->phone_number) placeholder="+998(00)000-00-00"
                                                        @else
                                                            value="{{$user->is_phone_number_verified?$user->phone_number:$user->phone_number_old}}"
                                                        @endif >
                                                    @error('phone_number')
                                                    <p class="text-red-500">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="w-full block w-full mb-4">
                                                    <label for="date"
                                                           class="mt-3 text-gray-500 text-sm">{{__('Дата рождения')}}</label>
                                                    <div class="flatpickr inline-block flex items-center">
                                                        <div class="flex-shrink">
                                                            <input type="text" name="born_date"
                                                                   value="{{auth()->user()->born_date}}"
                                                                   placeholder="{{__('Какой месяц..')}}" data-input
                                                                   class=" bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm text-xs rounded-lg focus:outline-none focus:border-yellow-500 block pl-10 p-2.5 dark:bg-gray-700 dark:border-yellow-300 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                                   required> <!-- input is mandatory -->
                                                        </div>
                                                        <div class="flatpickr-calendar w-full sm:text-sm"></div>
                                                        <div class="transform hover:scale-125 relative right-8">
                                                            <a class="input-button w-1 h-1" title="toggle" data-toggle>
                                                                <i class="far fa-calendar-alt fill-current text-yellow-500 cursor-pointer"></i>
                                                            </a>
                                                        </div>
                                                        <div class="transform hover:scale-125">
                                                            <a class="input-button w-1 h-1 " title="clear" data-clear>
                                                                <i class="fas fa-trash-alt stroke-current text-red-600 cursor-pointer"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="w-full block w-full mb-6 mt-2 flex gap-x-5">
                                                    <div class="flex items-center gap-x-2">
                                                        <input type="radio" name="gender" id="male" value="1"
                                                               class="h-4 w-4" {{$user->gender==1 ? 'checked' : ''}}>
                                                        <label for="male"
                                                               class="text-gray-800 text-lg cursor-pointer">{{__('Мужской')}}</label>
                                                    </div>
                                                    <div class="flex items-center gap-x-2">
                                                        <input type="radio" name="gender" id="fermale" value="0"
                                                               class="h-4 w-4" {{$user->gender==0 ? 'checked' : ''}}>
                                                        <label for="fermale"
                                                               class="text-gray-800 text-lg cursor-pointer">{{__('Женской')}}</label>
                                                    </div>
                                                </div>
                                                <div class="w-full block w-full mb-4">
                                                    <label class="mb-2 text-md md:block text-gray-400"
                                                           for="location">{{__('Город')}}</label>
                                                    <select
                                                        class="border rounded-xl py-2 px-3 w-full focus:border-yellow-500 text-grey-900 outline-none"
                                                        name="location">
                                                        <option value="">{{__('Выберите город')}}</option>
                                                        @foreach($regions as $region)
                                                            <option
                                                                value="{{$region->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale')}}" {{$region->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') == $user->location??old('location') ? 'selected' : null}}>{{$region->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale')}}</option>
                                                        @endforeach

                                                    </select>

                                                    @error('location')
                                                    <p class="text-red-500">{{ $message }}</p>
                                                    @enderror

                                                </div>
                                                <input type="submit"
                                                       class="block xl:w-3/5 lg:w-3/4 sm:w-3/5 w-full text-center bg-green-400 hover:bg-green-600 text-white uppercase p-4 rounded-xl mb-5 cursor-pointer"
                                                       name="submit1" value="{{__('Сохранить')}}">
                                                <hr>
                                            </form>

                                            <a href="{{ route('profile.destroy') }}" onclick="ConfirmDelete()"
                                               class="block xl:w-3/5 lg:w-3/4 sm:w-3/5 w-full text-center bg-red-400 hover:bg-red-600 text-white mt-5 uppercase p-4 rounded-xl cursor-pointer">{{__('Удалить профиль')}}</a>
                                        </div>
                                    </div>
                                    {{-- settings/ first tab -> base settings end--}}
                                </div>
                                <div id="second" class="hidden tab-pane tab-pane p-4">
                                    {{-- settings/ second tab -> enable notification start --}}
                                    <div class="md:w-4/5 w-full mt-5">
                                        <h3 class="font-bold text-3xl">{{__('Получать уведомления:')}}</h3>
                                        <div class="grid grid-cols-10 mt-5">
                                            <input type="checkbox" id="notif_checkbox2"
                                                   {{$user->news_notification==1 ? 'checked' : ''}} class="w-5 h-5 col-span-1 my-auto mx-auto"/>
                                            <span class="col-span-9 ml-2">{{__('Я хочу получать новости сайта')}}</span>
                                        </div>
                                        <button onclick="ajax_func()"
                                                class="block  md:w-1/2 w-full mt-10 bg-green-400 hover:bg-green-600 text-white uppercase p-4 rounded-xl"
                                                type="submit">{{__('Сохранить')}}</button>
                                    </div>
                                    {{-- settings/ second tab -> enable notification end --}}
                                </div>
                                <div id="third" class="hidden tab-pane tab-pane p-4">
                                    {{-- settings/ third tab start -> subscribe for some tasks --}}
                                    <div class="sm:w-4/5 w-full mt-10">
                                        <h3 class="font-bold text-3xl mb-7">1. {{__('Выберите категории')}}</h3>
                                        {{-- choosing categories --}}
                                        <form action="{{route('profile.getCategory')}}" method="post">
                                            @csrf
                                            <div class="acordion mt-16">
                                                @foreach ($categories as $category )

                                                    <div
                                                        class="mb-4 rounded-md border shadow-md py-2 pl-3 bg-yellow-100">
                                                        <div
                                                            class="accordion text-gray-700 cursor-pointer w-full text-left text-lg">
                                                            {{ $category->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}
                                                        </div>
                                                        <div
                                                            class="panel overflow-hidden hidden px-[18px] bg-white p-2 bg-yellow-100">
                                                            @foreach ($categories2 as $category2)
                                                                @if($category2->parent_id == $category->id)
                                                                    <label
                                                                        class="block my-1 text-base flex items-center">
                                                                        @php
                                                                            $cat_arr = explode(",",$user->category_id);
                                                                              $res_c_arr = array_search($category2->id,$cat_arr);
                                                                        @endphp
                                                                        <input type="checkbox"
                                                                               @if($res_c_arr !== false) checked
                                                                               @endif name="category[]"
                                                                               value="{{$category2->id}}"
                                                                               class="mr-2 required:border-yellow-500 h-4 w-4">{{ $category2->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}
                                                                    </label>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="font-bold text-xl mb-7"> {{__('Дополнительные типы уведомлений:')}}</p>
                                            <div class="flex sm:flex-row flex-col itens-center">
                                                <div class="items-center mr-8">
                                                    <input type="checkbox" id="sms" name="sms_notification" value="1"
                                                           {{$user->sms_notification==1 ? 'checked' : ''}} class="w-5 h-5"/>
                                                    <label class="cursor-pointer text-2xl" for="sms">
                                                        <i class="fas fa-mobile text-yellow-600 text-2xl mx-1"></i>{{__('SMS')}}
                                                    </label>
                                                </div>
                                                <div class="items-center">
                                                    <input type="checkbox" id="email_notif" name="email_notification"
                                                           value="1"
                                                           {{$user->email_notification==1 ? 'checked' : ''}} class="w-5 h-5"/>
                                                    <label class="cursor-pointer mx-1 text-xl" for="email_notif">
                                                        <i class="fas fa-envelope text-yellow-600 text-2xl mx-1"></i>{{__('EMAIL')}}
                                                    </label>
                                                </div>
                                            </div>
                                            <button
                                                class="block  md:w-1/2 w-full mt-10 bg-green-400 hover:bg-green-600 text-white uppercase p-4 rounded-xl"
                                                type="submit">{{__('Сохранить')}}</button>
                                        </form>
                                        <script>
                                            var acc = document.getElementsByClassName("accordion");
                                            var i;

                                            for (i = 0; i < acc.length; i++) {
                                                acc[i].addEventListener("click", function () {
                                                    this.classList.toggle("active");
                                                    var panel = this.nextElementSibling;
                                                    if (panel.style.display === "block") {
                                                        panel.style.display = "none";
                                                    } else {
                                                        panel.style.display = "block";
                                                    }
                                                });
                                            }
                                        </script>

                                    </div>
                                    {{-- settings/ third tab end -> subscribe for some tasks --}}
                                </div>
                                <div id="fourth"
                                     class="hidden tab-pane @if($errors->has('password')) error  @endif py-4">

                                    <div class="container max-w-full mx-auto">
                                        <div class="">
                                            <div class="max-w-sm">
                                                <div class="relative flex flex-wrap">
                                                    <div class="w-full relative">
                                                        <div class="mt-6">
                                                            <h2 class="font-bold text-black text-3xl">
                                                                {{__('Изменить пароль')}}
                                                            </h2>
                                                            <ul class="mt-10">
                                                                <li class="flex gap-2 mt-2">
                                                                    <i class="fas fa-check"></i>
                                                                    <p class="text-sm">
                                                                        {{__('длина — не менее 8 символов')}}</p>
                                                                </li>
                                                            </ul>
                                                            <form class="mt-8"
                                                                  action="{{route('profile.change_password')}}"
                                                                  method="post">
                                                                @csrf

                                                                <div class="mx-auto max-w-lg">
                                                                    @if($user->passwrod != '' || $user->passwrod != null)
                                                                        <div class="py-2" x-data="{ show: true }">
                                                                        <span
                                                                            class="px-1 text-sm text-gray-600">{{__('Stariy пароль')}}</span>
                                                                            <div class="relative">
                                                                                <input placeholder=""
                                                                                       name="old_password"
                                                                                       :type="show ? 'password' : 'text'"
                                                                                       class="text-md block px-3 py-2 rounded-lg w-full
                                                                                bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md
                                                                                focus:placeholder-gray-500
                                                                                focus:bg-white
                                                                                focus:border-yellow-400
                                                                                focus:outline-none">
                                                                                <div
                                                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">

                                                                                    <svg class="h-4 text-gray-700"
                                                                                         fill="none"
                                                                                         @click="show = !show"
                                                                                         :class="{'hidden': show, 'block':!show }"
                                                                                         xmlns="http://www.w3.org/2000/svg"
                                                                                         viewbox="0 0 576 512">
                                                                                        <path fill="currentColor"
                                                                                              d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z">
                                                                                        </path>
                                                                                    </svg>

                                                                                    <svg class="h-4 text-gray-700"
                                                                                         fill="none"
                                                                                         @click="show = !show"
                                                                                         :class="{'block': show, 'hidden':!show }"
                                                                                         xmlns="http://www.w3.org/2000/svg"
                                                                                         viewbox="0 0 640 512">
                                                                                        <path fill="currentColor"
                                                                                              d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.07 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144.13 144.13 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zm-183.72-142l-39.3-30.38A94.75 94.75 0 0 0 416 256a94.76 94.76 0 0 0-121.31-92.21A47.65 47.65 0 0 1 304 192a46.64 46.64 0 0 1-1.54 10l-73.61-56.89A142.31 142.31 0 0 1 320 112a143.92 143.92 0 0 1 144 144c0 21.63-5.29 41.79-13.9 60.11z">
                                                                                        </path>
                                                                                    </svg>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    <div class="py-2" x-data="{ show: true }">
                                                                        <span
                                                                            class="px-1 text-sm text-gray-600">{{__('Новый пароль')}}</span>
                                                                        <div class="relative">
                                                                            <input placeholder="" name="password"
                                                                                   :type="show ? 'password' : 'text'"
                                                                                   class="text-md block px-3 py-2 rounded-lg w-full
                                                                                bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md
                                                                                focus:placeholder-gray-500
                                                                                focus:bg-white
                                                                                focus:border-yellow-400
                                                                                focus:outline-none">
                                                                            <div
                                                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">

                                                                                <svg class="h-4 text-gray-700"
                                                                                     fill="none" @click="show = !show"
                                                                                     :class="{'hidden': show, 'block':!show }"
                                                                                     xmlns="http://www.w3.org/2000/svg"
                                                                                     viewbox="0 0 576 512">
                                                                                    <path fill="currentColor"
                                                                                          d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z">
                                                                                    </path>
                                                                                </svg>

                                                                                <svg class="h-4 text-gray-700"
                                                                                     fill="none" @click="show = !show"
                                                                                     :class="{'block': show, 'hidden':!show }"
                                                                                     xmlns="http://www.w3.org/2000/svg"
                                                                                     viewbox="0 0 640 512">
                                                                                    <path fill="currentColor"
                                                                                          d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.07 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144.13 144.13 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zm-183.72-142l-39.3-30.38A94.75 94.75 0 0 0 416 256a94.76 94.76 0 0 0-121.31-92.21A47.65 47.65 0 0 1 304 192a46.64 46.64 0 0 1-1.54 10l-73.61-56.89A142.31 142.31 0 0 1 320 112a143.92 143.92 0 0 1 144 144c0 21.63-5.29 41.79-13.9 60.11z">
                                                                                    </path>
                                                                                </svg>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="py-2" x-data="{ show: true }">
                                                                        <span
                                                                            class="px-1 text-sm text-gray-600">{{__('Повторите пароль')}}</span>
                                                                        <div class="relative">
                                                                            <input placeholder=""
                                                                                   name="password_confirmation"
                                                                                   :type="show ? 'password' : 'text'"
                                                                                   class="text-md block px-3 py-2 rounded-lg w-full
                                                                        bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md
                                                                        focus:placeholder-gray-500
                                                                        focus:bg-white
                                                                        focus:border-yellow-400
                                                                        focus:outline-none">
                                                                        </div>
                                                                    </div>

                                                                    @error('password')
                                                                    <p class="text-red-500">{{ $message }}</p>
                                                                    @enderror
                                                                    <button type="submit" class="mt-8 text-lg font-semibold
                                                                        bg-green-400 w-50 text-white rounded-lg
                                                                        px-6 py-3 block shadow-xl hover:text-white hover:bg-green-500">
                                                                        {{__('Сохранить новый пароль')}}
                                                                    </button>
                                                                </div>
                                                            </form>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="mt-12">
                                                <h1 class="font-bold text-black text-3xl">
                                                    {{__('Активные сессии')}}</h1>

                                                @foreach($sessions as $session)
                                                    <div class="flex sm:flex-row flex-col mt-4 items-center">
                                                        @if(!$session->is_mobile)
                                                            <div class="flex flex-row items-center">
                                                                <i class="fas fa-desktop mr-2 text-yellow-500"></i>
                                                                <p class="mx-1">
                                                                    {{ $session->ip_address == request()->ip()? "Текущая " :"" }}
                                                                    {{ $parser->parse($session->user_agent)->os->family }}
                                                                    ,
                                                                </p>
                                                            </div>
                                                            <h1 class="mx-1">{{ $session->last_active }}, </h1>
                                                            <span class="text-gray-500">
                                                                {{ __('браузер')}}: {{ $parser->parse($session->user_agent)->ua->family }}
                                                            </span>
                                                        @else
                                                            <div class="flex flex-row items-center">
                                                                <i class="fas fa-mobile mr-2 text-yellow-500"></i>
                                                                <p class="mx-1">{{ $session->platform }}, </p>
                                                            </div>
                                                            <h1 class="mx-1">{{ $session->last_active }}, </h1>
                                                            <span
                                                                class="text-gray-500"> {{__('устройство')}}: {{$session->device_name }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                @foreach($sessions as $session)
                                                    @if($session!=null && $loop->index==0)
                                                        <div class="my-5">
                                                            <a href="{{route('profile.clear_sessions')}}" type="btn"
                                                               class="focus:outline-none hover:bg-red-600 btn bg-red-400 uppercase p-2 text-white text-sm rounded-lg">
                                                                {{__('удалить сеансы')}}
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        {{-- scripts --}}
                    </div>
                </div>
            </div>
            {{-- right-side-bar --}}
            @include('auth.profile-side-info')
            {{-- tugashi o'ng tomon ispolnitel --}}

        </div>
    </div>

    <script>

        let notif_11, notif_22;

        function ajax_func() {
            if ($('#notif_checkbox1').is(":checked")) {
                notif_11 = 1;
            } else {
                notif_11 = 0;
            }
            if ($('#notif_checkbox2').is(":checked")) {
                notif_22 = 1;
            } else {
                notif_22 = 0;
            }
            console.log(notif_11)
            console.log(notif_22)
            {{--            @php $id=auth()->user()->id  @endphp--}}
            {{--            let id={{$id}}--}}
            let id = {{auth()->user()->id}}
            console.log(id);
            $.ajax({
                url: "{{route('profile.notif_setting_ajax')}}",
                type: 'GET',
                data: {
                    id: id,
                    notif11: notif_11,
                    notif22: notif_22
                },
                success: function (data) {
                    console.log(data)
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: '{{__('Сохранено')}}',
                        showConfirmButton: false,
                        timer: 2000
                    })
                },
                error: function (error) {
                    console.error("Ajax orqali yuklashda xatolik...", error);
                }
            });
        }

        flatpickr(".flatpickr",
            {
                wrap: true,
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                @if(session('lang')=='ru')
                locale: 'ru',
                @else
                locale: {
                    weekdays: {
                        shorthand: ['Yak', 'Du', 'Se', 'Chor', 'Pay', 'Juma', 'Shan'],
                        longhand: ['Yakshanba', 'Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma', 'Shanba'],
                    },
                    months: {
                        shorthand: ['Yan', 'Фев', 'Mart', 'Apr', 'May', 'Iyun', 'Iyul', 'Avg', 'Sen', 'Okt', 'Noy', 'Dek'],
                        longhand: ['Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun', 'Iyul', 'Avgust', 'Sentabr', 'Oktabr', 'Noyabr', 'Dekabr'],
                    },
                }
                @endif
            }
        )
        $(document).ready(function () {
            $('#fourth a[href="#{{ old('tab') }}"]').tab('show')
        })
    </script>
    <script src="https://unpkg.com/imask"></script>
    <script src="{{ asset('js/profile/setting.js') }}"></script>
@endsection
