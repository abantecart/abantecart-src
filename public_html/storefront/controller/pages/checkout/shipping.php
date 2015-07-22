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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}
class ControllerPagesCheckoutShipping extends AController {
	public $error = array();
	public $data = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$cart_rt = 'checkout/cart';		
		$checkout_rt = 'checkout/shipping';		
		$payment_rt = 'checkout/payment';	
		$login_rt = 'account/login';
		$home_rt = 'index/home';	
		$address_rt = 'checkout/address/shipping';	
		if($this->config->get('embed_mode') == true){
			$cart_rt = 'r/checkout/cart/embed';
		}
		
		//validate if order min/max are met
		if (!$this->cart->hasMinRequirement() || !$this->cart->hasMaxRequirement()) {
			$this->redirect($this->html->getSecureURL('checkout/cart'));
		}

		if ( $this->request->is_POST() && $this->validate()) {

			$shipping = explode('.', $this->request->post['shipping_method']);
			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][ $shipping[ 0 ] ]['quote'][ $shipping[ 1 ] ];
			$this->session->data['comment'] = strip_tags($this->request->post['comment']);

			//process data
			$this->extensions->hk_ProcessData($this);

			$this->redirect($this->html->getSecureURL($payment_rt));
		}

		if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->redirect($this->html->getSecureURL($cart_rt));
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->html->getSecureURL($checkout_rt);

			$this->redirect($this->html->getSecureURL($login_rt));
		}
        unset($this->session->data['redirect']);
        
        //if no products require shipping go to payment step
		if (!$this->cart->hasShipping()) {
			unset($this->session->data['shipping_address_id']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);

			$this->tax->setZone($this->session->data['country_id'], $this->session->data['zone_id']);
			$this->redirect($this->html->getSecureURL($payment_rt));
		}
		
		//If no shipping address is set yet, use default
		if (!isset($this->session->data['shipping_address_id'])) {
			$this->session->data['shipping_address_id'] = $this->customer->getAddressId();
		}

		//still missing address, go to address selection page
		if (!$this->session->data['shipping_address_id']) {
			$this->redirect($this->html->getSecureURL($address_rt));
		}

		$this->loadModel('account/address');
		$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
		
		//something wrong with shipping address go to address selection page
		if (!$shipping_address) {
			$this->redirect($this->html->getSecureURL($address_rt));
		}

		// if tax zone is taken from shipping address
		if (!$this->config->get('config_tax_customer')) {
			$this->tax->setZone($shipping_address['country_id'], $shipping_address['zone_id']);
		} else { // if tax zone is taken from billing address
			$address = $this->model_account_address->getAddress($this->customer->getAddressId());
			$this->tax->setZone($address['country_id'], $address['zone_id']);
		}

		$this->loadModel('checkout/extension');

		if (!isset($this->session->data['shipping_methods']) || !$this->config->get('config_shipping_session')) {
			$quote_data = array();

			$results = $this->model_checkout_extension->getExtensions('shipping');
			foreach ($results as $result) {
				$this->loadModel('extension/' . $result['key']);

				/** @noinspection PhpUndefinedMethodInspection */
				$quote = $this->{'model_extension_' . $result['key']}->getQuote($shipping_address);

				if ($quote) {
					$quote_data[ $result['key'] ] = array(
						'title' => $quote['title'],
						'quote' => $quote['quote'],
						'sort_order' => $quote['sort_order'],
						'error' => $quote['error']
					);
					//# Add storefront icon if available
					$ext_setgs = $this->model_checkout_extension->getSettings($result['key']);
					$icon = $ext_setgs[$result['key']."_shipping_storefront_icon"];
					if ( has_value( $icon ) ) {
						$icon_data = $this->model_checkout_extension->getSettingImage($icon);
						$icon_data['image'] =  $icon;
						$quote_data[ $result['key'] ]['icon'] = $icon_data;
					}
				}
			}

			$sort_order = array();
			foreach ($quote_data as $key => $value) {
				$sort_order[ $key ] = $value['sort_order'];
			}
			
			array_multisort($sort_order, SORT_ASC, $quote_data);
			$this->session->data['shipping_methods'] = $quote_data;
		}

		//# If only 1 shipping and it is set to be defaulted, select and skip and redirect to paymnet 
		if (count($this->session->data['shipping_methods']) == 1 && $this->request->get['mode'] != 'edit') {
		    //set only method
		    $only_method = $this->session->data['shipping_methods'];
		    foreach ($only_method as $key => $value) {
		    	$method_name = $key;		
		    	#Check config if we allowed to set this shipping and skip the step
		    	$ext_config = $this->model_checkout_extension->getSettings($method_name);
		    	$autoselect = $ext_config[$method_name."_autoselect"];
		    	if ($autoselect) {
					if(sizeof($only_method[$method_name]['quote'])==1){
						$this->session->data['shipping_method'] = current($only_method[$method_name]['quote']);
						$this->redirect($this->html->getSecureURL($payment_rt));
					}
		    	}
		    }
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getURL('index/home'),
		                                    'text' => $this->language->get('text_home'),
		                                    'separator' => FALSE
		                               ));

		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getURL($cart_rt),
		                                    'text' => $this->language->get('text_basket'),
		                                    'separator' => $this->language->get('text_separator')
		                               ));

		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getURL($checkout_rt),
		                                    'text' => $this->language->get('text_shipping'),
		                                    'separator' => $this->language->get('text_separator')
		                               ));

		$this->data['error_warning'] = $this->error['warning'];

		if (isset($this->session->data['shipping_methods']) && !$this->session->data['shipping_methods']) {
			$this->data['error_warning'] = $this->language->get('error_no_shipping');
		}

		$this->data['address'] = $this->customer->getFormatedAdress($shipping_address, $shipping_address['address_format'] );

		$item = HtmlElementFactory::create(array( 'type' => 'button',
		                                        'name' => 'change_address',
		                                        'style' => 'button',
		                                        'text' => $this->language->get('button_change_address')
		                                   ));
		$this->data['change_address'] = $item;
		$this->data['change_address_href'] = $this->html->getSecureURL($address_rt);

		$form = new AForm();
		$form->setForm(array( 'form_name' => 'shipping' ));
		$this->data['form']['form_open'] = $form->getFieldHtml(
																	array( 'type' => 'form',
																		   'name' => 'shipping',
																		   'action' => $this->html->getSecureURL($checkout_rt) ));

		$this->data['shipping_methods'] = $this->session->data['shipping_methods'] 	? $this->session->data['shipping_methods'] : array();
		$shipping = $this->session->data['shipping_method']['id'];
		if ($this->data['shipping_methods']) {
			foreach ($this->data['shipping_methods'] as $k => $v) {
				if($v['quote']){
					foreach($v['quote'] as $key => $val){
						//check if we have only one method and select by default if was selected before
						$selected = FALSE;
						if ( count($this->data['shipping_methods']) == 1 && count($v['quote']) == 1 ) {
							$selected = TRUE;
						} else if( $shipping == $val['id'] )  {
							$selected = TRUE;
						}

						$this->data['shipping_methods'][ $k ]['quote'][$key]['radio'] = $form->getFieldHtml(array(
																									  'type' => 'radio',
																									  'id' => $val['id'],
																									  'name' => 'shipping_method',
																									  'options' => array( $val['id'] => '' ),
																									  'value' => $selected
																								 ));
					}
				}
			}
		} else {
			$this->data['shipping_methods'] = array();
		}


		$this->data['comment'] = isset($this->request->post['comment']) ? $this->request->post['comment'] : $this->session->data['comment'];
		$this->data['form']['comment'] =  $form->getFieldHtml( array(
																	'type' => 'textarea',
																	'name' => 'comment',
																	'value' => $this->data['comment'],
																	'attr' => ' rows="3" style="width: 99%" ' ));
		$this->data['back'] = $this->html->getSecureURL($cart_rt);
		$this->data['form']['back'] = $form->getFieldHtml( array( 'type' => 'button',
		                                                            'name' => 'back',
			                                                        'style' => 'button',
		                                                            'text' => $this->language->get('button_back') ));
		$this->data['form']['continue'] = $form->getFieldHtml( array(
                                                                       'type' => 'submit',
		                                                               'name' => $this->language->get('button_continue') ));
		//render buttons
		$this->view->batchAssign($this->data);
		if($this->config->get('embed_mode') == true){
			$this->view->assign('buttons', $this->view->fetch('embed/checkout/shipping.buttons.tpl') );
			//load special headers
	        $this->addChild('responses/embed/head', 'head');
	        $this->addChild('responses/embed/footer', 'footer');
		    $this->processTemplate('embed/checkout/shipping.tpl');
		} else {
			$this->view->assign('buttons', $this->view->fetch('pages/checkout/shipping.buttons.tpl') );
			$this->processTemplate('pages/checkout/shipping.tpl');
		}

		//update data before render
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

	}

	public function validate() {
		if (!isset($this->request->post['shipping_method'])) {
			$this->error['warning'] = $this->language->get('error_shipping');
		} else {
			$shipping = explode('.', $this->request->post['shipping_method']);
			if (!isset($this->session->data['shipping_methods'][ $shipping[ 0 ] ]['quote'][ $shipping[ 1 ] ])) {
				$this->error['warning'] = $this->language->get('error_shipping');
			}
		}

		//validate post data
		$this->extensions->hk_ValidateData($this);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
