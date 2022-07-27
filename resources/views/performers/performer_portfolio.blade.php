@extends("layouts.app")

@section("content")

<div class="w-9/12 mx-auto ">
    <div class="grid grid-cols-3 grid-flow-row mt-10">
        <div class="col-span-3 mt-4">
            <a class="text-lg text-blue-500 hover:text-red-500 cursor-pointer" onclick="myportfolio()"><i
                class="fas fa-arrow-left"></i> {{__('Вернуться к профилю')}}
                <script>
                    function myportfolio() {
                        window.history.back();
                    }
                </script>
            </a>
            <div class="w-full my-8 ">
                <h1 class="text-3xl font-semibold">{{$portfolio->comment}}</h1>
                <p class="text-lg mt-3">{{$portfolio->description}}</p>
            </div>
            <div class="flex flex-wrap gap-x-2">
                @foreach(json_decode($portfolio->image)??[] as $key => $image)
                    <div class="relative boxItem">
                        <a class="boxItem relative" href="{{ asset('portfolio/' . $image) }}"
                        data-fancybox="img1"
                        data-caption="<span>{{ $portfolio->created_at }}</span>">
                            <div class="mediateka_photo_content">
                                <img src="{{ asset('portfolio/' . $image) }}" alt="">
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
    <script
        src="https://cdn.rawgit.com/sachinchoolur/lightgallery.js/master/dist/js/lightgallery.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-pager.js/master/dist/lg-pager.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-autoplay.js/master/dist/lg-autoplay.js"></script>
    <script
        src="https://cdn.rawgit.com/sachinchoolur/lg-fullscreen.js/master/dist/lg-fullscreen.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-zoom.js/master/dist/lg-zoom.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-hash.js/master/dist/lg-hash.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-share.js/master/dist/lg-share.js"></script>
    <script type="text/javascript" src="{{ asset('js/lg-thumbnail.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/lg-rotate.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/lg-video.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/fancybox.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/mediateka2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lightgallery.css') }}">
@endsection
