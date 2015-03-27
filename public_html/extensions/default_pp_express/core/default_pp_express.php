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

class ExtensionDefaultPPExpress extends Extension {

	public function onControllerPagesExtensionExtensions_validateData(){
		$that = $this->baseObject;
		if(has_value($that->request->post['default_pp_express_custom_bg_color'])){
			$that->request->post['default_pp_express_custom_bg_color'] = ltrim($that->request->post['default_pp_express_custom_bg_color'], '#');
			$is_valid = $this->_check_valid_colorhex($that->request->post['default_pp_express_custom_bg_color']);
			if(!$is_valid){
				$that->loadLanguage('default_pp_express/default_pp_express');
				$that->error['warning'] = $that->language->get('default_pp_express_error_bg_color');
			}
		}
	}

	public function onControllerResponsesListingGridExtension_InitData(){
		if($this->baseObject_method!='update'){ return null;}
		$that = $this->baseObject;
		if( $that->request->get['id'] != 'default_pp_express' ){ return false; }
		if( !has_value($that->request->post['default_pp_express_custom_bg_color']) ){ return false; }

		$that->request->post['default_pp_express_custom_bg_color'] = ltrim($that->request->post['default_pp_express_custom_bg_color'],'#');
		$is_valid = $this->_check_valid_colorhex($that->request->post['default_pp_express_custom_bg_color']);
		if(!$is_valid){
			$that->loadLanguage('default_pp_express/default_pp_express');
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => $that->language->get('default_pp_express_error_bg_color'),
					'reset_value' => false
				));
		}
	}

	private function _check_valid_colorhex($colorCode) {
	    // If user accidentally passed along the # sign, strip it off
	    $colorCode = ltrim($colorCode, '#');

	    if ( ctype_xdigit($colorCode) &&
	          (strlen($colorCode) == 6 || strlen($colorCode) == 3)){
		    return true;
	    }else{
		    return false;
	    }
	}

	public function onControllerCommonHead_InitData() {
		if(IS_ADMIN!==true){
			$that = $this->baseObject;
			$that->document->addScript($that->view->templateResource('/javascript/paypal_ajax_buttons.js'));
			// clear ppexp session when currency changed. note: use $_GET!
			if( !has_value( $that->session->data['pp_express_checkout']['currency'] ) ){
				$that->session->data['pp_express_checkout']['currency'] = $that->currency->getCode();
			}

			if($that->session->data['pp_express_checkout']['currency'] != $that->currency->getCode()){
				unset($that->session->data['pp_express_checkout']);
				// rebuild order data if currency changed
				$order = new AOrder( Registry::getInstance() );
				$this->data = $order->buildOrderData( $that->session->data );
				$order->saveOrder();
			}
		}
	}

	public function onControllerPagesCheckoutCart_UpdateData() {
		$that = $this->baseObject;
		if ( $that->data['form']['checkout'] ) {

            $min = $that->config->get("default_pp_express_payment_minimum_total");
            $max = $that->config->get("default_pp_express_payment_maximum_total");
            $amount = $that->cart->getFinalTotal();
            if ( 	(has_value( $min ) && $amount < $min )
                ||  (has_value( $max ) && $amount > $max )
				|| !$that->config->get('config_guest_checkout')) {
                return null;
            }

			$language = $that->language->getCurrentLanguage();
			$locale = explode(',',$language['locale']);
			$data['image_src'] = 'https://www.paypal.com/'.$locale[1].'/i/btn/btn_xpressCheckout.gif';

			$data['href'] = $that->html->getSecureURL('r/extension/default_pp_express/set_pp');
			$data['style'] = 'pull-right';

			if ( $that->config->get('default_pp_express_billmelater') ) {
				$data['billmelater'] = array(
					'image_src' => 'https://www.paypalobjects.com/webstatic/'.$locale[1].'/btn/btn_bml_SM.png',
					'href' => $that->html->getSecureURL('r/extension/default_pp_express/set_pp', '&fundsource=bml'),
					'style' => 'pull-right',
				);
				$data['billmelater_txt'] = array(
					'image_src' => 'https://www.paypalobjects.com/webstatic/'.$locale[1].'/btn/btn_bml_text.png'
				);
			}

			$view = new AView(Registry::getInstance(), 0);
			$view->batchAssign($data);

			$that->view->addHookVar('post_cart_buttons', $view->fetch('blocks/default_pp_express_cart_button.tpl'));

			$data['style'] = 'pull-right ml10';

			$view = new AView(Registry::getInstance(), 0);
			$view->batchAssign($data);

			$that->view->addHookVar('pre_top_cart_buttons', $view->fetch('blocks/default_pp_express_cart_button.tpl'));

		}
	}

	public function onControllerBlocksOrderSummary_UpdateData() {
		$that = $this->baseObject;
        $min = $that->config->get("default_pp_express_payment_minimum_total");
        $max = $that->config->get("default_pp_express_payment_maximum_total");
        $amount = $that->cart->getFinalTotal();
        if ( 	(has_value( $min ) && $amount < $min )
            ||  (has_value( $max ) && $amount > $max )
			|| !$that->config->get('config_guest_checkout')) {
            return null;
        }

		$language = $that->language->getCurrentLanguage();
		$locale = explode(',',$language['locale']);
		$data['image_src'] = 'https://www.paypal.com/'.$locale[1].'/i/btn/btn_xpressCheckout.gif';

		$data['href'] = $that->html->getSecureURL('r/extension/default_pp_express/set_pp');

		if ( $that->config->get('default_pp_express_billmelater') ) {
			$data['billmelater'] = array(
				'image_src' => 'https://www.paypalobjects.com/webstatic/en_US/btn/btn_bml_SM.png',
				'href' => $that->html->getSecureURL('r/extension/default_pp_express/set_pp', '&fundsource=bml'),
				'style' => 'pull-right',
			);
		}

		$view = new AView(Registry::getInstance(), 0);
		$view->batchAssign($data);

		$that->view->addHookVar('post_cart_buttons', '<br/><br/>'. $view->fetch('blocks/default_pp_express_button.tpl'));

	}


	public function onControllerPagesCheckoutPayment_InitData() {
		$that = $this->baseObject;
		if ( $that->request->get['mode'] == 'edit' || has_value($that->request->post['coupon'] )) {
			unset($that->session->data['pp_express_checkout']);
		}
	}

	public function onControllerPagesCheckoutPayment_ProcessData() {
		$that = $this->baseObject;
		if ( $that->session->data[ 'payment_method' ]['id'] == 'default_pp_express' ) {

			// rebuild order data if coupon applied
			$order = new AOrder( Registry::getInstance() );
			$this->data = $order->buildOrderData( $that->session->data );
			$order_id = $order->saveOrder();
			$that->session->data['order_id'] = $order_id;

			$that->loadModel('checkout/order');

			if ( has_value($that->session->data['order_id']) ) {
				$order_info = $that->model_checkout_order->getOrder($that->session->data['order_id']);
			} else if ( $that->cart->hasProducts() && $that->cart->hasStock() && ($amount = $that->cart->getFinalTotal()) ) {
				$order_info = array(
					'total' => $amount,
					'currency' => $that->session->data['currency'],
					'value' => '',
					'discount' => $amount-$that->cart->getSubTotal()
				);
			} else {
				header('Location:' . $that->html->getSecureURL('checkout/cart'));
				die();
			}

			if (!$that->config->get('default_pp_express_test')) {
				$api_endpoint = 'https://api-3t.paypal.com/nvp';
			} else {
				$api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
			}

			if (!$that->config->get('default_pp_express_transaction')) {
				$paymentaction = 'authorization';
			} else {
				$paymentaction = 'sale';
			}

			$payment_data = array(
				'METHOD'         => 'SetExpressCheckout',
				'VERSION'        => '98.0',
				'USER'           => html_entity_decode($that->config->get('default_pp_express_username'), ENT_QUOTES, 'UTF-8'),
				'PWD'            => html_entity_decode($that->config->get('default_pp_express_password'), ENT_QUOTES, 'UTF-8'),
				'SIGNATURE'      => html_entity_decode($that->config->get('default_pp_express_signature'), ENT_QUOTES, 'UTF-8'),
				'PAYMENTREQUEST_0_PAYMENTACTION'  => $paymentaction,
				'PAYMENTREQUEST_0_AMT'            => $that->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE),
				'PAYMENTREQUEST_0_CURRENCYCODE'	  => $order_info['currency'],
				'PAYMENTREQUEST_0_DISCOUNT'	  => $order_info['discount'],
				'RETURNURL'		 => $that->html->getSecureURL('r/extension/default_pp_express/callback'),
				'CANCELURL'		 => $that->request->server['HTTP_REFERER'],
			);
			$that->loadLanguage('default_pp_express/default_pp_express');
			$products_data = $this->_get_products_data(array(
				'currency' => $order_info['currency'],
				'value' => $order_info['value']
			));

			foreach ( $products_data as $key => $product ) {
				$payment_data['L_PAYMENTREQUEST_0_ITEMAMT'] += $product['price'];
				$payment_data['L_PAYMENTREQUEST_0_NAME' . $key] = $product['name'];
				$payment_data['L_PAYMENTREQUEST_0_AMT' . $key] = $product['price'];
				$payment_data['L_PAYMENTREQUEST_0_NUMBER' . $key] = $product['model'];
				$payment_data['L_PAYMENTREQUEST_0_QTY' . $key] = $product['quantity'];
				$payment_data['L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE' . $key] = $product['weight'];
				$payment_data['L_PAYMENTREQUEST_0_ITEMWEGHTUNIT' . $key] = $product['weight_type'];
			}

			if ( has_value($order_info['shipping_method']) ) {
				$payment_data['PAYMENTREQUEST_0_SHIPTONAME']		= $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOSTREET']		= $order_info['shipping_address_1'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOSTREET2']		= $order_info['shipping_address_2'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOCITY']		= $order_info['shipping_city'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOSTATE']		= $order_info['shipping_zone'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOZIP']			= $order_info['shipping_postcode'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE']	= $order_info['shipping_iso_code_2'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOPHONENUM']	= $order_info['telephone'];
			}

			if ( $that->config->get('default_pp_express_credit_cards') ) {
				$payment_data['SOLUTIONTYPE'] = 'Sole';
				$payment_data['LANDINGPAGE'] = 'Billing';
			}

			if ( $that->config->get('default_pp_express_billmelater') && has_value($that->request->get['fundsource']) && strtolower($that->request->get['fundsource']) == 'bml' ) {
				$payment_data['SOLUTIONTYPE'] = 'Sole';
				$payment_data['LANDINGPAGE'] = 'Billing';
				$payment_data['USERSELECTEDFUNDINGSOURCE'] = 'BML';
			}

			if ( has_value($that->config->get('default_pp_express_custom_logo')) ) {
				if ( strpos($that->config->get('default_pp_express_custom_logo'), 'http')===0 ) {
					$custom_logo = $that->config->get('default_pp_express_custom_logo');
				} else {
					$custom_logo = HTTPS_SERVER . $that->config->get('default_pp_express_custom_logo');
				}

				$payment_data['LOGOIMG'] = $custom_logo;
			}

			if ( has_value($that->config->get('default_pp_express_custom_bg_color')) ) {
				$payment_data['CARTBORDERCOLOR'] = $that->config->get('default_pp_express_custom_bg_color');
			}

			$curl = curl_init($api_endpoint);

			curl_setopt($curl, CURLOPT_PORT, 443);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payment_data));

			$response = curl_exec($curl);

			curl_close($curl);

			$ec_settings = $this->_parse_http_query($response);

			if ( isset($ec_settings['TOKEN']) ) {
				if ( !$that->config->get('default_pp_express_test') ) {
					header('Location: https://www.paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($ec_settings['TOKEN']) . '&useraction=commit');
					die();
				} else {
					header('Location: https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($ec_settings['TOKEN']) . '&useraction=commit');
					die();
				}
			} else {
				$warning = new AWarning('PayPal Express Checkout Error: ' . $ec_settings['L_LONGMESSAGE0'] . '. Test mode = ' . $that->config->get('default_pp_express_test') .'.');
				$warning->toLog()->toDebug();
				$that->loadLanguage('default_pp_express/default_pp_express');
				$that->session->data['pp_express_checkout_error'] = $that->language->get('service_error');
				header('Location: ' . $that->html->getSecureURL('extension/default_pp_express/error'));
				die();
			}
		}
	}

	public function onControllerPagesCheckoutGuestStep2_InitData() {
		$error = false;
		$that = $this->baseObject;
		if (isset($that->request->post['shipping_method'])) {
			$shipping = explode('.', $that->request->post['shipping_method']);
			$that->session->data['shipping_method'] = $that->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
		}

		if ( $that->request->get['mode'] == 'edit' ) {
			unset($that->session->data['pp_express_checkout']);
		}
		// if coupon code was submited
		if ( has_value($that->request->post['coupon'] )){
			$promotion = new APromotion();
			$coupon = $promotion->getCouponData($that->request->post['coupon']);
			// rebuild order data if coupon applied
			$order = new AOrder( Registry::getInstance() );
			$this->data = $order->buildOrderData( $that->session->data );
			$order->saveOrder();
			if($coupon){
				$that->session->data['coupon'] = $that->request->post['coupon'];
			}else{
				$error=true;
			}
		}

		if( !$error && $that->request->server['REQUEST_METHOD']=='POST'
			&&
			($that->request->post['payment_method']=='default_pp_express'
					|| $that->session->data[ 'payment_method' ]['id'] == 'default_pp_express')	){
			if(!has_value($that->session->data['pp_express_checkout']['token']) || !has_value($that->session->data['pp_express_checkout']['PayerID'])){
				// rebuild order data if coupon applied
				$order = new AOrder( Registry::getInstance() );
				$this->data = $order->buildOrderData( $that->session->data );
				$order->saveOrder();
				$redirect_url = $that->html->getSecureURL('checkout/guest_step_3');
				header ( 'Location: '.$that->html->getSecureURL('r/extension/default_pp_express/set_pp','&to_confirm=1&redirect_to='.urlencode($redirect_url)));
				exit;
			}
		}
	}

	public function onControllerPagesCheckoutGuestStep2_UpdateData() {
		$that = $this->baseObject;
		if ( has_value($that->session->data['pp_express_checkout']['token'])
				&& $that->request->get['mode'] != 'edit'
			&& $that->session->data['shipping_method']['id']) {
			$data = $that->view->getData();
			$shipping = explode('.',$that->session->data['shipping_method']['id']);

			$that->view->assign('payment_methods',
				array($shipping[0] => array('default_pp_express' => $data['payment_methods'][$shipping[0]]['default_pp_express'])));
		}

		//deprecated part since 1.1.7
		$action = $that->html->getSecureURL('checkout/guest_step_2', ($that->request->get['mode'] ? '&mode=' . $that->request->get['mode'] : ''), true);
		$form = new AForm();
		$form->setForm(array('form_name' => 'coupon'));
		$data = $that->view->getData();

		$data['form0']['form_open'] = $form->getFieldHtml(
			array('type' => 'form',
				'name' => 'coupon',
				'action' => $action));

		$that->view->assign('form0', $data['form0']);
	}

	public function onControllerPagesCheckoutGuestStep2_ProcessData() {
		$that = $this->baseObject;
		if ( $that->session->data[ 'payment_method' ]['id'] == 'default_pp_express' && !has_value($that->session->data['pp_express_checkout']['token']) ) {

			$that->loadModel('checkout/order');

			if ( has_value($that->session->data['order_id']) ) {
				$order_info = $that->model_checkout_order->getOrder($that->session->data['order_id']);
			} else if ( $that->cart->hasProducts() && $that->cart->hasStock() && ($amount = $that->cart->getFinalTotal()) ) {
				$order_info = array(
					'total' => $amount,
					'currency' => $that->session->data['currency'],
					'value' => ''
				);
			} else {
				header('Location:' . $that->html->getSecureURL('checkout/cart'));
				die();
			}

			if (!$that->config->get('default_pp_express_test')) {
				$api_endpoint = 'https://api-3t.paypal.com/nvp';
			} else {
				$api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
			}

			if (!$that->config->get('default_pp_express_transaction')) {
				$paymentaction = 'authorization';
			} else {
				$paymentaction = 'sale';
			}
			$order_total = $that->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
			$payment_data = array(
				'METHOD'         => 'SetExpressCheckout',
				'VERSION'        => '98.0',
				'USER'           => html_entity_decode($that->config->get('default_pp_express_username'), ENT_QUOTES, 'UTF-8'),
				'PWD'            => html_entity_decode($that->config->get('default_pp_express_password'), ENT_QUOTES, 'UTF-8'),
				'SIGNATURE'      => html_entity_decode($that->config->get('default_pp_express_signature'), ENT_QUOTES, 'UTF-8'),
				'PAYMENTREQUEST_0_PAYMENTACTION'  => $paymentaction,
				'PAYMENTREQUEST_0_AMT'            => $order_total,
				'PAYMENTREQUEST_0_CURRENCYCODE'	  => $order_info['currency'],
				'RETURNURL'		 => $that->html->getSecureURL('r/extension/default_pp_express/callback'),
				'CANCELURL'		 => $that->request->server['HTTP_REFERER'],
			);
			$that->loadLanguage('default_pp_express/default_pp_express');
			$products_data = $this->_get_products_data(array(
				'currency' => $order_info['currency'],
				'value' => $order_info['value']
			));

			if(($this->data['items_total'] - $order_total)!==0.0){
				$payment_data['L_PAYMENTREQUEST_0_ITEMAMT'] = $order_total;
				$skip_item_list = true;
			}
			if(!$skip_item_list){
				foreach ( $products_data as $key => $product ) {
					//$payment_data['L_PAYMENTREQUEST_0_ITEMAMT'] += $this->data['items_total'];
					$payment_data['L_PAYMENTREQUEST_0_NAME' . $key] = $product['name'];
					$payment_data['L_PAYMENTREQUEST_0_AMT' . $key] = (float)$product['price'];
					$payment_data['L_PAYMENTREQUEST_0_NUMBER' . $key] = $product['model'];
					$payment_data['L_PAYMENTREQUEST_0_QTY' . $key] = $product['quantity'];
					$payment_data['L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE' . $key] = $product['weight'];
					$payment_data['L_PAYMENTREQUEST_0_ITEMWEGHTUNIT' . $key] = $product['weight_type'];
				}
			}else{
				$payment_data['L_PAYMENTREQUEST_0_NAME0'] = $that->language->get('text_order_total_amount');
				$payment_data['L_PAYMENTREQUEST_0_AMT0'] = $order_total;
				$payment_data['L_PAYMENTREQUEST_0_NUMBER0'] = '';
				$payment_data['L_PAYMENTREQUEST_0_QTY0'] = 1;
				$payment_data['L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE0'] = '';
				$payment_data['L_PAYMENTREQUEST_0_ITEMWEGHTUNIT0'] = '';

			}

			if ( has_value($order_info['shipping_method']) ) {
				$payment_data['PAYMENTREQUEST_0_SHIPTONAME']		= $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOSTREET']		= $order_info['shipping_address_1'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOSTREET2']		= $order_info['shipping_address_2'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOCITY']		= $order_info['shipping_city'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOSTATE']		= $order_info['shipping_zone'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOZIP']			= $order_info['shipping_postcode'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE']	= $order_info['shipping_iso_code_2'];
				$payment_data['PAYMENTREQUEST_0_SHIPTOPHONENUM']	= $order_info['telephone'];
			}

			if ( $that->config->get('default_pp_express_credit_cards') ) {
				$payment_data['SOLUTIONTYPE'] = 'Sole';
				$payment_data['LANDINGPAGE'] = 'Billing';
			}

			if ( $that->config->get('default_pp_express_billmelater') && has_value($that->request->get['fundsource']) && strtolower($that->request->get['fundsource']) == 'bml' ) {
				$payment_data['SOLUTIONTYPE'] = 'Sole';
				$payment_data['LANDINGPAGE'] = 'Billing';
				$payment_data['USERSELECTEDFUNDINGSOURCE'] = 'BML';
			}

			if ( has_value($that->config->get('default_pp_express_custom_logo')) ) {
				if ( strpos($that->config->get('default_pp_express_custom_logo'), 'http')===0 ) {
					$custom_logo = $that->config->get('default_pp_express_custom_logo');
				} else {
					$custom_logo = HTTPS_SERVER . $that->config->get('default_pp_express_custom_logo');
				}

				$payment_data['LOGOIMG'] = $custom_logo;
			}

			if ( has_value($that->config->get('default_pp_express_custom_bg_color')) ) {
				$payment_data['CARTBORDERCOLOR'] = $that->config->get('default_pp_express_custom_bg_color');
			}

			$curl = curl_init($api_endpoint);

			curl_setopt($curl, CURLOPT_PORT, 443);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payment_data));

			$response = curl_exec($curl);

			curl_close($curl);

			$ec_settings = $this->_parse_http_query($response);

			if ( isset($ec_settings['TOKEN']) ) {
				if ( !$that->config->get('default_pp_express_test') ) {
					header('Location: https://www.paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($ec_settings['TOKEN']) . '&useraction=commit');
					die();
				} else {
					header('Location: https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($ec_settings['TOKEN']) . '&useraction=commit');
					die();
				}
			} else {
				$warning = new AWarning('PayPal Express Checkout Error: ' . $ec_settings['L_LONGMESSAGE0'] . '. Test mode = ' . $that->config->get('default_pp_express_test') .'.');
				$warning->toLog()->toDebug()->toMessages();
				$that->loadLanguage('default_pp_express/default_pp_express');
				$that->session->data['pp_express_checkout_error'] = $that->language->get('service_error');
				header('Location: ' . $that->html->getSecureURL('extension/default_pp_express/error'));
				die();
			}

		}
	}

	//ADMIN HOOKS

	//Hook to enable payment details tab in admin
	public function onControllerPagesSaleOrderTabs_UpdateData() {
		$that = $this->baseObject;
		$order_id = $that->data['order_id'];
		//are we logged in and in admin?
	    if ( IS_ADMIN && $that->user->isLogged() ) {
	    	//check if tab is not yet enabled.
	    	if ( in_array('payment_details', $that->data['groups'])) {
	    		return null;
	    	}

	    	$that->data['groups'][] = 'payment_details';
	    	$that->data['link_payment_details'] = $that->html->getSecureURL('sale/order/payment_details', '&order_id=' . $order_id.'&extension=default_pp_express');
			//reload main view data with updated tab
			$that->view->batchAssign( $that->data );
	    }
	}


	public function onControllerPagesSaleOrder_InitData() {
		$this->baseObject->loadLanguage('default_pp_express/default_pp_express');
	}
	public function onControllerPagesSaleOrder_UpdateData() {
		$that = $this->baseObject;

		if ( IS_ADMIN && $that->user->isLogged() && $this->baseObject_method == 'payment_details' && has_value($that->data['order_info']['payment_method_data'] == 'PayPal Express Checkout') ) {

			$payment_method_data = unserialize($that->data['order_info']['payment_method_data']);

			$payment_status = strtolower($payment_method_data['PAYMENTINFO_0_PAYMENTSTATUS']);

			$data = array();

			$data['text_payment_status'] = $that->language->get('text_payment_status');
			$data['payment_status'] = $payment_method_data['PAYMENTINFO_0_PAYMENTSTATUS'];

			if ( has_value($payment_method_data['PAYMENTINFO_0_PENDINGREASON']) ) {
				$data['pending_reason'] = $that->language->get('text_reason') . ' ' . $payment_method_data['PAYMENTINFO_0_PENDINGREASON'] ;
			}

			$data['error_service_unavailable'] = $that->language->get('error_service_unavailable');
			if ( ($payment_status == 'pending' && $payment_method_data['PAYMENTINFO_0_PENDINGREASON'] == 'authorization') || has_value($payment_method_data['captured_transaction_id']) ) {
				// show "capture" form
				$tpl_data = $this->_get_capture_form($data, $payment_method_data);
			} elseif ( $payment_status == 'completed' ) {
				// show "refund" form/button
				$tpl_data = $this->_get_refund_form($data, $payment_method_data);
			} elseif ( $payment_status == 'partially refunded' ) {
				// show "refund" form/button
				$tpl_data = $this->_get_refund_form($data, $payment_method_data);
			}

			if($tpl_data){
				$view = new AView(Registry::getInstance(), 0);
				$view->batchAssign($that->language->getASet('default_pp_express/default_pp_express'));
				$view->batchAssign($tpl_data);
				$that->view->addHookVar('extension_payment_details', $view->fetch('pages/sale/pp_express_payment_details.tpl'));
			}
		}
	}

	private function _get_capture_form($data = array(), $payment_method_data = array()) {
		$that = $this->baseObject;
		$captured_amount = has_value($payment_method_data['captured_amount']) ? (float) $payment_method_data['captured_amount'] : 0;

		if ( $captured_amount < $payment_method_data['PAYMENTINFO_0_AMT'] ) {
			$data['pp_capture_amount'] = $that->html->buildElement(
				array(
					'type' => 'input',
					'name' => 'pp_capture_amount',
					'value' => $payment_method_data['PAYMENTINFO_0_AMT'] - $captured_amount,
					'style' => 'no-save',
					'attr' => 'disabled'
				)
			);
			$data['text_capture_funds'] = $that->language->get('text_capture_funds');
			$data['pp_capture_submit'] = $that->html->buildElement(array(
				'type' => 'button',
				'text' => $that->language->get('text_capture'),
				'name' => 'pp_capture_submit',
				'style' => 'button3'
			));



			$data['pp_capture_action'] = $that->html->getSecureURL(
				'r/extension/default_pp_express/capture',
				'&order_id=' . (int) $that->data['order_info']['order_id'] .
					'&currency=' . $that->data['currency']['code']
			);
		}

		if ( $captured_amount > 0 ) {
			return $this->_get_refund_form($data, $payment_method_data, $captured_amount);
		} else {
			return $data;
		}
	}

	private function _get_refund_form($data = array(), $payment_method_data = array(), $not_refunded = 0 ) {
		$that = $this->baseObject;
		$refunded_amount = has_value($payment_method_data['refunded_amount']) ? (float) $payment_method_data['refunded_amount'] : 0;

		if ( $not_refunded ) {
			$data['add_to_capture'] = true;
			$not_refunded = (float) $not_refunded;
		} else {
			$data['add_to_capture'] = false;
			$not_refunded = (float) $payment_method_data['PAYMENTINFO_0_AMT'];
		}

		if ( $refunded_amount < $not_refunded ) {

			$data['pp_refund_amount'] = $that->html->buildElement(
				array(
					'type'  => 'input',
					'name' => 'pp_refund_amount',
					'value' => $not_refunded - $refunded_amount,
					'style' => 'no-save'
				)
			);
			$data['text_do_paypal_refund'] = $that->language->get('text_do_paypal_refund');
			$data['pp_refund_submit'] = $that->html->buildElement(array(
				'type'  => 'button',
				'text' => $that->language->get('text_refund'),
				'name' => 'pp_refund_submit',
				'style' => 'button3'
			));

			$params = '&order_id=' . (int) $that->data['order_info']['order_id'] .
					  '&currency=' . $that->data['currency']['code'];

			if ( $data['add_to_capture'] ) {
				$params .= '&refund_captured=1';
			}

			$data['pp_refund_action'] = $that->html->getSecureURL(
				'r/extension/default_pp_express/refund',
				$params
			);


		}
		$data['text_already_refunded'] = $that->language->get('text_already_refunded');
		$data['error_wrong_amount'] = $that->language->get('error_wrong_amount');

		if ( (float) $refunded_amount > 0 ) {
			$data['refunded_amount'] = $that->currency->format($refunded_amount, $that->data['currency']['code'], $that->data['order_info']['value']);
		}

		return $data;
	}

	private function _parse_http_query($query) {

		$parts = explode('&', $query);

		$results = array();
		foreach ( $parts as $part ) {
			$item = explode('=', $part);
			$results[$item[0]] = urldecode($item[1]);
		}

		return $results;
	}

	private function _get_products_data($order_info) {
		$that = $this->baseObject;
		$that->load->library('encryption');
		$encryption = new AEncryption($that->config->get('encryption_key'));

		$this->data['products'] = array();
		$this->data['items_total'] = 0.0;
		$products = $that->cart->getProducts();
		foreach ($products as $product) {
			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$filename = $encryption->decrypt($option['value']);
					$value = mb_substr($filename, 0, mb_strrpos($filename, '.'));
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (mb_strlen($value) > 20 ? mb_substr($value, 0, 20) . '..' : $value)
				);
			}

			$price = $that->currency->format($product['price'], $order_info['currency'], $order_info['value'], FALSE);
			$this->data['products'][] = array(
				'name'     => $product['name'],
				'model'    => $product['model'],
				'price'    => $price,
				'quantity' => $product['quantity'],
				'option'   => $option_data,
				'weight'   => $product['weight'],
				'weight_type' => $product['weight_type']
			);
			$this->data['items_total'] += $price*$product['quantity'];
		}


		$this->data['discount_amount_cart'] = 0;
		$totals = $that->cart->buildTotalDisplay();

		foreach($totals['total_data'] as $total){
			if(in_array($total['id'],array('subtotal','total'))){ continue;}
			if(in_array($total['id'],array('promotion','coupon'))){
				$total['value'] = $total['value']<0 ? $total['value']*-1 : $total['value'];
				$this->data['discount_amount_cart'] += $total['value'];
			}else{
				$price = $that->currency->format($total['value'], $order_info['currency'], $order_info['value'], FALSE);
				$this->data['products'][] = array(
					'name'     => $total['title'],
					'model'    => '',
					'price'    => $price,
					'quantity' => 1,
					'option'   => array(),
					'weight'   => 0
				);
				$this->data['items_total'] += $price;
			}
		}


		if($this->data['discount_amount_cart']>0){
			$price = -1*$that->currency->format($this->data['discount_amount_cart'], $order_info['currency'], $order_info['value'], FALSE);
			$this->data['products'][] = array(
				'name'     => $that->language->get('text_discount'),
				'model'    => '',
				'price'    => $price,
				'quantity' => 1,
				'option'   => array(),
				'weight'   => 0
			);
			$this->data['items_total'] += $price;
		}

		return $this->data['products'];
	}

}