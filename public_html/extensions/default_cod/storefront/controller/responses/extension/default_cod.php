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

class ControllerResponsesExtensionDefaultCod extends AController {
	public function main() {

		$item = HtmlElementFactory::create( array( 'type' => 'button',
		                                          'name' => 'back',
			                                      'style' => 'button',
		                                          'text' => $this->language->get('button_back') ));
		$this->view->assign('button_back', $item);

		$item = HtmlElementFactory::create( array( 'type' => 'button',
		                                          'name' => 'checkout',
			                                      'style' => 'button btn-primary',
		                                          'text' => $this->language->get('button_confirm') ));
		$this->view->assign('button_confirm', $item);

		$this->view->assign('continue', $this->html->getSecureURL('checkout/success'));

		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$this->view->assign('back', $this->html->getSecureURL('checkout/payment','&mode=edit',true)); // to prevent coming back when payment method only one
		} else {
			$this->view->assign('back', $this->html->getSecureURL('checkout/guest_step_2'));
		}
		
		$this->processTemplate('responses/default_cod.tpl' );
	}

	public function api() {
		$data = array();
		
		$data['text_note'] = $this->language->get('text_note');
		$data['process_rt'] = 'default_cod/api_confirm';		

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($data));
	}


	public function api_confirm() {
		$data = array();
			
		$this->confirm();
		$data[ 'success' ] = 'completed';

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($data));
	}
	
	public function confirm() {
		$this->load->model('checkout/order');
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('default_cod_order_status_id'));
	}
}
