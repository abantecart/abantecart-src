<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2017 Belavier Commerce LLC

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

/**
 * Class ModelCheckoutOrder
 * @property  ModelLocalisationCountry $model_localisation_country
 * @property  ModelLocalisationZone $model_localisation_zone
 */
class ModelCheckoutOrder extends Model{
	public $data = array ();

	/**
	 * @param $order_id
	 * @return array|bool
	 */
	public function getOrder($order_id){
		$order_query = $this->db->query(
				"SELECT *
				FROM `" . $this->db->table("orders") . "`
				WHERE order_id = '" . (int)$order_id . "'"
		);

		if ($order_query->num_rows){

			$this->load->model('localisation/country');
			$this->load->model('localisation/zone');
			$country_row = $this->model_localisation_country->getCountry($order_query->row['shipping_country_id']);
			if ($country_row){
				$shipping_iso_code_2 = $country_row['iso_code_2'];
				$shipping_iso_code_3 = $country_row['iso_code_3'];
			} else{
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_row = $this->model_localisation_zone->getZone($order_query->row['shipping_zone_id']);
			if ($zone_row){
				$shipping_zone_code = $zone_row['code'];
			} else{
				$shipping_zone_code = '';
			}

			$country_row = $this->model_localisation_country->getCountry($order_query->row['payment_country_id']);
			if ($country_row){
				$payment_iso_code_2 = $country_row['iso_code_2'];
				$payment_iso_code_3 = $country_row['iso_code_3'];
			} else{
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_row = $this->model_localisation_zone->getZone($order_query->row['payment_zone_id']);
			if ($zone_row){
				$payment_zone_code = $zone_row['code'];
			} else{
				$payment_zone_code = '';
			}

			$order_data = $this->dcrypt->decrypt_data($order_query->row, 'orders');

			$order_data['shipping_zone_code'] = $shipping_zone_code;
			$order_data['shipping_iso_code_2'] = $shipping_iso_code_2;
			$order_data['shipping_iso_code_3'] = $shipping_iso_code_3;
			$order_data['payment_zone_code'] = $payment_zone_code;
			$order_data['payment_iso_code_2'] = $payment_iso_code_2;
			$order_data['payment_iso_code_3'] = $payment_iso_code_3;

			return $order_data;
		} else{
			return false;
		}
	}

	/**
	 * @param array $data
	 * @param int $set_order_id
	 */
	public function create($data, $set_order_id = '') {
		$result = $this->extensions->hk_create($this, $data, $set_order_id);
		if($result !== null ){
			return $result;
		}
	}
	/**
	 * @param array $data
	 * @param int|string $set_order_id
	 * @return bool|int
	 */
	public function _create($data, $set_order_id = '') {
		$set_order_id = (int)$set_order_id;
		//reuse same order_id or unused one order_status_id = 0
		if ($set_order_id){
			$query = $this->db->query("SELECT order_id
										FROM `" . $this->db->table("orders") . "`
										WHERE order_id = " . $set_order_id . " AND order_status_id = '0'");

			if (!$query->num_rows){ // for already processed orders do redirect
				$query = $this->db->query("SELECT order_id
											FROM `" . $this->db->table("orders") . "`
											WHERE order_id = " . $set_order_id . " AND order_status_id > '0'");
				if ($query->num_rows){
					return false;
				}
				//remove
			} else{
				$this->_remove_order($query->row['order_id']);
			}
		}

		//clean up based on setting (remove already created or abandoned orders)
		if ((int)$this->config->get('config_expire_order_days')){
			$query = $this->db->query(
					"SELECT order_id
					FROM " . $this->db->table("orders") . "
					WHERE date_modified < '" . date('Y-m-d', strtotime('-' . (int)$this->config->get('config_expire_order_days') . ' days')) . "'
						AND order_status_id = '0'");
			foreach ($query->rows as $result){
				$this->_remove_order($result['order_id']);
			}
		}

		if (!$set_order_id && (int)$this->config->get('config_start_order_id')){
			$query = $this->db->query("SELECT MAX(order_id) AS order_id
										FROM `" . $this->db->table("orders") . "`");
			if ($query->row['order_id'] && $query->row['order_id'] >= $this->config->get('config_start_order_id')){
				$set_order_id = (int)$query->row['order_id'] + 1;
			} elseif ($this->config->get('config_start_order_id')){
				$set_order_id = (int)$this->config->get('config_start_order_id');
			} else{
				$set_order_id = 0;
			}
		}

		if ($set_order_id){
			$set_order_id = "order_id = '" . (int)$set_order_id . "', ";
		} else{
			$set_order_id = '';
		}

		$key_sql = '';
		if ($this->dcrypt->active){
			$data = $this->dcrypt->encrypt_data($data, 'orders');
			$key_sql = ", key_id = '" . (int)$data['key_id'] . "'";
		}

		$this->db->query("INSERT INTO `" . $this->db->table("orders") . "`
							SET " . $set_order_id . " store_id = '" . (int)$data['store_id'] . "',
								store_name = '" . $this->db->escape($data['store_name']) . "',
								store_url = '" . $this->db->escape($data['store_url']) . "',
								customer_id = '" . (int)$data['customer_id'] . "',
								customer_group_id = '" . (int)$data['customer_group_id'] . "',
								firstname = '" . $this->db->escape($data['firstname']) . "',
								lastname = '" . $this->db->escape($data['lastname']) . "',
								email = '" . $this->db->escape($data['email']) . "',
								telephone = '" . $this->db->escape($data['telephone']) . "',
								fax = '" . $this->db->escape($data['fax']) . "',
								total = '" . (float)$data['total'] . "',
								language_id = '" . (int)$data['language_id'] . "',
								currency = '" . $this->db->escape($data['currency']) . "',
								currency_id = '" . (int)$data['currency_id'] . "',
								value = '" . (float)$data['value'] . "',
								coupon_id = '" . (int)$data['coupon_id'] . "',
								ip = '" . $this->db->escape($data['ip']) . "',
								shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "',
								shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "',
								shipping_company = '" . $this->db->escape($data['shipping_company']) . "',
								shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "',
								shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "',
								shipping_city = '" . $this->db->escape($data['shipping_city']) . "',
								shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "',
								shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "',
								shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "',
								shipping_country = '" . $this->db->escape($data['shipping_country']) . "',
								shipping_country_id = '" . (int)$data['shipping_country_id'] . "',
								shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "',
								shipping_method = '" . $this->db->escape($data['shipping_method']) . "',
								shipping_method_key = '" . $this->db->escape($data['shipping_method_key']) . "',
								payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "',
								payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "',
								payment_company = '" . $this->db->escape($data['payment_company']) . "',
								payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "',
								payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "',
								payment_city = '" . $this->db->escape($data['payment_city']) . "',
								payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "',
								payment_zone = '" . $this->db->escape($data['payment_zone']) . "',
								payment_zone_id = '" . (int)$data['payment_zone_id'] . "',
								payment_country = '" . $this->db->escape($data['payment_country']) . "',
								payment_country_id = '" . (int)$data['payment_country_id'] . "',
								payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "',
								payment_method = '" . $this->db->escape($data['payment_method']) . "',
								payment_method_key = '" . $this->db->escape($data['payment_method_key']) . "',
								comment = '" . $this->db->escape($data['comment']) . "'"
				. $key_sql . ",
								date_modified = NOW(),
								date_added = NOW()");

		$order_id = $this->db->getLastId();

		foreach ($data['products'] as $product){
			$this->db->query("INSERT INTO " . $this->db->table("order_products") . "
								SET order_id = '" . (int)$order_id . "',
								product_id = '" . (int)$product['product_id'] . "',
								name = '" . $this->db->escape($product['name']) . "',
								model = '" . $this->db->escape($product['model']) . "',
								sku = '" . $this->db->escape($product['sku']) . "',
								price = '" . (float)$product['price'] . "',
								total = '" . (float)$product['total'] . "',
								tax = '" . (float)$product['tax'] . "',
								quantity = '" . (int)$product['quantity'] . "',
								subtract = '" . (int)$product['stock'] . "'");

			$order_product_id = $this->db->getLastId();

			foreach ($product['option'] as $option){
				$this->db->query("INSERT INTO " . $this->db->table("order_options") . "
									SET order_id = '" . (int)$order_id . "',
										order_product_id = '" . (int)$order_product_id . "',
										product_option_value_id = '" . (int)$option['product_option_value_id'] . "',
										name = '" . $this->db->escape($option['name']) . "',
										sku = '" . $this->db->escape($option['sku']) . "',
										`value` = '" . $this->db->escape($option['value']) . "',
										price = '" . (float)$product['price'] . "',
										prefix = '" . $this->db->escape($option['prefix']) . "',
										settings = '" . $this->db->escape($option['settings']) . "'");
			}

			foreach ($product['download'] as $download){
				// if expire days not set - 0  as unexpired
				$download['expire_days'] = (int)$download['expire_days'] > 0 ? $download['expire_days'] : 0;
				$download['max_downloads'] = ((int)$download['max_downloads'] ? (int)$download['max_downloads'] * $product['quantity'] : '');
				$download['status'] = $download['activate'] == 'manually' ? 0 : 1; //disable download for manual mode for customer
				$download['attributes_data'] = serialize($this->download->getDownloadAttributesValues($download['download_id']));
				$this->download->addProductDownloadToOrder($order_product_id, $order_id, $download);
			}
		}
		foreach ($data['totals'] as $total){
			$this->db->query("INSERT INTO " . $this->db->table("order_totals") . "
								SET `order_id` = '" . (int)$order_id . "',
									`title` = '" . $this->db->escape($total['title']) . "',
									`text` = '" . $this->db->escape($total['text']) . "',
									`value` = '" . (float)$total['value'] . "',
									`sort_order` = '" . (int)$total['sort_order'] . "',
									`type` = '" . $this->db->escape($total['total_type']) . "',
									`key` = '" . $this->db->escape($total['id']) . "'"
			);
		}

		//save IM URI of order
		$this->saveIMOrderData($order_id, $data);
		return $order_id;
	}

	protected function saveIMOrderData($order_id, $data){
		$protocols = $this->im->getProtocols();
		$p = array ();
		foreach ($protocols as $protocol){
			$p[] = $this->db->escape($protocol);
		}

		$sql = "SELECT DISTINCT `type_id`, `name` as protocol
				FROM " . $this->db->table('order_data_types') . "
				WHERE `name` IN ('" . implode("', '", $p) . "')";
		$result = $this->db->query($sql);
		if(!$result->num_rows){
			return null;
		}

		foreach ($result->rows as $row){
			$type_id = (int)$row['type_id'];
			if($data['customer_id']){
				$uri = $this->im->getCustomerURI($row['protocol'], $data['customer_id']);
			}else{
				$uri = $data[$row['protocol']];
			}
			if ($uri){
				$im_data = serialize(
						array (
								'uri' => $uri,
								'status' => $this->config->get('config_im_guest_' . $row['protocol'] . '_status')
						)
				);

				$sql = "REPLACE INTO " . $this->db->table('order_data') . "
						(`order_id`, `type_id`, `data`, `date_added`)
						VALUES (" . (int)$order_id . ", " . (int)$type_id . ", '" . $this->db->escape($im_data) . "', NOW() )";

				$this->db->query($sql);
			}
		}
	}

	/**
	 * @param int $order_id
	 * @param int $order_status_id
	 * @param string $comment
	 */
	public function confirm($order_id, $order_status_id, $comment = ''){
		$this->extensions->hk_confirm($this, $order_id, $order_status_id, $comment);
	}

	/**
	 * @param int $order_id
	 * @param int $order_status_id
	 * @param string $comment
	 * @return bool
	 */
	public function _confirm($order_id, $order_status_id, $comment = ''){
		$order_query = $this->db->query("SELECT *,
												l.filename AS filename,
												l.directory AS directory
										 FROM `" . $this->db->table("orders") . "` o
										 LEFT JOIN " . $this->db->table("languages") . " l
										    ON (o.language_id = l.language_id)
										 WHERE o.order_id = '" . (int)$order_id . "'
										        AND o.order_status_id = '0'");
		if (!$order_query->num_rows){
			return false;
		}
		$order_row = $this->dcrypt->decrypt_data($order_query->row, 'orders');
		$update = array ();

		//update order status
		$update[] = "order_status_id = '" . (int)$order_status_id . "'";
		$sql = "UPDATE `" . $this->db->table("orders") . "`
			    SET " . implode(", ", $update) . "
				WHERE order_id = '" . (int)$order_id . "'";
		$this->db->query($sql);

		//record history
		$this->db->query("INSERT INTO " . $this->db->table("order_history") . "
						   SET order_id = '" . (int)$order_id . "',
						        order_status_id = '" . (int)$order_status_id . "',
						        notify = '1',
						        comment = '" . $this->db->escape($comment) . "',
						        date_added = NOW()");
		$order_row['comment'] = $order_row['comment'] . ' ' . $comment;

		$order_product_query = $this->db->query("SELECT *
												 FROM " . $this->db->table("order_products") . "
												 WHERE order_id = '" . (int)$order_id . "'");
		//update products inventory
		// load language for IM
		$language = new ALanguage($this->registry);
		$language->load('common/im');

		foreach ($order_product_query->rows as $product){
			$this->db->query("UPDATE " . $this->db->table("products") . "
							  SET quantity = (quantity - " . (int)$product['quantity'] . ")
							  WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = 1");

			//check quantity and send notification when 0 or less
			$sql = "SELECT quantity
			        FROM " . $this->db->table("products") . "
					WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = 1";
			$res = $this->db->query($sql);
			if ($res->num_rows && $res->row['quantity'] <= 0){
				//notify admin with out of stock
				$message_arr = array (
						1 => array ('message' => sprintf($language->get('im_product_out_of_stock_admin_text'), $product['product_id']))
				);
				$this->im->send('product_out_of_stock', $message_arr);
			}

			$order_option_query = $this->db->query("SELECT *
													FROM " . $this->db->table("order_options") . "
													WHERE order_id = '" . (int)$order_id . "'
															AND order_product_id = '" . (int)$product['order_product_id'] . "'");

			foreach ($order_option_query->rows as $option){
				$this->db->query("UPDATE " . $this->db->table("product_option_values") . "
								  SET quantity = (quantity - " . (int)$product['quantity'] . ")
								  WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "'
								        AND subtract = 1");
				$sql = "SELECT quantity
				        FROM " . $this->db->table("product_option_values") . "
						WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "'
							AND subtract = 1";
				$res = $this->db->query($sql);
				if ($res->num_rows && $res->row['quantity'] <= 0){
					//notify admin with out of stock for option based product
					$message_arr = array (
							1 => array ('message' => sprintf($language->get('im_product_out_of_stock_admin_text'), $product['product_id']))
					);
					$this->im->send('product_out_of_stock', $message_arr);
				}
			}
		}

		//clean product cache as stock might have changed.
		$this->cache->remove('product');

		//build confirmation email
		$language = new ALanguage($this->registry, $order_row['code']);
		$language->load($order_row['filename']);
		$language->load('mail/order_confirm');

		$this->load->model('localisation/currency');
		$order_product_query = $this->db->query("SELECT *
												FROM " . $this->db->table("order_products") . "
												WHERE order_id = '" . (int)$order_id . "'");
		$order_total_query = $this->db->query("SELECT *
												FROM " . $this->db->table("order_totals") . "
												WHERE order_id = '" . (int)$order_id . "'
												ORDER BY sort_order ASC");

		$subject = sprintf($language->get('text_subject'), $order_row['store_name'], $order_id);

		// HTML Mail
		$this->data['mail_template_data']['title'] = sprintf($language->get('text_subject'), html_entity_decode($order_row['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);
		$this->data['mail_template_data']['text_greeting'] = sprintf($language->get('text_greeting'), html_entity_decode($order_row['store_name'], ENT_QUOTES, 'UTF-8'));
		$this->data['mail_template_data']['text_order_detail'] = $language->get('text_order_detail');
		$this->data['mail_template_data']['text_order_id'] = $language->get('text_order_id');
		$this->data['mail_template_data']['text_invoice'] = $language->get('text_invoice');
		$this->data['mail_template_data']['text_date_added'] = $language->get('text_date_added');
		$this->data['mail_template_data']['text_telephone'] = $language->get('text_telephone');
		$this->data['mail_template_data']['text_mobile_phone'] = $language->get('text_mobile_phone');

		$this->data['mail_template_data']['text_email'] = $language->get('text_email');
		$this->data['mail_template_data']['text_ip'] = $language->get('text_ip');
		$this->data['mail_template_data']['text_fax'] = $language->get('text_fax');
		$this->data['mail_template_data']['text_shipping_address'] = $language->get('text_shipping_address');
		$this->data['mail_template_data']['text_payment_address'] = $language->get('text_payment_address');
		$this->data['mail_template_data']['text_shipping_method'] = $language->get('text_shipping_method');
		$this->data['mail_template_data']['text_payment_method'] = $language->get('text_payment_method');
		$this->data['mail_template_data']['text_comment'] = $language->get('text_comment');
		$this->data['mail_template_data']['text_powered_by'] = $language->get('text_powered_by');
		$this->data['mail_template_data']['text_project_label'] = $language->get('text_powered_by') . ' ' . project_base();

		$this->data['mail_template_data']['text_total'] = $language->get('text_total');
		$this->data['mail_template_data']['text_footer'] = $language->get('text_footer');

		$this->data['mail_template_data']['column_product'] = $language->get('column_product');
		$this->data['mail_template_data']['column_model'] = $language->get('column_model');
		$this->data['mail_template_data']['column_quantity'] = $language->get('column_quantity');
		$this->data['mail_template_data']['column_price'] = $language->get('column_price');
		$this->data['mail_template_data']['column_total'] = $language->get('column_total');

		$this->data['mail_template_data']['order_id'] = $order_id;
		$this->data['mail_template_data']['customer_id'] = $order_row['customer_id'];
		$this->data['mail_template_data']['date_added'] = dateISO2Display($order_row['date_added'], $language->get('date_format_short'));

		$config_mail_logo = $this->config->get('config_mail_logo');
		$config_mail_logo = !$config_mail_logo ? $this->config->get('config_logo') : $config_mail_logo;
		if($config_mail_logo) {
			if (is_numeric($config_mail_logo)) {
				$r = new AResource('image');
				$resource_info = $r->getResource($config_mail_logo);
				if ($resource_info) {
					$this->data['mail_template_data']['logo_html'] = html_entity_decode($resource_info['resource_code'],
							ENT_QUOTES, 'UTF-8');
				}
			} else {
				$this->data['mail_template_data']['logo_uri'] = 'cid:'
						. md5(pathinfo($config_mail_logo, PATHINFO_FILENAME))
						. '.' . pathinfo($config_mail_logo, PATHINFO_EXTENSION);
			}
		}
		//backward compatibility. TODO: remove this in 2.0
		if($this->data['mail_template_data']['logo_uri']){
			$this->data['mail_template_data']['logo'] = $this->data['mail_template_data']['logo_uri'];
		}else{
			$this->data['mail_template_data']['logo'] = $config_mail_logo;
		}

		$this->data['mail_template_data']['store_name'] = $order_row['store_name'];
		$this->data['mail_template_data']['address'] = nl2br($this->config->get('config_address'));
		$this->data['mail_template_data']['telephone'] = $this->config->get('config_telephone');
		$this->data['mail_template_data']['fax'] = $this->config->get('config_fax');
		$this->data['mail_template_data']['email'] = $this->config->get('store_main_email');
		$this->data['mail_template_data']['store_url'] = $order_row['store_url'];

		//give link on order page for quest
		if ($this->config->get('config_guest_checkout') && $order_row['email']){
			$enc = new AEncryption($this->config->get('encryption_key'));
			$order_token = $enc->encrypt($order_id . '::' . $order_row['email']);
			$this->data['mail_template_data']['invoice'] = $order_row['store_url'] . 'index.php?rt=account/invoice&ot=' . $order_token . "\n\n";
		}//give link on order for registered customers
		elseif ($order_row['customer_id']){
			$this->data['mail_template_data']['invoice'] = $order_row['store_url'] . 'index.php?rt=account/invoice&order_id=' . $order_id;
		}

		$this->data['mail_template_data']['firstname'] = $order_row['firstname'];
		$this->data['mail_template_data']['lastname'] = $order_row['lastname'];
		$this->data['mail_template_data']['shipping_method'] = $order_row['shipping_method'];
		$this->data['mail_template_data']['payment_method'] = $order_row['payment_method'];
		$this->data['mail_template_data']['customer_email'] = $order_row['email'];
		$this->data['mail_template_data']['customer_telephone'] = $order_row['telephone'];
		$this->data['mail_template_data']['customer_mobile_phone'] = $this->im->getCustomerURI('sms', (int)$order_row['customer_id'], $order_id);
		$this->data['mail_template_data']['customer_fax'] = $order_row['fax'];
		$this->data['mail_template_data']['customer_ip'] = $order_row['ip'];
		$this->data['mail_template_data']['comment'] = trim(nl2br($order_row['comment']));

		//override with the data from the before hooks
		if ($this->data){
			$this->data['mail_template_data'] = array_merge($this->data['mail_template_data'], $this->data);
		}

		$this->load->model('localisation/zone');
		$zone_row = $this->model_localisation_zone->getZone($order_row['shipping_zone_id']);
		if ($zone_row){
			$zone_code = $zone_row['code'];
		} else{
			$zone_code = '';
		}

		$shipping_data = array (
				'firstname' => $order_row['shipping_firstname'],
				'lastname'  => $order_row['shipping_lastname'],
				'company'   => $order_row['shipping_company'],
				'address_1' => $order_row['shipping_address_1'],
				'address_2' => $order_row['shipping_address_2'],
				'city'      => $order_row['shipping_city'],
				'postcode'  => $order_row['shipping_postcode'],
				'zone'      => $order_row['shipping_zone'],
				'zone_code' => $zone_code,
				'country'   => $order_row['shipping_country']
		);

		$this->data['mail_template_data']['shipping_address'] = $this->customer->getFormattedAddress($shipping_data, $order_row['shipping_address_format']);
		$zone_row = $this->model_localisation_zone->getZone($order_row['payment_zone_id']);
		if ($zone_row){
			$zone_code = $zone_row['code'];
		} else{
			$zone_code = '';
		}

		$payment_data = array (
				'firstname' => $order_row['payment_firstname'],
				'lastname'  => $order_row['payment_lastname'],
				'company'   => $order_row['payment_company'],
				'address_1' => $order_row['payment_address_1'],
				'address_2' => $order_row['payment_address_2'],
				'city'      => $order_row['payment_city'],
				'postcode'  => $order_row['payment_postcode'],
				'zone'      => $order_row['payment_zone'],
				'zone_code' => $zone_code,
				'country'   => $order_row['payment_country']
		);

		$this->data['mail_template_data']['payment_address'] = $this->customer->getFormattedAddress($payment_data, $order_row['payment_address_format']);

		if (!has_value($this->data['products'])){
			$this->data['products'] = array ();
		}

		foreach ($order_product_query->rows as $product){
			$option_data = array ();

			$order_option_query = $this->db->query(
					"SELECT oo.*, po.element_type, p.sku, p.product_id
					FROM " . $this->db->table("order_options") . " oo
					LEFT JOIN " . $this->db->table("product_option_values") . " pov
						ON pov.product_option_value_id = oo.product_option_value_id
					LEFT JOIN " . $this->db->table("product_options") . " po
						ON po.product_option_id = pov.product_option_id
					LEFT JOIN " . $this->db->table("products") . " p
						ON p.product_id = po.product_id
					WHERE oo.order_id = '" . (int)$order_id . "' AND oo.order_product_id = '" . (int)$product['order_product_id'] . "'");

			foreach ($order_option_query->rows as $option){
				if ($option['element_type'] == 'H'){
					continue;
				} //skip hidden options
				elseif ($option['element_type'] == 'C' && in_array($option['value'], array (0, 1, ''))){
					$option['value'] = '';
				}
				$option_data[] = array (
						'name'  => $option['name'],
						'value' => $option['value']
				);
			}

			$this->data['products'][] = array (
					'name'     => $product['name'],
					'product_id' => $product['product_id'],
				    'sku'       => $product['sku'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'], $order_row['currency'], $order_row['value']),
					'total'    => $this->currency->format_total($product['price'], $product['quantity'], $order_row['currency'], $order_row['value'])
			);
		}
		$this->data['mail_template_data']['products'] = $this->data['products'];
		$this->data['mail_template_data']['totals'] = $order_total_query->rows;

		$this->data['mail_template'] = 'mail/order_confirm.tpl';

		//allow to change email data from extensions
		$this->extensions->hk_ProcessData($this, 'sf_order_confirm_mail');

		$view = new AView($this->registry, 0);
		$view->batchAssign($this->data['mail_template_data']);
		$html_body = $view->fetch($this->data['mail_template']);

		//text email
		$this->data['mail_template'] = 'mail/order_confirm_text.tpl';

		//allow to change email data from extensions
		$this->extensions->hk_ProcessData($this, 'sf_order_confirm_mail_text');
		$this->data['mail_plain_text'] = $view->fetch($this->data['mail_template']);
		$this->data['mail_plain_text'] = html_entity_decode($this->data['mail_plain_text'], ENT_QUOTES, 'UTF-8');
		//remove html-tags
		$breaks = array ("<br />", "<br>", "<br/>");
		$this->data['mail_plain_text'] = str_ireplace($breaks, "\r\n", $this->data['mail_plain_text']);

		$mail = new AMail($this->config);
		$mail->setTo($order_row['email']);
		$mail->setFrom($this->config->get('store_main_email'));
		$mail->setSender($order_row['store_name']);
		$mail->setSubject($subject);
		$mail->setHtml($html_body);
		$mail->setText($this->data['mail_plain_text']);
		if(is_file(DIR_RESOURCE . $config_mail_logo)) {
			$mail->addAttachment(DIR_RESOURCE . $config_mail_logo,
					md5(pathinfo($config_mail_logo, PATHINFO_FILENAME))
					. '.' . pathinfo($config_mail_logo, PATHINFO_EXTENSION));
		}
		$mail->send();

		//send alert email for merchant
		if ($this->config->get('config_alert_mail')){

			// HTML
			$this->data['mail_template_data']['text_greeting'] = $language->get('text_received') . "\n\n";
			$this->data['mail_template_data']['invoice'] = '';
			$this->data['mail_template_data']['text_invoice'] = '';
			$this->data['mail_template_data']['text_footer'] = '';

			$this->data['mail_template'] = 'mail/order_confirm.tpl';

			//allow to change email data from extensions
			$this->extensions->hk_ProcessData($this, 'sf_order_confirm_alert_mail');

			$view = new AView($this->registry, 0);
			$view->batchAssign($this->data['mail_template_data']);
			$html_body = $view->fetch($this->data['mail_template']);

			//text email
			//allow to change email data from extensions
			$this->data['mail_template'] = 'mail/order_confirm_text.tpl';
			$this->extensions->hk_ProcessData($this, 'sf_order_confirm_alert_mail_text');

			$this->data['mail_plain_text'] = $view->fetch($this->data['mail_template']);
			$this->data['mail_plain_text'] = html_entity_decode($this->data['mail_plain_text'], ENT_QUOTES, 'UTF-8');
			//remove html-tags
			$breaks = array ("<br />", "<br>", "<br/>");
			$this->data['mail_plain_text'] = str_ireplace($breaks, "\r\n", $this->data['mail_plain_text']);

			$order_total = '';
			foreach ($order_total_query->rows as $row){
				if ($row['key'] == 'total'){
					$order_total = $row['text'];
					break;
				}
			}

			$subject = sprintf($language->get('text_subject'), html_entity_decode($this->config->get('store_name'), ENT_QUOTES, 'UTF-8'), $order_id . ' (' . $order_total . ')');

			$mail->setSubject($subject);
			$mail->setTo($this->config->get('store_main_email'));
			$mail->setHtml($html_body);
			$mail->setText($this->data['mail_plain_text']);
			$mail->send();

			// Send to additional alert emails
			$emails = explode(',', $this->config->get('config_alert_emails'));
			foreach ($emails as $email){
				if (trim($email)){
					$mail->setTo($email);
					$mail->send();
				}
			}
		}

		$msg_text = sprintf($language->get('text_new_order_text'), $order_row['firstname'] . ' ' . $order_row['lastname']);
		$msg_text .= "<br/><br/>";
		foreach ($this->data['mail_template_data']['totals'] as $total){
			$msg_text .= $total['title'] . ' - ' . $total['text'] . "<br/>";
		}
		$msg = new AMessage();
		$msg->saveNotice($language->get('text_new_order') . $order_id, $msg_text);

		$language = new ALanguage($this->registry);
		$language->load('common/im');
		$message_arr = array (
				1 => array ('message' => sprintf($language->get('im_new_order_text_to_admin'), $order_id))
		);
		$this->im->send('new_order', $message_arr);

		return true;
	}

	/**
	 * @param int $order_id
	 * @param int $order_status_id
	 * @param string $comment
	 * @param bool|false $notify
	 * @return mixed
	 */
	public function update($order_id, $order_status_id, $comment = '', $notify = FALSE) {
		$this->extensions->hk_update($this, $order_id, $order_status_id, $comment, $notify);
	}
	/**
	 * @param int $order_id
	 * @param int $order_status_id
	 * @param string $comment
	 * @param bool $notify
	 */
	public function _update($order_id, $order_status_id, $comment = '', $notify = FALSE) {
		$order_query = $this->db->query("SELECT *
										 FROM `" . $this->db->table("orders") . "` o
										 LEFT JOIN " . $this->db->table("languages") . " l ON (o.language_id = l.language_id)
										 WHERE o.order_id = '" . (int)$order_id . "' AND o.order_status_id > '0'");

		if ($order_query->num_rows){
			$order_row = $this->dcrypt->decrypt_data($order_query->row, 'orders');

			$this->db->query("UPDATE `" . $this->db->table("orders") . "`
								SET order_status_id = '" . (int)$order_status_id . "',
									date_modified = NOW()
								WHERE order_id = '" . (int)$order_id . "'");

			$this->db->query("INSERT INTO " . $this->db->table("order_history") . "
								SET order_id = '" . (int)$order_id . "',
									order_status_id = '" . (int)$order_status_id . "',
									notify = '" . (int)$notify . "',
									comment = '" . $this->db->escape($comment) . "',
									date_added = NOW()");

			//send notifications
			$language = new ALanguage($this->registry, $order_row['code']);
			$language->load($order_row['filename']);
			$language->load('mail/order_update');

			$order_status_query = $this->db->query("SELECT *
													FROM " . $this->db->table("order_statuses") . "
													WHERE order_status_id = '" . (int)$order_status_id . "'
														AND language_id = '" . (int)$order_row['language_id'] . "'");

			$language_im = new ALanguage($this->registry);
			$language_im->load('common/im');
			$status_name = '';
			if ($order_status_query->row['name']){
				$status_name = $order_status_query->row['name'];
			}
			$message_arr = array (
					0 => array ('message' => sprintf($language_im->get('im_order_update_text_to_customer'), $order_id, $status_name)),
					1 => array ('message' => sprintf($language_im->get('im_order_update_text_to_admin'), $order_id, $status_name))
			);
			$this->im->send('order_update', $message_arr);

			//notify via email
			if ($notify){

				$subject = sprintf($language->get('text_subject'), html_entity_decode($order_row['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

				$message = $language->get('text_order') . ' ' . $order_id . "\n";
				$message .= $language->get('text_date_added') . ' ' . dateISO2Display($order_row['date_added'], $language->get('date_format_short')) . "\n\n";

				if ($order_status_query->num_rows){
					$message .= $language->get('text_order_status') . "\n\n";
					$message .= $order_status_query->row['name'] . "\n\n";
				}

				if ($order_row['customer_id']){
					$message .= $language->get('text_invoice') . "\n";
					$message .= $order_row['store_url'] . 'index.php?rt=account/invoice&order_id=' . $order_id . "\n\n";
				} //give link on order page for quest
				elseif ($this->config->get('config_guest_checkout') && $order_row['email']){
					$enc = new AEncryption($this->config->get('encryption_key'));
					$order_token = $enc->encrypt($order_id . '::' . $order_row['email']);
					if ($order_token){
						$message .= $language->get('text_invoice') . "\n";
						$message .= $order_row['store_url'] . 'index.php?rt=account/invoice&ot=' . $order_token . "\n\n";
					}
				}

				if ($comment){
					$message .= $language->get('text_comment') . "\n\n";
					$message .= $comment . "\n\n";
				}

				$message .= $language->get('text_footer');

				$mail = new AMail($this->config);
				$mail->setTo($order_row['email']);
				$mail->setFrom($this->config->get('store_main_email'));
				$mail->setSender($order_row['store_name']);
				$mail->setSubject($subject);
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$mail->send();
			}
		}
	}

	/**
	 * @param int $order_id
	 * @param int $order_status_id
	 * @param string $comment
	 * @return null
	 */
	public function addHistory($order_id, $order_status_id, $comment){
		$this->db->query("INSERT INTO " . $this->db->table('order_history') . " 
							SET order_id = '" . (int)$order_id . "', 
								order_status_id = '" . (int)$order_status_id . "', 
								notify = '0', 
								comment = '" . $this->db->escape($comment) . "', 
								date_added = NOW()"
		);
		return null;
	}

	/**
	 * @param int $order_id
	 * @param string|array $data
	 * @return bool|stdClass
	 */
	public function updatePaymentMethodData($order_id, $data){

		if (is_array($data)){
			$data = serialize($data);
		}

		return $this->db->query(
				'UPDATE ' . $this->db->table('orders') . '
				SET payment_method_data = "' . $this->db->escape($data) . '"
				WHERE order_id = "' . (int)$order_id . '"'
		);
	}

	/**
	 * @param $order_id
	 * @return bool
	 */
	private function _remove_order($order_id){
		$order_id = (int)$order_id;
		if (!$order_id){
			return false;
		}

		$this->db->query("DELETE FROM `" . $this->db->table("orders") . "` WHERE order_id = '" . $order_id . "'");
		$this->db->query("DELETE FROM `" . $this->db->table("order_history") . "` WHERE order_id = '" . $order_id . "'");
		$this->db->query("DELETE FROM `" . $this->db->table("order_products") . "` WHERE order_id = '" . $order_id . "'");
		$this->db->query("DELETE FROM `" . $this->db->table("order_options") . "` WHERE order_id = '" . $order_id . "'");
		$this->db->query("DELETE FROM `" . $this->db->table("order_downloads") . "` WHERE order_id = '" . $order_id . "'");
		$this->db->query("DELETE FROM `" . $this->db->table("order_totals") . "` WHERE order_id = '" . $order_id . "'");
		$this->db->query("DELETE FROM `" . $this->db->table("order_data") . "` WHERE order_id = '" . $order_id . "'");

		return true;
	}
}