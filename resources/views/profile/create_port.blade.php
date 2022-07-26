@extends("layouts.app")

@section("content")


    <div class="w-10/12 mx-auto mt-8">
        <form action="{{ route('profile.createPortfolio') }}" method="post">
            @csrf
            <div class="lg:w-3/5 w-full">
                <div>
                    <a class="text-sm text-blue-500 hover:text-red-500" href="/profile"><i
                            class="fas fa-arrow-left"></i> {{__('Вернуться к профилю')}}</a>
                    <h1 class="font-semibold md:text-2xl text-lg ">{{__('Создание нового альбома')}}</h1>
                </div>
                <div id="comdes" class="bg-yellow-100 p-8 rounded-md my-6">
                    <label class="text-sm" for="name">{{__('Название')}}</label><br>
                    <input name="comment"
                           class="border break-all focus:outline-none focus:border-yellow-500 mb-6 text-sm border-gray-200 rounded-md w-full px-4 py-2"
                           type="text" placeholder='{{__('Например: Ремонт кухни')}}'>
                    @error('comment')
                    <p>{{ $message }}</p>
                    @enderror

                    <label class="text-sm" for="textarea">{{__('Описание')}}</label><br>
                    <textarea name="description" placeholder='{{__('Опишите какие работы представлены в этом альбоме, в чем их особенность, когда они были выполнены, в каких целях и т.д.')}}' required
                              class="border break-all text-sm mb-8 focus:outline-none focus:border-yellow-500 border-gray-200 rounded-md w-full px-4 py-2"
                              cols="30" rows="10"></textarea>
                    <div class="text-center mx-auto text-base">
                        <input id="button1" type="button"
                               class="bg-green-500 hover:bg-green-700 text-white cursor-pointer py-2 px-10 mb-4 rounded"
                               value="{{__('Далее')}}">
                        @error('comment')
                        <p>{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <div id="comdes1" class="text-center h-full w-full text-base hidden">
                        @csrf
                        <div id="photos" class="bg-yellow-50 p-8 rounded-md my-6"></div>
                        <input type="submit"
                               class="bg-green-500 hover:bg-green-700 text-white py-2 px-10 mb-4 cursor-pointer rounded"
                               value="{{__('Сохранить')}}">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/profile/create_port.js') }}"></script>

    <x-laravelUppy route="{{route('profile.UploadImage')}}"/>

@endsection
