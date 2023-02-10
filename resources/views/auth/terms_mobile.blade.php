@extends('layouts.app2')

@section('content')

    <div class="w-11/12 mx-auto my-12">
        {!! App\Services\CustomService::getContentText('terms', 'terms_text') !!}
    </div>

@endsection








