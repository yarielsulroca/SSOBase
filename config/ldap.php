<?php

return [
    'default' => env('LDAP_CONNECTION', 'default'),

    'connections' => [
        'default' => [
            'hosts' => [env('LDAP_HOST', '10.128.225.9')],
            'port' => env('LDAP_PORT', 389),
            'base_dn' => env('LDAP_BASE_DN', 'DC=tuteurgroup,DC=com'),
            'username' => env('LDAP_USERNAME', ''),
            'password' => env('LDAP_PASSWORD', ''),
            'use_ssl' => env('LDAP_SSL', false),
            'use_tls' => env('LDAP_TLS', false),
            'version' => 3,
            'timeout' => 5,
        ],
    ],

    'logging' => [
        'enabled' => env('LDAP_LOGGING', true),
        'channel' => env('LDAP_LOG_CHANNEL', 'stack'),
    ],
];
