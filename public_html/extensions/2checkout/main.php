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
require_once(__DIR__.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'hooks.php');
$controllers = array(
    'storefront' => array('responses/extension/2checkout'),
    'admin'      => array(),
);

$models = array(
    'storefront' => array('extension/2checkout'),
    'admin'      => array(),
);

$languages = array(
    'storefront' => array(
        '2checkout/2checkout',
    ),
    'admin'      => array(
        '2checkout/2checkout',
    ),
);

$templates = array(
    'storefront' => array(
        'responses/2checkout.tpl',
        'responses/pending_ipn.tpl'
    ),
    'admin'      => array(),
);