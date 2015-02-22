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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

class ControllerResponsesExtensionDefaultRealex extends AController {

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('default_realex/default_realex');

		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_wait'] = $this->language->get('text_wait');

		$data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$data['cc_owner'] = HtmlElementFactory::create(array( 
			'type' => 'input',
			'name' => 'cc_owner',
			'placeholder' => $this->language->get('entry_cc_owner'),
			'value' => '' ));

		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['cc_number'] = HtmlElementFactory::create(array(
			'type' => 'input',
			'name' => 'cc_number',
			'attr' => 'autocomplete="off"',
			'placeholder' => $this->language->get('entry_cc_number'),
			'value' => '' ));

		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');

		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$data['entry_cc_cvv2_short'] = $this->language->get('entry_cc_cvv2_short');
		$data['cc_cvv2_help_url'] = $this->html->getURL('r/extension/default_realex/cvv2_help');

		$data['cc_cvv2'] = HtmlElementFactory::create(array( 'type' => 'input',
			'name' => 'cc_cvv2',
			'value' => '',
			'style' => 'short',
			'attr' => ' autocomplete="off" ',
		));

		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_back'] = $this->language->get('button_back');

		$months = array();

		for ($i = 1; $i <= 12; $i++) {
			$months[ sprintf('%02d', $i) ] = sprintf('%02d - ', $i) . strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
		}
		$data['cc_expire_date_month'] = HtmlElementFactory::create(
			array( 'type' => 'selectbox',
				'name' => 'cc_expire_date_month',
				'value' => sprintf('%02d', date('m')),
				'options' => $months,
				'style' => 'input-medium short'
			));

