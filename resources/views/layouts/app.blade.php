<!DOCTYPE html>
<html lang="{{session('lang')}}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Universal services </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{$app_logo ?? ''}}"/>
    <link rel="stylesheet" href="{{asset('vendor/fontawesome-free/css/all.min.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
            integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="{{ asset('css/fonts/fonts.css') }}">
    <link href="https://releases.transloadit.com/uppy/v2.4.1/uppy.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.1.0/dist/cdn.min.js"></script>
    {{--JS Panel--}}
    <script src="https://cdn.jsdelivr.net/npm/jspanel4@4.14.1/dist/jspanel.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspanel4@4.14.1/dist/extensions/modal/jspanel.modal.js"></script>
    {{--JS Panel CSS--}}
    <link href="https://cdn.jsdelivr.net/npm/jspanel4@4.14.1/dist/jspanel.css" rel="stylesheet">
    @yield('style')

    <style>
        body {
            font-family: 'Montserrat', sans-serif !important;
        }
    </style>
    <style>
        [class*="copyrights-pane"]
        {display: none !important;}
    </style>
</head>
<body class=" text-xl">

@include('components.preloader')
<x-navbar/>
@yield('content')
<script>
    const createChatPanel = (event) => {
    jsPanel.create({
        content: '<iframe src="http://youdo.cc/chat" frameborder="0" style="width: 100%; height: 100%"></iframe>',
        theme: 'primary',
        position: 'center',
        resizeit: false,
        closeOnEscape: true,
        headerTitle: 'Интерактивный чат',
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
    const chatUI = document.querySelector('.open-chat');
    chatUI.addEventListener('click', createChatPanel);
</script>
<x-footer/>
@include('sweetalert::alert')

<x-modal></x-modal>

</body>

<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@yield("javasript")
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
