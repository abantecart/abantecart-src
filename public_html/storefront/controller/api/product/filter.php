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
class ControllerApiProductFilter extends AControllerAPI {
	
	public function get() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
        
	    $this->loadModel('catalog/product');

	    $filter_params = array('category_id', 'manufacturer_id', 'keyword', 'match', 'pfrom', 'pto' );
 		$grid_filter_params = array( 'name', 'description', 'model', 'sku' );
		$filter_data = array(
			'method' => 'get',
			'filter_params' => $filter_params, 
			'grid_filter_params' => $grid_filter_params
		);

		$filter = new AFilter( $filter_data );


		$filters = $filter->getFilterData();
		$category_id = $filter->getFilterParam('category_id');
		$manufacturer_id = $filter->getFilterParam('manufacturer_id');
		$keyword = $filter->getFilterParam('keyword');

		if ( !$category_id && !$manufacturer_id && !$keyword) {
			$this->rest->setResponseData( array('Error' => 'Missing one of required product filter parameters') );
			$this->rest->sendResponse(200);
			return null;
		}


	    //get total
		if($keyword){
			$total = $this->model_catalog_product->getTotalProducts( $filters );
		}elseif($category_id){
			$total = $this->model_catalog_product->getTotalProductsByCategoryId( $category_id );
		}elseif($manufacturer_id){
			$total = $this->model_catalog_product->getTotalProductsByManufacturerId( $manufacturer_id );
		}

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
	    $response->limit = $filters['limit'];
	    $response->sidx = $filters['sort'];
	    $response->sord = $filters['order'];
	    $response->params = $filters;

	    $resource = new AResource('image');

		if($keyword){
	    	$results = $this->model_catalog_product->getProducts( $filters );
		}elseif($category_id){
	    	$results = $this->model_catalog_product->getProductsByCategoryId( $category_id,
																			  $filters['sort'],
	    																	  $filters['order'],
	    																	  $filters['start'],
	    																	  $filters['limit'] );
		}elseif($manufacturer_id){
	    	$results = $this->model_catalog_product->getProductsByManufacturerId( $manufacturer_id,
																				  $filters['sort'],
																				  $filters['order'],
																				  $filters['start'],
																				  $filters['limit'] );
		}

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
	    		$response->rows[ $i ]['cell']['price'] = $this->currency->convert($result['final_price'], $this->config->get('config_currency'), $this->currency->getCode());
				$response->rows[ $i ]['cell']['currency_code'] = $this->currency->getCode();
	    		$response->rows[ $i ]['cell']['rating'] = $result['rating'];
	    		$i++;
	    	}
	    }

		
        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( $response );
		$this->rest->sendResponse( 200 );
	}
		
}        