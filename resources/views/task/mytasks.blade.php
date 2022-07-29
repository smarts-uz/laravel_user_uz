@extends("layouts.app")

@section("content")

    <div class="mx-auto w-10/12 my-10">
        <div class="lg:grid lg:grid-cols-3 lg:gap-x-10 text-base">
            <div class="col-span-2">
                <div class="">
                    <!-- Tabs -->
                    <div class="w-full bg-gray-100 rounded-md px-5 py-5 border-gray-100">
                        <ul id="tabs" class="flex rounded-sm sm:w-96 w-full divide-x shadow bg-gray-200">
                            <li id="first_tab" class="w-full text-center p-1 bg-gray-400 rounded-sm text-white">
                                <a id="default-tab" href="#first">{{__('Я исполнитель')}}</a>
                            </li>
                            <li id="second_tab" class="w-full text-center p-1">
                                <a href="#second">{{__('Я заказчик')}}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Tab Contents -->
                <div id="tab-contents">
                    <div id="first">
                        <p class="p-5 lenght"></p>
                        @foreach($perform_tasks as $task)
                            <div class="w-full border-t border-solid hover:bg-blue-100 category">
                                <div class="md:grid md:grid-cols-10 p-2">
                                    @foreach ($categories2 as $category2)
                                        @if ($category2->id == $task->category_id)
                                            <img src=" {{ asset('storage/'.$task->category->ico) }}" alt=""
                                                 class="h-14 w-14 bg-blue-200 p-2 rounded-xl md:mb-0 mb-3">
                                        @endif
                                    @endforeach
                                    <div class="col-span-6">
                                        <a href="/detailed-tasks/{{$task->id}}"
                                           class="text-blue-500 text-xl hover:text-red-500">
                                            {{$task->name}}
                                        </a>

                                        @if(count($task->addresses))
                                            <p class="font-normal text-sm mt-1">{{$task->addresses[0]->location}}</p>
                                        @else
                                            <p class="font-normal text-sm mt-1">{{__('Виртуальное задание')}}</p>
                                        @endif

                                        @if ($task->status == 3)
                                            <p class="text-amber-500 font-normal">{{__('В исполнении')}}</p>
                                        @elseif($task->status < 3)
                                            <p class="text-green-400 font-normal">{{__('Открыто')}}</p>
                                        @elseif($task->status == 5)
                                            <p class="text-red-400 font-normal">{{__('Не выполнено')}}</p>
                                        @elseif($task->status == 6)
                                            <p class="text-red-400 font-normal">{{__('Отменен')}}</p>
                                        @else
                                            <p class="text-red-400 font-normal">{{__('Закрыто')}}</p>
                                        @endif
                                    </div>
                                    <div class="col-span-3 md:text-right categoryid">
                                        <p class="text-xl font-medium text-gray-600">
                                            @if ( session('lang') == 'uz' )
                                                {{ number_format($task->budget) }} {{__('сум')}}{{__('до')}}
                                            @else
                                                {{__('до')}} {{ number_format($task->budget) }} {{__('сум')}}
                                            @endif
                                        </p>
                                        @foreach ($categories2 as $category2)
                                            @if($category2->id == $task->category_id)
                                                <span class="text-sm text-gray-500 hover:text-red-600 my-3"
                                                      about="{{$category2->id}}">{{ $category2->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}</span>
                                            @endif
                                        @endforeach
                                        <p class="text-sm text-gray-500"> {{__("Количество откликов :")}}
                                            @if ($task->task_responses()->count() > 0)
                                                {{  $task->task_responses()->count() }}
                                            @else
                                                0
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="second" class="hidden">
                        <p class="p-5 lenght2"></p>
                        @foreach($tasks as $task)
                            <div class="w-full border-t border-solid hover:bg-blue-100 category2 my-5">
                                <div class="md:grid md:grid-cols-10 p-2">
                                    @foreach ($categories2 as $category2)
                                        @if ($category2->id == $task->category_id)
                                            <img src=" {{ asset('storage/'.$task->category->ico) }}" alt=""
                                                 class="h-14 w-14 bg-blue-200 p-2 rounded-xl md:mb-0 mb-3">
                                        @endif
                                    @endforeach
                                    <div class="col-span-6">
                                        <a href="/detailed-tasks/{{$task->id}}"
                                           class="text-blue-500 text-xl hover:text-red-500">
                                            {{$task->name}}
                                        </a>

                                        @if(count($task->addresses))
                                            <p class="font-normal text-sm mt-1">{{$task->addresses[0]->location}}</p>
                                        @else
                                            <p class="font-normal text-sm mt-1">{{__('Виртуальное задание')}}</p>
                                        @endif

                                        @if ($task->status == 3)
                                            <p class="text-amber-500 font-normal">{{__('В исполнении')}}</p>
                                        @elseif($task->status < 3)
                                            <p class="text-green-400 font-normal">{{__('Открыто')}}</p>
                                        @elseif($task->status == 5)
                                            <p class="text-red-400 font-normal">{{__('Не выполнено')}}</p>
                                        @elseif($task->status == 6)
                                            <p class="text-red-400 font-normal">{{__('Отменен')}}</p>
                                        @else
                                            <p class="text-red-400 font-normal">{{__('Закрыто')}}</p>
                                        @endif
                                    </div>
                                    <div class="col-span-3 md:text-right categoryid">
                                        <p class="text-xl font-medium text-gray-600">
                                            @if ( session('lang') == 'uz')
                                                {{ number_format($task->budget) }} {{__('сум')}}{{__('до')}}
                                            @else
                                                {{__('до')}} {{ number_format($task->budget) }} {{__('сум')}}
                                            @endif
                                        </p>
                                        @foreach ($categories2 as $category2)
                                            @if($category2->id == $task->category_id)
                                                <span class="text-sm text-gray-500 hover:text-red-600 my-3"
                                                      about="{{$category2->id}}">{{ $category2->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}</span>
                                            @endif
                                        @endforeach
                                        <p class="text-sm text-gray-500"> {{__("Количество откликов :")}} @if ($task->task_responses()->count() > 0)
                                                {{  $task->task_responses()->count() }}
                                            @else
                                                0
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-span lg:block hidden">
                <div class="w-full h-full mt-5">
                    <div id="map" class="h-60 rounded-lg w-full">
                    </div>
                    <div class="w-full h-full mt-5">
                        <button
                            class="font-medium hover:text-red-500 rounded-lg text-xl text-center inline-flex items-center mb-1 allshow"
                            type="button">{{__('Все категории')}}</button>

                        <div class="w-full my-1">
                            @foreach ($categories as $category)
                                <div x-data={show:false} class="rounded-sm">
                                    <div class="my-3 text-blue-500 hover:text-red-500 cursor-pointer"
                                         id="{{ preg_replace('/[ ,]+/', '', $category->name) }}">
                                        {{ $category->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}
                                    </div>
                                    <div id="{{$category->slug}}" class="px-8 py-1 hidden">
                                        @foreach ($categories2 as $category2)
                                            @if($category2->parent_id == $category->id)
                                                <div class="child_cat">
                                                    <a class="text-blue-500 hover:text-red-500 my-1 send-request cursor-pointer"
                                                       id="{{$category2->id}}"
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
            </div>
        </div>
    </div>

@endsection

@push("javascript")

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script
        src="https://api-maps.yandex.ru/2.1/?lang={{app()->getLocale()}}&apikey=f4b34baa-cbd1-432b-865b-9562afa3fcdb"
        type="text/javascript"></script>
    <script type="text/javascript">

        let tabsContainer = document.querySelector("#tabs");
        let tabTogglers = tabsContainer.querySelectorAll("#tabs a");
        tabTogglers.forEach(function (toggler) {
            toggler.addEventListener("click", function (e) {
                e.preventDefault();
                let tabName = this.getAttribute("href");
                let tabContents = document.querySelector("#tab-contents");
                for (let i = 0; i < tabContents.children.length; i++) {
                    tabTogglers[i].parentElement.classList.remove("bg-gray-400", "rounded-sm", "text-white");
                    tabContents.children[i].classList.remove("hidden");
                    if ("#" + tabContents.children[i].id === tabName) {
                        continue;
                    }
                    tabContents.children[i].classList.add("hidden");
                }
                e.target.parentElement.classList.add("bg-gray-400", "rounded-sm", "text-white");
            });
        });


        let myTaskCoordinates = [];
        let myCoordinates = [];
        myTaskCoordinates = $.parseJSON(JSON.stringify({!! $tasks->where('coordinates', '!=', '') !!}));
        if (myTaskCoordinates[Object.keys(myTaskCoordinates)[0]].coordinates != null) {
            myCoordinates = myTaskCoordinates[Object.keys(myTaskCoordinates)[0]].coordinates
        }
        ymaps.ready(init);
        function init() {
            if (myCoordinates) {
                let location = ymaps.geolocation;
                location.get({
                    mapStateAutoApply: true
                })
                    .then(
                        function (result) {
                            myCoordinates = result.geoObjects.get(0).geometry.getCoordinates();
                        },
                        function (err) {
                            console.log('Ошибка: ' + err);
                        }
                    );
            }

            var myMap = new ymaps.Map('map', {
                    center: [41, 69],
                    zoom: 9,
                    controls: ['zoomControl']
                }, {
                    searchControlProvider: 'yandex#search'
                }),

                clusterer = new ymaps.Clusterer({
                    preset: 'islands#invertedVioletClusterIcons',
                    groupByCoordinates: true,
                    clusterDisableClickZoom: true,
                    clusterHideIconOnBalloonOpen: false,
                    geoObjectHideIconOnBalloonOpen: false
                }),

                getPointData = function (index) {
                    let status = myTaskCoordinates[index].status;
                    let status_text = '';

                    if (status === 3) {
                        status_text = '{{__('В исполнение')}}'
                    } else if (status === 4) {
                        status_text = '{{__('Закрыто')}}'
                    } else if (status === 5) {
                        status_text = '{{__('Не выполнено')}}'
                    } else if (status === 6) {
                        status_text = '{{__('Отменен')}}'
                    } else {
                        status_text = '{{__('Открыто')}}'
                    }
                    return {
                        balloonContentBody: '<p>{{__('Название')}}: <a class="text-blue-500" href="detailed-tasks/' + myTaskCoordinates[index].id + '">' + myTaskCoordinates[index].name + '</a></p>',
                        clusterCaption: '{{__('Задание')}} № <strong>' + myTaskCoordinates[index].id + '</strong>',
                        balloonContentFooter: '{{__('Статус задания')}}: <strong>' + status_text + '</strong>'
                    };
                },

                getPointOptions = function () {
                    return {
                        preset: 'islands#violetIcon'
                    };
                },

                geoObjects = [];
            for (let i = 0, len = Object.keys(myTaskCoordinates).length; i < len; i++) {
                if (myTaskCoordinates[i] && myTaskCoordinates[i].coordinates != null) {
                    geoObjects.push(new ymaps.Placemark(myTaskCoordinates[i].coordinates.split(','), getPointData(i), getPointOptions()));
                }
            }
            clusterer.add(geoObjects);
            myMap.geoObjects.add(clusterer);
            myMap.setBounds(clusterer.getBounds(), {
                boundsAutoApply: true,
                checkZoomRange: true
            });
        }

        @foreach ($categories as $category)
            $("#{{ preg_replace('/[ ,]+/', '', $category->name) }}").click(function () {
                console.log('{{$category->slug}}', 123)
                if ($("#{{$category->slug}}").hasClass("hidden")) {

                    $("#{{$category->slug}}").removeClass('hidden');
                } else {
                    $("#{{$category->slug}}").addClass('hidden');
                }
            });
        @endforeach
        @foreach ($categories2 as $category2)
            $("#{{$category2->id}}").click(function () {
                var category = $(".categoryid").children("span");
                $(category).each(function () {

                    if ($(this).attr("about") != {{$category2->id}}) {
                        $(this).parents(".category").hide();
                    } else {
                        $(this).parents(".category").show();
                    }
                    if ($(this).attr("about") != {{$category2->id}}) {
                        $(this).parents(".category2").hide();
                    } else {
                        $(this).parents(".category2").show();
                    }
                });
            });
        @endforeach

        $(".allshow").click(function () {
            var category = $(".categoryid").children("span");
            $(category).each(function () {
                if ($(this).parents(".category").is(":hidden")) {
                    $(this).parents(".category").show();
                }
                if ($(this).parents(".category2").is(":hidden")) {
                    $(this).parents(".category2").show();
                }
            });
        });

        $(document).ready(function () {
            $(".lenght2").text(`{{__("Количество заданий : ")}}` + $(".category2").length);
            if ($(".category").is(":visible")) {
                $(".lenght").text(`{{__("Количество заданий : ")}}` + $(".category").length);
            }
        });
    </script>
@endpush
