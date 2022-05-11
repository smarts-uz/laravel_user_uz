@extends('layouts.app')

@section('content')

    <div class="text-sm w-full bg-gray-200 my-4 py-3">
        <p class="w-8/12 mx-auto text-gray-500 font-normal">{{__('Вы находитесь в разделе исполнителей U-Ser')}} <br>
            {{__("Чтобы предложить работу выбранному исполнителю, нужно нажать на кнопку «Предложить задание» в его профиле.")
            }}</p>
    </div>
    <div class="xl:w-8/12 mx-auto mt-16 text-base">
        <div class="grid grid-cols-3 ">

            {{-----------------------------------------------------------------------------------}}
            {{--                             Left column                                       --}}
            {{-----------------------------------------------------------------------------------}}

            <div class="lg:col-span-1 col-span-3 px-8">
                @if (Auth::check())
                    <a href="/verificationInfo" class="flex flex-row shadow-lg rounded-lg mb-8">
                        <div class="w-1/2 h-24 bg-contain bg-no-repeat bg-center"
                             style="background-image: url({{asset('images/like.png')}});">
                        </div>
                        <div class="font-bold text-xs text-gray-700 text-left my-auto">
                            {!!__('Станьте исполнителем <br> U-Ser. И начните <br> зарабатывать.')!!}
                        </div>
                    </a>
                @else
                    <a href="/login" class="flex flex-row shadow-lg rounded-lg mb-8">
                        <div class="w-1/2 h-24 bg-contain bg-no-repeat bg-center"
                             style="background-image: url({{asset('images/like.png')}});">
                        </div>
                        <div class="font-bold text-xs text-gray-700 text-left my-auto">
                            {!!__('Станьте исполнителем <br> U-Ser. И начните <br> зарабатывать.')!!}
                        </div>
                    </a>
                @endif

                <div>
                    <div class="max-w-md mx-left">
                        @foreach ($categories as $category)
                            <div x-data={show:false} class="rounded-sm">
                                <div class="my-3 text-blue-500 hover:text-red-500 cursor-pointer"
                                     id="{{ str_replace(' ', '', $category->name) }}">
                                    {{ $category->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}
                                </div>
                                <div id="{{$category->slug}}" class="px-8 py-1 hidden">
                                    @foreach ($categories2 as $category2)
                                        @if($category2->parent_id == $category->id)
                                            <div>
                                                <a href="/perf-ajax/{{ $category2->id }}"
                                                   class="text-blue-500 cursor-pointer hover:text-red-500 my-1 send-request"
                                                   data-id="{{$category2->id}}">{{ $category2->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}</a>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 col-span-3 lg:mt-0 mt-16">
                <div class="bg-gray-100 h-40 rounded-xl w-full sm:mx-0 mx-auto">
                    <div class="font-bold text-2xl mx-8 py-4">
                        <p>{{__('Все исполнители')}}</p>
                    </div>
                    <div class="form-check flex flex-row mx-8 mt-10">
                        <input
                            class="focus:outline-none  form-check-input h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-black-600 checked:border-black-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer"
                            type="checkbox" id="online">
                        <label class="form-check-label inline-block text-gray-800" for="online">
                            {{__('Сейчас на сайте')}}
                        </label>
                    </div>
                </div>
                <div class="sortable">
                    @foreach($users as $user)
                        <div
                            class="difficultTask score scores{{$user->id}} w-12/12 m-5 h-[200px] flex md:flex-none overflow-hidden md:overflow-visible mb-10 "
                            id="{{$user->id}}">
                            <div class=" float-left">
                                <img class="rounded-lg w-32 h-32 bg-black mb-4 mr-4"
                                     @if ($user->avatar === null) src='{{asset("storage/images/default.jpg")}}'
                                     @else src="{{asset("storage/{$user->avatar}")}}" @endif alt="avatar">
                                <div class="flex sm:flex-row items-center text-base">
                                    <p class="text-black ">{{__('Отзывы:')}}</p>
                                    <i class="far fa-thumbs-up text-blue-500 ml-1 mb-1"></i>
                                    <span class="text-gray-800 mr-2 ">{{$user->review_good}}</span>
                                    <i class="far fa-thumbs-down mt-0.5 text-blue-500"></i>
                                    <span class="text-gray-800">{{$user->review_bad}}</span>
                                </div>
                                <div class="flex items-center" id="stars{{$user->id}}">
                                </div>
                            </div>
                            <div class="w-4/5 ">
                                <div class="flex sm:flex-row flex-col sm:items-center items-start">
                                    @if (Auth::check() && Auth::user()->id == $user->id)
                                        <a href="/profile"
                                           class="lg:text-3xl mr-2 text-2xl underline text-blue-500 hover:text-red-500"
                                           id="{{$user->id}}">
                                            {{$user->name}}
                                        </a>
                                    @else
                                        <a class="user mr-2" href="performers/{{$user->id}}">
                                            <p class="lg:text-3xl text-2xl underline text-blue-500 performer-page{{$user->id}} hover:text-red-500"
                                               id="{{$user->id}}"> {{$user->name}} </p>
                                        </a>
                                    @endif
                                    <div class="flex items-center sm:my-0 my-2">
                                        @if ($user->is_email_verified && $user->is_phone_number_verified)
                                            <div data-tooltip-target="tooltip-animation-verified"
                                                 class="mx-1 tooltip-1">
                                                <img
                                                    src="{{asset('images/verify.png')}}"
                                                    alt="" class="w-10">
                                                <div id="tooltip-animation-verified" role="tooltip"
                                                     class="inline-block sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                                    <p class="text-center">
                                                        {{__('Номер телефона и Е-mail пользователя подтверждены')}}
                                                    </p>
                                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                                </div>
                                            </div>
                                        @else
                                            <div data-tooltip-target="tooltip-animation-not-verified"
                                                 class="mx-1 tooltip-1">
                                                <img
                                                    src="{{asset('images/verify_gray.png') }}"
                                                    alt="" class="w-10">
                                                <div id="tooltip-animation-not-verified" role="tooltip"
                                                     class="inline-block sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                                    <p class="text-center">
                                                        {{__('Номер телефона и Е-mail пользователя неподтверждены')}}
                                                    </p>
                                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                                </div>
                                            </div>
                                        @endif
                                        @if(in_array($user->id, $top_users))
                                            <div data-tooltip-target="tooltip-animation-on-top"
                                                 class="mx-1 tooltip-2">
                                                <img src="{{ asset('images/best.png') }}" alt="" class="w-10">
                                                <div id="tooltip-animation-on-top" role="tooltip"
                                                     class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                                    <p class="text-center">
                                                        {{__('Входит в ТОП-20 исполнителей User.uz')}}
                                                    </p>
                                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                                </div>
                                            </div>
                                        @else
                                            <div data-tooltip-target="tooltip-animation-on-top"
                                                 class="mx-1 tooltip-2">
                                                <img src="{{ asset('images/best_gray.png') }}" alt="" class="w-10">
                                                <div id="tooltip-animation-on-top" role="tooltip"
                                                     class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                                    <p class="text-center">
                                                        {{__('Невходит в ТОП-20 исполнителей User.uz')}}
                                                    </p>
                                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                                </div>
                                            </div>
                                        @endif
                                        <div data-tooltip-target="tooltip-animation-many" class="mx-1">
                                            @if(($user->review_good)+($user->review_bad) >= 50)
                                                <img src="{{ asset('images/50.png') }}" alt="" class="w-10">
                                            @else
                                                <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-10">
                                            @endif
                                            <div id="tooltip-animation-many" role="tooltip"
                                                 class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                                <p class="text-center">
                                                    {{__('Более 50 выполненных заданий')}}
                                                </p>
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    @if(Cache::has('user-is-online-' . $user->id))
                                        <span id="only" class="text-green-500">Online</span>
                                    @else
                                        <span
                                            class="text-gray-500"> {{ Carbon\Carbon::parse($user->last_seen)->diffForHumans() }}</span>
                                    @endif

                                </div>
                                <div>
                                    <p class="text-base  leading-0  ">
                                        {{substr($user->description,0,100)}}
                                        @if(strlen($user->description) >= 100)
                                            ...
                                        @endif
                                    </p>
                                </div>
                                <div class="mt-6">
                                    @auth
                                        @if($tasks->count() > 0 && Auth::user()->id != $user->id)
                                            <a id="open{{$user->id}}">
                                                <button
                                                    class="cursor-pointer rounded-lg py-2 px-1 md:px-3 font-bold bg-yellow-500 hover:bg-yellow-600 transition duration-300 text-white"
                                                    onclick="$('#performer_id').val({{$user->id}}); $('#performer_id_task').val({{$user->id}});">
                                                    {{__('Предложить задание')}}
                                                </button>
                                            </a>
                                        @elseif ($tasks->count() > 0 && Auth::user()->id == $user->id)
                                            <a class="hidden lg:block">
                                                <button
                                                    class="rounded-lg py-2 px-1 md:px-3 font-bold bg-yellow-500 hover:bg-yellow-600 transition duration-300 text-white mt-3">
                                                    {{__('Предложить задание')}}</button>
                                            </a>
                                        @else
                                            <a onclick="toggleModal12('modal-id12')" class="hidden lg:block">
                                                <button
                                                    class="rounded-lg py-2 px-1 md:px-3 font-bold bg-yellow-500 hover:bg-yellow-600 transition duration-300 text-white mt-3">
                                                    {{__('Предложить задание')}}</button>
                                            </a>
                                        @endif
                                        <input type="hidden" id="performer_id" value="">
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                    {{ $users->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>


    <div id="modal_content"
         class="modal_content hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
         style="background-color:rgba(0,0,0,0.5)">
        <div class="modal relative w-auto mt-12 mx-auto max-w-3xl">
            <div
                class="border-0 rounded-lg shadow-2xl px-10 relative flex mx-auto flex-col sm:w-4/5 w-full bg-white outline-none focus:outline-none text-center py-12">
                <h1 class="text-3xl font-semibold">{{__('Выберите задание, которое хотите предложить исполнителью')}}</h1>
                @foreach($tasks as $task)
                    <label>
                        <input type="text" name="tasks_id" class="hidden" value="{{ $task->id }}">
                    </label>
                @endforeach

                <select name="tasks" id="task_name" onchange="showDiv(this)"
                        class="appearance-none focus:outline-none border border-solid border-gray-500 rounded-lg text-gray-500 px-6 py-2 text-lg mt-6 hover:text-yellow-500  hover:border-yellow-500 hover:shadow-xl shadow-yellow-500 mx-auto block"><br>

                    @foreach($tasks as $task)
                        @auth
                            @if ($task->status <= 2)
                                <option value="{{ $task->id }}">
                                    {{ $task->name }}
                                </option>
                            @endif
                        @endauth
                    @endforeach
                    <option value="1">
                        + {{__('новое задание')}}
                    </option>
                </select>
                <label>
                    <input type="text" name="csrf" class="hidden" value="{{ csrf_token() }}">
                </label>

                <div id="hidden_div">
                    <button type="submit" onclick="myFunc()"
                            class="cursor-pointer bg-red-500 text-white rounded-lg p-2 px-4 mt-4">
                        {{__('Предложить работу')}}
                    </button>
                    <p class="py-7">
                        {{__('Каждое задание можно предложить пяти исполнителям из каталога. исполнители получат СМС со ссылкой на ваше задание.')}}</p>
                </div>


                <form action="{{route('profile.set_session')}}" method="POST">
                    @csrf
                    <input type="hidden" name="performer_id" id="performer_id_task">
                    <button id="hidden_div2" type="submit"
                            class="cursor-pointer bg-green-500 text-white rounded-lg p-2 px-4 mt-6 mx-auto"
                            style="display: none;">
                        {{__('Создать новое задание')}}
                    </button>
                </form>

                <button
                    class="cursor-pointer close text-gray-400 font-bold rounded-lg p-2 px-4 mt-6 absolute -top-6 right-0 text-2xl">
                    x
                </button>
            </div>
        </div>
    </div>

    <!-- Основной контент страницы -->
    <div id="modal" style="display: none">
        <div class="modal h-screen w-full fixed left-0 top-0 flex justify-center items-center bg-black bg-opacity-50">
            <!-- modal -->
            <div class="bg-white rounded shadow-lg w-10/12 md:w-1/3 text-center py-12">
                <!-- modal header -->

                <div class="text-2xl font-bold my-6">
                    {{__('Мы отправили ему уведомление.')}}
                </div>
                <button onclick="myFunction1()"
                        class="cursor-pointer bg-green-500 text-white rounded-lg p-2 px-4 mt-6 mx-auto">
                    ok
                </button>
            </div>
        </div>
    </div>

    {{-- Modal start --}}
    <div
        class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
        id="modal-id12" style="background-color:rgba(0,0,0,0.5)">
        <div class="relative w-auto my-6 mx-auto max-w-3xl" id="modal-id12">
            <div
                class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
                <div class=" text-center p-12  rounded-t">
                    <button type="submit" onclick="toggleModal12('modal-id12')"
                            class="rounded-md w-100 h-16 absolute top-1 right-4">
                        <i class="fas fa-times  text-slate-400 hover:text-slate-600 text-xl w-full"></i>
                    </button>
                    <h3 class="font-medium text-4xl block mt-4">
                        {!!__('У вас пока нет опубликованных <br> заданий')!!}
                    </h3>
                </div>
                <!--body-->
                <div class="relative p-6 flex-auto">
                    <p class="my-4   text-center">
                        {!!__('Создайте задание, после чего вы сможете предложить <br> выполнить его исполнителям.')!!}
                    </p>
                </div>
                <!--footer-->
                <div class="flex mx-auto items-center justify-end p-6 rounded-b mb-8">
                    <div class="mt-4 ">
                        <form action="{{route('profile.set_session')}}" method="POST">
                            @csrf
                            <input type="hidden" name="performer_id" id="performer_id_task">
                            <button type="submit"
                                    class="bg-green-500 rounded-lg text-white text-xl py-3 px-6 hover:bg-green-600">
                                {{__('Создать новое задание')}}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id12-backdrop"></div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/raty/3.1.1/jquery.raty.min.css"
          integrity="sha512-XsO5ywONBZOjW5xo5zqAd0YgshSlNF+YlX39QltzJWIjtA4KXfkAYGbYpllbX2t5WW2tTGS7bmR0uWgAIQ8JLQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://code.jquery.com/jquery-3.6.0.js"
            integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-raty-js@2.8.0/lib/jquery.raty.min.js"></script>

    <script>
        @foreach ($users as $user)
        // let star = $('.review{{$user->id}}').text();
        $("#stars{{$user->id}}").raty({
            path: 'https://cdn.jsdelivr.net/npm/jquery-raty-js@2.8.0/lib/images',
            readOnly: true,
            score: {{$user->review_rating ?? 0}},
            size: 12
        });
        @endforeach
    </script>
    @if($user->role_id == 2)
        <script>
            if ($('.tooltip-2').length === 0) {
                $("<div data-tooltip-target='tooltip-animation_2' class='mx-4 tooltip-2' ><img src='{{ asset("images/best_gray.png") }}'alt='' class='w-24'><div id='tooltip-animation_2' role='tooltip' class='inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700'><p class='text-center'>{{__('Невходит в ТОП-20 всех исполнителей User.uz')}}</p><div class='tooltip-arrow' data-popper-arrow></div> </div></div>").insertAfter($(".tooltip-1"));
            }
        </script>
    @endif
    <script>
        @foreach ($categories as $category)
        $("#{{ str_replace(' ', '', $category->name) }}").click(function () {
            if ($("#{{$category->slug}}").hasClass("hidden")) {
                $("#{{$category->slug}}").removeClass('hidden');
            } else {
                $("#{{$category->slug}}").addClass('hidden');
            }
        });
        @endforeach
    </script>
    <script type="text/javascript">
        function toggleModal12(modalID12) {
            document.getElementById(modalID12).classList.toggle("hidden");
            document.getElementById(modalID12 + "-backdrop").classList.toggle("hidden");
            document.getElementById(modalID12).classList.toggle("flex");
            document.getElementById(modalID12 + "-backdrop").classList.toggle("flex");
        }
    </script>
    <script> //Bu scriptda Active Performers id lari User table dan Ajax orqali chaqililadi va ekranga chiqaziladi.
        let activePerformersId = [];
        $('#online').click(function () {
            let id, find;
            if (this.checked == true) {
                $.ajax({
                    url: "{{route('performers.active_performers')}}",
                    type: 'GET',
                    success: function (data) {
                        activePerformersId = $.parseJSON(JSON.stringify(data));
                        $('.difficultTask').each(function () {
                            id = $(this).attr('id');
                            find = 0;
                            $.each(activePerformersId, function (index, activePerformersId) {
                                if (activePerformersId.id == id) {
                                    find = 1;
                                }
                            });
                            if (find) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    },
                    error: function (error) {
                        console.error("Ajax orqali yuklashda xatolik...", error);
                    }
                });
            } else {
                $('.difficultTask').each(function () {
                    $(this).show();
                });
            }
        });
    </script>

    <script>
        @foreach($users as $user)
        $("#open{{$user->id}}").click(function () {
            var username = $(".{{$user->id}}").text();
            var namem = $(".namem").text('{{__('Вы предложили задание исполнителю')}}' + username);
            $(".modal_content").show();
            let user_id = $('#performer_id').val();//$('.{{$user->id}}').attr('id');
            $.ajax({
                url: "/give-task",
                type: "POST",
                data: {
                    user_id: user_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log(response);
                    if (response) {
                        $('.success').text(response.success);
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });
        $(".close").click(function () {
            $(".modal_content").hide();
        });
        @endforeach
    </script>
    <script type="text/javascript">
        function showDiv(select) {
            if (select.value == 0) {
                document.getElementById('hidden_div').style.display = "block";
            }
            if (select.value == 1) {
                document.getElementById('hidden_div').style.display = "none";
                document.getElementById('hidden_div2').style.display = "block";
            } else {
                document.getElementById('hidden_div2').style.display = "none";
                document.getElementById('hidden_div').style.display = "block";

            }
        }
    </script>

    <script>
        function myFunc() {
            document.getElementById('modal').style.display = "block";
            document.getElementById('modal_content').style.display = "none";
            let task_id = $("#task_name").val();
            $.ajax({
                url: "/give-task",
                type: "POST",
                data: {
                    task_id: task_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log(response);
                    if (response) {
                        $('.success').text(response.success);
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        };

        function myFunction1() {
            document.getElementById('modal').style.display = "none";
            document.getElementById('modal_content').style.display = "none";
        };
    </script>
@endsection

@section('javasript')
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        $(".score").slice(0, 2).attr('about', 1);
    </script>
    <script>
        @foreach($users as $user)
        $('.performer-page{{$user->id}}').click(function () {
            $.ajax({
                url: "/performers/{{$user->id}}",
                type: "GET",
                data: {
                    about: $(".scores{{$user->id}}").attr('about'),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log(response);
                    if (response) {
                        $('.success').text(response.success);
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });
        @endforeach
        $('.send-request').click(function () {
            $.ajax({
                url: '/performers-by-category', //PHP file to execute
                type: 'GET', //method used POST or GET
                data: {'category_id': $(this).data('id')}, // Parameters passed to the PHP file
                success: function (result) { // Has to be there !
                },

                error: function (result, statut, error) { // Handle errors

                }

            });
        })
        // @foreach($users as $user)
        //     var star = $('#review{{$user->id}}').text();
        //     if (star > 0) {
        //         for (let i = 0; i < star; i++) {
        //             $("#stars{{$user->id}}").append('<i class="fas fa-star text-yellow-500"></i>');
        //         }
        //         for (let u = star; u < 5; u++) {
        //             $("#stars{{$user->id}}").append('<i class="fas fa-star text-gray-500"></i>');
        //         }
        //     }
        //     else {
        //         for (let e = 0; e < 5; e++) {
        //             $("#stars{{$user->id}}").append('<i class="fas fa-star text-gray-500"></i>');
        //         }
        //     }
        // @endforeach
    </script>
@endsection

