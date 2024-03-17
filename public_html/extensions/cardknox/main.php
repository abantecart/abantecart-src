<?php

if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}
include_once(DIR_EXT.'cardknox/core/cardknox_hooks.php');

$controllers = [
    'storefront' => [
        'responses/extension/cardknox'
    ],
    'admin' => [
        'responses/extension/cardknox'
    ]
];

$models = [
    'storefront' => [
        'extension/cardknox'
    ],
    'admin' => [
        'extension/cardknox'
    ]
];

$templates = [
    'storefront' => [],
    'admin' => [
        'pages/sale/cardknox_payment_details.tpl'
    ]
];

$languages = [
    'storefront' => [
        'english/cardknox/cardknox'
    ],
    'admin' => [
        'english/cardknox/cardknox'
    ]
];

