<?php

return [
    'token_lifetime' => env('ESKIZ_SMS_TOKEN_DURATION', 24 * 3600 * 25),
    'api_url' => env('ESKIZ_SMS_URL', 'http://notify.eskiz.uz/api/'),
    'email' => env('ESKIZ_SMS_EMAIL', ''),
    'password' => env('ESKIZ_SMS_PASSWORD', ''),
    'from' => env('ESKIZ_SMS_FROM', '')
];
