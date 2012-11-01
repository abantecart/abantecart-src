<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  &lt;http://www.opensource.org/licenses/OSL-3.0&gt;

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/

if (! defined ( 'DIR_CORE' )) {
header ( 'Location: static_pages/' );
}

$controllers = array(
    'storefront' => array(
        'responses/extension/google_checkout'),
    'admin' => array());

$models = array(
    'storefront' => array(
        'extension/google_checkout'),
    'admin' => array());

$templates = array(
    'storefront' => array(
        'responses/google_checkout/google_checkout.tpl'),
    'admin' => array());

$languages = array(
    'storefront' => array(
        'google_checkout/google_checkout'),
    'admin' => array(
        'google_checkout/google_checkout'));

