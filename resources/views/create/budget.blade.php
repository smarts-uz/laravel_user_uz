@extends('layouts.app')

@include('layouts.fornewtask')

@section('content')
    <link rel="stylesheet" href="{{asset('css/budget_library.css')}}">
    <link rel="stylesheet" href="{{asset('css/budjet.css')}}">

    <form class="" action="{{route('task.create.budget.store', $task->id)}}" method="post">
        @csrf
        <div class="mx-auto sm:w-9/12 w-11/12 my-16">
            <div class="grid grid-cols-3 gap-x-20">
                <div class="lg:col-span-2 col-span-3">
                    <div class="w-full text-center text-2xl">
                        {{__('Ищем исполнителя для задания')}} "{{$task->name}}"
                    </div>
                    <div class="w-full text-center my-4 text-gray-400">
                        {{__('Задание заполнено на 75%')}}
                    </div>
                    <div class="relative pt-1">
                        <div class="overflow-hidden h-1 text-xs flex rounded bg-gray-200  mx-auto ">
                            <div style="width: 75%" class="shadow-none  flex flex-col text-center whitespace-nowrap text-white justify-center bg-yellow-500"></div>
                        </div>
                    </div>
                    <div class="shadow-xl w-full mx-auto md:mt-7 rounded-2xl  w-full p-6 md:px-20">
                        <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
                            {{__('На какой бюджет вы рассчитываете?')}}
                        </div>
                        <div class="py-4 mx-auto  text-left ">
                            <div class="mb-4">
                                <div class="content__38cf1 w-8/12 mx-auto">
                                    <div class="">
                                        <div class="wrapper__1144f white__d3db2">
                                            <div class="desktop__66de4">
                                                <div class="container__dbb1e">
                                                    <div class="rails__0ca6e">
                                                        <svg class="triangle__67899" width="1" height="1" viewBox="0 0 1 1" preserveAspectRatio="none" xmlns="https://www.w3.org/2000/svg">
                                                            <path class="back__d97a2" d="M0,1 L1,0 L1,1 L0,1"></path>
                                                        </svg>
                                                        <svg class="triangle__678999" width="1" height="1" viewBox="0 0 1 1" preserveAspectRatio="none" xmlns="https://www.w3.org/2000/svg">
                                                            <path class="back__d97a222" d="M0,1 L1,0 L1,1 L0,1"></path>
                                                        </svg>
                                                        <div id="slider-range-min" class="flex">
                                                            <div class="ui-slider-handle" style="left: 20%;"  data-tooltip-target="tooltip-light" data-tooltip-style="light" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="tooltip-light" role="tooltip" class="inline-block shadow-xl absolute visible py-2 px-1 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 shadow-sm opacity-100 tooltip" style="z-index: 100;!important;">
                                                        <input class="focus:outline-none text-center text-yellow-500 text-xl" type="text" id="amount2" name="amount" readonly >
                                                    </div>
                                                    <div class="handle__27597">

                                                        <input class="focus:outline-none focus:border-yellow-500  mt-8" type="text" id="amount" name="amount1" readonly >

                                                    </div>
                                                    <div class="tickWrapper__6685b" style="width: 16.6667%; left: 0%;">
                                                        <div class="dot__b4c97"></div>
                                                        <div class="bar__f0e59"></div>
                                                    </div>
                                                    <div class="tickWrapper__6685b" style="width: 16.6667%; left: 20%;">
                                                        <div class="dot__b4c97"></div>
                                                        <div class="bar__f0e59"></div>
                                                    </div>
                                                    <div class="tickWrapper__6685b" style="width: 16.6667%; left: 40%;">
                                                        <div class="dot__b4c97"></div>
                                                        <div class="bar__f0e59"></div>
                                                    </div>
                                                    <div class="tickWrapper__6685b" style="width: 16.6667%; left: 60%;">
                                                        <div class="dot__b4c97"></div>
                                                        <div class="bar__f0e59"></div>
                                                    </div>
                                                    <div class="tickWrapper__6685b" style="width: 16.6667%; left: 80%;">
                                                        <div class="dot__b4c97"></div>
                                                        <div class="bar__f0e59"></div>
                                                    </div>
                                                    <div class="tickWrapper__6685b" style="width: 16.6667%; left: 100%;">
                                                        <div class="dot__b4c97"></div>
                                                        <div class="bar__f0e59"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>




                            </div>
                                <div class="w-[100px]  md:w-[200px] md:hidden text-center">
                                    <select id="" name="amount" class="border md:ml-14 bg-yellow-400  text-white font-semibold rounded-lg text-lg md:text-2xl my-4 py-3 px-10 hover:bg-yellow-600">
                                        <option value="0">
                                            {{__('Выберите бюджет')}}
                                        </option>
                                        <option value="от {{$category->max/5}} UZS">
                                            от {{$category->max/5}} UZS
                                        </option>
                                        <option value="от {{$category->max/5 * 2}} UZS">
                                            от {{$category->max/5 * 2}} UZS
                                        </option>
                                        <option value="от {{$category->max/5 * 3}} UZS">
                                            от {{$category->max/5 * 3}} UZS
                                        </option>
                                        <option value="от {{$category->max/5 * 4}} UZS">
                                            от {{$category->max/5 * 4}} UZS
                                        </option>
                                        <option value="до {{$category->max}} UZS">
                                            до {{$category->max}} UZS
                                        </option>
                                    </select>
                                </div>
                            @include('create.custom-fields2')

                            <div class="mt-4">
                                    <div class="flex w-full mt-4">
                                        <a onclick="myFunction()"
                                        class="bg-white my-4 cursor-pointer hover:border-yellow-500 text-gray-600 hover:text-yellow-500 transition duration-300 font-normal text-lg py-3 sm:px-8 px-4 rounded-2xl border border-2">
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
                <x-faq/>

                </div>

            </div>
        </div>
    </form>

    <script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script src="{{ asset('/js/flowbite.js') }}"></script>
    <script>
        $(function() {
            $("#slider-range-min").slider({
                range: "min",
                value: 0,
                min: {{$category->max}}/6,
                max: {{$category->max}},
                step: {{$category->max}}/6,
                slide: function(event, ui) {

                    var maximum = {{$category->max}};
                    var pre_maximum = Math.floor({{$category->max}} - ({{$category->max}}/6));
                    if (maximum == Math.floor(ui.value)) {

                        $("#amount").val("от " + maximum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + " UZS");
                        $("#amount2").val("от " + maximum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + " UZS");
                    }else if (pre_maximum == Math.floor(ui.value)){
                        $("#amount").val("до " + maximum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + " UZS");
                        $("#amount2").val("до " + maximum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + " UZS");
                    } else {
                        var delitel = ui.value / 1000;
                        var round   = Math.floor(delitel)*1000;
                        $("#amount").val("до " + round.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + " UZS");
                        $("#amount2").val("до " + round.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + " UZS");

                    }

                }
            });
            $('.ui-slider-handle').attr('data-tooltip-target', 'tooltip-no-arrow');
            $(".ui-slider-range").css("height", '142px');
            $(".ui-slider-range").css("background", 'linear-gradient(rgb(255, 132, 56)  , rgb(255, 132, 56))');
            $(".ui-slider-range").css("top", '-147px');
            $(".ui-slider-handle").css("display", 'block');
            var delitel = Math.floor($("#slider-range-min").slider("value")) / 1000;
            var round   = Math.floor(delitel)*1000;
            $("#amount").val('до ' + round.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' UZS');
            $("#amount2").val('до ' + round.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' UZS');
        });
    </script>
@endsection


