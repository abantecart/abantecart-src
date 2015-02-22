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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerApiProductCategory extends AControllerAPI {
	
	public function get() {
        $this->extensions->hk_InitData($this,__FUNCTION__);
			
		if(!isset($this->request->get['path']) && isset($this->request->get['category_id']) ){
			$this->request->get['path'] = $this->request->get['category_id'];
		}  

		if ( !isset($this->request->get['path']) && !isset($this->request->get['category_id']) ) {
			$this->rest->setResponseData( array('Error' => 'Missing one of required category parameters') );
			$this->rest->sendResponse(200);
			return null;
		}
		
		if (isset($this->request->get['path']) && $this->request->get['path'] != 0) {		
			$parts = explode('_', $this->request->get['path']);		
			$category_id = array_pop($parts);
			$category_info = $this->getCategoryDetails($category_id);
		} else {
			$category_info['category_id'] = 0;
			$category_info['subcategories'] = $this->getCategories( );
		}

        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( $category_info );
		$this->rest->sendResponse( 200 );
	}


	public function getCategoryDetails( $category_id ) {
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadModel('catalog/category');
		$this->loadModel('catalog/product');
		$this->loadModel('tool/image'); 

		$category_info = $this->model_catalog_category->getCategory($category_id);
		if (!$category_info) {
			return array( 'message' => 'category not found' );
		}
		$resource = new AResource('image');
		$thumbnail = $resource->getMainThumb('categories',
			                                     $category_id,
			                                     $this->config->get('config_image_category_width'),
			                                     $this->config->get('config_image_category_height'),true);
		$category_info['tumbnail'] = $thumbnail['thumb_url'];

		//Process data for category 
		$category_info['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8'); 
		$category_info['total_products'] = $this->model_catalog_product->getTotalProductsByCategoryId($category_id);
		$category_info['total_subcategories'] = $this->model_catalog_category->getTotalCategoriesByCategoryId($category_id);
		if ($category_info['total_products']) {
        	$category_info['subcategories'] = $this->getCategories( $category_id );
		
		} 
        $this->extensions->hk_UpdateData($this,__FUNCTION__);		
		return $category_info;

	}
		
	public function getCategories( $parent_categ_id = 0 ) {

        $this->extensions->hk_InitData($this,__FUNCTION__);
		$this->loadModel('catalog/category');
		$resource = new AResource('image');	
		$results = $this->model_catalog_category->getCategories($parent_categ_id);
		foreach ($results as $result) {
				$thumbnail = $resource->getMainThumb('categories',
			                                     $result['category_id'],
			                                     $this->config->get('config_image_category_width'),
			                                     $this->config->get('config_image_category_height'),true);
				
				$categories[] = array(
            			'name'  => $result['name'],
            			'category_id'	=> $result['category_id'],
            			'sort_order'	=> $result['sort_order'],
            			'thumb' => $thumbnail['thumb_url']);
		}
	
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
        return $categories;
	}		
}