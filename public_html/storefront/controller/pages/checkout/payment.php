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
class ControllerPagesCheckoutPayment extends AController {
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
		$address_rt = 'checkout/address/payment';	
		$confirm_rt = 'checkout/confirm';
		if($this->config->get('embed_mode') == true){
			$cart_rt = 'r/checkout/cart/embed';
		}

		//validate if order min/max are met
		if (!$this->cart->hasMinRequirement() || !$this->cart->hasMaxRequirement()) {
			$this->redirect($this->html->getSecureURL($cart_rt));
		}

		//Selections are posted, validate and apply
		if ( $this->request->is_POST() && isset($this->request->post['coupon']) && $this->_validateCoupon()) {
			$this->session->data['coupon'] = $this->request->post['coupon'];
			$this->session->data['success'] = $this->language->get('text_success');

			//process data
			$this->extensions->hk_ProcessData($this, __FUNCTION__);

			$this->redirect($this->html->getSecureURL($payment_rt));
		}
		//process balance
		if($this->request->get['balance']=='apply'){
			$order_totals = $this->cart->buildTotalDisplay(true);
			$order_total = $order_totals['total'];
			$balance = $this->currency->convert($this->customer->getBalance(),$this->config->get('config_currency'),$this->session->data['currency']);
			if($this->session->data['used_balance']){
				#check if we still have balance. 
				if($this->session->data['used_balance'] <= $balance){
					$this->session->data['used_balance_full'] = true;					
				} else {
					//if balance become less or 0 reaply partial
					$this->session->data['used_balance'] = $balance;
					$this->session->data['used_balance_full'] = false;				
				}
			} else if($balance > 0){
				if($balance >= $order_total){ //if enough
					$this->session->data['used_balance'] = $order_total;
					$this->session->data['used_balance_full'] = true;
				}else{ //partial pay
					$this->session->data['used_balance'] = $balance;
					$this->session->data['used_balance_full'] = false;
				}
			}
			unset($this->request->get['balance']);
		}
		if($this->request->get['balance']=='disapply'){
			unset($this->session->data['used_balance'],$this->request->get['balance'],$this->session->data['used_balance_full']);
		}
		//we might have some uncleaned session. Show only if comes together with used balance  
		if ($this->session->data['used_balance']) {
			$this->data['used_balance_full'] = $this->session->data['used_balance_full'];
		}

