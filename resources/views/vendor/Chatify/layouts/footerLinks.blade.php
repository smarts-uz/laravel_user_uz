<script src="https://js.pusher.com/7.0.3/pusher.min.js"></script>
<script>
    // Enable pusher logging - don't include this in production
    //Pusher.logToConsole = true;
    var pusher = new Pusher("{{ config('chatify.pusher.key') }}", {
        // encrypted: true,
        cluster: '{{config("chatify.pusher.options.cluster")}}',
        wsHost: '{{config('chatify.pusher.options.host')}}',
        wsPort: {{config('chatify.pusher.options.port', 6001)}},
        wssPort: {{config('chatify.pusher.options.port', 6001)}},
        forceTLS: true,
        disableStats: false,
        authEndpoint: 'http://' + '{{config('chatify.pusher.options.host')}}' +'/chat/pusher/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }
    });
</script>
<script src="{{ asset('js/chatify/code.js') }}"></script>
