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
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ModelExtensionDefaultRealex extends Model {

	public function getMethod($address) {
		$this->load->language('default_realex/default_realex');
		if ($this->config->get('default_realex_status')) {
			$query = $this->db->query("SELECT * FROM `" . $this->db->table("zones_to_locations") . "` WHERE location_id = '" . (int)$this->config->get('default_realex_location_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if (!$this->config->get('default_realex_location_id')) {
				$status = TRUE;
			} elseif ($query->num_rows) {
				$status = TRUE;
			} else {
				$status = FALSE;
			}
		} else {
			$status = FALSE;
		}

		$payment_data = array();
		if ($status) {
			$payment_data = array(
				'id'         => 'default_realex',
				'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('default_realex_sort_order')
			);
		}
		return $payment_data;
	}

	public function check3DEnrollment($account, $amount, $currency, $order_ref, $data) {
		$timestamp = strftime("%Y%m%d%H%M%S");
		$merchant_id = $this->config->get('default_realex_merchant_id');
		$secret = $this->config->get('default_realex_secret');

		$tmp = $timestamp . '.' . $merchant_id . '.' . $order_ref . '.' . $amount . '.' . $currency . '.' . $data['cc_number'];
		$hash = sha1($tmp);
		$tmp = $hash . '.' . $secret;
		$hash = sha1($tmp);

		$xml = '<request type="3ds-verifyenrolled" timestamp="' . $timestamp . '">'."\n";
			$xml .= '<merchantid>' . $merchant_id . '</merchantid>'."\n";
			$xml .= '<account>' . $account . '</account>'."\n";
			$xml .= '<orderid>' . $order_ref . '</orderid>'."\n";
			$xml .= '<amount currency="' . $currency . '">' . $amount . '</amount>'."\n";
			$xml .= '<card>'."\n";
				$xml .= '<number>' . $data['cc_number'] . '</number>'."\n";
				$xml .= '<expdate>' . $data['cc_expire_date_month'] . $data['cc_expire_date_year'] . '</expdate>'."\n";
				$xml .= '<type>' . $data['cc_type'] . '</type>'."\n";
				$xml .= '<chname>' . $data['cc_owner'] . '</chname>'."\n";
			$xml .= '</card>'."\n";
			$xml .= '<sha1hash>' . $hash . '</sha1hash>'."\n";
		$xml .= '</request>'."\n";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://epage.payandshop.com/epage-3dsecure.cgi");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "AbanteCart " . VERSION);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec ($ch);
		curl_close ($ch);

		return simplexml_load_string($response);
	}

	public function processPayment($pd, $v3d) {
	
		$this->load->model('checkout/order');
		$this->load->language('default_realex/default_realex');

		$timestamp = strftime("%Y%m%d%H%M%S");
		$merchant_id = $this->config->get('default_realex_merchant_id');
		$secret = $this->config->get('default_realex_secret');

		$tmp = $timestamp.'.'.$merchant_id.'.'.$pd['order_ref'].'.'.$pd['amount'].'.'.$pd['currency'].'.'.$pd['cc_number'];
		$hash = sha1($tmp);
		$tmp = $hash.'.'.$secret;
		$hash = sha1($tmp);

		$order_info = $this->model_checkout_order->getOrder($pd['order_id']);
		
		$xml = '<request timestamp="' . $timestamp . '" type="auth">'."\n";
			$xml .= '<merchantid>' . $merchant_id . '</merchantid>'."\n";
			if ($pd['account']) {
				$xml .= '<account>' . $pd['account'] . '</account>'."\n";
			}
			$xml .= '<orderid>' . $pd['order_ref'] . '</orderid>'."\n";
			$xml .= '<amount currency="' . $pd['currency'] . '">' . $pd['amount'] . '</amount>'."\n";
			$xml .= '<comments>'."\n";
				$xml .= '<comment id="1">AbanteCart</comment>'."\n";
			$xml .= '</comments>'."\n";
			$xml .= '<card>'."\n";
				$xml .= '<number>' . $pd['cc_number'] . '</number>'."\n";
				$xml .= '<expdate>' . $pd['cc_expire'] . '</expdate>'."\n";
				$xml .= '<type>' . $pd['cc_type'] . '</type>'."\n";
				$xml .= '<chname>' . $pd['cc_owner'] . '</chname>'."\n";
				$xml .= '<cvn>'."\n";
					$xml .= '<number>' . (int)$pd['cc_cvv2'] . '</number>'."\n";
					$xml .= '<presind>2</presind>'."\n";
				$xml .= '</cvn>'."\n";
				if (has_value($pd['cc_issue'])) {
					$xml .= '<issueno>' . (int)$pd['cc_issue'] . '</issueno>'."\n";
				}
			$xml .= '</card>'."\n";

			if ($this->config->get('default_realex_settlement') == 'delayed') {
				$xml .= '<autosettle flag="0" />'."\n";
			} elseif ($this->config->get('default_realex_settlement') == 'auto') {
				$xml .= '<autosettle flag="1" />'."\n";
			} elseif ($this->config->get('default_realex_settlement') == 'multi') {
				$xml .= '<autosettle flag="MULTI" />'."\n";
			} else {
				$xml .= '<autosettle flag="0" />'."\n";
			}

			if ( has_value($v3d['eci']) || has_value($v3d['cavv']) || has_value($v3d['xid']) ) {
				$xml .= '<mpi>'."\n";
				if (has_value($v3d['eci'])) {
				    $xml .= '<eci>' . (string)$v3d['eci'] . '</eci>'."\n";
				}
				if (has_value($v3d['cavv'])) {
				    $xml .= '<cavv>' . (string)$v3d['cavv'] . '</cavv>'."\n";
				}
				if (has_value($v3d['xid'])) {
				    $xml .= '<xid>' . (string)$v3d['xid'] . '</xid>'."\n";
				}
				$xml .= '</mpi>'."\n";
			}

			$xml .= '<sha1hash>' . $hash . '</sha1hash>'."\n";

			if ($this->config->get('default_realex_tss_check')) {
				$xml .= '<tssinfo>'."\n";

					$xml .= '<custipaddress>' . $order_info['ip'] . '</custipaddress>'."\n";
					//if not guest checkout
					if ($this->customer->getId() > 0) {
						$xml .= '<custnum>' . (int)$this->customer->getId() . '</custnum>'."\n";
					}
					if (has_value($order_info['payment_iso_code_2']) || has_value($order_info['payment_postcode'])) {
						$xml .= '<address type="billing">'."\n";
						if (has_value($order_info['payment_postcode'])) {
							$xml .= '<code>' . filter_var($order_info['payment_postcode'], FILTER_SANITIZE_NUMBER_INT) . '|' . filter_var($order_info['payment_address_1'], FILTER_SANITIZE_NUMBER_INT) . '</code>'."\n";
						}
						if (has_value($order_info['payment_iso_code_2'])) {
							$xml .= '<country>' . $order_info['payment_iso_code_2'] . '</country>'."\n";
						}
						$xml .= '</address>'."\n";
					}
					if (has_value($order_info['shipping_iso_code_2']) || has_value($order_info['shipping_postcode'])) {
						$xml .= '<address type="shipping">'."\n";
						if (has_value($order_info['shipping_postcode'])) {
							$xml .= '<code>' . filter_var($order_info['shipping_postcode'], FILTER_SANITIZE_NUMBER_INT) . '|' . filter_var($order_info['shipping_address_1'], FILTER_SANITIZE_NUMBER_INT) . '</code>'."\n";
						}
						if (has_value($order_info['shipping_iso_code_2'])) {
							$xml .= '<country>' . $order_info['shipping_iso_code_2'] . '</country>'."\n";
						}
						$xml .= '</address>'."\n";
					}
				$xml .= '</tssinfo>'."\n";
			}

		$xml .= '</request>'."\n";

		ADebug::variable('Processing realex payment request: ', $xml);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://epage.payandshop.com/epage-remote.cgi");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "AbanteCart ".VERSION);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec ($ch);
		curl_close ($ch);

		ADebug::variable('Processing realex payment response: ', $response);
		$response = simplexml_load_string($response);

		//Note: no language support as this UK based payment
		$message .= 'Order Reference: ' . (string)$pd['order_ref'] . "\n";
		$message .= 'Transaction Timestamp: ' . (string)$timestamp;
		$message = 'Response Result: ' . (int)$response->result . "\n";
		$message .= 'Response Message: ' . (string) $result->message . "\n";
		if (isset($result->authcode)) {
		    $message .= 'AuthCode: ' . (string) $result->authcode . "\n";
		}
		if (isset($result->cvnresult)) {
		    $message .= 'CVNResult: ' . (string) $result->cvnresult . "\n";
		}
		if (isset($result->avsaddressresponse)) {
		    $message .= 'AvsAddressResponse: ' . (string) $result->avsaddressresponse . "\n";
		}
		if (isset($result->avspostcoderesponse)) {
		    $message .= 'AvsPostCodeResponse: ' . (string) $result->avspostcoderesponse . "\n";
		}
		if (has_value($v3d['eci_ref'])) {
			$text_arr = array();
			$text_arr[0] = 'ECI (3D secure) ';
			$text_arr[1] = 'Cardholder Not Enrolled, liability shift';
			$text_arr[2] = 'Unable To Verify Enrolment, no liability shift';
			$text_arr[3] = 'Invalid Response From Enrolment Server, no liability shift';
			$text_arr[4] = 'Enrolled, But Invalid Response From ACS (Access Control Server), no liability shift';
			$text_arr[5] = 'Successful Authentication, liability shift';
			$text_arr[6] = 'Authentication Attempt Acknowledged, liability shift';
			$text_arr[7] = 'Incorrect Password Entered, no liability shift';
			$text_arr[8] = 'Authentication Unavailable, no liability shift';
			$text_arr[9] = 'Invalid Response From ACS, no liability shift';
			$text_arr[10] = 'RealMPI Fatal Error, no liability shift';
			$message .= $text_arr[0].': (' . (int)$v3d['eci'] . ') ' . $text_arr[(int)$v3d['eci_ref']];
		}
		if (has_value($response->tss->result)) {
			$message .= 'TSS: ' . (int)$response->tss->result;
		}

		if ($response->result == '00') {
			//finalize order only if payment is a success
			
			$realex_order_id = $this->recordOrder($order_info, $response, $pd['account'], $pd['order_ref']);

			if ($this->config->get('default_realex_settlement') == 'auto') {
				$this->addTransaction($realex_order_id, 'payment', $order_info);
				//auto complete the order in sattled mode 				
				$this->model_checkout_order->confirm(
						$pd['order_id'], 
						$this->config->get('default_realex_status_success_settled')
				);
			} else {
				$this->addTransaction($realex_order_id, 'auth', 0);
				//complete the order in unsattled mode 				
				$this->model_checkout_order->confirm(
						$pd['order_id'], 
						$this->config->get('default_realex_status_success_unsettled')
				);
			}
		} elseif ($response->result == "101") {
			// Transaction Declined
			$this->model_checkout_order->addHistory(
				$pd['order_id'], 
				$this->config->get('default_realex_status_decline'), 
				$message 
			);
		} elseif ($response->result == "102") {
			$this->model_checkout_order->addHistory(
				$pd['order_id'], 
				$this->config->get('default_realex_status_decline_pending'), 
				$message
			);
		} elseif ($response->result == "103") {
			$this->model_checkout_order->addHistory(
				$pd['order_id'], 
				$this->config->get('default_realex_status_decline_stolen'), 
				$message
			);
		} elseif (in_array($response->result, array("200", "204", "205"))) {
			$this->model_checkout_order->addHistory(
				$pd['order_id'], 
				$this->config->get('default_realex_status_decline_bank'), 
				$message
			);
		} else {
			// Some other error
			$this->model_checkout_order->addHistory(
				$pd['order_id'], 
				$this->config->get('default_realex_status_decline'), 
				$message
			);
		}

		return $response;
	}

	//Check 3D Signature
	public function verify3DSignature( $data, $pares) {
	
		$this->load->model('checkout/order');

		$timestamp = strftime("%Y%m%d%H%M%S");
		$merchant_id = $this->config->get('default_realex_merchant_id');
		$secret = $this->config->get('default_realex_secret');

		$tmp = $timestamp.'.'.$merchant_id.'.'.$data['order_ref'].'.'.$data['amount'].'.'.$data['currency'].'.'.$data['cc_number'];
		$hash = sha1($tmp);
		$tmp = $hash . '.' . $secret;
		$hash = sha1($tmp);

		$xml = '';
		$xml .= '<request type="3ds-verifysig" timestamp="' . $timestamp . '">'."\n";
			$xml .= '<merchantid>' . $merchant_id . '</merchantid>'."\n";
			$xml .= '<account>' . $data['account'] . '</account>'."\n";
			$xml .= '<orderid>' . $data['order_ref'] . '</orderid>'."\n";
			$xml .= '<amount currency="' . $data['currency'] . '">' . (int)$data['amount'] . '</amount>'."\n";
			$xml .= '<card>'."\n";
				$xml .= '<number>' . $data['cc_number'] . '</number>'."\n";
				$xml .= '<expdate>' . $data['cc_expire'] . '</expdate>'."\n";
				$xml .= '<type>' . $data['cc_type'] . '</type>'."\n";
				$xml .= '<chname>' . $data['cc_owner'] . '</chname>'."\n";
			$xml .= '</card>'."\n";
			$xml .= '<pares>' . $pares . '</pares>'."\n";
			$xml .= '<sha1hash>' . $hash . '</sha1hash>'."\n";
		$xml .= '</request>'."\n";

		ADebug::variable('Running verify3DSignature: ', $xml);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://epage.payandshop.com/epage-3dsecure.cgi");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "AbanteCart " . VERSION);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec ($ch);
		curl_close ($ch);

		ADebug::variable('Response from verify3DSignature: ', $response);

		return simplexml_load_string($response);
	}

	//record order with realex database
	public function recordOrder($order_info, $response, $account, $order_ref) {
		if ($this->config->get('default_realex_settlement') == 'auto') {
			$settle_status = 1;
		} else {
			$settle_status = 0;
		}

		$this->db->query("INSERT INTO `" . $this->db->table("realex_orders") . "` 
			SET `order_id` = '" . (int)$order_info['order_id'] . "', 
				`settle_type` = '" . (int)$this->config->get('default_realex_settlement') . "', 
				`order_ref` = '" . $this->db->escape($order_ref) . "', 
				`order_ref_previous` = '" . $this->db->escape($order_ref) . "', 
				`capture_status` = '" . (int)$settle_status . "', 
				`currency_code` = '" . $this->db->escape($order_info['currency']) . "', 
				`pasref` = '" . $this->db->escape($response->pasref) . "', 
				`pasref_previous` = '" . $this->db->escape($response->pasref) . "', 
				`authcode` = '" . $this->db->escape($response->authcode) . "', 
				`account` = '" . $this->db->escape($account) . "', 
				`total` = '" . $this->currency->convert($order_info['total'],$this->config->get('config_currency'), $order_info['currency']) . "',
				`date_added` = now() 						
			");

		return $this->db->getLastId();
	}

	public function addTransaction($realex_order_id, $type, $order_info) {
		$this->db->query("INSERT INTO `" . $this->db->table("realex_order_transactions") . "` 
			SET `realex_order_id` = '" . (int)$realex_order_id . "', 
			`type` = '" . $this->db->escape($type) . "', 
			`amount` = '" . $this->currency->convert($order_info['total'],$this->config->get('config_currency'), $order_info['currency']) . "',
			`date_added` = now()
		");
	}

}