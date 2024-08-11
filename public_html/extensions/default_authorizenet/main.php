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

require_once DIR_EXT . 'default_authorizenet' . DS .'core'.DS.'hooks.php';
require_once DIR_EXT . 'default_authorizenet' . DS . 'vendor' . DS . '/autoload.php';

$controllers = [
    'storefront' => [
        'responses/extension/default_authorizenet'
    ],
    'admin' => [],
];

$models = [
    'storefront' => [
        'extension/default_authorizenet'
    ],
    'admin'      => [
        'extension/default_authorizenet'
    ],
];

$languages = [
    'storefront' => [
        'default_authorizenet/default_authorizenet',
    ],
    'admin'      => [
        'default_authorizenet/default_authorizenet',
    ],
];

$templates = [
    'storefront' => [
        'responses/default_authorizenet.tpl',
    ],
    'admin'      => [],
];