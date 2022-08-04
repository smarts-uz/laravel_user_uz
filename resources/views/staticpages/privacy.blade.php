@extends("layouts.app")

@section('content')

    <div class="m-3">

        @foreach ($policies as $polic)
            <h4 class="font-semibold m-3">{{$polic->title}}</h4>
            <p class="text-base">{{$polic->text}}</p>
        @endforeach
    </div>

@endsection
