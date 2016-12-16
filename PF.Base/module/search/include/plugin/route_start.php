<?php

\Core\Api\ApiManager::register([
    'search' => [
        'api_service' => 'search.api',
        'maps' => [
            'get' => 'get'
        ]
    ],
]);