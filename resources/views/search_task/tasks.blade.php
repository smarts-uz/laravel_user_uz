@if (isset($tasks))
    @if ($tasks->count())
        @foreach ($tasks as $task)
            <div class="border-b border-gray-500 bg-gray-50 rounded-t-xl shadow-2xl hover:bg-blue-100 h-auto my-3">
                <div class="grid grid-cols-5 w-11/12 mx-auto py-2">
                    <div class="sm:col-span-3 col-span-5 flex flex-row">
                        <div class="sm:mr-6 mr-3 w-1/6 my-auto">
                            <img src="{{ asset('storage/'.$task->category_icon) }}"
                                 class="text-2xl float-left text-blue-400 sm:mr-14 mr-3 h-14 w-14 bg-blue-200 p-2 rounded-xl"/>
                        </div>
                        <div class="w-5/6">
                            @if( in_array($task->id, Illuminate\Support\Facades\Cache::get('user_viewed_tasks' . auth()->id()) ?? [], true))
                                <a href="/detailed-tasks/{{$task->id}}"
                                   class="sm:text-lg text-base font-semibold text-gray-400 hover:text-red-600">{{ $task->name }}</a>
                            @else
                                <a href="/detailed-tasks/{{$task->id}}"
                                   class="sm:text-lg text-base font-semibold text-blue-500 hover:text-red-600">{{ $task->name }}</a>
                            @endif
                            <p class="text-sm">
                                {{ ($task->address_main) ?: 'Можно выполнить удаленно' }}</p>
                            @if($task->date_type === 1 || $task->date_type === 3)
                                <p class="text-sm my-0.5">{{__('Начать')}} {{ $task->sd_parse }}</p>
                            @endif
                            @if($task->date_type === 2 || $task->date_type === 3)
                                <p class="text-sm my-0.5">{{__('Закончить')}} {{ $task->ed_parse }}</p>
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
                        <p class="text-sm sm:mt-1 mt-0">{{ $task->category_name }}</p>
                        @if (Auth::check() && Auth::user()->id == $task->user_id)
                            <a href="/profile"
                               class="text-sm sm:mt-1 mt-0 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">{{ $task->user_name?$task->user_name:'' }}</a>
                        @else
                            <a href="/performers/{{$task->user_id}}"
                               class="text-sm sm:mt-1 mt-0 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">{{ $task->user_name?$task->user_name:'' }}</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        <div class="w-full h-full lM mt-5" id="loadData">
            <div class="text-center">
                <p class="text-center">{{__('Показано')}}
                    {{$tasks->currentPage()*$tasks->perPage()<=$tasks->total()?$tasks->currentPage()*$tasks->perPage():$tasks->total()}}
                    {{__('из')}}
                    <span>{{ $tasks->total()}}</span>
                </p>
                @if($tasks->currentPage()*$tasks->perPage()<$tasks->total())
                    <button id="loadMoreData" type="button"
                            class="butt mt-2 px-5 py-1 border border-black rounded hover:cursor-pointer">{{__('Показать ещё')}}</button>
                @endif

            </div>
        </div>
    @else

        <div class="w-11/12">
            <div class="no_tasks">
                <div class=" w-3/5 h-3/5 mx-auto">
                    <img src="images/notlikes.png" class="w-full h-full">
                    <div class="text-center w-full h-full">
                        <p class="text-4xl"><b>{{__('Задания не найдены')}}</b></p>
                        <p class="text-xl">{{__('Попробуйте уточнить запрос или выбрать другие категории')}}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
