<?php


	if (!defined('DIR_CORE')) {
		header('Location: static_pages/');
	}

class ModelExtensionDefaultPPExpress extends Model {

	public $data = array();
	private $error = array();

	public function processRefund($data) {
		$this->language->load('default_pp_express/default_pp_express');

		$sql = "INSERT INTO " . $this->db->table('order_totals') . " (`order_id`,`title`,`text`,`value`,`sort_order`,`type`)
							VALUES ('" . (int)$data['order_id'] . "',
									'" . $this->db->escape($this->language->get('paypal_refund_title')) . "',
									'-" . $this->currency->format((float)$data['amount'], $data['currency']) . "',
									'-" . (float)$data['amount'] . "',
									'500',
									'paypal_refund')";
		$this->db->query($sql);

		$sql = "SELECT * FROM " . DB_PREFIX . "order_totals WHERE type='total' AND order_id = '".(int)$data['order_id']."'";
		$res = $this->db->query($sql);
		$total = $res->row;


		$sql = "UPDATE " . DB_PREFIX . "order_totals
						SET `text` = '".$this->currency->format(($total['value']-$data['amount']), $data['currency']) . "',
						`value` = '".((float)$total['value']-(float)$data['amount'])."'
						WHERE order_id = '".(int)$data['order_id']."'
							AND type='total'";
		$this->db->query($sql);
	}

	public function updatePaymentMethodData($order_id, $data) {

		if ( is_array($data) ) {
			$data = serialize($data);
		}

		return $this->db->query(
			'UPDATE ' . $this->db->table('orders') . '
				SET payment_method_data = "' . $this->db->escape($data) . '"
				WHERE order_id = "' . (int) $order_id . '"'
		);
	}

	public function addOrderHistory($data) {
		$this->db->query("INSERT INTO " . $this->db->table("order_history") . "
								SET order_id = '" . (int) $data['order_id'] . "',
									order_status_id = '" . (int) $data['order_status_id'] . "',
									notify = '" . (int) $data['notify'] . "',
									comment = '" . $this->db->escape($data['comment']) . "',
									date_added = NOW()");
	}
}
