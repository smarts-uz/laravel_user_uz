@extends("layouts.app")

@section("content")
<div class="sm:shadow-2xl shadow-none sm:border-t border-none sxXE sm:px-10 px-0 rounded-md w-full md:w-7/12 mx-auto grid grid-flow-col gap-4 my-5 flex flex-wrap md:flex-wrap-reverse">
    @php
        $user = auth()->user();
    @endphp
    <div сlass="grid-rows-12">
        <div class="container p-5">
            <h3 class="text-2xl font-semibold text-center">{{__('Чем вы хотите заниматься?')}}</h3>
            <p class="text-base text-center my-5">
                {{__('Выберите категории заданий, в которых хотите работать. Можно сразу несколько — изменить их всегда можно в профиле.')}}
            </p>
                <form action="{{route('profile.getCategory')}}" method="post">
                    @csrf
                    <div class="acordion mt-16">
                        @foreach ($categories as $category )

                            <div class="mb-4 rounded-md border shadow-md px-2">
                                <div class="accordion text-gray-700 cursor-pointer w-full text-left text-lg flex items-center gap-x-2">
                                    <input type="checkbox" id="selectall{{$category->id}}" class="h-3 w-3">
                                    {{ $category->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}
                                    <h1 class="text-blue-500">[<span id="count{{$category->id}}">0</span>]</h1>
                                </div>
                                <div class="panel overflow-hidden hidden px-[18px] bg-white p-2">
                                    @foreach ($categories2 as $category2)
                                        @if($category2->parent_id === $category->id)
                                            <label class="block for_check{{$category->id}}">
                                                @php
                                                    $res_c_arr = array_search($category2->id,$user_categories);
                                                @endphp
                                                <input type="checkbox" @if($res_c_arr !== false) checked @endif name="category[]"
                                                    value="{{$category2->id}}"
                                                    class="checkbox{{$category->id}} mr-2 required:border-yellow-500">{{ $category2->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                <div class="flex w-full gap-x-4 mt-4">
                    <a onclick="myFunction()" class="w-1/3  border border-black-700 hover:border-black transition-colors rounded-lg py-2 text-center flex justify-center items-center gap-2">
                        <!-- <button type="button"> -->
                        {{__('Назад')}}

                        <!-- </button> -->
                    </a>

                    <input type="submit"  class="bg-green-500 hover:bg-green-500 w-2/3 cursor-pointer text-white font-bold py-5 sm:px-5 px-1 rounded" name="" value="{{__('Стать исполнителем')}}">

                </div>
            </form>
        </div>
    </div>
</div>
@foreach($categories as $category)
    <script>
        $('#selectall{{$category->id}}').click(function() {
            if (this.checked === false) {
                $(".for_check{{$category->id}} input:checkbox").each(function() {
                    this.checked = false;
                });
                $('#count{{$category->id}}').text($('.checkbox{{$category->id}}').filter(":checked").length);
            } else {
                $(".for_check{{$category->id}} input:checkbox").each(function() {
                    this.checked = true;
                });
                $('#count{{$category->id}}').text($('.checkbox{{$category->id}}').filter(":checked").length);
            }
        });
        $('.checkbox{{$category->id}}').change(function () {
            var check = ($('.checkbox{{$category->id}}').filter(":checked").length === $('.checkbox{{$category->id}}').length);
            $('#selectall{{$category->id}}').prop("checked", check);
            $('#count{{$category->id}}').text($('.checkbox{{$category->id}}').filter(":checked").length);
        });
        $('#count{{$category->id}}').text($('.checkbox{{$category->id}}').filter(":checked").length);
    </script>
@endforeach
@endsection
@push('javascript')
    <script src="{{ asset('js/personalinfo/personalcategoriya.js') }}"></script>
@endpush
