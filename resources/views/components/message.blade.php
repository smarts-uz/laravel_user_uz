<link rel="stylesheet" href="{{asset('css/app.css')}}">
<!--  CHAT  -->
<div class="fabs">
    <div class="chat">
        <div class='bg-green-600 rounded-t-lg px-4 py-2 flex flex-row items-center'>
            <div class='w-16 m-1 rounded-full bg-white p-2'><img src='/storage/{!!str_replace("\\","/",setting('site.image'))!!}'/></div>
            <div class="ml-6 text-white">
                <span class="text-lg">Universal Services</span><br>
                <span class="text-xs">{{__('Cвяжитесь с нами')}}</span>
            </div>
        </div>
        <div class='start-chat'>
            <div class="flex z-10 overflow-y-auto h-96 bg-gray-50">
                <iframe src="htt" frameborder="0" width="100%"></iframe>
            </div>
        </div>
    </div>

<script>
    $('#prime').click(function() {
        $('.chat').toggleClass('is-visible');
        $('.fab').toggleClass('is-visible');
    });
</script>
