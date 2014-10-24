<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>
  
 UPGRADE NOTE: 
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.  
------------------------------------------------------------------------------*/

class ControllerResponsesExtensionDefaultSagepayUS extends AController {
	public function main() {
    	$this->loadLanguage('default_sagepay_us/default_sagepay_us');

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data[ 'cc_owner' ] = HtmlElementFactory::create(array(
					'type' => 'input',
					'name' => 'cc_owner',
					'value' => $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'],
					'style' => 'input-medium'
				));

        $data[ 'cc_number' ] = HtmlElementFactory::create(array(
			'type' => 'input',
			'name' => 'cc_number',
			'value' => '',
			'style' => 'input-medium'
		));


		$months = array();
		for ($i = 1; $i <= 12; $i++) {
			$months[ sprintf('%02d', $i) ] = strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
		}
		$data[ 'cc_expire_date_month' ] = HtmlElementFactory::create(
			array( 'type' => 'selectbox',
			     'name' => 'cc_expire_date_month',
			     'value' => sprintf('%02d', date('m')),
			     'options' => $months,
			     'style' => 'short input-small'
			));

        $today = getdate();
		$years = array();
		for ($i = $today[ 'year' ]; $i < $today[ 'year' ] + 11; $i++) {
			$years[ strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) ] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
		}
		$data[ 'cc_expire_date_year' ] = HtmlElementFactory::create(array( 'type' => 'selectbox',
		                                                                 'name' => 'cc_expire_date_year',
		                                                                 'value' => sprintf('%02d', date('Y') + 1),
		                                                                 'options' => $years,
		                                                                 'style' => 'short input-small' ));



        $data[ 'cc_cvv2' ] = HtmlElementFactory::create(array( 'type' => 'input',
		                                                     'name' => 'cc_cvv2',
		                                                     'value' => '',
		                                                     'style' => 'short',
		                                                     'attr' => ' size="3" '
		                                                ));


		$back = $this->request->get[ 'rt' ] != 'checkout/guest_step_3' ? $this->html->getSecureURL('checkout/payment')
				: $this->html->getSecureURL('checkout/guest_step_2');
		$data[ 'back' ] = HtmlElementFactory::create(array( 'type' => 'button',
		                                                  'name' => 'back',
		                                                  'text' => $this->language->get('button_back'),
		                                                  'style' => 'button',
		                                                  'href' => $back ));

		$data[ 'submit' ] = HtmlElementFactory::create(array( 'type' => 'button',
			                                                  'name' => 'sp_button',
		                                                      'text' => $this->language->get('button_confirm'),
			                                                  'style' => 'button btn-orange',
		                                               ));


		$this->view->batchAssign( $data );
		$this->processTemplate('responses/default_sagepay_us.tpl' );
	}
	
	public function send() {
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$url = 'https://www.sagepayments.net/cgi-bin/eftbankcard.dll?transaction';
		
		$data  = 'm_id=' . $this->config->get('default_sagepay_us_merchant_id');
		$data .= '&m_key=' . $this->config->get('default_sagepay_us_merchant_key');
		$data .= '&T_amt=' . urlencode($this->currency->format($order_info['total'], $order_info['currency'], 1.00000, FALSE));
		$data .= '&T_ordernum=' . $this->session->data['order_id'];
		$data .= '&C_name=' . urlencode($this->request->post['cc_owner']);
		$data .= '&C_address=' . urlencode($order_info['payment_address_1']);
		$data .= '&C_state=' . urlencode($order_info['payment_zone']);
		$data .= '&C_city=' . urlencode($order_info['payment_city']);
		$data .= '&C_cardnumber=' . urlencode($this->request->post['cc_number']);
		$data .= '&C_exp=' . urlencode($this->request->post['cc_expire_date_month'] . substr($this->request->post['cc_expire_date_year'], '2'));
		$data .= '&C_cvv=' . urlencode($this->request->post['cc_cvv2']);
		$data .= '&C_zip=' . urlencode($order_info['payment_postcode']);
		$data .= '&C_email=' . urlencode($order_info['email']);
		$data .= '&T_code=02';
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);
		
		curl_close($ch);

		$json = array();
															
		if ($response[1] == 'A') {
			$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));

			$message  = 'Approval Indicator: ' . $response[1] . "\n";
			$message .= 'Approval/Error Code: ' . substr($response, 2, 6) . "\n";
			$message .= 'Approval/Error Message: ' . substr($response, 8, 32) . "\n";
			$message .= 'Front-End Indicator: ' . substr($response, 40, 2) . "\n";
			$message .= 'CVV Indicator: ' . $response[42] . "\n";
			$message .= 'AVS Indicator: ' . $response[43] . "\n";
			$message .= 'Risk Indicator: ' . substr($response, 44, 2) . "\n";
			$message .= 'Reference: ' . substr($response, 46, 10) . "\n";
			$message .= 'Order Number: ' . substr($response, strpos($response, chr(28)) + 1, strrpos($response, chr(28) - 1)) . "\n";
			
			$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('default_sagepay_us_order_status_id'), $message, FALSE);

			$json['success'] = $this->html->getSecureURL('checkout/success');
		} else {
			$json['error'] = substr($response, 8, 32);
		}

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}	
}
