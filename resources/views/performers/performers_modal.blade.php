<div id="modal_content"
     class="modal_content hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
     style="background-color:rgba(0,0,0,0.5)">
    <div class="modal relative w-auto mt-12 mx-auto max-w-3xl">
        <div
            class="border-0 rounded-lg shadow-2xl px-10 relative flex mx-auto flex-col sm:w-4/5 w-full bg-white outline-none focus:outline-none text-center py-12">
            <h1 class="text-3xl font-semibold">{{__('Выберите задание, которое хотите предложить исполнителью')}}</h1>
            @foreach($tasks as $task)
                <label>
                    <input type="text" name="tasks_id" class="hidden" value="{{ $task->id }}">
                </label>
            @endforeach

            <select name="tasks" id="task_name" onchange="showDiv(this)"
                    class="appearance-none focus:outline-none border border-solid border-gray-500 rounded-lg text-gray-500 px-6 py-2 text-lg mt-6 hover:text-yellow-500  hover:border-yellow-500 hover:shadow-xl shadow-yellow-500 mx-auto block"><br>

                @foreach($tasks as $task)
                    <option value="{{ $task->id }}">
                        {{ $task->name }}
                    </option>
                @endforeach
                <option value="1">
                    + {{__('новое задание')}}
                </option>
            </select>
            <label>
                <input type="text" name="csrf" class="hidden" value="{{ csrf_token() }}">
            </label>

            <div id="hidden_div">
                <button type="submit" onclick="myFunc()"
                        class="cursor-pointer bg-red-500 text-white rounded-lg p-2 px-4 mt-4">
                    {{__('Предложить работу')}}
                </button>
                <p class="py-7">
                    {{__('Каждое задание можно предложить пяти исполнителям из каталога. исполнители получат СМС со ссылкой на ваше задание.')}}
                </p>
            </div>


            <form action="{{route('profile.set_session')}}" method="POST">
                @csrf
                <input type="hidden" name="performer_id" id="performer_id_task">
                <button id="hidden_div2" type="submit"
                        class="cursor-pointer bg-green-500 text-white rounded-lg p-2 px-4 mt-6 mx-auto"
                        style="display: none;">
                    {{__('Создать новое задание')}}
                </button>
            </form>

            <button
                class="cursor-pointer close text-gray-400 font-bold rounded-lg p-2 px-4 mt-6 absolute -top-6 right-0 text-2xl">
                x
            </button>
        </div>
    </div>
</div>

<!-- Основной контент страницы -->
<div id="modal" style="display: none">
    <div class="modal h-screen w-full fixed left-0 top-0 flex justify-center items-center bg-black bg-opacity-50">
        <!-- modal -->
        <div class="bg-white rounded shadow-lg w-10/12 md:w-1/3 text-center py-12">
            <!-- modal header -->

            <div class="text-2xl font-bold my-6">
                {{__('Мы отправили ему уведомление.')}}
            </div>
            <button onclick="myFunction1()"
                    class="cursor-pointer bg-green-500 text-white rounded-lg p-2 px-4 mt-6 mx-auto">
                ok
            </button>
        </div>
    </div>
</div>

{{-- Modal start --}}
<div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
     id="modal-id12" style="background-color:rgba(0,0,0,0.5)">
    <div class="relative w-auto my-6 mx-auto max-w-3xl" id="modal-id12">
        <div
            class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
            <div class=" text-center p-12  rounded-t">
                <button type="submit" onclick="toggleModal12('modal-id12')"
                        class="rounded-md w-100 h-16 absolute top-1 right-4">
                    <i class="fas fa-times  text-slate-400 hover:text-slate-600 text-xl w-full"></i>
                </button>
                <h3 class="font-medium text-4xl block mt-4">
                    {!!__('У вас пока нет опубликованных <br> заданий')!!}
                </h3>
            </div>
            <!--body-->
            <div class="relative p-6 flex-auto">
                <p class="my-4   text-center">
                    {!!__('Создайте задание, после чего вы сможете предложить <br> выполнить его исполнителям.')!!}
                </p>
            </div>
            <!--footer-->
            <div class="flex mx-auto items-center justify-end p-6 rounded-b mb-8">
                <div class="mt-4 ">
                    <form action="{{route('profile.set_session')}}" method="POST">
                        @csrf
                        <input type="hidden" name="performer_id" id="performer_id_task">
                        <button type="submit"
                                class="bg-green-500 rounded-lg text-white text-xl py-3 px-6 hover:bg-green-600">
                            {{__('Создать новое задание')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id12-backdrop"></div>
</div>
