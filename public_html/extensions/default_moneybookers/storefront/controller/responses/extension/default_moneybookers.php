<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ControllerResponsesExtensionDefaultMoneybookers extends AController {
	public function main() {
		$this->load->model('checkout/order');
		$this->loadLanguage('default_moneybookers/default_moneybookers');

		$form = new AForm();
		$form->setForm(array( 'form_name' => 'checkout' ));
		$data[ 'form' ][ 'form_open' ] = $form->getFieldHtml(array( 'type' => 'form',
		                                                            'name' => 'checkout',
		                                                            'action' => 'https://www.moneybookers.com/app/payment.pl?rid=10111486' ));

		$data[ 'form' ][ 'pay_to_email' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                            'name' => 'pay_to_email',
		                                                            'value' => $this->config->get('default_moneybookers_email'),
		                                                       ));
		$data[ 'form' ][ 'recipient_description' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                            'name' => 'recipient_description',
		                                                            'value' => $this->config->get('store_name'),
		                                                       ));
		$data[ 'form' ][ 'transaction_id' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                            'name' => 'transaction_id',
		                                                            'value' => $this->session->data['order_id'],
		                                                       ));
		$data[ 'form' ][ 'return_url' ] = $form->getFieldHtml(array( 'type' => 'hidden',
		                                                            'name' => 'return_url',
		                                                            'value' => $this->html->getSecureURL('checkout/success'),
		                                                       ));

		$back = $this->request->get[ 'rt' ] != 'checkout/guest_step_3' ? $this->html->getSecureURL('checkout/payment')
				: $this->html->getSecureURL('checkout/guest_step_2');
		$data[ 'form' ][ 'cancel_url' ] = $form->getFieldHtml(array( 'type' => 'hidden',
				                                                     'name' => 'cancel_url',
				                                                     'value' => $back,
				                                                    ));
		$data[ 'form' ][ 'status_url' ] = $form->getFieldHtml(array( 'type' => 'hidden',
				                                                     'name' => 'status_url',
				                                                     'value' => $this->html->getSecureURL('extension/moneybookers/callback'),
				                                                    ));
		$data[ 'form' ][ 'language' ] = $form->getFieldHtml(array( 'type' => 'hidden',
				                                                   'name' => 'language',
				                                                   'value' => $this->session->data['language'],
				                                                    ));

		if ( is_file( DIR_RESOURCE . $this->config->get('config_logo')) ) {
            $logo = HTTP_DIR_RESOURCE. $this->config->get('config_logo');
        } else {
            $logo = $this->config->get('config_logo');
        }
		$data[ 'form' ][ 'logo_url' ] = $form->getFieldHtml(array( 'type' => 'hidden',
				                                                   'name' => 'logo_url',
				                                                   'value' => $logo,
				                                                    ));
		/* * */
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data[ 'form' ][ 'pay_from_email' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                       'name' => 'pay_from_email',
                                                                       'value' => $order_info[ 'email' ],
                                                                  ));
		$data[ 'form' ][ 'firstname' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                       'name' => 'firstname',
                                                                       'value' => $order_info['payment_firstname'],
                                                                  ));
		$data[ 'form' ][ 'lastname' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                       'name' => 'lastname',
                                                                       'value' => $order_info['payment_lastname'],
                                                                  ));
		$data[ 'form' ][ 'address' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                       'name' => 'address',
                                                                       'value' => $order_info['payment_address_1'],
                                                                  ));
		$data[ 'form' ][ 'address2' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                       'name' => 'address2',
                                                                       'value' => $order_info['payment_address_2'],
                                                                  ));
		$data[ 'form' ][ 'phone_number' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                       'name' => 'phone_number',
                                                                       'value' => $order_info['telephone'],
                                                                  ));
		$data[ 'form' ][ 'postal_code' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                       'name' => 'postal_code',
                                                                       'value' => $order_info['payment_postcode'],
                                                                  ));
		$data[ 'form' ][ 'city' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                       'name' => 'city',
                                                                       'value' => $order_info['payment_city'],
                                                                  ));
		$data[ 'form' ][ 'state' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                       'name' => 'state',
                                                                       'value' => $order_info['payment_zone'],
                                                                  ));
		$data[ 'form' ][ 'country' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                       'name' => 'country',
                                                                       'value' => $order_info['payment_iso_code_3'],
                                                                  ));

        $data[ 'form' ][ 'amount' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                       'name' => 'amount',
                                                                       'value' => $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE),
                                                                  ));

        $data[ 'form' ][ 'currency' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                   'name' => 'currency',
                                                                   'value' => $order_info['currency'],
                                                                  ));
		$products = '';
		foreach ($this->cart->getProducts() as $product) {
    		$products .= $product['quantity'] . ' x ' . $product['name'] . ', ';
    	}
		$data[ 'form' ][ 'detail1_text' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                   'name' => 'detail1_text',
                                                                   'value' => $products,
                                                                  ));
		$data[ 'form' ][ 'merchant_fields' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                   'name' => 'merchant_fields',
                                                                   'value' => 'order_id',
                                                                  ));


		$this->load->library('encryption');
		$encryption = new AEncryption($this->config->get('encryption_key'));
		$data[ 'form' ][ 'order_id' ] = $form->getFieldHtml(array( 'type' => 'hidden',
                                                                   'name' => 'order_id',
                                                                   'value' => $encryption->encrypt($this->session->data['order_id']),
                                                                  ));

		$data[ 'form' ][ 'back' ] = $form->getFieldHtml(array( 'type' => 'button',
		                                                     'name' => 'back',
		                                                     'text' => $this->language->get('button_back'),
		                                                     'style' => 'button',
		                                                     'href' => $back ));
		$data[ 'form' ][ 'submit' ] = $form->getFieldHtml(array( 'type' => 'submit',
		                                                       'name' => $this->language->get('button_confirm')
		                                                  ));
		$this->view->batchAssign( $data );
		$this->processTemplate('responses/default_moneybookers.tpl' );
	}
	
	public function callback() {
		$this->load->library('encryption');
		
		$encryption = new AEncryption($this->config->get('encryption_key'));
		
		if (isset($this->request->post['order_id'])) {
			$order_id = $encryption->decrypt($this->request->post['order_id']);
		} else {
			$order_id = 0;
		}
		
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if ($order_info) {
			$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
			
			switch($this->request->post['status']) {
				case '2':
					$this->model_checkout_order->update($order_id, $this->config->get('default_moneybookers_order_status_id'), '', TRUE);
					break;
				case '0':	
					$this->model_checkout_order->update($order_id, $this->config->get('default_moneybookers_order_status_pending_id'), '', TRUE);		
					break;
				case '-1':
					$this->model_checkout_order->update($order_id, $this->config->get('default_moneybookers_order_status_canceled_id'), '', TRUE);
					break;
				case '-2':
					$this->model_checkout_order->update($order_id, $this->config->get('default_moneybookers_order_status_failed_id'), '', TRUE);
					break;					
				case '-3':
					$this->model_checkout_order->update($order_id, $this->config->get('default_moneybookers_order_status_chargeback_id'), '', TRUE);
					break;
			}
		}
	}
}
?>