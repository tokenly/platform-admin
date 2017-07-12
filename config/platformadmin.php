<?php

return [
    'routes' => [
    ],
    'control' => [
        'versionFilepath'   => env('PLATFORM_CONTROL_VERSION_FILEPATH', '/app/version'),
        'healthFilepath'    => env('PLATFORM_CONTROL_HEALTH_FILEPATH', '/app/current/public/_health_down'),
        'authBasicUsername' => env('PLATFORM_CONTROL_AUTH_USERNAME', 'tkadmin'),
        'authBasicPassword' => env('PLATFORM_CONTROL_AUTH_PASSWORD', null),
    ],
    'healthcheck' => [
        'checkDatabase'   => env('PLATFORM_HEALTH_CHECK_DB', true),
    ],
];