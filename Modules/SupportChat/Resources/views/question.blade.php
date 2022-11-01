@extends('supportchat::layout.app')

@section('content')
    <div class="w-3/4 mx-auto flex flex-col mt-12 ">
        @foreach($questions as $question)
            <a href="{{route('supportchat.login')}}" class="my-3 text-center inline-block px-6 py-2.5 bg-green-500 text-white font-medium text-sm leading-tight uppercase rounded-full shadow-md hover:bg-green-600 hover:shadow-lg transition duration-150 ease-in-out">
                {{$question->getTranslatedAttribute('text')}}
            </a>
        @endforeach
    </div>
@endsection






