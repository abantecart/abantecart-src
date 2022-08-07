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
require_once DIR_EXT . 'avatax_integration' . DS . 'vendor' . DS . 'autoload.php';
require_once DIR_EXT . 'avatax_integration' . DS . 'core' . DS . 'avatax_integration.php';

$controllers = [
    'storefront' => ['responses/extension/avatax_integration'],
    'admin' => [
        'responses/extension/avatax_integration',
        'pages/catalog/avatax_integration',
        'pages/total/avatax_integration_total',
        'pages/sale/avatax_customer_data',
    ],
];

$models = [
    'storefront' => [
        'extension/avatax_integration',
        'total/avatax_integration_total',
    ],
    'admin' => ['extension/avatax_integration'],
];

$templates = [
    'storefront' => ['pages/account/tax_exempt_edit.tpl'],
    'admin' => [
        'pages/extension/avatax_integration_settings.tpl',
        'pages/extension/avatax_integration_shipping_taxcodes.tpl',
        'pages/avatax_integration/tabs.tpl',
        'pages/avatax_integration/avatax_integration_form.tpl',
        'pages/sale/avatax_customer_form.tpl',
    ],
];

$languages = [
    'storefront' => ['english/avatax_integration/avatax_integration'],
    'admin' => ['english/avatax_integration/avatax_integration'],
];

