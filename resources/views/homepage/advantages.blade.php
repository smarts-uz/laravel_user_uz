<div class="w-4/5 mx-auto grid lg:grid-cols-2 grid-cols-1 mt-12">
    <div class="grid-col-1 w-11/12">
        <img src="{{ asset('/images/advantages.png') }}" alt="">
    </div>
    <div class="grid-col-1 -ml-8">
        <div class="text-2xl font-bold mx-auto py-6 text-center">
            {!! getContentText('home', 'advantages_title') !!}
        </div>
        <div class="lg:w-full w-4/5 mx-auto">
            {!! getContentText('home', 'advantages_first') !!}
        </div>
        <hr>
        <div class="lg:w-full w-4/5 mx-auto flex flex-wrap">
            {!! getContentText('home', 'advantages_two') !!}
        </div>
    </div>
</div>

