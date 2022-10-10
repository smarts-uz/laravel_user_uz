<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Universal services </title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.css" rel="stylesheet">
</head>
<body>

<div class="container w-4/5 mx-auto mt-12">

    <div class="flex lg:flex-row flex-col justify-center mt-6">
        {{--@include('components.footerpage')--}}
        <div class="lg:w-4/5 w-full lg:mt-0 mt-4">
            <div class="sm:w-4/5 w-full mx-auto">
                <h1 class="font-semibold text-4xl text-center">
                    {{__('Как оплатить через Paynet?')}}
                </h1>
            </div>
            <div class="flex lg:flex-row flex-col mt-10 sm:w-4/5 w-full mx-auto">
                <div class="lg:w-1/2 w-full my-auto text-center">
                    <h4 class="text-2xl font-medium mb-2">
                        1-{{__('этап')}}
                    </h4>
                    <p>{{__('Для начала вам надо скачать программу Paynet.uz  и зарегистироваться.')}}</p>
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
                    <h4 class="text-2xl font-medium mb-2">2-{{__('этап')}}</h4>
                    <p>{{__('После установки, перейдите в раздел оплата. Нажмите на значок поиска в правом углу')}}</p>
                </div>

                <div class="lg:w-1/2 w-full text-center ml-4 mt-4 lg:hidden block">
                    <h4 class="text-2xl font-medium mb-2">2-{{__('этап')}}</h4>
                    <p class="mb-8">{{__('После установки, перейдите в раздел оплата. Нажмите на значок поиска в правом углу')}}</p>
                </div>
                <div class="lg:w-1/2 w-full lg:hidden block">
                    <img src="{{asset('images/icons/2.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                </div>
            </div>

            <div class="flex lg:flex-row flex-col mt-10 sm:w-4/5 w-full mx-auto">
                <div class="lg:w-1/2 w-full my-auto text-center">
                    <h4 class="text-2xl font-medium mb-2">3-{{__('этап')}}</h4>
                    <p>{{__('Напишите Universal services и кликните на результат поиска.')}}</p>
                </div>
                <div class="lg:w-1/2 w-full ml-4 lg:mt-0 mt-8">
                    <img src="{{asset('images/icons/3.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                </div>
            </div>

            <div class="w-full my-12 flex flex-col">
                <div class="text-center mb-4">
                    <h4 class="text-2xl font-medium mb-2">4-{{__('этап')}}</h4>
                    <p>{{__('Перейти в профиль и скопируйте свой ID.')}}</p>
                </div>
                <img src="{{asset('images/icons/7.jpg')}}" alt="">
            </div>

            <div class="flex lg:flex-row flex-col mt-4 sm:w-4/5 w-full mx-auto">
                <div class="lg:w-1/2 w-full lg:block hidden">
                    <img src="{{asset('images/icons/4.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                </div>
                <div class="lg:w-1/2 w-full text-center ml-4 mt-4 lg:block hidden">
                    <h4 class="text-2xl font-medium mb-2">5-{{__('этап')}}</h4>
                    <p>{{__('Введите свой ID который вы скопировали и гапишите сумму.')}}</p>
                </div>

                <div class="lg:w-1/2 w-full text-center ml-4 mt-4 lg:hidden block">
                    <h4 class="text-2xl font-medium mb-2">5-{{__('этап')}}</h4>
                    <p class="mb-8">{{__('Введите свой ID который вы скопировали и гапишите сумму.')}}</p>
                </div>
                <div class="lg:w-1/2 w-full lg:hidden block">
                    <img src="{{asset('images/icons/4.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                </div>
            </div>

            <div class="flex lg:flex-row flex-col mt-10 sm:w-4/5 w-full mx-auto">
                <div class="lg:w-1/2 w-full my-auto text-center">
                    <h4 class="text-2xl font-medium mb-2">6-{{__('этап')}}</h4>
                    <p>{{__('Подтвердите оплату.')}}</p>
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
                    <h4 class="text-2xl font-medium mb-2">{{__('Поздравляем')}}</h4>
                    <p>{{__('Вы пополнили свой счет.')}}</p>
                </div>

                <div class="lg:w-1/2 w-full text-center ml-4 mt-4 lg:hidden block">
                    <h4 class="text-2xl font-medium mb-2">{{__('Поздравляем')}}</h4>
                    <p class="mb-8">{{__('Вы пополнили свой счет.')}}</p>
                </div>
                <div class="lg:w-1/2 w-full lg:hidden block">
                    <img src="{{asset('images/icons/6.jpg')}}" class="mx-auto w-80 h-full border-2 rounded-lg border-gray-300"/>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
