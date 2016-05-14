<?php  
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

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
class ControllerBlocksLatest extends AController {
	public $data;
	public function main() {

		if($this->html_cache()){
			return;
		}

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('blocks/latest');

      	$this->view->assign('heading_title', $this->language->get('heading_title', 'blocks/latest') );

		$this->loadModel('catalog/product');
		$this->loadModel('catalog/review');
		$this->loadModel('tool/image');
		
	    $this->view->assign('button_add_to_cart', $this->language->get('button_add_to_cart') );
		$this->data['products'] = array();
		
		$results = $this->model_catalog_product->getLatestProducts($this->config->get('config_latest_limit'));
		$product_ids = array();
		foreach($results as $result){
			$product_ids[] = $result['product_id'];
		}

		$products_info = $this->model_catalog_product->getProductsAllInfo($product_ids);

        //get thumbnails by one pass
        $resource = new AResource('image');
        $thumbnails = $resource->getMainThumbList(
                        'products',
                        $product_ids,
                        $this->config->get('config_image_product_width'),
                        $this->config->get('config_image_product_height')
        );
        $stock_info = $this->model_catalog_product->getProductsStockInfo($product_ids);

		foreach ($results as $result) {
			$thumbnail = $thumbnails[ $result['product_id'] ];

			$rating = $products_info[$result['product_id']]['rating'];

			$special = FALSE;

			$discount = $products_info[$result['product_id']]['discount'];

			if ($discount) {
				$price = $this->currency->format($this->tax->calculate($discount, $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
				$special = $products_info[$result['product_id']]['special'];
				if ($special) {
					$special = $this->currency->format($this->tax->calculate($special, $result['tax_class_id'], $this->config->get('config_tax')));
				}						
			}
			
			$options = $products_info[$result['product_id']]['options'];

			if ($options) {
				$add = $this->html->getSEOURL('product/product','&product_id=' . $result['product_id'], '&encode');
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
			if ( $stock_info[$result['product_id']]['subtract'] ) {
				$track_stock = true;
    			$total_quantity = $stock_info[$result['product_id']]['quantity'];
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
				'href'    		=> $this->html->getSEOURL('product/product','&product_id=' . $result['product_id'], '&encode'),
				'add'    		=> $add,
				'track_stock' 	=> $track_stock,
				'in_stock'		=> $in_stock,
				'no_stock_text' => $no_stock_text,
				'total_quantity'=> $total_quantity,
				'date_added'    => $result['date_added']
			);
		}

        $this->view->assign('products', $this->data['products'] );

		if ($this->config->get('config_customer_price')) {
			$display_price = TRUE;
		}elseif ($this->customer->isLogged()) {
			$display_price = TRUE;
		} else {
			$display_price = FALSE;
		}
        $this->view->assign('block_framed', true );
        $this->view->assign('display_price', $display_price );
        $this->view->assign('review_status', $this->config->get('enable_reviews'));
		$this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}
}
