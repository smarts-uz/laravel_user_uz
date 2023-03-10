@extends('layouts.app')

@include('layouts.fornewtask')

@section('content')
    <!-- Information section -->
    <x-roadmap/>
    <form class="" action="{{route('task.create.note.store', $task->id)}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="mx-auto sm:w-9/12 w-11/12 my-8">
            <div class="grid md:grid-cols-3 lg:gap-x-20 md:gap-x-14">
                <div class="lg:col-span-2 col-span-3">
                    <div class="w-full text-center text-2xl">
                        @if(session('lang') === 'ru')
                            {{__('Ищем исполнителя для задания')}} "{{$task->name}}"
                        @else
                            "{{$task->name}}" {{__('Ищем исполнителя для задания')}}
                        @endif
                    </div>
                    <div class="w-full text-center my-4 text-gray-400">
                        {{__('Задание заполнено на 90%')}}
                    </div>
                    <div class="relative pt-1">
                        <div class="overflow-hidden h-1  flex rounded bg-gray-200  mx-auto ">
                            <div style="width: 90%"
                                 class="shadow-none  flex flex-col text-center whitespace-nowrap text-white justify-center bg-yellow-500"></div>
                        </div>
                    </div>
                    <div class="shadow-xl w-full mx-auto mt-7 rounded-2xl px-6 mb-6 p-6 md:px-20">
                        <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
                            {{__('Уточните детали')}}
                        </div>

                        <div class="py-4 mx-auto text-left">
                            <div class="mb-4">
                                <div id="formulario" class="flex flex-col gap-y-4">
                                    <div class="">
                                        <div class="mb-3 xl:w-full">
                                            <label for="exampleFormControlTextarea1"
                                                   class="form-label inline-block mb-2 text-gray-700">
                                                {{__('Описание')}}</label>
                                            <textarea name="description" autofocus="autofocus" required
                                                      class="form-control block resize-none w-full h-36  px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:outline-none focus:border-yellow-500"
                                                      placeholder="{{__('Например')}}{{$task->category->placeholder}}">
                                            </textarea>
                                            @error('description')
                                                <p class="text-lg text-red-500 ">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="w-full text-center flex justify-center items-center gap-2"
                                         type="button">
                                        <div id="photos" class="w-full"></div>
                                    </div>
                                    <div class="my-6">
                                        <label class="md:w-2/3 block mt-6">
                                            <input class="focus:outline-none  mr-2 h-4 w-4" type="checkbox" name="docs">
                                            <span class="text-slate-900">
                                                {{__('Предоставить документы')}}
                                                <br>
                                                <p class="text-sm text-slate-500">
                                                    {{__('Для оформления расписки/доверенности')}}
                                                </p>
                                            </span>
                                        </label>
                                        <label class="md:w-2/3 block mt-6">
                                            <input class="focus:outline-none  mr-2 h-4 w-4" type="radio" checked
                                                   name="oplata" value="0">
                                            <span class="text-slate-900">
                                                {{__('Оплата через карту')}}
                                            </span>
                                        </label>
                                        <label class="md:w-2/3 block mt-6">
                                            <input class="focus:outline-none  mr-2 h-4 w-4" type="radio" name="oplata" value="1">
                                            <span class="text-slate-900">
                                                {{__('Оплата наличными')}}
                                            </span>
                                        </label>

                                        @include('create.custom-fields2')

                                    </div>
                                    <div class="flex w-full gap-x-4 mt-4">
                                        <a onclick="myFunction()"
                                           class="bg-white my-4 cursor-pointer hover:border-yellow-500 text-gray-600 hover:text-yellow-500 transition duration-300 font-normal text-base py-3 sm:px-8 px-6 rounded-2xl  border border-2">
                                            <!-- <button type="button"> -->
                                            {{__('Назад')}}
                                            <!-- </button> -->
                                            <script>
                                                function myFunction() {
                                                    window.history.back();
                                                }
                                            </script>
                                        </a>
                                        <input type="submit"
                                               style="background: linear-gradient(164.22deg, #FDC4A5 4.2%, #FE6D1D 87.72%);"
                                               class="bg-yellow-500 hover:bg-yellow-600 m-4 cursor-pointer text-white font-normal text-xl py-3 sm:px-14 px-8 rounded-2xl "
                                               name="" value="{{__('Далее')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <x-faq/>

            </div>
        </div>

    </form>
    <x-laravelUppy url="{{route('task.create.images.store', $task->id)}}" target="#photos" fieldName="images[]" height="400" width="1000"/>
@endsection

@push("javascript")
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
@endpush
