@php
    $notifications = \App\Services\NotificationService::getNotifications(auth()->user());
    $count = $notifications->count();
@endphp
@if($count > 0)
    <div id="content_count"
         class="w-4 h-4 absolute rounded-full bg-red-500 ml-3 text-white text-xs text-center">{{$count}}</div>
@endif
<button class="focus:outline-none" type="button" data-dropdown-toggle="dropdown">
    <i class="text-2xl lg:mr-6 mr-0 text-gray-500 hover:text-yellow-500 far fa-bell"></i>
</button>
<!-- Dropdown menu -->
<div class="hidden bg-white text-base z-50 list-none divide-y divide-gray-100 rounded shadow my-4 w-96" id="dropdown">
    <div class="px-4 py-3">
        @if($count == 0)
            <span
                class="block text-base font-bold text-center">{{__('У вас на данный момент нет никаких уведомлений')}}</span>
        @else
            <span class="block text-base font-bold">{{__('Уведомления')}}</span>
        @endif
    </div>
    <ul class="py-1 overflow-y-auto max-h-96" id="notifs" aria-labelledby="dropdown">
        @foreach($notifications as $notification)
            <li class="border-b-2 border-gray-500 flex gap-x-2 p-3 text-gray-800">
                <div class="flex flex-col w-full">
                    <p class="text-right text-sm">{{$notification->created_at->format('d M')}}</p>
                    @if($notification->type == \App\Models\Notification::TASK_CREATED)
                        <div class="w-full flex flex-row gap-x-4">
                            <i class="fas fa-bell text-yellow-500 text-xl"></i>
                            <div>
                                <p>{{__('Новая задания')}}</p>
                                <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">
                                    {{ __('task_name  №task_id с бюджетом до task_budget', [
                                        'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
                                        'budget' => number_format($notification->task?->budget, 0, '.', ' ')])
                                    }}
                                </a>
                            </div>
                        </div>
                    @elseif($notification->type == \App\Models\Notification::NEWS_NOTIFICATION || $notification->type == \App\Models\Notification::SYSTEM_NOTIFICATION)
                        <div class="w-full flex flex-row gap-x-4">
                            <i class="fas fa-bookmark text-xl text-yellow-500"></i>
                            <div>
                                <p>{{__('Новости')}}</p>
                                <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">
                                    {{__('Важные новости и объявления для вас')}}
                                </a>
                            </div>
                        </div>
                    @elseif($notification->type == \App\Models\Notification::GIVE_TASK)
                        <div class="w-full flex flex-row gap-x-4">
                            <i class="fas fa-comment text-xl text-yellow-500"></i>
                            <div>
                                <p>{{__('Предложение')}}</p>
                                <a class="hover:text-blue-500"
                                   href="{{route('show_notification', [$notification])}}">
                                    {{__('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                                        'task_name' => $notification->name_task, 'task_id' => $notification->task_id, 'task_user' => $notification->user?->name
                                    ])}}
                                </a>
                            </div>
                        </div>
                    @elseif($notification->type == \App\Models\Notification::RESPONSE_TO_TASK)
                        <div class="w-full flex flex-row gap-x-4">
                            <i class="fas fa-check-circle text-yellow-500 text-xl"></i>
                            <div>
                                <p> {{__('Отклик к заданию')}}</p>
                                <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">
                                    {{__('task_name №task_id отправлен', ['task_name' => $notification->name_task, 'task_id' => $notification->task_id])}}
                                </a>
                            </div>
                        </div>
                    @elseif($notification->type == \App\Models\Notification::SEND_REVIEW)
                        <div class="w-full flex flex-row gap-x-4">
                            <i class="fas fa-star text-xl text-yellow-500"></i>
                            <div>
                                <p>{{__('Задание выполнено')}}</p>
                                {{__('Заказчик указал, что вы выполнили  задание')}}
                                <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">
                                    “{{$notification->name_task}}" №{{$notification->task_id}}
                                </a>
                                {{__(' и оставил вам отзыв')}}
                            </div>
                        </div>
                    @elseif($notification->type == \App\Models\Notification::SELECT_PERFORMER)
                        <div class="w-full flex flex-row gap-x-4">
                            <i class="fas fa-user text-xl text-yellow-500"></i>
                            <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">
                                {{__('Вас выбрали исполнителем  в задании task_name №task_id task_user', ['task_name' => $notification->name_task,
                                'task_id' => $notification->task_id, 'task_user' => $notification->user?->name])}}
                            </a>
                        </div>
                    @elseif ($notification->type == \App\Models\Notification::SEND_REVIEW_PERFORMER)
                        <div class="w-full flex flex-row gap-x-4">
                            <i class="fas fa-star text-xl text-yellow-500"></i>
                            <div>
                                <p>{{__('Новый отзыв')}}</p>
                                <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">
                                    {{ __('О вас оставлен новый отзыв') . " \"$notification->name_task\" №$notification->task_id"}}
                                </a>
                            </div>
                        </div>
                    @elseif ($notification->type == \App\Models\Notification::RESPONSE_TO_TASK_FOR_USER)
                        <div class="w-full flex flex-row gap-x-4">
                            <i class="fas fa-star text-xl text-yellow-500"></i>
                            <div>
                                <p>{{__('Новый отклик')}}</p>
                                <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">
                                    {{ __('performer откликнулся на задания task_name', [
                                            'performer' => $notification->performer?->name, 'task_name' => $notification->name_task
                                        ])
                                    }}
                                </a>
                            </div>
                        </div>
                    @elseif ($notification->type == \App\Models\Notification::CANCELLED_TASK && $notification->user_id == auth()->id())
                        <div class="w-full flex flex-row gap-x-4">
                            <i class="fas fa-star text-xl text-yellow-500"></i>
                            <div>
                                <p>{{__('3адание отменено')}}</p>
                                <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">
                                    {{ __('Ваше задание task_name №task_id было отменено', [
                                            'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
                                        ])
                                    }}
                                </a>
                            </div>
                        </div>
                    @elseif($notification->type == \App\Models\Notification::CANCELLED_TASK && $notification->performer_id == auth()->id())
                        <div class="w-full flex flex-row gap-x-4">
                            <i class="fas fa-star text-xl text-yellow-500"></i>
                            <div>
                                <p>{{__('3адание отменено')}}</p>
                                <a class="hover:text-red-500" href="{{route('show_notification', [$notification])}}">
                                    {{ __('3адание task_name №task_id было отменено', [
                                            'task_name' => $notification->name_task, 'task_id' => $notification->task_id,
                                        ])
                                    }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </li>

        @endforeach
    </ul>

</div>
{{-- modal notification --}}

<div
    class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
    id="modal-id121"
    style="background-color:rgba(0,0,0,0.5)">
    <div class="relative w-auto my-6 mx-auto max-w-3xl" id="modal-id121">
        <div
            class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
            <div class=" text-center p-12  rounded-t">
                <button type="submit" onclick="toggleModal121('modal-id121', 'Title', 'Description', 0)"
                        class="rounded-md w-100 h-16 absolute top-1 right-4">
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
            encrypted: true,

            wsHost: '{{env('WEBSOCKET_SERVER_HOST')}}',
            wsPort: {{env('WEBSOCKET_SERVER_PORT', 6001)}},
            wssPort: {{env('WEBSOCKET_SERVER_PORT', 6001)}},
            forceTLS: true,
            disableStats: false,
        });
        let channel = pusher.subscribe('user-notification-send-' + {{auth()->id()}});
        channel.bind('server-user', function (data) {
            data = JSON.parse(data.data)
            console.log(data)
            let count = parseInt($('#content_count').text())
            count = count ? count : 0;
            count += 1
            $('#content_count').text(count)
            $('#notifs').prepend(`
            <li>
                <a href=${data['url']} class="text-sm font-bold hover:bg-gray-100 text-gray-700 block px-4 py-2">${data['name']}</a>
            </li>
            `)
        });

        function toggleModal121(modalID121, title, description, not_id) {
            if (not_id !== 0) {
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
            }
            $('#title_notification').text(title)
            $('#description_notification').text(description)
            document.getElementById(modalID121).classList.toggle("hidden");
            document.getElementById(modalID121 + "-backdrop").classList.toggle("hidden");
            document.getElementById(modalID121).classList.toggle("flex");
            document.getElementById(modalID121 + "-backdrop").classList.toggle("flex");
        }
    </script>
@endauth
