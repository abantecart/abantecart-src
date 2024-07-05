<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

if (!class_exists('ComposerAutoloaderInit201ab571ece14ed3032f19483f079ba7')) {
    require_once(DIR_EXT.'stripe'.DS.'vendor'.DS.'autoload.php');
}

require_once(DIR_EXT . 'stripe'.DS.'core'.DS.'stripe_modules.php');
require_once(DIR_EXT.'stripe'.DS.'core'.DS.'stripe_hooks.php');

$controllers = [
    'storefront' => ['responses/extension/stripe'],
    'admin'      => [
        'responses/extension/stripe',
        'pages/extension/stripe_settings',
    ],
];

$models = [
    'storefront' => ['extension/stripe'],
    'admin'      => ['extension/stripe'],
];

$languages = [
    'storefront' => [
        'stripe/stripe',
    ],
    'admin'      => [
        'stripe/stripe',
    ],
];

$templates = [
    'storefront' => [
        'responses/stripe.tpl',
    ],
    'admin'      => [
        'pages/extension/stripe_settings.tpl',
        'pages/sale/stripe_payment_details.tpl',
    ],
];