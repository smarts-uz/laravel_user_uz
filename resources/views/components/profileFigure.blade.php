<figure class="w-full">
    <div class="hidden md:block float-right mr-8 text-gray-500 text-base">
        <i class="far fa-eye"> {{$user->performer_views()->count()}}  {{__('просмотр')}}</i>
    </div>
    <br>
    <h2 class="font-bold text-2xl text-gray-800 mb-2">{{__('Здравствуйте')}}, {{$user->name}}!</h2>
    <div class="flex sm:flex-row flex-col mt-6">
        <div class="sm:w-1/3 pb-10 w-full">
            <img class="border border-3 border-gray-400 h-44 w-44" src="{{asset('storage/'.$user->avatar)}}" alt="avatar">
            <div class="rounded-md bg-gray-200 w-44 mt-2 py-1 border-2 border-gray-700" type="button">
                <input type="file" name="file" id="file" onclick="fileupdate()" class="hidden">
                <label for="file" class="p-1 cursor-pointer">
                    <i class="fas fa-camera mr-1"></i>
                    <span>{{__('Изменить фото')}}</span>
                </label>
            </div>
        </div>
        <div class="sm:w-2/3 w-full text-base text-gray-500 sm:ml-4 ml-0">
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
                    @else
                        {{__('город не обозначен')}}
                    @endisset
                </p>
            </span>
            <div class="text-gray-500 mt-2">
                @if ( session('lang') === 'ru' )
                    <p class="mt-2">{{__('Создал')}}
                        <a href="{{route('searchTask.mytasks')}}" class="text-blue-500 hover:text-red-600">
                            <span>{{$user->tasks()->count()}}</span> {{__('задание')}}
                        </a>
                    </p>
                @else
                    <p class="mt-2">
                        <a href="{{route('searchTask.mytasks')}}" class="text-blue-500 hover:text-red-600">
                            <span>{{$user->tasks()->count()}}</span> {{__('задание')}}
                        </a>
                        {{__('Создал')}}
                    </p>
                @endif
                @if(session('lang')==='ru')
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
                <div class="flex flex-row items-center"><p>{{__('Средняя оценка:')}}</p>
                    <span id="review{{$user->id}}" class="mx-1">{{$user->review_rating}}</span>
                </div>
                <div class="flex items-center ml-2" id="stars{{$user->id}}">
                </div>
            </div>
            <div class="mt-3 hidden" id="str2">{{__('Нет оценок')}}</div>
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
                    @if($user->reviews >= 50 && $user->role_id === \App\Models\User::ROLE_PERFORMER)
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
<link rel="stylesheet" href="{{ asset('path/ijaboCropTool.min.css') }}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{ asset('path/ijaboCropTool.min.js') }}"></script>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.css" rel="stylesheet">
<script>
    $('#file').ijaboCropTool({
        preview: '.image-previewer',
        setRatio: 1,
        allowedExtensions: ['jpg', 'jpeg', 'png'],
        buttonsText: ['{{__('Сохранить')}}', '{{__('Отмена')}}'],
        buttonsColor: ['#30bf7d', '#ee5155', -15],
        processUrl: '{{ route('profile.storeProfileImage') }}',
        withCSRF: ['_token', '{{ csrf_token() }}'],
        fileName: 'image',
        onSuccess: function (message, element, status) {
            window.location.href = "{{ route('profile.profileData') }}";
        },
        onError: function (message, element, status) {
            alert(message);
        }
    });
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/raty/3.1.1/jquery.raty.min.css"
      integrity="sha512-XsO5ywONBZOjW5xo5zqAd0YgshSlNF+YlX39QltzJWIjtA4KXfkAYGbYpllbX2t5WW2tTGS7bmR0uWgAIQ8JLQ=="
      crossorigin="anonymous" referrerpolicy="no-referrer"/>
<script src="https://cdn.jsdelivr.net/npm/jquery-raty-js@2.8.0/lib/jquery.raty.min.js"></script>
<script>
    var star = $('#review{{$user->id}}').text();
    if (star > 0) {
        $("#stars{{$user->id}}").raty({
            path: 'https://cdn.jsdelivr.net/npm/jquery-raty-js@2.8.0/lib/images',
            readOnly: true,
            score: star,
            size: 12
        });
    } else {
        $('#str1').addClass('hidden');
        $('#str2').removeClass('hidden');
    }

</script>
