@extends("layouts.app")

@section("content")

    <div class="xl:w-9/12 w-10/12 mx-auto ">
        <div class="grid grid-cols-3 grid-flow-row mt-10">
            {{-- left sidebar start --}}
            <div class="lg:col-span-2 col-span-3">
                <figure class="w-full">
                    <div class="float-right text-gray-500 text-sm">
                        <i class="far fa-eye"> {{ $user->performer_views_count }} {{__('просмотр')}}</i>
                    </div>
                    <div>
                        @if(Cache::has('user-is-online-' . $user->id))
                            <span class="text-green-500">Online</span>
                        @else
                            <span class="text-gray-500"> {{ Carbon\Carbon::parse($user->last_seen)->diffForHumans() }}</span>
                        @endif
                        <h1 class="text-3xl font-bold ">{{$user->name}}</h1>
                    </div>
                    <div class="flex sm:flex-row flex-col w-full mt-6">
                        <div class="sm:w-1/3 pb-10 w-full">
                            <img class="border border-3 border-gray-400 h-44 w-44"
                                 @if ($user->avatar == Null)
                                 src='{{asset("storage/images/default.jpg")}}'
                                 @else
                                 src="{{asset("storage/{$user->avatar}")}}"
                                 @endif alt="avatar">
                        </div>

                        <div class="flex-initial sm:w-2/3 w-full sm:mt-0 mt-6 sm:ml-8 ml-0">
                            <div class="font-medium text-lg">
                                @if($user->phone_verified_at && $user->email_verified_at)
                                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                                    <span>{{__('Документы подтверждены')}}</span>
                                @endif
                            </div>
                            <div class="w-2/3 text-base text-gray-500 lg:ml-0 ml-4">
                                @isset($user->age)
                                    <p class="inline-block mr-2">
                                        {{$user->age}}
                                        @if($user->age>20 && $user->age%10==1) {{__('год')}}
                                        @elseif ($user->age>20 && ($user->age%10==2 || $user->age%10==3 || $user->age%10==1)){{__('года')}}
                                        @else {{__('лет')}}
                                        @endif
                                    </p>
                                @endisset
                                <span class="inline-block">
                                    <p class="inline-block text-m">
                                        @isset($user->location)
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{__('Местоположение')}} {{$user->location}}
                                        @else {{__('город не обозначен')}}
                                        @endisset
                                    </p>
                                </span>
                            </div>
                            <div class="text-gray-500 text-base mt-2">
                                <p class="mt-2">{{__('Создал')}} <a>
                                    <span>
                                        {{count($user->tasks??[])}}
                                    </span> {{__('задание')}}</a></p>
                                @switch($user->reviews()->count())
                                    @case(1)
                                    <span>{{__('Получил')}} {{$user->reviews()->count()}} {{__('Отзыв')}}</span>
                                    @break
                                    @case(1 && 5)
                                    <span>{{__('Получил')}} {{$user->reviews()->count()}} {{__('Отзыва')}}</span>
                                    @break
                                    @default
                                    <span>{{__('Получил')}} {{$user->reviews()->count()}} {{__('Отзывов')}}</span>
                                @endswitch
                            </div>
                            <div>
                                <div class="flex flex-row items-center text-base hidden">
                                    <p class="text-black ">{{__('Отзывы:')}}</p>
                                    <i class="far fa-thumbs-up text-blue-500 ml-1 mb-1"></i>
                                    <span
                                        class="text-gray-800 mr-2 like{{$user->id}}">{{ $user->reviews()->where('good_bad',1)->count()}}</span>
                                    <i class="far fa-thumbs-down mt-0.5 text-blue-500"></i>
                                    <span
                                        class="text-gray-800 dislike{{$user->id}}">{{ $user->reviews()->where('good_bad',0)->count()}}</span>
                                </div>
                                <div class="flex flex-row items-center mt-3" id="str1">
                                    <div class="flex flex-row items-center"><p>{{__('Средняя оценка:')}}</p><span
                                            class="mx-1" id="num"></span></div>
                                    <div class="flex flex-row stars{{$user->id}}">
                                    </div>
                                </div>
                                <div class="mt-3 hidden" id="str2">{{__('Нет оценок')}}</div>
                                <script>
                                    $(document).ready(function () {
                                        var good = $(".like{{$user->id}}").text();
                                        var bad = $(".dislike{{$user->id}}").text();
                                        var allcount = good * 5;
                                        var coundlikes = (good * 1) + (bad * 1);
                                        var overallStars = Math.round(allcount / coundlikes);
                                        console.log(overallStars);
                                        $('#num').text(overallStars);
                                        var star = overallStars.toFixed();
                                        if (!isNaN(star)) {
                                            for (let i = 0; i < star; i++) {
                                                $(".stars{{$user->id}}").append('<i class="fas fa-star text-yellow-500"></i>');
                                            }
                                            for (let u = star; u < 5; u++) {
                                                $(".stars{{$user->id}}").append('<i class="fas fa-star text-gray-500"></i>');
                                            }
                                        } else {
                                            for (let e = 0; e < 5; e++) {
                                                $(".stars{{$user->id}}").append('<i class="fas fa-star text-gray-500"></i>');
                                            }
                                            $('#str1').addClass('hidden');
                                            $('#str2').removeClass('hidden');
                                        }
                                    });
                                </script>
                            </div>
                            <div class="flex mt-6 items-center">
                                @if ($user->is_email_verified && $user->is_phone_number_verified)
                                    <div data-tooltip-target="tooltip-animation_1" class="mx-4 tooltip-1">
                                        <img
                                            src="{{asset('images/verify.png')}}"
                                            alt="" class="w-24">
                                        <div id="tooltip-animation_1" role="tooltip"
                                            class="inline-block sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                            <p class="text-center">
                                                {{__('Номер телефона и Е-mail пользователя подтверждены')}}
                                            </p>
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    </div>
                                @else
                                    <div data-tooltip-target="tooltip-animation_1" class="mx-4 tooltip-1">
                                        <img
                                            src="{{asset('images/verify_gray.png') }}"
                                            alt="" class="w-24">
                                        <div id="tooltip-animation_1" role="tooltip"
                                            class="inline-block sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                            <p class="text-center">
                                                {{__('Номер телефона и Е-mail пользователя неподтверждены')}}
                                            </p>
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    </div>
                                @endif
                                @if($user->role_id == 2)
                                    @foreach($about as $rating)
                                        @if($rating->id == $user->id)
                                            <div data-tooltip-target="tooltip-animation_2" class="mx-4 tooltip-2">
                                                <img src="{{ asset('images/best.png') }}" alt="" class="w-24">
                                                <div id="tooltip-animation_2" role="tooltip"
                                                     class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                                    <p class="text-center">
                                                        {{__('Входит в ТОП-20 исполнителей User.uz')}}
                                                    </p>
                                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                                </div>
                                            </div>
                                        @else
                                            @continue
                                        @endif
                                    @endforeach
                                    <div data-tooltip-target="tooltip-animation_3" class="mx-4">
                                        @if($task_count >= 50)
                                            <img src="{{ asset('images/50.png') }}" alt="" class="w-24">
                                        @else
                                            <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-24">
                                        @endif
                                        <div id="tooltip-animation_3" role="tooltip"
                                             class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                            <p class="text-center">
                                                {{__('Более 50 выполненных заданий')}}
                                            </p>
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    </div>
                                @else
                                    <div data-tooltip-target="tooltip-animation_2" class="mx-4 tooltip-2">
                                        <img src="{{ asset('images/best_gray.png') }}" alt="" class="w-24">
                                        <div id="tooltip-animation_2" role="tooltip"
                                             class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                            <p class="text-center">
                                                {{__('Невходит в ТОП-20 всех исполнителей User.uz')}}
                                            </p>
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    </div>
                                    <div data-tooltip-target="tooltip-animation_3" class="mx-4">
                                        <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-24">
                                        <div id="tooltip-animation_3" role="tooltip"
                                             class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                            <p class="text-center">
                                                {{__('Более 50 выполненных заданий')}}
                                            </p>
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </figure>
                <div class="col-span-2">
                    <h1 class="text-3xl font-semibold text-gray-700">{{__('Обо мне')}}</h1>
                    <p>{{$user->description}}</p>
                </div>
                <div class="mt-8">
                    <div class="grid xl:grid-cols-3 md:grid-cols-2 grid-cols-1 w-full mx-auto">
                        @foreach($portfolios as $portfolio)
                            <a href="{{ route('profile.portfolio', $portfolio->id) }}"
                               class="border my-6 border-gray-400 mr-auto w-56 h-48 mr-6 sm:mb-0 mb-8">
                                <img
                                    src="{{  count(json_decode($portfolio->image)) == 0 ? '': asset('storage/'.json_decode($portfolio->image)[0])  }}"
                                    alt="#" class="w-56 h-48">

                                <div class="h-12 flex relative bottom-12 w-full bg-black opacity-75 hover:opacity-100 items-center">
                                    <p class="w-2/3 text-center text-base text-white">{{$portfolio->comment}}</p>
                                    <div class="w-1/3 flex items-center">
                                        <i class="fas fa-camera float-right text-white text-2xl m-2"></i>
                                        <span class="text-white">{{count(json_decode($portfolio->image)??[])}}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="my-4">
                    @if(!(count($goodReviews) && count($badReviews)))
                        <h1 class="text-xl font-semibold mt-2">{{__('Отзывов пока нет')}}</h1>
                        <p class="mt-2">{{__('Отзывы появятся после того, как вы создадите или выполните задание')}}</p>

                    @else
                        <h1 class="text-xl font-semibold mt-2">{{__('Отзывы')}}</h1>
                        {{-- tabs --}}
                        <div class="tab my-2">
                            <button
                                class="tablinks tablinks border-2 rounded-xl px-2 py-1 mr-4 my-2 border-gray-500  "
                                onclick="openCity(event, 'first')"><i
                                    class="far fa-thumbs-up text-blue-500 mr-1"></i> {{__('Положительные')}}
                            </button>
                            <button
                                class="tablinks tablinks border-2 rounded-xl px-2 py-1 my-2  border-gray-500 text-gray-800 "
                                onclick="openCity(event, 'second')"><i
                                    class="far fa-thumbs-down text-blue-500 mr-2"></i>{{__('Отрицательные')}}
                            </button>
                        </div>
                        {{-- tab contents --}}
                        <div id="first" class="tabcontent">
                            @foreach($goodReviews as $goodReview)
                                @if($goodReview->user && $goodReview->task)
                                    <div class="my-6">
                                        <div class="flex flex-row gap-x-2 my-4">
                                            <img src="{{ asset('storage/'.$goodReview->user->avatar) }}" alt="#"
                                                 class="w-12 h-12 border-2 rounded-lg border-gray-500">
                                            <a href="#"
                                               class="text-blue-500 hover:text-red-500">{{ $goodReview->user->name }}</a>
                                        </div>
                                        <div class="sm:w-3/4 w-full p-3 bg-yellow-50 rounded-xl">
                                            <p>{{__('Задание')}} <a
                                                    href="{{ route('searchTask.task',$goodReview->task_id) }}"
                                                    class="hover:text-red-400 border-b border-gray-300 hover:border-red-400">"{{ $goodReview->task->name }}
                                                    "</a> {{__('выполнено')}}</p>
                                            <p class="border-t-2 border-gray-300 my-3 pt-3">{{ $goodReview->description }}</p>
                                            <p class="text-right">{{ $goodReview->created }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div id="second" class="tabcontent">
                            @foreach($badReviews as $badReview)
                                @if($badReview->user && $badReview->task)
                                    <div class="my-6">
                                        <div class="flex flex-row gap-x-2 my-4">
                                            <img src="{{  asset('storage/'.$badReview->user->avatar) }}" alt="#"
                                                 class="w-12 h-12 border-2 rounded-lg border-gray-500">
                                            <a href="#"
                                               class="text-blue-500 hover:text-red-500">{{ $badReview->user->name }}</a>
                                        </div>
                                        <div class="sm:w-3/4 w-full p-3 bg-yellow-50 rounded-xl">
                                            <p>{{__('Задание')}} <a href="#"
                                                                    class="hover:text-red-400 border-b border-gray-300 hover:border-red-400">"{{ $badReview->task->name }}
                                                    "</a> {{__('выполнено')}}</p>
                                            <p class="border-t-2 border-gray-300 my-3 pt-3">{{ $badReview->description }}</p>
                                            <p class="text-right">{{ $badReview->created }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            {{-- left sidebar start end--}}

            {{-- right sidebar start --}}
            <div class="lg:col-span-1 col-span-2 sm:w-80 w-72 sm:ml-14 ml-0">
                <div class="mt-16 border p-8 rounded-lg border-gray-300">
                    <div>
                        <h1 class="font-medium text-2xl">{{__('Исполнитель')}}</h1>
                        <p class="text-gray-400">{{__('на Universal Services с ')}} {{date('d-m-Y', strtotime($user->created_at))}}</p>
                    </div>
                    <div class="">
                        <div class="flex w-full mt-4">
                            <div class="flex-initial w-1/4">
                                <i class="fas fa-phone-alt text-white text-2xl bg-yellow-500 py-1 px-2 rounded-lg"></i>
                            </div>
                            <div class="flex-initial w-3/4">
                                <h2 class="font-medium text-lg">{{__('Телефон')}}</h2>
                                @if($user->is_phone_number_verified)
                                    <p>{{__('Подтвержден')}}</p>
                                @else
                                    <p>{{__('Не подтвержден')}}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex w-full mt-4">
                            <div class="flex-initial w-1/4">
                                <i class="text-white far fa-envelope text-2xl bg-blue-500 py-1 px-2 rounded-lg"></i>
                            </div>
                            <div class="flex-initial w-3/4">
                                <h2 class="font-medium text-lg">Email</h2>
                                @if($user->is_email_verified)
                                    <p>{{__('Подтвержден')}}</p>
                                @else
                                    <p>{{__('Не подтвержден')}}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <h1 class="text-3xl font-medium">{{__('Виды выполняемых работ')}}</h1>
                    <ul>
                        @foreach(explode(',', $user->category_id) as $user_cat)
                            @foreach(getAllCategories() as $cat)
                                @if($cat->id == $user_cat)
                                    <li class="mt-2 text-gray-500"><a
                                            class="hover:text-red-500 underline underline-offset-4"
                                            href="{{route('categories',$cat->parent_id)}}">{{ $cat->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endforeach
                    </ul>
                </div>
            </div>
            {{-- right sidebar end --}}
        </div>
    </div>
    <style>
        .tabcontent {
            display: none;
        }
    </style>

    @if($user->role_id == 2)
        <script>
            if ($('.tooltip-2').length === 0) {
                $("<div data-tooltip-target='tooltip-animation_2' class='mx-4 tooltip-2' ><img src='{{ asset("images/best_gray.png") }}'alt='' class='w-16'><div id='tooltip-animation_2' role='tooltip' class='inline-block  w-2/12 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700'><p class='text-center'>{{__('Невходит в ТОП-20 всех исполнителей User.uz')}}</p><div class='tooltip-arrow' data-popper-arrow></div> </div></div>").insertAfter($(".tooltip-1"));
            }
        </script>
    @endif
        <script>
            // tabs
        function openCity(evt, cityName) {
            var index, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (index = 0; index < tabcontent.length; index++) {
              tabcontent[index].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (index = 0; index < tablinks.length; index++) {
              tablinks[index].className = tablinks[index].className.replace("bg-yellow-200 text-gray-900", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += "bg-yellow-200 text-gray-900";
          }
        //tabs end
        </script>
@endsection
