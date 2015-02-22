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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}

class ControllerResponsesExtensionDefaultRealex extends AController {

	public function void() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('default_realex/default_realex');
		$json = array();

		if (has_value($this->request->post['order_id'])) {
			$order_id = $this->request->post['order_id'];
			$this->loadModel('extension/default_realex');

			$realex_order = $this->model_extension_default_realex->getRealexOrder($order_id);
			$v_response = $this->model_extension_default_realex->void($order_id);

			if (isset($v_response->result) && $v_response->result == '00') {
				$this->model_extension_default_realex->addTransaction($realex_order['realex_order_id'], 'void', 0.00);
				$this->model_extension_default_realex->updateVoidStatus($realex_order['realex_order_id'], 1);

				$json['msg'] = $this->language->get('text_voided');
				$json['data'] = array();
				$json['data']['date_added'] = date("Y-m-d H:i:s");
				$json['error'] = false;

				// update main order status
				$this->loadModel('sale/order');
				$this->model_sale_order->addOrderHistory($order_id, array(
				    'order_status_id' => $this->config->get('default_realex_status_void'),
				    'notify' => 0,
					'append' => 1,
				    'comment' => $this->language->get('text_voided')
				));

			} else {
				$json['error'] = true;
				$json['msg'] = has_value($v_response->message) ? (string)$v_response->message : 'Unable to void';
			}
		} else {
			$json['error'] = true;
			$json['msg'] = 'Missing data';
		}

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}

	public function capture() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('default_realex/default_realex');
		$json = array();

		if (has_value($this->request->post['order_id']) && $this->request->post['amount'] > 0) {
			$order_id = $this->request->post['order_id'];
			$amount = $this->request->post['amount'];
			$this->loadModel('extension/default_realex');
			
			$realex_order = $this->model_extension_default_realex->getRealexOrder($order_id);

			$c_response = $this->model_extension_default_realex->capture($order_id, $amount);

			if (isset($c_response->result) && $c_response->result == '00') {
				$this->model_extension_default_realex->addTransaction($realex_order['realex_order_id'], 'payment', $amount);
				$total_captured = $this->model_extension_default_realex->getTotalCaptured($realex_order['realex_order_id']);

				if ($total_captured >= $realex_order['total'] || $realex_order['settle_type'] == 0) {
					$this->model_extension_default_realex->updateCaptureStatus($realex_order['realex_order_id'], 1);
					$capture_status = 1;
					$json['msg'] = $this->language->get('text_captured_order');
				} else {
					$capture_status = 0;
					$json['msg'] = $this->language->get('text_captured_ok');
				}

				$this->model_extension_default_realex->updateForRebate($realex_order['realex_order_id'], $c_response->pasref, $c_response->orderid);

				$json['data'] = array();
				$json['data']['date_added'] = date("Y-m-d H:i:s");
				$json['data']['amount'] = (float)$amount;
				$json['data']['capture_status'] = $capture_status;
				$json['data']['total'] = (float)$total_captured;
				$json['data']['total_formatted'] = $this->currency->format($total_captured, $realex_order['currency_code'], 1, true);
				$json['error'] = false;
			} else {
				$json['error'] = true;
				$json['msg'] = has_value($c_response->message) ? (string)$c_response->message : 'Unable to capture';

			}
		} else {
			$json['error'] = true;
			$json['msg'] = 'Missing data';
		}

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}

	public function rebate() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('default_realex/default_realex');
		$json = array();

		if (has_value($this->request->post['order_id']) && $this->request->post['amount'] > 0) {
			$order_id = $this->request->post['order_id'];
			$amount = $this->request->post['amount'];
			$this->loadModel('extension/default_realex');

			$realex_order = $this->model_extension_default_realex->getRealexOrder($order_id);

			$r_response = $this->model_extension_default_realex->rebate($order_id, $amount);

			if (isset($r_response->result) && $r_response->result == '00') {
				$this->model_extension_default_realex->addTransaction($realex_order['realex_order_id'], 'rebate', $amount*-1);

				$total_rebated = $this->model_extension_default_realex->getTotalRebated($realex_order['realex_order_id']);
				$total_captured = $this->model_extension_default_realex->getTotalCaptured($realex_order['realex_order_id']);

				if ($total_captured <= 0 && $realex_order['capture_status'] == 1) {
					$this->model_extension_default_realex->updateRebateStatus($realex_order['realex_order_id'], 1);					
					$rebate_status = 1;
					$json['msg'] = $this->language->get('text_rebated_order');
				} else {
					$rebate_status = 0;
					$json['msg'] = $this->language->get('text_rebated_ok');
					// update main order status
					$this->loadModel('sale/order');
					$this->model_sale_order->addOrderHistory($order_id, array(
						'order_status_id' => $this->config->get('default_realex_status_rebate'),
						'notify' => 0,
						'append' => 1,
						'comment' => $amount. ' rebated.'
					));
				}

				$json['data'] = array();
				$json['data']['date_added'] = date("Y-m-d H:i:s");
				$json['data']['amount'] = $this->request->post['amount'] * -1;
				$json['data']['total_captured'] = (float)$total_captured;
				$json['data']['total_rebated'] = (float)$total_rebated;
				$json['data']['rebate_status'] = $rebate_status;
				$json['error'] = false;
			} else {
				$json['error'] = true;
				$json['msg'] = has_value($r_response->message) ? (string)$r_response->message : 'Unable to rebate';
			}
		} else {
			$json['error'] = true;
			$json['msg'] = 'Missing data';
		}

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}

}