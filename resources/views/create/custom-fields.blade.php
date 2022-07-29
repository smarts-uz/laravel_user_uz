@if($data->type == 'select')
    @if($data->title !== "")
        <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
            {{ $data->getTranslatedAttribute('title')}}
        </div>
    @endif

    @if($data->description !== "")
        <div class="py-4 mx-auto px-auto text-center text-sm texl-bold">
            {{ $data->getTranslatedAttribute('description') }}
        </div>
    @endif

    @if($data->options)
        <div class="py-4 mx-auto  text-left ">
            <div class="mb-4">
                <div id="formulario" class="flex flex-col gap-y-4">
                    {{ $data->getTranslatedAttribute('label') }}
                    <select id="where" name="{{$data->name}}[]"
                            class="shadow appearance-none border focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none"
                            required
                    >
                        @php $options = app()->getLocale() == 'ru' ? $data->options_ru['options'] : $data->options['options'] @endphp
                        @foreach($options as $key => $option)
                            <option
                                @if(in_array($key, json_decode($task->custom_field_values()->where('custom_field_id', $data->id)->first()->value)))
                                    selected
                                @endif
                                value="{{ $option }}"
                            >
                                {{ __($option) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    <div class="border-b-4"></div>
@endif
@if($data->type == 'checkbox')

    @if($data->title !== "")
        <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
            {{ $data->getTranslatedAttribute('title') }}
        </div>
    @endif
    @if($data->description !== "")
        <div class="py-4 mx-auto px-auto text-center text-sm texl-bold">
            {{ $data->getTranslatedAttribute('description') }}
        </div>
    @endif
    @if($data->options)
        <div class="py-4 mx-auto  text-left ">
            <div class="mb-4">
                <div id="formulario" class="flex flex-col gap-y-4">
                    <div>
                        <div class="mb-3 xl:w-full">
                            @if(array_key_exists('options', $data->options) || array_key_exists('options', $data->options_ru))
                                @php $options = app()->getLocale() == 'ru' ? $data->options_ru['options'] : $data->options['options'] @endphp
                                @foreach($options as $key => $option)
                                    <label class="md:w-2/3 block mt-6">
                                        <input
                                            @if(in_array($key, json_decode($task->custom_field_values()->where('custom_field_id', $data->id)->first()->value)))
                                                checked
                                            @endif
                                            class="mr-2  h-4 w-4" type="checkbox"
                                            value="{{ $option }}" name="{{$data->name}}[]">
                                        <span class="text-slate-900">
                                            {{ __($option) }}
                                        </span>
                                    </label>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="border-b-4"></div>
@endif
@if($data->type == 'radio')

    @if($data->title !== "")
        <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
            {{ $data->getTranslatedAttribute('title',Session::get('lang') , 'fallbackLocale') }}
        </div>
    @endif
    @if($data->description !== "")
        <div class="py-4 mx-auto px-auto text-center text-sm texl-bold">
            {{ $data->getTranslatedAttribute('description',Session::get('lang') , 'fallbackLocale') }}
        </div>
    @endif

    @if($data->options)
        <div class="py-4 mx-auto  text-left ">
            <div class="mb-4">
                <div id="formulario" class="flex flex-col gap-y-4">
                    <div>
                        <div name="glassSht" class="mb-3 xl:w-full">
                            @php $options = app()->getLocale() == 'ru' ? $data->options_ru['options'] : $data->options['options'] @endphp
                            @foreach($options as $key => $option)
                                <input type="radio"
                                       @if(in_array($key, json_decode($task->custom_field_values()->where('custom_field_id', $data->id)->first()->value)))
                                           checked
                                       @endif
                                       id="radio_{{$key}}" name="{{$data->name}}[]"
                                       value="{{$option}}">


                                <label for="radio_{{$key}}">{{ __($option) }}</label>
                                <br>
                                <br>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif
    <div class="border-b-4"></div>
@endif
@if($data->type == 'input')

    @if($data->title !== "")
        <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
            {{ $data->getTranslatedAttribute('title') }}
        </div>
    @endif
    @if($data->description !== "")
        <div class="py-4 mx-auto px-auto text-center text-sm texl-bold">
            {{ $data->getTranslatedAttribute('description') }}
        </div>
    @endif

    <div class="py-4 mx-auto  text-left ">
        <div class="mb-4">
            <div id="formulario" class="flex flex-col gap-y-4">
                {{ $data->getTranslatedAttribute('label') }}
                <input
                    placeholder="{{ $data->getTranslatedAttribute('placeholder') }}"
                    id="car_{{ $data['order'] }}" name="{{$data->name}}[]" type="text"
                    value="{{App\Services\Task\CustomFieldService::setInputValue($task, $data->id)}}"
                    class="shadow appearance-none border focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500"
                    required
                >
            </div>
        </div>
    </div>
@endif

