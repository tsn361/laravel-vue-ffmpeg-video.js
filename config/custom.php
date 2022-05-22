<?php

return [
    'app' => [
        'title' => 'Letsmints',
        'subtitle' => '',
        'email' => '' 
    ],   
    'user_role' => [
        'default' => env('USER_ROLE_LITE', 'LITE'),
        'admin' => env('USER_ROLE_ADMIN', 'ADMIN'),
        'super_admin' => env('USER_ROLE_SUPER_ADMIN', 'SUPER_ADMIN'),
        'lite' => env('USER_ROLE_LITE', 'LITE'),
        'pro' => env('USER_ROLE_LITE', 'PRO')
    ]
];