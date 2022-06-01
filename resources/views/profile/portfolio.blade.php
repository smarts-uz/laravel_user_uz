@extends('layouts.app')

@section('content')
    <div class="w-10/12 mx-auto mt-8">
        <div class="lg:w-7/12 w-full">
            @if ($portfolio->user_id == $user->id)
                <a class="text-sm text-blue-500 hover:text-red-500" href="/profile"><i
                class="fas fa-arrow-left"></i> {{__('Венруться в профиль')}}</a>
            @endif
            <form action="{{ route('profile.updatePortfolio', $portfolio->id) }}" method="post">
                @csrf
                <div class="bg-yellow-50 p-8 rounded-md my-6 flex flex-wrap">
                    {{__('Название')}}*
                    <input name="comment" class="border focus:outline-none focus:border-yellow-500 mb-6 text-sm border-gray-200 rounded-md w-full px-4 py-2" type="text" value="{{$portfolio->comment}}">
                    {{__('Описание')}}
                    <input name="description" class="border focus:outline-none focus:border-yellow-500 mb-6 text-sm border-gray-200 rounded-md w-full px-4 py-2" type="text" value="{{$portfolio->description}}">


                    @foreach(json_decode($portfolio->image)??[] as $key => $image)
                        <div class="relative boxItem">
                            <a class="boxItem relative" href="{{ asset('portfolio/' . $image) }}"
                               data-fancybox="img1"
                               data-caption="<span>{{ $portfolio->created_at }}</span>">
                                <div class="mediateka_photo_content">
                                    <img src="{{ asset('portfolio/' . $image) }}" alt="">
                                </div>
                            </a>
                            <div class="absolute right-0 top-0 absolute"><i class=' text-red-600 text-2xl fas fa-times-circle img-delete hover:text-black cursor-pointer' data-action="{{ $image }}"></i></div>
                        </div>
                    @endforeach
                    <div id="comdes1" class="text-center h-full w-full text-base">
                        <div id="photos" class="bg-yellow-50 p-8 rounded-md my-6"></div>
                    </div>
                </div>
                <div class="flex justify-center">
                    <button id="update" class=" mr-5 bg-green-500 hover:bg-green-700 text-white cursor-pointer py-2 px-10 mb-4 rounded " type="submit">{{__('Сохранить')}}</button>

                    @if($isDelete)
                        <input type="button" id="delete-btn" class=" mr-5 bg-red-500 hover:bg-red-700 text-white cursor-pointer py-2 px-10 mb-4 rounded" value="{{__('Удалить')}}">
                    @endif
                </div>
            </form>
            <form action="{{ route('profile.delete', $portfolio->id) }}" method="post" id="delete-form">
                @csrf
            </form>
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
    <script src="https://releases.transloadit.com/uppy/v2.4.1/uppy.min.js"></script>
    <script src="https://releases.transloadit.com/uppy/v2.4.1/uppy.legacy.min.js" nomodule></script>
    <script src="https://releases.transloadit.com/uppy/locales/v2.0.5/ru_RU.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.img-delete').on('click', function () {
                var image = $(this).attr('data-action');
                var index = $(this).closest('.boxItem').index();
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    url: "{{ route('profile.deleteImage', $portfolio->id) }}",
                    data: {
                        'image': image
                    },
                    success: function (response) {
                        $('.boxItem:nth-child(' + (index + 1) + ')').remove();
                    },
                    error: function (error) {
                        console.log(error);
                    }
                })
            });
            $('#delete-btn').on('click', function () {
                $('#delete-form').submit();
            })
        });
        var uppy = new Uppy.Core()
            .use(Uppy.Dashboard, {
                trigger: '.UppyModalOpenerBtn',
                inline: true,
                target: '#photos',
                showProgressDetails: true,
                allowedFileTypes: ['image/*'],
                debug: true,
                note: 'Все типы файлов, до 10 МБ',
                height: 400,
                metaFields: [
                    {id: 'name', name: 'Name', placeholder: 'file name'},
                    {id: 'caption', name: 'Caption', placeholder: 'describe what the image is about'}
                ],
                browserBackButtonClose: true
            })

            .use(Uppy.ImageEditor, {target: Uppy.Dashboard})
            .use(Uppy.XHRUpload, {
                endpoint: '{{ route('profile.UploadImage') }}',
                formData: true,
                fieldName: 'images[]',
                headers: file => ({
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }),
            });

        uppy.on('upload-success', (file, response) => {
            const httpStatus = response.status // HTTP status code
            const httpBody = response.body   // extracted response data

        });


        uppy.on('file-added', (file) => {
            uppy.setFileMeta(file.id, {
                size: file.size,

            })
            console.log(file.name);
        });
        uppy.on('complete', result => {
            console.log('successful files:', result.successful)
        });

    </script>
@endsection
