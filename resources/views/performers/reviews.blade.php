<style>
    .tabcontent {
        display: none;
    }
</style>
{{-- tabs --}}
<div class="tab my-2">
    <button
        class="tablinks tablinks border-2 rounded-xl px-2 py-1 mr-4 my-2 border-gray-500  "
        onclick="openCity(event, 'first')"><i
            class="far fa-thumbs-up text-blue-500 mr-1"></i> {{__('Положительные')}}
            ({{count($goodReviews)}})
    </button>
    <button
        class="tablinks tablinks border-2 rounded-xl px-2 py-1 my-2  border-gray-500 text-gray-800 "
        onclick="openCity(event, 'second')"><i
            class="far fa-thumbs-down text-blue-500 mr-2"></i>{{__('Отрицательные')}}
            ({{count($badReviews)}})
    </button>
</div>
{{-- tab contents --}}
<div id="first" class="tabcontent">
    @foreach($goodReviews as $goodReview)
        @if($goodReview->reviewer && $goodReview->task)
            <div class="my-6">
                <div class="flex flex-row gap-x-2 my-4 items-start">
                    <img src="{{ asset('storage/'.$goodReview->reviewer->avatar) }}" alt="#"
                         class="w-12 h-12 border-2 rounded-lg border-gray-500">
                    <a href="{{ route('performers.performer',$goodReview->reviewer_id ) }}"
                       class="text-blue-500 hover:text-red-500 text-xl">{{ $goodReview->reviewer->name }}</a>
                    @if ($goodReview->as_performer==0)
                        <p> - {{__('Заказчик')}}</p>
                    @elseif ($goodReview->as_performer==1)
                        <p> - {{__('Исполнитель')}}</p>
                    @endif

                </div>
                <div class="w-full p-3 bg-yellow-50 rounded-xl">
                    <p>{{__('Задание')}} <a
                            href="{{ route('searchTask.task',$goodReview->task_id) }}"
                            class="hover:text-red-400 border-b border-gray-300 hover:border-red-400">"{{ $goodReview->task->name }}
                            "</a> {{__('выполнено')}}</p>
                    <p class="border-t-2 border-gray-300 my-3 pt-3">{{ $goodReview->description }}</p>
                    <p class="text-right">{{ $goodReview->created }}</p>
                </div>
            </div>
        @endif
    @endforeach
</div>

<div id="second" class="tabcontent">
    @foreach($badReviews as $badReview)
        @if($badReview->reviewer && $badReview->task)
            <div class="my-6">
                <div class="flex flex-row gap-x-2 my-4 items-start">
                    <img src="{{  asset('storage/'.$badReview->reviewer->avatar) }}" alt="#"
                         class="w-12 h-12 border-2 rounded-lg border-gray-500">
                    <a href="{{ route('performers.performer',$badReview->reviewer_id ) }}"
                       class="text-blue-500 hover:text-red-500 text-xl">{{ $badReview->reviewer->name }}</a>
                    @if ($badReview->as_performer==0)
                       <p> - {{__('Заказчик')}}</p>
                    @elseif ($badReview->as_performer==1)
                       <p> - {{__('Исполнитель')}}</p>
                    @endif
                </div>
                <div class="w-full p-3 bg-yellow-50 rounded-xl">
                    <p>{{__('Задание')}} <a href="{{ route('searchTask.task',$badReview->task_id) }}"
                                            class="hover:text-red-400 border-b border-gray-300 hover:border-red-400">"{{ $badReview->task->name }}
                            "</a> {{__('выполнено')}}</p>
                    <p class="border-t-2 border-gray-300 my-3 pt-3">{{ $badReview->description }}</p>
                    <p class="text-right">{{ $badReview->created }}</p>
                </div>
            </div>
        @endif
    @endforeach
</div>



<script>
    // tabs
function openCity(evt, cityName) {
    var index, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (index = 0; index < tabcontent.length; index++) {
      tabcontent[index].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (index = 0; index < tablinks.length; index++) {
      tablinks[index].className = tablinks[index].className.replace("bg-yellow-200 text-gray-900", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += "bg-yellow-200 text-gray-900";
  }
//tabs end
</script>
