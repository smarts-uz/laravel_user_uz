@if(isset($custom_fields))

    @foreach($custom_fields as $custom_field)

        @if($custom_field['type'] == 'select')
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

                <div class="py-4 mx-auto  text-left ">
                    <div class="mb-4">
                        <div id="formulario" class="flex flex-col gap-y-4">

                            {{ ($custom_field['label']) }}
                            <select id="where" name="{{$custom_field['name']}}[]"
                                    class="shadow appearance-none border focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none"
                                    required>
                                @foreach($custom_field['options'] as $key => $option)
                                    <option {{ $option['selected'] ? 'selected':'' }} value="{{$option['value']}}">{{$option['value']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif
            <div class="border-b-4"></div>
        @endif

        @if($custom_field['type'] == 'checkbox')


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
                                                value="{{ $option['value'] }}" name="{{$custom_field['name']}}[]">
                                            <span class="text-slate-900">
                                                    {{$option['value']}}
                                                    </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <!-- <span class="underline hover:text-gray-400 decoration-dotted cursor-pointer float-right">Приватная информация</span> -->
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="border-b-4"></div>
        @endif

        @if($custom_field['type']  == 'radio')


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

                            <div>

                                <div name="glassSht" class="mb-3 xl:w-full">
                                    @foreach($custom_field['options'] as $key => $option)

                                        <input type="radio"
                                               id="radio_{{$key}}" name="{{$custom_field['name']}}[]"
                                               value="{{$option['value']}}" {{ $option['selected']? 'checked':'' }}>
                                        <label for="radio_{{$key}}">{{$option['value']}}</label>
                                        <br>
                                        <br>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <!-- <span class="underline hover:text-gray-400 decoration-dotted cursor-pointer float-right">Приватная информация</span> -->
                            </div>

                        </div>
                    </div>
                </div>

            @endif
            <div class="border-b-4"></div>
        @endif

        @if($custom_field['type']  == 'input')

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

                        <input
                            placeholder="{{ $custom_field['placeholder'] }}"
                            id="car_{{ $custom_field['order'] }}" name="{{$custom_field['name']}}[]" type="number" value="{{ $custom_field['task_value'] }}"
                            class="shadow appearance-none border focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500"
                            >

                    </div>
                </div>
            </div>
        @endif
        @if($custom_field['type']  == 'number')

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

                        <input
                            min="0" placeholder="{{ $custom_field['placeholder'] }}"
                            id="car_{{ $custom_field['order'] }}" name="{{$custom_field['name']}}[]" type="number" value="{{ $custom_field['task_value'] }}"
                            class="shadow appearance-none border focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500"
                            >

                    </div>
                </div>
            </div>
        @endif

    @endforeach


@endif


