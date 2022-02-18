<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Licence details is bundled with this package in the file LICENSE.txt.
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
if (!class_exists('ExtensionDefaultLocalDelivery')) {
    include_once('core/default_local_delivery_hooks.php');
}

$controllers = array(
    'storefront' => array(),
    'admin'      => array(),
);

$models = array(
    'storefront' => array('extension/default_local_delivery'),
    'admin'      => array(),
);

$languages = array(
    'storefront' => array(
        'default_local_delivery/default_local_delivery',
    ),
    'admin'      => array(
        'default_local_delivery/default_local_delivery',
    ),
);

$templates = array(
    'storefront' => array(
        'pages/checkout/default_local_delivery_fields.tpl',
        'pages/checkout/fast_checkout_fields.tpl'
    ),
    'admin'      => array(),
);