<figure class="w-full">
    <div class="float-right text-gray-500 text-lg">
        <i class="far fa-eye"> {{$user->performer_views()->count()}} {{__('просмотр')}}</i>
    </div>
    <div>
        @if(Cache::has('user-is-online-' . $user->id))
            <span class="text-green-500">{{__('В сети')}}</span>
        @else
            <span class="text-gray-500"> {{ $user->last_seen_at }}</span>
        @endif
        <h1 class="text-3xl font-bold ">{{$user->name}}</h1>
    </div>
    <div class="flex sm:flex-row flex-col w-full mt-6">
        <div class="sm:w-1/3 pb-10 w-full">
            <img class="border border-3 border-gray-400 h-44 w-44"
                 @if (!($user->avatar))
                     src='{{asset("images/pofilevector.png")}}'
                 @else
                     src="{{asset('storage/'.$user->avatar)}}"
                 @endif alt="avatar">
        </div>

        <div class="flex-initial sm:w-2/3 w-full sm:mt-0 mt-6 sm:ml-8 ml-0">
            <div class="w-2/3 text-base text-gray-500">
                @php
                    $age = Carbon\Carbon::parse($user->born_date)->age;
                @endphp
                @if( $age>0)
                    <p class="inline-block mr-2">
                        {{ $age}}
                        @switch(true)
                            @case ($age%10 === 1)
                                {{('год')}}
                                @break
                            @case($age%10 === 2 ||  $age%10 === 3 ||  $age%10 === 4)
                                {{('года')}}
                                @break
                            @default
                                {{__('лет')}}
                        @endswitch
                    </p>
                @endif
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
                @if ( session('lang') === 'ru' )
                    <p class="mt-2">{{__('Создал')}}
                        <span>{{count($task_count??[])}}</span> {{__('задание')}}
                    </p>
                @else
                    <p class="mt-2">
                        <span>{{count($task_count??[])}}</span> {{__('задание')}}
                        {{__('Создал')}}
                    </p>
                @endif

                @if(session('lang') === 'ru')
                    @switch($user->reviews)
                        @case(0)
                            <span>{{__('Отзывов нет')}}</span>
                            @break
                        @case(1)
                            <span>{{__('Получил')}} {{$user->reviews}} {{__('Отзыв')}}</span>
                            @break
                        @case(1 && 5)
                            <span>{{__('Получил')}} {{$user->reviews}} {{__('Отзыва')}}</span>
                            @break
                        @default
                            <span>{{__('Получил')}} {{$user->reviews}} {{__('Отзывов')}}</span>
                    @endswitch
                @else
                    {{$user->reviews}} {{__('ta sharh oldim')}}
                @endif
            </div>
            <div class="flex flex-row items-center mt-3" id="str1">
                <div class="flex flex-row items-center text-gray-500 text-base"> <p>{{__('Средняя оценка:')}}</p>
                    <span id="review{{$user->id}}" class="mx-1">{{$review_rating}}</span>
                </div>
                <div class="flex items-center ml-2" id="stars{{$user->id}}">
                </div>
            </div>
            <div class="text-gray-500 text-base mt-3 hidden" id="str2">{{__('Нет оценок')}}</div>
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

                @if(in_array($user->id, $top_users))
                    <div data-tooltip-target="tooltip-animation_2" class="mx-4 tooltip-2">
                        <img src="{{ asset('images/best.png') }}" alt="" class="w-24">
                        <div id="tooltip-animation_2" role="tooltip"
                             class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                            <p class="text-center">
                                {{__('Входит в ТОП-20 исполнителей USer.Uz')}}
                            </p>
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                @else
                    <div data-tooltip-target="tooltip-animation-on-top"
                         class="mx-4 tooltip-2">
                        <img src="{{ asset('images/best_gray.png') }}" alt="" class="w-24">
                        <div id="tooltip-animation-on-top" role="tooltip"
                             class="inline-block  sm:w-2/12 w-1/2 absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
                            <p class="text-center">
                                {{__('Не входит в ТОП-20 всех исполнителей USer.Uz')}}
                            </p>
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                @endif

                <div data-tooltip-target="tooltip-animation_3" class="mx-4">
                    @if(($user->review_good)+($user->review_bad) >= 50 && $user->role_id==2)
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
            </div>
        </div>
    </div>
</figure>
<div class="col-span-2 mt-4">
    <h1 class="text-3xl font-semibold text-gray-700">{{__('Обо мне')}}</h1>
    <p>{{$user->description}}</p>
</div>
<div class="mt-8">
    @if (count($portfolios) || $user->youtube_link != null)
        <h1 class="text-xl font-semibold mt-2">{{__('Примеры работ')}}</h1>
    @endif
    <div class="grid xl:grid-cols-3 md:grid-cols-2 grid-cols-1 w-full mx-auto">
        @foreach($portfolios as $portfolio)
            <a href="{{ route('performers.performers_portfolio', $portfolio->id) }}"
               class="border my-6 border-gray-400 mr-auto w-56 h-48 mr-6 sm:mb-0 mb-8">
                <img
                    src="{{  count(json_decode($portfolio->image)) == 0 ? '': asset('portfolio/'.json_decode($portfolio->image)[0])  }}"
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
    <div class="my-2">
        @if($user->youtube_link)
            <iframe class="my-4 sm:w-full w-5/6" width="644" height="362" id="iframe" src="{{$user->youtube_link}}" frameborder="0"></iframe>
        @endif
    </div>
</div>
