@extends("layouts.app")

@section("content")
<div class="shadow-2xl border-t px-10 rounded-md w-full md:w-6/12 mx-auto grid grid-flow-col gap-4 my-5 flex flex-wrap md:flex-wrap-reverse">

    <div сlass="">
        <div class="container sm:p-5 p-0">
            <p class="text-2xl font-semibold mb-3 text-center">
                {{__('Kак с вами связаться?')}}
            </p>
            <p class="text-base mt-3 text-center md:px-10 px-1">
                {{__('Заказчики будут контактировать с вами по телефону, либо в чате. На почту будем присылать уведомления.')}}
            </p>
            <form action="{{route('profile.verificationContactStore')}}" method="Post" class="my-10">
                @csrf
                <div class="mt-3 mb-3">
                    <label class="text-gray-500 text-sm" for="lastname"> {{__('Электронная почта')}}</label>
                    <input type="email" id="lastname" name="email" value="{{auth()->user()->email}}" class="block px-2 w-full border  border-grey-300 py-2 rounded-lg shadow-sm focus:outline-none focus:border-yellow-500" />
                </div>
                <div class="mt-3">
                    <label class="text-gray-500 text-sm" for="name"> {{__('Телефон')}}</label>
                    <input type="text" id="phone_number" value="{{auth()->user()->phone_number}}"
                    placeholder="+998(00)000-00-00" name="phone_number"
                      class="block px-2 w-full border  border-grey-300 py-2 rounded-lg shadow-sm focus:outline-none focus:border-yellow-500" />
                    @error('phone_number')
                        <p class="text-red-500"> {{ $message }}</p>
                    @enderror
                </div>
                <div class="flex w-full gap-x-4 mt-4">
                <a onclick="myFunction()" class="w-1/3  border border-black-700 hover:border-black transition-colors rounded-lg py-2 text-center flex justify-center items-center gap-2">
                    <!-- <button type="button"> -->
                    {{__('Назад')}}

                    <!-- </button> -->
                    <script>
                        function myFunction() {
                            window.history.back();
                        }
                    </script>
                </a>
                <input type="submit" class="bg-green-500 hover:bg-green-500 w-2/3 cursor-pointer text-white font-bold py-5 px-5 rounded" name="" value="{{__('Далее')}}">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js"></script>
<script src="{{ asset('js/personalinfo/contact.js') }}"></script>
