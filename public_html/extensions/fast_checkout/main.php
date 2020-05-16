<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

if (!class_exists('ExtensionFastCheckout')) {
    include_once('core/fast_checkout.php');
}
$controllers = [
    'storefront' => [
        'blocks/fast_checkout_cart_btn',
        'blocks/fast_checkout_summary',
        'responses/checkout/pay',
        'responses/includes/head',
        'responses/includes/footer',
        'responses/account/order_details',
        'responses/checkout/fast_checkout',
        'responses/checkout/fast_checkout_success',
        'responses/checkout/fast_checkout_summary',
        'pages/account/order_details',
        'pages/checkout/fast_checkout',
        'pages/checkout/fast_checkout_success',
    ],
    'admin'      => [],
];

$models = [
    'storefront' => ['extension/fast_checkout'],
    'admin'      => ['extension/fast_checkout'],
];

$templates = [
    'storefront' => [
        'embed/footer.post.tpl',
        'common/footer.post.tpl',
        'pages/account/order_details.tpl',
        'blocks/product_list.tpl',
        'blocks/fast_checkout_summary.tpl',
        'blocks/fast_checkout_cart_btn.tpl',
        'responses/checkout/payment.tpl',
        'responses/checkout/payment_select.tpl',
        'responses/checkout/payment_form.tpl',
        'responses/checkout/address.tpl',
        'responses/checkout/login.tpl',
        'responses/checkout/main.tpl',
        'responses/checkout/success.tpl',
        'responses/checkout/fast_checkout_summary.tpl',
        'responses/includes/head.tpl',
        'responses/includes/footer.tpl',
        'responses/includes/page_header.tpl',
        'responses/includes/page_footer.tpl',
        'embed/js_product_sc.tpl',
        'embed/product/product_sc.tpl',
        'embed/account/order_details.tpl',
        'pages/checkout/fast_checkout.tpl',
        'pages/checkout/fast_checkout_success.tpl',
    ],
    'admin'      => [],
];

$languages = [
    'storefront' => [
        'english/fast_checkout/fast_checkout',
    ],
    'admin'      => [
        'english/fast_checkout/fast_checkout',
    ],
];

