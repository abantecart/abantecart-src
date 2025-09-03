<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}
include_once(DIR_EXT . 'sola/core/sola_hooks.php');

$controllers = [
    'storefront' => [
        'responses/extension/sola'
    ],
    'admin'      => [
        'responses/extension/sola'
    ]
];

$models = [
    'storefront' => [
        'extension/sola'
    ],
    'admin'      => [
        'extension/sola'
    ]
];

$templates = [
    'storefront' => [],
    'admin'      => [
        'pages/sale/sola_payment_details.tpl'
    ]
];

$languages = [
    'storefront' => [
        'english/sola/sola'
    ],
    'admin'      => [
        'english/sola/sola'
    ]
];

