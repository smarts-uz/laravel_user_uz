@extends("layouts.app")

@section("content")
    <script type="text/javascript" src="http://connect.mail.ru/js/loader.js">
    </script>
    <div class="hidden" id="map_route">{{ route('task.map', $task->id) }}</div>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=f4b34baa-cbd1-432b-865b-9562afa3fcdb"
            type="text/javascript"></script>
    <script src="{{ asset('js/detailed-task-map.js') }}" type="text/javascript"></script>


    <div class="xl:flex container w-11/12 mx-auto">
        <div class="md:flex mx-auto xl:w-9/12 w-full">
            {{-- left sidebar start --}}
            <div class="w-full float-left mt-8">
                <h1 class="text-3xl font-bold mb-2">{{$task->name}}</h1>
                <div class="md:flex flex-row">
                        <span class="text-black rounded-lg bg-yellow-400 p-2">
                            @if ( session('lang') == 'uz' )
                                {{ number_format($task->budget) }} {{__('сум')}}{{__('до')}}
                            @else
                                {{__('до')}} {{ number_format($task->budget) }} {{__('сум')}}
                            @endif
                        </span>
                    @auth()
                        @if($task->user_id === auth()->user()->id && !$task->responses_count && $task->status < 3)
                            <a href="{{ route('searchTask.changetask', $task->id) }}"
                               class="py-2 px-2 text-gray-500 hover:text-red-500">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                        @endif
                    @endauth
                </div>
                <div class="md:flex flex-row text-gray-400 mt-4 text-base">
                    @switch(true)
                        @case($task->status<3)
                            <p class="text-green-400 font-normal md:border-r-2 border-gray-400 pr-2">{{__('Открыто')}}</p>
                            @break
                        @case($task->status === 3)
                            <p class="text-green-400 font-normal md:border-r-2 border-gray-400 pr-2">{{__('В исполнении')}}</p>
                            @break
                        @case($task->status === 4)
                            <p class="text-green-400 font-normal md:border-r-2 border-red-400 pr-2">{{__('Закрыто')}}</p>
                            @break
                        @case($task->status === 5)
                            <p class="text-red-400 font-normal md:border-r-2 border-gray-400 pr-2">{{__('Не выполнено')}}</p>
                            @break
                        @default
                            <p class="text-red-400 font-normal md:border-r-2 border-gray-400 pr-2">{{__('Отменен')}}</p>
                    @endswitch
                    <p class="font-normal md:border-r-2 border-gray-400 md:px-2 px-0">{{$task->views }}  {{__('просмотров')}}</p>
                    <p class="mr-3 md:pl-2 pr-3 md:border-r-2 border-gray-400">{{$created}}</p>
                        @if($task->category->getTranslatedAttribute('name')==="Что-то другое" ||$task->category->getTranslatedAttribute('name')==="Boshqa narsa")
                            <p class="pr-3 ">{{ $task->category->parent->getTranslatedAttribute('name') }}</p>
                        @else
                            <p class="pr-3 ">{{ $task->category->getTranslatedAttribute('name') }}</p>
                        @endif
                    @if($task->user_id === auth()->id() && !count($responses) && $task->status === 1 )
                        <form action="{{route("searchTask.delete_task", $task->id)}}" method="post">
                            @csrf
                            @method('delete')
                            <button type="submit"
                                    class="mr-3 border-l-2  pl-2 pl-3 border-gray-400 text-red-500">
                                {{__('Отменить')}}
                            </button>
                        </form>
                    @endif
                </div>

                <div class="mt-12 border-2 py-2 lg:w-[600px]  w-[400px] rounded-lg border-orange-100 shadow-2xl">
                    {{-- task information --}}
                    @include('detailed_task.detailed_task_information')
                    {{-- task information --}}


                    <div>
                        {{-- otklik buttons --}}
                       @include('detailed_task.detailed_buttons')
                        {{-- otklik buttons --}}
                    </div>
                </div>
                @if($task->user_id == auth()->id())
                @else
                    <div
                        class="mt-12 border-2 p-6 lg:w-[600px]  w-[400px] rounded-lg border-orange-100 shadow-lg">
                        <h1 class="text-3xl font-semibold py-3">{{__('Хотите найти надежного помощника?')}}</h1>
                        <p class="mb-10">{{__('Universal Services помогает быстро решать любые бытовые и бизнес-задачи.')}}</p>
                        <a href="/categories/1">
                            <button
                                class="font-sans text-lg font-semibold bg-yellow-500 text-white hover:bg-orange-500 px-8 pt-2 pb-3 rounded">
                                {{__('Создайте свое задание')}}
                            </button>
                        </a>
                    </div>
                @endif

               {{-- task respons  --}}
                    @include('detailed_task.detailed_respons')
               {{-- task respons  --}}

                @if ($task->status === 4)
                    @foreach ($respons_reviews as $respons_review)
                        @if ($respons_review->good_bad === 1 && $respons_review->task_id === $task->id)
                            <div class="my-6">
                                <div class="flex flex-row gap-x-2 my-4">
                                    @if ($respons_review->as_performer === 0)
                                        <img src="@if ($task->user?->avatar === ''){{ asset("storage/images/default.png") }}
                                        @else{{asset("storage/{$task->user->avatar}") }}" @endif alt="#"
                                            class="w-12 h-12 border-2 rounded-lg border-gray-500">
                                    @elseif ($respons_review->as_performer === 1)
                                        <img src="@if ($respons_review->reviewer?->avatar === ''){{ asset("storage/images/default.png") }}
                                        @else{{asset("storage/{$respons_review->reviewer->avatar}") }}" @endif alt="#"
                                            class="w-12 h-12 border-2 rounded-lg border-gray-500">
                                    @endif

                                    <div class="flex flex-col">
                                        @if ($respons_review->as_performer === 0)
                                            <a @if (Auth::check() && Auth::user()->id === $task->user->id)
                                                href="/profile"
                                            @else href="/performers/{{$task->user->id}}"
                                             @endif
                                            class="text-2xl text-blue-500 hover:text-red-500">{{$task->user->name ?? $task->user_name}}
                                            </a>
                                        @elseif ( $respons_review->as_performer === 1)
                                            <a  @if (Auth::check() && Auth::user()->id === $respons_review->reviewer->id)
                                                href="/profile"
                                                @else href="/performers/{{$respons_review->reviewer->id}}"
                                                @endif
                                            class="text-2xl text-blue-500 hover:text-red-500">{{$respons_review->reviewer->name}}
                                            </a>
                                        @endif
                                        <div class="flex flex-row gap-x-2">
                                            <i class="far fa-thumbs-up text-gray-400"></i>
                                            @if ($respons_review->as_performer === 0)
                                                <p class="text-base"> - {{__('Заказчик')}}</p>
                                            @elseif ($respons_review->as_performer === 1)
                                                <p class="text-base"> - {{__('Исполнитель')}}</p>
                                            @endif
                                       </div>
                                    </div>
                                </div>
                                <div class="w-full py-3 px-6 bg-yellow-50 rounded-xl">
                                    <p>{{__('Задание')}} <a href="#"
                                                            class="hover:text-red-400 border-b border-gray-300 hover:border-red-400"> {{$task->name}} </a> {{__('выполнено')}}</p>
                                    <p class="border-t-2 border-gray-300 my-3 pt-3"><i class="far fa-thumbs-up text-gray-400 mr-3"></i>{{$respons_review->description}}</p>
                                    <p class="text-right">{{$respons_review->created}}</p>
                                </div>
                            </div>
                        @elseif ($respons_review->good_bad === 0 && $respons_review->task_id === $task->id)
                            <div class="my-6">
                                <div class="flex flex-row gap-x-2 my-4">
                                    @if ($respons_review->as_performer === 0)
                                        <img src="@if ($task->user->avatar === ''){{ asset("storage/images/default.png") }}
                                        @else{{asset("storage/{$task->user->avatar}") }}" @endif alt="#"
                                            class="w-12 h-12 border-2 rounded-lg border-gray-500">
                                    @elseif ($respons_review->as_performer === 1)
                                        <img src="@if ($respons_review->reviewer->avatar === ''){{ asset("storage/images/default.png") }}
                                        @else{{asset("storage/{$respons_review->reviewer->avatar}") }}" @endif alt="#"
                                            class="w-12 h-12 border-2 rounded-lg border-gray-500">
                                    @endif
                                    <div class="flex flex-col">
                                        @if ($respons_review->as_performer === 0)
                                            <a @if (Auth::check() && Auth::user()->id === $task->user->id)
                                                href="/profile"
                                            @else href="/performers/{{$task->user->id}}"
                                            @endif
                                            class="text-2xl text-blue-500 hover:text-red-500">{{$task->user->name ?? $task->user_name}}
                                            </a>
                                        @elseif ( $respons_review->as_performer === 1)
                                            <a  @if (Auth::check() && Auth::user()->id === $respons_review->reviewer->id)
                                                href="/profile"
                                                @else href="/performers/{{$respons_review->reviewer->id}}"
                                                @endif
                                            class="text-2xl text-blue-500 hover:text-red-500">{{$respons_review->reviewer->name}}
                                            </a>
                                        @endif
                                       <div class="flex flex-row gap-x-2">
                                            <i class="far fa-thumbs-down text-gray-400"></i>
                                            @if ($respons_review->as_performer === 0)
                                                <p class="text-base"> - {{__('Заказчик')}}</p>
                                            @elseif ($respons_review->as_performer === 1)
                                                <p class="text-base"> - {{__('Исполнитель')}}</p>
                                            @endif
                                       </div>
                                    </div>
                                </div>
                                <div class="w-full py-3 px-6 bg-yellow-50 rounded-xl">
                                    <p>{{__('Задание')}} <a href="#"
                                                            class="hover:text-red-400 border-b border-gray-300 hover:border-red-400"> {{$task->name}} </a> {{__('выполнено')}}</p>
                                    <p class="border-t-2 border-gray-300 my-3 pt-3"><i class="far fa-thumbs-down text-gray-400 mr-3"></i>{{$respons_review->description}}</p>
                                    <p class="text-right">{{$respons_review->created}}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif

                <div>
                    @if(count($same_tasks))
                        <div class=" my-3">
                            <h1 class="font-medium text-3xl mt-3">{{__('Похожиe задания')}}</h1>
                            @foreach($same_tasks as $item)
                                @if ($item->user_id !==null)
                                    <div class="border-2 border-gray-500 rounded-xl bg-gray-50 hover:bg-blue-100 h-auto my-3">
                                        <div class="grid grid-cols-5 w-11/12 mx-auto py-2">
                                            <div class="sm:col-span-3 col-span-5 flex flex-row">
                                                <div class="sm:mr-6 mr-3 w-1/6">
                                                    <img src="{{ asset('storage/'.$item->category->ico) }}"
                                                        class="text-2xl float-left text-blue-400 sm:mr-4 mr-3 h-14 w-14 bg-blue-200 p-2 rounded-xl"/>
                                                </div>
                                                <div class="w-5/6">
                                                    <a href="/detailed-tasks/{{$item->id}}"
                                                    class="sm:text-lg text-base font-semibold text-blue-500 hover:text-red-600">{{ $item->name }}</a>
                                                    <p class="text-sm">{{ count($item->addresses)? $item->addresses[0]->location:'Можно выполнить удаленно' }}</p>
                                                    @if($item->date_type === 1 || $item->date_type === 3)
                                                        <p class="text-sm my-0.5">{{__('Начать')}} {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->start_date)->translatedFormat('d-M  H:i') }}  </p>
                                                    @endif
                                                    @if($item->date_type === 2 || $item->date_type === 3)
                                                        <p class="text-sm my-0.5">{{__('Закончить')}}{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->end_date)->translatedFormat('d-M  H:i') }}  </p>
                                                    @endif
                                                    @if($item->oplata === 1)
                                                        <p class="text-sm">{{__(' Оплата наличными')}}</p>
                                                    @else
                                                        <p class="text-sm">{{__('Оплата через карту')}}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2 col-span-5 sm:text-right text-left sm:ml-0 ml-16">
                                                <p class="sm:text-lg text-sm font-semibold text-gray-700">
                                                    @if ( session('lang') === 'uz' )
                                                        {{ number_format($item->budget) }} {{__('сум')}}{{__('до')}}
                                                    @else
                                                        {{__('до')}} {{ number_format($item->budget) }} {{__('сум')}}
                                                    @endif
                                                </p>
                                                <span class="text-sm sm:mt-5 sm:mt-1 mt-0">{{__('Откликов')}} -
                                                    @if ($item->response_count>0)
                                                        {{  $item->response_count }}
                                                    @else
                                                        0
                                                    @endif
                                                </span>
                                                <p class="text-sm sm:mt-1 mt-0">{{ $item->category->name }}</p>
                                                @if (Auth::check() && Auth::id() === $item->user_id)
                                                    <a href="/profile"
                                                    class="text-sm sm:mt-1 mt-0 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">{{ $item->user?$item->user->name:'' }}</a>
                                                @else
                                                    <a href="/performers/{{$item->user_id}}"
                                                    class="text-sm sm:mt-1 mt-0 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">{{ $item->user?$item->user->name:'' }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            {{--end left sidebar start --}}

            {{-- right sidebar start --}}
            @include('detailed_task.detailed_right')
            {{--end right sidebar start --}}
        </div>
    </div>

    {{-- modal content --}}
    @include('detailed_task.detailed_modal')
    {{-- end modal cotent --}}

    <script type='text/javascript'
            src='https://platform-api.sharethis.com/js/sharethis.js#property=620cba4733b7500019540f3c&product=inline-share-buttons'
            async='async'></script>
    <input type="hidden" id="task" value="{{ $task->id }}">
    <script src="{{asset('js/tasks/detailed-tasks.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/picturefill/2.3.1/picturefill.min.js"></script>
    <script
        src="https://cdn.rawgit.com/sachinchoolur/lightgallery.js/master/dist/js/lightgallery.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-pager.js/master/dist/lg-pager.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-autoplay.js/master/dist/lg-autoplay.js"></script>
    <script
        src="https://cdn.rawgit.com/sachinchoolur/lg-fullscreen.js/master/dist/lg-fullscreen.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-zoom.js/master/dist/lg-zoom.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-hash.js/master/dist/lg-hash.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-share.js/master/dist/lg-share.js"></script>
    <script type="text/javascript" src="{{ asset('js/lg-thumbnail.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/lg-rotate.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/lg-video.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/fancybox.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/mediateka.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lightgallery.css') }}">
    <link rel="stylesheet" href="{{asset('css/modal.css')}}">

    <div style="display: none;">
        @foreach(json_decode($task->photos)??[] as $key => $image)
            @if ($loop->first)
            @else
                <a style="display: none;" class="boxItem" href="{{ asset('storage/uploads/'.$image) }}"
                   data-fancybox="img1"
                   data-caption="<span>{{ $task->created_at }}</span>">
                    <div class="mediateka_photo_content">
                        <img src="{{ asset('storage/uploads/'.$image) }}" alt="">
                    </div>
                </a>
            @endif
        @endforeach
    </div>

@endsection

