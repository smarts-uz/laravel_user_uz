@extends("layouts.app")

@section('content')

    <div class="w-11/12 container mx-auto my-12">
        <h1 class="text-center text-5xl font-semibold mx-6">{{__('Новости сайта')}}</h1>
        @foreach ($news as $new)
            <div class="flex lg:flex-row  flex-col gap-x-2 my-6">
                <div class="lg:w-1/2 w-full">
                    <img class="w-full h-full rounded-lg" src="{{ asset('storage/'.$new->img) }}" alt="#">
                </div>
                <div class="lg:w-1/2 w-full">
                    <h1 class="text-left text-3xl">{{$new->getTranslatedAttribute('title',Session::get('lang') , 'fallbackLocale')}}</h1>
                    <p class="text-left text-xl mt-3">{{$new->getTranslatedAttribute('text',Session::get('lang') , 'fallbackLocale')}}</p>
                </div>  
            </div>
        @endforeach
    </div>

@endsection