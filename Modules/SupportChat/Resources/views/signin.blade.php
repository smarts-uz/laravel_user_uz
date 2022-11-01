@extends('supportchat::layout.app')


@section('content')

    <div class="w-full mt-12">
        <form action="{{route('supportchat.login.store')}}" class="px-8 pt-6 pb-8 mb-4" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    {{__('Введите ваше имя')}}
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       value="{{old('name')}}" required type="text" name="name">
                @error('name')
                    <p class="text-red-500"> {{$message}}</p>
                @enderror
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    {{__('Номер телефона')}}
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                       value="{{old('phone_number')}}"
                       id="phone_number" name="phone_number" type="text" required>
                @error('phone_number')
                    <p class="text-red-500"> {{$message}}</p>
                @enderror
                @if(!empty($expired_message))
                    <p class="text-red-500">{{ $expired_message }}</p>
                @endif
            </div>
            <div class="float-right">
                <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-xl" type="submit">
                    {{__('Отправить')}}
                </button>
            </div>
        </form>
    </div>

    <script src='https://unpkg.com/imask'></script>
    <script>
        var element = document.getElementById('phone_number');
        var maskOptions = {
            mask: '+998(00)000-00-00',
            lazy: false
        }
        var mask = new IMask(element, maskOptions);
    </script>


@endsection






