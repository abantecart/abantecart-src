<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}
require_once DIR_EXT . 'default_twilio' . DS . 'core' . DS . 'lib' . DS . 'default_twilio.php';
$controllers = [
    'storefront' => [],
    'admin'      => ['responses/extension/default_twilio'],
];

$models = [
    'storefront' => [],
    'admin'      => [],
];

$templates = [
    'storefront' => [],
    'admin'      => ['responses/extension/default_twilio_test.tpl'],
];

$languages = [
    'storefront' => [
        'english/default_twilio/default_twilio',
    ],
    'admin'      => [
        'english/default_twilio/default_twilio',
    ],
];

