@if($auth_response)
    <div class="text-4xl font-semibold my-6">
        @if ($task->response_count <= 4)
            @if ($task->responses_count == 1)
                {{__('У задания')}} {{$task->responses()->count()}} {{__('отклик')}}
            @else
                {{__('У задания')}} {{$task->responses()->count()}} {{__('откликa')}}
            @endif
        @else
            {{__('У задания')}} {{$task->responses()->count()}} {{__('откликов')}}
        @endif
    </div>
<div class="mt-3">
    <h1 class="text-3xl font-semibold text-black">{{__('Ваш отклик')}}</h1>
    <div class="border-2 border-gray-400 p-2 rounded-lg my-2">
        <div class="my-3 flex flex-row">
            <div class="">
                <img class="w-24 h-24 rounded-lg border-2"
                    src='{{ auth()->user()->avatar? asset('storage/'.auth()->user()->avatar) : asset('images/avatar-avtor-image.png') }}'
                    alt="avatar">
            </div>
            <div class="sm:ml-4 ml-0 flex flex-col sm:my-0 my-3">
                <a href="/profile" class="text-2xl text-blue-500 hover:text-red-500">
                    {{ auth()->user()->name }}
                </a>
                <input type="text" name="performer_id" class="hidden"
                    value="">
                    <div class="text-gray-700 sm:mt-4 mt-2">
                        @if(session('lang') === 'ru')
                            <i class="fas fa-star text-yellow-500 mr-1"></i>{{ auth()->user()->review_rating }}
                            по {{ auth()->user()->reviews }} отзывам
                        @else
                            <i class="fas fa-star text-yellow-500 mr-1"></i>{{ auth()->user()->reviews }}
                            ta sharhdan {{ auth()->user()->review_rating }} ball
                        @endif
                    </div>
            </div>
            <div class="flex flex-row items-start">
                @if (auth()->user()->is_email_verified && auth()->user()->is_phone_number_verified)
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
                @if(in_array(auth()->user()->id, $top_users))
                    <div data-tooltip-target="tooltip-animation-on-2"
                         class="mx-1 tooltip-2">
                        <img src="{{ asset('images/best.png') }}" alt="" class="w-10">
                        <div id="tooltip-animation-on-2" role="tooltip"
                             class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                            <p class="text-center">
                                {{__('Входит в ТОП-20 исполнителей USer.Uz')}}
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
                                {{__('Не входит в ТОП-20 всех исполнителей USer.Uz')}}
                            </p>
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                @endif
                <div data-tooltip-target="tooltip-animation-many" class="mx-1">
                    @if(auth()->user()->reviews >= 50 && (int)auth()->user()->role_id === \App\Models\User::ROLE_PERFORMER)
                        <img src="{{ asset('images/50.png') }}" alt="" class="w-10 mt-1">
                    @else
                        <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-10 mt-1">
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
        <div class="mb-6">
            <div class="bg-gray-100 rounded-[10px] p-4">
                <div class="ml-0">
                    <div
                        class="text-gray-500 font-semibold">{{__('Стоимость')}} {{ number_format($auth_response->price) }}
                        UZS
                    </div>

                    <div
                        class="text-[17px] text-gray-500 my-5">{{ $auth_response->description }}
                    </div>

                    <div
                        class="text-gray-500 font-semibold my-4">{{__('Телефон исполнителя:')}}
                         {{ auth()->user()->phone_number }}
                    </div>
                    @if ((int)$task->status === \App\Models\Task::STATUS_IN_PROGRESS && auth()->user()->id === $task->performer_id)
                        <div class="w-10/12 mx-auto">
                            <a class="auth_response cursor-pointer text-semibold text-center mb-2 inline-block py-3 px-4 hover:bg-gray-200 transition duration-200 bg-white text-black font-medium border border-gray-300 rounded-md">
                                {{__('Написать в чат')}}
                            </a>
                            <script>
                                const Auth_response = (event) => {
                                    jsPanel.create({
                                        content: `<iframe src="{{ url('/chat/' . $task->user_id) }}" frameborder="0" style="width: 100%; height: 100%"></iframe>`,
                                        theme: 'primary',
                                        position: 'center',
                                        closeOnEscape: true,
                                        headerTitle: 'Universal Services',
                                        headerControls: {
                                            size: 'md',
                                        },
                                        borderRadius: '1rem',
                                        panelSize: {
                                            width: '80vw',
                                            height: '90vh'
                                        },
                                        contentSize: '80vw 90vh',
                                    });
                                    event.preventDefault();
                                }
                                const auth_response = document.querySelector('.auth_response');
                                auth_response.addEventListener('click', Auth_response);
                            </script>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@auth()
