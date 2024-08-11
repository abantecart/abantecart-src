<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

include_once(DIR_EXT.'cardconnect/core/cardconnect_hooks.php');

$controllers = [
    'storefront' => [
        'responses/extension/cardconnect',
    ],
    'admin'      => [
        'pages/extension/cardconnect_settings',
        'responses/extension/cardconnect',
    ],
];

$models = [
    'storefront' => [
        'extension/cardconnect',
    ],
    'admin'      => ['extension/cardconnect'],
];

$templates = [
    'storefront' => ['extension/cardconnect_buttons.tpl'],
    'admin'      => [
        'pages/extension/cardconnect.tpl',
        'pages/sale/cardconnect_payment_details.tpl',
    ],
];

$languages = [
    'storefront' => [
        'english/cardconnect/cardconnect',
    ],
    'admin'      => [
        'english/cardconnect/cardconnect',
    ],
];

if(!function_exists('getCardConnectEndPoint')) {
    function getCardConnectEndPoint($domainOnly = false)
    {
        $testMode = Registry::getInstance()->get('config')->get('cardconnect_test_mode');
        $output =  'https://' . ($testMode ? 'isv-uat.cardconnect.com/cardconnect/rest' : 'developer.cardconnect.com/cardconnect-api');
        if($domainOnly){
            $output = parse_url($output, PHP_URL_HOST);
        }
        return $output;
    }
}