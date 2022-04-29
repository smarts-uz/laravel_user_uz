@extends("layouts.app")

@section("content")

    <link rel="stylesheet" href="{{ asset('/css/profile.css') }}">
    <link href="https://releases.transloadit.com/uppy/v2.1.0/uppy.min.css" rel="stylesheet">
    <div class="w-11/12  mx-auto text-base mt-4">
        <div class="grid lg:grid-cols-3 grid-cols-2 lg:w-5/6 w-full mx-auto">
            {{-- user ma'lumotlari --}}
            <div class="col-span-2 w-full mx-auto">
                @include('components.profileFigure')
                {{-- user ma'lumotlari tugashi --}}
                <div class="content mt-20 ">
                    <div class="grid md:grid-cols-10 w-full items-center">
                        <ul class=" md:col-span-9 items-center w-3/4 md:w-full" id="tabs">
                            <li class=" md:mr-5 mr-1 inline-block"><a href="/profile"
                                                                      class=" text-lg font-bold block text-gray-700 border-b-4 border-green-500 pb-3"
                                                                      id="default-tab">{{__('Обо мне')}}</a></li>
                            <li class=" md:mr-5 mr-1 inline-block"><a href="/profile/cash"
                                                                      class=" text-lg text-gray-600">{{__('Счет')}}
                                </a></li>
                            <li class=" md:mr-5 mr-1 inline-block md:hidden block"><a href="/profile/settings"
                                                                                      class="text-lg text-gray-600"
                                                                                      id="settingsText">{{__('Настройки')}}
                                </a></li>

                        </ul>
                        <div class="md:col-span-1 md:block hidden text-gray-600 ml-4" id="settingsIcon"><a
                                href="/profile/settings"><i class="fas fa-cog text-2xl"></i></a></div>

                    </div>
                    <hr>
                    {{-- ABOUT-ME start --}}
                    <div class="about-me block" id="tab-profile">
                        <div class="about-a-bit mt-10">
                            <h4 class="inline font-bold text-lg text-gray-700">{{__('Немного о себе')}}</h4>
                            @if ($user->description == Null)
                                <span class="ml-10">
                                        <i class="fas fa-pencil-alt inline text-gray-700"></i>
                                        <p class="inline text-gray-500 cursor-pointer hover:text-red-500 border-b-2 hover:border-b-2 hover:border-red-500"
                                           id="padd">{{__('Добавить')}}</p>
                                    </span>
                                <p class="text-red-400 desc mt-4">
                                    {{__('Заказчики ничего о вас не знают. Добавьте информацию о вашем опыте.')}}</p>
                            @else
                                <span class="ml-10">
                                        <i class="fas fa-pencil-alt inline text-gray-700"></i>
                                        <p class="inline text-gray-500 cursor-pointer"
                                           id="padd">{{__('Редактировать')}}</p>
                                    </span>
                                <p class="mt-3 w-4/5 desc">{{$user->description}}</p>
                            @endif
                            <form action="{{route('profile.EditDescription')}}" method="POST" class="formdesc hidden">
                                @csrf
                                <textarea name="description" name="description"
                                          class="w-full h-32 border border-gray-400 focus:outline-none focus:border-yellow-500 py-2 px-4 mt-3"
                                          @if (!$user->description) placeholder="{{__('Введите описание')}}"@endif
                                    >@if ($user->description){{$user->description}}@endif</textarea><br>
                                <input type="submit"
                                       class="bg-green-500 cursor-pointer hover:bg-green-600 text-white py-2 px-6 rounded cursor-"
                                       id="s1" value="{{__('Сохранить')}}">
                                <a id="s2"
                                   class="border-dotted border-b-2 mx-4 pb-1 text-gray-500 hover:text-red-500 hover:border-red-500"
                                   href="">{{__('Отмена')}}
                                </a>
                            </form>
                        </div>
                        <h4 class="font-bold mt-5 text-gray-700">{{__('Примеры работ')}}</h4>
                        <p class="mt-2">{{__('Если у вас есть примеры выполненной вами работы, обязательно прикрепите их, это покажет вас в лучшем свете в глазах автора задания.')}} {{__('А также вы будете вызывать больше доверия как исполнитель.')}}</p>

                        <form action="{{route('youtube_link')}}" method="POST">
                            @csrf
                            <div class="border-dashed border-2 border-gray-500 rounded-xl mt-6 p-4">
                                <h1 class="text-xl font-bold">{{__('Добавьте видеоролик о себе')}}</h1>
                                <p class="text-lg mt-2">{{__('Профили с видео получают больше внимания и вызывают доверие заказчиков.')}}</p>
                                <div class="flex sm:flex-row flex-col my-3">
                                    <input name="youtube_link" type="text" id="youtube_link"
                                           class="border border-gray-400 hover:border-yellow-500 focus:outline-none rounded-lg sm:w-2/3 w-full p-2"
                                           placeholder="{{__('Ссылка на ролик с YouTube')}}" required>
                                    <button
                                            class="sm:w-1/3 w-2/3 sm:mx-3 mx-0 rounded-lg bg-green-500 hover:bg-green-600 text-white py-2 px-4 cursor-pointer sm:mt-0 mt-3">{{('Добавить')}}</button>
                                </div>
                            </div>
                        </form>
                        @if($user->youtube_link != null)
                            <iframe width="800" height="500" id="iframe" src="{{$user->youtube_link}}" frameborder="0"></iframe>
                        @endif
                        <div class="example-of-works w-full my-10">
                            <a href="/profile/create">
                                <button class="bg-green-500 px-8 py-3 rounded-md text-white text-2xl">
                                    <i class="fas fa-camera"></i>
                                    <span>{{__('Создать фотоальбом')}}</span>
                                </button>
                            </a>
                        </div>
                        <div class="grid xl:grid-cols-3 md:grid-cols-2 grid-cols-1 w-full mx-auto">
                            @foreach($portfolios as $portfolio)
                                <a href="{{ route('profile.portfolio', $portfolio->id) }}"
                                   class="border my-6 border-gray-400 mr-auto w-56 h-48 mr-6 sm:mb-0 mb-8">
                                    <img
                                        src="{{  count(json_decode($portfolio->image)) == 0 ? '': asset('storage/'.json_decode($portfolio->image)[0])  }}"
                                        alt="#" class="w-56 h-48">
                                    <div
                                        class="h-12 flex relative bottom-12 w-full bg-black opacity-75 hover:opacity-100 items-center">
                                        <p class="w-2/3 text-center text-base text-white">{{$portfolio->comment}}</p>
                                        <div class="w-1/3 flex items-center">
                                            <i class="fas fa-camera float-right text-white text-2xl m-2"></i>
                                            <span
                                                class="text-white">{{count(json_decode($portfolio->image)??[])}}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-8">
                        <p class="text-2xl font-semibold">
                            {{__('Виды выполняемых работ')}}
                        </p>
                        <div class="my-4">
                            <ul class="pl-10 leading-7">
                                @foreach(explode(',', $user->category_id) as $user_cat)
                                    @foreach($categories as $cat)
                                        @if($cat->id == $user_cat)
                                            <li>
                                                <a href="/categories/{{$cat->parent_id}}" class="underline">
                                                    {{ $cat->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                        <div class="my-4">
                            @if(!(count($goodReviews) || count($badReviews)))
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
                                                    <a href="{{ route('performers.performer',$goodReview->reviewer_id) }}"
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
                                                    <a href="{{ route('performers.performer',$badReview->reviewer_id ) }}"
                                                       class="text-blue-500 hover:text-red-500">{{ $badReview->user->name }}</a>
                                                </div>
                                                <div class="sm:w-3/4 w-full p-3 bg-yellow-50 rounded-xl">
                                                    <p>{{__('Задание')}} <a href="{{ route('searchTask.task',$badReview->task_id) }}"
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
                    {{-- about-me end --}}
                </div>
            </div>

            {{-- right-side-bar --}}
            @include('auth.profile-side-info')
            {{-- right-side-bar --}}
        </div>
    </div>
    <style>
        .tabcontent {
            display: none;
        }
    </style>
    <script>
    </script>
    <script src="{{ asset('js/profile/profile.js') }}">
    </script>
    @if($user->role_id == 2)
        <script>
            if ($('.tooltip-2').length === 0) {
                $("<div data-tooltip-target='tooltip-animation_2' class='mx-4 tooltip-2' ><img src='{{ asset("images/best_gray.png") }}'alt='' class='w-24'><div id='tooltip-animation_2' role='tooltip' class='inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700'><p class='text-center'>{{__('Невходит в ТОП-20 всех исполнителей User.uz')}}</p><div class='tooltip-arrow' data-popper-arrow></div> </div></div>").insertAfter($(".tooltip-1"));
            }
        </script>
    @endif
    @include('sweetalert::alert')
@endsection
