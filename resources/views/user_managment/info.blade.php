@extends('layouts.app2')

@section('content')

<div class="w-11/12 mx-auto text-gray-500 mt-4">
    <div class="flex items-center">
        <a href="/admin/users" class="text-lg text-blue-500 hover:text-red-500 cursor-pointer mr-12">
            <i class="fas fa-arrow-left"></i> {{__('Вернитесь назад')}}
        </a>
        @if(session('lang') === 'ru')
            <a href="{{route('lang', ['lang'=>'uz'])}}" class="hover:text-red-500 mr-2">
                UZ
            </a>
            I
            <a href="{{route('lang', ['lang'=>'ru'])}}" class="hover:text-red-500 text-red-500 ml-2">
                RU
            </a>
        @else
            <a href="{{route('lang', ['lang'=>'uz'])}}" class="hover:text-red-500 text-red-500 mr-2">
                UZ
            </a>
            I
            <a href="{{route('lang', ['lang'=>'ru'])}}" class="hover:text-red-500 ml-2">
                RU
            </a>
        @endif
    </div>
</div>
<h1 class="text-center mt-4 text-3xl font-bold">{{__('Информация о пользователе')}}</h1>
<div class="w-11/12 mx-auto mt-4 grid grid-cols-4">
    <!-- Tabs -->
    <div id="tabs" class="md:col-span-1 col-span-4 flex flex-col pt-2 px-1 w-full">
        <div class="bg-blue-500 text-white text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a id="default-tab" href="#first">
                {{__('Задания, созданные пользователем')}}
            </a>
        </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#second">
                {{__('Пользователь ответил на задачи')}}
            </a>
        </div>
       <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
           <a href="#third">
               {{__('Комментарии, оставленные пользователем')}}
           </a>
       </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#fourth">
                {{__('Комментарии оставленные пользователю')}}
            </a>
        </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#five">
                {{__('Откликы оставленные пользователю')}}
            </a>
        </div>
    </div>

    <!-- Tab Contents -->
    <div id="tab-contents" class="w-full md:col-span-3 col-span-4 border-2 rounded-xl mt-2">
        <div id="first" class="p-4">
            @if(!count($tasks))
                <p class="">{{__('Этот пользователь не создал никаких задач')}}</p>
            @else
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
                                    @if ( session('lang') === 'ru' )
                                        {{__('до')}} {{ number_format($task->budget) }} {{__('сум')}}
                                    @else
                                        {{ number_format($task->budget) }} {{__('сум')}}{{__('до')}}
                                    @endif
                                </p>
                                <span class="text-sm sm:mt-5 sm:mt-1 mt-0">{{__('Откликов')}} -
                                @if ($task->task_responses()->count() > 0)
                                        {{  $task->task_responses()->count() }}
                                    @else
                                        0
                                    @endif
                            </span>
                                <p class="text-sm sm:mt-1 mt-0">{{ $task->category->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}</p>
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
            @endif
        </div>
        <div id="second" class="hidden p-4">
            @if(!count($performer_tasks))
                <p class="">{{__('Этот пользователь не ответил на задание')}}</p>
            @else
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
                                    @if ( session('lang') === 'ru' )
                                        {{__('до')}} {{ number_format($performer_task->budget) }} {{__('сум')}}
                                    @else
                                        {{ number_format($performer_task->budget) }} {{__('сум')}}{{__('до')}}
                                    @endif
                                </p>
                                <span class="text-sm sm:mt-5 sm:mt-1 mt-0">{{__('Откликов')}} -
                                @if ($performer_task->task_responses()->count() > 0)
                                        {{  $performer_task->task_responses()->count() }}
                                    @else
                                        0
                                    @endif
                            </span>
                                <p class="text-sm sm:mt-1 mt-0">{{ $performer_task->category->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale') }}</p>
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
            @endif
        </div>
        <div id="third" class="hidden p-4">
            @if(!count($user_reviews))
                <p class="">{{__('Комментариев не осталось')}}</p>
            @else
                @foreach($user_reviews as $user_review)
                    <div class="my-6">
                        <div class="flex flex-row gap-x-2 my-4 items-start">
                            <img src="{{asset("storage/{$user_review->reviewer?->avatar}") }}" alt="#"
                                 class="w-12 h-12 border-2 rounded-lg border-gray-500">
                            <div class="flex flex-col">
                                <a target="_blank" href="{{ route('performers.performer',$user_review->reviewer_id ) }}"
                                   class="text-blue-500 hover:text-red-500 text-xl">{{ $user_review->reviewer_name }}</a>
                                <div class="flex flex-row items-center">
                                    @if ($user_review->good_bad === 1)
                                        <i class="far fa-thumbs-up text-gray-400"></i>
                                    @else
                                        <i class="far fa-thumbs-down text-gray-400"></i>
                                    @endif
                                    @if ($user_review->as_performer === 0)
                                        <p> - {{__('Заказчик')}}</p>
                                    @elseif ($user_review->as_performer === 1)
                                        <p> - {{__('Исполнитель')}}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="w-full p-3 bg-yellow-50 rounded-xl">
                            <p>{{__('Задание')}}
                                <a  target="_blank" href="{{ route('searchTask.task',$user_review->task_id) }}" class="hover:text-red-400 border-b border-gray-300 hover:border-red-400">
                                    "{{ $user_review->task?->name }}"
                                </a>
                                {{__('выполнено')}}
                            </p>
                            <p class="border-t-2 border-gray-300 my-3 pt-3">{{ $user_review->description }}</p>
                            <p class="text-right">{{ $user_review->created }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div id="fourth" class="hidden p-4">
            @if(!count($performer_reviews))
                <p class="">{{__('Комментариев не осталось')}}</p>
            @else
                @foreach($performer_reviews as $performer_review)
                    <div class="my-6">
                        <div class="flex flex-row gap-x-2 my-4 items-start">
                            <img src="{{asset("storage/{$performer_review->reviewer?->avatar}") }}" alt="#"
                                 class="w-12 h-12 border-2 rounded-lg border-gray-500">
                            <div class="flex flex-col">
                                <a target="_blank" href="{{ route('performers.performer',$performer_review->reviewer_id ) }}"
                                   class="text-blue-500 hover:text-red-500 text-xl">{{ $performer_review->reviewer_name }}</a>
                                <div class="flex flex-row items-center">
                                    @if ($performer_review->good_bad === 1)
                                        <i class="far fa-thumbs-up text-gray-400"></i>
                                    @else
                                        <i class="far fa-thumbs-down text-gray-400"></i>
                                    @endif
                                    @if ($performer_review->as_performer === 0)
                                        <p> - {{__('Заказчик')}}</p>
                                    @elseif ($performer_review->as_performer === 1)
                                        <p> - {{__('Исполнитель')}}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="w-full p-3 bg-yellow-50 rounded-xl">
                            <p>{{__('Задание')}}
                                <a target="_blank" href="{{ route('searchTask.task',$performer_review->task_id) }}" class="hover:text-red-400 border-b border-gray-300 hover:border-red-400">
                                    "{{ $performer_review->task?->name }}"
                                </a>
                                {{__('выполнено')}}
                            </p>
                            <p class="border-t-2 border-gray-300 my-3 pt-3">{{ $performer_review->description }}</p>
                            <p class="text-right">{{ $performer_review->created }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div id="five" class="hidden p-4">
            @if(!count($task_responses))
                <p class="">{{__('Пользователь не оставил ответов')}}</p>
            @else
                @foreach($task_responses as $task_response)
                    <div class="bg-gray-100 rounded-lg p-4 my-4">
                        <div class="ml-0">
                            <div class="text-gray-500 flex flex-row gap-x-2">
                                <p class="font-semibold">{{__('Task id')}} : </p>
                                <span>{{ $task_response->task_id }}</span>
                            </div>
                            <div class="text-gray-500 my-2 flex flex-row gap-x-2">
                                <p class="font-semibold">{{__('Task name')}} : </p>
                                <a target="_blank" class="text-blue-500 hover:text-red-500" href="/detailed-tasks/{{$task_response->task_id}}">
                                    {{ $task_response->task->name }}
                                </a>
                            </div>
                            <div class="text-gray-500 my-2 flex flex-row gap-x-2">
                                <p class="font-semibold">{{__('Стоимость')}}</p> : {{ number_format($task_response->price) }} UZS
                            </div>
                            <div class="text-gray-500 my-2 flex flex-row gap-x-2">
                                <p class="font-semibold">{{__('Комментарий ')}}</p> : {{ $task_response->description }}
                            </div>
                            <div class="text-gray-500 my-2 flex flex-row gap-x-2">
                                <p class="font-semibold">{{__('Телефон исполнителя:')}}</p> {{ $task_response->user->phone_number }}
                            </div>
                        </div>
                    </div>
                @endforeach
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

@endsection
