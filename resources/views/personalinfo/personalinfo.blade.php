@extends("layouts.app")

@section("content")
<script id="map_api" src="https://api-maps.yandex.ru/2.1/?apikey=f4b34baa-cbd1-432b-865b-9562afa3fcdb&lang={{__('ru_RU')}}&onload=onLoad" type="text/javascript">
</script>

<div class="container-sm md:-mx-0 mx-auto my-3">
    <div class="shadow-2xl px-10 rounded-md w-full sm:w-7/12 mx-auto grid grid-flow-col gap-4 my-5 flex-wrap md:flex-wrap-reverse">
        <div class="text-center grid-rows-12 p-5 hidShad">
            {!! App\Services\CustomService::getContentText('verificationInfo', 'verificationInfo') !!}
            <div class="flex justify-center mt-4">
                <img src="{{ App\Services\CustomService::getContentImage('verificationInfo', 'verificationInfo') }}" alt="">
            </div>
        </div>
        <div class="grid-rows-12 sm:px-5 px-0 pb-5  showshad hidden">
            <div class="container sm:p-5 sm:p-0">
                <p class="text-2xl font-semibold mb-3 text-center ">
                    <span class="p-5">{{__('Как вас представлять заказчикам?')}}</span>
                </p>
                <p class="text-base mt-3 text-center ">
                    {{__('Эта данные нужны для работы на сервисе и связи с заказчиком. При этом заказчик увидит только ваше имя и телефон.')}}
                </p>
                <form class="mt-4 w-full" method="Post" action="{{route('profile.verificationInfoStore')}}">
                    @csrf
                    <div>
                        <div class="flex items-center rounded-lg border py-1">
                            <input autocomplete="off" oninput="myFunction()" name="location" id="suggest0" class="appearance-none bg-transparent w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none focus:border-yellow-500" type="text" placeholder="{{__('Город, Улица, Дом')}}" value="{{session('location2')}}" name="location0" required>
                            <button id="getlocal" class="flex-shrink-0 border-transparent border-4 text-teal-500 hover:text-teal-800 text-sm py-1 px-2 rounded" type="button">
                                <svg class="h-4 w-4 text-purple-500" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" />
                                    <path d="M21 3L14.5 21a.55 .55 0 0 1 -1 0L10 14L3 10.5a.55 .55 0 0 1 0 -1L21 3" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-3">
                            <label class="text-gray-500 text-sm" for="name">{{__('Имя')}}</label>
                            <input type="text" id="name" value="{{auth()->user()->name}}" name="name" class="block px-2 w-full border  border-grey-300 py-2 rounded-lg shadow-sm focus:outline-none focus:border-yellow-500 focus:ring focus:ring-yellow-300" />
                        </div>
                        @error('name')
                        <p class="text-red-500">{{ $message }}</p>
                        @enderror
                        <div class="mt-3 mb-3">
                            <label class="text-gray-500 text-sm" for="lastname">{{__('Фамилия')}}</label>
                            <input type="text" id="lastname" value="{{auth()->user()->last_name}}" name="last_name" class="block px-2 w-full border  border-grey-300 py-2 rounded-lg shadow-sm focus:outline-none focus:border-yellow-500" />
                        </div>
                        @error('last_name')
                            <p class="text-red-500">{{ $message }}</p>
                        @enderror

                        <label for="date" class="mt-3 text-gray-500 text-sm">{{__('Дата рождения')}}</label>
                        <div class="flatpickr inline-block flex flex-shrink">
                            <div class="flex-shrink">
                                <input type="text" name="born_date" value="{{auth()->user()->born_date}}" placeholder="{{__('Какой месяц..')}}" data-input class="sm:w-full w-60 bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm text-xs rounded-lg focus:outline-none focus:border-yellow-500 block pl-10 p-2.5" required> <!-- input is mandatory -->
                            </div>
                            <div class="flatpickr-calendar w-full sm:text-sm"></div>
                            <div class="transform hover:scale-125 relative right-8 my-auto">
                                <a class="input-button w-1 h-1" title="toggle" data-toggle>
                                    <i class="far fa-calendar-alt fill-current text-yellow-500"></i>
                                </a>
                            </div>
                            <div class="transform hover:scale-125 my-auto">
                                <a class="input-button w-1 h-1" title="clear" data-clear>
                                    <i class="fas fa-trash-alt stroke-current text-red-500 "></i>
                                </a>
                            </div>
                            @error('born_date')
                                <p class="text-red-500 mt-2 ml-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex w-full gap-x-4 mt-4">
                            <a onclick="backPersonalinfo()" class="w-1/3 cursor-pointer	  border border-black-700 hover:border-black transition-colors rounded-lg py-2 text-center flex justify-center items-center gap-2">
                                {{__('Назад')}}
                            </a>
                            <input type="submit" class="bg-green-500 hover:bg-green-500 w-2/3 cursor-pointer text-white font-bold py-5 px-5 rounded" name="" value="{{__('Далее')}}">
                        </div>
                </form>
                <p class="text-xs text-center pt-3">
                    {{__('Продолжая верификацию, вы подтверждаете, что проходите её впервые, указали достоверную информацию о себе и соглашаетесь на обработку персональных данных.')}}
                </p>
            </div>
        </div>
    </div>
</div>
<style>.flatpickr-calendar{max-width: 300px; width: 100%;} </style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/ru.js"></script>
<script>
    flatpickr(".flatpickr",
        {
            wrap: true,
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            maxDate: new Date().fp_incr(-6575),
            @if(session('lang') === 'ru')
            locale: 'ru',
            @else
            locale: {
                weekdays: {
                    shorthand: ['Yak', 'Du', 'Se', 'Chor', 'Pay', 'Juma', 'Shan'],
                    longhand: ['Yakshanba', 'Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma', 'Shanba'],
                },
                months: {
                    shorthand: ['Yan', 'Fev', 'Mart', 'Apr', 'May', 'Iyun', 'Iyul', 'Avg', 'Sen', 'Okt', 'Noy', 'Dek'],
                    longhand: ['Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun', 'Iyul', 'Avgust', 'Sentabr', 'Oktabr', 'Noyabr', 'Dekabr'],
                },
            }
            @endif
        }
    )
</script>
@endsection

@push('javascript')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<script src="{{ asset('js/personalinfo/personalinfo.js') }}"></script>
@endpush
