<?php

return [
    'default' => env('HASH_DRIVER', 'bcrypt'),

    'drivers' => [
        'bcrypt' => [
            'rounds' => env('BCRYPT_ROUNDS', 12),
        ],


        'crypt' => [],
    ],
];
