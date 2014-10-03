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
	protected $data = array();
	protected $mp_url = 'bWFya2V0cGxhY2UuYWJhbnRlY2FydC5jb20v';
	public function getMPURL(){
		return (HTTPS===true ? 'https://' : 'http://') . base64_decode($this->mp_url);
	}

	public function authorize(){

		$auth_params =  array( 'rt' => 'a/account/authorize/post',
							   'store_id' => UNIQUE_ID,
							   'store_ip' => $_SERVER ['SERVER_ADDR'],
							   'store_url' => HTTP_SERVER,
							   'store_version' => VERSION,
							   'installer_url' => $this->html->getSecureURL('tool/package_installer/download')
							 );
		$extensions_list = $this->extensions->getExtensionsList();
		if ($extensions_list) {
			foreach ( $extensions_list->rows as $ext ){
				$auth_params["extensions[" . $ext['key'] . "]"] = $ext['version'];
			}
		}

		$connect = new AConnect();
		$connect->connect_method = 'curl'; // set curl as default connection type
		$auth = $this->send( $connect, $auth_params );

		//TODO: need to add validation for authorized stores count later

		if($auth['mp_token']){
			$this->session->data['mp_token'] = $auth['mp_token'];
			$this->session->data['mp_hash'] = $auth['mp_hash'];
		}
	}

	public function processRequest($params=array()){
		$output = array(
					'categories'=>array(),
					'products'=>array()
		);
		$connect = new AConnect();
		$connect->connect_method = 'curl'; // set curl as default connection type

		if(!has_value($this->session->data['mp_token'])){
			$this->authorize();
		}

		// prepare parameters
		if(has_value($params['limit'])){
			$get_params['limit'] = $get_params['rows'] = $params['limit'];
		}else{
			$get_params['limit'] = $get_params['rows'] = 24;
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
		$output['categories'] = $this->send($connect,
											array( 'rt' => 'a/product/category',
												   'category_id' => 0,
												   'mp_token' => $this->session->data['mp_token']
		));


		if( $output['categories'] ){
			foreach($output['categories']['subcategories'] as &$category){
				$category['href'] = $this->html->getSecureURL('extension/extensions_store',
															  '&category_id='.$category['category_id'].'&sidx='.$get_params['sidx'].'&sord='.$get_params['sord'].'&limit='.$get_params['limit']);
				$category['active'] = $category['category_id']==$params['category_id'] ? true : false;
			} unset($category);
			//add all categories option at the beginning of array
			array_unshift($output['categories']['subcategories'], array(
				'category_id' => '',
				'name' => $this->language->get('text_all_categories'),
				'href' => $this->html->getSecureURL('extension/extensions_store',
									'&sidx='.$get_params['sidx'].'&sord='.$get_params['sord'].'&limit='.$get_params['limit']),
				'active' => $params['category_id'] ? false : true
			));				
		}
		// get products of category
		if(has_value($params['category_id'])){
			$get_params['rt'] = 'a/product/filter';
			$get_params['category_id'] = (int)$params['category_id'];
			$output['products'] = $this->send( $connect, $get_params);
		}elseif(has_value($params['keyword'])){//get products by keyword
			$get_params['rt'] = 'a/product/filter';
			$get_params['keyword'] = $params['keyword'];
			$output['products'] = $this->send( $connect, $get_params );
		}else{
			$get_params['rt'] = 'a/product/latest';
			$output['products'] = $this->send( $connect, $get_params );
		}

		//prepare products
		if($output['products']){
			foreach($output['products']['rows'] as &$product){
				$info = $product['cell'];
				$info['rating'] = (int)$info['rating'];
				$info['description'] = substr(strip_tags(html_entity_decode(str_replace('&nbsp;','',$info['description']),ENT_QUOTES)),0,344).'...';

				$info['price'] = $info['price']>0 ? $this->currency->format($info['price'],$info['currency_code']) : $this->language->get('text_free');

				$info['addtocart'] = $this->html->buildElement(array(
																	'type' => 'button',
																	'text' =>$info['price'],
																	'style' => 'button3',
																	'href' => '#'));
				$product['cell'] = $info;
			}
		}

		if(!$output['categories'] && !$output['products']  ){
			$output = array();
		}
		return $output;
	}

	/**
	 * @param AConnect $connect
	 * @param array $params - plain associative array
	 * @return mixed
	 */
	private function send( $connect, $params=array()){
		if(!is_object($connect)){
			return false;
		}

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

		// add session id as cookie for autostart remote session on MP-side (non-token)
		if($this->session->data['mp_token']){ // if server-server connect was created
			$connect->setCurlOptions(array(
					CURLOPT_CONNECTTIMEOUT => 2,
					CURLOPT_HTTPHEADER => array('Expect:'),
					CURLOPT_MAXREDIRS => 4,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_COOKIE => 'PHPSESSID_AC_SF='.$this->session->data['mp_token'] ));
		}

		$response = $connect->getResponse($this->getMPURL().$href);

		return $response;
	}

	public function getExtensions($params=array()){
		$params = array($params);
		$connect = new AConnect(true);
		$connect->connect_method = 'curl'; // set curl as default connection type

		if(!has_value($this->session->data['mp_token'])){ // do auto-authorize...
			$this->authorize();
		}

		if(has_value($this->session->data['mp_token'])){

			$auth_params =  array( 'rt' => 'a/account/account/get',
								   'store_id' => UNIQUE_ID,
								   'store_ip' => $_SERVER ['SERVER_ADDR'],
								   'store_url' => HTTP_SERVER,
								   'store_version' => VERSION
								 );
			$auth_params = array_merge($auth_params, $params);

			$response = $this->send( $connect, $auth_params	);

			return $response;
		}
	}
}
