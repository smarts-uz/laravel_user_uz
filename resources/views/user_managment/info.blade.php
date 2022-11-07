<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fonts/fonts.css') }}">
    <title>Universal services</title>
</head>
<body>
<h1 class="text-center mt-4 text-3xl font-bold">Users info</h1>
<div class="w-11/12 mx-auto mt-4 grid grid-cols-4">
    <!-- Tabs -->
    <div id="tabs" class="col-span-1 flex flex-col pt-2 px-1 w-full">
        <div class="bg-blue-500 text-white text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a id="default-tab" href="#first">
                user yaratgan tasklari
            </a>
        </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#second">
                user otklik tashlagan tasklar
            </a>
        </div>
       <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
           <a href="#third">
               user qoldirgan izohlari
           </a>
       </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#fourth">
                userga qoldirilgan izohlar
            </a>
        </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#five">
                user qoldirgan okliklari
            </a>
        </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#six">
                user yuklagan rasmlari
            </a>
        </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#seven">
                user yuklagan youtube link
            </a>
        </div>
    </div>

    <!-- Tab Contents -->
    <div id="tab-contents" class="w-full col-span-3 border-2 rounded-xl mt-2">
        <div id="first" class="p-4">
            @foreach($tasks as $task)
                <div class="border-2 border-gray-500 rounded-xl bg-gray-50 hover:bg-blue-100 h-auto my-3 bg-gray-100">
                    <div class="grid grid-cols-5 w-11/12 mx-auto py-2">
                        <div class="sm:col-span-3 col-span-5 flex flex-row">
                            <div class="sm:mr-6 mr-3 w-1/6">
                                <img src="{{ asset('storage/'.$task->category->ico) }}"
                                     class="text-2xl float-left text-blue-400 sm:mr-14 mr-3 h-14 w-14 bg-blue-200 p-2 rounded-xl" />
                            </div>
                            <div class="w-5/6">
                                <a href="/detailed-tasks/{{$task->id}}" target="_blank" class="sm:text-lg text-base font-semibold text-blue-500 hover:text-red-600">
                                    {{ $task->name }}
                                </a>
                                @if(count($task->addresses))
                                    <p class="font-normal text-sm mt-1">{{$task->addresses[0]->location}}</p>
                                @else
                                    <p class="font-normal text-sm mt-1">{{__('Виртуальное задание')}}</p>
                                @endif
                                @if($task->date_type === 1 || $task->date_type === 3)
                                    <p class="text-sm my-0.5">{{__('Начать')}} {{ $task->start_date }}</p>
                                @endif
                                @if($task->date_type === 2 || $task->date_type === 3)
                                    <p class="text-sm my-0.5">{{__('Закончить')}} {{ $task->end_date }}</p>
                                @endif
                                @if($task->oplata === 1)
                                    <p class="text-sm">{{__(' Оплата наличными')}}</p>
                                @else
                                    <p class="text-sm">{{__('Оплата через карту')}}</p>
                                @endif
                            </div>
                        </div>
                        <div class="sm:col-span-2 col-span-5 sm:text-right text-left sm:ml-0 ml-16">
                            <p class="sm:text-lg text-sm font-semibold text-gray-700">
                                @if ( session('lang') === 'uz' )
                                    {{ number_format($task->budget) }} {{__('сум')}}{{__('до')}}
                                @else
                                    {{__('до')}} {{ number_format($task->budget) }} {{__('сум')}}
                                @endif
                            </p>
                            <span class="text-sm sm:mt-5 sm:mt-1 mt-0">{{__('Откликов')}} -
                                @if ($task->task_responses()->count() > 0)
                                    {{  $task->task_responses()->count() }}
                                @else
                                    0
                                @endif
                            </span>
                            <p class="text-sm sm:mt-1 mt-0">{{ $task->category->name }}</p>
                            @if (Auth::check() && Auth::user()->id === $task->user_id)
                                <a href="/profile" target="_blank"
                                   class="text-sm sm:mt-1 mt-0 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">{{ $task->user->name }}</a>
                            @else
                                <a href="/performers/{{$task->user_id}}" target="_blank"
                                   class="text-sm sm:mt-1 mt-0 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">{{ $task->user->name }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div id="second" class="hidden p-4">
            @foreach($performer_tasks as $performer_task)
                <div class="border-2 border-gray-500 rounded-xl bg-gray-50 hover:bg-blue-100 h-auto my-3 bg-gray-100">
                    <div class="grid grid-cols-5 w-11/12 mx-auto py-2">
                        <div class="sm:col-span-3 col-span-5 flex flex-row">
                            <div class="sm:mr-6 mr-3 w-1/6">
                                <img src="{{ asset('storage/'.$performer_task->category->ico) }}"
                                     class="text-2xl float-left text-blue-400 sm:mr-14 mr-3 h-14 w-14 bg-blue-200 p-2 rounded-xl" />
                            </div>
                            <div class="w-5/6">
                                <a href="/detailed-tasks/{{$performer_task->id}}" target="_blank" class="sm:text-lg text-base font-semibold text-blue-500 hover:text-red-600">
                                    {{ $performer_task->name }}
                                </a>
                                @if(count($performer_task->addresses))
                                    <p class="font-normal text-sm mt-1">{{$performer_task->addresses[0]->location}}</p>
                                @else
                                    <p class="font-normal text-sm mt-1">{{__('Виртуальное задание')}}</p>
                                @endif
                                @if($performer_task->date_type === 1 || $performer_task->date_type === 3)
                                    <p class="text-sm my-0.5">{{__('Начать')}} {{ $performer_task->start_date }}</p>
                                @endif
                                @if($performer_task->date_type === 2 || $performer_task->date_type === 3)
                                    <p class="text-sm my-0.5">{{__('Закончить')}} {{ $performer_task->end_date }}</p>
                                @endif
                                @if($performer_task->oplata === 1)
                                    <p class="text-sm">{{__(' Оплата наличными')}}</p>
                                @else
                                    <p class="text-sm">{{__('Оплата через карту')}}</p>
                                @endif
                            </div>
                        </div>
                        <div class="sm:col-span-2 col-span-5 sm:text-right text-left sm:ml-0 ml-16">
                            <p class="sm:text-lg text-sm font-semibold text-gray-700">
                                @if ( session('lang') === 'uz' )
                                    {{ number_format($performer_task->budget) }} {{__('сум')}}{{__('до')}}
                                @else
                                    {{__('до')}} {{ number_format($performer_task->budget) }} {{__('сум')}}
                                @endif
                            </p>
                            <span class="text-sm sm:mt-5 sm:mt-1 mt-0">{{__('Откликов')}} -
                                @if ($performer_task->task_responses()->count() > 0)
                                    {{  $performer_task->task_responses()->count() }}
                                @else
                                    0
                                @endif
                            </span>
                            <p class="text-sm sm:mt-1 mt-0">{{ $performer_task->category->name }}</p>
                            @if (Auth::check() && Auth::user()->id === $performer_task->user_id)
                                <a href="/profile" target="_blank"
                                   class="text-sm sm:mt-1 mt-0 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">{{ $performer_task->user->name }}</a>
                            @else
                                <a href="/performers/{{$performer_task->user_id}}" target="_blank"
                                   class="text-sm sm:mt-1 mt-0 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">{{ $performer_task->user->name }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div id="third" class="hidden p-4">
            @foreach($user_reviews as $user_review)
                <a target="_blank" href="">{{$user_review->description}}</a> <br>
            @endforeach
        </div>
        <div id="fourth" class="hidden p-4">
            @foreach($performer_reviews as $performer_review)
                <a target="_blank" href="">{{$performer_review->description}}</a> <br>
            @endforeach
        </div>
        <div id="five" class="hidden p-4">
            @foreach($task_responses as $task_response)
                <a target="_blank" href="">{{$task_response->description}}</a> <br>
            @endforeach
        </div>
        <div id="six" class="hidden p-4">
            Portfolio
        </div>
        <div id="seven" class="hidden p-4">
            @if($user->youtube_link)
                <iframe class="my-4 sm:w-full w-5/6 rounded-lg" width="644" height="500" id="iframe" src="{{$user->youtube_link}}" frameborder="0"></iframe>
            @else
                <p>Youtube link yuklanmagan</p>
            @endif
        </div>
    </div>
</div>

<script>
    let tabsContainer = document.querySelector("#tabs");
    let tabTogglers = tabsContainer.querySelectorAll("#tabs a");
    tabTogglers.forEach(function(toggler) {
        toggler.addEventListener("click", function(e) {
            e.preventDefault();
            let tabName = this.getAttribute("href");
            let tabContents = document.querySelector("#tab-contents");
            for (let i = 0; i < tabContents.children.length; i++) {
                tabTogglers[i].parentElement.classList.remove("bg-blue-500","text-white");
                tabContents.children[i].classList.remove("hidden");
                if ("#" + tabContents.children[i].id === tabName) {
                    continue;
                }
                tabContents.children[i].classList.add("hidden");
            }
            e.target.parentElement.classList.add("bg-blue-500","text-white");
        });
    });
</script>
</body>
</html>
