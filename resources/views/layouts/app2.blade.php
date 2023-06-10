<!doctype html>
<html lang="{{Lang::locale()}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Universal services </title>
    <link rel="icon" type="image/png" href="/storage/{!!str_replace("\\","/",setting('site.image'))!!}"/>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/fonts/fonts.css') }}">
</head>
<body>

@include('components.preloader')
@yield('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function ($) {
        var Body = $('body');
        Body.addClass('preloader-site');
        $('#st-cmp-v2').addClass('hidden');
        $('.sharethisbutton').click(function () {
            $('.st-logo').addClass('hidden');
            $('.st-close').attr('style', 'position:fixed !important;top: 20px !important');
            $('.st-disclaimer').addClass('hidden');
        });
    });
    window.addEventListener('load', function () {
        $('.preloader-wrapper').fadeOut();
        var Body = $('body');
        Body.removeClass('preloader-site');
        $('#st-cmp-v2').addClass('hidden');
    })
</script>
</body>
</html>
