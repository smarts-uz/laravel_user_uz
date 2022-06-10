<?php

return [

    /*
    |-------------------------------------
    | Messenger display name
    |-------------------------------------
    */
    'name' => env('CHATIFY_NAME', 'UService Messanger'),

    /*
    |-------------------------------------
    | Routes configurations
    |-------------------------------------
    */
    'routes' => [
        'prefix' => env('CHATIFY_ROUTES_PREFIX', 'chat'),
        'middleware' => env('CHATIFY_ROUTES_MIDDLEWARE', ['web','auth']),
        'namespace' => env('CHATIFY_ROUTES_NAMESPACE', 'Chatify\Http\Controllers'),
        //   App\Http\Controllers\vendor\Chatify
    ],
    'api_routes' => [
        'prefix' => env('CHATIFY_API_ROUTES_PREFIX', 'api/chat'),
        'middleware' => env('CHATIFY_API_ROUTES_MIDDLEWARE', ['custom.auth:api']),
        'namespace' => env('CHATIFY_API_ROUTES_NAMESPACE', 'Chatify\Http\Controllers\Api'),
    ],

    /*
    |-------------------------------------
    | Pusher API credentials
    |-------------------------------------
    */
    'pusher' => [
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => false,
            'host' => (string) env('WEBSOCKET_SERVER_HOST'),
            'port' => env('WEBSOCKET_SERVER_PORT'),
            'scheme' => 'https',
            'forceTLS' => true,
            'disableStats' => true,
        ],
    ],

    /*
    |-------------------------------------
    | User Avatar
    |-------------------------------------
    */
    'user_avatar' => [
        'folder' => '',
        'default' => 'default.png',
    ],

    /*
    |-------------------------------------
    | Attachments
    |-------------------------------------
    */
    'attachments' => [
        'folder' => 'attachments',
        'download_route_name' => 'attachments.download',
        'allowed_images' => (array) ['png','jpg','jpeg','gif'],
        'allowed_files' => (array) ['zip','rar','txt'],
        'max_upload_size' => 1
    ],
];
