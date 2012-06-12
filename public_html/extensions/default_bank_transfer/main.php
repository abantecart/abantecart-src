<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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

$controllers = array(
    'storefront' => array('responses/extension/default_bank_transfer'),
    'admin' => array( 'pages/extension/default_bank_transfer' ),
);

$models = array(
    'storefront' => array( 'extension/default_bank_transfer' ),
    'admin' => array( ),
);

$languages = array(
    'storefront' => array(
	    'default_bank_transfer/default_bank_transfer'),
    'admin' => array(
        'default_cod/default_bank_transfer'));

$templates = array(
    'storefront' => array( 'responses/default_bank_transfer.tpl' ),
    'admin' => array('pages/extension/default_bank_transfer.tpl'));