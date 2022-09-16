<?php

return [
    'default' => env('ELASTIC_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'hosts' => [
                env('ELASTIC_HOST', 'localhost:9200'),
            ],
        ],
    ],
];
