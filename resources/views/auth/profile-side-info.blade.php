<div class="lg:col-span-1 col-span-2 rounded-xl ring-1 ring-gray-300 h-auto text-gray-600 sm:ml-8 ml-0">
    @if(auth()->user()->role_id !== \App\Models\User::ROLE_PERFORMER)
            <a href="/verification" class="flex flex-row shadow-lg rounded-lg mb-8">
                <div class="w-1/2 h-24 bg-contain bg-no-repeat bg-center" style="background-image: url({{asset('images/like.png')}});">
                </div>
                <div class="font-bold text-xs text-gray-700 text-left my-auto">
                    {!!__('Станьте исполнителем <br> USer.Uz. И начните  <br> зарабатывать')!!}
                </div>
            </a>
        @endif
    <div class="mt-6 ml-4">
        @if (auth()->user()->role_id === \App\Models\User::ROLE_PERFORMER)
            <h3 class="font-medium text-gray-700 text-3xl">
                {{__('Исполнитель')}}
            </h3>
        @endif
    </div>
    <div class="contacts">
        <div class="ml-4 h-20 grid grid-cols-4 content-center">
            <div class="w-12 h-12 text-center mx-auto my-auto py-2 bg-gray-300 rounded-xl col-span-1"
                style="background-color: orange;">
                <i class="fas fa-phone-alt text-white text-2xl"></i>
            </div>
            <div class="ml-3 col-span-3">
                <h5 class="font-bold text-gray-700 block">{{__('Телефон')}}</h5>
                @if ($user->phone_number != '')
                    <p class="text-gray-600 block ">{{ correctPhoneNumber($user->phone_number) }}</p>
                @else
                    {{__('нет номера')}}
                @endif
            </div>
        </div>
        <div class="telefon ml-4 h-20 grid grid-cols-4 content-center">
            <div class="w-12 h-12 text-center mx-auto my-auto py-2 bg-gray-300 rounded-xl col-span-1"
                style="background-color: #0091E6;">
                <i class="far fa-envelope text-white text-2xl"></i>
            </div>
            <div class="ml-3 col-span-3">
                <h5 class="font-bold text-gray-700 block">Email</h5>
                <p class="text-sm break-all">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    <div class="contacts">
        @if($user->google_id)

            <div class="telefon ml-4 h-20 grid grid-cols-4">
                <div class="w-12 h-12 text-center mx-auto my-auto py-2 bg-gray-300 rounded-xl col-span-1">
                    <svg id="Слой_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40" heith="40" viewBox="-380.2 274.7 65.7 65.8"><style>.st0{fill:#e0e0e0}.st1{fill:#fff}.st2{clip-path:url(#SVGID_2_);fill:#fbbc05}.st3{clip-path:url(#SVGID_4_);fill:#ea4335}.st4{clip-path:url(#SVGID_6_);fill:#34a853}.st5{clip-path:url(#SVGID_8_);fill:#4285f4}</style><g><defs><path id="SVGID_1_" d="M-326.3 303.3h-20.5v8.5h11.8c-1.1 5.4-5.7 8.5-11.8 8.5-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4c-3.9-3.4-8.9-5.5-14.5-5.5-12.2 0-22 9.8-22 22s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"></path></defs><clipPath id="SVGID_2_"><use xlink:href="#SVGID_1_" overflow="visible"></use></clipPath><path class="st2" d="M-370.8 320.3v-26l17 13z"></path><defs><path id="SVGID_3_" d="M-326.3 303.3h-20.5v8.5h11.8c-1.1 5.4-5.7 8.5-11.8 8.5-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4c-3.9-3.4-8.9-5.5-14.5-5.5-12.2 0-22 9.8-22 22s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"></path></defs><clipPath id="SVGID_4_"><use xlink:href="#SVGID_3_" overflow="visible"></use></clipPath><path class="st3" d="M-370.8 294.3l17 13 7-6.1 24-3.9v-14h-48z"></path><g><defs><path id="SVGID_5_" d="M-326.3 303.3h-20.5v8.5h11.8c-1.1 5.4-5.7 8.5-11.8 8.5-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4c-3.9-3.4-8.9-5.5-14.5-5.5-12.2 0-22 9.8-22 22s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"></path></defs><clipPath id="SVGID_6_"><use xlink:href="#SVGID_5_" overflow="visible"></use></clipPath><path class="st4" d="M-370.8 320.3l30-23 7.9 1 10.1-15v48h-48z"></path></g><g><defs><path id="SVGID_7_" d="M-326.3 303.3h-20.5v8.5h11.8c-1.1 5.4-5.7 8.5-11.8 8.5-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4c-3.9-3.4-8.9-5.5-14.5-5.5-12.2 0-22 9.8-22 22s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"></path></defs><clipPath id="SVGID_8_"><use xlink:href="#SVGID_7_" overflow="visible"></use></clipPath><path class="st5" d="M-322.8 331.3l-31-24-4-3 35-10z"></path></g></g>
                    </svg>
                </div>
                <div class="ml-3 col-span-3">
                    <h5 class="font-bold text-gray-700 block mt-2 text-md">Google</h5>
                    {{__('Подтвержден')}}
                </div>
            </div>
        @endif
        @if($user->facebook_id)
            <div class="telefon ml-4 h-20 grid grid-cols-4">
                <div class="w-12 h-12 text-center mx-auto my-auto py-2 bg-gray-300 rounded-xl col-span-1"
                     style="background-color: #4285F4;">
                    <i class="fab fa-facebook-f text-white text-2xl"></i>
                </div>
                <div class="ml-3 col-span-3">
                    <h5 class="font-bold text-gray-700 block mt-2 text-md">Facebook</h5>
                    <p>{{__('Подтвержден')}}</p>
                </div>
            </div>
        @endif
        @if($user->facebook_id)
            <div class="telefon ml-4 h-20 grid grid-cols-4">
                <div class="w-12 h-12 text-center mx-auto my-auto py-2 bg-gray-300 rounded-xl col-span-1"
                     style="background-color: #000000;">
                    <i class="fab fa-apple text-white text-2xl"></i>
                </div>
                <div class="ml-3 col-span-3">
                    <h5 class="font-bold text-gray-700 block mt-2 text-md">Apple</h5>
                    <p>{{__('Подтвержден')}}</p>
                </div>
            </div>
        @endif
    </div>
    <p class="mx-5 my-4">
        {!! getContentText('profile', 'profile_text') !!}
    </p>
    @if(!$user->google_id)

        <div class="telefon ml-4 h-20 grid grid-cols-4">
            <div class="w-12 h-12 text-center mx-auto my-auto py-2 bg-gray-300 rounded-xl col-span-1">
                <svg id="Слой_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40" heith="40" viewBox="-380.2 274.7 65.7 65.8"><style>.st0{fill:#e0e0e0}.st1{fill:#fff}.st2{clip-path:url(#SVGID_2_);fill:#fbbc05}.st3{clip-path:url(#SVGID_4_);fill:#ea4335}.st4{clip-path:url(#SVGID_6_);fill:#34a853}.st5{clip-path:url(#SVGID_8_);fill:#4285f4}</style><g><defs><path id="SVGID_1_" d="M-326.3 303.3h-20.5v8.5h11.8c-1.1 5.4-5.7 8.5-11.8 8.5-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4c-3.9-3.4-8.9-5.5-14.5-5.5-12.2 0-22 9.8-22 22s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"></path></defs><clipPath id="SVGID_2_"><use xlink:href="#SVGID_1_" overflow="visible"></use></clipPath><path class="st2" d="M-370.8 320.3v-26l17 13z"></path><defs><path id="SVGID_3_" d="M-326.3 303.3h-20.5v8.5h11.8c-1.1 5.4-5.7 8.5-11.8 8.5-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4c-3.9-3.4-8.9-5.5-14.5-5.5-12.2 0-22 9.8-22 22s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"></path></defs><clipPath id="SVGID_4_"><use xlink:href="#SVGID_3_" overflow="visible"></use></clipPath><path class="st3" d="M-370.8 294.3l17 13 7-6.1 24-3.9v-14h-48z"></path><g><defs><path id="SVGID_5_" d="M-326.3 303.3h-20.5v8.5h11.8c-1.1 5.4-5.7 8.5-11.8 8.5-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4c-3.9-3.4-8.9-5.5-14.5-5.5-12.2 0-22 9.8-22 22s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"></path></defs><clipPath id="SVGID_6_"><use xlink:href="#SVGID_5_" overflow="visible"></use></clipPath><path class="st4" d="M-370.8 320.3l30-23 7.9 1 10.1-15v48h-48z"></path></g><g><defs><path id="SVGID_7_" d="M-326.3 303.3h-20.5v8.5h11.8c-1.1 5.4-5.7 8.5-11.8 8.5-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6.4-6.4c-3.9-3.4-8.9-5.5-14.5-5.5-12.2 0-22 9.8-22 22s9.8 22 22 22c11 0 21-8 21-22 0-1.3-.2-2.7-.5-4z"></path></defs><clipPath id="SVGID_8_"><use xlink:href="#SVGID_7_" overflow="visible"></use></clipPath><path class="st5" d="M-322.8 331.3l-31-24-4-3 35-10z"></path></g></g>
                </svg>
            </div>
            <div class="ml-3 col-span-3">
                <h5 class="font-bold text-gray-700 block mt-4 text-md">Google</h5>
                <a href="{{route('social.googleRedirect')}}" target="_blank"
                   class="block text-sm text-blue-600">
                {{__('Привязать')}}
                </a>
            </div>
        </div>
    @endif
    @if(!$user->facebook_id)
        <div class="telefon ml-4 h-20 grid grid-cols-4">
            <div class="w-12 h-12 text-center mx-auto my-auto py-2 bg-gray-300 rounded-xl col-span-1"
                 style="background-color: #4285F4;">
                <i class="fab fa-facebook-f text-white text-2xl"></i>
            </div>
            <div class="ml-3 col-span-3">
                <h5 class="font-bold text-gray-700 block mt-4 text-md">Facebook</h5>
                <a href="{{route('social.facebookRedirect')}}" target="_blank"
                   class="block text-sm text-blue-600">
                {{__('Привязать')}}
                </a>
            </div>
        </div>
    @endif
    @if(!$user->apple_id)
        <div class="telefon ml-4 h-20 grid grid-cols-4">
            <div class="w-12 h-12 text-center mx-auto my-auto py-2 bg-gray-300 rounded-xl col-span-1"
                 style="background-color: #000000;">
                <i class="fab fa-apple text-white text-2xl"></i>
            </div>
            <div class="ml-3 col-span-3">
                <h5 class="font-bold text-gray-700 block mt-4 text-md">Apple Id</h5>
                <a href="{{route('social.appleRedirect')}}" target="_blank"
                   class="block text-sm text-blue-600">
                {{__('Привязать')}}
                </a>
            </div>
        </div>
    @endif

</div>
