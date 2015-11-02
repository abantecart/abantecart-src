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
/** @noinspection PhpUndefinedClassInspection */
class ControllerBlocksListingBlock extends AController {
	public $data;
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$instance_id = func_get_arg(0);
		$block_data = $this->_getBlockContent($instance_id);

		$block_details = $this->layout->getBlockDetails($instance_id);
		$parent_block = $this->layout->getBlockDetails($block_details['parent_instance_id']);
		$parent_block_txt_id = $parent_block['block_txt_id'];


		$extension_controllers = $this->extensions->getExtensionControllers();
		$exists = false;
		foreach($extension_controllers as $ext){
			if(in_array($this->data['controller'],$ext['storefront'])){
				$exists = true;
				break;
			}
		}

		if($block_data){
			if(!$exists || !$this->data['controller']){
				//Only products have special listing data preparation
				if(in_array($this->data['listing_datasource'],
							array(  'custom_products',
				                    'catalog_product_getPopularProducts',
				                    'catalog_product_getSpecialProducts',
									'catalog_product_getfeatured',
									'catalog_product_getlatest',
									'catalog_product_getbestsellers'
							)
						)){

					$this->_prepareProducts( $block_data['content'], $block_data['block_wrapper'] );
					$template_overrided = true;
				}else{
					$block_data['content'] = $this->_prepareItems($block_data['content']);
				}

				$this->view->assign('block_framed',(int)$block_data['block_framed']);
				$this->view->assign('content',$block_data['content']);
				$this->view->assign('heading_title', $block_data['title'] );
			}else{
				$override = $this->dispatch($this->data['controller'],array($parent_block_txt_id,$block_data));
				$this->view->setOutput($override->dispatchGetOutput());
			}
			// need to set wrapper for non products listing blocks
			if($this->view->isTemplateExists($block_data['block_wrapper']) && !$template_overrided){
				$this->view->setTemplate( $block_data['block_wrapper'] );
			}

			$this->processTemplate();
		}
		//init controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

	/**
	 * @param array $data
	 * @param string $block_wrapper
	 */
	protected function _prepareProducts(&$data, $block_wrapper=''){
		$this->loadModel('catalog/product');
		$this->loadModel('catalog/review');
		$this->loadLanguage('product/product');

		foreach($data as $result){
			$product_ids[] = (int)$result['product_id'];
		}
		$products_info = $this->model_catalog_product->getProductsAllInfo($product_ids);

		foreach ($data as $result) {
			$rating = $products_info[$result['product_id']]['rating'];


			$options = $products_info[$result['product_id']]['options'];
			if ($options) {
				$add_to_cart = $this->html->getSEOURL('product/product', '&product_id=' . $result['product_id'], '&encode');
			} else {
                if($this->config->get('config_cart_ajax')){
                    $add_to_cart = '#';
                }else{
                    $add_to_cart = $this->html->getSecureURL('checkout/cart', '&product_id=' . $result['product_id'], '&encode');
                }
			}

			if($products_info[$result['product_id']]['special']){
				$special_price = $this->currency->format($this->tax->calculate($products_info[$result['product_id']]['special'], $result['tax_class_id'], $this->config->get('config_tax')));
			}else{
				$special_price = null;
			}

			$products[] = array(
								'product_id'    => $result['product_id'],
								'name'    		=> $result['name'],
								'model'   		=> $result['model'],
								'rating'  		=> $rating,
								'stars'   		=> sprintf($this->language->get('text_stars'), $rating),
								'price'   		=> $result['price'],
								'options'   	=> $result['options'],
								'special' 		=> $special_price,
								'thumb'   		=> $result['image'],
								'image'   		=> $result['image'],
								'href'    		=> $this->html->getSEOURL('product/product', '&product_id=' . $result['product_id'], '&encode'),
								'add'    		=> $add_to_cart,
								'item_name'		=> 'product'
			);
		}
		$data_source= array(
				'rl_object_name'=>'products',
				'data_type'=>'product_id'
		);
		//add thumbnails to list of products. 1 thumbnail per product
		$products = $this->_prepareCustomItems($data_source, $products);
		//need to override reference (see params)
		$data = $products;

		// set sign of displaying prices on storefront
		if ($this->config->get('config_customer_price')) {
			$display_price = TRUE;
		} elseif ($this->customer->isLogged()) {
			$display_price = TRUE;
		} else {
			$display_price = FALSE;
		}
		$this->view->assign('display_price',$display_price);
		$this->view->assign('review_status', $this->config->get('enable_reviews'));

		$this->view->assign('products',$products);
		$vertical_tpl = array( 'blocks/listing_block_column_left.tpl',
		                       'blocks/listing_block_column_right.tpl');

		if($this->view->isTemplateExists($block_wrapper) ){
			$template = $block_wrapper;
		}else{
			$template =  in_array( $this->view->getTemplate(), $vertical_tpl ) ? 'blocks/special.tpl' : 'blocks/special_home.tpl';
		}
		$this->view->setTemplate( $template );
	}

