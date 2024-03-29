@extends("layouts.app")

@section("content")
    <script>
        let var_for_id_task = null;
    </script>
    <div class="mx-auto sm:w-9/12 w-11/12 my-16">
        <div class="grid grid-cols-3   lg:gap-x-8 md:gap-x-0.5 h-full">
            <div class="lg:col-span-2 col-span-3">
                <div class="w-full text-center md:text-2xl text-xl">
                    {{__('Поможем найти исполнителя для вашего задания')}}
                </div>
                <div class="w-full text-center my-4 text-gray-400">
                    {{__('Задание заполнено на 14%')}}
                </div>
                <div class="pt-1">
                    <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200 mx-auto ">
                        <div style="width: 14%"
                             class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-yellow-500"></div>
                    </div>
                </div>
                <div class="shadow-2xl w-full lg:p-8 p-4 mx-auto my-4 rounded-2xl	w-full">
                    <div class="py-4 md:w-1/2 w-full mx-auto px-auto text-center md:text-3xl text-xl texl-bold">
                        {{__('Чем вам помочь?')}}
                    </div>
                    <form action="{{route('task.create.name.store')}}" method="post">
                        @csrf
                        <input type="hidden" name="category_id" value="{{$current_category->id}}">

                        <div class="py-4 w-11/12 mx-auto px-auto text-left my-4">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-base mb-2" for="username">
                                    {{__('Название задания')}}
                                </label>
                                <input class="shadow sm:text-base text-sm  border focus:shadow-orange-500 rounded-lg w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none
                                    focus:border-yellow-500" id="username" type="text" autofocus="autofocus"
                                    placeholder="{{__('Например, ')}} {{ $current_category->getTranslatedAttribute('name') }}"
                                    name="name" value="{{session('neym')}}">
                            </div>
                            @error('name')
                                <p class="text-red-500">{{ $message }}</p>
                            @enderror
                            <div class="hidden" id="naprimer">{{__('Например, ')}} </div>
                            <p class="text-base text-gray-700 mt-10">{{__('Если хотите выбрать другую категорию')}}</p>
                            <div id="categories">
                                <div class="flex lg:flex-row flex-col">
                                    <div class="lg:w-1/2 w-full lg:pr-3 py-5">
                                        <select class="select2 parent-category"
                                                style="width: 100%">
                                            @foreach($categories as $parentCategory)
                                                <option value="{{ $parentCategory->id }}" @selected($parentCategory->id === $current_category->parent_id)> {{ $parentCategory->getTranslatedAttribute('name') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="lg:w-1/2 w-full lg:pl-3 py-5">
                                        @foreach($categories as $category)
                                            <div class="hidden child-category child-category-{{ $category->id }}">
                                                <select class="select2 child-category1" style="width: 100%">
                                                    @foreach($child_categories as $child)
                                                        <option value="{{ $child->id }}" @selected($current_category->id === $child->id) class="hidden" data-parent="{{ $child->parent_id }}">{{ $child->getTranslatedAttribute('name') }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                        </div>
                        <div class="flex  mx-auto" >
                            <input type="submit" id="next" style="background: linear-gradient(164.22deg, #FDC4A5 4.2%, #FE6D1D 87.72%);"
                                   class="bg-yellow-500 hover:bg-yellow-600 my-4 cursor-pointer text-white font-normal text-2xl py-3 px-14 px-8 rounded-2xl"
                                   name="" value="{{__('Oтправить')}}">
                        </div>

                        </div>

                    </form>
                </div>
            </div>

            <x-faq>
            </x-faq>
        </div>
    </div>
    <!-- </form> -->

    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/js/maximize-select2-height.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{asset('css/name.css')}}">
    <script>
        $('.select2').select2({
            //minimumResultsForSearch: Infinity,
        }).maximizeSelect2Height()

        let parentCategory = $(".parent-category").val();

        $(".parent-category").change(function (){
            $('.child-category').removeClass('hidden')
            $('.child-category').addClass('hidden')
            $('.child-category-'+$(this).val()).removeClass('hidden')
            window.location.href = "/task/create?category_id=" +
             $('*[data-parent="' + $(this).val() + '"]:first').val();
            $('#username').attr('placeholder',$('#naprimer').text() + $('*[data-parent="' + $(this).val() + '"]:first').text())
        })
        $('.child-category1').change(function (){
            window.location.href = "/task/create?category_id=" + $(this).val();
        })
        $('.child-category-'+parentCategory+'').removeClass('hidden')

        $('#username').keyup(function (){
            sessionStorage.setItem("name", $(this).val())
        })

        var name = sessionStorage.getItem("name")
        if (name !== 'null'){
            $('#username').val(name)
        }else {
            $('#username').val("")
        }

        $('#next').click(function (){
            sessionStorage.clear();
        })
    </script>

@endsection


@push("javascript")
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
@endpush
