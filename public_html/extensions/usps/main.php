<?php
/**
 * $Id$
 *
 * AbanteCart, Ideal OpenSource Ecommerce Solution
 * http://www.AbanteCart.com
 *
 * Copyright © 2011-2026 Belavier Commerce LLC
 *
 * This source file is subject to Open Software License (OSL 3.0)
 * License details are bundled with this package in the file LICENSE.txt.
 * It is also available at this URL:
 * <http://www.opensource.org/licenses/OSL-3.0>
 *
 * UPGRADE NOTE:
 *   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *   versions in the future. If you wish to customize AbanteCart for your
 *   needs, please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

$uspsSdkAutoload = DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_sdk' . DS . 'vendor' . DS . 'autoload.php';
if (is_file($uspsSdkAutoload)) {
    require_once($uspsSdkAutoload);
}
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_hooks.php');

const USPS_CLASSES = [
    'domestic' => [
        1    => 'Priority Mail',
        2    => 'Priority Mail Express',
        3    => 'Parcel Select',
        4    => 'Bound Printed Matter',
        5    => 'Media Mail',
        6    => 'Library Mail',
        7    => 'USPS Ground Advantage',
    ],
    'international' => [
        1  => 'Priority Mail Express International',
        2  => 'Priority Mail International',
        3  => 'Global Express Guaranteed (GXG)',
        4  => 'First-Class Package International Service',
    ]
];

$controllers = [
    'storefront' => [],
    'admin'      => [
        'pages/extension/usps',
        'responses/extension/usps',
        'responses/extension/usps_save',
    ]
];

$models = [
    'storefront' => ['extension/usps'],
    'admin'      => [],
];

$languages = [
    'storefront' => [
        'usps/usps',
    ],
    'admin'      => [
        'usps/usps',
    ],
];

$templates = [
    'storefront' => [],
    'admin'      => [
        'pages/extension/usps.tpl',
        'responses/extension/usps_test.tpl',
    ]
];
