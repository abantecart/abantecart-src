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

class ModelExtensionDefaultRealex extends Model {

	public function void($order_id) {
		$realex_order = $this->getRealexOrder($order_id);

		if (!empty($realex_order)) {
			$timestamp = strftime("%Y%m%d%H%M%S");
			$merchant_id = $this->config->get('default_realex_merchant_id');
			$secret = $this->config->get('default_realex_secret');

			//$this->log->write('Void hash construct: ' . $timestamp . '.' . $merchant_id . '.' . $realex_order['order_ref'] . '...');

			$tmp = $timestamp . '.' . $merchant_id . '.' . $realex_order['order_ref'] . '...';
			$hash = sha1($tmp);
			$tmp = $hash . '.' . $secret;
			$hash = sha1($tmp);

			$xml = '<request type="void" timestamp="' . $timestamp . '">'."\n";
			$xml .= '<merchantid>' . $merchant_id . '</merchantid>'."\n";
			$xml .= '<account>' . $realex_order['account'] . '</account>'."\n";
			$xml .= '<orderid>' . $realex_order['order_ref'] . '</orderid>'."\n";
			$xml .= '<pasref>' . $realex_order['pasref'] . '</pasref>'."\n";
			$xml .= '<authcode>' . $realex_order['authcode'] . '</authcode>'."\n";
			$xml .= '<sha1hash>' . $hash . '</sha1hash>'."\n";
			$xml .= '</request>'."\n";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://epage.payandshop.com/epage-remote.cgi");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, "AbanteCart ".VERSION);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec ($ch);
			curl_close ($ch);

			return simplexml_load_string($response);
		} else {
			return false;
		}
	}

