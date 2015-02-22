<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
        header ( 'Location: static_pages/' );
}
include_once('core/custom_block_hook.php');
$controllers = array(
    'storefront' => array(
	                'responses/extension/banner_manager',
	                'blocks/banner_block'
    ),
    'admin' => array( 'pages/extension/banner_manager',
                      'responses/listing_grid/banner_manager',
					  'pages/extension/banner_manager_stat',
                      'responses/listing_grid/banner_manager_stat',
                      'responses/extension/banner_manager_chart',
    )
);

$models = array(
    'storefront' => array( 'extension/banner_manager' ),
    'admin' => array( 'extension/banner_manager' ),
);

$languages = array(
    'storefront' => array(
	    'banner_manager/banner_manager'),
    'admin' => array(
        'banner_manager/banner_manager'));

$templates = array(
    'storefront' => array(
	                        'blocks/banner_block.tpl',
	                        'blocks/banner_block_content.tpl',
	                        'blocks/banner_block_header.tpl',
	                        'blocks/banner_block/one_by_one_slider_banner_block.tpl',
	                        'blocks/banner_block/flex_slider_banner_block.tpl'
	                        
    ),
    'admin' => array( 'pages/extension/banner_manager.tpl',
                      'pages/extension/banner_manager_form.tpl',
                      'responses/extension/banner_listing.tpl',
                      'pages/extension/banner_manager_stat.tpl',
                      'pages/extension/banner_manager_stat_details.tpl',
                      'pages/extension/banner_manager_block_form.tpl',
    ));