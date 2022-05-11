<div class="show_tasks">
    @if ($tasks)
    @foreach ($tasks as $task)
    @if ($task->user)
    <div class="border-2 border-gray-500 rounded-xl bg-gray-50 hover:bg-blue-100 h-auto my-3">
        <div class="grid grid-cols-5 w-11/12 mx-auto py-2">
            <div class="sm:col-span-3 col-span-5 flex flex-row">
                <div class="sm:mr-6 mr-3 w-1/6">
                    <img src="{{ asset('storage/'.$task->category->ico) }}"
                        class="text-2xl float-left text-blue-400 sm:mr-4 mr-3 h-14 w-14 bg-blue-200 p-2 rounded-xl" />
                </div>
                <div class="w-5/6">
                    <a href="/detailed-tasks/{{$task->id}}"
                        class="sm:text-lg text-base font-semibold text-blue-500 hover:text-red-600">{{ $task->name }}</a>
                    <p class="text-sm">
                        {{ count($task->addresses)? $task->addresses[0]->location:'Можно выполнить удаленно' }}</p>
                    @if($task->date_type == 1 || $task->date_type == 3)
                    <p class="text-sm my-0.5">{{__('Начать')}} {{ $task->start_date }}</p>
                    @endif
                    @if($task->date_type == 2 || $task->date_type == 3)
                    <p class="text-sm my-0.5">{{__('Закончить')}} {{ $task->end_date }}</p>
                    @endif
                    @if($task->oplata == 1)
                    <p class="text-sm">{{__(' Оплата наличными')}}</p>
                    @else
                    <p class="text-sm">{{__('Оплата через карту')}}</p>
                    @endif
                </div>
            </div>
            <div class="sm:col-span-2 col-span-5 sm:text-right text-left sm:ml-0 ml-16">
                <p class="sm:text-lg text-sm font-semibold text-gray-700">
                    @if ( __('до') == 'gacha' )
                    {{ number_format($task->budget) }} {{__('сум')}}{{__('до')}}
                    @else
                    {{__('до')}} {{ number_format($task->budget) }} {{__('сум')}}
                    @endif
                </p>
                <span class="text-sm sm:mt-5 sm:mt-1 mt-0">{{__('Откликов')}} -
                    @if ($task->response_count>0)
                    {{  $task->response_count }}
                    @else
                    0
                    @endif
                </span>
                <p class="text-sm sm:mt-1 mt-0">{{ $task->category->name }}</p>
                @if (Auth::check() && Auth::user()->id == $task->user_id)
                <a href="/profile"
                    class="text-sm sm:mt-1 mt-0 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">{{ $task->user?$task->user->name:'' }}</a>
                @else
                <a href="/performers/{{$task->user_id}}"
                    class="text-sm sm:mt-1 mt-0 hover:text-red-500 border-b-2 border-gray-500 hover:border-red-500">{{ $task->user?$task->user->name:'' }}</a>
                @endif
            </div>
        </div>
    </div>
    @endif

    @endforeach
    @endif
</div>