<?php

return [

    'namespace' => '',

    'client' => 'predis',

    'conn' => 'default',

    'name' => 'http_server_requests_seconds',

    'help' => 'duration of http_requests',

    'prefix' => 'bes:' . env('APP_ENV') . ':prometheus',

    'namespace_http_server' => 'http',

    'histogram_buckets' => [1500, 3000],
];
