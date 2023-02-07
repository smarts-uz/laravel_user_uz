@extends("layouts.app")

@section("content")
    <div class="container w-10/12 mx-auto sm:my-12 my-4 text-base">
        <div class="text-center">
            <h1 class="text-3xl pt-5 md:text-5xl font-semibold">{{__('Выберите категорию задания')}}</h1>
            <h3 class="text-lg my-5 font-medium text-grey-400  mb-8">{{__('Мы готовы помочь вам в решении самых разнообразных задач')}}</h3>
            <div class="max-w-full container mx-auto lg:hidden">
                @foreach ($categories as $category2)
                    <span class="flex w-full flex-row items-center sm:my-2 my-1">
                        <img src="{{asset('storage/'.$category2->ico) }}" alt="" class="h-8 w-8"></i>
                        @if ($category2->id === $choosed_category->id)
                            <a href="{{route('categories', ['id'=> $category2->id])}}"
                            class="sm:ml-4 ml-2 sm:text-base text-xs text-yellow-500">
                                {{ $category2->getTranslatedAttribute('name', Session::get('lang') , 'fallbackLocale' )}}
                            </a>
                        @else
                            <a href="{{route('categories', ['id'=> $category2->id])}}"
                            class="sm:ml-4 ml-2 sm:text-base text-xs text-gray-600 hover:text-yellow-500">
                                {{ $category2->getTranslatedAttribute('name', Session::get('lang') , 'fallbackLocale' )}}
                            </a>
                        @endif
                    </span>
                @endforeach
            </div>
            <div class="hidden lg:block">
                @foreach($categories as $category)
                    <button type="button"
                            class="bg-inherit hover:text-yellow-500 border py-1 rounded-full px-4 my-2 mx-2 text-gray-500 border-gray-300 text-left md:text-center text-md md:inline-block block">
                        <span class="flex w-full flex-wrap content-center items-center">
                            <img src="{{asset('storage/'.$category->ico) }}" alt="" class="h-8 w-8">
                            @if ($category->id === $choosed_category->id)
                                <a class="text-yellow-500 text-sm p-1" href="{{route('categories',['id'=>$category->id])}}">
                                    {{$category->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale')}}
                                </a>
                            @else
                                <a class="text-sm p-1" href="{{route('categories',['id'=>$category->id])}}">
                                    {{$category->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale')}}
                                </a>
                            @endif
                        </span>

                    </button>
                @endforeach
            </div>

        </div>
        <div class="w-full ml-4 md:text-left md:m-0">
            <h4 class="font-bold sm:text-3xl text-2xl mt-14 ">{{$choosed_category->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale')}}</h4>
        </div>
        <div class="flex flex-wrap  mt-8">
            @foreach($child_categories as $category)
                <div class="lg:w-1/3 w-full text-left my-2">
                    <a href="/task/create?category_id={{$category->id}}">
                        <span
                            class="text-gray-900 sm:text-base text-sm hover:text-yellow-600 block hover:underline">{{$category->getTranslatedAttribute('name', Session::get('lang') , 'fallbackLocale')}}</span>
                    </a>
                    <hr class="mt-4 lg:hidden block">
                </div>
            @endforeach
        </div>
    </div>
@endsection
