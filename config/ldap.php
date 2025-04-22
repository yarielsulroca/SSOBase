<?php

return [
    'default' => env('LDAP_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'hosts' => [env('LDAP_HOST', '10.128.225.9')],
            'port' => env('LDAP_PORT', 389),
            'base_dn' => env('LDAP_BASE_DN', 'DC=tuteurgroup,DC=com'),
            'username' => env('LDAP_USERNAME', 'testsso@tuteurgroup.com'),
            'password' => env('LDAP_PASSWORD'),
            'use_ssl' => env('LDAP_SSL', false),
            'use_tls' => env('LDAP_TLS', false),
            'timeout' => 5,
            'options' => [
                LDAP_OPT_PROTOCOL_VERSION => 3,
                LDAP_OPT_REFERRALS => 0,
                LDAP_OPT_NETWORK_TIMEOUT => 5,
            ],
        ],
    ],
    'logging' => true,
    'cache' => [
        'enabled' => env('LDAP_CACHE', false),
        'driver' => 'file',
        'ttl' => 300,
    ],
];
