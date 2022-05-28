<div class="w-4/5 mx-auto text-center pt-10">
    <div class="text-3xl font-bold">
        {{__('Более')}}  {{__('исполнителей')}}
    </div>
    <div class="text-base mt-4">
        {{__('Исполнители готовы помочь вам в решении самых разнообразных задач')}}
    </div>
    <div class="my-14 w-full text-center md:block hidden">
        @foreach($categories as $category2)
            <button type="button"
                    class="bg-inherit hover:text-yellow-500 border py-1 rounded-full px-4 my-2 mx-2 text-gray-700 border-gray-400 text-left md:text-center text-md md:inline-block block">
                <span class="flex w-full flex-wrap content-center items-center">
                    <img src=" {{ asset('storage/'.$category2->ico) }}" alt="" class="h-8 w-8">
                <a class="text-sm p-3" href="{{route('categories',['id'=>$category2->id])}}">
                    {{$category2->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale')}}
                </a>
                </span>
            </button>
        @endforeach
    </div>

    <div class="flex flex-col float-left my-10 w-full md:hidden block">
        @foreach ($categories as $category2)  
            <span class="flex w-full flex-row items-center sm:my-2 my-1">
                <img src="{{ asset('storage/'.$category2->ico) }}" alt="" class="h-8 w-8"></i>
                <a href="{{route('categories', ['id'=> $category2->id])}}"
                class="sm:ml-4 ml-2 sm:text-base text-xs text-gray-600 hover:text-yellow-500">
                    {{ $category2->getTranslatedAttribute('name', Session::get('lang') , 'fallbackLocale' )}}
                </a>
            </span>
        @endforeach
    </div>
</div>