	public function updateVoidStatus($realex_order_id, $status) {
		$this->db->query("UPDATE `" . $this->db->table("realex_orders") . "` 
							SET `void_status` = '" . (int)$status . "' 
							WHERE `realex_order_id` = '" . (int)$realex_order_id . "'");
	}

	public function capture($order_id, $amount) {
		$realex_order = $this->getRealexOrder($order_id);

		if (!empty($realex_order) && $realex_order['capture_status'] == 0) {
			$timestamp = strftime("%Y%m%d%H%M%S");
			$merchant_id = $this->config->get('default_realex_merchant_id');
			$secret = $this->config->get('default_realex_secret');

			if ($realex_order['settle_type'] == 2) {
				$tmp = $timestamp . '.' . $merchant_id . '.' . $realex_order['order_ref'] . '.' . (int)round($amount*100) . '.' . (string)$realex_order['currency_code'] . '.';
				$hash = sha1($tmp);
				$tmp = $hash . '.' . $secret;
				$hash = sha1($tmp);

				$settle_type = 'multisettle';
				$xml_amount = '<amount currency="' . (string)$realex_order['currency_code'] . '">' . (int)round($amount*100) . '</amount>'."\n";
			} else {
				$tmp = $timestamp . '.' . $merchant_id . '.' . $realex_order['order_ref'] . '.' . (int)round($amount*100) . '.' . (string)$realex_order['currency_code'] . '.';
				$hash = sha1($tmp);
				$tmp = $hash . '.' . $secret;
				$hash = sha1($tmp);

				$settle_type = 'settle';
				$xml_amount = '<amount currency="' . (string)$realex_order['currency_code'] . '">' . (int)round($amount*100) . '</amount>'."\n";
			}

			$xml = '<request type="' . $settle_type . '" timestamp="' . $timestamp . '">'."\n";
			$xml .= '<merchantid>' . $merchant_id . '</merchantid>'."\n";
			$xml .= '<account>' . $realex_order['account'] . '</account>'."\n";
			$xml .= '<orderid>' . $realex_order['order_ref'] . '</orderid>'."\n";
			$xml .= $xml_amount;
			$xml .= '<pasref>' . $realex_order['pasref'] . '</pasref>'."\n";
			$xml .= '<authcode>' . $realex_order['authcode'] . '</authcode>'."\n";
			$xml .= '<sha1hash>' . $hash . '</sha1hash>'."\n";
			$xml .= '</request>'."\n";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://epage.payandshop.com/epage-remote.cgi");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, "AbanteCart ".VERSION);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec ($ch);
			curl_close ($ch);

			return simplexml_load_string($response);
		} else {
			return false;
		}
	}

	public function updateCaptureStatus($realex_order_id, $status) {
		$this->db->query("UPDATE `" . $this->db->table("realex_orders") . "` 
							SET `capture_status` = '" . (int)$status . "' 
							WHERE `realex_order_id` = '" . (int)$realex_order_id . "'
						");
	}

	public function updateForRebate($realex_order_id, $pas_ref, $order_ref) {
		$this->db->query("UPDATE `" . $this->db->table("realex_orders") . "` 
							SET `order_ref_previous` = '_multisettle_" . $this->db->escape($order_ref) . "', 
								`pasref_previous` = '" . $this->db->escape($pas_ref) . "' 
							WHERE `realex_order_id` = '" . (int)$realex_order_id . "' 
							LIMIT 1
						");
	}

	public function rebate($order_id, $amount) {
		$realex_order = $this->getRealexOrder($order_id);

		if (!empty($realex_order) && $realex_order['rebate_status'] != 1) {
			$timestamp = strftime("%Y%m%d%H%M%S");
			$merchant_id = $this->config->get('default_realex_merchant_id');
			$secret = $this->config->get('default_realex_secret');

			if ($realex_order['settle_type'] == 2) {
				$order_ref = '_multisettle_' . $realex_order['order_ref'];

				if (empty($realex_order['pasref_previous'])) {
					$pas_ref = $realex_order['pasref'];
				} else {
					$pas_ref = $realex_order['pasref_previous'];
				}
			} else {
				$order_ref = $realex_order['order_ref'];
				$pas_ref = $realex_order['pasref'];
			}

			//$this->log->write('Rebate hash construct: ' . $timestamp . '.' . $merchant_id . '.' . $order_ref . '.' . (int)round($amount*100) . '.' . $realex_order['currency_code'] . '.');

			$tmp = $timestamp . '.' . $merchant_id . '.' . $order_ref . '.' . (int)round($amount*100) . '.' . $realex_order['currency_code'] . '.';
			$hash = sha1($tmp);
			$tmp = $hash . '.' . $secret;
			$hash = sha1($tmp);

			$rebatehash = sha1($this->config->get('default_realex_rebate_password'));

			$xml = '<request type="rebate" timestamp="' . $timestamp . '">'."\n";
			$xml .= '<merchantid>' . $merchant_id . '</merchantid>'."\n";
			$xml .= '<account>' . $realex_order['account'] . '</account>'."\n";
			$xml .= '<orderid>' . $order_ref . '</orderid>'."\n";
			$xml .= '<pasref>' . $pas_ref . '</pasref>'."\n";
			$xml .= '<authcode>' . $realex_order['authcode'] . '</authcode>'."\n";
			$xml .= '<amount currency="' . (string)$realex_order['currency_code'] . '">' . (int)round($amount*100) . '</amount>'."\n";
			$xml .= '<refundhash>' . $rebatehash . '</refundhash>'."\n";
			$xml .= '<sha1hash>' . $hash . '</sha1hash>'."\n";
			$xml .= '</request>'."\n";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://epage.payandshop.com/epage-remote.cgi");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, "AbanteCart ".VERSION);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec ($ch);
			curl_close ($ch);

			return simplexml_load_string($response);
		} else {
			return false;
		}
	}

	public function getRealexOrder($order_id) {
		$qry = $this->db->query("SELECT * FROM `" . $this->db->table("realex_orders") . "` 
									WHERE `order_id` = '" . (int)$order_id . "' LIMIT 1");

		if ($qry->num_rows) {
			$order = $qry->row;
			$order['transactions'] = $this->getTransactions($order['realex_order_id']);

			return $order;
		} else {
			return false;
		}
	}

	private function getTransactions($realex_order_id) {
		$qry = $this->db->query("SELECT * FROM `" . $this->db->table("realex_order_transactions") . "` 
									WHERE `realex_order_id` = '" . (int)$realex_order_id . "'");

		if ($qry->num_rows) {
			return $qry->rows;
		} else {
			return false;
		}
	}

	public function addTransaction($realex_order_id, $type, $total) {
		$this->db->query("INSERT INTO `" . $this->db->table("realex_order_transactions") . "` 
							SET `realex_order_id` = '" . (int)$realex_order_id . "', 
								`date_added` = now(), 
								`type` = '" . $this->db->escape($type) . "', 
								`amount` = '" . (float)$total . "'"
						);
	}

	public function getTotalCaptured($realex_order_id) {
		$query = $this->db->query("SELECT SUM(`amount`) AS `total` FROM `" . $this->db->table("realex_order_transactions") . "` 
									WHERE `realex_order_id` = '" . (int)$realex_order_id . "' 
										AND (`type` = 'payment' OR `type` = 'rebate')");

		return (float)$query->row['total'];
	}

	public function getTotalRebated($realex_order_id) {
		$query = $this->db->query("SELECT SUM(`amount`) AS `total` FROM `" . $this->db->table("realex_order_transactions") . "` 
									WHERE `realex_order_id` = '" . (int)$realex_order_id . "' 
										AND 'rebate'");

		return (double)$query->row['total'];
	}

	public function updateRebateStatus($realex_order_id, $status) {
		$this->db->query("UPDATE `" . $this->db->table("realex_orders") . "` 
							SET `rebate_status` = '" . (int)$status . "' 
							WHERE `realex_order_id` = '" . (int)$realex_order_id . "'"
						);
	}
}