@extends('layouts.app')

@section('content')

    <div class="mt-3 text-center text-base">
        <div class="mx-auto flex items-center justify-center w-full">
            <h3 class="font-bold text-2xl block my-4 text-gray-700">
            {{__('Зарегистрироваться через:')}}
            </h3>
        </div>
        <div class="mt-4 flex flex-row justify-center mb-3">
            <a class="border-2 py-2 px-8 mx-2 rounded-lg" href="{{route('social.googleRedirect')}}">
                <svg id="Слой_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40" heith="40" viewBox="-380.2 274.7 65.7 65.8"><style>.st0{fill:#e0e0e0}.st1{fill:#fff}.st2{clip-path:url(#SVGID_2_);fill:#fbbc05}.st3{clip-path:url(#SVGID_4_);fill:#ea4335}.st4{clip-path:url(#SVGID_6_);fill:#34a853}.st5{clip-path:url(#SVGID_8_);fill:#4285f4}</style><g><defs><path id="SVGID_1_" d="M-326.3 303.3h-20.5v8.5h11.8c-1.1 5.4-5.7 8.5-11.8 8.5-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4c-3.9-3.4-8.9-5.5-14.5-5.5-12.2 0-22 9.8-22 22s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"></path></defs><clipPath id="SVGID_2_"><use xlink:href="#SVGID_1_" overflow="visible"></use></clipPath><path class="st2" d="M-370.8 320.3v-26l17 13z"></path><defs><path id="SVGID_3_" d="M-326.3 303.3h-20.5v8.5h11.8c-1.1 5.4-5.7 8.5-11.8 8.5-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4c-3.9-3.4-8.9-5.5-14.5-5.5-12.2 0-22 9.8-22 22s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"></path></defs><clipPath id="SVGID_4_"><use xlink:href="#SVGID_3_" overflow="visible"></use></clipPath><path class="st3" d="M-370.8 294.3l17 13 7-6.1 24-3.9v-14h-48z"></path><g><defs><path id="SVGID_5_" d="M-326.3 303.3h-20.5v8.5h11.8c-1.1 5.4-5.7 8.5-11.8 8.5-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4c-3.9-3.4-8.9-5.5-14.5-5.5-12.2 0-22 9.8-22 22s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"></path></defs><clipPath id="SVGID_6_"><use xlink:href="#SVGID_5_" overflow="visible"></use></clipPath><path class="st4" d="M-370.8 320.3l30-23 7.9 1 10.1-15v48h-48z"></path></g><g><defs><path id="SVGID_7_" d="M-326.3 303.3h-20.5v8.5h11.8c-1.1 5.4-5.7 8.5-11.8 8.5-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4c-3.9-3.4-8.9-5.5-14.5-5.5-12.2 0-22 9.8-22 22s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"></path></defs><clipPath id="SVGID_8_"><use xlink:href="#SVGID_7_" overflow="visible"></use></clipPath><path class="st5" d="M-322.8 331.3l-31-24-4-3 35-10z"></path></g></g>
                </svg></a>
{{--            <a class="border-2 py-2 px-4 rounded-lg mx-3 text-2xl font-bold bg-yellow-500 text-white my-auto hidden" href="{{ route('one.auth') }}">--}}
{{--                ONE ID--}}
{{--            </a>--}}
            <a class="border-2 py-2 px-8 mx-2 rounded-lg bg-blue-700" href="{{route("social.facebookRedirect")}}"> <i class="fab fa-facebook text-2xl text-white"></i></a>
            <a class="border-2 py-2 px-8 mx-2 rounded-lg bg-black" href="{{route('social.appleRedirect')}}">
                <i class="fab fa-apple text-2xl text-white"></i>
            </a>
        </div>
        <div class="mx-auto flex items-center justify-center w-full">
            <h3 class="font-bold text-2xl block mb-4 text-gray-700">
                {{__('Заполните форму')}}
            </h3>
        </div>
        <form action="{{ route('login.customRegister') }}" method="POST">
            @csrf
            <div>
                <div class="mb-4">
                    <div class="my-3">
                        <input type="text" name="name" placeholder= "{{__('Имя Фамилия')}}"
                               value="{{ request()->input('name', old('name')) }}"
                               id="name"
                               class="focus:outline-none focus:border-yellow-500 shadow appearance-none border border-slate-300 rounded sm:w-80 w-64 py-2 px-3 text-gray-700 leading-tight hover:border-amber-500">
                        @error('name')
                        <p class="text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="my-3">
                        <input type="text" name="email" placeholder="{{__('Электронная почта')}}"
                               value="{{ request()->input('email', old('email')) }}"
                               id="email_address"
                               class=" focus:outline-none focus:border-yellow-500 shadow appearance-none border border-slate-300 rounded sm:w-80 w-64 py-2 px-3 text-gray-700 leading-tight hover:border-amber-500">
                        @error('email')
                        <p class="text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="my-3">
                        <input type="text" name="phone_number" id="phone_number"  value="{{ request()->input('phone_number', old('phone_number')) }}"
                               class=" focus:outline-none focus:border-yellow-500 shadow appearance-none border border-slate-300 rounded sm:w-80 w-64 py-2 px-3 text-gray-700 leading-tight hover:border-amber-500">
                        <br>
                        @error('phone_number')
                            <p class="text-red-500">{{ $message }}</p>
                        @enderror
                    </div>



                    <div class="my-3">
                        <input type="password" name="password" placeholder="{{__('Пароль')}}"
                               id="password" maxlength="20" value="{{ request()->input('password', old('password')) }}"
                               class=" focus:outline-none focus:border-yellow-500 ml-6 shadow appearance-none border border-slate-300 rounded sm:w-80 w-64 py-2 px-3 text-gray-700 leading-tight hover:border-amber-500"
                               required>
                        <i class="fas fa-eye-slash text-gray-500 relative -left-10" id="eye"></i>
                    </div>
                    <div class="my-3">
                        <input type="password" name="password_confirmation"
                               placeholder="{{__(' Подтвердите пароль')}}"
                               id="password_confirmation" maxlength="20"
                               class="ml-6 focus:outline-none focus:border-yellow-500 shadow appearance-none border border-slate-300 rounded sm:w-80 w-64 py-2 px-3 text-gray-700 mb-3 leading-tight hover:border-amber-500"
                               required>
                        <i class="fas fa-eye-slash text-gray-500 relative -left-10" id="eye1"></i>

                        @error('password')
                            <p class="text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="w-full my-3">
                        <p> <input type="checkbox" name="" id="checkbox1" class="w-4 h-4 mr-2"> {!!__('Нажимая «Зарегистрироваться», <br> вы соглашаетесь с ')!!}
                            <a class="text-blue-600 hover:text-red-500 cursor-pointer" href="/terms">
                                {{__('Правилами сайта')}}
                            </a>
                        </p>
                    </div>

                    <button disabled type="button" id="btn11"
                            class="sm:w-80 w-64 h-12 rounded-lg bg-gray-500 text-white uppercase font-semibold  transition mb-4">
                        {{__('Зарегистрироваться')}}
                    </button>
                    <button type="submit" id="btn22"
                            class="hidden sm:w-80 w-64 h-12 rounded-lg bg-green-500 hover:bg-green-600 text-white uppercase font-semibold  transition mb-4">
                        {{__('Зарегистрироваться')}}
                    </button>
                </div>

            </div>

        </form>
    </div>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js'></script>
    <script src="{{ asset('js/auth/signup.js') }}"></script>
@endsection
