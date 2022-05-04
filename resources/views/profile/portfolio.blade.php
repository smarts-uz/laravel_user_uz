@extends('layouts.app')

@section('content')
    <div class="w-10/12 mx-auto mt-8">
        <div class="lg:w-7/12 w-full">
            @if ($portfolio->user_id == $user->id)
                <a class="text-sm text-blue-500 hover:text-red-500" href="/profile"><i
                class="fas fa-arrow-left"></i> {{__('Венруться в профиль')}}</a>
            @endif
            <div class="bg-yellow-50 p-8 rounded-md my-6 flex flex-wrap">
                Portfilio Name
                <input class="border focus:outline-none focus:border-yellow-500 mb-6 text-sm border-gray-200 rounded-md w-full px-4 py-2" type="text" disabled value="{{$portfolio->comment}}">
                Portfolio Description
                <input class="border focus:outline-none focus:border-yellow-500 mb-6 text-sm border-gray-200 rounded-md w-full px-4 py-2" type="text" disabled value="{{$portfolio->description}}">


                @foreach(json_decode($portfolio->image)??[] as $key => $image)
                    @if($loop->first)
                        <div class="relative boxItem">
                            <a class="boxItem relative" href="{{ asset('storage/'.$image) }}"
                            data-fancybox="img1"
                            data-caption="<span>{{  $portfolio->created_at}}</span>">
                                <div class="mediateka_photo_content">
                                    <img src="{{ asset('storage/'.$image) }}" alt="">
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>

            @if($isDelete)
                <form action="{{ route('profile.delete', $portfolio->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="photos" class="bg-yellow-50 p-8 rounded-md my-6"></div>
                    <input type="submit" class="bg-red-500 hover:bg-red-700 text-white cursor-pointer py-2 px-10 mb-4 rounded" value="Удалить">
                </form>
            @endif
        </div>
    </div>
    <div style="display: none;">

        @foreach(json_decode($portfolio->image)??[] as $key => $image)
            @if ($loop->first)

                @continue

            @else
                <a style="display: none;" class="boxItem" href="{{ asset('storage/'.$image) }}"
                   data-fancybox="img1"
                   data-caption="<span>{{ $portfolio->created_at }}</span>">
                    <div class="mediateka_photo_content">
                        <img src="{{ asset('storage/'.$image)  }}" alt="">
                    </div>
                </a>
            @endif
        @endforeach
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
