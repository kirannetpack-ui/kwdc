<?php

return [
    'default' => env('MAIL_MAILER', 'smtp'),
    
    'mailers' => [
    'smtp' => [
        'transport' => 'smtp',
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'kiran.kwdc@gmail.com',
        'password' => 'nuzpnwuaavxynsdg',
        'timeout' => null,
         'local_domain' => env('MAIL_EHLO_DOMAIN', 'localhost'),
        ],
        'ses' => [
            'transport' => 'ses',
        ],
        'mailgun' => [
            'transport' => 'mailgun',
        ],
        'postmark' => [
            'transport' => 'postmark',
        ],
        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],
        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],
        'array' => [
            'transport' => 'array',
        ],
        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],
        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
        ],
    ],
    
    'from' => [
    'address' => 'kiran.kwdc@gmail.com',
    'name' => 'KTM-WDC : kKTM - Warehouse & Distribution Center',
],
    
    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
];