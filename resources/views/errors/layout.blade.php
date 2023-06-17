<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="/storage/{!!str_replace("\\","/",setting('site.image'))!!}"/>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.css" rel="stylesheet">
    <title>Universal Services</title>
</head>
<body>
<div class="container text-center py-12 mx-auto">
    <div class="w-4/5 mx-auto mt-24">
        <h1 class="text-8xl font-semibold mb-4 text-yellow-600">
            {{__('Ошибка')}} @yield('errors')
        </h1>
        <p class="text-gray-700 my-10 text-xl text-yellow-700">
            {{__('Пожалуйста, пришлите нам скриншот этой проблемы и как она возникла.')}}
        </p>
        <a href="/" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 tracking-wider uppercase text-sm mx-1">
            {{__('Вернуться на главную страницу')}}
        </a>
        <a href="https://t.me/UserUz_iBot" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 tracking-wider uppercase text-sm mx-1">
            {{__('Связаться с администраторами')}}
        </a>
    </div>
</div>
</body>
</html>
