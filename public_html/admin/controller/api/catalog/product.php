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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerApiCatalogProduct extends AControllerAPI {
 	protected $error = array();
 
	public function get() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('catalog/product');
		$this->loadModel('catalog/product');

		$request = $this->rest->getRequestParams();
		
		if ( !has_value($request['product_id']) ) {
			$this->rest->setResponseData( array('Error' => 'Product ID is missing') );
			$this->rest->sendResponse(200);
			return;
		}		
		$language_id = 0;
		if( has_value($request['language_id']) ) {
			$language_id = $request['language_id'];
		}
	
		$product_info = $this->model_catalog_product->getProduct($request['product_id']);
		$product_info['product_description'] = $this->model_catalog_product->getProductDescriptions($request['product_id'], $language_id);
		$product_info['product_tags'] = $this->model_catalog_product->getProductTags($request['product_id'], $language_id);
			
		$resource = new AResource('image');
		$images = $resource->getResourceAllObjects('products', $request['product_id']);
		$product_info['images'] = $images;

		$product_info['product_category'] = $this->model_catalog_product->getProductCategories($request['product_id']);		
		$product_info['product_store'] = $this->model_catalog_product->getProductStores($request['product_id']);
		
		if (!count($product_info)) {
			$this->rest->setResponseData( array('Error' => 'Incorrect product ID or missing product data') );
			$this->rest->sendResponse(200);
			return;			
		}
			    
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( $product_info );
		$this->rest->sendResponse( 200 );
	    
	}


  	public function images() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$request = $this->rest->getRequestParams();
		if ( !has_value($request['product_id']) ) {
			$this->rest->setResponseData( array('Error' => 'Product ID is missing') );
			$this->rest->sendResponse(200);
			return;
		}		
		$language_id = 0;
		if( has_value($request['language_id']) ) {
			$language_id = $request['language_id'];
		}
		
		//check mode for create or update
		//for update mode, we need to delete all images first
		if($request['mode'] == 'update') {
			$this->loadModel('catalog/product');
			$this->model_catalog_product->deleteResources( 'products', $request['product_id'], 'image' );
		}
		
		//prepare images array 
		$images = array();
		$count = 0;
		foreach($request['images'] as $img){
			$images[$count]['url'] = $img;
			$images[$count]['name'] = '';			
			if($request['name'] && $request['name'][$count]) {
				$images[$count]['name'] = $request['name'][$count];						
			}
			$images[$count]['title'] = '';			
			if($request['title'] && $request['title'][$count]) {
				$images[$count]['title'] = $request['title'][$count];						
			}
			$count++;
		}
		$this->_create_images_rl($request['product_id'], $language_id, $images);
		if(has_value($this->error)){
		    $this->rest->setResponseData( $this->error );
		} else {
		    $this->rest->setResponseData( array('Success' => 'Images are processed') );		
		    $this->cache->delete('*');
		}
		$this->rest->sendResponse(200);
		return;			
	
        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

/************/	    
  	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
            $product_data = $this->_prepareData($this->request->post);
            $product_id = $this->model_catalog_product->addProduct($product_data);
            $this->model_catalog_product->updateProductLinks($product_id, $product_data);

			//$this->_create_images_rl($product_id, $product_data['images']);

			$this->rest->setResponseData( array('product_id' => $product_id) );
			$this->rest->sendResponse( 200 );
			return;			
		} else {
			if(has_value($this->error)){
				$this->rest->setResponseData( $this->error );
			} else {
				$this->rest->setResponseData( array('Error' => 'Incorrect request type') );		
			}
			$this->rest->sendResponse(200);
			return;			
		}

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}


  	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
            $product_data = $this->_prepareData($this->request->post);
			$this->model_catalog_product->updateProduct($this->request->get['product_id'], $product_data);
            $this->model_catalog_product->updateProductLinks($this->request->get['product_id'], $product_data);

			$this->rest->setResponseData( array('product_id' => $product_data['product_id']) );
			$this->rest->sendResponse( 200 );
			return;			
		} else {
			if(has_value($this->error)){
				$this->rest->setResponseData( $this->error );
			} else {
				$this->rest->setResponseData( array('Error' => 'Incorrect request type') );		
			}
			$this->rest->sendResponse(200);
			return;			
		}

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	private function _validateForm() {
    	if (!$this->user->canModify('catalog/product')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
		$len = mb_strlen($this->request->post['product_description']['name']);
		if ($len<1 || $len>255) {
			$this->error['name'] = $this->language->get('error_name');
		}

    	if ( mb_strlen($this->request->post['model']) > 64 ) {
      		$this->error['model'] = $this->language->get('error_model');
    	}

    	if (($error_text = $this->html->isSEOkeywordExists('product_id='.$this->request->get['product_id'], $this->request->post['keyword']))) {
      		$this->error['keyword'] = $error_text;
    	}
		
    	if (!$this->error) {
			return TRUE;
    	} else {
			if (!isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_required_data');
			}
      		return FALSE;
    	}
  	}

    private function _prepareData($data=array()){
        if(isset($data['date_available'])){
            $data['date_available'] = dateDisplay2ISO($data['date_available']);
        }
        return $data;
    }

	//IMAGE PROCESSING
	private function _create_images_rl($product_id, $language_id, $images = array()) {
		$rm = new AResourceManager();
		$rm->setType('image');

		if(!count($images)) {
			$this->error['image'] = "No images to update";
		}

		foreach($images as $img){
		 	// check if remote image exists
			$src_exists = @getimagesize($img['url']);
			if ($src_exists) {
				$image_basename = basename($img['url']);
				$target = DIR_RESOURCE . 'image/' . $image_basename;
				$file = '';
				if (($file = $this->downloadFile($img['url'])) === false) {
					$this->is_error = true;
					$this->error['image'] = "File  " . $img['url'] . " cannot be uploaded";
					continue;
				} else {
					if (!is_dir(DIR_RESOURCE . 'image/')) {
						mkdir(DIR_RESOURCE . 'image/', 0777);
					}
					if (!$this->writeToFile($file, $target)) {
						$this->error['image'] = "Can not copy " . $img['url'] . " to ". $target . " in resource/image folder";
						continue;
					}
					$resource = array(  'language_id' => $language_id,
										'name' => array(),
										'title' => $img['title'],
										'description' => $img['description'],
										'resource_path' => $image_basename,
										'resource_code' => '' );

					if($img['name']) {
						$resource['name'][$language_id] = $img['name'];
					} else {
						$resource['name'][$language_id] = str_replace('%20',' ',$image_basename);
					}
					$resource_id = $rm->addResource($resource);
					if ($resource_id) {
						$rm->mapResource('products', $product_id, $resource_id);
					} else {
						$this->error['image'] = "Can not create resource for " . $img['url'] . "";
						continue;
					}
				}
			} else {
				$this->error['image'] = "Image  " . $img['url'] . " cannot be accessed";
			} 
		}
	}
	
	private function writeToFile($data, $file) {
		if (is_dir($file)) return null;
		if (function_exists("file_put_contents")) {
			$bytes = @file_put_contents($file, $data->body);
			return $bytes == $data->content_length;
		}

		$handle = @fopen($file, 'w+');
		$bytes = fwrite($handle, $data->body);
		@fclose($handle);

		return $bytes == $data->content_length;
	}

	private function _get_file($uri) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = new stdClass();

		$response->body = curl_exec($ch);
		$response->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response->content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		$response->content_length = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

		curl_close($ch);

		return $response;
	}

	private function downloadFile($path) {
		$file = $this->_get_file($path);
		if ($file->http_code == 200) {
			return $file;
		}
		return false;
	}
	    	    
}