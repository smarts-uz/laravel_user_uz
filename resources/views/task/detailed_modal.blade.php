{{-- podelitsa modal start --}}
<div
class="hidden overflow-x-auto overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none        justify-center items-center"
style="background-color:rgba(0,0,0,0.5)" id="modal-id45">
<div class="relative w-full my-6 mx-auto max-w-3xl" id="modal45">
    <div
        class="border-0 rounded-lg shadow-2xl px-10 relative flex mx-auto flex-col sm:w-4/5 w-full bg-white outline-none focus:outline-none">
        <div class=" text-center p-6  rounded-t">
            <button type="submit" onclick="toggleModal45()"
                    class="rounded-md w-100 h-16 absolute top-1 right-4 focus:outline-none">
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
                        <option value="{{$complType->id}}">{{$complType->name}}</option>
                    @endforeach
                </select>
                <textarea name="c_text" id=""
                          class="border-2 border-gray-500 rounded-lg p-2 w-4/5 focus:outline-none hover:border-yellow-500"></textarea>
                <input type="submit" value="{{__('Отправить')}}"
                       class="bg-yellow-500 mt-4 py-3 px-5 rounded-lg text-white text-xl cursor-pointer font-medium border-2 border-gray-500 hover:bg-yellow-600">
            </form>

        </div>
    </div>
</div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id45-backdrop"></div>
{{-- podelitsa modal end --}}

 {{-- share modal start --}}
 <div
 class="hidden overflow-x-auto overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none        justify-center items-center"
 style="background-color:rgba(0,0,0,0.5)" id="modal-id44">
 <div class="relative w-full my-32 mx-auto max-w-3xl" id="modal44">
     <div
         class="border-0 rounded-lg shadow-2xl px-10 relative flex mx-auto flex-col sm:w-4/5 w-full bg-white outline-none focus:outline-none">
         <div class=" text-center p-6  rounded-t">
             <button type="submit" onclick="toggleModal44()"
                     class="rounded-md w-100 h-16 absolute top-1 right-4 focus:outline-none">
                 <i class="fas fa-times text-xl w-full"></i>
             </button>
             <h1 class="font-bold text-3xl block mt-6">
                 {{__('Рассказать о заказе')}}
             </h1>
             <p class="my-3">{{__('Расскажите об этом заказе в социальных сетях — оно заслуживает того, чтобы его увидели.')}}</p>
         </div>
         <div class="text-center mb-8 flex flex-wrap md:w-4/5 w-full mx-auto">
             <span class="telegram"><i
                     class="fab fa-telegram px-4 py-3 bg-blue-500 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
             <span class="instagram"><i
                     class="fab fa-instagram px-4 py-3 bg-red-700 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
             <span class="whatsapp"><i
                     class="fab fa-whatsapp px-4 py-3 bg-green-700 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
             <span class="facebook"><i
                     class="fab fa-facebook px-4 py-3 bg-blue-700 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
             <span class="email"><i
                     class="fas fa-at px-4 py-3 bg-yellow-600 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
             <span class="twitter"><i
                     class="fab fa-twitter px-3 py-2.5 text-blue-500 text-white rounded-lg m-4 text-4xl cursor-pointer border-2 border-blue-500"></i></span>
             <span class="linkedin"><i
                     class="fab fa-linkedin px-4 py-3 bg-blue-400 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
             <span class="google"><i
                     class="fab fa-google px-4 py-3 bg-red-700 text-white rounded-lg m-4 text-4xl cursor-pointer"></i></span>
         </div>
     </div>
 </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id45-backdrop"></div>
{{-- share modal end --}}

