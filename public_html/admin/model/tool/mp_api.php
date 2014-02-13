<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ModelToolMPAPI  - class for converting parameters from url to api-request for marketplace
 *
 */

class ModelToolMPAPI extends Model {

	public function processRequest($mpurl, $params=array()){
		$output = array(
					'categories'=>array(),
					'products'=>array()
		);
		$connect = new AConnect();


		// prepare parameters
		if(has_value($params['limit'])){
			$get_params['limit'] = $params['limit'];
		}else{
			$get_params['limit'] = 20;
		}
		if(has_value($params['page'])){
			$get_params['page'] = $params['page'];
		}
		if(has_value($params['sidx'])){
			$get_params['sidx'] = $params['sidx'];
		}else{
			$get_params['sidx'] = 'rating';
		}
		if(has_value($params['sord'])){
			$get_params['sord'] = $params['sord'];
		}else{
			$get_params['sord'] = 'DESC';
		}
		// get category list
		$output['categories'] = $this->send($mpurl,
											$connect,
											array( 'rt' => 'a/product/category',
													 'category_id' => 0
		));

		foreach($output['categories']['subcategories'] as &$category){
			$category['href'] = $this->html->getSecureURL('extension/extensions_store',
														  '&category_id='.$category['category_id'].'&sidx='.$get_params['sidx'].'&sord='.$get_params['sord'].'&limit='.$get_params['limit']);
			$category['active'] = $category['category_id']==$params['category_id'] ? true : false;
		} unset($category);

		// get products of category
		if(has_value($params['category_id'])){
			$get_params['rt'] = 'a/product/filter';
			$get_params['category_id'] = (int)$params['category_id'];
			$output['products'] = $this->send( $mpurl, $connect, $get_params);
		}elseif(has_value($params['keyword'])){//get products by keyword
			$get_params['rt'] = 'a/product/filter';
			$get_params['keyword'] = $params['keyword'];
			$output['products'] = $this->send( $mpurl, $connect, $get_params );
		}else{
			$get_params['rt'] = 'a/product/latest';
			$output['products'] = $this->send( $mpurl, $connect, $get_params );
		}


		//prepare products
		foreach($output['products']['rows'] as &$product){
			$info = $product['cell'];
			$info['rating'] = (int)$info['rating'];
			$info['description'] = substr(strip_tags(html_entity_decode($info['description'],ENT_QUOTES)),0,344).'...';
			$info['price'] = $this->currency->format($info['price'],$info['currency_code']);
			$info['addtocart'] = $this->html->buildElement(array(
																'type' => 'button',
																'text' =>$info['price'],
																'style' => 'button3',
																'href' => '#'));
			$product['cell'] = $info;
		}
		return $output;
	}

	/**
	 * @param string $url
	 * @param AConnect $connect
	 * @param array $params
	 * @return mixed
	 */
	private function send($url, $connect, $params=array()){
		if(!$url){
			return false;
		}
		$GET['api_key'] = 'abolabo';
		$GET['store_id'] = UNIQUE_ID;
		$GET['store_ip'] = $_SERVER ['SERVER_ADDR'];
		$GET['store_url'] = HTTP_SERVER;
		$GET['store_version'] = VERSION;
		$GET['language_code'] = $this->request->cookie ['language'];

		// place your affiliate id here
		define('MP_AFFILIATE_ID','');
		if(MP_AFFILIATE_ID){
			$GET['aff_id'] = MP_AFFILIATE_ID;
		}

		$GET = array_merge($params,$GET);

		$href .= '?'.http_build_query($GET);

		$response = $connect->getResponse($url.$href);
		return $response;
	}
}
