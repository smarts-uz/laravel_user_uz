@extends('layouts.app')

@section('content')

    <div class="w-9/12 mx-auto my-12">
        {!! App\Services\CustomService::getContentText('terms', 'terms_text') !!}
    </div>

@endsection
