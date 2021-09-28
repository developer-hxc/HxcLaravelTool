<?php
return [
    'controller_path' => env("HXC_CONTROLLER_PATH",'Api/V1'),

    'sms' => [
        'exp' => env('QTTX_SMS_EXP',300),
        'gateway' => [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,
            // 默认发送配置
            'default' => [
                // 默认可用的发送网关
                'gateways' => [
                    'QTTXGateway'
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'QTTXGateway' => [
                    'account' => env('QTTX_SMS_ACCOUNT',''),
                    'password' => env('QTTX_SMS_PASSWORD',''),
                ]
            ],
        ]
    ]
];
