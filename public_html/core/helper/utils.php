<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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

function isFunctionAvailable( $func_name ){
	return function_exists($func_name);
}
/*
 * prepare prices and other floats for database writing,, based on locale settings of number formatting
 * */
function preformatFloat($value, $decimal_point='.'){
	if($decimal_point!='.'){
		$value = str_replace('.','~',$value);
		$value = str_replace($decimal_point,'.',$value);
	}
	return (float)preg_replace('/[^0-9\.]/','',$value);
}

/*
 * prepare integer for database writing
 * */
function preformatInteger($value){
	return (int)preg_replace('/[^0-9]/','',$value);
}

/*
 * prepare string for text id 
 * */
function preformatTextID($value){
	return strtolower( preg_replace("/[^A-Za-z0-9_]/", "", $value) );
}

/*
 * check that argument variable has value (even 0 is a value)  
 * */
function has_value( $value ){
	if ( !is_array($value) && $value !== '' && !is_null($value) ) {
		return true;	
	}
	else if (is_array($value) && count($value) > 0) {
		return true;		
	} else {
		return false;
	}
}

/*
*  Convert input text to alpaha numeric string for SEO URL use
*/
function SEOEncode( $string_value ){
	$seo_key = html_entity_decode($string_value, ENT_QUOTES,'UTF-8');
	$seo_key = preg_replace( '/[^\w\d\s_-]/si', '', $seo_key );
	$seo_key = trim( mb_strtolower( $seo_key ) );
	$seo_key = htmlentities( preg_replace( '/\s+/', '_', $seo_key ) );
	return $seo_key;
}

