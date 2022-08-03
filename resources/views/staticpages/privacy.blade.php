@extends("layouts.app")

@section('content')

    <div class="m-5 ">

        @foreach ($policies as $policy)
            <h4 class="font-semibold mb-1">{{$policy->title}}</h4>
            <p>{{$policy->text}}</p>
        @endforeach
    </div>

@endsection
