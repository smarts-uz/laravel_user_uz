@php
    $notifications = \App\Services\NotificationService::getNotifications(auth()->user());
    $count = $notifications->count();
@endphp
@if($count > 0)
    <div id="all_notification_count"
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
    <ul class="py-1 overflow-y-auto max-h-96" id="notifications" aria-labelledby="dropdown">
        @foreach($notifications as $notification)
            <li class="border-b-2 border-gray-500 flex gap-x-2 p-3 text-gray-800 hover:bg-yellow-200">
                <a href="{{route('show_notification', [$notification])}}">
                    <div class="flex flex-col w-full">
                        <p class="text-right text-sm">{{$notification->created_at->format('d M')}}</p>
                        <div class="w-full flex flex-row gap-x-4">
                            <i class="fas fa-bell text-yellow-500 text-xl"></i>
                            <div>
                                <p>{{\App\Services\NotificationService::titles($notification->type)}}</p>
                                <p>
                                    {{ \App\Services\NotificationService::descriptions($notification)}}
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
    @if($count !== 0)
        <span id="clear_notification_desk"  class="clear-notification flex justify-center cursor-pointer text-center my-0 mx-auto mb-1.5 px-5 py-2 font-sans  text-sm  font-semibold text-black-50 rounded-full max-w-full w-3/4  bg-yellow-400 blur-sm"><p class="font-sans  text-sm  font-semibold text-black-50">{{__('Пометить, как прочитанное')}}</p></span>
    @endif

</div>
{{-- modal notification --}}

<div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
    id="modal-id121" style="background-color:rgba(0,0,0,0.5)">
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
        $user = auth()->id();
    @endphp

    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = false;

        let pusher = new Pusher('{{env("MIX_PUSHER_APP_KEY")}}', {
            cluster: '{{env("PUSHER_APP_CLUSTER")}}',

            encrypted: true,
            forceTLS: true,
            disableStats: false,
        });
        let channel = pusher.subscribe('user-notification-send-' + {{auth()->id()}});
        channel.bind('server-user', function (data) {
            data = JSON.parse(data.data)
            // console.log(data)

            let element = $('#all_notification_count');
            let element2 = $('#content_count');
            let count = element.text();
            let count2 = element2.text();
            count = isNumeric(String(count)) ? parseInt(count) : 0;
            count2 = isNumeric(String(count2)) ? parseInt(count2) : 0;
            count += 1
            count2 += 1
            element.text(String(count))
            element2.text(String(count2))
            $('#notifications').prepend(`
            <li class="border-b-2 border-gray-500 flex gap-x-2 p-3 text-gray-800">
                <div class="flex flex-col w-full">
                    <p class="text-right text-sm">${data['created_date']}</p>
                    <div class="w-full flex flex-row gap-x-4">
                        <i class="fas fa-bell text-yellow-500 text-xl"></i>
                        <div>
                            <p>${data['title']}</p>
                            <a class="hover:text-red-500" href=${data['url']}>
                                ${data['description']}
                            </a>
                        </div>
                    </div>
                </div>
            </li>
            `)
        });

        function isNumeric(str) {
            if (typeof str != "string") return false // we only process strings!
            return !isNaN(str) && // use type coercion to parse the _entirety_ of the string (`parseFloat` alone does not do this)...
                !isNaN(parseFloat(str)) // ...and ensure strings of whitespace fail
        }

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

        var response = '';

        $(".clear-notification").click(function () {
            $.ajax({
                url: '/read-all-notification/{{auth()->id()}}',
                method: "GET",
                dataType: "JSON",
                success: function (text) {
                    response = text;
                }
            })

            $('#notifications').empty()
            $('#all_notification_count').addClass('hidden');
            $('#clear_notification_desk').addClass('hidden');
            console.log(response);
        });
    </script>
@endauth