@if ($task->user_id === auth()->user()->id)
    <div class="text-4xl font-semibold my-6">
        @if ($task->response_count <= 4)
            @if ($task->responses_count == 1)
                {{__('У задания')}} {{$task->responses()->count()}} {{__('отклик')}}
            @else
                {{__('У задания')}} {{$task->responses()->count()}} {{__('откликa')}}
            @endif
        @else
            {{__('У задания')}} {{$task->responses()->count()}} {{__('откликов')}}
        @endif
    </div>

    @if($selected && $selected->performer)
        <h1 class="font-semibold text-2xl">{{__('Выбранный исполнитель')}} </h1>
        <div class="border-2 border-gray-400 p-2 rounded-lg my-2">
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
                    @if (Auth::check() && Auth::user()->id === $selected->performer->id)
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
                        @if(session('lang') === 'ru')
                            <i class="fas fa-star text-yellow-500 mr-1"></i>{{$selected->performer->review_rating}}
                            по {{ $selected->performer->reviews }} отзывам
                        @else
                            <i class="fas fa-star text-yellow-500 mr-1"></i>{{ $selected->performer->reviews }}
                            ta sharhdan {{$selected->performer->review_rating}} ball
                        @endif
                    </div>
                </div>
                <div class="flex flex-row items-start">
                    @if ($selected->performer->is_email_verified && $selected->performer->is_phone_number_verified)
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
                    @if(in_array($selected->performer->id, $top_users))
                        <div data-tooltip-target="tooltip-animation-on-2"
                             class="mx-1 tooltip-2">
                            <img src="{{ asset('images/best.png') }}" alt="" class="w-10">
                            <div id="tooltip-animation-on-2" role="tooltip"
                                 class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                <p class="text-center">
                                    {{__('Входит в ТОП-20 исполнителей USer.Uz')}}
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
                                    {{__('Не входит в ТОП-20 всех исполнителей USer.Uz')}}
                                </p>
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </div>
                    @endif
                    <div data-tooltip-target="tooltip-animation-many" class="mx-1">
                        @if($selected->performer->reviews >= 50 && (int)$selected->performer->role_id === \App\Models\User::ROLE_PERFORMER)
                            <img src="{{ asset('images/50.png') }}" alt="" class="w-10 mt-1">
                        @else
                            <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-10 mt-1">
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
            <div class="mb-6">
                <div class="bg-gray-100 rounded-[10px] p-4">
                    <div class="ml-0">
                        <div class="text-gray-500 font-semibold">
                            {{__('Стоимость')}} {{number_format($selected->price)}} UZS
                        </div>

                        <div class="text-[17px] text-gray-500 my-5">{{$selected->description}}</div>


                        @if((int)$selected->not_free === 1 || $task->user_id === auth()->id())
                            <div
                                class="text-gray-500 font-semibold my-4">{{__('Телефон исполнителя:')}} {{$selected->performer->phone_number}}</div>
                        @endif


                        @auth()
                            @if((int)$task->status === \App\Models\Task::STATUS_IN_PROGRESS && $selected->performer_id === $task->performer_id)
                                <div class="w-10/12 mx-auto">
                                    <a class="selected_chat cursor-pointer text-semibold text-center w-[200px] mb-2 md:w-[320px] ml-0 inline-block py-3 px-4 hover:bg-gray-200 transition duration-200 bg-white text-black font-medium border border-gray-300 rounded-md">
                                        {{__('Написать в чат')}}
                                    </a>
                                    <script>
                                        const Selected_chat = (event) => {
                                            jsPanel.create({
                                                content: `<iframe src="{{ url('/chat/' . $selected->performer->id) }}" frameborder="0" style="width: 100%; height: 100%"></iframe>`,
                                                theme: 'primary',
                                                position: 'center',
                                                closeOnEscape: true,
                                                headerTitle: 'Universal Services',
                                                headerControls: {
                                                    size: 'md',
                                                },
                                                borderRadius: '1rem',
                                                panelSize: {
                                                    width: '80vw',
                                                    height: '90vh'
                                                },
                                                contentSize: '80vw 90vh',
                                            });
                                            event.preventDefault();
                                        }
                                        const selected_chat = document.querySelector('.selected_chat');
                                        selected_chat.addEventListener('click', Selected_chat);
                                    </script>
                                </div>
                            @elseif($task->status <= \App\Models\Task::STATUS_RESPONSE && auth()->user()->id === $task->user_id)
                                <form action="{{ route('response.selectPerformer', $selected->id) }}" method="post">
                                    @csrf
                                    <button type="submit" class="cursor-pointer text-semibold text-center md:ml-4 inline-block py-3 px-4 bg-white transition duration-200 text-white bg-green-500 hover:bg-green-500 font-medium border border-transparent rounded-md"> {{__('Выбрать исполнителем')}}</button>
                                </form>
                            @endif

                        @endauth

                        <div class="text-gray-400 text-[14px] my-6">
                            {{__('Выберите исполнителя, чтобы потом оставить отзыв о работе.')}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @foreach ($responses as $response)
        @if($response->performer)
        <div class="border-2 border-gray-400 p-2 rounded-lg my-2">
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
                    @if (Auth::check() && Auth::user()->id === $response->performer->id)
                        <a href="/profile" class="text-2xl text-blue-500 hover:text-red-500">
                            {{ $response->performer->name }}
                        </a>
                    @else
                        <a href="/performers/{{$response->performer->id}}" class="text-blue-400 text-xl font-semibold hover:text-blue-500">
                            {{ $response->performer->name }}
                        </a>
                    @endif
                    <input type="text" name="performer_id" class="hidden" value="{{ $response->performer_id }}">
                    <div class="text-gray-700 sm:mt-4 mt-2">
                        @if(session('lang') === 'ru')
                            <i class="fas fa-star text-yellow-500 mr-1"></i>{{$response->performer->review_rating}}
                            по {{ $response->performer->reviews }} отзывам
                        @else
                            <i class="fas fa-star text-yellow-500 mr-1"></i>{{ $response->performer->reviews }}
                            ta sharhdan {{$response->performer->review_rating}}ball
                        @endif
                    </div>
                </div>
                <div class="flex flex-row items-start">
                    @if ($response->performer->is_email_verified && $response->performer->is_phone_number_verified)
                        <div data-tooltip-target="tooltip-animation-verified"
                             class="mx-1 tooltip-1">
                            <img src="{{asset('images/verify.png')}}" alt="" class="w-10">
                            <div id="tooltip-animation-verified" role="tooltip"
                                 class="inline-block sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                <p class="text-center">
                                    {{__('Номер телефона и Е-mail пользователя подтверждены')}}
                                </p>
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </div>
                    @else
                        <div data-tooltip-target="tooltip-animation-not-verified" class="mx-1 tooltip-1">
                            <img src="{{asset('images/verify_gray.png') }}" alt="" class="w-10">
                            <div id="tooltip-animation-not-verified" role="tooltip"
                                 class="inline-block sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                <p class="text-center">
                                    {{__('Номер телефона и Е-mail пользователя неподтверждены')}}
                                </p>
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </div>
                    @endif
                    @if(in_array($response->performer->id, $top_users))
                        <div data-tooltip-target="tooltip-animation-on-2"
                             class="mx-1 tooltip-2">
                            <img src="{{ asset('images/best.png') }}" alt="" class="w-10">
                            <div id="tooltip-animation-on-2" role="tooltip"
                                 class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                                <p class="text-center">
                                    {{__('Входит в ТОП-20 исполнителей USer.Uz')}}
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
                                    {{__('Не входит в ТОП-20 всех исполнителей USer.Uz')}}
                                </p>
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </div>
                    @endif
                    <div data-tooltip-target="tooltip-animation-many" class="mx-1">
                        @if($response->performer->reviews >= 50 && (int)$response->performer->role_id === \App\Models\User::ROLE_PERFORMER)
                            <img src="{{ asset('images/50.png') }}" alt="" class="w-10 mt-1">
                        @else
                            <img src="{{ asset('images/50_gray.png') }}" alt="" class="w-10 mt-1">
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
            <div class="mb-6">
                <div class="bg-gray-100 rounded-[10px] p-4">
                    <div class="ml-0">
                        <div class="text-gray-500 font-semibold">
                            {{__('Стоимость')}} {{number_format($response->price)}} UZS
                        </div>

                        <div class="text-[17px] text-gray-500 my-5">{{$response->description}}</div>
                        @if((int)$response->not_free === 1)
                            <div class="text-gray-500 font-semibold my-4">
                                {{__('Телефон исполнителя:')}} {{$response->performer->phone_number}}
                            </div>
                        @endif

                        @auth()
                            @if((int)$task->status === \App\Models\Task::STATUS_IN_PROGRESS && $response->performer_id === $task->performer_id)
                                <div class="w-10/12 mx-auto">
                                    <a class="responses_chat cursor-pointer text-semibold text-center ml-0 inline-block py-3 px-4 hover:bg-gray-200 transition duration-200 bg-white text-black font-medium border border-gray-300 rounded-md">
                                        {{__('Написать в чат')}}
                                    </a>
                                    <script>
                                        const Responses_chat = (event) => {
                                            jsPanel.create({
                                                content: `<iframe src="{{ url('/chat/' . $response->performer->id) }}" frameborder="0" style="width: 100%; height: 100%"></iframe>`,
                                                theme: 'primary',
                                                position: 'center',
                                                closeOnEscape: true,
                                                headerTitle: 'Universal Services',
                                                headerControls: {
                                                    size: 'md',
                                                },
                                                borderRadius: '1rem',
                                                panelSize: {
                                                    width: '80vw',
                                                    height: '90vh'
                                                },
                                                contentSize: '80vw 90vh',
                                            });
                                            event.preventDefault();
                                        }
                                        const responses_chat = document.querySelector('.responses_chat');
                                        responses_chat.addEventListener('click', Responses_chat);
                                    </script>
                                </div>
                            @elseif($task->status <= \App\Models\Task::STATUS_RESPONSE && auth()->user()->id === $task->user_id)
                                <form action="{{ route('response.selectPerformer', $response->id) }}" method="post">
                                    @csrf
                                    <button type="submit" class="cursor-pointer text-semibold text-center md:ml-4 inline-block py-3
                                         px-4 bg-white transition duration-200 text-white bg-green-500 hover:bg-green-500 font-medium
                                        border border-transparent rounded-md"> {{__('Выбрать исполнителем')}}
                                    </button>
                                </form>
                            @endif

                        @endauth

                        <div class="text-gray-400 my-6">
                            {{__('Выберите исполнителя, чтобы потом оставить отзыв о работе.')}}
                        </div>
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
