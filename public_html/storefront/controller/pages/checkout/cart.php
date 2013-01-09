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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesCheckoutCart extends AController {
	private $error = array();
	public $data = array();
	public function main() {
		$error_msg = array();
		
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if ($this->request->server['REQUEST_METHOD'] == 'GET' && isset($this->request->get['product_id']) ) {

			if (isset($this->request->get['option'])) {
				$option = $this->request->get['option'];
			} else {
				$option = array();	
			}
			
			if (isset($this->request->get['quantity'])) {
				$quantity = $this->request->get['quantity'];
			} else {
				$quantity = 1;
			}
			
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['payment_method']);

			$this->cart->add($this->request->get['product_id'], $quantity, $option);

			$this->redirect($this->html->getSecureURL('checkout/cart'));
			
		} elseif ($this->request->server['REQUEST_METHOD'] == 'POST') {
      		if (isset($this->request->post['quantity'])) {
				if (!is_array($this->request->post['quantity'])) {

					$this->loadModel('catalog/product');
					$product_id = $this->request->post['product_id'];

					if (isset($this->request->post['option'])) {
						$options = $this->request->post['option'];
					} else {
						$options = array();
					}
					
					if ( $this->model_catalog_product->validateRequiredOptions($product_id, $options) ) {
						$this->session->data['error'] = $this->language->get('error_required_options');
						$this->redirect($_SERVER['HTTP_REFERER']);
					}

      				$this->cart->add($this->request->post['product_id'], $this->request->post['quantity'], $options);
				} else {
					foreach ($this->request->post['quantity'] as $key => $value) {
	      				$this->cart->update($key, $value);
					}
				}
				
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['shipping_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['payment_method']);
	
				#upate min and max
				$this->cart->setMinQty();
				$this->cart->setMaxQty();
      		}

      		if (isset($this->request->post['remove'])) {
	    		foreach (array_keys($this->request->post['remove']) as $key) {
          			$this->cart->remove($key);
				}
      		}
			
			if (isset($this->request->post['redirect'])) {
				$this->session->data['redirect'] = $this->request->post['redirect'];
			}	
			
			if (isset($this->request->post['quantity']) || isset($this->request->post['remove'])) {
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['shipping_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['payment_method']);	
				
				$this->redirect($this->html->getSecureURL('checkout/cart'));
			}
    	}

    	$this->document->setTitle( $this->language->get('heading_title') );

      	$this->document->resetBreadcrumbs();

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('index/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	 )); 

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('checkout/cart'),
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	 ));
			
    	if ($this->cart->hasProducts()) {

            if (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) {
                $error_msg[] = $this->language->get('error_stock');
			}
						
			$this->loadModel('tool/seo_url'); 
						
      		$products = array();
			$resource = new AResource('image');

		    $form = new AForm();
		    $form->setForm(array( 'form_name' => 'cart' ));
            $this->data['form'][ 'form_open' ] = $form->getFieldHtml(
                                                                array( 'type' => 'form',
                                                                       'name' => 'cart',
                                                                       'action' => $this->html->getSecureURL('checkout/cart')));


      		foreach ($this->cart->getProducts() as $result) {
        		$option_data = array();
				$thumbnail = $resource->getMainThumb('products',
			                                     $result['product_id'],
			                                     $this->config->get('config_image_cart_width'),
			                                     $this->config->get('config_image_cart_height'),true);



        		foreach ($result['option'] as $option) {
          			$option_data[] = array(
            			'name'  => $option['name'],
            			'value' => $option['value']
          			);
        		}

        		$products[] = array(
			        'remove' => $form->getFieldHtml( array( 'type' => 'checkbox',
				                                            'name' => 'remove['.$result['key'].']',
			                                                )),
          			'key'      => $result['key'],
          			'name'     => $result['name'],
          			'model'    => $result['model'],
          			'thumb'    => $thumbnail,
          			'option'   => $option_data,
          			'quantity' => $form->getFieldHtml( array( 'type' => 'input',
				                                              'name' => 'quantity['.$result['key'].']',
				                                              'value' => $result['quantity'],
			                                                  'attr' => ' size="3" ',
				                                              'style' => 'short'
			                                                )),
          			'stock'    => $result['stock'],
					'price'    => $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))),
					'total'    => $this->currency->format($this->tax->calculate($result['total'], $result['tax_class_id'], $this->config->get('config_tax'))),
					'href'     => $this->html->getSEOURL('product/product', '&product_id=' . $result['product_id'])
        		);
      		}
            $this->data['products'] =  $products ;

		    $this->data['form'][ 'update' ] = $form->getFieldHtml( array(
                                                                       'type' => 'submit',
		                                                               'name' => $this->language->get('button_update') ));
			
			$this->data['form'][ 'checkout' ] = $form->getFieldHtml( array(
																			'type' => 'button',
																			'name' => 'checkout',
																			'text' => $this->language->get('button_checkout'),
			                                                                'style' => 'button' ));
			
			if ($this->config->get('config_cart_weight')) {
				$this->data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class'));
			} else {
				$this->data['weight'] = FALSE;
			}
			
      		$display_totals = $this->cart->buildTotalDisplay();      		
            $this->data['totals'] = $display_totals['total_data'];;
			
			if (isset($this->session->data['redirect'])) {
      			$this->data['continue'] = $this->session->data['redirect'];
				unset($this->session->data['redirect']);
			} else {
                $this->data['continue'] = $this->html->getURL('index/home');
			}
			
            $this->data['checkout'] = $this->html->getSecureURL('checkout/shipping');

			#Check if order total max/min is set and met
			$cf_total_min = $this->config->get('total_order_minimum'); 
			$cf_total_max = $this->config->get('total_order_maximum'); 
			if ( !$this->cart->hasMinRequirement() ) {
			    $this->data['form'][ 'checkout' ] = '';
			    $error_msg[] = sprintf($this->language->get('error_order_minimum'), $this->currency->format($cf_total_min) );
			}
			if ( !$this->cart->hasMaxRequirement() ) {
			    $this->data['form'][ 'checkout' ] = '';
			    $error_msg[] = sprintf($this->language->get('error_order_maximum'), $this->currency->format($cf_total_max) );	
			}	
			
			$this->view->assign('error_warning', $error_msg );
			$this->view->setTemplate( 'pages/checkout/cart.tpl' );

    	} else {            
            $this->data['heading_title'] = $this->language->get('heading_title');
            $this->data['text_error'] = $this->language->get('text_error');

		    $this->data['button_continue'] = HtmlElementFactory::create( array('name' => 'continue',
																			   'type' => 'button',
																			   'text' => $this->language->get('button_continue'),
																			   'href' =>$this->html->getURL('index/home'),
																			   'style' => 'button' ));
		    $this->data['button_continue'] = $this->data['button_continue']->getHtml();

            $this->view->setTemplate( 'pages/error/not_found.tpl' );
    	}


		$this->view->batchAssign( $this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

  	}

}