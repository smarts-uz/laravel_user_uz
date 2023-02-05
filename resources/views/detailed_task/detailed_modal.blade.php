{{-- not_complete modal start --}}
<div class="hidden overflow-x-auto overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
    style="background-color:rgba(0,0,0,0.5)" id="modal-id45">
    <div class="relative w-full my-6 mx-auto max-w-3xl" id="modal45">
        <div class="border-0 rounded-lg shadow-2xl px-10 relative flex mx-auto flex-col sm:w-4/5 w-full bg-white outline-none focus:outline-none">
            <div class="text-center p-6 rounded-t">
                <button type="submit" onclick="toggleModal45()" class="rounded-md w-100 h-16 absolute top-1 right-4 focus:outline-none">
                    <i class="fas fa-times text-xl w-full"></i>
                </button>
                <h1 class="font-medium text-3xl block mt-6">
                    {{__('Напишите причину по которой задание не было закончено')}}
                </h1>
            </div>
            <div class="text-center my-6">

                <form action="{{route('update.not_completed', ['task' => $task])}}" method="POST">
                    @csrf
                    <textarea name="reason" required
                              class="border-2 border-gray-500 rounded-lg p-2 w-4/5 focus:outline-none hover:border-yellow-500"></textarea>
                    <input type="submit" value="{{__('Отправить')}}" required
                           class="bg-yellow-500 mt-4 py-3 px-5 rounded-lg text-white text-xl cursor-pointer font-medium border-2 border-gray-500 hover:bg-yellow-600">
                </form>

            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id45-backdrop"></div>
{{-- not_complete modal end --}}

{{-- podelitsa modal start --}}
<div class="hidden overflow-x-auto overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
    style="background-color:rgba(0,0,0,0.5)" id="modal-id88">
    <div class="relative w-full my-6 mx-auto max-w-3xl" id="modal88">
        <div class="border-0 rounded-lg shadow-2xl px-10 relative flex mx-auto flex-col sm:w-4/5 w-full bg-white outline-none focus:outline-none">
            <div class=" text-center p-6  rounded-t">
                <button type="submit" onclick="toggleModal88()" class="rounded-md w-100 h-16 absolute top-1 right-4 focus:outline-none">
                    <i class="fas fa-times text-xl w-full"></i>
                </button>
                <h1 class="font-medium text-3xl block mt-6">
                    {{__('Напишите свое возражение по созданной задаче')}}
                </h1>
            </div>
            <div class="text-center my-6">

                <form action="{{route('searchTask.comlianse_save')}}" method="POST">
                    @csrf
                    <input type="hidden" name="taskId" value="{{ $task->id }}">
                    <input type="hidden" name="userId"
                           value="{{ Auth::check() ? Auth::user()->id : $task->user->id}}">
                    <select name="c_type" id=""
                            class="w-4/5 border-2 border-gray-500 rounded-lg mb-4 py-2 px-2 focus:outline-none hover:border-yellow-500">
                        @foreach ($complianceType as $complType)
                            <option value="{{$complType->id}}">{{$complType->getTranslatedAttribute('name')}}</option>
                        @endforeach
                    </select>
                    <textarea name="c_text" id="" required
                              class="border-2 border-gray-500 rounded-lg p-2 w-4/5 focus:outline-none hover:border-yellow-500"></textarea>
                    <input type="submit" value="{{__('Отправить')}}" required
                           class="bg-yellow-500 mt-4 py-3 px-5 rounded-lg text-white text-xl cursor-pointer font-medium border-2 border-gray-500 hover:bg-yellow-600">
                </form>

            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id88-backdrop"></div>
{{-- podelitsa modal end --}}

{{-- share modal start --}}
<div class="hidden overflow-x-auto overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
    style="background-color:rgba(0,0,0,0.5)" id="modal-id44">
    <div class="relative w-full my-32 mx-auto max-w-3xl" id="modal44">
        <div class="border-0 rounded-lg shadow-2xl px-10 relative flex mx-auto flex-col sm:w-4/5 w-full bg-white outline-none focus:outline-none">
            <div class=" text-center p-6  rounded-t">
                <button type="submit" onclick="toggleModal44()" class="rounded-md w-100 h-16 absolute top-1 right-4 focus:outline-none">
                    <i class="fas fa-times text-xl w-full"></i>
                </button>
                <h1 class="font-bold text-3xl block mt-6">
                    {{__('Рассказать о заказе')}}
                </h1>
                <p class="my-3">{{__('Расскажите об этом заказе в социальных сетях — оно заслуживает того, чтобы его увидели.')}}</p>
            </div>
            <div class="grid grid-cols-4 mb-8">
                <span class="telegram mx-auto"><i
                        class="fab fa-telegram px-4 py-3 bg-blue-500 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
                <span class="instagram mx-auto"><i
                        class="fab fa-instagram px-4 py-3 bg-red-700 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
                <span class="whatsapp mx-auto"><i
                        class="fab fa-whatsapp px-4 py-3 bg-green-700 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
                <span class="facebook mx-auto"><i
                        class="fab fa-facebook px-4 py-3 bg-blue-700 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
                <span class="email mx-auto"><i
                        class="fas fa-at px-4 py-3 bg-yellow-600 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
                <span class="twitter mx-auto"><i
                        class="fab fa-twitter px-3 py-2.5 text-blue-500 text-white rounded-lg m-4 text-4xl cursor-pointer border-2 border-blue-500"></i></span>
                <span class="linkedin mx-auto"><i
                        class="fab fa-linkedin px-4 py-3 bg-blue-400 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
                <span class="google mx-auto"><i
                        class="fab fa-google px-4 py-3 bg-red-700 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id45-backdrop"></div>
{{-- share modal end --}}

