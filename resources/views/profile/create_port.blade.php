@extends("layouts.app")

@section("content")


    <div class="w-10/12 mx-auto mt-8">
        <form action="{{ route('profile.createPortfolio') }}" method="post">
            @csrf
            <div class="lg:w-3/5 w-full">
                <div>
                    <a class="text-sm text-blue-500 hover:text-red-500" href="/profile">
                        <i class="fas fa-arrow-left"></i> {{__('Вернуться к профилю')}}
                    </a>
                    <h1 class="font-semibold md:text-2xl text-lg ">{{__('Создание нового альбома')}}</h1>
                </div>
                <div class="bg-yellow-100 p-8 rounded-md my-6">
                    <label class="text-sm" for="name">{{__('Название')}}</label><br>
                    <input name="comment"
                           class="border break-all focus:outline-none focus:border-yellow-500 mb-1 text-sm border-gray-200 rounded-md w-full px-4 py-2"
                           type="text" placeholder='{{__('Например: Ремонт кухни')}}'>
                    @error('comment')
                        <p class="text-red-500">{{ $message }}</p>
                    @enderror

                    <label class="text-sm" for="textarea">{{__('Описание')}}</label><br>
                    <textarea name="description" placeholder='{{__('Опишите какие работы представлены в этом альбоме, в чем их особенность, когда они были выполнены, в каких целях и т.д.')}}'
                              class="border break-all text-sm focus:outline-none focus:border-yellow-500 border-gray-200 rounded-md w-full px-4 py-2"
                              cols="30" rows="10"></textarea>
                    @error('description')
                        <p class="text-red-500">{{ $message }}</p>
                    @enderror
                    <div id="photos" class="bg-yellow-50 rounded-md my-6"></div>
                    <input type="submit" id="save_button"
                           class="bg-green-500 hover:bg-green-700 text-white py-2 px-10 mb-4 cursor-pointer rounded hidden"
                           value="{{__('Сохранить')}}">
                </div>
            </div>
        </form>
    </div>

{{--    <script src="{{ asset('js/profile/create_port.js') }}"></script>--}}
    <x-laravelUppy route="{{route('profile.UploadImage')}}"/>
    <script>
        $('.uppy-StatusBar-actions').click(function (){
            $('#save_button').removeClass('hidden');
        })
    </script>
@endsection
