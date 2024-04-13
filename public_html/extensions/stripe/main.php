<?php

if (!class_exists('ExtensionStripe')) {
    require_once(DIR_EXT.'stripe'.DS.'vendor'.DS.'autoload.php');
    require_once(DIR_EXT . 'stripe'.DS.'core'.DS.'stripe_modules.php');
    require_once(DIR_EXT.'stripe'.DS.'core'.DS.'stripe_hooks.php');
}

$controllers = [
    'storefront' => ['responses/extension/stripe'],
    'admin'      => [
        'responses/extension/stripe',
        'pages/extension/stripe_settings',
    ],
];

$models = [
    'storefront' => ['extension/stripe'],
    'admin'      => ['extension/stripe'],
];

$languages = [
    'storefront' => [
        'stripe/stripe',
    ],
    'admin'      => [
        'stripe/stripe',
    ],
];

$templates = [
    'storefront' => [
        'responses/stripe.tpl',
    ],
    'admin'      => [
        'pages/extension/stripe_settings.tpl',
        'pages/sale/stripe_payment_details.tpl',
    ],
];
