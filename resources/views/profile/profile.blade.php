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
                        <ul class=" md:col-span-8 items-center w-3/4 md:w-full" id="tabs">
                            <li class=" md:mr-5 mr-1 inline-block">
                                <a href="/profile" class=" text-lg font-bold block text-gray-700 border-b-4 border-green-500 pb-3" id="default-tab">{{__('Обо мне')}}</a>
                            </li>
                            <li class=" md:mr-5 mr-1 inline-block">
                                <a href="/profile/cash" class="text-lg text-gray-600">{{__('Счет')}}</a>
                            </li>
                            <li class=" md:mr-5 mr-1 inline-block md:hidden block">
                                <a href="/profile/settings" class="text-lg text-gray-600" id="settingsText">{{__('Настройки')}}</a>
                            </li>
                        </ul>
                        <div class="md:col-span-2 md:block hidden text-gray-600 ml-6" id="settingsIcon">
                            <a href="/profile/settings" class="flex items-center mb-3">
                                <i class="fas fa-cog text-2xl"></i>
                                <span class="text-lg text-gray-600 ml-2">{{__('Настройки')}}</span>
                            </a>
                        </div>
                    </div>
                    <hr>
                    {{-- ABOUT-ME start --}}
                    <div class="about-me block" id="tab-profile">
                        <div class="about-a-bit mt-10">
                            <h4 class="inline font-bold text-lg text-gray-700">{{__('Немного о себе')}}</h4>
                            @if ($user->description === null)
                                <span class="ml-10">
                                     <i class="fas fa-pencil-alt inline text-gray-700"></i>
                                     <p class="inline text-gray-500 cursor-pointer hover:text-red-500 border-b-2 hover:border-b-2 hover:border-red-500"
                                           id="padd">{{__('Добавить')}}
                                     </p>
                                </span>
                                <p class="text-red-400 desc mt-4">{{__('Заказчики ничего о вас не знают. Добавьте информацию о вашем опыте.')}}</p>
                            @else
                                <span class="ml-10">
                                     <i class="fas fa-pencil-alt inline text-gray-700"></i>
                                     <p class="inline text-gray-500 cursor-pointer"
                                           id="padd">{{__('Редактировать')}}
                                     </p>
                                </span>
                                <p class="mt-3 w-4/5 desc">{{$user->description}}</p>
                            @endif
                            <form action="{{route('profile.EditDescription')}}" method="POST" class="formdesc hidden">
                                @csrf
                                <textarea name="description" class="w-full h-32 border border-gray-400 focus:outline-none focus:border-yellow-500 py-2 px-4 mt-3"
                                    @if (!$user->description) placeholder="{{__('Введите описание')}}" @endif>
                                    @if ($user->description) {{$user->description}}@endif</textarea><br>
                                <input type="submit" class="bg-green-500 cursor-pointer hover:bg-green-600 text-white py-2 px-6 rounded cursor-"
                                       id="s1" value="{{__('Сохранить')}}">
                                <a id="s2" href="" class="border-dotted border-b-2 mx-4 pb-1 text-gray-500 hover:text-red-500 hover:border-red-500">
                                    {{__('Отмена')}}
                                </a>
                            </form>
                        </div>
                        <h4 class="font-bold mt-5 text-gray-700">{{__('Примеры работ')}}</h4>
                        <p class="mt-2">{{__('Если у вас есть примеры выполненной вами работы, обязательно прикрепите их, это покажет вас в лучшем свете в глазах автора задания.')}} {{__('А также вы будете вызывать больше доверия как исполнитель.')}}</p>

                        <form action="{{route('youtube_link')}}" method="POST" id="youtube_form">
                            @csrf
                            <div class="border-dashed border-2 border-gray-500 rounded-xl mt-6 p-4">
                                <h1 class="text-xl font-bold">{{__('Добавьте видеоролик о себе')}}</h1>
                                <p class="text-lg mt-2">{{__('Профили с видео получают больше внимания и вызывают доверие заказчиков.')}}</p>
                                <div class="grid grid-cols-3 my-3">
                                    <div class="sm:col-span-2 col-span-3 flex flex-col">
                                        <input name="link" type="text" id="youtube_link"
                                               class="border border-gray-400 hover:border-yellow-500 focus:outline-none rounded-lg p-2"
                                               placeholder="{{__('Ссылка на ролик с YouTube')}}">
                                        @if(session()->has('message'))
                                            <div class="text-red-500">
                                                {{ session()->get('message') }}
                                            </div>
                                        @endif
                                    </div>
                                    <button class="sm:col-span-1 col-span-3 h-10 sm:mx-3 mx-0 rounded-lg bg-green-500 hover:bg-green-600 text-white py-2 px-4 cursor-pointer sm:mt-0 mt-3">{{__('Добавить')}}</button>
                                </div>
                            </div>
                        </form>

                        @if($user->youtube_link)
                            <iframe class="my-4 sm:w-full w-5/6" width="644" height="362" id="iframe" src="{{$user->youtube_link}}" frameborder="0"></iframe>
                                <a href="{{route('youtube_link_delete')}}" class="float-right text-gray-500 hover:text-red-500 mb-3 border-b-2 border-dotted hover:border-red-500 border-gray-500">{{__('Удалить')}}</a>
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
                                    <img src="{{ count(json_decode($portfolio->image)) == 0 ? '': asset('/storage/portfolio/' . json_decode($portfolio->image)[0]) }}"
                                        alt="#" class="w-56 h-48">
                                    <div class="h-12 flex relative bottom-12 w-full bg-black opacity-75 hover:opacity-100 items-center">
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
                            <ul class="leading-7">
                                @foreach($user_category as $per_cat)
                                    <div class="my-4">
                                        @foreach($per_cat['parent'] as $per_c)
                                            <div class="flex flex-row gap-x-4">
                                                <img src="{{asset('storage/'.$per_c->ico) }}" alt="" class="h-10 w-10">
                                                <p class="font-semibold text-xl">{{$per_c->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale')}}</p>
                                            </div>
                                        @endforeach

                                        @foreach($per_cat['category'] as $per_c)
                                            @php
                                                $task_count = $per_c->category->tasks()->where('performer_id',$user->id)->where('status',App\Models\Task::STATUS_COMPLETE)->count()
                                            @endphp
                                            <div class="flex justify-between sm:w-9/12 w-full pl-16 my-2">
                                                <span class="text-sm">{{$per_c->category->getTranslatedAttribute('name')}}</span>
                                                <div class="border-b border-dashed border-gray-500"></div>
                                                @if($task_count>0)
                                                    <span class="text-sm">
                                                        {{$task_count}}
                                                        @switch(true)
                                                            @case ($task_count === 1)
                                                                {{__('задание ')}}
                                                                @break
                                                            @case($task_count === 2 ||  $task_count === 3 ||  $task_count === 4)
                                                                {{__('задания')}}
                                                                @break
                                                            @case ($task_count === 5 || $task_count === 6)
                                                                {{__('задач')}}
                                                                @break
                                                            @default
                                                                {{__('заданий')}}
                                                        @endswitch
                                                    </span>
                                                @else
                                                    <span class="text-sm">{{__('нет заданий')}}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </ul>
                        </div>
                        <div class="my-4">
                            @if(!(count($goodReviews) || count($badReviews)))
                                <h1 class="text-xl font-semibold mt-2">{{__('Отзывов пока нет')}}</h1>
                                <p class="mt-2">{{__('Отзывы появятся после того, как вы создадите или выполните задание')}}</p>
                            @else
                                <h1 class="text-xl font-semibold mt-2">{{__('Отзывы')}}</h1>
                                @include('performers.reviews')
                            @endif

                        </div>
                    </div>
                    {{-- about-me end --}}
                </div>
            </div>
            <style>
                #youtube_link-error{
                    color:red;
                }
            </style>
            {{-- right-side-bar --}}
            <x-profile-info></x-profile-info>
            {{-- right-side-bar --}}
        </div>
    </div>
    <script src="{{ asset('js/profile/profile.js') }}"></script>
    <script>
        $(document).ready(function($) {
            $("#youtube_form").validate({
                rules: {
                    link: {
                        required: true,
                        url: true
                    },
                },
                messages: {
                    link: {
                        @if(session('lang')==='ru')
                        required: 'Пожалуйста, введите вашу ссылку!',
                        url: 'Пожалуйста, введите корректный адрес.'
                        @else
                        required: 'Iltimos, havolangizni kiriting!',
                        url: 'Yaroqli URL manzilini kiriting.'
                        @endif
                    },
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        });
    </script>
    @include('sweetalert::alert')
@endsection
