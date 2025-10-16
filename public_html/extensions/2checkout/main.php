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
require_once(__DIR__.DS.'core'.DS.'hooks.php');
$controllers = [
    'storefront' => ['responses/extension/2checkout'],
    'admin'      => [],
];

$models = [
    'storefront' => ['extension/2checkout'],
    'admin'      => [],
];

$languages = [
    'storefront' => [
        '2checkout/2checkout',
    ],
    'admin'      => [
        '2checkout/2checkout',
    ],
];

$templates = [
    'storefront' => [
        'responses/2checkout.tpl',
        'responses/pending_ipn.tpl'
    ],
    'admin'      => [],
];