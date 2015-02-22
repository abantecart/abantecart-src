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
class ControllerApiProductRelated extends AControllerAPI {
	
	public function get() {
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$product_id = $this->request->get['product_id'];

		if ( !$product_id ) {
			$this->rest->setResponseData( array('Error' => 'Missing product ID as a required parameter') );
			$this->rest->sendResponse( 200);
			return null;
		}

		$products = array();
    	$this->loadModel('catalog/review');		
    	$this->loadModel('catalog/product');		
		$results = $this->model_catalog_product->getProductRelated($product_id);

		
		foreach ($results as $result) {
				$resource = new AResource('image');
				$sizes = array('main'=> array( 'width'=>$this->config->get('config_image_related_width'),
											   'height' => $this->config->get('config_image_related_height')),
							   'thumb'=> array('width'=>$this->config->get('config_image_thumb_width'),
											   'height' => $this->config->get('config_image_thumb_height')));
				$image = $resource->getResourceAllObjects('products',  $result['product_id'], $sizes,1);

				if ($this->config->get('enable_reviews')) {
					$rating = $this->model_catalog_review->getAverageRating($result['product_id']);
				} else {
					$rating = false;
				}
				
				$special = FALSE;
				$discount = $this->model_catalog_product->getProductDiscount($result['product_id']);
				if ($discount) {
					$price = $this->currency->format($this->tax->calculate($discount, $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
					$special = $this->model_catalog_product->getProductSpecial($result['product_id']);
					if ($special) {
						$special = $this->currency->format($this->tax->calculate($special, $result['tax_class_id'], $this->config->get('config_tax')));
					}
				}
			
				$options = $this->model_catalog_product->getProductOptions($result['product_id']);			
				if ($options) {
					$add = 'a/product/product';
				} else {
					$add = 'a/checkout/cart';
				}
			
				$products[] = array(
					'product_id'    => $result['product_id'],
					'name'    		=> $result['name'],
					'model'   		=> $result['model'],
					'rating'  		=> $rating,
					'stars'   		=> sprintf($this->language->get('text_stars'), $rating),
					'price'   		=> $price,
					'options'   	=> $options,
					'special' 		=> $special,
					'image'   		=> $image['main_url'],
					'thumb'   		=> $image['thumb_url'],
					'cart_add_rt'   => $add
				);

		}

        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( array( 'total' => count($products), 'related_products' => $products) );
		$this->rest->sendResponse( 200 );
	}

	
}