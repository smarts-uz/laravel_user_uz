<div class="grid grid-cols-2 gap-4 my-20 w-4/5 mx-auto">
    <div class="lg:col-span-1 col-span-2 flex sm:flex-row flex-col m-3">
        <div class="w-1/3 sm:block hidden">
            <img class="w-full h-36 rounded-l-lg" src="{{ App\Services\CustomService::getContentImage('home', 'blogs_first') }}" alt="#">
        </div>
        {!! App\Services\CustomService::getContentText('home', 'blogs_first') !!}
    </div>
    <div class="lg:col-span-1 col-span-2 flex sm:flex-row flex-col m-3">
        <div class="w-1/3 sm:block hidden">
            <img  class="w-full h-36 rounded-l-lg" src="{{ App\Services\CustomService::getContentImage('home', 'blogs_second') }}" alt="#">
        </div>
        {!! App\Services\CustomService::getContentText('home', 'blogs_second') !!}
    </div>
    <div class="lg:col-span-1 col-span-2 flex sm:flex-row flex-col m-3">
        <div class="w-1/3 sm:block hidden">
            <img class="w-full h-36 rounded-l-lg" src="{{ App\Services\CustomService::getContentImage('home', 'blogs_third') }}" alt="#">
        </div>
        {!! App\Services\CustomService::getContentText('home', 'blogs_third') !!}
    </div>
    <div class="lg:col-span-1 col-span-2 flex sm:flex-row flex-col m-3">
        <div class="w-1/3 sm:block hidden">
            <img class="w-full h-36 rounded-l-lg" src="{{App\Services\CustomService::getContentImage('home', 'blogs_fourth')}}" alt="#">
        </div>
        {!! App\Services\CustomService::getContentText('home', 'blogs_fourth') !!}
    </div>
</div>
