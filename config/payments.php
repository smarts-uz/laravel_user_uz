<?php

return [
    'click' => [
        'return_url' => env('CLICKUZ_RETURN_URL'),
        'service_id' => env('CLICKUZ_SERVICE_ID'),
        'merchant_id' => env('CLICKUZ_MERCHANT_ID')
    ],

    'payme' => [
        'merchant_id' => env('PAYCOM_MERCHANT'),
        'paycom_login' => env('PAYCOM_LOGIN', 'Paycom'),
        'paycom_key' => env('PAYCOM_KEY', ''),
        'paycom_key_test' => env('PAYCOM_KEY_TEST', '')
    ],

    'paynet' => [
        'minimum_amount' => env('MINIMUM_AMOUNT', ''),
        'paynet_username' => env('PAYNET_USERNAME', ''),
        'paynet_password' => env('PAYNET_PASSWORD', ''),
        'paynet_service_id' => env('PAYNET_SERVICE_ID', ''),
        'index_url' => env('INDEX_URL', ''),
        'wsdl_url' => env('WSDL_URL', '')
    ]
];