{{-- review modal --}}
<div class="hidden overflow-x-auto overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
    style="background-color:rgba(0,0,0,0.5)" id="modal-id4">
    <div class="relative w-full my-32 mx-auto max-w-3xl" id="modal4">
        <div
            class="border-0 rounded-lg shadow-2xl px-10 relative flex mx-auto flex-col sm:w-4/5 w-full bg-white outline-none focus:outline-none">
            <div class=" text-center p-6  rounded-t">
                <button id="close_btn" type="button" onclick="toggleModal4()"
                        class="rounded-md w-100 h-16 absolute top-1 right-4 focus:outline-none">
                    <i class="fas fa-times text-xl w-full"></i>
                </button>
                <h3 class="font-semibold text-gray-700 text-3xl block">
                    {{__(' Оставить отзыв')}}
                </h3>
            </div>
            <div class="text-center h-64 w-full mx-auto text-base mb-4">
                <form id="updatereview" action="{{route('update.sendReview', $task->id)}}" method="POST">
                    @csrf
                    <div class="">
                        <div class="flex flex-row justify-center w-full my-4 mx-auto">
                            <label id="class_demo"
                                class="cursor-pointer w-32 text-gray-500 border rounded-l hover:bg-green-500 transition duration-300 hover:text-white">
                                <input type="radio" name="good" checked
                                    class="good border hidden rounded ml-6 w-8/12"
                                    value="1">
                                <i id="icon" class="far fa-thumbs-up text-2xl mr-2"></i><span
                                    class="relative -top-1" id="good">good</span>
                            </label>
                            <label id="class_demo1"
                                class="cursor-pointer w-32 text-gray-500 border rounded-r hover:bg-red-500 transition duration-300 hover:text-white">
                                <input type="radio" name="good"
                                    class="good border hidden rounded ml-6  w-8/12"
                                    value="0">
                                <i class="far fa-thumbs-down text-2xl mr-2"></i><span
                                    class="relative -top-1">bad</span>
                            </label>
                        </div>
                        <textarea name="comment" required class="h-24 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white shadow-lg drop-shadow-xl
                            border resize-none w-full border-solid border-gray-200 rounded transition ease-in-out m-0 focus:outline-none  focus:border-yellow-500 "></textarea>

                        <button
                            class="font-sans w-full text-lg font-semibold bg-green-500 text-white hover:bg-green-400 px-12 pt-2 pb-3 rounded transition-all duration-300 mt-8"
                            type="submit">
                            {{__('Отправить')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id4-backdrop"></div>
{{-- review modal end--}}

{{-- otklik modal  --}}
<div id="authentication-modal"
     aria-hidden="true"
     class="btn-preloader hidden overflow-x-hidden overflow-y-auto fixed h-modal md:h-full top-4 left-0 right-0 md:inset-0 z-50 justify-center items-center">
    <div class="relative w-full max-w-md h-full md:h-auto">
        @if(Auth::check() && (int)auth()->user()->is_phone_number_verified !== 1)
            <div class="bg-white rounded-lg shadow relative dark:bg-gray-700">
                <div class="flex justify-end p-2">
                    <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white"
                            data-modal-toggle="authentication-modal">
                        <svg class="w-5 h-5" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                  clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <header>
                    <h2 class="font-semibold text-3xl mb-4 text-center">{{__('Подтвердите номер телефона')}}</h2>
                </header>
                <main class="py-6">
                    <h1 class="text-xl text-gray-900 text-center my-6">{{__('Вы не подтвердили свой номер телефона, пожалуйста, подтвердите свой номер телефона')}}</h1>
                </main>
            </div>
        @elseif(Auth::check() && (int)auth()->user()->role_id !== \App\Models\User::ROLE_PERFORMER)
            <div class="bg-white rounded-lg shadow relative dark:bg-gray-700">
                <div class="flex justify-end p-2">
                    <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white"
                            data-modal-toggle="authentication-modal">
                        <svg class="w-5 h-5" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                  clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <header>
                    <h2 class="font-semibold text-3xl mb-4 text-center">{{__('Вы еще не исполнитель')}}</h2>
                </header>
                <main class="py-6 text-center">
                    <h1 class="text-xl text-gray-900 text-center my-6 px-4">{{__('Вы еще не являетесь руководителем Universal Services. Присоединяйтесь к списку исполнителей.')}}</h1>
                    <a href="{{ route('profile.verificationInfo') }}">
                        <button  class="px-6 py-2 font-sans mb-4 text-lg mt-8 font-semibold bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-lg">
                            {{__('СТАТЬ ИСПОЛНИТЕЛЕМ')}}
                        </button>
                    </a>
                </main>
            </div>
        @else
            <!-- Modal content -->
            <div
                class="bg-white rounded-lg shadow relative dark:bg-gray-700">
                <div class="flex justify-end p-2">
                    <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white"
                            data-modal-toggle="authentication-modal">
                        <svg class="w-5 h-5" fill="currentColor"
                             viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                  clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <form class="space-y-6 px-6 lg:px-8 pb-4 sm:pb-6 xl:pb-8" action="{{route("task.response.store", $task->id)}}" method="post">
                    @csrf
                    <header>
                        <h2 class="font-semibold text-3xl mb-4 text-center">{{__('Откликнуться')}}</h2>
                    </header>
                    <main>
                        <span class="text-base">{{__('Описание отклика')}}</span>
                        <textarea required
                                  class="resize-none rounded-md w-full focus:outline-none  focus:border-yellow-500 border border-gray-300 p-4  transition duration-200 mb-4"
                                  type="text" id="form8" rows="4" name="description"></textarea>
                        <p class="text-base">{{__('Сколько вы предлагаете')}}</p>
                        <label>
                            <input type="text" required onkeypress='validate(event)' id="task_price"
                                   class="border border-gray-300 rounded-md px-2 border-solid focus:outline-none  focus:border-yellow-500 mr-3 mb-2">UZS
                            <input type="hidden" name="price" id="price">
                            <input type="hidden" name="task_user_id"
                                   class="pays border rounded-md px-2 border-solid focus:outline-none  focus:border-yellow-500 mr-3 my-2"
                                   value="{{$task->user_id}}">
                        </label>
                        <hr>
                    </main>
                    <input type="text" class="hidden" id="not_free" name="not_free">
                    <footer
                        class="flex justify-center bg-transparent">
                        <button type="submit"
                                class=" bg-yellow-500 font-semibold text-white py-3 w-full rounded-md my-4 hover:bg-orange-500 focus:outline-none shadow-lg hover:shadow-none transition-all duration-300">
                            {{__('Далее')}}
                        </button>
                    </footer>

                </form>
            </div>
        @endif
    </div>
</div>
{{-- otklik modal  --}}

<div class="modal___1" style="display: none">
    <div
        class="modal__1 h-screen w-full fixed left-0 top-0 flex justify-center items-center bg-black bg-opacity-50">
        <!-- modal -->
        <div
            class="bg-white rounded shadow-lg w-10/12 md:w-1/3 text-center text-green-500 py-12 text-3xl">
            <!-- modal header -->
            <i class="far fa-check-circle fa-4x py-4"></i>
            <div class="mx-12">
                {{__('Ваш отклик успешно отправлен!')}}
            </div>
        </div>
    </div>
</div>


{{-- zakazchik ispolnitel tanlagandagi modal --}}
@if(session()->has('data'))
    <div
        class="{{ session()->has('data') ?"":'hidden' }} overflow-x-auto overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
        style="background-color:rgba(0,0,0,0.5)" id="modal-id33">
        <div class="relative w-full my-6 mx-auto max-w-3xl" id="modal33">
            <div
                class="border-0 rounded-lg shadow-2xl px-10 relative flex mx-auto flex-col sm:w-4/5 w-full bg-white outline-none focus:outline-none">
                <div class=" text-center p-6  rounded-t">
                    <h1 class="font-medium text-3xl block mt-6">
                        {{__('Исполнитель выбран')}}
                    </h1>
                </div>
                <div class="text-center mb-6 mx-auto">
                    <img class="border-2 rounded-xl w-28 h-28 mx-auto" src="{{ session('data')['performer_avatar'] }}"
                         alt="user_avatar">
                    <h1 class="my-2 font-medium hover:text-red-500 ">{{ session('data')['performer_name'] }}</h1>
                    <p class="mb-2"> {{  session('data')['performer_phone'] }}</p>
                    <p>{{  session('data')['performer_description'] }}</p>
                    <button onclick="toggleModal33()" type="submit" class="cursor-pointer mt-2 text-semibold text-center
                    inline-block py-3 px-4 bg-white transition duration-200 text-white bg-green-500 hover:bg-green-500 font-medium border border-transparent rounded-md">
                        {{__('Хорошо')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
{{-- zakazchik ispolnitel tanlagandagi modal end--}}
<div class='modal' id='modal1'>
    <div class='content'>
        <img class="w-64 h-64"
             src="{{asset('images/cash_icon.png')}}"
             alt="">
        <h1 class="title">{{__('Пополните баланс')}}</h1>
        <a class='btn rounded-lg'
           href="/profile/cash">{{__('Пополнить')}}</a>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js"></script>
<script>
    var numberMask = IMask(
        document.getElementById('task_price'),
        {
            mask: Number,
            min: 0,
            max: 100000000,
            thousandsSeparator: ' '
        }
    );
    $("#task_price").keyup(function () {
        var text = $(this).val()
        text = text.replace(/[^0-9.]/g, "")
        $("#price").val(text)
    })
</script>

