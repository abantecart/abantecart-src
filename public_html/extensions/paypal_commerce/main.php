<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

if (!class_exists('ExtensionPaypalCommerce')) {
    require_once(__DIR__ . DS . 'vendor' . DS . 'autoload.php');
    require_once(__DIR__ . DS . 'core' . DS . 'debugPayPalHttpClient.php');
    require_once(__DIR__ . DS . 'core' . DS . 'paypal_commerce_hooks.php');
    require_once(__DIR__ . DS . 'core' . DS . 'paypal_commerce_modules.php');
}

if (!defined('PAYPAL_SUPPORTED_CURRENCIES')) {
    define(
        'PAYPAL_SUPPORTED_CURRENCIES',
        [
            'AUD', 'BRL', 'CAD', 'CNY', 'CZK', 'DKK', 'EUR',
            'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'TWD',
            'NZD', 'NOK', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD',
            'SEK', 'CHF', 'THB', 'USD'
        ]
    );
}

$controllers = [
    'storefront' => [
        'responses/extension/paypal_commerce'
    ],
    'admin'      => [
        'responses/extension/paypal_commerce'
    ],
];

$models = [
    'storefront' => [
        'extension/paypal_commerce',
    ],
    'admin'      => [
        'extension/paypal_commerce'
    ],
];

$languages = [
    'storefront' => [
        'paypal_commerce/paypal_commerce',
    ],
    'admin'      => [
        'paypal_commerce/paypal_commerce',
    ],
];

$templates = [
    'storefront' => [
        'responses/paypal_commerce_confirm.tpl',
        'responses/paypal_commerce_buy_now.tpl'
    ],
    'admin'      => [
        'pages/sale/paypal_commerce_payment_details.tpl',
        'responses/extension/paypal_commerce_connect.tpl',
        'responses/extension/paypal_commerce_test.tpl',
        'responses/extension/paypal_commerce_manual_connect.tpl',
        'responses/extension/paypal_commerce_note_wrapper.tpl',
        'responses/extension/paylater_configurator.tpl'
    ]
];
