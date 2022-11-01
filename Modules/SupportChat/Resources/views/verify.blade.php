@extends('supportchat::layout.app')


@section('content')

    <div class="w-full mt-12">
        <form action="{{route('supportchat.verify.store',[$user])}}" method="POST" class="px-8 pt-6 pb-8 mb-4">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    {{__('Введите код подтверждения')}}
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       type="number" placeholder="123456" name="code">
                @error('code')
                    <p class="text-red-500"> {{$message}}</p>
                @enderror
                @if(!empty($incorrect_message))
                    <p class="text-red-500">{{ $incorrect_message }}</p>
                @endif
            </div>
            <div class="float-right">
                <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-xl" type="submit">
                    {{__('Отправить')}}
                </button>
            </div>
        </form>
    </div>

@endsection






