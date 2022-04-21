@php
    $notifications = App\Models\Notification::query()
        ->where('user_id', auth()->id())
        ->where('is_read', 0)
        ->limit(10)->get();
    $count = $notifications->count();
@endphp
@if($count > 0)
    <div id="content_count" class="w-4 h-4 absolute rounded-full bg-red-500 ml-3 text-white text-xs text-center">{{$count}}</div>
@endif
<button class="focus:outline-none" type="button" data-dropdown-toggle="dropdown">
    <i class="text-2xl lg:mr-6 mr-0 text-gray-500 hover:text-yellow-500 far fa-bell"></i>
</button>
<!-- Dropdown menu -->
<div class="hidden bg-white text-base z-50 list-none divide-y divide-gray-100 rounded shadow my-4" id="dropdown">
    <div class="px-4 py-3">
        <span class="block text-base font-bold">{{__('Уведомления')}}</span>
    </div>
    <ul class="py-1 overflow-y-auto max-h-96" id="notifs" aria-labelledby="dropdown">
        @foreach($notifications as $notification)
            <li class="border-b-2 border-gray-500 flex gap-x-2 p-3 text-gray-800">
                <div class="">
                    <i class="fas fa-star text-yellow-500"></i>
                </div>
                <div class="flex flex-col w-full">
                    <p class="mb-2 text-right">{{$notification->created_at->format('d M')}}</p>
                    @if($notification->type == 1)
                        <div class="w-full">{{__('Отклик к заданию')}} <br>
                            <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">“{{$notification->name_task}}"  №{{$notification->task_id}}</a> <br>
                            {{__('задания отправлен')}}
                        </div>
                    @elseif($notification->type == 2 || $notification->type == 3)
                        <button onclick="toggleModal121('modal-id121', '{{$notification->name_task}}', '{{$notification->description}}', {{$notification->id}})"
                                class="text-sm font-bold hover:bg-gray-100 text-gray-700 block px-4 py-2">
                            {{$notification->name_task}}
                        </button>
                    @elseif($notification->type == 4)
                        <div class="w-full">{!!__('Вас выбрали исполнителем <br> в задании')!!}
                            <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">“{{$notification->name_task}}"  №{{$notification->task_id}}</a> <br>
                            <a class="hover:text-blue-500" href="/performers/{{$notification->user_id}}"> {{$notification->user->name}}</a></div>
                    @elseif($notification->type == 5)
                        <div class="w-full">{{__('Отклик к заданию')}} <br>
                            <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">“{{$notification->name_task}}" №{{$notification->task_id}}</a> <br>
                            {{__('задания отправлен')}}
                        </div>
                    @else
                        <div class="w-full"> {!!__('Заказчик указал, что вы выполнили <br> задание')!!}
                            <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">“{{$notification->name_task}}"  №{{$notification->task_id}}</a> <br>
                            {{__(' и оставил вам отзыв')}}
                        </div>
                    @endif
                </div>
            </li>

        @endforeach
    </ul>

</div>
{{-- modal notification --}}

<div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" id="modal-id121"
     style="background-color:rgba(0,0,0,0.5)">
    <div class="relative w-auto my-6 mx-auto max-w-3xl" id="modal-id121">
        <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
            <div class=" text-center p-12  rounded-t">
                <button type="submit" onclick="toggleModal121('modal-id121', 'Title', 'Description', 0)" class="rounded-md w-100 h-16 absolute top-1 right-4">
                    <i class="fas fa-times  text-slate-400 hover:text-slate-600 text-xl w-full"></i>
                </button>
                <h3 class="font-medium text-4xl block mt-4" id="title_notification">Title</h3>
            </div>
            <div class="mb-4 h-auto p-4">
                <p class="text-center h-full w-full text-lg" id="description_notification">Description</p>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id121-backdrop"></div>

{{-- end modal notification --}}
@auth
    @php
        $array_cats_user = auth()->user()->category_id;
        $user = auth()->id();
    @endphp

    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        // Pusher.logToConsole = true;

        let pusher = new Pusher('{{env("MIX_PUSHER_APP_KEY")}}', {
            cluster: '{{env("PUSHER_APP_CLUSTER")}}',
            // encrypted: true,

            wsHost: 'ws.smarts.uz', // 'bidding.uztelecom.uz',
            wsPort: 6001,
            forceTLS: false,
            disableStats: true,
        });
        let channel = pusher.subscribe('user-notification-send-' + {{auth()->id()}});
        channel.bind('server-user', function (data) {
            data = JSON.parse(data.data)
            let count = parseInt($('#content_count').text())
            count = count ? count : 0;
            count += 1
            $('#content_count').text(count)
            $('#notifs').append(`
            <li>
                <a href=${data['url']} class="text-sm font-bold hover:bg-gray-100 text-gray-700 block px-4 py-2">${data['name']}</a>
            </li>
            `)
        });

        function toggleModal121(modalID121, title, description, not_id) {
            $.ajax({
                url: '/read-notification/' + not_id,
                method: "GET",
                // data: { _token: access_token, id, type },
                dataType: "JSON",
                success: (data) => {

                },
                error: () => {
                    console.error("Error, check server response!");
                },
            });
            $('#title_notification').val(title)
            $('#description_notification').val(description)
            document.getElementById(modalID121).classList.toggle("hidden");
            document.getElementById(modalID121 + "-backdrop").classList.toggle("hidden");
            document.getElementById(modalID121).classList.toggle("flex");
            document.getElementById(modalID121 + "-backdrop").classList.toggle("flex");
        }
    </script>
@endauth
