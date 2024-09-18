<?php

return [
    'paypal' => [
        'logo'         => 'assets/images/payments/paypal.png',
        'visible'      => 1,
        'description'  => '',
        'environments' => [
            'live'    => 'Live',
            'sandbox' => 'Sandbox',
        ],
        'settings'     => [
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled'         => true,
            'log.FileName'           => storage_path('logs/paypal.log'),
            'log.LogLevel'           => 'ERROR',
        ],
    ],

    'stripe' => [
        'logo'         => 'assets/images/payments/stripe.png',
        'visible'      => 1,
        'description'  => 'Visa, MasterCard, American Express, Diners Club, JCB, and Discover',
        'environments' => [],
    ],

    'braintree' => [
        'logo'         => 'assets/images/payments/braintree.png',
        'visible'      => 1,
        'description'  => 'Visa, Mastercard, Amex, Discover, JCB, Diners, Maestro, UnionPay and Paypal',
        'environments' => [
            'production' => 'Live',
            'sandbox'    => 'Sandbox',
        ],
    ],

    'paydunya' => [
        'logo'         => 'assets/images/payments/paydunya.png',
        'visible'      => 1,
        'description'  => 'Orange Money, Joni Joni, Vitfè, VISA, MasterCard and GIM-UEMOA',
        'environments' => [
            'live' => 'Live',
            'test' => 'Sandbox',
        ],
    ],

    'mobile_direct_debit' => [
        'logo'         => 'assets/images/payments/mobile_direct_debit.png',
        'visible'      => 1,
        'description'  => 'Mobile Direct Debit',
        'environments' => [],
    ],

    'paysera' => [
        'logo'         => 'assets/images/payments/paysera.png',
        'visible'      => 1,
        'description'  => '',
        'environments' => [
            'production' => 'Live',
            'sandbox'    => 'Sandbox',
        ],
    ],

    'twocheckout' => [
        'logo'         => 'assets/images/payments/2checkout.png',
        'visible'      => 1,
        'description'  => '',
    ],

    'kevin' => [
        'logo'         => 'assets/images/payments/kevin.png',
        'visible'      => 0,
        'description'  => '',
    ],
];