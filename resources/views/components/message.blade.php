<link rel="stylesheet" href="{{asset('css/app.css')}}">

<div class="blantershow-chat fixed bottom-10 right-10 chat">
    <div class="p-3 bg-green-700 rounded-xl cursor-pointer">
        <i class="far fa-comments text-4xl text-white"></i>
    </div>
</div>

<div id='whatsapp-chat' class='hide'>
    <div class='header-chat'>
        <div class='head-home'>
            <div class='info-avatar'><img src='{{asset('images/1.png')}}'/></div>
            <p><span class="whatsapp-name">Universal Services</span><br><small>{{__('Cвяжитесь с нами')}}</small></p>
        </div>
    </div>
    <div class='start-chat'>
        <div class="flex z-10 overflow-y-auto h-96 bg-gray-50">
            <iframe src="{{setting('site.support_chat_url')}}" frameborder="0" width="100%"></iframe>
        </div>
    </div>
    <div id='get-number'></div><a class='close-chat cursor-pointer'>×</a>
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
