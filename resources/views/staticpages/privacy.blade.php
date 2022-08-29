@extends("layouts.app")

@section('content')

    <div class="w-4/5 mx-auto mx-3 mt-16">

        @foreach ($policies as $polic)
            <div class="my-4">
                <h4 class="font-semibold my-3">
                    {{$polic->title}}
                </h4>
                <p class="text-base">
                    {{$polic->text}}
                </p>
            </div>
        @endforeach
    </div>

@endsection
