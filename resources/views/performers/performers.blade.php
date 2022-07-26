@extends('layouts.app')

@section('content')

    <div class="text-sm w-full bg-gray-200 my-4 py-3">
        <p class="w-8/12 mx-auto text-gray-500 font-normal">{{__('Вы находитесь в разделе исполнителей U-Ser')}} <br>
            {{__("Чтобы предложить работу выбранному исполнителю, нужно нажать на кнопку «Предложить задание» в его профиле.")}}</p>
    </div>
    <div class="xl:w-9/12 container mx-auto mt-16 text-base">
        <div class="grid grid-cols-3 ">

            {{-----------------------------------------------------------------------------------}}
            {{--                             Left column                                       --}}
            {{-----------------------------------------------------------------------------------}}

            <div class="lg:col-span-1 col-span-3 px-8">
                @if (Auth::check())
                    <a href="/verification" class="flex flex-row shadow-lg rounded-lg mb-8">
                        <div class="w-1/2 h-24 bg-contain bg-no-repeat bg-center"
                             style="background-image: url({{asset('images/like.png')}});">
                        </div>
                        <div class="font-bold text-xs text-gray-700 text-left my-auto">
                            {!!__('Станьте исполнителем <br> U-ser. И начните  <br> зарабатывать')!!}
                        </div>
                    </a>
                @else
                    <a href="/login" class="flex flex-row shadow-lg rounded-lg mb-8">
                        <div class="w-1/2 h-24 bg-contain bg-no-repeat bg-center"
                             style="background-image: url({{asset('images/like.png')}});">
                        </div>
                        <div class="font-bold text-xs text-gray-700 text-left my-auto">
                            {!!__('Станьте исполнителем <br> U-ser. И начните  <br> зарабатывать')!!}
                        </div>
                    </a>
                @endif

                <div>
                    <div class="max-w-md mx-left">
                        @foreach ($categories as $category)
                            <div x-data={show:false} class="rounded-sm">
                                <div class="my-3 text-blue-500 hover:text-red-500 cursor-pointer"
                                     id="{{ preg_replace('/[ ,]+/', '', $category->name) }}">
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
                        @include('performers.performers_figure')
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
    <div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
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
            $("#stars{{$user->id}}").raty({
                path: 'https://cdn.jsdelivr.net/npm/jquery-raty-js@2.8.0/lib/images',
                readOnly: true,
                score: {{$user->review_rating ?? 0}},
                size: 12
            });
        @endforeach
    </script>
    <script>
        @foreach ($categories as $category)
        $("#{{ preg_replace('/[ ,]+/', '', $category->name) }}").click(function () {
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

