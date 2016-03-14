<?php


if (! defined ( 'DIR_CORE' )) {
header ( 'Location: static_pages/' );
}

$controllers = array(
    'storefront' => array(),
    'admin' => array('responses/extension/default_twilio'));

$models = array(
    'storefront' => array(),
    'admin' => array());

$templates = array(
    'storefront' => array(),
    'admin' => array('responses/extension/default_twilio_test.tpl'));

$languages = array(
    'storefront' => array(
            'english/default_twilio/default_twilio'
    ),
    'admin' => array(
        'english/default_twilio/default_twilio'));

