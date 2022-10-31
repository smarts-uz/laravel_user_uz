<link rel="stylesheet" href="{{asset('css/app.css')}}">
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
                <iframe src="/supportchat" frameborder="0" width="100%"></iframe>
            </div>
        </div>
    </div>

    <a id="prime" class="fab_prime bg-green-500 w-16 h-16 rounded-full">
        <i class="prime fas fa-comment text-white text-4xl relative top-3 left-3"></i>
    </a>
</div>

<script>
    $('#prime').click(function() {
        toggleFab();
    });

    function toggleFab() {
        $('.prime').toggleClass('fa-comment');
        $('.prime').toggleClass('fa-times left-5');
        $('.prime').toggleClass('is-active');
        $('.chat').toggleClass('is-visible');
        $('.fab').toggleClass('is-visible');
    }
</script>
