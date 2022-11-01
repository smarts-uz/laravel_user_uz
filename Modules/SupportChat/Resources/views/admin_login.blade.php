@extends('supportchat::layout.app')

@section('content')

    <div class="flex items-center justify-center min-h-screen bg-no-repeat bg-cover" style="background-image: url('{{asset('images/auth-light.jpg')}}')">
        <div class="w-full sm:w-2/3 xl:w-1/3 mx-auto px-4 py-6 mt-4 text-left bg-white shadow-lg rounded-lg">
            <div class="flex justify-center">
                <img src="/storage/{!!str_replace("\\","/",setting('chat.image_logo'))!!}" width="150" class="mt-2">
            </div>
            <form action="{{route('admin.login.store')}}" method="post">
                @csrf
                <div class="mt-4">
                    <div>
                        <label class="block">{{ __('Имя администратора') }}</label>
                        <input type="text" placeholder="{{ __('Имя администратора') }}" name="name" required
                               class="w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600">
                    </div>
                    <div class="mt-4">
                        <label class="block">{{ __('Пароль администратора') }}</label>
                        <input type="password" placeholder="{{ __('Пароль администратора') }}" name="password" required
                               class="w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600">
                    </div>
                    <div class="flex items-baseline justify-between mx-auto">
                        <button class="px-6 py-2 mt-4 text-white bg-blue-600 rounded-lg hover:bg-blue-900">{{ __('ВОЙТИ') }}</button>
                    </div>
                    @if(!empty($password_incorrect))
                        <p class="text-red-500">{{ $password_incorrect }}</p>
                    @endif
                </div>
            </form>
        </div>
    </div>




@endsection
