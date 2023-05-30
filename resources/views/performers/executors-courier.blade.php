@extends("layouts.app")

@section("content")

    <div class="xl:w-9/12 w-10/12 mx-auto ">
        <div class="grid grid-cols-3 grid-flow-row mt-10">
            {{-- left sidebar start --}}
            <div class="lg:col-span-2 col-span-3">
                @include('performers.executors_figure')
                <div class="my-4">
                    <ul class="leading-7">
                        @foreach($user_category as $per_cat)
                            <div class="my-4">
                                @foreach($per_cat['parent'] as $per_c)
                                    <div class="flex flex-row gap-x-4">
                                        <img src="{{asset('storage/'.$per_c->ico) }}" alt="" class="h-10 w-10">
                                        <p class="font-semibold text-xl">{{$per_c->getTranslatedAttribute('name',Session::get('lang') , 'fallbackLocale')}}</p>
                                    </div>
                                @endforeach

                                @foreach($per_cat['category'] as $per_c)
                                    @php
                                        $task_count = $per_c->category->tasks()->where('performer_id', $user->id)->where('status',App\Models\Task::STATUS_COMPLETE)->count()
                                    @endphp
                                    <div class="flex justify-between sm:w-9/12 w-full pl-16 my-2">
                                        <span class="text-sm">{{$per_c->category->getTranslatedAttribute('name')}}</span>
                                        <div class="border-b border-dashed border-gray-500"></div>
                                        @if($task_count>0)
                                            <span class="text-sm">
                                                        {{$task_count}}
                                                @switch(true)
                                                    @case ($task_count === 1)
                                                        {{__('задание ')}}
                                                        @break
                                                    @case($task_count === 2 ||  $task_count === 3 ||  $task_count === 4)
                                                        {{__('задания')}}
                                                        @break
                                                    @case ($task_count === 5 || $task_count === 6)
                                                        {{__('задач')}}
                                                        @break
                                                    @default
                                                        {{__('заданий')}}
                                                @endswitch
                                                    </span>
                                        @else
                                            <span class="text-sm">{{__('нет заданий')}}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </ul>
                </div>
                <div class="my-4">
                    @if(!(count($goodReviews) || count($badReviews)))
                        <h1 class="text-2xl font-semibold mt-2">{{__('Отзывов пока нет')}}</h1>
                    @else
                        <h1 class="text-2xl font-semibold mt-2">{{__('Отзывы')}}</h1>
                        @include('performers.reviews')
                    @endif
                </div>
            </div>
            {{-- left sidebar start end--}}

            {{-- right sidebar start --}}
            @include('performers.executors_right')
            {{-- right sidebar end --}}
        </div>
    </div>



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/raty/3.1.1/jquery.raty.min.css" integrity="sha512-XsO5ywONBZOjW5xo5zqAd0YgshSlNF+YlX39QltzJWIjtA4KXfkAYGbYpllbX2t5WW2tTGS7bmR0uWgAIQ8JLQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/jquery-raty-js@2.8.0/lib/jquery.raty.min.js"></script>
    <script>
        var star = $('#review{{$user->id}}').text();
        if(star>0){
            $("#stars{{$user->id}}").raty({
                path: 'https://cdn.jsdelivr.net/npm/jquery-raty-js@2.8.0/lib/images',
                readOnly: true,
                score: star,
                size: 12
            });
        }
        else{
            $('#str1').addClass('hidden');
            $('#str2').removeClass('hidden');
        }
    </script>
@endsection
