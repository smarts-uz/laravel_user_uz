<link rel="stylesheet" href="{{asset('css/app.css')}}">
<link rel="stylesheet" href="https://zavoloklom.github.io/material-design-iconic-font/css/docs.md-iconic-font.min.css">
<!--  CHAT  -->
<div class="fabs">
    <div class="chat">
        <div class='bg-green-600 rounded-t-lg px-4 py-2 flex flex-row items-center'>
            <div class='w-16 m-1 rounded-full bg-white p-2'><img src='{{asset('images/logo_image.png')}}'/></div>
            <div class="ml-6 text-white">
                <span class="text-lg">Universal Services</span><br>
                <span class="text-xs">{{__('Cвяжитесь с нами')}}</span>
            </div>
        </div>
        <div class='start-chat'>
            <div class="flex z-10 overflow-y-auto h-96 bg-gray-50">
                <iframe src="{{setting('site.support_chat_url')}}" frameborder="0" width="100%"></iframe>
            </div>
        </div>
    </div>

    <a id="prime" class="fab_prime bg-green-500"><i class="prime zmdi zmdi-comments text-white"></i></a>
</div>

<script>
    $('#prime').click(function() {
        toggleFab();
    });

    function toggleFab() {
        $('.prime').toggleClass('zmdi-comments');
        $('.prime').toggleClass('zmdi-close');
        $('.prime').toggleClass('is-active');
        $('.chat').toggleClass('is-visible');
        $('.fab').toggleClass('is-visible');
    }
</script>
