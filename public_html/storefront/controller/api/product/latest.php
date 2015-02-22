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
class ControllerApiProductLatest extends AControllerAPI {
	
	public function get() {
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadModel('catalog/product');

		$filter_data = array(
			'method' => 'get'
		);

		$filter = new AFilter( $filter_data );
		$filters = $filter->getFilterData();

		$results = $this->model_catalog_product->getLatestProducts($filters['limit']);

		$response = new stdClass();
		$response->page = $filter->getParam('page');
		$response->total = $total_pages;
		$response->records = $filters['limit'];
		$response->limit = $filters['limit'];
		$response->sidx = $filters['sort'];
		$response->sord = $filters['order'];
		$response->params = $filters;

		$i = 0;
		if ($results) {
			$resource = new AResource('image');
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

		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( $response );
		$this->rest->sendResponse( 200 );
	}

	
}