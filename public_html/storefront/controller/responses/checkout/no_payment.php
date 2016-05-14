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

class ControllerResponsesCheckoutNoPayment extends AController {
	public $data = array();
	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->data['button_back'] = $this->html->buildElement( array( 'type' => 'button',
		                                          'name' => 'back',
			                                      'style' => 'button mr10',
		                                          'text' => $this->language->get('button_back'),
												  'icon' => 'fa fa-arrow-left'));

		$this->data['button_confirm'] = $this->html->buildElement( array( 'type' => 'button',
		                                          'name' => 'checkout',
			                                      'style' => 'button btn-orange pull-right',
		                                          'text' => $this->language->get('button_confirm'),
												  'icon' => 'fa fa-check'));

		$this->data['continue'] = $this->html->getSecureURL('checkout/success');

		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$this->data['back'] = $this->html->getSecureURL('checkout/cart');
		} else {
			$this->data['back'] = $this->html->getSecureURL('checkout/guest_step_2');
		}

		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/checkout/no_payment.tpl' );
	}

	public function api() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->data['text_note'] = $this->language->get('text_note');
		$this->data['process_rt'] = 'checkout/no_payment/api_confirm';

		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($this->data));
	}


	public function api_confirm() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->confirm();
		$this->data[ 'success' ] = 'completed';

		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($this->data));
	}
	
	public function confirm() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->load->model('checkout/order');
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
