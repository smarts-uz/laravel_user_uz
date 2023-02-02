<link rel="stylesheet" href="{{ asset('/css/index.css') }}">
<div class="border-b border-solid border-gray-200 w-full shadow-sm fixed bg-white top-0 z-50">

    <nav class="z-10 relative flex items-center xl:w-11/12 mx-auto lg:justify-start text-base" aria-label="Global">
        <div class="flex items-center flex-grow flex-shrink-0 lg:flex-grow-0">
            {{-- mobile menu --}}
           @include('components.mobile_menu')
            {{-- mobile menu end--}}
        </div>

        <div class="hidden w-7/12 lg:inline-block xl:ml-12 lg:ml-12 md:text-sm xl:text-base">
            <div class="group inline-block mr-4">
                <button class="hover:text-yellow-500 focus:outline-none">
                    <span class="pr-1 flex-1">{{__('Создать задание')}}</span>
                    <span></span>
                </button>
                <ul class="bg-white border rounded-md transform scale-0 group-hover:scale-100 absolute transition duration-150 ease-in-out origin-top z-10">
                    @foreach (categories() as $category)
                        <li class="py-2 px-4 rounded-sm hover:bg-gray-100">
                            <button class="w-full text-left flex items-center outline-none focus:outline-none">
                                <span class="pr-1 flex-1 font-semibold text-sm hover:text-blue-700">{{ $category[0]->parent->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}</span>
                                <span class="mr-auto">
                                <svg class="fill-current h-4 w-4 transition duration-150 ease-in-out" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                </svg>
                            </span>
                            </button>
                            <ul class="bg-white border rounded-sm absolute h-full overflow-y-auto top-0 right-0 transition duration-150 ease-in-out origin-top-left w-100">
                                @foreach ($category as $category2)
                                    <li class="rounded-sm">
                                        <a class="text-sm py-2 px-5 w-full block hover:bg-gray-100" href="{{route("task.create.name", ['category_id'=>$category2->id])}}">
                                            {{ $category2->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
            <a href="{{ route('searchTask.task_search') }}"
               class="task cursor-pointer delete-task hover:text-yellow-500 mr-4 text-[14px] xl:text-[16px] ">{{__('Найти задания')}}</a>
            <a href="/performers" class="performer delete-task cursor-pointer hover:text-yellow-500 text-[14px] mr-4 xl:text-[16px] ">{{__('Исполнители')}}</a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('searchTask.mytasks') }}" class="mytask delete-task cursor-pointer hover:text-yellow-500 text-[14px] xl:text-[16px] ">{{__('Мои заказы')}}</a>
                @else
                @endauth
            @endif
        </div>
        <?php
        use App\Models\Notification;
        use Illuminate\Support\Facades\Auth;
        ?>
        @if (Route::has('login'))
            @auth
                <div class="flex lg:inline-block hidden w-3/12 float-right mt-3">
                    {{-- icon-1  Notifications  --}}
                    <div class="max-w-lg mx-auto float-left">
                        @include('components.notification')
                    </div>

                    {{-- icon-2   Chat  --}}
                    <div class="float-left open-chat" style="cursor:pointer">
                        @php
                            $messages = App\Http\Controllers\vendor\Chatify\MessagesController::unseenCount();
                        @endphp
                        @if($messages > 0)
                            <div id="content_count"
                                 class="w-4 h-4 absolute rounded-full bg-red-500 ml-3 text-white text-xs text-center">
                                {{$messages}}</div>
                        @endif
                        <a class="delete-task">
                            <i class="xl:text-2xl lg:text-xl text-gray-500 hover:text-yellow-500 far fa-comment-alt"></i>
                        </a>
                    </div>
                    {{--  JS Panel--}}
                    <script>
                        const createChatPanel = (event) => {
                            jsPanel.create({
                                content: `<iframe src="{{url('/chat')}}" frameborder="0" style="width: 100%; height: 100%"></iframe>`,
                                theme: 'primary',
                                position: 'center',
                                closeOnEscape: true,
                                headerTitle: 'Universal Services',
                                headerControls: {
                                    size: 'md',
                                },
                                borderRadius: '1rem',
                                panelSize: {
                                    width: '80vw',
                                    height: '90vh'
                                },
                                contentSize: '80vw 90vh',
                            });
                            event.preventDefault();
                        }
                        const openChat = document.querySelector('.open-chat');
                        openChat.addEventListener('click', createChatPanel);
                    </script>
                    @php
                        $walletBalance = App\Services\Profile\ProfileService::walletBalance(auth()->user());
                    @endphp
                    {{-- icon 3  Payment--}}
                    <div class="max-w-lg ml-5 float-left">
                        <a onclick="toggleModal()" style="cursor:pointer">
                           <div class="flex flex-row space-x-2 justify-evenly text-green-400 hover:text-yellow-500">
                                <div>
                                  <i class="xl:text-2xl lg:text-xl fas fa-wallet"></i>
                                 </div>
                               <div class="font-medium py-1 text-center">
                                   {{$walletBalance}}  @if($walletBalance) UZS @endif
                               </div>
                           </div>
                        </a>
                    </div>


                    {{-- icon-4  Profile  --}}
                    <div class="max-w-lg ml-5 float-left">
                        <button class="focus:outline-none" type="button" data-dropdown-toggle="dropdowndesk">
                            <img class="w-8 h-8 border-2 rounded-lg hover:border-yellow-500" src="{{asset('storage/'.auth()->user()->avatar)}}" alt="avatar">
                        </button>
                        <!-- Dropdown menu -->
                        <div class="hidden bg-white text-base z-50 list-none divide-y divide-gray-100 rounded shadow my-4" id="dropdowndesk">
                            <ul class="py-1" aria-labelledby="dropdowndesk">
                                <li>
                                    <a href="/profile" class="delete-task cursor-pointer text-sm hover:bg-gray-100 text-gray-700 block px-4 py-2">{{__('Профиль')}}</a>
                                </li>
                                <li>
                                    <a href="/profile/settings" class="delete-task cursor-pointer text-sm hover:bg-gray-100 text-gray-700 block px-4 py-2">{{__('Настройки')}}</a>
                                </li>
                                <li>
                                    <a href="{{ route('login.logout') }}" class="delete-task text-sm hover:bg-gray-100 text-gray-700 block px-4 py-2">{{__('Выход')}}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- language blog -->
                <div class="flex justify-center text-gray-500 hidden lg:block md:text-sm xl:text-base pr-4">
                    <div class="flex">
                        @if (session('lang') === 'ru')
                            <a href="{{route('lang', ['lang'=>'uz'])}}" class="hover:text-red-500 mr-2 font-bold">
                                O'zbek
                            </a>
                            I
                            <a href="{{route('lang', ['lang'=>'ru'])}}" class="text-red-500 hover:text-gray-500-500 ml-2 font-bold">
                                Русский
                            </a>
                        @else
                            <a href="{{route('lang', ['lang'=>'uz'])}}" class="text-red-500 hover:text-gray-500 mr-2 font-bold">
                                O'zbek
                            </a>
                            I
                            <a href="{{route('lang', ['lang'=>'ru'])}}" class="hover:text-red-500 ml-2 font-bold">
                                Русский
                            </a>
                        @endif
                    </div>
                </div>



            @else
                <div class="w-3/12 text-right inline-block float-right md:float-none mt-6 mb-6 lg:block hidden mr-4 text-sm xl:text-base">
                    <a href="{{ route('login') }}"
                       class="delete-task border-b border-black border-dotted hover:text-yellow-500 hover:border-yellow-500 ">{{__('Вход')}}</a> {{__('или')}}
                    <a href="{{ route('user.signup') }}"
                       class="delete-task border-b border-black border-dotted hover:text-yellow-500 hover:border-yellow-500">{{__('Регистрация')}}</a>
                </div>
                <!-- language blog -->
                <div class="flex justify-center text-gray-500 hidden lg:block md:text-sm xl:text-base pr-4">
                    <div class="flex">
                        @if (session('lang') === 'ru')
                            <a href="{{route('lang', ['lang'=>'uz'])}}" class="hover:text-red-500 mr-2">
                                UZ
                            </a>
                            I
                            <a href="{{route('lang', ['lang'=>'ru'])}}" class="text-red-500 hover:text-gray-500-500 ml-2">
                                RU
                            </a>
                        @else
                            <a href="{{route('lang', ['lang'=>'uz'])}}" class="text-red-500 hover:text-gray-500 mr-2">
                                UZ
                            </a>
                            I
                            <a href="{{route('lang', ['lang'=>'ru'])}}" class="hover:text-red-500 ml-2">
                                RU
                            </a>
                        @endif
                    </div>
                </div>
            @endauth
        @endif
    </nav>
</div>

        {{-- payment modals --}}
             @include('components.payment')
        {{-- payment modals end--}}


<script>
    $('.see_all').click(function () {
        $.ajax({
            url: "/del-notif",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (response) {
                console.log(response);
            },
            error: function (error) {
                console.log(error);
            }
        });
        $("#notifs").load(location.href + " #notifs");
        $("#content_count").addClass('hidden');
    });
    var link = document.location.href.split('/');
    if (link[3] == 'task') {
        $('.delete-task').on('click', function () {

            let for_del_task_in = $(this).attr("href");
            // console.log(for_del_task_in);
            $(this).removeAttr('href');
            Swal.fire({
                title: "{!!__('Введённые данные будут потеряны. <br> Удалить задание?')!!}",
                showDenyButton: true,
                confirmButtonText: "{{__('Продолжить создание')}}",
                denyButtonText: "{{__('Удалить')}}",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = document.location.href;
                } else if (result.isDenied) {
                    if (var_for_id_task != null) {
                        $.ajax({
                            url: '/for_del_new_task/' + var_for_id_task + '',
                            method: 'get',
                        });
                    }
                    window.location.href = for_del_task_in;
                    return false;
                }
            });

        });

    }
</script>
<script>
    var link = document.location.href.split('/');
    if (link[3] == 'performers') {
        $(".performer").addClass("text-yellow-400");
    } else if (link[3] == 'my-tasks') {
        $(".mytask").addClass("text-yellow-400");
    } else if (link[3] == 'task-search') {
        $(".task").addClass("text-yellow-400");
    }
</script>

