<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

if (!class_exists('')) {
    include_once('core/cardconnect_hooks.php');
}
$controllers = array(
    'storefront' => array(
        'responses/extension/cardconnect',
    ),
    'admin'      => array(
        'pages/extension/cardconnect_settings',
        'responses/extension/cardconnect',
    ),
);

$models = array(
    'storefront' => array(
        'extension/cardconnect',
    ),
    'admin'      => array('extension/cardconnect'),
);

$templates = array(
    'storefront' => array('extension/cardconnect_buttons.tpl'),
    'admin'      => array(
        'pages/extension/cardconnect.tpl',
        'pages/sale/cardconnect_payment_details.tpl',
    ),
);

$languages = array(
    'storefront' => array(
        'english/cardconnect/cardconnect',
    ),
    'admin'      => array(
        'english/cardconnect/cardconnect',
    ),
);
