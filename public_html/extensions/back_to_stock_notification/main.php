<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}


if(!class_exists('ExtensionBackToStockNotification')){
    include_once('core/back_to_stock_notification_hooks.php');
}
$controllers = array(
    'storefront' => array('responses/extension/back_to_stock'),
    'admin' => array());

$models = array(
    'storefront' => array('extension/back_to_stock',),
    'admin' => array('extension/back_to_stock','user/user'
        ));

$templates = array(
    'storefront' => array('pages/product/product.post.tpl'),
    'admin' => array());

$languages = array(
    'storefront' => array(),
    'admin' => array(
        'english/back_to_stock_notification/back_to_stock_notification'));

