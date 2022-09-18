@extends("layouts.app")

@section('content')

<div class="container w-4/5 mx-auto mt-12">

        <div class="flex lg:flex-row flex-col justify-center mt-6">
            @include('components.footerpage')
            <div class="lg:w-4/5 w-full text-base lg:mt-0 mt-4">
                <div class="w-full">
                    <h1 class="font-medium text-4xl">{{__('Рейтинг исполнителей и награды')}}</h1>
                        {!! getContentText('badges', 'badges_text') !!}
                    <div class="bg-gray-200 p-5 mt-3">
                        <h2 class="text-3xl p-5">{{__('Типы значков')}}</h2>

                        <div class="grid grid-cols-5 gap-2 my-3 items-center">
                            <div class="col-span-1">
                                <img src="{{ getContentImage('badges', 'badges_verify') }}"  class="mx-auto w-36 h-36"/>
                            </div>
                            <div class="col-span-4">
                                <p class="">
                                    {!! getContentText('badges', 'badges_verify') !!}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-2 my-3 items-center">
                            <div class="col-span-1">
                                <img src="{{ getContentImage('badges', 'badges_best') }}"  class="mx-auto w-36 h-36"/>
                            </div>
                            <div class="col-span-4">
                                <p class="">
                                    {!! getContentText('badges', 'badges_best') !!}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-2 my-3 items-center">
                            <div class="col-span-1">
                                <img src="{{ getContentImage('badges', 'badges_50') }}"  class="mx-auto w-36 h-32"/>
                            </div>
                            <div class="col-span-4">
                                <p class="">
                                    {!! getContentText('badges', 'badges_50') !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
