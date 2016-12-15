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
if (!defined('DIR_CORE')){
	header('Location: static_pages/');
}

class ControllerPagesCheckoutSuccess extends AController{
	public $data = array ();

	public function main(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (isset($this->session->data['order_id'])){
			
			// in default currency
			$amount = $this->session->data['used_balance']; 
			if ($amount){
				$transaction_data = array (
						'order_id'         => (int)$this->session->data['order_id'],
						'amount'           => $amount,
						'transaction_type' => 'order',
						'created_by'       => $this->customer->getId(),
						'description'      => sprintf($this->language->get('text_applied_balance_to_order'),
								$this->currency->format($this->currency->convert($amount, $this->config->get('config_currency'), $this->session->data['currency']), $this->session->data['currency'], 1),
								(int)$this->session->data['order_id']));
				try{
					$this->customer->debitTransaction($transaction_data);
				}catch(AException $e){
					$error = new AError('Error: Debit transaction cannot be applied.'. var_export($transaction_data, true)."\n".$e->getMessage()."\n".$e->getFile());
					$error->toLog()->toMessages();
				}
			}

			$this->cart->clear();
			$this->customer->clearCustomerCart();

			//save order_id into session as processed order to allow one redirect
			$this->session->data['processed_order_id'] = (int)$this->session->data['order_id'];

			unset($this->session->data['shipping_method'],
					$this->session->data['shipping_methods'],
					$this->session->data['payment_method'],
					$this->session->data['payment_methods'],
					$this->session->data['guest'],
					$this->session->data['comment'],
					$this->session->data['order_id'],
					$this->session->data['coupon'],
					$this->session->data['used_balance'],
					$this->session->data['used_balance_full']);

			$this->extensions->hk_ProcessData($this);

			//Redirect back to load new page with cleared shopping cart content
			redirect($this->html->getSecureURL('checkout/success'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb(array (
				'href'      => $this->html->getHomeURL(),
				'text'      => $this->language->get('text_home'),
				'separator' => false
		));

		$this->document->addBreadcrumb(array (
				'href'      => $this->html->getSecureURL('checkout/cart'),
				'text'      => $this->language->get('text_basket'),
				'separator' => $this->language->get('text_separator')
		));

		if ($this->customer->isLogged()){
			$this->document->addBreadcrumb(array (
					'href'      => $this->html->getSecureURL('checkout/shipping'),
					'text'      => $this->language->get('text_shipping'),
					'separator' => $this->language->get('text_separator')
			));

			$this->document->addBreadcrumb(array (
					'href'      => $this->html->getSecureURL('checkout/payment'),
					'text'      => $this->language->get('text_payment'),
					'separator' => $this->language->get('text_separator')
			));

			$this->document->addBreadcrumb(array (
					'href'      => $this->html->getSecureURL('checkout/confirm'),
					'text'      => $this->language->get('text_confirm'),
					'separator' => $this->language->get('text_separator')
			));
		} else{
			$this->document->addBreadcrumb(array (
					'href'      => $this->html->getSecureURL('checkout/guest'),
					'text'      => $this->language->get('text_guest'),
					'separator' => $this->language->get('text_separator')
			));

			$this->document->addBreadcrumb(array (
					'href'      => $this->html->getSecureURL('checkout/guest/confirm'),
					'text'      => $this->language->get('text_confirm'),
					'separator' => $this->language->get('text_separator')
			));
		}

		$this->document->addBreadcrumb(array (
				'href'      => $this->html->getURL('checkout/success'),
				'text'      => $this->language->get('text_success'),
				'separator' => $this->language->get('text_separator')
		));

		$this->view->assign('heading_title', $this->language->get('heading_title'));

		$order_id = $this->session->data['processed_order_id'];
		//only one time load, reset
		unset($this->session->data['processed_order_id']);
		if(!$order_id) {
			redirect($this->html->getURL('index/home'));
		}

		$this->loadModel('account/order');
		$order_info = $this->model_account_order->getOrder($order_id);
		if($order_info){
			$order_info['order_products'] = $this->model_account_order->getOrderProducts($order_id);
		}

		$order_totals = $this->model_account_order->getOrderTotals($order_id);
		$this->_google_analytics($order_info, $order_totals);
			
		if ($this->session->data['account'] == 'guest'){
			//give link on order page for quest
			$enc = new AEncryption($this->config->get('encryption_key'));
			$order_token = $enc->encrypt($order_id.'::'.$order_info['email']);
			$order_url = $this->html->getSecureURL('account/invoice', '&ot=' . $order_token);
			$this->view->assign('text_message',
					sprintf($this->language->get('text_message_guest'), $order_url, $this->html->getURL('content/contact'))
			);
		} else {
			$text_message = sprintf($this->language->get('text_message_account'),
										$order_id,
										$this->html->getSecureURL('account/invoice','&order_id='.$order_id),
										$this->html->getURL('content/contact'));

			$this->view->assign( 'text_message', $text_message );
		}
		$this->view->assign('button_continue', $this->language->get('button_continue'));
		$this->view->assign('continue', $this->html->getHomeURL());
		$continue = $this->html->buildElement(
				array (
						'type'  => 'button',
						'name'  => 'continue_button',
						'text'  => $this->language->get('button_continue'),
						'style' => 'button'));
		$this->view->assign('continue_button', $continue);

		if ($this->config->get('embed_mode') == true){
			//load special headers
			$this->addChild('responses/embed/head', 'head');
			$this->addChild('responses/embed/footer', 'footer');
			$this->processTemplate('embed/common/success.tpl');
		} else{
			$this->processTemplate('common/success.tpl');
		}

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
	
	private function _google_analytics( $order_data, $order_totals ){

		// google analytics data for js-script in footer.tpl
		$order_tax = $order_total = $order_shipping = 0.0;
		foreach ($order_totals as $i => $total){
		    if ($total['type'] == 'total'){
		    	$order_total += $total['value'];
		    } elseif ($total['type'] == 'tax'){
		    	$order_tax += $total['value'];
		    } elseif ($total['type'] == 'shipping'){
		    	$order_shipping += $total['value'];
		    }
		}

		if (!$order_data['shipping_city']) {
			$addr = array(
				'city'           => $order_data['payment_city'],
				'state'          => $order_data['payment_zone'],
				'country'        => $order_data['payment_country']
			);					
		} else {
			$addr = array(
				'city'           => $order_data['shipping_city'],
				'state'          => $order_data['shipping_zone'],
				'country'        => $order_data['shipping_country']
			);		
		}

		$ga_data =  array_merge (
				    		array ('transaction_id' => (int)$order_data['order_id'],
				    		   'store_name'     => $this->config->get('store_name'),
				    		   'currency_code'  => $order_data['currency'],
				    		   'total'          => $this->currency->format_number($order_total),
				    		   'tax'            => $this->currency->format_number($order_tax),
				    		   'shipping'       => $this->currency->format_number($order_shipping)
				    		   ), $addr );

		if($order_data['order_products']){
			$ga_data['items'] = array();
			foreach($order_data['order_products'] as $product){
				$ga_data['items'][] = array(
						'id' => (int)$order_data['order_id'],
						'name' => $product['name'],
					// TODO: needs to add sku into order_products table in db
						'sku' => $product['model'],
						'price' => $product['price'],
						'quantity' => $product['quantity']
				);
			}
		}
		
		$this->registry->set('google_analytics_data', $ga_data);
	}
		
}