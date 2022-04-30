@extends("layouts.app")

@section("content")
    <script type="text/javascript" src="http://connect.mail.ru/js/loader.js">
    </script>
    <div class="hidden" id="map_route">{{ route('task.map', $task->id) }}</div>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=f4b34baa-cbd1-432b-865b-9562afa3fcdb"
            type="text/javascript"></script>
    <script src="{{ asset('js/detailed-task-map.js') }}" type="text/javascript"></script>


    <div class="xl:flex container w-11/12 mx-auto">
        <div class="md:flex mx-auto xl:w-9/12 w-full">
            {{-- left sidebar start --}}
            <div class="w-full float-left mt-8">
                <h1 class="text-3xl font-bold mb-2">{{$task->name}}</h1>
                <div class="md:flex flex-row">
                        <span class="text-black rounded-lg bg-yellow-400 p-2">
                            @if ( __('до') == 'gacha' )
                                {{ number_format($task->budget) }} {{__('сум')}}{{__('до')}}
                            @else
                                {{__('до')}} {{ number_format($task->budget) }} {{__('сум')}}
                            @endif
                        </span>
                    @auth()
                        @if($task->user_id == auth()->user()->id && !$task->responses_count)
                            <a href="{{ route('searchTask.changetask', $task->id) }}"
                               class="py-2 px-2 text-gray-500 hover:text-red-500">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                        @endif
                    @endauth
                    @if ($task->email_confirm == 1)
                        <h1 class="my-2 text-green-400">{{__('Сделка без риска')}}</h1>
                        <i class="far fa-credit-card text-green-400 mx-3 my-1 text-2xl"></i>
                    @endif
                </div>
                <div class="md:flex flex-row float-left">
                    @if ($task->show_only_to_performers == 1)
                        <p class="mt-4 text-gray-400 font-normal">{{__('Заказчик отдает предпочтение застрахованным исполнителям')}}</p>
                    @endif
                </div>
                <div class="md:flex flex-row text-gray-400 mt-4 text-base">
                    @if ($task->status == 3)
                        <p class="text-amber-500 font-normal md:border-r-2 border-gray-400 pr-2">{{__('В исполнении')}}</p>
                    @elseif($task->status < 3)
                        <p class="text-green-400 font-normal md:border-r-2 border-gray-400 pr-2">{{__('Открыто')}}</p>
                    @else
                        <p class="text-red-400 font-normal md:border-r-2 border-gray-400 pr-2">{{__('Закрыто')}}</p>
                    @endif
                    <p class="font-normal md:border-r-2 border-gray-400 md:px-2 px-0">{{$task->views }}  {{__('просмотров')}}</p>
                    <p class="mr-3 md:pl-2 pr-3 md:border-r-2 border-gray-400">{{$task->created_at}}</p>
                    <p class="pr-3 ">{{ $task->category->getTranslatedAttribute('name') }}</p>
                    @if($task->user_id == auth()->id() && !count($responses) && $task->status == 1 )
                        <form action="{{route("searchTask.delete_task", $task->id)}}" method="post">
                            @csrf
                            @method('delete')
                            <button type="submit"
                                    class="mr-3 border-l-2  pl-2 pl-3 border-gray-400 text-red-500">
                                {{__('Отменить')}}
                            </button>
                        </form>
                    @endif
                </div>

                <div
                    class="mt-12 border-2 py-2 lg:w-[600px]  w-[400px] rounded-lg border-orange-100 shadow-2xl">
                    <div id="map" class="h-64 mb-4 -mt-2 {{ count($addresses)?'':'hidden' }}  "></div>
                    <div class="ml-4 md:ml-12 flex flex-row my-4">
                        <h1 class="font-bold h-auto w-48">{{__('Место')}}</h1>
                        @if(count($addresses))
                            <p class=" h-auto w-96">
                                @foreach($addresses as $address)
                                    {{$address->location}}
                                    <br>
                                @endforeach
                            </p>
                        @else
                            {{__('Виртуальное задание')}}
                        @endif
                    </div>
                    <div class="ml-4 md:ml-12 flex flex-row mt-8">
                        @if($task->date_type == 1)
                            <h1 class="font-bold h-auto w-48">{{__('Начать работу')}}</h1>
                            {{ $task->start_date     }}
                        @elseif($task->date_type == 2)
                            <h1 class="font-bold h-auto w-48">{{__('Закончить работу')}}</h1>
                            {{ $task->end_date     }}
                        @else
                            <h1 class="font-bold h-auto w-48">{{__('Указать период')}}</h1>
                            <p class=" h-auto w-96">{{ $task->start_date     }} - {{ $task->end_date     }}  </p>

                        @endif
                    </div>
                    <div class="ml-4 md:ml-12 flex flex-row mt-8">
                        <h1 class="font-bold h-auto w-48">{{__('Бюджет')}}</h1>
                        <p class=" h-auto w-96">
                            @if ( __('до') == 'gacha' )
                                {{ number_format($task->budget) }} {{__('сум')}}{{__('до')}}
                            @else
                                {{__('до')}} {{ number_format($task->budget) }} {{__('сум')}}
                            @endif
                        </p>
                    </div>
                    @isset($value)
                        @foreach($task->custom_field_values as $value)
                            <div class="ml-4 md:ml-12 flex flex-row mt-8">

                                <h1 class="font-bold h-auto w-48">{{ $value->custom_field->getTranslatedAttribute('title') }}</h1>
                                <p class=" h-auto w-96">
                                    @foreach(json_decode($value->value, true) as $value_obj)
                                        @if ($loop->last)
                                            {{$value_obj}}
                                        @else
                                            {{$value_obj}},
                                        @endif
                                    @endforeach
                                </p>
                            </div>
                        @endforeach
                    @endisset


                    <div class="ml-4 md:ml-12 flex flex-row mt-8">
                        <h1 class="font-bold h-auto w-48">{{__('Оплата задания')}}</h1>
                        <div class=" h-auto w-96">
                            <p class="text-blue-400">
                                @if($task->oplata == 1)
                                    {{__(' Оплата наличными')}}
                                @else
                                    {{__('Оплата через карту')}}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="ml-4 md:ml-12 flex flex-row mt-8">
                        <h1 class="font-bold h-auto w-48">{{__('Нужно')}}</h1>
                        <p class=" h-auto w-96">{{$task->description}}</p>
                    </div>

                    <div class="ml-4 md:ml-12 flex flex-wrap mt-8">
                        <h1 class="font-bold h-auto w-48">{{__('Рисунок')}}</h1>
                        @foreach(json_decode($task->photos)??[] as $key => $image)
                            {{--@if ($loop->first)--}}

                            @if($loop->first)
                                <div class="relative boxItem">
                                    <a class="boxItem relative" href="{{ asset('storage/'.$image) }}"
                                       data-fancybox="img1"
                                       data-caption="<span>{{  $task->created_at}}</span>">
                                        <div class="mediateka_photo_content">
                                            <img src="{{ asset('storage/'.$image) }}" alt="">
                                        </div>
                                    </a>
                                </div>
                            @endif
                            {{--@endif--}}
                        @endforeach
                    </div>
                    @if($task->docs == 1)
                        <div class="ml-4 md:ml-12 flex flex-row mt-8">
                            <h1 class="font-bold h-auto w-48">{{__('Предоставил(а) документы')}}</h1>
                        </div>
                    @else
                        <div class="ml-4 md:ml-12 flex flex-row mt-8">
                            <h1 class="font-bold h-auto w-48">{{__('Не предоставил(а) документы')}}</h1>
                        </div>
                    @endif

                    @foreach($task->custom_field_values as $value)
                        @if($value->value &&  $value->custom_field)
                            <div class="ml-4 md:ml-12 flex flex-row mt-8">
                                <h1 class="font-bold text-gray-600 h-auto w-48">{{ $value->custom_field->title }}</h1>
                                <div class=" h-auto w-96">
                                    <p class="text-gray-500">
                                        <b class="ml-4">{{ $value->custom_field->label  }}:</b>

                                        {{ json_decode($value->value)[0]  }}
                                    </p>
                                </div>
                            </div>
                    @endif
                @endforeach

                <!--  ------------------------ showModal Откликнуться на это задание  ------------------------  -->

                    <div>
                        <div class="w-full flex flex-col sm:flex-row sm:p-6 p-2">
                            <!-- This is an example component -->
                            <div class="w-full text-center">
                                @auth
                                    @if(getAuthUserBalance() >= 4000 || $task->responses_count< setting('site.free_responses'))
                                        @if($task->user_id != auth()->id() && $task->status < 3 && !$auth_response)
                                            <button
                                                class="sm:w-4/5 w-full font-sans text-lg pay font-semibold bg-green-500 text-white hover:bg-green-600 px-8 pt-1 pb-2 mt-6 rounded-lg transition-all duration-300"
                                                id="btn1"
                                                type="button"
                                                data-modal-toggle="authentication-modal">
                                                {{__('Откликнуться за 4000 UZS')}}<br>
                                                <span class="text-sm">
                                                    {{__('и отправить контакты заказчику')}}<br>
                                                </span>
                                            </button>
                                            <button
                                                class="sm:w-4/5 w-full font-sans text-lg font-semibold bg-yellow-500 text-white hover:bg-yellow-600 px-8 pt-1 pb-2 mt-6 rounded-lg transition-all duration-300"
                                                id="btn2"
                                                type="button"
                                                data-modal-toggle="authentication-modal">
                                                {{__('Откликнуться на задание бесплатно')}}<br>
                                                <span class="text-sm">
                                                    {{__('отклик - 0 UZS, контакт с заказчиком - 5000 UZS')}}
                                                </span>
                                            </button>
                                        @endif
                                    @elseif(getAuthUserBalance() < 4000 || $response_count_user >= setting('site.free_responses'))
                                        @if($task->user_id != auth()->id() && $task->status < 3 && !$auth_response)
                                            <a class="open-modal"
                                               data-modal="#modal1">
                                                <button
                                                    class='w-1/2 font-sans text-lg font-semibold bg-green-500 text-white hover:bg-green-500 px-8 pt-2 pb-3 mt-6 rounded-lg transition-all duration-300 m-2'>
                                                    {{__('Откликнуться за 4000 UZS')}}
                                                </button>
                                            </a>
                                            <a class="open-modal"
                                               data-modal="#modal1">
                                                <button
                                                    class='font-sans text-lg font-semibold bg-yellow-500 text-white hover:bg-orange-500 px-8 pt-2 pb-3 mt-6 rounded-lg transition-all duration-300 m-2'>
                                                    {{__('Откликнуться на задание бесплатно')}}
                                                </button>
                                            </a>
                                            <div class='modal' id='modal1'>
                                                <div class='content'>
                                                    <img class="w-64 h-64"
                                                         src="{{asset('images/cash_icon.png')}}"
                                                         alt="">
                                                    <h1 class="title">{{__('Пополните баланс')}}</h1>
                                                    <p>
                                                        {{__('Для отклика на вашем балансе должно быть 4000 UZS. Если заказчик захочет с вами связаться, мы автоматически спишем стоимость контакта с вашего счёта.')}}
                                                    </p>
                                                    <a class='btn'
                                                       href="/profile/cash">{{__('Пополнить')}}</a>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <a href="/login">
                                        <button
                                            class="sm:w-4/5 w-full mx-auto font-sans mt-8 text-lg  font-semibold bg-yellow-500 text-white hover:bg-orange-500 px-10 py-4 rounded-lg">
                                            {{__('Откликнуться на это задание')}}
                                        </button>
                                    </a>
                                @endauth
                                @auth
                                    @if ($task->performer_id == auth()->user()->id || $task->user_id == auth()->user()->id)
                                        <button id="sendbutton"
                                                class="font-sans w-full text-lg font-semibold bg-green-500 hidden text-white hover:bg-green-400 px-12 ml-6 pt-2 pb-3 rounded-lg transition-all duration-300 m-2"
                                                type="button">
                                            {{__('Оставить отзыв')}}
                                        </button>

                                        @if($task->status == 3 && $task->user_id == auth()->user()->id)

                                            {{--                                                            <form action="{{ route('task.completed', $task->id) }}" method="post">--}}
                                            @csrf
                                            @if(!$review)
                                                <button
                                                    id="modal-open-id5"
                                                    class="not_done  sm:w-2/5 w-9/12 text-lg font-semibold bg-green-500 text-white hover:bg-green-400 px-5 ml-6 pt-2 pb-3 rounded-lg transition-all duration-300 m-2"
                                                    type="submit">
                                                    {{__('Задание выполнено')}}
                                                </button>
                                                <button
                                                    class="not_done  sm:w-2/5 w-9/12 text-lg font-semibold bg-red-500 text-white hover:bg-red-400 px-5 ml-6 pt-2 pb-3 rounded-lg transition-all duration-300 m-2"
                                                    type="button">
                                                    {{__('Задание не выполнено')}}
                                                </button>
                                            @endif
                                        @endif
                                    @endif
                                @endauth

                            </div>
                        </div>
                    </div>
                </div>
                @if($task->user_id == auth()->id())
                @else
                    <div
                        class="mt-12 border-2 p-6 lg:w-[600px]  w-[400px] rounded-lg border-orange-100 shadow-lg">
                        <h1 class="text-3xl font-semibold py-3">{{__('Хотите найти надежного помощника?')}}</h1>
                        <p class="mb-10">{{__('Universal Services помогает быстро решать любые бытовые и бизнес-задачи.')}}</p>
                        <a href="/categories/1">
                            <button
                                class="font-sans text-lg font-semibold bg-yellow-500 text-white hover:bg-orange-500 px-8 pt-2 pb-3 rounded">
                                {{__('Создайте свое задание')}}
                            </button>
                        </a>
                    </div>
                @endif

                @if($auth_response)
                    <div class="mt-3">
                        <h1 class="text-3xl font-semibold text-black">{{__('Ваш отклик')}}</h1>
                        <div class="my-3 flex flex-row">
                            <div class="">
                                <img class="w-24 h-24 rounded-lg border-2"
                                     src='{{ auth()->user()->avatar? asset('storage/'.auth()->user()->avatar) : asset('images/avatar-avtor-image.png') }}'
                                     alt="avatar">
                            </div>
                            <div class="sm:ml-4 ml-0 flex flex-col sm:my-0 my-3">
                                @if (Auth::check() && Auth::user()->id == auth()->user()->id)
                                    <a href="/profile"
                                        class="text-2xl text-blue-500 hover:text-red-500">
                                        {{ auth()->user()->name }}
                                    </a>
                                @else
                                    <a href="{{ route('performers.performer', auth()->user()->id) }}"
                                        class="text-blue-400 text-xl font-semibold hover:text-blue-500">
                                        {{ auth()->user()->name }}
                                    </a>
                                @endif
                                <input type="text" name="performer_id" class="hidden"
                                       value="">
                                    <div class="text-gray-700 sm:mt-4 mt-2">
                                        <i class="fas fa-star text-yellow-500 mr-1"></i>{{ auth()->user()->reviews()->count()? auth()->user()->goodReviews()->count()/auth()->user()->reviews()->count():0 }}
                                        по {{ auth()->user()->reviews()->count() }} отзывам
                                    </div>
                            </div>
                            <div class="flex flex-row items-start">
                                <div data-tooltip-target="tooltip-animation_1" class="mx-1 tooltip-1">
                                    <img
                                        src="{{ auth()->user()->is_email_verified && auth()->user()->is_phone_number_verified? asset('images/verify.png') : asset('images/verify_gray.png')  }}"
                                        alt="" class="w-10">
                                    <div id="tooltip-animation_1" role="tooltip"
                                         class="inline-block sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                        @if (auth()->user()->is_email_verified && auth()->user()->is_phone_number_verified)
                                            <p class="text-center">
                                                {{__('Номер телефона и Е-mail пользователя подтверждены')}}
                                            </p>
                                        @else
                                            <p class="text-center">
                                                {{__('Номер телефона и Е-mail пользователя неподтверждены')}}
                                            </p>
                                        @endif
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </div>
                                @if(auth()->user()->role_id == 2)
                                    @foreach($about as $rating)
                                        @if($rating->id == $auth_response->performer_id)
                                            <div data-tooltip-target="tooltip-animation_2" class="mx-1 tooltip-2">
                                                <img src="{{ asset('images/best.png') }}" alt="" class="w-10">
                                                <div id="tooltip-animation_2" role="tooltip"
                                                    class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                                    <p class="text-center">
                                                        {{__('Входит в ТОП-20 исполнителей User.uz')}}
                                                    </p>
                                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    <div data-tooltip-target="tooltip-animation_3" class="mx-1">
                                        @if(auth()->user()->tasks()->count() >= 50)
                                            <img src="{{ asset('images/50.png') }}" alt="" class="w-10 mt-1">
                                        @else
                                            <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-10 mt-1">
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
                                    <div data-tooltip-target="tooltip-animation_2" class="mx-1 tooltip-2">
                                        <img src="{{ asset('images/best_gray.png') }}" alt="" class="w-10">
                                        <div id="tooltip-animation_2" role="tooltip"
                                            class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                            <p class="text-center">
                                                {{__('Невходит в ТОП-20 всех исполнителей User.uz')}}
                                            </p>
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    </div>
                                    <div data-tooltip-target="tooltip-animation_3" class="mx-1">
                                        <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-10 mt-1">
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
                        <div class="mb-6">
                            <div class="bg-gray-100 rounded-[10px] p-4">
                                <div class="ml-0">
                                    <div
                                        class="text-[17px] text-gray-500 font-semibold">{{__('Стоимость')}} {{ number_format($auth_response->price) }}
                                        UZS
                                    </div>

                                    <div
                                        class="text-[17px] text-gray-500 my-5">{{ $auth_response->description }}
                                    </div>

                                    <div
                                        class="text-[17px] text-gray-500 font-semibold my-4">{{__('Телефон исполнителя:')}}
                                        +998 {{ auth()->user()->phone_number }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @auth()
                    @if ($task->user_id == auth()->user()->id)
                        <div class="text-4xl font-semibold my-6">
                            @if ($task->response_count <= 4)
                                @if ($task->responses_count == 1)
                                    {{__('У задания')}} {{$task->responses_count}} {{__('отклик')}}
                                @else
                                    {{__('У задания')}} {{$task->responses_count}} {{__('откликa')}}
                                @endif
                            @else
                                {{__('У задания')}} {{$task->responses_count}} {{__('откликов')}}
                            @endif
                        </div>

                        @if($selected && $selected->performer)
                            <h1 class="font-semibold text-2xl">{{__('Выбранный исполнитель')}} </h1>
                            <div class="my-6 flex flex-row">
                                <div class="">
                                    <img class="w-24 h-24 rounded-lg border-2"
                                         @if ($selected->performer->avatar == Null)
                                         src='{{asset("storage/images/default.jpg")}}'
                                         @else
                                         src="{{asset("storage/{$selected->performer->avatar}")}}"
                                         @endif alt="avatar">
                                </div>
                                <div class="sm:ml-4 ml-0 flex flex-col sm:my-0 my-3">
                                    @if (Auth::check() && Auth::user()->id == $selected->performer->id)
                                        <a href="/profile"
                                        class="text-2xl text-blue-500 hover:text-red-500">
                                        {{ $selected->performer->name }}
                                        </a>
                                    @else
                                        <a href="/performers/{{$selected->performer->id}}"
                                            class="text-blue-400 text-xl font-semibold hover:text-blue-500">
                                            {{ $selected->performer->name }}
                                        </a>
                                    @endif
                                    <input type="text" name="performer_id" class="hidden"
                                           value="{{ $selected->performer_id }}">
                                    <div class="text-gray-700 sm:mt-4 mt-2">
                                        <i class="fas fa-star text-yellow-500 mr-1"></i>{{ $selected->performer->reviews()->count()? $selected->performer->goodReviews()->count()/$selected->performer->reviews()->count():0 }}
                                        по {{ $selected->performer->reviews()->count() }} отзывам
                                    </div>
                                </div>
                                <div class="flex flex-row items-start">
                                    <div data-tooltip-target="tooltip-animation_1" class="mx-1 tooltip-1">
                                        <img
                                            src="{{ $selected->performer->is_email_verified && $selected->performer->is_phone_number_verified? asset('images/verify.png') : asset('images/verify_gray.png')  }}"
                                            alt="" class="w-10">
                                        <div id="tooltip-animation_1" role="tooltip"
                                             class="inline-block sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                            @if ($selected->performer->is_email_verified && $selected->performer->is_phone_number_verified)
                                                <p class="text-center">
                                                    {{__('Номер телефона и Е-mail пользователя подтверждены')}}
                                                </p>
                                            @else
                                                <p class="text-center">
                                                    {{__('Номер телефона и Е-mail пользователя неподтверждены')}}
                                                </p>
                                            @endif
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    </div>
                                    @if($selected->performer->role_id == 2)
                                        @foreach($about as $rating)
                                            @if($rating->id == $selected->performer_id)
                                                <div data-tooltip-target="tooltip-animation_2" class="mx-1 tooltip-2">
                                                    <img src="{{ asset('images/best.png') }}" alt="" class="w-10">
                                                    <div id="tooltip-animation_2" role="tooltip"
                                                        class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                                        <p class="text-center">
                                                            {{__('Входит в ТОП-20 исполнителей User.uz')}}
                                                        </p>
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                        <div data-tooltip-target="tooltip-animation_3" class="mx-1">
                                            @if($selected->performer->tasks()->count() >= 50)
                                                <img src="{{ asset('images/50.png') }}" alt="" class="w-10 mt-1">
                                            @else
                                                <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-10 mt-1">
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
                                        <div data-tooltip-target="tooltip-animation_2" class="mx-1 tooltip-2">
                                            <img src="{{ asset('images/best_gray.png') }}" alt="" class="w-10">
                                            <div id="tooltip-animation_2" role="tooltip"
                                                class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                                <p class="text-center">
                                                    {{__('Невходит в ТОП-20 всех исполнителей User.uz')}}
                                                </p>
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        </div>
                                        <div data-tooltip-target="tooltip-animation_3" class="mx-1">
                                            <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-10 mt-1">
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
                            <div class="mb-6">
                                <div class="bg-gray-100 rounded-[10px] p-4">
                                    <div class="ml-0">
                                        <div
                                            class="text-[17px] text-gray-500 font-semibold">{{__('Стоимость')}} {{$selected->price}}
                                            UZS
                                        </div>

                                        <div
                                            class="text-[17px] text-gray-500 my-5">{{$selected->description}}</div>
                                        

                                        @if($selected->not_free == 1 || $task->user_id == auth()->id())
                                            <div
                                                class="text-[17px] text-gray-500 font-semibold my-4">{{__('Телефон исполнителя:')}} +998 {{$selected->performer->phone_number}}</div>
                                        @endif


                                        @auth()
                                            @if($task->status == 3 && $selected->performer_id == $task->performer_id)
                                                <div class="w-10/12 mx-auto">
                                                    <a href="{{ route('user', $selected->performer->id) }}"
                                                       class="text-semibold text-center w-[200px] mb-2 md:w-[320px] ml-0 inline-block py-3 px-4 hover:bg-gray-200 transition duration-200 bg-white text-black font-medium border border-gray-300 rounded-md">
                                                        {{__('Написать в чат')}}
                                                    </a>

                                                </div>
                                            @elseif($task->status <= 2 && auth()->user()->id == $task->user_id)
                                                <form
                                                    action="{{ route('response.selectPerformer', $selected->id) }}"
                                                    method="post">
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        class="cursor-pointer text-semibold text-center w-[200px]
                                        md:w-[320px] md:ml-4 inline-block py-3 px-4 bg-white transition
                                        duration-200 text-white bg-green-500 hover:bg-green-500 font-medium
                                        border border-transparent rounded-md"> {{__('Выбрать исполнителем')}}</button>

                                                </form>
                                            @endif

                                        @endauth

                                        <div class="text-gray-400 text-[14px] my-6">
                                            {{__('Выберите исполнителя, чтобы потом оставить отзыв о работе.')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif


                        @foreach ($responses as $response)
                            @if($response->performer)
                                <div class="my-6 flex flex-row">
                                    <div class="">
                                        <img class="w-24 h-24 rounded-lg border-2"
                                             @if ($response->performer->avatar == Null)
                                             src='{{asset("storage/images/default.jpg")}}'
                                             @else
                                             src="{{asset("storage/{$response->performer->avatar}")}}"
                                             @endif alt="avatar">
                                    </div>
                                    <div class="sm:ml-4 ml-0 flex flex-col sm:my-0 my-3">
                                        @if (Auth::check() && Auth::user()->id == $response->performer->id)
                                            <a href="/profile"
                                            class="text-2xl text-blue-500 hover:text-red-500">{{ $response->performer->name }}
                                            </a>
                                        @else
                                            <a href="/performers/{{$response->performer->id}}"
                                                class="text-blue-400 text-xl font-semibold hover:text-blue-500">
                                                {{ $response->performer->name }}
                                            </a>
                                        @endif
                                        <input type="text" name="performer_id" class="hidden"
                                               value="{{ $response->performer_id }}">
                                        <div class="text-gray-700 sm:mt-4 mt-2">
                                            <i class="fas fa-star text-yellow-500 mr-1"></i>{{ $response->performer->reviews()->count()? number_format($response->performer->goodReviews()->count() / $response->performer->reviews()->count()):0 }}
                                            по {{ $response->performer->reviews()->count() }} отзывам
                                        </div>
                                    </div>
                                    <div class="flex flex-row items-start">
                                        <div data-tooltip-target="tooltip-animation_1" class="mx-1 tooltip-1">
                                            <img
                                                src="{{ $response->performer->is_email_verified && $response->performer->is_phone_number_verified? asset('images/verify.png') : asset('images/verify_gray.png')  }}"
                                                alt="" class="w-10">
                                            <div id="tooltip-animation_1" role="tooltip"
                                                 class="inline-block sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                                @if ($response->performer->is_email_verified && $response->performer->is_phone_number_verified)
                                                    <p class="text-center">
                                                        {{__('Номер телефона и Е-mail пользователя подтверждены')}}
                                                    </p>
                                                @else
                                                    <p class="text-center">
                                                        {{__('Номер телефона и Е-mail пользователя неподтверждены')}}
                                                    </p>
                                                @endif
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        </div>
                                        @if($response->performer->role_id == 2)
                                            @foreach($about as $rating)
                                                @if($rating->id == $response->performer_id)
                                                    <div data-tooltip-target="tooltip-animation_2" class="mx-1 tooltip-2">
                                                        <img src="{{ asset('images/best.png') }}" alt="" class="w-10">
                                                        <div id="tooltip-animation_2" role="tooltip"
                                                            class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                                            <p class="text-center">
                                                                {{__('Входит в ТОП-20 исполнителей User.uz')}}
                                                            </p>
                                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                            <div data-tooltip-target="tooltip-animation_3" class="mx-1">
                                                @if($response->performer->tasks()->count() >= 50)
                                                    <img src="{{ asset('images/50.png') }}" alt="" class="w-10 mt-1">
                                                @else
                                                    <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-10 mt-1">
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
                                            <div data-tooltip-target="tooltip-animation_2" class="mx-1 tooltip-2">
                                                <img src="{{ asset('images/best_gray.png') }}" alt="" class="w-10">
                                                <div id="tooltip-animation_2" role="tooltip"
                                                    class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                                    <p class="text-center">
                                                        {{__('Невходит в ТОП-20 всех исполнителей User.uz')}}
                                                    </p>
                                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                                </div>
                                            </div>
                                            <div data-tooltip-target="tooltip-animation_3" class="mx-1">
                                                <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-10 mt-1">
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
                                <div class="mb-6">
                                    <div class="bg-gray-100 rounded-[10px] p-4">
                                        <div class="ml-0">
                                            <div
                                                class="text-[17px] text-gray-500 font-semibold">{{__('Стоимость')}} {{$response->price}}
                                                UZS
                                            </div>

                                            <div
                                                class="text-[17px] text-gray-500 my-5">{{$response->description}}</div>
                                            @if($response->not_free == 1)
                                                <div
                                                    class="text-[17px] text-gray-500 font-semibold my-4">{{__('Телефон исполнителя:')}} {{$response->performer->phone_number}}</div>
                                            @endif

                                            @auth()
                                                @if($task->status == 3 && $response->performer_id == $task->performer_id)
                                                    <div class="w-10/12 mx-auto">
                                                        <a href="{{ route('user', $response->performer->id) }}"
                                                           class="text-semibold text-center w-[200px] mb-2 md:w-[320px] ml-0 inline-block py-3 px-4 hover:bg-gray-200 transition duration-200 bg-white text-black font-medium border border-gray-300 rounded-md">
                                                            {{__('Написать в чат')}}
                                                        </a>

                                                    </div>
                                                @elseif($task->status <= 2 && auth()->user()->id == $task->user_id)
                                                    <form
                                                        action="{{ route('response.selectPerformer', $response->id) }}"
                                                        method="post">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="cursor-pointer text-semibold text-center w-[200px]
                                        md:w-[320px] md:ml-4 inline-block py-3 px-4 bg-white transition
                                        duration-200 text-white bg-green-500 hover:bg-green-500 font-medium
                                        border border-transparent rounded-md"> {{__('Выбрать исполнителем')}}</button>

                                                    </form>
                                                @endif

                                            @endauth

                                            <div class="text-gray-400 text-[14px] my-6">
                                                {{__('Выберите исполнителя, чтобы потом оставить отзыв о работе.')}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                    @else
                        @if(!$task->responses_count)
                            <div class="text-4xl font-semibold my-6">
                                {{__('У задания нет откликов')}}
                            </div>
                        @endif
                    @endif
                    <hr>
                @endauth
                
                @if ($task->status == 4)
                    @foreach ($respons_reviews as $respons_review)
                        @if ($respons_review->good_bad==1 && $respons_review->task_id == $task->id)
                            <div class="my-6">
                                <div class="flex flex-row gap-x-2 my-4">
                                    <img src="@if ($task->user->avatar == ''){{ asset("storage/images/default.png") }}
                                    @else{{asset("storage/{$task->user->avatar}") }}" @endif alt="#"
                                        class="w-12 h-12 border-2 rounded-lg border-gray-500">
                                    <div class="flex flex-col">
                                       @if (Auth::check() && Auth::user()->id == $task->user->id)
                                            <a href="/profile"
                                            class="text-2xl text-blue-500 hover:text-red-500">{{$task->user->name ?? $task->user_name}}
                                            </a>
                                        @else
                                            <a href="/performers/{{$task->user->id}}"
                                            class="text-2xl text-blue-500 hover:text-red-500">{{$task->user->name ?? $task->user_name}}
                                            </a>
                                        @endif
                                        <i class="far fa-thumbs-up text-gray-400"></i>
                                    </div>
                                </div>
                                <div class="w-full py-3 px-6 bg-yellow-50 rounded-xl">
                                    <p>{{__('Задание')}} <a href="#"
                                                            class="hover:text-red-400 border-b border-gray-300 hover:border-red-400"> {{$task->name}} </a> {{__('выполнено')}}</p>
                                    <p class="border-t-2 border-gray-300 my-3 pt-3"><i class="far fa-thumbs-up text-gray-400 mr-3"></i>{{$respons_review->description}}</p>
                                    <p class="text-right">{{$respons_review->created}}</p>
                                </div>
                            </div>
                        @elseif ($respons_review->good_bad==0 && $respons_review->task_id == $task->id)
                            <div class="my-6">
                                <div class="flex flex-row gap-x-2 my-4">
                                    <img src="@if ($task->user->avatar == ''){{ asset("storage/images/default.png") }}
                                    @else{{asset("storage/{$task->user->avatar}") }}" @endif alt="#"
                                        class="w-12 h-12 border-2 rounded-lg border-gray-500">
                                    <div class="flex flex-col">
                                       @if (Auth::check() && Auth::user()->id == $task->user->id)
                                            <a href="/profile"
                                            class="text-2xl text-blue-500 hover:text-red-500">{{$task->user->name ?? $task->user_name}}
                                            </a>
                                        @else
                                            <a href="/performers/{{$task->user->id}}"
                                            class="text-2xl text-blue-500 hover:text-red-500">{{$task->user->name ?? $task->user_name}}
                                            </a>
                                        @endif
                                        <i class="far fa-thumbs-down text-gray-400"></i>
                                    </div>
                                </div>
                                <div class="w-full py-3 px-6 bg-yellow-50 rounded-xl">
                                    <p>{{__('Задание')}} <a href="#"
                                                            class="hover:text-red-400 border-b border-gray-300 hover:border-red-400"> {{$task->name}} </a> {{__('выполнено')}}</p>
                                    <p class="border-t-2 border-gray-300 my-3 pt-3"><i class="far fa-thumbs-down text-gray-400 mr-3"></i>{{$respons_review->description}}</p>
                                    <p class="text-right">{{$respons_review->created}}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif

                <div>
                    @if(count($same_tasks))
                        <div class=" my-3">
                            <h1 class="font-medium text-3xl mt-3">{{__('Похожиe задания')}}</h1>
                            @foreach($same_tasks as $item)
                                @if ($item->user_id !=null)
                                    <div class="border-2 border-gray-500 rounded-xl bg-gray-50 hover:bg-blue-100 h-auto my-3">
                                        <div class="grid grid-cols-5 w-11/12 mx-auto py-2">
                                            <div class="sm:col-span-3 col-span-5 flex flex-row">
                                                <div class="sm:mr-6 mr-3 w-1/6">
                                                    <img src="{{ asset('storage/'.$item->category->ico) }}"
                                                        class="text-2xl float-left text-blue-400 sm:mr-4 mr-3 h-14 w-14 bg-blue-200 p-2 rounded-xl"/>
                                                </div>
                                                <div class="w-5/6">
                                                    <a href="/detailed-tasks/{{$item->id}}"
                                                    class="sm:text-lg text-base font-semibold text-blue-500 hover:text-red-600">{{ $item->name }}</a>
                                                    <p class="text-sm">{{ count($addresses)? $addresses[0]->location:'Можно выполнить удаленно' }}</p>
                                                    @if($item->date_type == 1 || $item->date_type == 3)
                                                        <p class="text-sm my-0.5">{{__('Начать')}} {{ $item->start_date }}</p>
                                                    @endif
                                                    @if($item->date_type == 2 || $item->date_type == 3)
                                                        <p class="text-sm my-0.5">{{__('Закончить')}} {{ $item->end_date }}</p>
                                                    @endif
                                                    @if($item->oplata == 1)
                                                        <p class="text-sm">{{__(' Оплата наличными')}}</p>
                                                    @else
                                                        <p class="text-sm">{{__('Оплата через карту')}}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2 col-span-5 sm:text-right text-left sm:ml-0 ml-16">
                                                <p class="sm:text-lg text-sm font-semibold text-gray-700">
                                                    @if ( __('до') == 'gacha' )
                                                        {{ number_format($task->budget) }} {{__('сум')}}{{__('до')}}
                                                    @else
                                                        {{__('до')}} {{ number_format($task->budget) }} {{__('сум')}}
                                                    @endif
                                                </p>
                                                <span class="text-sm sm:mt-5 sm:mt-1 mt-0">{{__('Откликов')}} -
                                                    @if ($item->response_count>0)
                                                        {{  $item->response_count }}
                                                    @else
                                                        0
                                                    @endif
                                                </span>
                                                <p class="text-sm sm:mt-1 mt-0">{{ $item->category->name }}</p>
                                                @if (Auth::check() && Auth::user()->id == $item->user->id)
                                                    <a href="/profile"
                                                    class="text-sm sm:mt-1 mt-0 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">{{ $item->user?$item->user->name:'' }}</a>
                                                @else
                                                    <a href="/performers/{{$item->user->id}}"
                                                    class="text-sm sm:mt-1 mt-0 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">{{ $item->user?$item->user->name:'' }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            {{--end left sidebar start --}}

            {{-- right sidebar start --}}
            <div class="lg:w-3/12 w-full mt-8 lg:ml-8 ml-0">
                <div class="mb-10">
                    <h1 class="text-xl font-medium mb-4">{{__('Задание')}} № {{$task->id}}</h1>
                    <div>
                        <button onclick="toggleModal44()"
                                class="px-3 py-3 border border-3 ml-4 rounded-md border-gray-500 hover:border-gray-400">
                            <i class="fas fa-share-alt"></i>
                        </button>
                        @if (Auth::check())
                            <button onclick="toggleModal45()"
                                    class="px-3 py-3 border border-3 ml-4 rounded-md border-gray-500 hover:border-gray-400">
                                <i class="far fa-flag"></i>
                            </button>
                        @else
                            <button
                                class="px-3 py-3 border border-3 ml-4 rounded-md border-gray-400">
                                <i class="far fa-flag"></i>
                            </button>
                        @endif
                    </div>
                </div>
                <h1 class="text-lg">{{__('Заказчик этого задания')}}</h1>
                <div class="flex flex-col mt-4">
                    <div class="mb-4">
                        <img class="border-2 border-radius-500 border-gray-400 w-32 h-32 rounded-lg" alt="#"
                             src="@if ($task->user->avatar == ''){{ asset("storage/images/default.png") }}
                             @else{{asset("storage/{$task->user->avatar}") }}" @endif
                        >
                    </div>
                    <div class="">
                        @if (Auth::check() && Auth::user()->id == $task->user->id)
                            <a href="/profile"
                               class="text-2xl text-blue-500 hover:text-red-500">{{$task->user->name ?? $task->user_name}}
                            </a>
                        @else
                            <a href="/performers/{{$task->user->id}}"
                               class="text-2xl text-blue-500 hover:text-red-500">{{$task->user->name ?? $task->user_name}}
                            </a>
                        @endif

                        <br>
                        <a class="text-xl text-gray-500">
                            @if($task->user->age != "")
                                <p class="inline-block text-m mr-2">
                                    {{$task->user->age}}
                                    @if($task->user->age>20 && $task->user->age%10==1) {{__('год')}}
                                    @elseif ($task->user->age>20 && ($task->user->age%10==2 || $task->user->age%10==3 || $task->user->age%10==1)) {{__('года')}}
                                    @else {{__('лет')}}
                                    @endif
                                </p>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
            {{--end right sidebar start --}}
        </div>
    </div>

    {{-- modal content --}}
    @include('task.detailed_modal')
    {{-- end modal cotent --}}

    <script type='text/javascript'
            src='https://platform-api.sharethis.com/js/sharethis.js#property=620cba4733b7500019540f3c&product=inline-share-buttons'
            async='async'></script>
    <input type="hidden" id="task" value="{{ $task->id }}">
    <script src="{{asset('js/tasks/detailed-tasks.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/picturefill/2.3.1/picturefill.min.js"></script>
    <script
        src="https://cdn.rawgit.com/sachinchoolur/lightgallery.js/master/dist/js/lightgallery.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-pager.js/master/dist/lg-pager.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-autoplay.js/master/dist/lg-autoplay.js"></script>
    <script
        src="https://cdn.rawgit.com/sachinchoolur/lg-fullscreen.js/master/dist/lg-fullscreen.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-zoom.js/master/dist/lg-zoom.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-hash.js/master/dist/lg-hash.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-share.js/master/dist/lg-share.js"></script>
    <script type="text/javascript" src="{{ asset('js/lg-thumbnail.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/lg-rotate.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/lg-video.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/fancybox.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/mediateka.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lightgallery.css') }}">
    <link rel="stylesheet" href="{{asset('css/modal.css')}}">

    <div style="display: none;">

        @foreach(json_decode($task->photos)??[] as $key => $image)
            @if ($loop->first)

            @else
                <a style="display: none;" class="boxItem" href="{{ asset('storage/'.$image) }}"
                   data-fancybox="img1"
                   data-caption="<span>{{ $task->created_at }}</span>">
                    <div class="mediateka_photo_content">
                        <img src="{{ asset('storage/'.$image)  }}" alt="">
                    </div>
                </a>
            @endif
        @endforeach
    </div>

@endsection

