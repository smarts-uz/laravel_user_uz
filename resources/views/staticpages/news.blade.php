@extends("layouts.app")

@section('content')

    <div class="w-11/12 container mx-auto my-12">
        <h1 class="text-center text-5xl font-semibold mx-6 pb-12">{{__('Новости сайта')}}</h1>
        @foreach ($news as $new)
            <div class="flex lg:flex-row  flex-col gap-x-4 my-10">
                <div class="lg:w-1/2 w-full">
                    <a href="/news/{{$new->id}}">
                        <img class="md:w-3/4 w-full mx-auto h-80 rounded-lg" src="{{ asset('storage/'.$new->img) }}" alt="#">
                    </a>
                </div>
                <div class="lg:w-1/2 w-full lg:mt-0 mt-4">
                    <a href="/news/{{$new->id}}" class="text-left text-3xl font-semibold hover:text-red-500">{{$new->getTranslatedAttribute('title',Session::get('lang') , 'fallbackLocale')}}</a>
                    <p class="text-left text-xl mt-3">{{$new->getTranslatedAttribute('desc',Session::get('lang') , 'fallbackLocale')}}</p>
                </div>
            </div>
        @endforeach
    </div>

@endsection
