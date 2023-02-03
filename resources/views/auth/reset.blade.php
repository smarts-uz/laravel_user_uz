@extends('layouts.app')
@section('content')
    <div class="mt-3 text-center text-base">
        <div class="mx-auto w-full">
            <h3 class="font-bold text-3xl block mt-8 mb-4">
                {{__('Восстановление пароля')}}
            </h3>
            <p class="font-medium text-lg mt-3 mb-6">
               {{__(' Выберите подходящий способ:')}}
            </p>
        </div>


        <!-- Tabs -->
        <div class="mx-auto my-8">
            <ul id="tabs"
                class="nav nav-tabs flex  text-center flex-wrap list-none border-b-0 pl-0 mb-2 justify-center">
                <li class="bg-white text-xl px-12 text-gray-800 font-semibold hover:bg-gray-200 py-2  @if(!$errors->has('phone_number'))  text-yellow-500 border-b-2 border-yellow-500 @endif ">
                    <a id="default-tab" href="#first">{{__('ЭЛ. ПОЧТА')}}</a>
                </li>
                <li class="px-12 text-xl text-gray-800 hover:bg-gray-200 font-semibold py-2  @if($errors->has('phone_number'))  text-yellow-500 border-b-2 border-yellow-500 @endif  ">
                    <a href="#second">{{__('СМС')}}</a>
                </li>
            </ul>
        </div>

        <!-- Tab Contents -->

        <div id="tab-contents" class="flex justify-center">
            <div id="first" class="p-2   @if($errors->has('phone_number')) hidden @endif">
                <form action="{{route('user.reset_submit_email')}}" method="POST">
                    @csrf
                    <div class="mx-auto flex items-center justify-center w-full">
                        <p class="mb-4">
                           {!!__('Укажите почту, привязанную к вашей <br> учетной записи. Мы отправим письмо <br> для восстановления пароля.')!!}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-500 text-sm mb-1" for="phone_number">
                            <span>{{__('Электронная почта')}}</span>
                        </label>
                        <input type="text" placeholder="Your email"
                               value="{{ old('email') }}" name="email"
                               id="email"
                               class="shadow appearance-none border focus:outline-none focus:border-yellow-500 rounded w-80 py-2 px-3 text-gray-700 mb-3 leading-tight">
                        @if(session()->has('message'))
                            <p class="text-red-500">{{session('message')}}</p>
                        @endif
                        @error('email')
                        <p class="text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                            class="w-80 h-12 rounded-lg bg-green-500 text-gray-200 uppercase   font-semibold hover:bg-green-500 text-gray-100 transition mb-4">
                        {{__('Отправить')}}
                    </button>
                </form>
            </div>
            <div id="second" class="p-2 @if(!$errors->has('phone_number')) hidden @endif">
                <div class="mx-auto flex items-center justify-center w-full">
                    <p class="mb-4">
                        {!!__("Укажите телефон, привязанный к вашей <br> учетной записи. Мы отправим СМС с кодом.")!!}
                    </p>
                </div>
                <form action="{{route('user.reset_submit')}}" method="POST">
                    @csrf
                    <div>
                        <div class="mb-4">
                            <label class="block text-gray-500 text-sm mb-1" for="phone_number">
                                <span>{{__('Телефон немер')}}</span>
                            </label>
                            <input type="text" placeholder="" name="phone_number"
                                   value="{{ request()->input('phone_number', old('phone_number')) }}"
                                   id="phone_number"
                                   class="shadow appearance-none border focus:outline-none focus:border-yellow-500 rounded w-80 py-2 px-3 text-gray-700 mb-3 leading-tight">
                            <br>
                            @if(session()->has('message'))
                                <p class="text-red-500">{{session('message')}}</p>
                            @endif
                            @error('phone_number')
                            <span class="text-danger" style="color: red">{{ $message  }}</span>
                            @enderror
                        </div>
                    </div>
                    <button type="submit"
                            class="w-80 h-12 rounded-lg bg-green-500 text-gray-200 uppercase font-semibold hover:bg-green-500 text-gray-100 transition mb-4">
                        {{__('Отправить')}}
                    </button>
                </form>
            </div>
        </div>


    </div>
    <script>
        //tab content script start
        let tabsContainer = document.querySelector("#tabs");
        let tabTogglers = tabsContainer.querySelectorAll("#tabs a");
        console.log(tabTogglers);
        tabTogglers.forEach(function (toggler) {
            toggler.addEventListener("click", function (e) {
                e.preventDefault();
                let tabName = this.getAttribute("href");
                let tabContents = document.querySelector("#tab-contents");
                for (let i = 0; i < tabContents.children.length; i++) {
                    tabTogglers[i].parentElement.classList.remove("text-yellow-500", "border-b-2", "border-yellow-500");
                    tabContents.children[i].classList.remove("hidden");
                    if ("#" + tabContents.children[i].id === tabName) {
                        continue;
                    }
                    tabContents.children[i].classList.add("hidden");
                }
                e.target.parentElement.classList.add("text-yellow-500", "border-b-2", "border-yellow-500");
            });
        });

        $('#second').click(function () {
            $(this).removeClass('hidden');
        })
        //tab content script end
    </script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js'></script>
    <script>
        //phone number js
        var element = document.getElementById('phone_number');
        var maskOptions = {
            mask: '+998(00)000-00-00',
            lazy: false
        }
        var mask = new IMask(element, maskOptions);
    </script>
@endsection