		if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->redirect($this->html->getSecureURL($cart_rt));
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->html->getSecureURL($checkout_rt);
			$this->redirect($this->html->getSecureURL($login_rt));
		}

		$this->loadModel('account/address');

		if ($this->cart->hasShipping()) {
			if (!isset($this->session->data['shipping_address_id']) || !$this->session->data['shipping_address_id']) {
				$this->redirect($this->html->getSecureURL($checkout_rt));
			}

			if (!isset($this->session->data['shipping_method'])) {
				$this->redirect($this->html->getSecureURL($checkout_rt));
			}
		} else {
			unset($this->session->data['shipping_address_id']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);

			//$this->tax->setZone($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
			$this->tax->setZone($this->session->data['country_id'], $this->session->data['zone_id']);
		}

		if (!isset($this->session->data['payment_address_id']) && isset($this->session->data['shipping_address_id']) && $this->session->data['shipping_address_id']) {
			$this->session->data['payment_address_id'] = $this->session->data['shipping_address_id'];
		}

		if (!isset($this->session->data['payment_address_id'])) {
			$this->session->data['payment_address_id'] = $this->customer->getAddressId();
		}

		if (!$this->session->data['payment_address_id']) {
			$this->redirect($this->html->getSecureURL($address_rt));
		}

		$this->loadModel('account/address');
		$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
		if (!$payment_address) {
			$this->redirect($this->html->getSecureURL($address_rt));
		}
		if (!$this->cart->hasShipping() || $this->config->get('config_tax_customer')) {
			$this->tax->setZone($payment_address['country_id'], $payment_address['zone_id']);
		}

		$this->loadModel('checkout/extension');
		$method_data = array();

		// If total amount of order is zero - do redirect on confirmation page
		$total = $this->cart->buildTotalDisplay(true);
		
		$results = $this->model_checkout_extension->getExtensions('payment');
		$ac_payments = array();
		//#Check config of selected shipping method and see if we have accepted payments restriction
		$shipping_ext = explode('.',$this->session->data['shipping_method']['id']);
		$ship_ext_config = $this->model_checkout_extension->getSettings($shipping_ext[0]);
		$accept_payment_ids = $ship_ext_config[$shipping_ext[0]."_accept_payments"];
		if ( is_array($accept_payment_ids) && count($accept_payment_ids) ) {
			//#filter only allowed payment methods based on shipping 
			foreach ($results as $result) {
				if ( in_array($result['extension_id'], $accept_payment_ids) ) {
					$ac_payments[] = $result;
				}
			}
		} else {
			$ac_payments = $results;
		}

		foreach ($ac_payments as $result) {
			//#filter only allowed payment methods based on total min/max
			$ext_setgs = $this->model_checkout_extension->getSettings($result['key']);
			$min = $ext_setgs[$result['key']."_payment_minimum_total"];
			$max = $ext_setgs[$result['key']."_payment_maximum_total"];
			if ( 	(has_value( $min ) && $total['total'] < $min ) 
				||  (has_value( $max ) && $total['total'] > $max )  ) {
				continue;
		    }			
		
			$this->loadModel('extension/' . $result['key']);
			$method = $this->{'model_extension_' . $result['key']}->getMethod($payment_address);
			if ($method) {
				$method_data[ $result['key'] ] = $method;
				//# Add storefront icon if available
				$icon = $ext_setgs[$result['key']."_payment_storefront_icon"];
				if ( has_value( $icon ) ) {
					$icon_data = $this->model_checkout_extension->getSettingImage($icon);
					$icon_data['image'] =  $icon;
					$method_data[ $result['key'] ]['icon'] = $icon_data;
				}
				//check if this is a redirect type of the payment
				if($ext_setgs[$result['key']."_redirect_payment"]) {
					$method_data[ $result['key'] ]['is_redirect_payment'] = true;
				}
			}
		}

		$this->session->data['payment_methods'] = $method_data;

		if ( $this->request->is_POST() && !isset($this->request->post['coupon']) && $this->_validate()) {
			$this->session->data['payment_method'] = $this->session->data['payment_methods'][ $this->request->post['payment_method'] ];
			$this->session->data['comment'] = strip_tags($this->request->post['comment']);

			//process data
			$this->extensions->hk_ProcessData($this);

			$this->redirect($this->html->getSecureURL($confirm_rt));
		}

		if($total['total']==0 && $this->request->get['mode'] != 'edit'){
			$this->session->data['payment_method'] = array(
															'id'         => 'no_payment_required',
															'title'      => $this->language->get('no_payment_required')
			      		);
			$this->redirect($this->html->getSecureURL($confirm_rt));

		}

		//# If only 1 payment and it is set to be defaulted, select and skip and redirect to confirmation 
		if (count($this->session->data['payment_methods']) == 1 && $this->request->get['mode'] != 'edit') {
		    //set only method
		    $only_method = $this->session->data['payment_methods'];
		    foreach ($only_method as $key => $value) {
		    	$method_name = $key;	
		    	#Check config if we allowed to set this payment and skip the step
		    	$ext_config = $this->model_checkout_extension->getSettings($method_name);
		    	$autoselect = $ext_config[$method_name."_autoselect"];
		    	if ( $autoselect ) {
		    		$this->session->data['payment_method'] = $only_method[$method_name];
		    		$this->redirect($this->html->getSecureURL($confirm_rt));				
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
		                                    'text' => $this->language->get('entry_shipping'),
		                                    'separator' => $this->language->get('text_separator')
		                               ));

		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getURL($payment_rt),
		                                    'text' => $this->language->get('entry_payment'),
		                                    'separator' => $this->language->get('text_separator')
		                               ));

		if (isset($this->session->data['error'])) {
			$this->view->assign('error_warning', $this->session->data['error']);
			unset($this->session->data['error']);
		} else {
			$this->view->assign('error_warning', $this->error['warning']);
		}

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}
		$action = $this->html->getSecureURL($payment_rt, '&mode='.$this->request->get['mode'],true);

		$this->data['change_address'] = HtmlElementFactory::create( array('type' => 'button',
			                                      'name' => 'change_address',
			                                      'style' => 'button',
		                                          'text' => $this->language->get('button_change_address')
		                                    ));

		$this->data['change_address_href'] = $this->html->getSecureURL($address_rt);

		$this->view->assign( 'coupon_status', $this->config->get('coupon_status') );
		$coupon_form = $this->dispatch('blocks/coupon_codes', array( 'action' => $action ) );
		$this->view->assign('coupon_form', $coupon_form->dispatchGetOutput() );

		$this->data['address'] = $this->customer->getFormatedAdress($payment_address, $payment_address['address_format'] );

		$form = new AForm();
		$form->setForm(array( 'form_name' => 'payment' ));
		$this->data['form']['form_open'] = $form->getFieldHtml(
                                                                array( 'type' => 'form',
                                                                       'name' => 'payment',
                                                                       'action' => $action ));

		$this->data['payment_methods'] = $this->session->data['payment_methods'];
		$payment = isset($this->request->post['payment_method']) ? $this->request->post['payment_method'] : $this->session->data['payment_method']['id'];

		//balance handling
		$balance_def_currency = $this->customer->getBalance();
		$balance = $this->currency->convert($balance_def_currency,$this->config->get('config_currency'),$this->session->data['currency']);
		if($balance!=0 || ($balance==0 && $this->config->get('config_zero_customer_balance')) && (float)$this->session->data['used_balance']!=0){
			if((float)$this->session->data['used_balance']==0 && $balance>0){
				$this->data['apply_balance_button'] = $this->html->buildButton(array('name' => 'apply_balance',
																					'href' => $this->html->getSecureURL($payment_rt,'&mode=edit&balance=apply',true),
																					'text' => $this->language->get('button_apply_balance'),
																					'icon' => 'fa fa-money',
																					'style'=>'btn-default'));
			}elseif((float)$this->session->data['used_balance']>0){

				$this->data['apply_balance_button'] = $this->html->buildButton(array('name' => 'apply_balance',
																					'href' => $this->html->getSecureURL($payment_rt,'&mode=edit&balance=disapply',true),
																					'text' => $this->language->get('button_disapply_balance'),
																					'icon' => 'fa fa-times',
																					'style'=>'btn-default'));
			}

			$this->data['balance'] = $this->language->get('text_balance_checkout').' '.$this->currency->format($balance,$this->session->data['currency'],1);
			if((float)$this->session->data['used_balance']>0){
				$this->data['balance'] .=  ' ('.$this->currency->format($balance_def_currency-(float)$this->session->data['used_balance']).')';
				$this->data['balance'] .=  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$this->currency->format((float)$this->session->data['used_balance']).' '.$this->language->get('text_applied_balance');
			}
		}

		if($this->data['payment_methods']){
			foreach($this->data['payment_methods'] as $k=>$v){
				//check if we have only one method and select by default if was selected before
				$selected = FALSE;
				if ( count($this->data['payment_methods']) == 1) {
					$selected = TRUE;
				} else if( $payment == $v['id'] )  {
					$selected = TRUE;
				}	
			
				$this->data['payment_methods'][$k]['radio'] = $form->getFieldHtml( array(
					                                                                   'type' => 'radio',
					                                                                   'name' => 'payment_method',
					                                                                   'options' => array($v['id']=>''),
					                                                                   'value' => $selected,
				                                                                  ));
			}
		}else{
			$this->data['payment_methods'] = array();
		}

		$this->data['comment'] = isset($this->request->post['comment']) ? $this->request->post['comment'] : $this->session->data['comment'];
		$this->data['form']['comment'] =  $form->getFieldHtml( array(
																	'type' => 'textarea',
																	'name' => 'comment',
																	'value' => $this->data['comment'],
																	'attr' => ' rows="3" style="width: 99%" ' ));

		if ($this->config->get('config_checkout_id')) {
			$this->loadModel('catalog/content');
			$content_info = $this->model_catalog_content->getContent($this->config->get('config_checkout_id'));
			if ($content_info) {
				$this->data['text_agree'] = $this->language->get('text_agree');
				$this->data['text_agree_href'] = $this->html->getURL('r/content/content/loadInfo', '&content_id=' . $this->config->get('config_checkout_id'),true);
				$this->data['text_agree_href_text'] = $content_info['title'];
			} else {
				$this->data['text_agree'] = '';
			}
		} else {
			$this->data['text_agree'] = '';
		}

		if($this->data['text_agree']){
			$this->data['form']['agree'] = $form->getFieldHtml( array(
					                                                  'type' => 'checkbox',
					                                                  'name' => 'agree',
				                                                      'value' => '1',
					                                                  'checked' => ( $this->request->post['agree'] ? TRUE : FALSE )
				                                                      ));
		}

		$this->data['agree'] = $this->request->post['agree'];
		$this->data['back'] = $this->cart->hasShipping() ? $this->html->getSecureURL($checkout_rt) : $this->data['back'] = $this->html->getSecureURL($cart_rt);

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
			$this->view->assign('buttons', $this->view->fetch('embed/checkout/payment.buttons.tpl'));	
			//load special headers
	        $this->addChild('responses/embed/head', 'head');
	        $this->addChild('responses/embed/footer', 'footer');
		    $this->processTemplate('embed/checkout/payment.tpl');
		} else {
			$this->view->assign('buttons', $this->view->fetch('pages/checkout/payment.buttons.tpl'));	
			$this->processTemplate('pages/checkout/payment.tpl');
		}

		//update data before render
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

	}

	private function _validate() {
		if($this->cart->getFinalTotal()){
			if (!isset($this->request->post['payment_method']) ) {
				$this->error['warning'] = $this->language->get('error_payment');
				return FALSE;
			} else {
				if (!isset($this->session->data['payment_methods'][ $this->request->post['payment_method'] ])) {
					$this->error['warning'] = $this->language->get('error_payment');
					return FALSE;
				}
			}
		}

		if ($this->config->get('config_checkout_id')) {
			$this->loadModel('catalog/content');

			$content_info = $this->model_catalog_content->getContent($this->config->get('config_checkout_id'));

			if ($content_info) {
				if (!isset($this->request->post['agree'])) {
					$this->error['warning'] = sprintf($this->language->get('error_agree'), $content_info['title']);
					return FALSE;
				}
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

	private function _validateCoupon() {
		$promotion = new APromotion();
		$coupon = $promotion->getCouponData($this->request->post['coupon']);
		if (!$coupon) {
			$this->error['warning'] = $this->language->get('error_coupon');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
