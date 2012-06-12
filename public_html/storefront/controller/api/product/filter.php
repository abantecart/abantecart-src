<?php  
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright 2011 Belavier Commerce LLC

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
class ControllerApiProductFilter extends AControllerAPI {
	
	public function get() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
        
	    $this->loadModel('catalog/product');

	    $filter_params = array('category_id', 'manufacturer_id', 'keyword', 'match', 'pfrom', 'pto');
 		$grid_filter_params = array( 'name', 'description', 'model', 'sku' );
		$filter_data = array(
			'method' => 'get',
			'filter_params' => $filter_params, 
			'grid_filter_params' => $grid_filter_params
		);

		$filter = new AFilter( $filter_data );
		
		if ( !$filter->getFilterParam('category_id') && !$filter->getFilterParam('manufacturer_id') && !$filter->getFilterParam('keyword')) {
			$this->rest->setResponseData( array('Error' => 'Missing one of required product filter parameters') );
			$this->rest->sendResponse(200);
			return;
		}		
		
	    $total = $this->model_catalog_product->getTotalProducts( $filter->getFilterData() );

	    if ($total > 0) {
	    	$total_pages = ceil($total / $filter->getParam('rows'));
	    } else {
	    	$total_pages = 0;
	    }

		//Preserved jqGrid JSON interface 
	    $response = new stdClass();
	    $response->page = $filter->getParam('page');
	    $response->total = $total_pages;
	    $response->records = $total;

	    $resource = new AResource('image');
	    $results = $this->model_catalog_product->getProducts( $filter->getFilterData() );
	    $i = 0;

	    if ($results) {
	    	foreach ($results as $result) {
			    $thumbnail = $resource->getMainThumb('products',
			                                     $result['product_id'],
			                                     $this->config->get('config_image_thumb_width'),
			                                     $this->config->get('config_image_thumb_height'),true);

	    		$response->rows[ $i ]['id'] = $result['product_id'];
	    		$response->rows[ $i ]['cell']['thumb'] = $thumbnail['thumb_url'];
	    		$response->rows[ $i ]['cell']['name'] = $result['name'];
	    		$response->rows[ $i ]['cell']['description'] = $result['description'];
	    		$response->rows[ $i ]['cell']['model'] = $result['model'];
	    		// Need global methos to get the price. 
	    		$response->rows[ $i ]['cell']['price'] = '';
	    		$i++;
	    	}
	    }

		
        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( $response );
		$this->rest->sendResponse( 200 );
	}
		
}        