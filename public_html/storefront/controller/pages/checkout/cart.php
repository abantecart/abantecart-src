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
class ControllerPagesCheckoutCart extends AController {
	private $error = array();
	public $data = array();

	public function main() {
		$error_msg = array();

		$cart_rt = 'checkout/cart';		
		$product_rt = 'product/product';		
		$checkout_rt = 'checkout/shipping';		
		$home_rt = 'index/home';	
		//is this an embed mode	
		if($this->config->get('embed_mode') == true){
			$cart_rt = 'r/checkout/cart/embed';
		}
		
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		//process all possible requests first
		if ($this->request->is_GET() && isset($this->request->get['product_id']) ) {

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

			$this->redirect($this->html->getSecureURL($cart_rt));

		} else if ($this->request->is_GET() && isset($this->request->get['remove']) ) {
		
			//remove product with button claick.
          	$this->cart->remove($this->request->get['remove']);
			$this->redirect($this->html->getSecureURL($cart_rt));
			          	
		} else if ($this->request->is_POST()) {

			//if this is coupon, validate and apply
			if ( isset($this->request->post['coupon']) && $this->_validateCoupon() ) {
				$this->session->data[ 'coupon' ] = $this->request->post[ 'coupon' ];
				$this->data['success'] = $this->session->data[ 'success' ] = $this->language->get('text_coupon_success');
				unset($this->session->data['success']);
				//process data
				$this->extensions->hk_ProcessData($this);
			}
			if ($this->error['error_warning']) {
				$error_msg[] = $this->error['error_warning'];
			}
			
      		if (isset($this->request->post['quantity'])) {
				//we update cart
				if (!is_array($this->request->post['quantity'])) {

					$this->loadModel('catalog/product','storefront');
					$product_id = $this->request->post['product_id'];

					if (isset($this->request->post['option'])) {
						$options = $this->request->post['option'];
					} else {
						$options = array();
					}

					//for FILE-attributes
					if ( has_value($this->request->files['option']['name']) ) {

						$fm = new AFile();
						foreach ( $this->request->files['option']['name'] as $id => $name ) {

							$attribute_data = $this->model_catalog_product->getProductOption($product_id,$id);
							$attribute_data['settings'] = unserialize($attribute_data['settings']);
							$file_path_info = $fm->getUploadFilePath($attribute_data['settings']['directory'], $name);

							$options[$id] = $file_path_info['name'];

							if ( !has_value($name) ) {
								continue;
							}

							if($attribute_data['required'] && !$this->request->files['option']['size'][$id]){
								$this->session->data['error'] = $this->language->get('error_required_options');
								$this->redirect($_SERVER['HTTP_REFERER']);
							}

							$file_data = array(
								'option_id' => $id,
								'name' => $file_path_info['name'],
								'path' => $file_path_info['path'],
								'type' => $this->request->files['option']['type'][$id],
								'tmp_name' => $this->request->files['option']['tmp_name'][$id],
								'error' => $this->request->files['option']['error'][$id],
								'size' => $this->request->files['option']['size'][$id],
							);

							$file_errors = $fm->validateFileOption($attribute_data['settings'], $file_data, $attribute_data['required']);

							if ( has_value($file_errors) ) {
								$this->session->data['error'] = implode('<br/>', $file_errors);
								$this->redirect($_SERVER['HTTP_REFERER']);
							} else {
								$result = move_uploaded_file($file_data['tmp_name'], $file_path_info['path']);

								if ( !$result || $this->request->files[ 'package_file' ]['error'] ) {
									$this->session->data['error'] .= '<br>Error: ' . getTextUploadError($this->request->files['option']['error'][$id]);
									$this->redirect($_SERVER['HTTP_REFERER']);
								}
							}

							$dataset = new ADataset('file_uploads','admin');
							$dataset->addRows(
								array(
									'date_added'=> date("Y-m-d H:i:s",time()),
									'name' => $file_path_info['name'],
									'type' => $file_data['type'],
									'section' => 'product_option',
									'section_id' => $attribute_data['attribute_id'],
									'path' => $file_path_info['path'],
								)
							);

						}
					}

					if ($text_errors = $this->model_catalog_product->validateProductOptions($product_id, $options) ) {
						$this->session->data['error'] = $text_errors;
						//send options values back via _GET
						$url = '&'.http_build_query(array('option' => $this->request->post['option']));
						$this->redirect($this->html->getSecureURL($product_rt,'&product_id='.$this->request->post['product_id'].$url));
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
	
      		}

      		if (isset($this->request->post['remove'])) {
 	    		foreach (array_keys($this->request->post['remove']) as $key) {
          			$this->cart->remove($key);
				}
      		}
			
			//next page is requested after cart update
			if (isset($this->request->post['next_step'])) {
				$this->redirect($this->html->getSecureURL($this->request->post['next_step']));
			}
			
			if (isset($this->request->post['redirect'])) {
				$this->session->data['redirect'] = $this->request->post['redirect'];
			}	
			
			if (isset($this->request->post['quantity']) || isset($this->request->post['remove'])) {
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['shipping_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['payment_method']);	
				
				$this->redirect($this->html->getSecureURL($cart_rt));
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
						
			$this->loadModel('tool/seo_url','storefront'); 
						
      		$products = array();
			$resource = new AResource('image');

		    $form = new AForm();
		    $form->setForm(array( 'form_name' => 'cart' ));
            $this->data['form'][ 'form_open' ] = $form->getFieldHtml(
                                                                array( 'type' => 'form',
                                                                       'name' => 'cart',
                                                                       'action' => $this->html->getSecureURL($cart_rt)));

			$cart_products = $this->cart->getProducts();

      		foreach ($cart_products as $result) {
        		$option_data = array();
				$thumbnail = $resource->getMainThumb('products',
			                                     $result['product_id'],
			                                     (int)$this->config->get('config_image_cart_width'),
			                                     (int)$this->config->get('config_image_cart_height'),true);



        		foreach ($result['option'] as $option) {
			        $title = '';
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

        		$products[] = array(
			        'remove' => $form->getFieldHtml( array( 'type' => 'checkbox',
				                                            'name' => 'remove['.$result['key'].']',
			                                                )),
			        'remove_url' => $this->html->getSecureURL($cart_rt, '&remove='.$result['key']),                                        
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
					'href'     => $this->html->getSEOURL($product_rt, '&key='.$result['key'],true)
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
				$this->data['continue'] = str_replace('&amp;','&',$this->session->data['redirect']);
				unset($this->session->data['redirect']);
			} else {
                $this->data['continue'] = $this->html->getURL($home_rt);
			}
			$this->data['form'][ 'continue_shopping' ] = $form->getFieldHtml(
				array(
					'type' => 'button',
					'name' => 'continue_shopping',
					'text' => $this->language->get('button_shopping'),
					'style' => 'button',
					'href' => $this->data['continue']
				)
			);
			
            $this->data['checkout'] = $this->html->getSecureURL($checkout_rt);
            $this->data['checkout_rt'] = $checkout_rt;

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
			
			//prepare coupon display
			if($this->config->get('config_coupon_on_cart_page')){
				$this->view->assign( 'coupon_status', $this->config->get('coupon_status') );
				$action = $this->html->getSecureURL($cart_rt);
				$coupon_form = $this->dispatch('blocks/coupon_codes', array('action' => $action));
				$this->view->assign('coupon_form', $coupon_form->dispatchGetOutput() );
			}

			if($this->config->get('config_shipping_tax_estimate')){
				$form = new AForm();
				$form->setForm(array( 'form_name' => 'estimate' ));
				$this->data['form_estimate']['form_open'] = $form->getFieldHtml(
														array(	'type' => 'form',
																'name' => 'estimate',
																'action' => $this->html->getSecureURL($cart_rt)));
				$this->data['estimates_enabled'] = true;
			}
			//try to get shipping address details if we have them
			$country_id = $this->config->get('config_country_id');
			if ($this->session->data[ 'shipping_address_id' ]	) {
				$this->loadModel('account/address','storefront');
				$shipping_address = $this->model_account_address->getAddress($this->session->data[ 'shipping_address_id' ]);
				$postcode = $shipping_address['postcode'];
				$country_id = $shipping_address['country_id'];
				$zone_id = $shipping_address['zone_id'];
			}
			// use default address of customer for estimate form whe shipping address is unknown
			if(!$zone_id && $this->customer->isLogged()){
				$this->loadModel('account/address','storefront');
				$payment_address = $this->model_account_address->getAddress($this->customer->getAddressId());
				$postcode = $payment_address['postcode'];
				$country_id = $payment_address['country_id'];
				$zone_id = $payment_address['zone_id'];
			}

			if ($this->request->post['postcode']) {
				$postcode = $this->request->post['postcode'];
			}
			if ($this->request->post['country'][0]) {
				$country_id = $this->request->post['country'][0];
			}
			if ($this->request->post['country_zones'][0]) {
				$zone_id = $this->request->post['country_zones'][0];
			}
			if ( $zone_id ) {
				$this->loadModel('localisation/zone','storefront');
	    		$zone_data = $this->model_localisation_zone->getZone($zone_id);
			}											

			$this->data['form_estimate']['postcode'] = $form->getFieldHtml( array(
														'type' => 'input',
													 	'name' => 'postcode',
													  	'value' => $postcode,
													  	'style' => 'short',
														));

			$this->data['form_estimate']['country_zones'] = $form->getFieldHtml( array(
														'type' => 'zones',
													 	'name' => 'country',
													  	'submit_mode' => 'id',
													  	'value' => $country_id,
													  	'zone_name' => $zone_data['name'],
														'zone_value' => $zone_id
														));
														
			$this->data['form_estimate']['submit'] = $form->getFieldHtml( array(
														'type' => 'submit',
														'name' => $this->language->get('button_text_estimate') 
														));

		    if($this->session->data['error']){
			    $error_msg[] = $this->session->data['error'];
			    unset($this->session->data['error']);
		    }

			$this->view->assign('error_warning', $error_msg );
			$this->view->setTemplate( 'pages/checkout/cart.tpl' );

	    } else{
            $this->data['heading_title'] = $this->language->get('heading_title');
            $this->data['text_error'] = $this->language->get('text_error');

		    $this->data['button_continue'] = HtmlElementFactory::create( array('name' => 'continue',
																			   'type' => 'button',
																			   'text' =>  $this->language->get('button_continue'),
																			   'href' =>  $this->html->getURL($home_rt),
																			   'style' => 'button' ));
		    if($this->config->get('embed_mode') == true){
			    $this->data['back_url'] = $this->html->getURL('r/product/category');
		    }

            $this->view->setTemplate( 'pages/error/not_found.tpl' );
    	}

		$this->view->batchAssign( $this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

	private function _validateCoupon() {
		$promotion = new APromotion();
		$coupon = $promotion->getCouponData($this->request->post['coupon']);
		if (!$coupon) {
			$this->error[ 'error_warning' ] = $this->language->get('error_coupon');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}