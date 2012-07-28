<?php 
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadModel('tool/seo_url');
    	$this->view->assign('heading_title', $this->language->get('heading_title'));
    	
		$this->view->assign('text_subtotal', $this->language->get('text_subtotal'));
		$this->view->assign('text_empty', $this->language->get('text_empty'));
		$this->view->assign('text_remove', $this->language->get('text_remove'));
		$this->view->assign('text_confirm', $this->language->get('text_confirm'));
		$this->view->assign('text_view', $this->language->get('text_view'));
		$this->view->assign('text_checkout', $this->language->get('text_checkout'));
		$this->view->assign('text_items', $this->language->get('text_items'));
		
		$this->view->assign('view', $this->html->getURL('checkout/cart'));
		$this->view->assign('remove', $this->html->getURL('r/checkout/cart'));
		$this->view->assign('checkout', $this->html->getURL('checkout/shipping'));
		
		$products = array();
		
		$qty = 0;
		
    	foreach ($this->cart->getProducts() as $result) {
        	$option_data = array();

        	foreach ($result['option'] as $option) {
          		$option_data[] = array(
            		'name'  => $option['name'],
            		'value' => $option['value']
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
				'href'       => $this->html->getSEOURL('product/product','&product_id=' . $result['product_id']),
      		);
    	}

        $this->view->assign('products', $products );
		
		$this->view->assign('total_qty', $qty);
	
		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();
		 
		$this->loadModel('checkout/extension');
		
		$sort_order = array(); 
		
		$results = $this->model_checkout_extension->getExtensions('total');
		
		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get($value['key'] . '_sort_order');
		}
		
		array_multisort($sort_order, SORT_ASC, $results);
		
		foreach ($results as $result) {
			if($result['key']=='total'){
				// apply promotions
				$promotions = new APromotion();
				$promotions->apply_promotions($total_data,$total);
				if(time()-$this->session->data['promotion_data']['time']<1){
					$total_data = $this->session->data['promotion_data']['total_data'];
					$total = $this->session->data['promotion_data']['total'];
				}else{
					unset($this->session->data['promotion_data']);
				}
			}
			$this->loadModel('total/' . $result['key']);
			$this->{'model_total_' . $result['key']}->getTotal($total_data, $total, $taxes);
		}
		
		$sort_order = array(); 
	  
		foreach ($total_data as $key => $value) {
      		$sort_order[$key] = $value['sort_order'];
    	}

    	array_multisort($sort_order, SORT_ASC, $total_data);
		
    	$this->view->assign('totals', $total_data);
		
		$this->view->assign('ajax', $this->config->get('cart_ajax'));
        $this->processTemplate();
		
	}

}