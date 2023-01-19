@extends('layouts.app')

@section('content')
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <div class="text-sm w-full bg-gray-200 my-4 py-3">
        <p class="w-8/12 mx-auto text-gray-500 font-normal">{{__('Вы находитесь в разделе исполнителей USer.Uz')}} <br>
            {{__("Чтобы предложить работу выбранному исполнителю, нужно нажать на кнопку «Предложить задание» в его профиле.")}}</p>
    </div>
    <div class="xl:w-9/12 container mx-auto mt-16 text-base">
        <div class="grid grid-cols-3 ">

            {{-----------------------------------------------------------------------------------}}
            {{--                             Left column                                       --}}
            {{-----------------------------------------------------------------------------------}}

            <div class="lg:col-span-1 col-span-3 px-8">
                <a href="/verification" class="flex flex-row shadow-lg rounded-lg mb-8">
                    <div class="w-1/2 h-24 bg-contain bg-no-repeat bg-center"
                         style="background-image: url({{asset('images/like.png')}});">
                    </div>
                    <div class="font-bold text-xs text-gray-700 text-left my-auto">
                        {!!__('Станьте исполнителем <br> USer.Uz. И начните  <br> зарабатывать')!!}
                    </div>
                </a>
                <div>
                    <div class="max-w-md mx-left">
                        @foreach ($categories as $category)
                            <div x-data={show:false} class="rounded-sm">
                                <div class="my-3 text-blue-500 hover:text-red-500 cursor-pointer"
                                     id="{{ preg_replace('/[ ,]+/', '', $category->name) }}">
                                    {{ $category->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}
                                </div>
                                <div id="{{$category->slug}}" class="px-8 py-1 hidden">
                                    @foreach ($categories2 as $category2)
                                        @if($category2->parent_id == $category->id)
                                            <div>
                                                <a href="/perf-ajax/{{ $category2->id }}"
                                                   class="text-blue-500 cursor-pointer hover:text-red-500 my-1 send-request"
                                                   data-id="{{$category2->id}}">{{ $category2->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}</a>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 col-span-3 lg:mt-0 mt-16">
                <div class="font-bold text-2xl mx-8 py-4">
                    <p>{{__('Все исполнители')}}</p>
                </div>

                <table class="table yajra-datatable">
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
        $(function () {
            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('performers.list') }}",
                columns: [
                    {data: 'user_images'},
                    {data: 'user_information'},
                ]
            });
        });
    </script>
    @include('performers.performers_modal')
    @include('performers.performer_script')
@endsection

