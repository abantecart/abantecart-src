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
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

if(!class_exists('ExtensionDefaultPpPro')){
	include_once('core/default_pp_pro.php');
}

$controllers = array(
    'storefront' => array('responses/extension/default_pp_pro'),
    'admin' => array('responses/extension/default_pp_pro'),
);

$models = array(
    'storefront' => array( 'extension/default_pp_pro' ),
    'admin' => array( 'extension/default_pp_pro' ),
);

$languages = array(
    'storefront' => array(
	    'default_pp_pro/default_pp_pro'),
    'admin' => array(
        'default_pp_pro/default_pp_pro'));

$templates = array(
    'storefront' => array(
	    'responses/default_pp_pro.tpl' ),
    'admin' => array(
		'pages/extension/default_pp_pro_settings.tpl',
		'pages/sale/pp_pro_payment_details.tpl'
	)
);