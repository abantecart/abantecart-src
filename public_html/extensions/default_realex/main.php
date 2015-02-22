<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/

if(!class_exists('ExtensionDefaultRealex')){
	include('core/default_realex.php');
}

$controllers = array(
    'storefront' => array('responses/extension/default_realex'),
    'admin' => array('responses/extension/default_realex'),
);

$models = array(
    'storefront' => array( 'extension/default_realex' ),
    'admin' => array( 'extension/default_realex' ),
);

$languages = array(
    'storefront' => array(
	    'default_realex/default_realex'),
    'admin' => array(
        'default_realex/default_realex')
);

$templates = array(
    'storefront' => array(
	'responses/default_realex.tpl' ),
    'admin' => array()
);