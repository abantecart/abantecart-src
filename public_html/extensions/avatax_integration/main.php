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
if (!class_exists('ExtensionAvataxIntegration')) {
    include('core/avatax_integration.php');
}

$controllers = array(
    'storefront' => array('responses/extension/avatax_integration'),
    'admin'      => array(
        'responses/extension/avatax_integration',
        'pages/catalog/avatax_integration',
        'pages/total/avatax_integration_total',
        'pages/sale/avatax_customer_data',
    ),
);

$models = array(
    'storefront' => array(
        'extension/avatax_integration',
        'total/avatax_integration_total',
    ),
    'admin'      => array('extension/avatax_integration'),
);

$templates = array(
    'storefront' => array('pages/account/tax_exempt_edit.tpl'),
    'admin'      => array(
        'pages/extension/avatax_integration_settings.tpl',
        'pages/extension/avatax_integration_shipping_taxcodes.tpl',
        'pages/avatax_integration/tabs.tpl',
        'pages/avatax_integration/avatax_integration_form.tpl',
        'pages/sale/avatax_customer_form.tpl',
    ),
);

$languages = array(
    'storefront' => array('english/avatax_integration/avatax_integration'),
    'admin'      => array('english/avatax_integration/avatax_integration'),
);

