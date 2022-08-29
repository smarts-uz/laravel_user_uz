@extends("layouts.app")

@section('content')

    <div class="w-4/5 mx-auto mx-3 mt-16">

        @foreach ($policies as $polic)
            <div class="my-4">
                <h4 class="font-semibold my-3">
                    {{$polic->getTranslatedAttribute('title',Session::get('lang') , 'fallbackLocale')}}
                </h4>
                <p class="text-base">
                    {{$polic->getTranslatedAttribute('text',Session::get('lang') , 'fallbackLocale')}}
                </p>
            </div>
        @endforeach
    </div>

@endsection
