<!DOCTYPE html>
<html lang="{{Lang::locale()}}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Universal services </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/storage/{!!str_replace("\\","/",setting('site.image'))!!}"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/fonts/fonts.css') }}">
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.10.5/cdn.min.js"></script>
    {{--JS Panel--}}
    <script src="https://cdn.jsdelivr.net/npm/jspanel4@4.14.1/dist/jspanel.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspanel4@4.14.1/dist/extensions/modal/jspanel.modal.js"></script>
    {{--JS Panel CSS--}}
    <link href="https://cdn.jsdelivr.net/npm/jspanel4@4.14.1/dist/jspanel.css" rel="stylesheet">
    @yield('style')
    <link href='https://fonts.googleapis.com/css?family=Outfit' rel='stylesheet'>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
    <style>
        [class*="copyrights-pane"]
        {display: none !important;}
    </style>
</head>
<body class=" text-xl">

@include('components.preloader')
<x-navbar></x-navbar>
@yield('content')
<x-footer></x-footer>
@include('sweetalert::alert')

<x-modal></x-modal>

</body>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation-cjs@1.0.0/dist/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables-responsive/2.4.0/dataTables.responsive.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stack("javascript")
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
</html>
