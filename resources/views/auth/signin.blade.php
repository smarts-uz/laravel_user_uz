@extends('layouts.app')

@section('content')

        <div class="mt-3 text-center text-base">
            <div class="mx-auto flex items-center justify-center w-full">
                <h3 class="font-bold text-2xl block my-4 text-gray-700">
                   {{__('Вход в систему')}}
                </h3>
            </div>
            <div class="mt-4 flex flex-row justify-center">
                <a class="border-2 py-2 px-8 mx-2 rounded-lg bg-red-500" href="{{route('social.googleRedirect')}}">
                    <i class="fab fa-google text-2xl text-white"> </i>
                </a>
                <a class="border-2 py-2 px-4 mx-2 rounded-lg hidden" href="{{ route('one.auth') }}">ONE ID</a>
               <a class="border-2 py-2 px-8 mx-2 rounded-lg bg-blue-700" href="{{route('social.facebookRedirect')}}">
                   <i class="fab fa-facebook text-2xl text-white"></i>
               </a>
               <a class="border-2 py-2 px-8 mx-2 rounded-lg bg-black" href="{{route('social.appleRedirect')}}">
                   <i class="fab fa-apple text-2xl text-white"></i>
               </a>
            </div>
            <div class="mx-auto flex items-center justify-center w-full">
                <h3 class="font-bold text-2xl block mb-4 mt-4 text-gray-700">
                 {{__('Войти в профиль пользователя')}}
                </h3>
            </div>
            <div>

                <form method="POST" action="{{ route('login.loginPost') }}" class="flex flex-col justify-items-center justify-items-center">
                    @csrf
                    <div class="mb-4">
                        <input type="text" name="email" placeholder="{{__('Электронная почта или телефон')}}" id="name" value="{{  old('email') }}"
                               class="shadow focus:outline-none  focus:border-yellow-500 appearance-none border border-slate-300 rounded
                        sm:w-80 w-64 py-2 px-3 text-gray-700 mb-3 leading-tight hover:border-amber-500"
                               autofocus>

                        @error('email')
                            <p class="text-red-500"> {{$message}}</p>
                        @enderror

                    </div>

                    <div class="mb-6">
                        <input   type="password" maxlength="20" name="password" placeholder="{{__('Пароль')}}" id="password"
                                 class="ml-6 shadow focus:outline-none  focus:border-yellow-500 appearance-none border border-slate-300 rounded sm:w-80 w-64 py-2 px-3
                        text-gray-700 mb-3 leading-tight hover:border-amber-500">
                        <i class="fas fa-eye-slash text-gray-500 relative -left-10" id="eye"></i>
                    </div>
                    <div>
                        <button type="submit"
                                class="sm:w-80 w-64 h-12 rounded-lg bg-green-500 text-white uppercase
                        font-semibold hover:bg-green-500 transition mb-4">
                            {{__('Войти')}}
                        </button>
                    </div>

                </form>

                <p class="mb-4">
                    <a class="text-blue-500 hover:text-red-500" href="{{ route('user.reset') }}">
                    {{__('Забыли пароль?')}}
                    </a>
                </p>
                <p class="mb-4">
                   {{__('Еще не с нами?')}}
                    <a class="text-blue-500 hover:text-red-500" href="{{ route('user.signup') }}">
                    {{__('Зарегистрируйтесь')}}
                    </a>
                </p>
            </div>
        </div>

        <script src="{{ asset('js/auth/signin.js') }}"></script>
@endsection
