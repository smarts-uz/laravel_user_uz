@extends("layouts.app")

@section('content')

    <div class="m-3">

        @foreach ($policies as $policy)
            <h4 class="font-semibold m-3">{{$policy->title}}</h4>
            <p class="text-base">{{$policy->text}}</p>
        @endforeach
    </div>

@endsection
