<div class="w-4/5 mx-auto bg-gradient-to-r from-white via-gray-100 to-white">
    <div class="container text-center mx-auto">
        <div class="text-4xl mx-auto py-10 md:py-16">
            {!! App\Services\CustomService::getContentText('home', 'economy_title') !!}
        </div>
        <div class="grid md:grid-cols-3 grid-col-1 w-full mx-auto">
            <div class="grid-cols-1 text-left ">
                {!! App\Services\CustomService::getContentText('home', 'economy_first') !!}
            </div>
            <div class="grid-cols-1 text-left md:my-0 my-3">
                {!! App\Services\CustomService::getContentText('home', 'economy_second') !!}
            </div>
            <div class="grid-cols-1 text-left">
                {!! App\Services\CustomService::getContentText('home', 'economy_third') !!}
            </div>
        </div>

        <div class="w-8/12 mx-auto -mt-16 lg:block hidden">
            <img src="{{ asset('/images/Vector.png') }}" alt="">
        </div>
    </div>
</div>
