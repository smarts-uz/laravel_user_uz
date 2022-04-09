@php
$notifications = App\Models\Notification::query()->where('user_id', auth()->id())->get();
$count = $notifications->count();
@endphp
@if($count > 0)
<div id="content_count" class="w-4 h-4 absolute rounded-full bg-red-500 ml-3 text-white text-xs text-center">{{$count}}</div>
@endif
<button class="focus:outline-none" type="button" data-dropdown-toggle="dropdown">
<i class="xl:text-2xl lg:text-xl mr-6 text-gray-500 hover:text-yellow-500 far fa-bell"></i>
</button>
<!-- Dropdown menu -->
<div class="hidden bg-white text-base z-50 list-none divide-y divide-gray-100 rounded shadow my-4" id="dropdown">
<div class="px-4 py-3">
    <span class="block text-base font-bold">{{__('Уведомления')}}</span>
</div>
<ul class="py-1 overflow-y-auto max-h-96" id="notifs" aria-labelledby="dropdown">
    @foreach($notifications as $notification)
        <li>
            <button onclick="toggleModal121('modal-id121')" class="text-sm font-bold hover:bg-gray-100 text-gray-700 block px-4 py-2">
                {{$notification->name_task}}
            </button>
        </li>
    @endforeach
</ul>
</div>

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

            wsHost:  'ws.smarts.uz', // 'bidding.uztelecom.uz',
            wsPort: 6001,
            forceTLS: false,
            disableStats: true,
        });
        let channel = pusher.subscribe('user-notification-send-' + {{auth()->id()}});
        channel.bind('server-user', function(data) {
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
            console.log(data)
        });

        // Enable pusher logging - don't include this in production
    {{--        Pusher.logToConsole = true;--}}

    {{--        var pusher = new Pusher('ec2f696b4a7b3e054939', {--}}
    {{--            cluster: 'ap2'--}}
    {{--        });--}}

    {{--        var channel = pusher.subscribe('my-channel');--}}
    {{--        channel.bind('my-event', function (data) {--}}
    {{--            alert(data)--}}
    {{--            if (Number(data["type"]) === 1) {--}}

    {{--                const for_check_cat_id = [<? echo $array_cats_user ?>];--}}

    {{--                let num_cat_id = Number(data["id_cat"]);--}}

    {{--                let check_arr = for_check_cat_id.includes(num_cat_id);--}}

    {{--                if (check_arr === true) {--}}
    {{--                    var content_count = document.getElementById('content_count').innerHTML;--}}
    {{--                    let count_for_inner = Number(content_count) + 1;--}}
    {{--                    document.getElementById('content_count').innerHTML = count_for_inner;--}}

    {{--                    let el_for_create = document.getElementById('for_append_notifications');--}}

    {{--                    el_for_create.insertAdjacentHTML('afterend', `--}}
    {{--<li>--}}
    {{--<a href="/detailed-tasks/` + Number(data["id_task"]) + `" class="text-sm font-bold hover:bg-gray-100 text-gray-700 block px-4 py-2">` + data["title_task"] + `</a>--}}
    {{--</li>--}}
    {{-- `);--}}

    {{--                }--}}

    {{--            }--}}

    {{--            if (Number(data["type"]) === 2) {--}}

    {{--                let user_id_for_js2 = Number(<? echo $array_cats_user ?>);--}}

    {{--                if (user_id_for_js2 === Number(data["user_id_fjs"])) {--}}
    {{--                    var content_count = document.getElementById('content_count').innerHTML;--}}
    {{--                    let count_for_inner = Number(content_count) + 1;--}}
    {{--                    document.getElementById('content_count').innerHTML = count_for_inner;--}}

    {{--                    let el_for_create = document.getElementById('for_append_notifications');--}}

    {{--                    el_for_create.insertAdjacentHTML('afterend', `--}}
    {{--<li>--}}
    {{--<a href="/detailed-tasks/` + Number(data["id_task"]) + `" class="text-sm font-bold hover:bg-gray-100 text-gray-700 block px-4 py-2">У вас новый отклик</a>--}}
    {{--</li>--}}
    {{-- `);--}}

    {{--                }--}}

    {{--            }--}}
    {{--            if (Number(data["type"]) === 3) {--}}

    {{--                let user_id_for_js3 = Number(<? echo $array_cats_user ?>);--}}

    {{--                if (user_id_for_js3 === Number(data["user_id_fjs"])) {--}}
    {{--                    var content_count = document.getElementById('content_count').innerHTML;--}}
    {{--                    let count_for_inner = Number(content_count) + 1;--}}
    {{--                    document.getElementById('content_count').innerHTML = count_for_inner;--}}

    {{--                    let el_for_create = document.getElementById('for_append_notifications');--}}

    {{--                    el_for_create.insertAdjacentHTML('afterend', `--}}
    {{--<li>--}}
    {{--<a href="/detailed-tasks/` + Number(data["id_task"]) + `" class="text-sm font-bold hover:bg-gray-100 text-gray-700 block px-4 py-2">Вы получили задание</a>--}}
    {{--</li>--}}
    {{--`);--}}

    {{--                }--}}

    {{--            }--}}
    {{--            if (Number(data["type"]) === 4) {--}}

    {{--                const for_check_cat_id = [<? echo $user ?>];--}}

    {{--                let num_cat_id = Number(data["user_id"]);--}}

    {{--                let check_arr = for_check_cat_id.includes(num_cat_id);--}}

    {{--                if (check_arr === true) {--}}
    {{--                    var content_count = document.getElementById('content_count').innerHTML;--}}
    {{--                    let count_for_inner = Number(content_count) + 1;--}}
    {{--                    document.getElementById('content_count').innerHTML = count_for_inner;--}}

    {{--                    let el_for_create = document.getElementById('for_append_notifications');--}}

    {{--                    el_for_create.insertAdjacentHTML('afterend', `--}}
    {{--<li>--}}
    {{--<a href="/detailed-tasks/` + Number(data["task_id"]) + `" class="text-sm font-bold hover:bg-gray-100 text-gray-700 block px-4 py-2">` + data["task_name"] + `</a>--}}
    {{--</li>--}}
    {{--`);--}}

    {{--                }--}}

    {{--            }--}}

    {{--        });--}}
    </script>
@endauth
