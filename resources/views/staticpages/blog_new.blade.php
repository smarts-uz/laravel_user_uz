@extends("layouts.app")

@section('content')

    <div class="sm:w-9/12 w-full container mx-auto my-12 sm:px-0 px-4">
       @foreach($news as $new)
           <h1 class="font-bold text-3xl">{{$new->getTranslatedAttribute('title',Session::get('lang') , 'fallbackLocale')}}</h1>
           <p class="mt-8">{{$new->getTranslatedAttribute('desc',Session::get('lang') , 'fallbackLocale')}}</p>
           <img class="mx-auto rounded-lg my-6 w-full" src="{{ asset('storage/'.$new->img) }}" alt="#">
           <p>{{$new->getTranslatedAttribute('text',Session::get('lang') , 'fallbackLocale')}}</p>
       @endforeach
    </div>

@endsection
