<?php

return [
    'token_lifetime' => env('ESKIZ_SMS_TOKEN_DURATION', 24 * 3600 * 30),
    'api_url' => env('ESKIZ_SMS_URL', 'http://notify.eskiz.uz/api/'),
    'email' => env('ESKIZ_SMS_EMAIL', 'sodikov42@gmail.com'),
    'password' => env('ESKIZ_SMS_PASSWORD', '2806507997'),
];
