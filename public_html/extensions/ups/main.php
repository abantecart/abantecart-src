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

require_once (DIR_EXT.'ups'.DS.'core'.DS.'ups-api'.DS.'vendor'.DS.'autoload.php');
require_once (DIR_EXT.'ups'.DS.'core'.DS.'ups_hooks.php');
require_once (DIR_EXT.'ups'.DS.'core'.DS.'utils.php');

$controllers = [
    'storefront' => [],
    'admin' => [
        'responses/extension/ups'
    ]
];

$models = [
    'storefront' => [
        'extension/ups'
    ],
    'admin' => []
];

$templates = [
    'storefront' => [],
    'admin' => [
        'responses/extension/ups_test.tpl'
    ]
];

$languages = [
    'storefront' => [
        'ups/ups'
    ],
    'admin' => [
        'ups/ups'
    ]
];
