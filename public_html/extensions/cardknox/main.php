<?php

if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}
if (!class_exists('ExtensionCardKnox')) {
    include_once('core/cardknox_hooks.php');
}

$controllers = array(
    'storefront' => array(
        'responses/extension/cardknox'),
    'admin' => array(
        'responses/extension/cardknox'
    ));

$models = array(
    'storefront' => array(
        'extension/cardknox'),
    'admin' => array(
        'extension/cardknox'
    )
);

$templates = array(
    'storefront' => array(),
    'admin' => array(
        'pages/sale/cardknox_payment_details.tpl'
    ));

$languages = array(
    'storefront' => array(
        'english/cardknox/cardknox'),
    'admin' => array(
        'english/cardknox/cardknox'));

