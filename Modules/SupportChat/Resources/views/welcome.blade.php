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
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <title>Universal services chat</title>
</head>
<body>

    <div class="mx-auto text-center mt-12">
        @if(Auth::check() && auth()->user()->role_id === \App\Models\User::ROLE_ADMIN)
            <a href="/chatify">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 border border-blue-700 rounded">
                    {{__('Войти в чат администратора')}}
                </button>
            </a>
        @else
            <a href="/admin_login">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 border border-blue-700 rounded">
                    {{__('Войти в чат администратора')}}
                </button>
            </a>
        @endif
    </div>

<div id='whatsapp-chat' class='hide'>
    <div class='header-chat'>
        <div class='head-home'>
            <div class='info-avatar'><img src='{{asset('image/1.png')}}'/></div>
            <p><span class="whatsapp-name">Universal Services</span><br><small>{{__('Cвяжитесь с нами')}}</small></p>
        </div>
    </div>
    <div class='start-chat'>
        <div class="flex z-10 h-96">
            <iframe src="/question" frameborder="0" width="100%"></iframe>
        </div>
    </div>
    <div id='get-number'></div><a class='close-chat cursor-pointer'>×</a>
</div>

<div class="blantershow-chat fixed bottom-10 right-10 chat">
    <div class="p-3 bg-green-700 rounded-xl cursor-pointer">
        <i class="far fa-comments text-4xl text-white"></i>
    </div>
</div>

<script>
    $(document).on("click", ".close-chat", function() {
        $("#whatsapp-chat")
            .addClass("hide")
            .removeClass("show");
    }),
    $(document).on("click", ".blantershow-chat", function() {
        $("#whatsapp-chat")
            .addClass("show")
            .removeClass("hide");
    });
</script>

</body>
</html>
