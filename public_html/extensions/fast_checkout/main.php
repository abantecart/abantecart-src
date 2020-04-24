<?php
/*------------------------------------------------------------------------------
$Id$

This file and its content is copyright of AlgoZone Inc - ©AlgoZone Inc 2003-2016. All rights reserved.

You may not, except with our express written permission, modify, distribute or commercially exploit the content. Nor may you transmit it or store it in any other website or other form of electronic retrieval system.
------------------------------------------------------------------------------*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

if (!class_exists('ExtensionFastCheckout')) {
    include_once('core/fast_checkout.php');
}
$controllers = array(
    'storefront' => array(
        'responses/checkout/pay',
        'responses/includes/head',
        'responses/includes/footer',
        'responses/account/order_details',
        'pages/account/order_details',
        'pages/checkout/fast_checkout',
        'pages/checkout/fast_checkout_success',
    ),
    'admin'      => array(),
);

$models = array(
    'storefront' => array('extension/fast_checkout'),
    'admin'      => array('extension/fast_checkout'),
);

$templates = array(
    'storefront' => array(
        'embed/footer.post.tpl',
        'common/footer.post.tpl',
        'pages/product/product.tpl',
        'pages/product/product_listing.tpl',
        'pages/account/order_details.tpl',
        'blocks/product_list.tpl',
        'blocks/fast_checkout_summary.tpl',
        'responses/checkout/payment.tpl',
        'responses/checkout/payment_select.tpl',
        'responses/checkout/payment_form.tpl',
        'responses/checkout/address.tpl',
        'responses/checkout/login.tpl',
        'responses/checkout/main.tpl',
        'responses/checkout/success.tpl',
        'responses/includes/head.tpl',
        'responses/includes/footer.tpl',
        'responses/includes/page_header.tpl',
        'responses/includes/page_footer.tpl',
        'embed/js_product_sc.tpl',
        'embed/product/product_sc.tpl',
        'embed/account/order_details.tpl',
        'pages/checkout/fast_checkout.tpl',
        'pages/checkout/fast_checkout_success.tpl',
    ),
    'admin'      => array(),
);

$languages = array(
    'storefront' => array(
        'english/fast_checkout/fast_checkout',
    ),
    'admin'      => array(
        'english/fast_checkout/fast_checkout',
    ),
);

