<script src="https://js.pusher.com/7.0.3/pusher.min.js"></script>
<script>
    // Enable pusher logging - don't include this in production
    // Pusher.logToConsole = true;

    var pusher = new Pusher("{{ config('chatify.pusher.key') }}", {
        // encrypted: true,
        cluster: '{{env("PUSHER_APP_CLUSTER")}}',
        wsHost: '{{env('WEBSOCKET_SERVER_HOST')}}',
        wsPort: 6001,
        forceTLS: false,
        disableStats: true,
        authEndpoint: 'http://' + {{env('WEBSOCKET_SERVER_HOST')}} +'/chat/pusher/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }
    });
</script>
<script src="{{ asset('js/chatify/code.js') }}"></script>
