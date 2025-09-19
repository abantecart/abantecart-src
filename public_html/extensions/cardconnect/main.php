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

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

include_once(DIR_EXT . 'cardconnect' . DS . 'core' . DS . 'cardconnect_hooks.php');

$controllers = [
    'storefront' => [
        'responses/extension/cardconnect',
    ],
    'admin'      => [
        'pages/extension/cardconnect_settings',
        'responses/extension/cardconnect',
    ],
];

$models = [
    'storefront' => [
        'extension/cardconnect',
    ],
    'admin'      => ['extension/cardconnect'],
];

$templates = [
    'storefront' => ['extension/cardconnect_buttons.tpl'],
    'admin'      => [
        'pages/extension/cardconnect.tpl',
        'pages/sale/cardconnect_payment_details.tpl',
    ],
];

$languages = [
    'storefront' => [
        'english/cardconnect/cardconnect',
    ],
    'admin'      => [
        'english/cardconnect/cardconnect',
    ],
];

if (!function_exists('getCardConnectEndPoint')) {
    function getCardConnectEndPoint($domainOnly = false)
    {
        $testMode = Registry::getInstance()->get('config')->get('cardconnect_test_mode');
        $output = 'https://' . ($testMode ? 'isv-uat.cardconnect.com/cardconnect/rest' : 'developer.cardconnect.com/cardconnect-api');
        if ($domainOnly) {
            $output = parse_url($output, PHP_URL_HOST);
        }
        return $output;
    }
}