<nav class="relative w-full lg:w-autopy-4 flex justify-start items-center bg-white md:ml-4">
    <div class="lg:hidden">
        <button class="navbar-burger flex items-center text-yellow-500 p-3">
            <svg class="block h-4 w-4 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <title>Mobile menu</title>

                <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"></path>
            </svg>
        </button>
    </div>

    <div class="flex justify-center xl:w-full w-10/12">
        <a class="logo cursor-pointer delete-task" href="/">
            <img src="/storage/{!!str_replace("\\","/",setting('site.logo'))!!}" class="overflow-hidden h-14 xl:h-16 lg:h-14 py-2" alt=""/>
        </a>
    </div>
    @if (Route::has('login'))
        @auth
            <div class="w-2/12 flex justify-center lg:hidden mr-2">
                {{-- icon-1 --}}
                <div class=" float-left">
                    <div class="w-4 h-4 absolute rounded-full bg-red-500 ml-3 text-white text-center">1</div>
                    <button class="" type="button" data-dropdown-toggle="notification">
                        <i class="text-xl text-gray-500 hover:text-yellow-500 far fa-bell"></i>
                    </button>
                    <!-- Dropdown menu -->
                    <div class="hidden bg-white text-base z-50 list-none divide-y divide-gray-100 rounded shadow my-4" id="notification">
                        <div class="px-4 py-3">
                            <span class="block text-base font-bold">{{__('Уведомления')}}</span>
                        </div>
                        <ul class="py-1" aria-labelledby="notification">
                            <li>
                                <a href="/profile/settings"
                                   class="delete-task text-sm hover:bg-gray-100 text-gray-700 block px-4 py-2">{{__("В раздел 'Настройки'")}}</a>
                            </li>
                            <li>
                                <a class="bg-slate-100 text-sm italic text-green-600 hover:text-red-600 underline decoration-dotted  block px-4 py-2">{{__('Отметить все как прочитанное')}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="ml-4">
                    <a href="/chat">
                        <i class="text-xl text-gray-500 hover:text-yellow-500 far fa-comment-alt"></i>
                    </a>
                </div>
            </div>
            <!-- </div> -->
            <script src="https://unpkg.com/@themesberg/flowbite@latest/dist/flowbite.bundle.js"></script>
        @endauth
    @endif
</nav>

<div class="navbar-menu relative z-50 hidden">
    <div class="navbar-backdrop fixed inset-0 bg-gray-800 opacity-25"></div>
    <nav class="fixed top-0 left-0 bottom-0 flex flex-col w-5/6 max-w-sm py-6 px-6 bg-white border-r overflow-y-auto">
        <div class="flex items-center mb-8">
            <p class="mr-auto text-3xl font-bold leading-none" href="#">
                <svg class="h-12" alt="logo" viewBox="0 0 10240 10240">
                </svg>
            </p>
            <button class="navbar-close">
                <svg class="h-6 w-6 text-gray-400 cursor-pointer hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div>
            <ul>
                @if (Route::has('login'))
                    @auth
                        <li class="mb-1">
                            {{-- icon-2 --}}
                            <div class="max-w-lg mx-auto ml-6">
                                <a href="/profile" class="delete-task cursor-pointer profiles">
                                    <button class="" type="button" data-dropdown-toggle="dropdownuser"><i
                                            class="text-2xl text-gray-500 hover:text-yellow-500  far fa-user"></i>
                                    </button>
                                </a>
                            </div>
                        </li>
                    @endauth
                @endif
                <li class="mb-1">
                    <a href="/categories/1" class="delete-task block p-4 text-base rounded hover:text-yellow-500">{{__('Создать задание')}}</a>
                </li>
                <li class="mb-1">
                    <a href="{{ route('searchTask.task_search') }}"
                       class="task block delete-task cursor-pointer p-4 text-base rounded hover:text-yellow-500">{{__('Найти задания')}}</a>
                </li>
                <li class="mb-1">
                    <a href="/performers" class="performer delete-task cursor-pointer block p-4 text-base rounded hover:text-yellow-500">{{__('Исполнители')}}</a>
                </li>

                @if (Route::has('login'))
                    @auth

                        <li class="mb-1">
                            <a href="{{ route('searchTask.mytasks') }}"
                               class="mytask delete-task cursor-pointer block p-4 text-base rounded text-gray-500 hover:text-yellow-500">{{__('Мои заказы')}}</a>
                        </li>

                        {{-- icon-3 --}}

                        <li class="">
                            <div class="float-left mr-6">
                                <a onclick="toggleModal()">
                                    <svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-6 HeaderBalance_icon__2FeBY">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M19 3.874c0-.953-.382-1.8-1.086-2.334-.7-.531-1.607-.667-2.488-.423h-.003L4.132 4.279a.973.973 0 00-.028.008c-1.127.35-1.986 1.287-2.093 2.563C2.004 6.9 2 6.95 2 7v11.344C2 20.334 3.608 22 5.607 22h12.785c2 0 3.608-1.666 3.608-3.657v-6.686c0-1.785-1.292-3.309-3-3.605V3.874zM4 18.343C4 19.265 4.748 20 5.607 20h12.785c.86 0 1.608-.735 1.608-1.657V16.25h-2a1.25 1.25 0 010-2.5h2v-2.093c0-.923-.748-1.657-1.608-1.657H4v8.343zM4 7.12c0 .507.41.88.813.88H17V3.874c0-.413-.153-.633-.294-.74-.145-.11-.391-.188-.746-.09h-.001L4.686 6.2c-.435.14-.686.46-.686.92z"
                                              fill="#5AB82E"></path>
                                    </svg>
                                </a>
                            </div>
                            <!-- language blog -->
                            <div class="text-gray-500 mt-2">
                                <div class="flex">
                                    <a href="{{route('lang', ['lang'=>'uz'])}}" class="hover:text-red-500 mr-2">
                                        UZ
                                    </a>
                                    I
                                    <a href="{{route('lang', ['lang'=>'ru'])}}" class="hover:text-red-500 ml-2">
                                        RU
                                    </a>
                                </div>
                            </div>
                        </li>

                        <div class="hover:text-yellow-500 hover:border-yellow-500 relative top-32 block w-full left-0">
                            <a href="{{ route('login.logout') }}" class="delete-task ml-4">{{__('Выход')}}</a>
                        </div>

                    @else
                        <div class="relative top-60 block w-[400px] ml-4">
                            <a href="{{ route('login') }}"
                               class="delete-task border-b border-black border-dotted hover:text-yellow-500 hover:border-yellow-500 ">{{__('Вход')}}
                            </a>
                            <p class="text-sm">{{__('или')}}</p>
                            <a href="{{ route('user.signup') }}"
                               class="delete-task border-b border-black border-dotted hover:text-yellow-500 hover:border-yellow-500">{{__('Регистрация')}}</a>
                        </div>
            @endauth
            @endif
        </div>
    </nav>
</div>

<script>
    // Burger menus
    document.addEventListener('DOMContentLoaded', function () {
        // open
        const burger = document.querySelectorAll('.navbar-burger');
        const menu = document.querySelectorAll('.navbar-menu');
        if (burger.length && menu.length) {
            for (var i = 0; i < burger.length; i++) {
                burger[i].addEventListener('click', function () {
                    for (var j = 0; j < menu.length; j++) {
                        menu[j].classList.toggle('hidden');
                    }
                });
            }
        }
        // close
        const close = document.querySelectorAll('.navbar-close');
        const backdrop = document.querySelectorAll('.navbar-backdrop');
        if (close.length) {
            for (var i = 0; i < close.length; i++) {
                close[i].addEventListener('click', function () {
                    for (var j = 0; j < menu.length; j++) {
                        menu[j].classList.toggle('hidden');
                    }
                });
            }
        }
        if (backdrop.length) {
            for (var i = 0; i < backdrop.length; i++) {
                backdrop[i].addEventListener('click', function () {
                    for (var j = 0; j < menu.length; j++) {
                        menu[j].classList.toggle('hidden');
                    }
                });
            }
        }
    });
</script>