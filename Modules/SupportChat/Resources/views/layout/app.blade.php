<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <title>Universal services chat</title>
</head>
<body>
    <ul class="list-none flex flex-row m-2 absolute right-2 top-1">
        @if(session('lang') === 'ru')
            <li class="mx-1">
                <a href="{{route('lang', ['lang'=>'uz'])}}" class="btn btn-link">UZ</a>
            </li>
            <li class="mx-1">
                <a href="{{route('lang', ['lang'=>'ru'])}}" class="btn btn-link text-red-500"> RU</a>
            </li>
        @else
            <li class="mx-1">
                <a href="{{route('lang', ['lang'=>'uz'])}}" class="btn btn-link text-red-500">UZ</a>
            </li>
            <li class="mx-1">
                <a href="{{route('lang', ['lang'=>'ru'])}}" class="btn btn-link">RU</a>
            </li>
        @endif
    </ul>
@yield('content')


</body>
</html>
