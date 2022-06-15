@extends("layouts.app")

@section('content')

    <div class="container w-4/5 mx-auto mt-12">

        <div class="flex lg:flex-row flex-col justify-center mt-6">
            @include('components.footerpage')
            <div class="lg:w-4/5 w-full lg:mt-0 mt-4">
                <div class="sm:w-4/5 w-full mx-auto">
                    <h1 class="font-semibold text-4xl">
                        {{__('Как оплатить через Paynet?')}}
                    </h1>
                </div>
                <div class="flex lg:flex-row flex-col mt-10 sm:w-4/5 w-full mx-auto">
                    <div class="lg:w-1/2 w-full my-auto text-center">
                        <h4 class="text-2xl font-medium mb-2">
                            {{__('Получайте отклики исполнителей')}}
                        </h4>
                        <p>{{__('Сразу после создания задания вам начнут поступать отклики от людей, которые готовы его выполнить.')}}</p>
                    </div>
                    <div class="lg:w-1/2 w-full">
                        <img src="{{asset('images/icons/1.jpg')}}" class="mx-auto w-80 h-64 border-2 rounded-lg border-gray-300"/>
                    </div>
                </div>
                <div class="flex lg:flex-row flex-col mt-4 sm:w-4/5 w-full mx-auto">
                    <div class="lg:w-1/2 w-full lg:block hidden">
                        <img src="{{asset('images/icons/2.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                    </div>
                    <div class="lg:w-1/2 w-full text-center ml-4 mt-4 lg:block hidden">
                        <h4 class="text-2xl font-medium mb-2">{{__('Выберите лучшего исполнителя')}}</h4>
                        <p>{{__('Вам остается выбрать среди откликов лучшее по цене или рейтингу исполнителя.')}}</p>
                    </div>

                    <div class="lg:w-1/2 w-full text-center ml-4 mt-4 lg:hidden block">
                        <h4 class="text-2xl font-medium mb-2">{{__('Выберите лучшего исполнителя')}}</h4>
                        <p class="mb-8">{{__('Вам остается выбрать среди откликов лучшее по цене или рейтингу исполнителя.')}}</p>
                    </div>
                    <div class="lg:w-1/2 w-full lg:hidden block">
                        <img src="{{asset('images/icons/2.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                    </div>
                </div>
                <div class="flex lg:flex-row flex-col mt-10 sm:w-4/5 w-full mx-auto">
                    <div class="lg:w-1/2 w-full my-auto text-center">
                        <h4 class="text-2xl font-medium mb-2">{{__('Завершение задания')}}</h4>
                        <p>{{__('После выполнения задания не забудьте подтвердить, что исполнитель выполнил поручение, и написать отзыв о его работе.')}}</p>
                    </div>
                    <div class="lg:w-1/2 w-full ml-4 lg:mt-0 mt-8">
                        <img src="{{asset('images/icons/3.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                    </div>
                </div>
                <div class="w-full my-12">
                    <img src="{{asset('images/icons/7.jpg')}}" alt="">
                </div>
                <div class="flex lg:flex-row flex-col mt-4 sm:w-4/5 w-full mx-auto">
                    <div class="lg:w-1/2 w-full lg:block hidden">
                        <img src="{{asset('images/icons/4.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                    </div>
                    <div class="lg:w-1/2 w-full text-center ml-4 mt-4 lg:block hidden">
                        <h4 class="text-2xl font-medium mb-2">{{__('Выберите лучшего исполнителя')}}</h4>
                        <p>{{__('Вам остается выбрать среди откликов лучшее по цене или рейтингу исполнителя.')}}</p>
                    </div>

                    <div class="lg:w-1/2 w-full text-center ml-4 mt-4 lg:hidden block">
                        <h4 class="text-2xl font-medium mb-2">{{__('Выберите лучшего исполнителя')}}</h4>
                        <p class="mb-8">{{__('Вам остается выбрать среди откликов лучшее по цене или рейтингу исполнителя.')}}</p>
                    </div>
                    <div class="lg:w-1/2 w-full lg:hidden block">
                        <img src="{{asset('images/icons/4.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                    </div>
                </div>
                <div class="flex lg:flex-row flex-col mt-10 sm:w-4/5 w-full mx-auto">
                    <div class="lg:w-1/2 w-full my-auto text-center">
                        <h4 class="text-2xl font-medium mb-2">{{__('Завершение задания')}}</h4>
                        <p>{{__('После выполнения задания не забудьте подтвердить, что исполнитель выполнил поручение, и написать отзыв о его работе.')}}</p>
                    </div>
                    <div class="lg:w-1/2 w-full ml-4 lg:mt-0 mt-8">
                        <img src="{{asset('images/icons/5.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                    </div>
                </div>
                <div class="flex lg:flex-row flex-col mt-4 sm:w-4/5 w-full mx-auto">
                    <div class="lg:w-1/2 w-full lg:block hidden">
                        <img src="{{asset('images/icons/6.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                    </div>
                    <div class="lg:w-1/2 w-full text-center ml-4 mt-4 lg:block hidden">
                        <h4 class="text-2xl font-medium mb-2">{{__('Выберите лучшего исполнителя')}}</h4>
                        <p>{{__('Вам остается выбрать среди откликов лучшее по цене или рейтингу исполнителя.')}}</p>
                    </div>

                    <div class="lg:w-1/2 w-full text-center ml-4 mt-4 lg:hidden block">
                        <h4 class="text-2xl font-medium mb-2">{{__('Выберите лучшего исполнителя')}}</h4>
                        <p class="mb-8">{{__('Вам остается выбрать среди откликов лучшее по цене или рейтингу исполнителя.')}}</p>
                    </div>
                    <div class="lg:w-1/2 w-full lg:hidden block">
                        <img src="{{asset('images/icons/6.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                    </div>
                </div>
            </div>
        </div>

    </div>





@endsection
