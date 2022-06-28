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
                        @foreach($data->options_ru['options'] as $key => $option)
                            <option
                                @if(App\Services\Task\CustomFieldService::showOptions($task, $data->id, $key, $option))
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
                            @if(array_key_exists('options', $data->options))
                                @foreach($data->options_ru['options'] as $key => $option)
                                    <label class="md:w-2/3 block mt-6">
                                        <input
                                            @if(App\Services\Task\CustomFieldService::showOptions($task, $data->id, $key, $option))
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
                    <div>
                        <!-- <span class="underline hover:text-gray-400 decoration-dotted cursor-pointer float-right">Приватная информация</span> -->
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
                            @foreach($data->options_ru['options'] as $key => $option)
                                <input type="radio"
                                       @if(App\Services\Task\CustomFieldService::showOptions($task, $data->id, $key, $option))
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
                    id="car" name="{{$data->name}}[]" type="text"
                    value="{{App\Services\Task\CustomFieldService::setInputValue($task, $data->id)}}"
                    class="shadow appearance-none border focus:shadow-orange-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-yellow-500"
                    required
                >
            </div>
        </div>
    </div>
@endif
