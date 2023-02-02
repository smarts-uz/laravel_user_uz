<div class="w-full flex flex-col sm:flex-row sm:p-6 p-2">
    <div class="w-full text-center">
        @php
            $walletBalance = \App\Services\Profile\ProfileService::walletBalance(auth()->user());
        @endphp
        @auth
            @if($walletBalance >= setting('admin.pullik_otklik'))
                @if($task->user_id !== auth()->id() && $task->status < 3 && !$auth_response)
                    <button
                        class="sm:w-4/5 w-full font-sans text-lg pay font-semibold bg-green-500 text-white hover:bg-green-600 px-8 pt-1 pb-2 mt-6 rounded-lg transition-all duration-300"
                        id="btn1"
                        type="button"
                        data-modal-toggle="authentication-modal">
                        {{__('Откликнуться за ')}} {{setting('admin.pullik_otklik')}} UZS<br>
                        <span class="text-sm">
                            {{__('и отправить контакты заказчику')}}<br>
                        </span>
                    </button>
                    <button
                        class="sm:w-4/5 w-full font-sans text-lg font-semibold bg-yellow-500 text-white hover:bg-yellow-600 px-8 pt-1 pb-2 mt-6 rounded-lg transition-all duration-300"
                        id="btn2"
                        type="button"
                        data-modal-toggle="authentication-modal">
                        {{__('Откликнуться на задание бесплатно')}}<br>
                        <span class="text-sm">
                            {{__('отклик - 0 UZS, контакт с заказчиком - ')}} {{setting('admin.bepul_otklik')}} UZS
                        </span>
                    </button>
                @endif
            @elseif($walletBalance < setting('admin.pullik_otklik') )
                @if($task->user_id !== auth()->id() && $task->status < 3 && !$auth_response)
                    <a class="open-modal"
                       data-modal="#modal1">
                        <button
                            class="sm:w-4/5 w-full font-sans text-lg pay font-semibold bg-green-500 text-white hover:bg-green-600 px-8 pt-1 pb-2 mt-6 rounded-lg transition-all duration-300">
                            {{__('Откликнуться за')}} {{setting('admin.pullik_otklik')}} UZS<br>
                            <span class="text-sm">
                            {{__('и отправить контакты заказчику')}}<br>
                        </span>
                        </button>
                    </a>
                    <a class="open-modal"
                       data-modal="#modal1">
                        <button
                            class="sm:w-4/5 w-full font-sans text-lg font-semibold bg-yellow-500 text-white hover:bg-yellow-600 px-8 pt-1 pb-2 mt-6 rounded-lg transition-all duration-300">
                            {{__('Откликнуться на задание бесплатно')}}<br>
                            <span class="text-sm">
                            {{__('отклик - 0 UZS, контакт с заказчиком - ')}} {{setting('admin.bepul_otklik')}} UZS
                        </span>
                        </button>
                    </a>
                @endif
            @endif
        @else
            <a href="/login">
                <button class="sm:w-4/5 w-full mx-auto font-sans mt-8 text-lg  font-semibold bg-yellow-500 text-white hover:bg-orange-500 px-10 py-4 rounded-lg">
                    {{__('Откликнуться на это задание')}}
                </button>
            </a>
        @endauth
        @auth
            @if($task->status === 3 && $task->user_id === auth()->user()->id)
                <div class="flex sm:flex-row flex-col w-11/12 mx-auto">
                    <button onclick="toggleModal4()"
                        class="text-lg font-semibold bg-green-500 text-white hover:bg-green-400 px-10 ml-6 pt-2 pb-3 rounded-lg transition-all duration-300 m-2"
                        type="submit">
                        {{__('Задание выполнено')}}
                    </button>
                    <button onclick="toggleModal45()"
                            class="text-lg font-semibold bg-red-500 text-white hover:bg-red-400 px-5 ml-6 pt-2 pb-3 rounded-lg transition-all duration-300 m-2"
                            type="submit">
                        {{__('Задание не выполнено')}}
                    </button>
                </div>
            @endif

            @if($task->status === 4 && $task->performer_id === auth()->user()->id && !$task->performer_review)
                <div class="flex sm:flex-row flex-col w-11/12 mx-auto">
                    <button onclick="toggleModal4()"
                        class="text-lg font-semibold bg-green-500 text-white hover:bg-green-400 px-10 ml-6 pt-2 pb-3 rounded-lg transition-all duration-300 m-2"
                        type="submit">
                        {{__('Задание выполнено')}}
                    </button>
                    <button onclick="toggleModal45()"
                            class="text-lg font-semibold bg-red-500 text-white hover:bg-red-400 px-5 ml-6 pt-2 pb-3 rounded-lg transition-all duration-300 m-2"
                            type="submit">
                        {{__('Задание не выполнено')}}
                    </button>
                </div>
            @endif
        @endauth
    </div>
</div>
