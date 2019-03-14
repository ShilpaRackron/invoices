<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
		'mailer' => [
            'class' => yii\swiftmailer\Mailer::class,
            /* 'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com', //'sipuk2-20.nexcess.net',
                'username' => 'amazoninvoices2019@gmail.com', //'amazoninvoices@invoices.arsgate.com',
                'password' => 'MoronsMythicFadedKapok40',
                'port' => '587',
                'encryption' => 'tls',
            ], */
        ],
    ],
];
