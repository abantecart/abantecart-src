<?php
require_once DIR_EXT . 'default_authorizenet' . DS .'core'.DS.'hooks.php';
require_once DIR_EXT . 'default_authorizenet' . DS . 'vendor' . DS . '/autoload.php';

$controllers = [
    'storefront' => [
        'responses/extension/default_authorizenet'
    ],
    'admin' => [],
];

$models = [
    'storefront' => [
        'extension/default_authorizenet'
    ],
    'admin'      => [
        'extension/default_authorizenet'
    ],
];

$languages = [
    'storefront' => [
        'default_authorizenet/default_authorizenet',
    ],
    'admin'      => [
        'default_authorizenet/default_authorizenet',
    ],
];

$templates = [
    'storefront' => [
        'responses/default_authorizenet.tpl',
    ],
    'admin'      => [],
];