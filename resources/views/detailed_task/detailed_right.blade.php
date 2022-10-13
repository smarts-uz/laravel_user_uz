<div class="lg:w-3/12 w-full mt-8 lg:ml-8 ml-0">
    <div class="mb-10">
        <h1 class="text-xl font-medium mb-4">{{__('Задание')}} № {{$task->id}}</h1>
        <div>
            <button onclick="toggleModal44()"
                    class="px-3 py-3 border border-3 ml-4 rounded-md border-gray-500 hover:border-gray-400">
                <i class="fas fa-share-alt"></i>
            </button>
            @if (Auth::check() && Auth::user()->id !== $task->user?->id)
                <button onclick="toggleModal88()"
                        class="px-3 py-3 border border-3 ml-4 rounded-md border-gray-500 hover:border-gray-400">
                    <i class="far fa-flag"></i>
                </button>
            @else
                <button
                    class="px-3 py-3 border border-3 ml-4 rounded-md border-gray-400">
                    <i class="far fa-flag"></i>
                </button>
            @endif
        </div>
    </div>
    <h1 class="text-lg">{{__('Заказчик этого задания')}}</h1>
    <div class="flex flex-col mt-4">
        <div class="mb-4">
            <img class="border-2 border-radius-500 border-gray-400 w-32 h-32 rounded-lg" alt="#"
                 src="@if ($task->user?->avatar === ''){{ asset("storage/images/default.jpg") }}
                 @else{{asset("storage/{$task->user->avatar}") }}" @endif
            >
        </div>
        <div class="">
            @if (Auth::check() && Auth::user()->id === $task->user?->id)
                <a href="/profile"
                   class="text-2xl text-blue-500 hover:text-red-500">{{$task->user->name ?? $task->user_name}}
                </a>
            @else
                <a href="/performers/{{$task->user->id}}"
                   class="text-2xl text-blue-500 hover:text-red-500">{{$task->user->name ?? $task->user_name}}
                </a>
            @endif

            <br>
            <a class="text-xl text-gray-500">
                @php
                    $age = Carbon\Carbon::parse($task->user->born_date)->age;
                @endphp
                @if( $age>0)
                    <p class="inline-block mr-2">
                        {{ $age}}
                        @switch(true)
                            @case ($age%10 === 1)
                                {{('год')}}
                                @break
                            @case($age%10 === 2 ||  $age%10 === 3 ||  $age%10 === 4)
                                {{('года')}}
                                @break
                            @default
                                {{__('лет')}}
                        @endswitch
                    </p>
                @endif
            </a>
        </div>
    </div>
</div>