	/**
	 * @param array $content
	 * @return array
	 */
	protected function _prepareItems($content=array()){
		if(isset($content[0]['category_id'])){
				$item_name = 'category';
		}else if(isset($content[0]['manufacturer_id'])){
				$item_name = 'manufacturer';
		}else if(isset($content[0]['product_id'])){
				$item_name = 'product';
		}else if(isset($content[0]['resource_id'])){
				$item_name = 'resource';
		}

		foreach($content as &$cn){
			$cn['item_name'] = $item_name;
			switch($item_name){
				case 'category':
					$cn['href'] = $this->html->getSEOURL('product/category','&category_id='.$cn['category_id'],'&encode');
				break;
				case 'manufacturer':
					$cn['href'] = $this->html->getSEOURL('product/manufacturer','&manufacturer_id='.$cn['manufacturer_id'],'&encode');
				break;
			}
		}
		return $content;
	}

	/**
	 * @param int $instance_id
	 * @return array
	 */
	protected function _getBlockContent($instance_id) {
		$this->data['block_info'] = $this->layout->getBlockDetails($instance_id);
		$this->data['custom_block_id'] = $this->data['block_info']['custom_block_id'];

		//getting block properties
		$this->data['descriptions'] = $this->layout->getBlockDescriptions($this->data['custom_block_id']);

		if($this->data['descriptions'][$this->config->get('storefront_language_id')]){
			$key = $this->config->get('storefront_language_id');
		}else{
			$key = key($this->data['descriptions']);
		}

		// getting list
		$this->data['content'] = $this->getListing();
		if($this->data['content']){
			$output = array(
				'title' => $this->data['descriptions'][$key]['title'],
				'block_framed' => $this->data['descriptions'][$key]['block_framed'],
				'content' => $this->data['content'],
				'block_wrapper' => $this->data['descriptions'][$key]['block_wrapper'],
			);
		}

		return $output;
	}


