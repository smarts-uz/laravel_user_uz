<div id="map" class="h-64 mb-4 -mt-2 {{ count($addresses)?'':'hidden' }}  "></div>
<div class="ml-4 md:ml-12 flex flex-row my-4">
    <h1 class="font-bold h-auto w-48">{{__('Место')}}</h1>
    @if(count($addresses))
        <p class=" h-auto w-96">
            @foreach($addresses as $address)
                {{$address->location}}
                <br>
            @endforeach
        </p>
    @else
        {{__('Виртуальное задание')}}
    @endif
</div>
<div class="ml-4 md:ml-12 flex flex-row mt-8">
    @if($task->date_type == 1)
        <h1 class="font-bold h-auto w-48">{{__('Начать работу')}}</h1>
        {{ $task->start_date     }}
    @elseif($task->date_type == 2)
        <h1 class="font-bold h-auto w-48">{{__('Закончить работу')}}</h1>
        {{ $task->end_date     }}
    @else
        <h1 class="font-bold h-auto w-48">{{__('Указать период')}}</h1>
        <p class=" h-auto w-96">{{ $task->start_date     }} - {{ $task->end_date     }}  </p>

    @endif
</div>
<div class="ml-4 md:ml-12 flex flex-row mt-8">
    <h1 class="font-bold h-auto w-48">{{__('Бюджет')}}</h1>
    <p class=" h-auto w-96">
        @if ( __('до') == 'gacha' )
            {{ number_format($task->budget) }} {{__('сум')}}{{__('до')}}
        @else
            {{__('до')}} {{ number_format($task->budget) }} {{__('сум')}}
        @endif
    </p>
</div>
@isset($value)
    @foreach($task->custom_field_values as $value)
        <div class="ml-4 md:ml-12 flex flex-row mt-8">

            <h1 class="font-bold h-auto w-48">{{ $value->custom_field->getTranslatedAttribute('title') }}</h1>
            <p class=" h-auto w-96">
                @foreach(json_decode($value->value, true) as $value_obj)
                    @if ($loop->last)
                        {{$value_obj}}
                    @else
                        {{$value_obj}},
                    @endif
                @endforeach
            </p>
        </div>
    @endforeach
@endisset
<div class="ml-4 md:ml-12 flex flex-row mt-8">
    <h1 class="font-bold h-auto w-48">{{__('Оплата задания')}}</h1>
    <div class=" h-auto w-96">
        <p class="text-blue-400">
            @if($task->oplata == 1)
                {{__(' Оплата наличными')}}
            @else
                {{__('Оплата через карту')}}
            @endif
        </p>
    </div>
</div>
<div class="ml-4 md:ml-12 flex flex-row mt-8">
    <h1 class="font-bold h-auto w-48">{{__('Нужно')}}</h1>
    <p class=" h-auto w-96">{{$task->description}}</p>
</div>
<div class="ml-4 md:ml-12 flex flex-wrap mt-8">
    <h1 class="font-bold h-auto w-48">{{__('Рисунок')}}</h1>
    @foreach(json_decode($task->photos)??[] as $key => $image)

        @if($loop->first)
            <div class="relative boxItem">
                <a class="boxItem relative" href="{{ asset('storage/'.$image) }}"
                   data-fancybox="img1"
                   data-caption="<span>{{  $task->created_at}}</span>">
                    <div class="mediateka_photo_content">
                        <img src="{{ asset('storage/'.$image) }}" alt="">
                    </div>
                </a>
            </div>
        @endif

    @endforeach
</div>
@if($task->docs == 1)
    <div class="ml-4 md:ml-12 flex flex-row mt-8">
        <h1 class="font-bold h-auto w-48">{{__('Предоставил(а) документы')}}</h1>
    </div>
@else
    <div class="ml-4 md:ml-12 flex flex-row mt-8">
        <h1 class="font-bold h-auto w-48">{{__('Не предоставил(а) документы')}}</h1>
    </div>
@endif
@foreach($task->custom_field_values as $value)
    @if($value->value &&  $value->custom_field)
        <div class="ml-4 md:ml-12 flex flex-row mt-8">
            <h1 class="font-bold text-gray-600 h-auto w-48">{{ $value->custom_field->title }}</h1>
            <div class=" h-auto w-96">
                <p class="text-gray-500">
                    <b class="ml-4">{{ $value->custom_field->label  }}:</b>

                    {{ json_decode($value->value)[0]  }}
                </p>
            </div>
        </div>
    @endif
@endforeach