		$today = getdate();
		$years = array();
		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$years[ strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) ] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
		}
		$data['cc_expire_date_year'] = HtmlElementFactory::create(array( 'type' => 'selectbox',
			'name' => 'cc_expire_date_year',
			'value' => sprintf('%02d', date('Y') + 1),
			'options' => $years,
			'style' => 'short' ));

		$conf_cc_list = unserialize($this->config->get('default_realex_creditcard_selection'));
		$card_types = array('');
		foreach ($conf_cc_list as $cc_type => $cc) {
			if ($cc['enabled']) {
				$card_types[$cc_type] = $this->language->get('default_realex_creditcard_selection_'.$cc_type);
			}
		}

		$data['entry_cc_type'] = $this->language->get('entry_cc_type');
		$data['cc_type'] = HtmlElementFactory::create(array(
			'type' => 'selectbox',
			'name' => 'cc_type',
			'value' => '',
			'options' => $card_types,
		));

		$back = $this->request->get['rt'] != 'checkout/guest_step_3' ? $this->html->getSecureURL('checkout/payment')
			: $this->html->getSecureURL('checkout/guest_step_2');
		$data['back'] = HtmlElementFactory::create(array( 'type' => 'button',
			'name' => 'back',
			'text' => $this->language->get('button_back'),
			'style' => 'button',
			'href' => $back,
			'icon' => 'icon-arrow-left'));
			
		$data['submit'] = HtmlElementFactory::create(array( 'type' => 'button',
			'name' => 'realex_button',
			'text' => $this->language->get('button_confirm'),
			'style' => 'button btn-orange pull-right',
			'icon' => 'icon-ok icon-white'
		));

		$this->view->batchAssign($data);

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		//load creditcard input validation
		$this->document->addScriptBottom($this->view->templateResource('/javascript/credit_card_validation.js'));

		$this->processTemplate('responses/default_realex.tpl');
	}

	public function api() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('default_realex/default_realex');

		$data['text_credit_card'] = $this->language->get('text_credit_card');

		$data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$data['cc_owner'] = array( 'type' => 'input',
			'name' => 'cc_owner',
			'required' => true,
			'value' => '' );

		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['cc_number'] = array( 'type' => 'input',
			'name' => 'cc_number',
			'attr' => 'autocomplete="off"',
			'required' => true,
			'value' => '' );

		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$data['entry_cc_cvv2_short'] = $this->language->get('entry_cc_cvv2_short');
		$data['cc_cvv2_help_url'] = $this->html->getURL('r/extension/default_realex/cvv2_help');

		$data['cc_cvv2'] = array( 'type' => 'input',
			'name' => 'cc_cvv2',
			'value' => '',
			'style' => 'short',
			'required' => true,
			'attr' => ' autocomplete="off"',
			);
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_back'] = $this->language->get('button_back');

		$months = array();
		for ($i = 1; $i <= 12; $i++) {
			$months[ sprintf('%02d', $i) ] = strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
		}
		$data['cc_expire_date_month'] =
			array( 'type' => 'selectbox',
				'name' => 'cc_expire_date_month',
				'value' => sprintf('%02d', date('m')),
				'options' => $months,
				'required' => true,
				'style' => 'short'
			);

		$today = getdate();
		$years = array();
		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$years[ strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) ] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
		}
		$data['cc_expire_date_year'] = array( 'type' => 'selectbox',
			'name' => 'cc_expire_date_year',
			'value' => sprintf('%02d', date('Y') + 1),
			'options' => $years,
			'required' => true,
			'style' => 'short'
		);

		$conf_cc_list = unserialize($this->config->get('default_realex_creditcard_selection'));
		$card_types = array();
		$default_cc = '';
		foreach ($conf_cc_list as $cc_type => $cc) {
			if ($cc['enabled']) {
				$card_types[$cc_type] = $this->language->get('default_realex_creditcard_selection_'.$cc_type);
			}
			if($cc['default']) {
				$default_cc = $cc_type;
			}
		}

		$data['cc_type'] = HtmlElementFactory::create(array(
			'type' => 'selectbox',
			'name' => 'cc_type',
			'value' => $default_cc,
			'options' => $card_types,
			'required' => true,
		));

		$data['process_rt'] = 'default_realex/send';

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($data));
	}

	public function cvv2_help() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('default_realex/default_realex');

		$image = '<img src="' . $this->view->templateResource('/image/securitycode.jpg') . '" alt="' . $this->language->get('entry_what_cvv2') . '" />';

		$this->view->assign('title', '' );
		$this->view->assign('description', $image );

		//init controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->processTemplate('responses/content/content.tpl' );
	}


	public function send() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		//validate input
		$post = $this->request->post;
		if (empty($post['cc_number'])) {
			$json['error'] = $this->language->get('error_incorrect_number');
		}

		if (empty($post['cc_owner'])) {
			$json['error'] = $this->language->get('error_incorrect_name');
		}

		if (empty($post['cc_expire_date_month']) || empty($post['cc_expire_date_year'])) {
			$json['error'] = $this->language->get('error_incorrect_expiration');
		}

		if (strlen($post['cc_cvv2']) != 3 && strlen($post['cc_cvv2']) != 4) {
			$json['error'] = $this->language->get('error_incorrect_cvv');
		}

		if (isset($json['error'])) {
			$this->load->library('json');
			$this->response->setOutput(AJson::encode($json));
			return null;
		}

		$this->loadModel('checkout/order');
		$this->loadModel('extension/default_realex');
		$this->loadLanguage('default_realex/default_realex');
		$order_id = $this->session->data['order_id'];

		$order_info = $this->model_checkout_order->getOrder($order_id);
		// currency code
		$currency = $this->currency->getCode();
		// order amount without decimal delimiter
		$amount = round($this->currency->convert($this->cart->getFinalTotal(),$this->config->get('config_currency'), $currency), 2)*100 ;
		$cardnumber = preg_replace('/[^0-9]/','',$post['cc_number']);
		$cvv2 = preg_replace('/[^0-9]/','',$post['cc_cvv2']);
		// Card owner name
		$cardname = html_entity_decode($post['cc_owner'], ENT_QUOTES, 'UTF-8');
		$cardtype = $post['cc_type'];
		// card expire date mmyy
		$expdate = $post['cc_expire_date_month'] . substr($post['cc_expire_date_year'], 2, 2);
		$cardissue = $post['cc_issue'];
		$order_ref = $order_id . 'AB' . strftime("%Y%m%d%H%M%S") . mt_rand(1, 999);

		// Mechant id values are provided by Realex Payments, if you have not already received them please contact releax
		$conf_cc_list = unserialize($this->config->get('default_realex_creditcard_selection'));
		$account = $conf_cc_list[$cardtype]['subaccount'];
		if (empty($account) || $accounts[$cardtype]['default']) {
			//use default account by setting it to empty
			$account = '';
		}

		ADebug::checkpoint('Realex Payment: Order ID '.$order_id);
		//do we have 3d on?
		$v3d = array();
		$pd = array(
		    'account' => $account,
		    'amount' => $amount,
		    'currency' => $currency,
		    'order_id' => $order_id,
		    'order_ref' => $order_ref,
		    'cc_number' => $cardnumber,
		    'cc_expire' => $expdate,
		    'cc_owner' => $cardname,
		    'cc_type' => $cardtype,
		    'cc_cvv2' => $cvv2,
		    'cc_issue' => $cardissue
		);

		if ($this->config->get('default_realex_3d')) {
			if ($cc_type == 'visa' || $cc_type == 'mc' || $cc_type == 'amex') {
				$verify_3ds = $this->model_extension_default_realex->check3DEnrollment($account, $amount, $currency, $order_ref,$post);
				ADebug::variable('Verify 3DS result: ', $verify_3ds);
				if (isset($verify_3ds->result) && $verify_3ds->result == '00') {

					$encryption = new AEncryption($this->config->get('encryption_key'));
					$enc_data = $encryption->encrypt(serialize($pd));

					$json = array();
					$json['ACSURL'] = (string)$verify_3ds->url;
					$json['MD'] = $enc_data;
					$json['PaReq'] = (string)$verify_3ds->pareq;
					$json['TermUrl'] = $this->html->getSecureURL('extension/default_realex/callback');

					$this->load->library('json');
					$this->response->setOutput(AJson::encode($json));
					return null;
				}

				//Cardholder Not Enrolled. Shift in liability. ECI = 6
				if (isset($verify_3ds->result) && $verify_3ds->result == '110' 
					&& isset($verify_3ds->enrolled) && $verify_3ds->enrolled == 'N') {
					$v3d['eci_ref'] = 1;
					if ($cardtype == 'mc') {
						$v3d['eci'] = 1;
					} else {
						$v3d['eci'] = 6;
					}
				}

				// Cannot Verify Enrollment. No shift in liability. ECI = 7
				if (isset($verify_3ds->result) && $verify_3ds->result == '110' 
					&& isset($verify_3ds->enrolled) && $verify_3ds->enrolled == 'U') {
					if ($this->config->get('default_realex_liability_shift') != 1) {
						$json['error'] = $this->language->get('error_3d_enroll');
						$this->load->library('json');
						$this->response->setOutput(AJson::encode($json));
						return null;
					} else {
						$v3d['eci_ref'] = 2;
						if ($cardtype == 'mc') {
							$v3d['eci'] = 0;
						} else {
							$v3d['eci'] = 7;
						}
					}
				}

				// Invalid response from Enrollment Server. No shift in liability. ECI = 7
				if (isset($verify_3ds->result) && $verify_3ds->result >= 500 && $verify_3ds->result < 600) {
					if ($this->config->get('realex_remote_liability') != 1) {
						$json['error'] = (string)$verify_3ds->message;
						$this->load->library('json');
						$this->response->setOutput(AJson::encode($json));
						return null;
					} else {
						$v3d['eci_ref'] = 3;
						if ($cardtype == 'mc') {
							$v3d['eci'] = 0;
						} else {
							$v3d['eci'] = 7;
						}
					}
				}
			}
		}

		$p_result = $this->model_extension_default_realex->processPayment($pd, $v3d);

		ADebug::variable('Processing payment result: ', $p_result);
		if ($p_result->result != '00') {
			// transaction failed
			$json['error'] = (string)$p_result->message . ' (' . (int)$p_result->result . ')';
		} else {
			$json['success'] = $this->html->getSecureURL('checkout/success');		
		}

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}

	//Callback function for 3D processing
	public function callback() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$order_id = $this->session->data['order_id'];
		$post = $this->request->post;
		
		if ( $order_id ) {
			$this->loadModel('checkout/order');
			$this->loadModel('extension/default_realex');
			$this->loadLanguage('default_realex/default_realex');

			$pd = unserialize($this->encryption->decrypt($post['MD']));

			$signature_result = $this->model_extension_default_realex->verify3DSignature( $pd, $post['PaRes'] );

			ADebug::checkpoint('Realex 3D processing');
			ADebug::variable('Signature result:'.$signature_result);
			
			$v3d = array();
			if ($signature_result->result == '00' && (strtoupper($signature_result->threedsecure->status) == 'Y' || strtoupper($signature_result->threedsecure->status) == 'A')) {
				if (strtoupper($signature_result->threedsecure->status) == 'Y') {
					$v3d['eci_ref'] = 5;
				} else {
					$v3d['eci_ref'] = 6;
				}

				$v3d['eci'] = (string)$signature_result->threedsecure->eci;
				$v3d['cavv'] = (string)$signature_result->threedsecure->cavv;
				$v3d['xid'] = (string)$signature_result->threedsecure->xid;
			} else {
				if ($pd['cc_type'] == 'mc') {
					$v3d['eci'] = 0;
				} else {
					$v3d['eci'] = 7;
				}

				// Enrolled but invalid response from ACS.  No shift in liability. ECI = 7
				if ($signature_result->result == '110' && strtoupper($signature_result->threedsecure->status) == 'Y') {
					$v3d['eci_ref'] = 4;
					$v3d['cavv'] = (string)$signature_result->threedsecure->cavv;
					$v3d['xid'] = (string)$signature_result->threedsecure->xid;
				}

				// Incorrect password entered.  No shift in liability. ECI = 7
				if ($signature_result->result == '00' && strtoupper($signature_result->threedsecure->status) == 'N') {
					$v3d['eci_ref'] = 7;
					$v3d['xid'] = (string)$signature_result->threedsecure->xid;
				}

				// Authentication Unavailable.  No shift in liability. ECI = 7
				if ($signature_result->result == '00' && strtoupper($signature_result->threedsecure->status) == 'U') {
					$v3d['eci_ref'] = 8;
					$v3d['xid'] = (string)$signature_result->threedsecure->xid;
				}

				// Invalid response from ACS.  No shift in liability. ECI = 7
				if (isset($signature_result->result)  && $signature_result->result >= 500 && $signature_result->result < 600) {
					$v3d['eci_ref'] = 9;
				}

				if (!$this->config->get('default_realex_liability_shift')) {
					// this is the check for liability shift
					// Merchant does not want to accept, redirect to checkout with message
					$error = '3D secure authorization failed';
					$message = $error;
					$message .= 'ECI (3D secure) result: (' . $v3d['eci'] . ')';
					$message .= 'Timestamp: ' . (string)strftime("%Y%m%d%H%M%S");
					$message .= 'Order Reference: ' . (string)$pd['order_ref'];

					$this->model_checkout_order->update(
						$order_id, 
						$this->config->get('default_realex_status_decline'), 
						$message, 
						FALSE
					);

					$this->session->data['error'] = $error;

					$this->redirect($this->html->getSecureURL('checkout/checkout'));
				}
			}

			$capture_result = $this->model_extension_default_realex->processPayment($pd, $v3d);

			ADebug::variable('Capture result:'.$capture_result);

			if ($capture_result->result != '00') {
				$this->session->data['error'] = (string)$capture_result->message . ' (' . (int)$capture_result->result . ')';

				$this->redirect($this->html->getSecureURL('checkout/checkout'));
			} else {
				$this->redirect($this->html->getSecureURL('checkout/success'));
			}
		} else {
			$this->redirect($this->html->getSecureURL('account/login'));
		}
	}

}

