@extends("layouts.app")

@section("content")

    <div class="xl:w-9/12 w-10/12 mx-auto ">
        <div class="grid grid-cols-3 grid-flow-row mt-10">
            {{-- left sidebar start --}}
            <div class="lg:col-span-2 col-span-3">
                @include('performers.executors_figure')
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
