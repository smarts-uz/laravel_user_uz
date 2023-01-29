<div id="map" class="h-64 mb-4 -mt-2 {{ count($addresses)?'':'hidden' }}  "></div>
<div class="ml-4 md:ml-12 flex flex-row my-4">
    <h1 class="font-bold h-auto w-48">{{__('Место')}}</h1>
    @if(count($addresses))
        <ul class="h-auto w-96 list-decimal">
            @foreach($addresses as $address)
                <li>{{$address->location}}</li>
            @endforeach
        </ul>
    @else
        {{__('Виртуальное задание')}}
    @endif
</div>
@if($task->go_back === 1)
    <div class="ml-4 md:ml-12 my-3 flex flex-row my-4">
        <div class="h-auto w-40"></div>
        <h1 class="h-auto w-96 ml-2">{{__('Вернуться в точку А')}}</h1>
    </div>
@endif
<div class="ml-4 md:ml-12 flex flex-row mt-8">
    @if($task->date_type === 1)
        <h1 class="font-bold h-auto w-48">{{__('Начать работу')}}</h1>
        {{ $start }}
    @elseif($task->date_type === 2)
        <h1 class="font-bold h-auto w-48">{{__('Закончить работу')}}</h1>
        {{ $end }}
    @else
        <h1 class="font-bold h-auto w-48">{{__('Указать период')}}</h1>
        <p class=" h-auto w-96">{{ $start }} - {{ $end }}  </p>
    @endif
</div>
<div class="ml-4 md:ml-12 flex flex-row mt-8">
    <h1 class="font-bold h-auto w-48">{{__('Бюджет')}}</h1>
    <p class=" h-auto w-96">
        @if (session('lang') === 'ru')
            {{__('до')}} {{ number_format($task->budget) }} {{__('сум')}}
        @else
            {{ number_format($task->budget) }} {{__('сум')}}
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
    <h1 class="font-bold h-auto w-48">{{__('Способ оплаты')}}</h1>
    <div class=" h-auto w-96">
        <p class="text-blue-400">
            @if($task->oplata === 1)
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
                <a class="boxItem relative" href="{{ asset('storage/uploads/'.$image) }}"
                   data-fancybox="img1"
                   data-caption="<span>{{ $task->created_at }}</span>">
                    <div class="mediateka_photo_content">
                        <img src="{{ asset('storage/uploads/'.$image) }}" alt="">
                    </div>
                </a>
            </div>
        @endif
    @endforeach
</div>

@foreach($task->custom_field_values as $value)
    @if($value->value &&  $value->custom_field && $value->value !== "[null]")
        @switch($value->custom_field->type)
            @case('input')
                @php $input_values[] = $value->custom_field->getTranslatedAttribute('label') . ': ' . json_decode($value->value)[0] @endphp
            @break

            @case('checkbox')
                <div class="ml-4 md:ml-12 flex flex-row mt-8">
                    <h1 class="font-bold h-auto w-48">{{ $value->custom_field->getTranslatedAttribute('label') }}</h1>
                    <div class="h-auto w-full ml-10">
                        <div class="text-gray-800">
                            @foreach($value->getValuesByIds() as $item)
                                <div class="flex flex-row items-center gap-x-4 mb-4">
                                    <i class="fas fa-check text-yellow-500"></i>
                                    <span>{{$item}}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @break

            @case('select')
            <div class="ml-4 md:ml-12 flex flex-row mt-8">
                <h1 class="font-bold h-auto w-48">{{ $value->custom_field->getTranslatedAttribute('label')}}</h1>
                <div class="h-auto w-full ml-10">
                    <p class="text-gray-800">
                        {{ array_values($value->getValuesByIds())[0] }}
                    </p>
                </div>
            </div>
            @break

            @case('radio')
            <div class="ml-4 md:ml-12 flex flex-row mt-8">
                <h1 class="font-bold h-auto w-48">{{ $value->custom_field->getTranslatedAttribute('label') }}</h1>
                <div class="h-auto w-full ml-10">
                    <p class="text-gray-800">
                        {{ array_values($value->getValuesByIds())[0] }}
                    </p>
                </div>
            </div>
            @break
            @default
        @endswitch
    @endif
@endforeach
@if(isset($input_values))
    <div class="ml-4 md:ml-12 flex flex-row mt-8">
        <h1 class="font-bold h-auto w-48">{{ __('Параметры')  }}</h1>
        <div class="flex flex-wrap gap-x-2 h-auto w-full ml-6">
            <h1 class="ml-4">
                {{ implode(', ', $input_values)  }}
            </h1>
        </div>
    </div>
@endif

@if($task->docs === 1)
    <div class="ml-4 md:ml-12 flex flex-row mt-8">
        <h1 class="font-bold h-auto w-48">{{__('Необходимо предоставить личные документы')}}</h1>
    </div>
@else
    <div class="ml-4 md:ml-12 flex flex-row mt-8">
        <h1 class="font-bold h-auto w-48">{{__('Личные документы не требуются')}}</h1>
    </div>
@endif