{{-- Modal start --}}
<div
class="hidden overflow-x-auto bg-black bg-opacity-50 overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
id="modal-id4">
<form id="updatereview" action="{{route('update.sendReview', $task->id)}}" method="POST">
    @csrf
    <div class="relative my-6 mx-auto max-w-xl" id="modal4">
        <input type="text" hidden name="status" id="status" value="">
        <div
            class="border-0 top-32 rounded-lg shadow-2xl px-10 py-10 relative flex mx-auto flex-col w-full bg-white outline-none focus:outline-none">
            <div class=" text-center  rounded-t">
                <button id="close-id4"
                        class=" w-100 h-16 absolute top-1 right-4">
                </button>
                <h3 class="font-semibold text-gray-700 text-3xl block">
                    {{__(' Оставить отзыв')}}
                </h3>
            </div>
            <div class="text-center h-56 w-full mx-auto text-base">
                <div class="">
                    <div class="flex flex-row justify-center w-full my-4 mx-auto">
                        <label id="class_demo"
                               class="cursor-pointer w-32 text-gray-500 border rounded-l hover:bg-green-500 transition duration-300 hover:text-white">
                            <input type="radio" name="good"
                                   class="good border hidden rounded ml-6 w-8/12"
                                   value="1">
                            <i class="far fa-thumbs-up text-2xl mr-2"></i><span
                                class="relative -top-1">good</span>
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
                    <textarea name="comment" class="h-24 block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white shadow-lg drop-shadow-xl
                            border resize-none w-full border-solid border-gray-200 rounded transition ease-in-out m-0 focus:outline-none  focus:border-yellow-500 "></textarea>

                    <button
                        class="send-comment font-sans w-full text-lg font-semibold bg-green-500 text-white hover:bg-green-400 px-12 pt-2 pb-3 rounded transition-all duration-300 mt-8"
                        type="submit">
                        {{__('Отправить')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id4-backdrop"></div>
{{--        share in webpages--}}

{{-- otklik modal  --}}
     <div id="authentication-modal"
     aria-hidden="true"
     class="btn-preloader hidden overflow-x-hidden overflow-y-auto fixed h-modal md:h-full top-4 left-0 right-0 md:inset-0 z-50 justify-center items-center">
    <div
        class="relative w-full max-w-md px-4 h-full md:h-auto">
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
            <form
                class="space-y-6 px-6 lg:px-8 pb-4 sm:pb-6 xl:pb-8"
                action="{{route("task.response.store", $task->id)}}"
                method="post">
                @csrf
                <header>
                    <h2 class="font-semibold text-2xl mb-4">{{__('Добавить предложение к заказу')}}</h2>
                </header>
                <main>
                <textarea required
                          class="resize-none rounded-md w-full focus:outline-none  focus:border-yellow-500 border border p-4  transition duration-200 my-4"
                          type="text" id="form8" rows="4"
                          name="description"></textarea>
                    <p id="text1" class="hidden text-lg">
                        {{__(' Если заказчик захочет с вами связаться, мы автоматически
                        спишем стоимость контакта с вашего счёта')}}</p>
                    <div class="my-2">
                        <label class=" px-2">
                            <input type="checkbox"
                                   name="notificate"
                                   class="mr-2 my-3 focus:outline-none  focus:border-yellow-500">{{__('Уведомить меня, если исполнителем')}}
                            <br>
                        </label>
                        <label class="px-2">
                            <input
                                class="focus:outline-none  focus:border-yellow-500   my-3 coupon_question mr-2"
                                type="checkbox"
                                name="coupon_question"
                                value="1"
                                onchange="valueChanged()"/>{{__('Указать время актуальности предложения')}}
                        </label>
                        <br>
                        <select name="response_time"
                                id="AttorneyEmpresa"
                                class="answer text-[16px] focus:outline-none border-gray-500 border rounded-lg hover:bg-gray-100 my-2 py-2 px-5 text-gray-500"
                                style="display: none">
                            <option value="1" class="">
                                1 {{__('часов')}}</option>
                            <option value="2" class="">
                                2 {{__('часов')}}</option>
                            <option value="4" class="">
                                4 {{__('часов')}}</option>
                            <option value="6" class="">
                                6 {{__('часов')}}</option>
                            <option value="8" class="">
                                8 {{__('часов')}}</option>
                            <option value="10" class="">
                                10 {{__('часов')}}</option>
                            <option value="12" class="">
                                12 {{__('часов')}}</option>
                            <option value="24" class="">
                                24 {{__('часов')}}</option>
                            <option value="48" class="">
                                48 {{__('часов')}}</option>
                        </select>
                    </div>
                    <label>
                        <input type="text"
                               onkeypress='validate(event)'
                               checked name="price"
                               class="border rounded-md px-2 border-solid focus:outline-none  focus:border-yellow-500 mr-3 my-2">UZS
                        <input type="text" name="pay"
                               class="pays border rounded-md px-2 border-solid focus:outline-none  focus:border-yellow-500 mr-3 my-2 hidden"
                               value="0">
                        <input type="text"
                               name="task_user_id"
                               class="pays border rounded-md px-2 border-solid focus:outline-none  focus:border-yellow-500 mr-3 my-2 hidden"
                               value="{{$task->user_id}}">
                    </label>
                    <hr>
                </main>
                <footer
                    class="flex justify-center bg-transparent">
                    <button type="submit"
                            class=" bg-yellow-500 font-semibold text-white py-3 w-full rounded-md my-4 hover:bg-orange-500 focus:outline-none shadow-lg hover:shadow-none transition-all duration-300">
                        {{__('Далее')}}
                    </button>
                </footer>

            </form>
        </div>
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
<div
class="hidden overflow-x-auto overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none        justify-center items-center"
style="background-color:rgba(0,0,0,0.5)" id="modal-id33">
<div class="relative w-full my-6 mx-auto max-w-3xl" id="modal33">
    <div
        class="border-0 rounded-lg shadow-2xl px-10 relative flex mx-auto flex-col sm:w-4/5 w-full bg-white outline-none focus:outline-none">
        <div class=" text-center p-6  rounded-t">
            <button type="submit" onclick="toggleModal33()"
                    class="rounded-md w-100 h-16 absolute top-1 right-4 focus:outline-none">
                <i class="fas fa-times text-xl w-full"></i>
            </button>
            <h1 class="font-medium text-3xl block mt-6">
                Исполнитель выбран 
            </h1>
        </div>
        <div class="text-center my-6 mx-auto">
            <img class="border-2 rounded-xl w-32 h-32 mx-auto" src="" alt="user_avatar">
            <h1>Ilhomjon</h1>
            <p>+998 94 548 05 14</p>
            <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution.</p>
            <button  onclick="toggleModal33()" type="submit" class="cursor-pointer mt-2 text-semibold text-center inline-block py-3 px-4 bg-white transition duration-200 text-white bg-green-500 hover:bg-green-500 font-medium border border-transparent rounded-md">Хорошо</button>
        </div>
    </div>
</div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id33-backdrop"></div>
{{-- zakazchik ispolnitel tanlagandagi modal end--}}