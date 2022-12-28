@if(isset($custom_fields))

    @foreach($custom_fields as $custom_field)

        @if($custom_field['type'] === 'select')
            @if($custom_field['title'])
                <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
                    {{ $custom_field['title']}}
                </div>
            @endif
            @if($custom_field['description'])
                <div class="py-4 mx-auto px-auto text-center text-sm texl-bold">
                    {{ $custom_field['description'] }}
                </div>
            @endif
            @if(($custom_field['options']))

                <div class="py-4 mx-auto text-left">
                    <div class="mb-4">
                        <div id="formulario" class="flex flex-col gap-y-4">

                            {{ ($custom_field['label']) }}
                            <select id="where" name="{{$custom_field['name']}}[]"
                                    class="shadow appearance-none border focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none"
                                    required>
                                @foreach($custom_field['options'] as $key => $option)
                                    <option {{ $option['selected'] ? 'selected':'' }} value="{{$option['id']}}">{{$option['value']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif
            <div class="border-b-4"></div>
        @endif

        @if($custom_field['type'] === 'checkbox')


            @if($custom_field['title'])
                <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
                    {{ $custom_field['title'] }}
                </div>
            @endif
            @if($custom_field['description'])
                <div class="py-4 mx-auto px-auto text-center text-sm texl-bold">
                    {{ $custom_field['description'] }}
                </div>
            @endif

            @if($custom_field['options'])

                <div class="py-4 mx-auto  text-left ">
                    <div class="mb-4">
                        <div id="formulario" class="flex flex-col gap-y-4">
                            <div>
                                <div class="mb-3 xl:w-full">
                                    @foreach($custom_field['options'] as $key => $option)
                                        <label class="md:w-2/3 block mt-6">
                                            <input
                                                class="mr-2  h-4 w-4" type="checkbox" {{ $option['selected']?'checked':'' }}
                                                value="{{ $option['id'] }}" name="{{$custom_field['name']}}[]">
                                            <span class="text-slate-900">
                                                    {{$option['value']}}
                                                    </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="border-b-4"></div>
        @endif

        @if($custom_field['type']  === 'radio')


            @if($custom_field['title'] )
                <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
                    {{ $custom_field['title']  }}
                </div>
            @endif
            @if($custom_field['description'])
                <div class="py-4 mx-auto px-auto text-center text-sm texl-bold">
                    {{ $custom_field['description'] }}
                </div>
            @endif

            @if($custom_field['options'])
                <div class="py-4 mx-auto  text-left ">
                    <div class="mb-4">
                        <div id="formulario" class="flex flex-col gap-y-4">
                            <div class="mb-3 xl:w-full">
                                @foreach($custom_field['options'] as $key => $option)
                                    <input type="radio"
                                           @if($custom_field['required'] === 1) required @endif
                                            id="radio_{{$custom_field['name']}}_{{$option['value']}}" name="{{$custom_field['name']}}[]"
                                           value="{{$option['id']}}" {{ $option['selected']? 'checked':'' }}>
                                    <label class="cursor-pointer" for="radio_{{$custom_field['name']}}_{{$option['value']}}">{{$option['value']}}</label>
                                    <br>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            @endif
            <div class="border-b-4"></div>
        @endif

        @if($custom_field['type']  === 'input')

            @if($custom_field['title'])
                <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
                    {{ $custom_field['title'] }}
                </div>
            @endif
            @if($custom_field['description'])
                <div class="py-4 mx-auto px-auto text-center text-sm texl-bold">
                    {{ $custom_field['description'] }}
                </div>
            @endif

            <div class="py-4 mx-auto  text-left ">
                <div class="mb-4">
                    <div id="formulario" class="flex flex-col gap-y-4">
                        <label for="car_{{ $custom_field['order'] }}">{{$custom_field['label']}}</label>

                        <input placeholder="{{ $custom_field['placeholder'] }}"
                               @if($custom_field['required'] === 1)
                                   required
                               @endif
                               @if($custom_field['data_type'] === 'int')
                                   min="{{$custom_field['min']}}" max="{{$custom_field['max']}}" type="number"
                               @elseif($custom_field['data_type'] === 'string')
                                   minlength="{{$custom_field['min']}}" maxlength="{{$custom_field['max']}}" type="text" onkeypress='validate(event)'
                               @elseif($custom_field['data_type'] === 'double')
                                   min="{{$custom_field['min']}}" max="{{$custom_field['max']}}" type="number"
                               @endif
                            id="car_{{ $custom_field['order'] }}" name="{{$custom_field['name']}}[]" value="{{ $custom_field['task_value'] }}"
                            class="shadow appearance-none border focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500" >
                    </div>
                </div>
            </div>
        @endif

        @if($custom_field['type']  === 'number')

            @if($custom_field['title'])
                <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
                    {{ $custom_field['title'] }}
                </div>
            @endif
            @if($custom_field['description'])
                <div class="py-4 mx-auto px-auto text-center text-sm texl-bold">
                    {{ $custom_field['description'] }}
                </div>
            @endif

            <div class="py-4 mx-auto  text-left ">
                <div class="mb-4">
                    <div id="formulario" class="flex flex-col gap-y-4">
                        <label for="car_{{ $custom_field['order'] }}">{{$custom_field['label']}}</label>

                        <input min="0" placeholder="{{ $custom_field['placeholder'] }}"
                               @if($custom_field['required'] === 1)
                                   required
                               @endif
                               @if($custom_field['data_type'] === 'int')
                                   min="{{$custom_field['min']}}" max="{{$custom_field['max']}}" type="number"
                               @elseif($custom_field['data_type'] === 'string')
                                   minlength="{{$custom_field['min']}}" maxlength="{{$custom_field['max']}}" type="text" onkeypress='validate(event)'
                               @elseif($custom_field['data_type'] === 'double')
                                   min="{{$custom_field['min']}}" max="{{$custom_field['max']}}" type="number"
                               @endif
                            id="car_{{ $custom_field['order'] }}" name="{{$custom_field['name']}}[]" value="{{ $custom_field['task_value'] }}"
                            class="shadow appearance-none border focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500" onkeypress='validate(event)'>
                    </div>
                </div>
            </div>
        @endif

    @endforeach
    <style>
        [class*="copyrights-pane"]
        {display: none !important;}

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endif


