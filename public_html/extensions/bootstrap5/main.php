<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

if (!class_exists('ExtensionBootstrap6')) {
    include_once('core/bootstrap5_hook.php');
}
$controllers = [
    'storefront' => [],
    'admin'      => [],
];

$models = [
    'storefront' => [],
    'admin'      => [],
];

$templates = [
    'storefront' => [],
    'admin'      => [],
];

$languages = [
    'storefront' => [],
    'admin'      => [
        'english/bootstrap5/bootstrap5',
    ],
];

