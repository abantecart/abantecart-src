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

class ControllerBlocksCart extends AController {
	
	public $data = array();
	
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadModel('tool/seo_url');
		$this->loadLanguage('total/total');
    	$this->data['heading_title'] = $this->language->get('heading_title');
    	
		$this->data['text_subtotal'] = $this->language->get('text_subtotal');
		$this->data['text_empty'] = $this->language->get('text_empty');
		$this->data['text_remove'] = $this->language->get('text_remove');
		$this->data['text_confirm'] = $this->language->get('text_confirm');
		$this->data['text_view'] = $this->language->get('text_view');
		$this->data['text_checkout'] = $this->language->get('text_checkout');
		$this->data['text_items'] = $this->language->get('text_items');
		$this->data['text_total'] = $this->language->get('text_total');

		$this->data['view'] = $this->html->getURL('checkout/cart');
		$this->data['remove'] = $this->html->getURL('r/checkout/cart');
		$this->data['checkout'] = $this->html->getURL('checkout/shipping');
		
		$products = array();
		
		$qty = 0;

		$resource = new AResource('image');

    	foreach ($this->cart->getProducts() as $result) {
        	$option_data = array();

			$thumbnail = $resource->getMainThumb('products',
			                                     $result['product_id'],
			                                     $this->config->get('config_image_additional_width'),
			                                     $this->config->get('config_image_additional_width'),true);

        	foreach ($result['option'] as $option) {
		        if($option['element_type']=='H'){ continue;} //hide hidden options
		        $value = $option['value'];
		        // hide binary value for checkbox
                if($option['element_type']=='C' && in_array($value, array(0,1))){
                    $value = '';
                }
                // strip long textarea value
                if($option['element_type']=='T'){
                    $title = strip_tags($value);
                    $title = str_replace('\r\n',"\n",$title);

                    $value = str_replace('\r\n',"\n",$value);
	                if(mb_strlen($value) > 64){
		                $value = mb_substr($value, 0, 64) . '...';
	                }
                }

          		$option_data[] = array(
            		'name'  => $option['name'],
            		'value' => $value,
			        'title' => $title
          		);
        	}
			
			$qty += $result['quantity'];
			
      		$products[] = array(
        		'key' 		 => $result['key'],
        		'name'       => $result['name'],
				'option'     => $option_data,
        		'quantity'   => $result['quantity'],
				'stock'      => $result['stock'],
				'price'      => $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))),
				'href'       => $this->html->getSEOURL('product/product','&product_id=' . $result['product_id'],true),
				'thumb'   		=> $thumbnail,
      		);
    	}

		$this->data['products'] = $products;
		$this->data['total_qty'] = $qty;

      	$display_totals = $this->cart->buildTotalDisplay();
    	$this->data['totals'] = $display_totals['total_data'];
		$this->data['subtotal'] = $this->currency->format($display_totals['total']);
		$this->data['taxes'] = $display_totals['taxes'];
		
		$this->data['ajax'] = $this->config->get('cart_ajax');
		$this->view->batchAssign($this->data);

		$this->processTemplate();
		
		//init controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

	}

}