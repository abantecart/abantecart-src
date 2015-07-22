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

class ControllerBlocksSpecial extends AController {
	public $data;
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('blocks/special');

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->loadModel('catalog/product');
		$this->loadModel('catalog/review');
		$this->loadModel('tool/seo_url');
		$this->loadModel('tool/image');
		$promoton = new APromotion();
		
		$this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
		
		$this->data['products'] = array();
		
		$results = $promoton->getProductSpecials('pd.name', 'ASC', 0, $this->config->get('config_special_limit'));

        $resource = new AResource('image');
		foreach ($results as $result) {
			$thumbnail = $resource->getMainThumb('products',
			                                     $result['product_id'],
			                                     $this->config->get('config_image_product_width'),
			                                     $this->config->get('config_image_product_height'),true);
           	
			if ($this->config->get('enable_reviews')) {
				$rating = $this->model_catalog_review->getAverageRating($result['product_id']);	
			} else {
				$rating = false;
			}

			$special = FALSE;
			
			$discount = $promoton->getProductDiscount($result['product_id']);
			
			if ($discount) {
				$price = $this->currency->format($this->tax->calculate($discount, $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
			
				$special = $promoton->getProductSpecial($result['product_id']);
			
				if ($special) {
					$special = $this->currency->format($this->tax->calculate($special, $result['tax_class_id'], $this->config->get('config_tax')));
				}						
			}
			
			$options = $this->model_catalog_product->getProductOptions($result['product_id']);
			
			if ($options) {
				$add = $this->html->getSEOURL('product/product','&product_id=' . $result['product_id'],'&encode');
			} else {
                if($this->config->get('config_cart_ajax')){
                    $add = '#';
                }else{
                    $add = $this->html->getSecureURL('checkout/cart', '&product_id=' . $result['product_id'], '&encode');
                }
			}

			//check for stock status, availability and config
			$track_stock = false;
			$in_stock = false;
			$no_stock_text = $result['stock'];
			$total_quantity = 0;
			if ( $this->model_catalog_product->isStockTrackable($result['product_id']) ) {
				$track_stock = true;
    			$total_quantity = $this->model_catalog_product->hasAnyStock($result['product_id']);
    			//we have stock or out of stock checkout is allowed
    			if ($total_quantity > 0 || $this->config->get('config_stock_checkout')) {
	    			$in_stock = true;
    			}
			}
			
			$this->data['products'][] = array(
				'product_id'    => $result['product_id'],
				'name'    		=> $result['name'],
				'blurb' => $result['blurb'],
				'model'   		=> $result['model'],
				'rating'  		=> $rating,
				'stars'   		=> sprintf($this->language->get('text_stars'), $rating),
				'price'   		=> $price,
				'call_to_order'=> $result['call_to_order'],
				'options'   	=> $options,
				'special' 		=> $special,
				'thumb'   		=> $thumbnail,
				'href'    		=> $this->html->getSEOURL('product/product','&product_id=' . $result['product_id'],'&encode'),
				'add'    		=> $add,
				'track_stock' => $track_stock,
				'in_stock'		=> $in_stock,
				'no_stock_text' => $no_stock_text,
				'total_quantity'=> $total_quantity			
			);
		}

		if ($this->config->get('config_customer_price')) {
			$this->data['display_price'] = TRUE;
		} elseif ($this->customer->isLogged()) {
			$this->data['display_price'] = TRUE;
		} else {
			$this->data['display_price'] = FALSE;
		}
		$this->data['review_status'] = $this->config->get('enable_reviews');
		// framed needs to show frames for generic block.
		//If tpl used by listing block framed was set by listing block settings
		$this->data['block_framed'] = true;
        $this->view->batchAssign($this->data);
		$this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
