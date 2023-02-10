<div class="sm:w-4/5 w-full mx-auto my-16">
    <div class="sm:w-full w-4/5 mx-auto text-center">
        <h1 class="text-2xl font-bold">{{__('Что заказывают на «Universal Services» прямо сейчас')}}</h1>
    </div>
    <div class="grid md:grid-cols-3 grid-cols-2 mx-auto mb-56">
        <div  class="lg:col-span-2 col-span-3 md:w-10/12 w-full h-screen blog1 mt-8">
            <div class="w-full overflow-y-scroll h-screen border rounded-lg px-4 home-categories">
                @foreach($tasks as $task)
                    <div class="w-full grid sm:grid-cols-5 grid-cols-3 gap-2 items-center border rounded-lg my-2 h-28 overflow-hidden force-overflow">
                        <div class="icon col-span-1 mx-auto">
                            <img src="{{ asset('storage/'.$task->category->ico) }}" alt="" class="sm:h-14 h-10 sm:w-14 w-10 rounded-xl">
                        </div>
                        <div class="sm:col-span-4 col-span-2">
                            <a href="/detailed-tasks/{{$task->id}}" class="font-normal sm:text-lg text-sm hover:text-yellow-500">
                                {{$task->name}}
                            </a>
                            <p class="sm:text-lg text-xs overflow-hidden text-gray-400">
                                @if(strlen($task->description) >= 150)
                                    {{ Str::limit($task->description, 150) }}
                                @else
                                    {{ $task->description}}
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 flex justify-center">
                <a href="{{route('searchTask.task_search')}}" type="button"
                   class="text-center p-4 bg-blue-500 border-blue-500 text-white text-base  rounded-lg">
                    {{__('Показать ещё задания')}}
                </a>

            </div>
        </div>
        <div class="w-full md:col-span-1  col-span-2 mt-10 lg:block hidden">
{{--            <div class="w-96 h-48 rounded-xl" style="background: url({{getContentImage('home', 'post_section_img1')}});">--}}
{{--                <div class="w-full text-center">--}}
{{--                    <p class="text-2xl font-bold text-yellow-400 pt-16">--}}
{{--                        {!!__('Как стать <br/> исполнителем ')!!}--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="w-96 h-48 rounded-xl my-8" style="background: url({{getContentImage('home', 'post_section_img2')}});">--}}
{{--                <div class="w-full text-center">--}}
{{--                    <p class="text-2xl font-bold text-yellow-400 pt-12">--}}
{{--                        {!!__('Безопасность и <br/> гарантии')!!}</p>--}}
{{--                </div>--}}
{{--            </div>--}}
            <a href="{{route('performers.service')}}">
                <div class="w-96 h-56 rounded-xl" style="background: url({{App\Services\CustomService::getContentImage('home', 'post_section_img3')}});">
                    <div class="w-full text-center">
                        <p class="text-2xl font-bold text-white pt-20">
                            {!!__('Надежные <br/> исполнители <br/> бизнеса')!!}</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
