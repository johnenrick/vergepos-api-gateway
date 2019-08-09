<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */

    'supportsCredentials' => false,
    'allowedOrigins' => ['*'],
    'allowedOriginsPatterns' => [],
    'allowedHeaders' => ['Authorization', 'authorization', 'Header', 'X-Requested-With', 'Content-Type', 'Accept'],
    'allowedMethods' => ['GET', 'POST', 'post', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'],
    'exposedHeaders' => ['Authorization'],
    'maxAge' => 0,

];
