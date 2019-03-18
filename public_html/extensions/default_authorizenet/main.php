<?php
require_once 'core/hooks.php';
require_once 'vendor/autoload.php';

$controllers = array(
    'storefront' => array('responses/extension/default_authorizenet'),
    'admin' => array(),
);

$models = array(
    'storefront' => array('extension/default_authorizenet'),
    'admin'      => array('extension/default_authorizenet'),
);

$languages = array(
    'storefront' => array(
        'default_authorizenet/default_authorizenet',
    ),
    'admin'      => array(
        'default_authorizenet/default_authorizenet',
    ),
);

$templates = array(
    'storefront' => array(
        'responses/default_authorizenet.tpl',
    ),
    'admin'      => array(),
);