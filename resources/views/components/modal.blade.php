@if(!session()->has('not-show'))

    @auth()
        @if(!session()->has('code'))

            @if(!auth()->user()->is_phone_number_verified)
                <div x-data="{ showModal : true }" class="">

                    <!-- Modal Background -->
                    <div x-show="showModal"
                         class="fixed flex items-center justify-center overflow-auto bg-black bg-opacity-40 left-0 right-0 top-0 bottom-0"
                         x-transition:enter="transition ease duration-300" x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100" x-transition:leave="transition ease duration-300"
                         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         style="z-index:500">


                        <!-- Modal -->
                        <div x-show="showModal" class="bg-white rounded-xl shadow-2xl p-6 sm:w-10/12 lg:w-5/12 mx-10"
                             @click.away="showModal = false" x-transition:enter="transition ease duration-100 transform"
                             x-transition:enter-start="opacity-0 scale-90 translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease duration-100 transform"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-90 translate-y-1">
                            <div class="mx-auto pl-5 py-5 text-black">
                                <div class="text-right -mt-5 ">
                                    <button @click="showModal = !showModal"
                                            class="px-4 py-2 text-sm bg-white rounded-xl border transition-colors duration-150 ease-linear border-gray-200 text-gray-500 focus:outline-none focus:ring-0 font-bold hover:bg-gray-50 focus:bg-indigo-50 focus:text-indigo">
                                        x
                                    </button>
                                </div>


                                <div
                                    class="text-2xl md:w-[500px] -mt-5 font-bold font-['Radiance,sans-serif,Noto Sans']">
                                    {{__('Подтвердите номер телефона')}}
                                </div>
                                <p class="my-8 text-gray-700 ">
                                    {{__('На ваш телефонный номер')}}  <strong>{{correctPhoneNumber(auth()->user()->phone_number)}}</strong>
                                      {{__('было отправлено письмо со кодом для подтверждения вашего аккаунта на Universal Services.')}}
                                </p>
                                <p class="my-8 text-gray-700 ">
                                    {{__('Отправить новый код для подтверждения телефонный номер')}}
                                </p>

                                <a class='text-yellow-500 send-email border-b hover:text-red-500 sent-email border-dotted @if($errors->has('phone_number') || session()->has('email-message') || !auth()->user()->phone_number)) hidden @endif border-gray-700 cursor-pointer'
                                   href="{{route('login.send_phone_verification')}}">
                                    {{__('Отправить новый код для подтверждения телефонный номер')}}</a><br>


                                <a class='text-yellow-500 hover:text-red-500 border-b border-dotted border-gray-700 @if($errors->has('phone_number') || session()->has('email-message') || !auth()->user()->phone_number) ) hidden @endif change-email cursor-pointer'>
                                    {{__('Указать другой телефонный номер')}}</a>


                                <form action="{{route('login.change_phone_number')}}" id="send-data-form"
                                      class="@if(!($errors->has('phone_number') || session()->has('email-message') || !auth()->user()->phone_number) || session()->has('code') ) hidden @endif"
                                      method="post">
                                    @csrf

                                    <a class='text-gray-800 hover:text-red-500 border-b sent-email border-dotted border-gray-700 cursor-pointer'
                                       id="cancel-email">{{__('Отмена')}}</a>
                                    <br>
                                    <div class="my-2">
                                        <input name="phone_number" type="text" placeholder="{{__('Номер телефона')}}" id="phone_number"
                                               value="{{  old('email').session()->has('email') ? session('email'):null  }}"
                                               class="shadow focus:outline-none  focus:border-yellow-500 appearance-none border border-slate-300 rounded
                        w-full py-2 px-3 text-gray-700 mb-1 leading-tight hover:border-amber-500"
                                               autofocus>
                                        @if(session()->has('email-message'))
                                            <p class="text-red-500"> {{ session('email-message') }}</p>
                                        @endif

                                        @error('phone_number')
                                        <p class="text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button class="w-full h-12 rounded-lg bg-yellow-500 text-gray-200 uppercase
                        font-semibold hover:bg-yellow-500 text-gray-100 transition mb-4">
                                        {{__('Отправить')}}
                                    </button>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>
            @endif

        @elseif(session()->has('code'))

            <div x-data="{ showModal : true }" class="">

                <!-- Modal Background -->
                <div x-show="showModal"
                     class="fixed flex items-center justify-center overflow-auto z-50 bg-opacity-40 left-0 right-0 top-0 bottom-0"
                     x-transition:enter="transition ease duration-300" x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100" x-transition:leave="transition ease duration-300"
                     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">


                    <!-- Modal -->
                    <div x-show="showModal" class="bg-white rounded-xl shadow-2xl p-6 sm:w-10/12 lg:w-5/12 mx-10"
                         @click.away="showModal = false" x-transition:enter="transition ease duration-100 transform"
                         x-transition:enter-start="opacity-0 scale-90 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease duration-100 transform"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-90 translate-y-1">
                        <div class="mx-auto pl-5 py-5 text-black">
                            <div class="text-right -mt-5 ">
                                <button @click="showModal = !showModal"
                                        class="px-4 py-2 text-sm bg-white rounded-xl border transition-colors duration-150 ease-linear border-gray-200 text-gray-500 focus:outline-none focus:ring-0 font-bold hover:bg-gray-50 focus:bg-indigo-50 focus:text-indigo">
                                    x
                                </button>
                            </div>

                            <form action="{{route('login.verify_phone')}}" method="post">
                                @csrf
                                <input type="text" placeholder="{{__('Код')}}" name="code"
                                       class="shadow focus:outline-none  focus:border-yellow-500 appearance-none border border-slate-300 rounded
                        w-full py-2 px-3 text-gray-700 mb-1 leading-tight hover:border-amber-500"
                                       autofocus>

                                <p class="text-blue-500">{{session('code')}}</p>

                                @error('code')
                                <p class="text-red-500">{{ $message }}</p>
                                @enderror
                                <button class="w-full h-12 rounded-lg bg-yellow-500 text-gray-200 uppercase
                        font-semibold hover:bg-yellow-500 text-gray-100 transition mb-4">
                                    {{__('Отправить')}}
                                </button>

                            </form>


                        </div>
                    </div>
                </div>
            </div>

        @endif

    @endauth

        <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js"></script>
        <script src="{{ asset('js/components/modal.js') }}"></script>
@endif
