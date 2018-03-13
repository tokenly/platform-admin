<?php

return [
    'navigation' => [
        // [
        //     'route'        => 'albums.index', // you can override defaults by route name such as 'user.index'
        //     'activePrefix' => 'albums',
        //     'label'        => 'Albums',
        // ],
        // [
        //     'route'    => 'user.index', // you can override defaults by route name such as 'user.index'
        //     'disabled' => true,
        // ],
    ],
    'control' => [
        'versionFilepath' => env('PLATFORM_CONTROL_VERSION_FILEPATH', '/app/version'),
        'healthFilepath' => env('PLATFORM_CONTROL_HEALTH_FILEPATH', '/app/current/public/_health_down'),
        'authBasicUsername' => env('PLATFORM_CONTROL_AUTH_USERNAME', 'tkadmin'),
        'authBasicPassword' => env('PLATFORM_CONTROL_AUTH_PASSWORD', null),
    ],
    'healthcheck' => [
        'checkDatabase' => env('PLATFORM_HEALTH_CHECK_DB', true),
    ],
    'console' => [
        'use_background_queue' => env('PLATFORM_CONSOLE_QUEUE_COMMANDS', false),
        'pusher_channel_base' => 'platformadmin',
        'queue' => 'platform_artisan_command',
    ],
];
