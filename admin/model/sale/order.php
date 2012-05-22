<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
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
class ModelSaleOrder extends Model {
	
	public function addOrder($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "orders`
							SET store_name = '" . $this->db->escape($data['store_name']) . "',
								store_url = '" . $this->db->escape($data['store_url']) . "',
								firstname = '" . $this->db->escape($data['firstname']) . "',
								lastname = '" . $this->db->escape($data['lastname']) . "',
								telephone = '" . $this->db->escape($data['telephone']) . "',
								email = '" . $this->db->escape($data['email']) . "',
								shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "',
								shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "',
								shipping_company = '" . $this->db->escape($data['shipping_company']) . "',
								shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "',
								shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', ip = '" . $this->db->escape('0.0.0.0') . "', total = '" . $this->db->escape(preg_replace("/[^0-9.]/",'', $data['total'])) . "', date_modified = NOW()");
		
		$order_id = $this->db->getLastId();
		
		if (isset($data['product'])) {
			foreach ($data['product'] as $product) {
				if ($product['product_id']) {
					$product_query = $this->db->query("SELECT *
														FROM " . DB_PREFIX . "products p
														LEFT JOIN " . DB_PREFIX . "product_descriptions pd ON (p.product_id = pd.product_id)
														WHERE p.product_id='" . (int)$product['product_id'] . "'");
										
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_products
									SET order_id = '" . (int)$order_id . "',
										product_id = '" . (int)$product['product_id'] . "',
										name = '" . $this->db->escape($product_query->row['name']) . "',
										model = '" . $this->db->escape($product_query->row['model']) . "',
										price = '" . $this->db->escape(preg_replace("/[^0-9.]/",'', $product['price'])) . "',
										total = '" . $this->db->escape(preg_replace("/[^0-9.]/",'', $product['total'])) . "',
										quantity = '" . $this->db->escape($product['quantity']) . "'");
				}
			}
		}
	}
	
	public function editOrder($order_id, $data) {
		$fields = array(
			'telephone',
			'email',
			'shipping_firstname',
			'shipping_lastname',
			'shipping_company',
			'shipping_address_1',
		    'shipping_address_2',
		    'shipping_city',
		    'shipping_postcode',
		    'shipping_zone',
		    'shipping_zone_id',
		    'shipping_country',
		    'shipping_country_id',
		    'payment_firstname',
		    'payment_lastname',
		    'payment_company',
		    'payment_address_1',
		    'payment_address_2',
		    'payment_city',
		    'payment_postcode',
		    'payment_zone',
		    'payment_zone_id',
		    'payment_country',
		    'payment_country_id',
		    'shipping_method',
		    'payment_method',
		    'order_status_id');
		$update = array('date_modified = NOW()');
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = "$f = '".$this->db->escape($data[$f])."'";
		}
		$this->db->query("UPDATE `" . DB_PREFIX . "orders`
						  SET ". implode(',', $update) ."
						  WHERE order_id = '" . (int)$order_id . "'");

        $order = $this->getOrder($order_id);
		if (isset($data['product'])) {
			// first of all delete deleted products
			foreach( $data['product'] as $item ){
				if($item['order_product_id']){
					$order_product_ids[] = $item['order_product_id'];
				}
			}
			$this->db->query("DELETE FROM " . DB_PREFIX . "order_products
							  WHERE order_id = '" . (int)$order_id . "' AND order_product_id NOT IN ('".(implode("','",$order_product_ids)) ."')");

			foreach ($data['product'] as $product) {
				if ($product['product_id']) {
					$exists = $this->db->query( "SELECT product_id
												 FROM " . DB_PREFIX . "order_products
												 WHERE order_id = '" . (int)$order_id . "'
												    AND product_id='" . (int)$product['product_id'] . "'
												    AND order_product_id = '" . (int)$product['order_product_id'] . "'");
					$exists = $exists->num_rows;
					if($exists){
						$this->db->query( "UPDATE " . DB_PREFIX . "order_products
										  SET price = '" . $this->db->escape( (preg_replace("/[^0-9.]/",'', $product['price'])/$order['value']) ) . "',
										  	  total = '" . $this->db->escape( (preg_replace("/[^0-9.]/",'', $product['total'])/$order['value']) ) . "',
											  quantity = '" . $this->db->escape($product['quantity']) . "'
										  WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");
					}else{
						// new products
						$product_query = $this->db->query(
							"SELECT * FROM " . DB_PREFIX . "products p
								LEFT JOIN " . DB_PREFIX . "product_descriptions pd ON (p.product_id = pd.product_id)
							WHERE p.product_id='" . (int)$product['product_id'] . "'");

						$this->db->query(
							"INSERT INTO " . DB_PREFIX . "order_products
							SET order_id = '" . (int)$order_id . "',
								product_id = '" . (int)$product['product_id'] . "',
								name = '" . $this->db->escape($product_query->row['name']) . "',
								model = '" . $this->db->escape($product_query->row['model']) . "',
								price = '" . $this->db->escape( (preg_replace("/[^0-9.]/",'', $product['price'])/$order['value']) ) . "',
								total = '" . $this->db->escape( (preg_replace("/[^0-9.]/",'', $product['total'])/$order['value']) ) . "',
								quantity = '" . $this->db->escape($product['quantity']) . "'");
					}
				}
			}
		}

		if (isset($data['totals'])) {
			ksort($data['totals'],SORT_NUMERIC); // it for to know that total amount is last
			foreach ($data['totals'] as $key => $value) {
				$val = (float)preg_replace("/[^0-9.]/",'', $value);
				$this->db->query("UPDATE " . DB_PREFIX . "order_totals
								  SET `text` = '" . $this->db->escape($value) . "',
								      `value` = '".$val."'
								  WHERE order_total_id = '" . (int)$key . "'");
			}
			// update total in orders table
			$this->db->query("UPDATE " . DB_PREFIX . "orders
							  SET `total` = '".$val."'
							  WHERE order_id = '" . (int)$order_id . "'");

		}
	}
	
	public function deleteOrder($order_id) {
		if ($this->config->get('config_stock_subtract')) {
			$order_query = $this->db->query("SELECT *
											FROM `" . DB_PREFIX . "orders`
											WHERE order_status_id > '0' AND order_id = '" . (int)$order_id . "'");
			
			if ($order_query->num_rows) {
				$product_query = $this->db->query("SELECT *
													FROM " . DB_PREFIX . "order_products
													WHERE order_id = '" . (int)$order_id . "'");
				
				foreach($product_query->rows as $product) {
					$this->db->query(  "UPDATE `" . DB_PREFIX . "products`
										SET quantity = (quantity + " . (int)$product['quantity'] . ")
										WHERE product_id = '" . (int)$product['product_id'] . "'");
					
					$option_query = $this->db->query("SELECT *
														FROM " . DB_PREFIX . "order_options
														WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");
				
					foreach ($option_query->rows as $option) {
						$this->db->query("UPDATE " . DB_PREFIX . "product_option_values
											SET quantity = (quantity + " . (int)$product['quantity'] . ")
											WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
					}				
				}
			}
		}
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "orders` WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_products WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_options WHERE order_id = '" . (int)$order_id . "'");
	  	$this->db->query("DELETE FROM " . DB_PREFIX . "order_downloads WHERE order_id = '" . (int)$order_id . "'");
      	$this->db->query("DELETE FROM " . DB_PREFIX . "order_totals WHERE order_id = '" . (int)$order_id . "'");
	}
	
	public function addOrderHistory($order_id, $data) {
		$this->db->query(  "UPDATE `" . DB_PREFIX . "orders`
							SET order_status_id = '" . (int)$data['order_status_id'] . "',
								date_modified = NOW()
							WHERE order_id = '" . (int)$order_id . "'");

		if ($data['append']) {
      		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history
      		                    SET order_id = '" . (int)$order_id . "',
      		                        order_status_id = '" . (int)$data['order_status_id'] . "',
      		                        notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "',
      		                        comment = '" . $this->db->escape(strip_tags($data['comment'])) . "',
      		                        date_added = NOW()");
		}

      	if ($data['notify']) {
        	$order_query = $this->db->query("SELECT *, os.name AS status
        	                                FROM `" . DB_PREFIX . "orders` o
        	                                LEFT JOIN " . DB_PREFIX . "order_statuses os ON (o.order_status_id = os.order_status_id AND os.language_id = o.language_id)
        	                                LEFT JOIN " . DB_PREFIX . "languages l ON (o.language_id = l.language_id)
        	                                WHERE o.order_id = '" . (int)$order_id . "'");
	    	
			if ($order_query->num_rows) {
				$language = new ALanguage( Registry::getInstance(), $order_query->row['code']);
				$language->load($order_query->row['filename']);
				$language->load('mail/order');

				$this->load->model('setting/store');
				
				$subject = sprintf($language->get('text_subject'), $order_query->row['store_name'], $order_id);
	
				$message  = $language->get('text_order') . ' ' . $order_id . "\n";
				$message .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_query->row['date_added'])) . "\n\n";
				$message .= $language->get('text_order_status') . "\n\n";
				$message .= $order_query->row['status'] . "\n\n";
				$message .= $language->get('text_invoice') . "\n";
				$message .= html_entity_decode($order_query->row['store_url'] . 'index.php?rt=account/invoice&order_id=' . $order_id, ENT_QUOTES, 'UTF-8') . "\n\n";
					
				if ($data['comment']) { 
					$message .= $language->get('text_comment') . "\n\n";
					$message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
				}
					
				$message .= $language->get('text_footer');

				$mail = new AMail( $this->config );
				$mail->setTo($order_query->row['email']);
				$mail->setFrom($this->config->get('store_main_email'));
	    		$mail->setSender($order_query->row['store_name']);
	    		$mail->setSubject($subject);
	    		$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
	    		$mail->send();
			}
		}
	}

	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT *
										 FROM `" . DB_PREFIX . "orders`
										 WHERE order_id = '" . (int)$order_id . "'");
		
		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT *
												FROM `" . DB_PREFIX . "countries`
												WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';				
			}
			
			$zone_query = $this->db->query("SELECT *
											FROM `" . DB_PREFIX . "zones`
											WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}
			
			$country_query = $this->db->query("SELECT *
											   FROM `" . DB_PREFIX . "countries`
											   WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';				
			}
			
			$zone_query = $this->db->query("SELECT *
											FROM `" . DB_PREFIX . "zones`
											WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}
			
			$order_data = array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_id'              => $order_query->row['invoice_id'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'customer_group_id'       => $order_query->row['customer_group_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'email'                   => $order_query->row['email'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],				
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],	
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_method'         => $order_query->row['shipping_method'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],				
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],	
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'language_id'             => $order_query->row['language_id'],
				'currency_id'             => $order_query->row['currency_id'],
				'currency'                => $order_query->row['currency'],
				'value'                   => $order_query->row['value'],
				'coupon_id'               => $order_query->row['coupon_id'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'ip'                      => $order_query->row['ip']
			);
			
			return $order_data;
		} else {
			return FALSE;	
		}
	}
	
	public function getOrders($data = array()) {
		$sql = "SELECT o.order_id,
						CONCAT(o.firstname, ' ', o.lastname) AS name,
						(SELECT os.name
						 FROM " . DB_PREFIX . "order_statuses os
						 WHERE os.order_status_id = o.order_status_id
						    AND os.language_id = '" . (int)$this->config->get('storefront_language_id') . "') AS status,
						 o.date_added,
						 o.total,
						 o.currency,
						 o.value
			    FROM `" . DB_PREFIX . "orders` o";
		
		if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}
		
		if (isset($data['filter_order_id']) && !is_null($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%' collate utf8_general_ci";
		}
		
		if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (isset($data['filter_total']) && !is_null($data['filter_total'])) {
			$data['filter_total'] = (float)$data['filter_total'];
			$currencies = $this->currency->getCurrencies();
			$temp = array($data['filter_total']);
			foreach( $currencies  as $currency1){
				foreach( $currencies  as $currency2){
					if($currency1['code']!=$currency2['code']){
						$temp[] = round($this->currency->convert($data['filter_total'], $currency1['code'],$currency2['code']),0);
					}
				}
			}
			$sql .= " AND ( ROUND(o.total,0) IN  (" . implode(",",$temp) . ") OR ROUND(CAST(o.total as DECIMAL(15,4)) * CAST(o.value as DECIMAL(15,4)),0) IN  (" . implode(",",$temp) . ") )";

		}

		$sort_data = array( 'o.order_id',
							'name',
							'status',
							'o.date_added',
							'o.total');
			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY o.order_id";	
		}
			
		if (isset($data['order']) && (strtoupper($data['order']) == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	
			
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
		
		return $query->rows;
	}	
	
	public function generateInvoiceId($order_id) {
		$query = $this->db->query("SELECT MAX(invoice_id) AS invoice_id FROM `" . DB_PREFIX . "orders`");
		
		if ($query->row['invoice_id']) {
			$invoice_id = (int)$query->row['invoice_id'] + 1;
		} elseif ($this->config->get('starting_invoice_id')) {
			$invoice_id = $this->config->get('starting_invoice_id');
		} else {
			$invoice_id = 1;
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "orders`
							SET invoice_id = '" . (int)$invoice_id . "',
								invoice_prefix = '" . $this->db->escape($this->config->get('invoice_prefix')) . "',
								date_modified = NOW()
							WHERE order_id = '" . (int)$order_id . "'");
		
		return $this->config->get('invoice_prefix') . $invoice_id;
	}
	
	public function getOrderProducts($order_id) {
		$query = $this->db->query( "SELECT *
									FROM " . DB_PREFIX . "order_products
									WHERE order_id = '" . (int)$order_id . "'");
	
		return $query->rows;
	}

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "order_options
									WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
	
		return $query->rows;
	}
	
	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "order_totals
									WHERE order_id = '" . (int)$order_id . "'
									ORDER BY sort_order");
	
		return $query->rows;
	}	

	public function getOrderHistory($order_id) { 
		$query = $this->db->query("SELECT oh.date_added,
										os.name AS status,
										oh.comment,
										oh.notify
									FROM " . DB_PREFIX . "order_history oh
									LEFT JOIN " . DB_PREFIX . "order_statuses os ON oh.order_status_id = os.order_status_id
									WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('storefront_language_id') . "'
									ORDER BY oh.date_added");
	
		return $query->rows;
	}	

	public function getOrderDownloads($order_id) {
		$query = $this->db->query("SELECT *
								   FROM " . DB_PREFIX . "order_downloads
								   WHERE order_id = '" . (int)$order_id . "'
								   ORDER BY name");
	
		return $query->rows; 
	}	
				
	public function getTotalOrders($data = array()) {
      	$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "orders`";

		if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
			$sql .= " WHERE order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE order_status_id > '0'";
		}
		
		if (isset($data['filter_order_id']) && !is_null($data['filter_order_id'])) {
			$sql .= " AND order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$sql .= " AND CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		
		if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		
		if (isset($data['filter_total']) && !is_null($data['filter_total'])) {
			$sql .= " AND total >= " . (float)$data['filter_total'] . " ";
		}

		$query = $this->db->query($sql);
		return $query->row['total'];
	} 

	public function getTotalOrdersByStoreId($store_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM `" . DB_PREFIX . "orders`
      	                            WHERE store_id = '" . (int)$store_id . "'");
		
		return $query->row['total'];
	}
	
	public function getOrderHistoryTotalByOrderStatusId($order_status_id) {
	  	$query = $this->db->query("SELECT oh.order_id
	  	                            FROM " . DB_PREFIX . "order_history oh
	  	                            LEFT JOIN `" . DB_PREFIX . "orders` o ON (oh.order_id = o.order_id)
	  	                            WHERE oh.order_status_id = '" . (int)$order_status_id . "' AND o.order_status_id > '0'
	  	                            GROUP BY order_id");

		return $query->num_rows;
	}

	public function getTotalOrdersByOrderStatusId($order_status_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM `" . DB_PREFIX . "orders`
      	                            WHERE order_status_id = '" . (int)$order_status_id . "' AND order_status_id > '0'");
		
		return $query->row['total'];
	}
	
	public function getTotalOrdersByLanguageId($language_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM `" . DB_PREFIX . "orders`
      	                            WHERE language_id = '" . (int)$language_id . "' AND order_status_id > '0'");
		
		return $query->row['total'];
	}	
	
	public function getTotalOrdersByCurrencyId($currency_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM `" . DB_PREFIX . "orders`
      	                            WHERE currency_id = '" . (int)$currency_id . "' AND order_status_id > '0'");
		
		return $query->row['total'];
	}	
	
	public function getTotalSales() {
      	$query = $this->db->query("SELECT SUM(total) AS total
      	                            FROM `" . DB_PREFIX . "orders`
      	                            WHERE order_status_id > '0'");
		
		return $query->row['total'];
	}
	
	public function getTotalSalesByYear($year) {
      	$query = $this->db->query("SELECT SUM(total) AS total
      	                            FROM `" . DB_PREFIX . "orders`
      	                            WHERE order_status_id > '0' AND YEAR(date_added) = '" . (int)$year . "'");
		
		return $query->row['total'];
	}	
}
?>