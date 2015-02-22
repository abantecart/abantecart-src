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

if(!class_exists('ExtensionDefaultPPExpress')){
	include_once('core/default_pp_express.php');
}

$controllers = array(
    'storefront' => array(
		'pages/extension/default_pp_express',
		'responses/extension/default_pp_express',
		'blocks/default_pp_express_button',
		'blocks/default_pp_express_banner',
	),
    'admin' => array(
		'responses/extension/default_pp_express',
	),
);

$models = array(
    'storefront' => array(
		'extension/default_pp_express',
	),
    'admin' => array(
		'extension/default_pp_express',
	),
);

$languages = array(
    'storefront' => array('default_pp_express/default_pp_express'),
    'admin' => array('default_pp_express/default_pp_express')
);

$templates = array(
    'storefront' => array(
		'blocks/default_pp_express_button.tpl',
		'blocks/default_pp_express_cart_button.tpl',
	    'responses/default_pp_express.tpl',
		'responses/default_pp_express_error.tpl',
		'blocks/default_pp_express_banner_left.tpl',
		'blocks/default_pp_express_banner_right.tpl',
		'blocks/default_pp_express_banner_footer_top.tpl',
		'blocks/default_pp_express_banner_footer.tpl',
		'blocks/default_pp_express_banner_header_bottom.tpl',
	),
    'admin' => array(
		'pages/sale/pp_express_payment_details.tpl',
		'pages/extension/default_pp_express_settings.tpl',
	)
);