	public function getListing(){
		if(!$this->data['custom_block_id'] || !$this->data['descriptions']){
			return false;
		}

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$listing = new AListing($this->data['custom_block_id']);
		$content = unserialize($this->data['descriptions'][$this->config->get('storefront_language_id')]['content']);
        if(!$content && $this->data['descriptions']){
            $content = current($this->data['descriptions']);
            $content = unserialize($content['content']);
        }
		$this->data['controller'] = $content[ 'block_appearance' ];
		$this->data['listing_datasource'] = $content['listing_datasource'];

		$data_sources = $listing->getListingDataSources();
		$data_source = $data_sources[$content['listing_datasource']];

		if(strpos($content['listing_datasource'],'custom_')===FALSE){ // for auto listings
			$route = $content['listing_datasource'];
			$limit = $content['limit'];

			// for resource library
			if($route=='media'){
				$rl = new AResource($content['resource_type']);

				if(isset($this->request->get['product_id'])){
					$object_name = 'products';
					$object_id = $this->request->get['product_id'];
				}elseif( isset($this->request->get['category_id']) || isset($this->request->get['path'])){
					$object_name = 'categories';
					if(isset($this->request->get['category_id'])){
						$object_id = $this->request->get['product_id'];
					}else{
						$temp = explode("_",$this->request->get['path']);
						end($temp);
						$object_id = current($temp);
					}

				}elseif( isset($this->request->get['manufacturer_id']) ){
					$object_name = 'manufacturers';
					$object_id = $this->request->get['manufacturer_id'];
				}

				$resources = $rl->getResourceAllObjects( $object_name,
														 $object_id,
														 array('main'=>array(
					                                                         'width'=>$this->config->get('config_image_popup_width'),
					                                                         'height'=>$this->config->get('config_image_popup_height')),

				                                               'thumb'=>array(
					                                                          'width'=>$this->config->get('config_image_product_width'),
					                                                          'height'=>$this->config->get('config_image_product_height'))),
														$limit,
														false);
				if(!$resources){ return null;}
				if($limit==1){
					$resources = array($resources);
				}

				foreach( $resources as $k=>$resource ){
					if($resource['origin']=='external'){
						$result[$k]['resource_code'] = $resource['thumb_html'];
					}else{
						if($content['resource_type']!='image'){
							$title = $resource['title'] .'<br>dowload';
						}else{
							$title = $resource['title'];
						}

						$result[$k]['thumb'] = array('main_url'=>$resource['main_url'],
													 'main_html'=>$resource['main_html'],
						                             'thumb_url'=>$resource['thumb_url'],
						                             'thumb_html'=>$resource['thumb_html'],
						                             'title' => $title,
													 'resource_type' => $content['resource_type'],
													 'origin' => 'internal');
					}
				}


			}else{
				// otherwise -  select list from method
				if($route){
					$this->loadModel($data_source['storefront_model']);
                  	$result = call_user_func_array(array( $this->{'model_'.str_replace('/','_',$data_source['storefront_model'])},
												       $data_source['storefront_method']),
                                                   $listing->getlistingArguments( $data_source['storefront_model'],
                                                                                  $data_source['storefront_method'],
                                                                                  array('limit'=>$limit)) );
					if($result){
						$desc = $listing->getListingDataSources();
						foreach($desc as $d){
							if($d['storefront_method']==$data_source['storefront_method']){
								$data_source=$d;
								break;
							}
						}
						//add thumbnails to custom list of items. 1 thumbnail per item
						$result = $this->_prepareCustomItems($data_source, $result);
					}

				}

			}
		}else{ // for custom listings

			$list = $listing->getCustomList();
			if(!$list){ return null; }

			$this->load->model($data_source['storefront_model']);

			foreach($list as $item){
				$result[] = call_user_func_array(array( $this->{'model_'.str_replace('/','_',$data_source['storefront_model'])},
												   $data_source['storefront_method']),
												   array($item['id']));
			}

			// Skip if data source is vanished but still set in the listing. 
			$result = array_filter($result);
		}

		if($result){
			//add thumbnails to custom list of items. 1 thumbnail per item
			$result = $this->_prepareCustomItems($data_source, $result);
		}
		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		return $result;
	}

	/**
	 * @param array $data_source
	 * @param array $result
	 * @return array
	 */
	private function _prepareCustomItems($data_source, $result){
		if(!$data_source['rl_object_name'] ){ return $result; }
		$resource = new AResource('image');
		if($result){
			foreach($result as $k=>$item){
				if($data_source['rl_object_name'] ){

					$thumbnail = $resource->getMainThumb($data_source['rl_object_name'],
			                                     $item[$data_source['data_type']],
			                                     $this->config->get('config_image_product_width'),
			                                     $this->config->get('config_image_product_height'),true);

					$result[$k]['image'] = $result[$k]['thumb'] = $thumbnail;

				}
				if(isset($item['price']) && preg_match('/^[0-9\.]/',$item['price'])){
					$result[$k]['price'] = $this->currency->format($this->tax->calculate($item['price'], $item['tax_class_id'], $this->config->get('config_tax')));
				}
			}
		}
	return $result;
	}
}
