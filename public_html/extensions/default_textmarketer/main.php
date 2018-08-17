<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

$controllers = array(
    'storefront' => array(),
    'admin'      => array('responses/extension/default_textmarketer'),
);

$models = array(
    'storefront' => array(),
    'admin'      => array(),
);

$templates = array(
    'storefront' => array(),
    'admin'      => array('responses/extension/default_textmarketer_test.tpl'),
);

$languages = array(
    'storefront' => array(
        'english/default_textmarketer/default_textmarketer',
    ),
    'admin'      => array(
        'english/default_textmarketer/default_textmarketer',
    ),
);

