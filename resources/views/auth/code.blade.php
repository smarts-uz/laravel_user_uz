@extends('layouts.app')
@section('content')
    <div class="mt-3 text-center text-base">
        <div class="mx-auto items-center justify-center w-full">
            <h3 class="font-bold text-3xl block mt-8 mb-4">
                {{__('Восстановление пароля')}}
            </h3>
        </div>
        <div class="mx-auto flex items-center justify-center w-full">
        </div>
        <form action="{{route('user.reset_code')}}" method="POST">
            @csrf
            <div>
                <div class="mb-4">
                    <label class="block text-gray-500 text-sm mb-1" for="phone_number">
                        {{__('Введите смс-код')}}
                    </label>
                    <input type="text" onfocus="onfocus" onkeypress='validate(event)'
                           name="code"
                           class="shadow focus:outline-none focus:border-yellow-500 appearance-none border border-slate-300 rounded w-80 py-2 px-3 text-gray-700 mb-3 leading-tight ">
                    <br>
                    @error('code')
                        <span class="text-red-500" >{{ $message  }}</span>
                    @enderror
                    @if(session()->has('error'))
                        <span class="text-red-500">{{ session('error')  }}</span>
                    @endif
                </div>
            </div>
            <button type="submit"
                    class="w-80 h-12 rounded-lg bg-green-500 text-gray-200 uppercase font-semibold hover:bg-green-500 text-gray-100 transition mb-4">
                {{__('Отправить')}}
            </button>
        </form>
    </div>
@endsection


