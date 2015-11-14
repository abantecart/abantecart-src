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
if(!defined('DIR_CORE') || !IS_ADMIN){
	header('Location: static_pages/');
}

/**
 * Class ModelSaleOrder
 */
class ModelSaleOrder extends Model{
	/**
	 * @param array $data
	 */
	public function addOrder($data){
		//encrypt order data
		$key_sql = '';
		if($this->dcrypt->active){
			$data = $this->dcrypt->encrypt_data($data, 'orders');
			$key_sql = ", key_id = '" . (int)$data['key_id'] . "'";
		}
		$this->db->query("INSERT INTO `" . $this->db->table("orders") . "`
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
								shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "',
								shipping_city = '" . $this->db->escape($data['shipping_city']) . "',
								shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "',
								shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "',
								shipping_country = '" . $this->db->escape($data['shipping_country']) . "',
								shipping_country_id = '" . (int)$data['shipping_country_id'] . "',
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
								ip = '" . $this->db->escape('0.0.0.0') . "',
								total = '" . $this->db->escape(preformatFloat($data['total'], $this->language->get('decimal_point'))) . "'" . $key_sql . ",
								date_modified = NOW()");

		$order_id = $this->db->getLastId();

		if(isset($data['product'])){
			foreach($data['product'] as $product){
				if($product['product_id']){
					$product_query = $this->db->query("SELECT *, p.product_id
														FROM " . $this->db->table("products") . " p
														LEFT JOIN " . $this->db->table("product_descriptions") . " pd ON (p.product_id = pd.product_id)
														WHERE p.product_id='" . (int)$product['product_id'] . "'");

					$this->db->query("INSERT INTO " . $this->db->table("order_products") . "
									SET order_id = '" . (int)$order_id . "',
										product_id = '" . (int)$product['product_id'] . "',
										name = '" . $this->db->escape($product_query->row['name']) . "',
										model = '" . $this->db->escape($product_query->row['model']) . "',
										price = '" . $this->db->escape(preformatFloat($product['price'], $this->language->get('decimal_point'))) . "',
										total = '" . $this->db->escape(preformatFloat($product['total'], $this->language->get('decimal_point'))) . "',
										quantity = '" . $this->db->escape($product['quantity']) . "'");
				}
			}
		}
	}

	/**
	 * @param int $order_id
	 * @param array $data
	 * @return int
	 */
	public function addOrderTotal($order_id, $data){
		if (!has_value($order_id)) {
			return null;
		}
		$value = preformatFloat($data['text'], $this->language->get('decimal_point'));
		//check if number is negative. disount
		if ( preg_match("/^\s*-/", $data['text']) ) {
			$value *= -1;
		}

		$this->db->query("INSERT INTO " . $this->db->table("order_totals") . "
		    			SET `order_id` = '" . (int)$order_id . "',
		    				`title` = '" . $this->db->escape($data['title']) . "',
		    				`text` = '" . $this->db->escape($data['text']) . "',
		    				`value` = '" . $this->db->escape($value) . "',
		    				`sort_order` = '" . (int)$data['sort_order'] . "',
		    				`type` = '" . $this->db->escape($data['type']) . "',
		    				`key` = '" . $this->db->escape($data['key']) . "'"
		    			);
	
		return $this->db->getLastId();
	}

	/**
	 * @param int $order_id
	 * @param int $order_total_id
	 * @return int|null
	 */
	public function deleteOrderTotal($order_id, $order_total_id){
		if (!has_value($order_id) && !has_value($order_total_id)) {
			return null;
		}
		
		$this->db->query("DELETE FROM " . $this->db->table("order_totals") . "
							  WHERE order_id = '" . (int)$order_id . "' AND order_total_id = '" . (int)$order_total_id . "'");

		return true;
	}
	

	/**
	 * @param int $order_id
	 * @param array $data
	 */
	public function editOrder($order_id, $data){
		$fields = array(
				'telephone',
				'email',
				'fax',
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
				'order_status_id',
				'key_id'
		);
		$update = array('date_modified = NOW()');

		if($this->dcrypt->active){
			//encrypt order data
			//check key_id to use from existing record
			$query_key = $this->db->query("select key_id from " . $this->db->table("orders") . "
							  WHERE order_id = '" . (int)$order_id . "'");
			$data['key_id'] = $query_key->rows[0]['key_id'];
			$data = $this->dcrypt->encrypt_data($data, 'orders');
			$fields[] = 'key_id';
		}

		foreach($fields as $f){
			if(isset($data[$f])){
				$update[] = $f . " = '" . $this->db->escape($data[$f]) . "'";
			}
		}

		$this->db->query("UPDATE `" . $this->db->table("orders") . "`
						  SET " . implode(',', $update) . "
						  WHERE order_id = '" . (int)$order_id . "'");

		$order = $this->getOrder($order_id);
		if(isset($data['product'])){
			// first of all delete removed products
			foreach($data['product'] as $item){
				if($item['order_product_id']){
					$order_product_ids[] = $item['order_product_id'];
				}
			}
			$this->db->query("DELETE FROM " . $this->db->table("order_products") . "
							  WHERE order_id = '" . (int)$order_id . "' AND order_product_id NOT IN ('" . (implode("','", $order_product_ids)) . "')");

			foreach($data['product'] as $product){
				if($product['product_id']){
					$exists = $this->db->query("SELECT product_id
												 FROM " . $this->db->table("order_products") . "
												 WHERE order_id = '" . (int)$order_id . "'
												    AND product_id='" . (int)$product['product_id'] . "'
												    AND order_product_id = '" . (int)$product['order_product_id'] . "'");
					$exists = $exists->num_rows;
					if($exists){
						$this->db->query("UPDATE " . $this->db->table("order_products") . "
										  SET price = '" . $this->db->escape((preformatFloat($product['price'], $this->language->get('decimal_point')) / $order['value'])) . "',
										  	  total = '" . $this->db->escape((preformatFloat($product['total'], $this->language->get('decimal_point')) / $order['value'])) . "',
											  quantity = '" . $this->db->escape($product['quantity']) . "'
										  WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");
					} else{
						// new products
						$product_query = $this->db->query(
								"SELECT *, p.product_id
							 FROM " . $this->db->table("products") . " p
							 LEFT JOIN " . $this->db->table("product_descriptions") . " pd ON (p.product_id = pd.product_id)
							 WHERE p.product_id='" . (int)$product['product_id'] . "'");

						$this->db->query(
								"INSERT INTO " . $this->db->table("order_products") . "
							SET order_id = '" . (int)$order_id . "',
								product_id = '" . (int)$product['product_id'] . "',
								name = '" . $this->db->escape($product_query->row['name']) . "',
								model = '" . $this->db->escape($product_query->row['model']) . "',
								price = '" . $this->db->escape((preformatFloat($product['price'], $this->language->get('decimal_point')) / $order['value'])) . "',
								total = '" . $this->db->escape((preformatFloat($product['total'], $this->language->get('decimal_point')) / $order['value'])) . "',
								quantity = '" . $this->db->escape($product['quantity']) . "'");
					}
				}
			}
		}

		if(isset($data['totals'])){		
		//TODO: Improve, not to rely on text value. Add 2 parameters for total, text_val and number. 
			foreach($data['totals'] as $total_id => $text_value){
				//get number portion together with the sign
				$number = preformatFloat($text_value, $this->language->get('decimal_point'));
				$this->db->query("UPDATE " . $this->db->table("order_totals") . "
								  SET `text` = '" . $this->db->escape($text_value) . "',
								      `value` = '" . $number . "'
								  WHERE order_total_id = '" . (int)$total_id . "'");
			}
			// update total in order main table reading back from all totals and select key 'total'
			$totals = $this->getOrderTotals($order_id);			
			if($totals){
				foreach($totals as $total_id => $t_data) {
					if( $t_data['key'] == 'total' ){
						$this->db->query("UPDATE " . $this->db->table("orders") . "
								  SET `total` = '" . $t_data['value'] . "'
								  WHERE order_id = '" . (int)$order_id . "'");
						break;			
					}
				}
			}
		}
	}

	/**
	 * @param int $order_id
	 * @param array $data
	 * @return bool
	 */
	public function editOrderProduct($order_id, $data){

		$order_id = (int)$order_id;
		$order_product_id = (int)$data['order_product_id'];
		$product_id = (int)$data['product_id'];

		if(!$product_id || !$order_id){
			return false;
		}

		$this->load->model('catalog/product');
		$product_info = $this->model_catalog_product->getProduct($product_id);

		$order_info = $this->getOrder($order_id);

		$elements_with_options = HtmlElementFactory::getElementsWithOptions();

		if(isset($data['product'])){

			foreach($data['product'] as $product){
				if($product['quantity'] <= 0){ // stupid situation
					return false;
				}
				//check is product exists
				$exists = $this->db->query("SELECT op.product_id, op.quantity
												 FROM " . $this->db->table("order_products") . " op
												 WHERE op.order_id = '" . (int)$order_id . "'
													    AND op.product_id='" . (int)$product_id . "'
													    AND op.order_product_id = '" . (int)$order_product_id . "'");

				if($exists->num_rows){
					//update order quantity
					$this->db->query("UPDATE " . $this->db->table("order_products") . "
										  SET price = '" . $this->db->escape((preformatFloat($product['price'], $this->language->get('decimal_point')) / $order_info['value'])) . "',
										  	  total = '" . $this->db->escape((preformatFloat($product['total'], $this->language->get('decimal_point')) / $order_info['value'])) . "',
											  quantity = '" . $this->db->escape($product['quantity']) . "'
										  WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
					//update stock quantity

					$old_qnt = $exists->row['quantity'];
					$stock_qnt = $product_info['quantity'];
					$qnt_diff = $old_qnt - $product['quantity'];

					if($qnt_diff != 0){
						if($qnt_diff < 0){
							$new_qnt = $stock_qnt - abs($qnt_diff);
						} else{
							$new_qnt = $stock_qnt + $qnt_diff;
						}
						if($product_info['subtract']){
							$this->db->query("UPDATE " . $this->db->table("products") . "
											  SET quantity = '" . $new_qnt . "'
											  WHERE product_id = '" . (int)$product_id . "' AND subtract = 1");
						}
					}

				} else{
					// add new product into order
					$product_query = $this->db->query(
							"SELECT *, p.product_id
							 FROM " . $this->db->table("products") . " p
							 LEFT JOIN " . $this->db->table("product_descriptions") . " pd
							    ON (p.product_id = pd.product_id AND pd.language_id=" . $this->language->getContentLanguageID() . ")
							 WHERE p.product_id='" . (int)$product_id . "'");

					$this->db->query(
							"INSERT INTO " . $this->db->table("order_products") . "
							SET order_id = '" . (int)$order_id . "',
								product_id = '" . (int)$product_id . "',
								name = '" . $this->db->escape($product_query->row['name']) . "',
								model = '" . $this->db->escape($product_query->row['model']) . "',
								price = '" . $this->db->escape((preformatFloat($product['price'], $this->language->get('decimal_point')) / $order_info['value'])) . "',
								total = '" . $this->db->escape((preformatFloat($product['total'], $this->language->get('decimal_point')) / $order_info['value'])) . "',
								quantity = '" . (int)$product['quantity'] . "'");
					$order_product_id = $this->db->getLastId();

					//update stock quantity
					$qnt_diff = -$product['quantity'];
					$stock_qnt = $product_query->row['quantity'];
					$new_qnt = $stock_qnt - (int)$product['quantity'];

					if($product_info['subtract']){
						$this->db->query("UPDATE " . $this->db->table("products") . "
										  SET quantity = '" . $new_qnt . "'
										  WHERE product_id = '" . (int)$product_id . "' AND subtract = 1");
					}
				}


				if($product['option']){
					//first of all find previous order options
					// if empty result - order products just added
					$order_product_options = $this->getOrderOptions($order_id, $order_product_id);

					$prev_subtract_options = array(); //array with previous option values with enabled stock tracking
					foreach($order_product_options as $old_value){
							if(!$old_value['subtract']){ continue; }
							$prev_subtract_options[(int)$old_value['product_option_id']][] = (int)$old_value['product_option_value_id'];
					}


					$po_ids = array();
					foreach($product['option'] as $k => $option){
						$po_ids[] = (int)$k;
					}
					//get all data of given product options from db
					$sql = "SELECT *, pov.product_option_value_id, povd.name as option_value_name, pod.name as option_name
								FROM " . $this->db->table('product_options') . " po
								LEFT JOIN " . $this->db->table('product_option_descriptions') . " pod
									ON (pod.product_option_id = po.product_option_id AND pod.language_id=" . $this->language->getContentLanguageID() . ")
								LEFT JOIN " . $this->db->table('product_option_values') . " pov
									ON po.product_option_id = pov.product_option_id
								LEFT JOIN " . $this->db->table('product_option_value_descriptions') . " povd
								    ON (povd.product_option_value_id = pov.product_option_value_id AND povd.language_id=" . $this->language->getContentLanguageID() . ")
								WHERE po.product_option_id IN (" . implode(',', $po_ids) . ")
								ORDER BY po.product_option_id";

					$result = $this->db->query($sql);

					$exclude_list = array(); //list of option value that we do not resave
					$option_value_info = array();
					foreach($result->rows as $row){
						//skip files
						if(in_array($row['element_type'], array('U'))){
							$exclude_list[] = (int)$row['product_option_value_id'];
						}
						$option_value_info[$row['product_option_id'] . '_' . $row['product_option_value_id']] = $row; //compond key for cases when val_id is null
						$option_types[$row['product_option_id']] = $row['element_type'];
					}

					//delete old options and then insert new
					$sql = "DELETE FROM " . $this->db->table('order_options') . "
								WHERE order_id = " . $order_id . " AND order_product_id=" . (int)$order_product_id;
					if($exclude_list){
						$sql .= " AND product_option_value_id NOT IN (" . implode(', ', $exclude_list) . ")";
					}

					$this->db->query($sql);

					foreach($product['option'] as $opt_id => $values){

						if(!is_array($values)){ // for non-multioptional elements
							//do not save empty inputs and texareas
							if(in_array($option_types[$opt_id], array('I','T')) && $values==''){
								continue;
							}elseif($option_types[$opt_id] == 'S'){
								$values = array($values);
							} else{
								foreach($option_value_info as $o){
									if($o['product_option_id'] == $opt_id){
										if(!in_array($option_types[$opt_id], $elements_with_options)){
											$option_value_info[$o['product_option_id'] . '_' . $o['product_option_value_id']]['option_value_name'] = $values;
										}
										$values = array($o['product_option_value_id']);
										break;
									}
								}
							}
						}


						foreach($values as $value){
							$arr_key = $opt_id . '_' . $value;
							$sql = "INSERT INTO " . $this->db->table('order_options') . "
											(`order_id`,
											`order_product_id`,
											`product_option_value_id`,
											`name`,
											`value`,
											`price`,
											`prefix`)
										VALUES	('" . $order_id . "',
												'" . (int)$order_product_id . "',
												'" . (int)$value . "',
												'" . $this->db->escape($option_value_info[$arr_key]['option_name']) . "',
												'" . $this->db->escape($option_value_info[$arr_key]['option_value_name']) . "',
												'" . $this->db->escape($option_value_info[$arr_key]['price']) . "',
												'" . $this->db->escape($option_value_info[$arr_key]['prefix']) . "')";

							$this->db->query($sql);

							if($option_value_info[$arr_key]['subtract']){
								$curr_subtract_options[(int)$opt_id][] = (int)$value;
							}
						}

						//reduce product quantity for option value that not assigned to product anymore
						$prev_arr = has_value($prev_subtract_options[$opt_id]) ? $prev_subtract_options[$opt_id] : array();
						$curr_arr = has_value($curr_subtract_options[$opt_id]) ? $curr_subtract_options[$opt_id] : array();

						if($prev_arr || $curr_arr){

							//increase qnt for old option values
							foreach($prev_arr as $v){
								if(!in_array($v, $curr_arr)){

									$sql = "UPDATE " . $this->db->table("product_option_values") . "
										  SET quantity = (quantity + " . $product['quantity'] . ")
										  WHERE product_option_value_id = '" . (int)$v . "'
										        AND subtract = 1";

									$this->db->query($sql);
								}
							}

							//decrease qnt for new option values
							foreach($curr_arr as $v){
								if(!in_array($v, $prev_arr)){
									$sql = "UPDATE " . $this->db->table("product_option_values") . "
										  SET quantity = (quantity - " . $product['quantity'] . ")
										  WHERE product_option_value_id = '" . (int)$v . "'
										        AND subtract = 1";

									$this->db->query($sql);
								}
							}

							//if qnt changed for the same option values
							$intersect = array_intersect($curr_arr, $prev_arr);
							if($intersect && $qnt_diff != 0){
								if($qnt_diff < 0){
									$sql_incl = "(quantity - " . abs($qnt_diff) . ")";
								} else{
									$sql_incl = "(quantity + " . abs($qnt_diff) . ")";
								}
								foreach($intersect as $v){
									$sql = "UPDATE " . $this->db->table("product_option_values") . "
										  SET quantity = " . $sql_incl . "
										  WHERE product_option_value_id = '" . (int)$v . "'
										        AND subtract = 1";
									$this->db->query($sql);
								}
							}
						}
					}
				}//end processing options

			}
		}

		//fix order total and subtotal

		$sql = "SELECT SUM(total) as subtotal
				FROM " . $this->db->table('order_products') . "
				WHERE order_id=" . $order_id;
		$result = $this->db->query($sql);
		$subtotal = $result->row['subtotal'];
		$text = $this->currency->format($subtotal, $order_info['currency'], $order_info['value']);

		$sql = "UPDATE " . $this->db->table('order_totals') . "
		        SET `value`='" . $subtotal . "', `text` = '" . $text . "'
				WHERE order_id=" . $order_id . " AND type='subtotal'";
		$this->db->query($sql);

		$sql = "SELECT SUM(`value`) as total
				FROM " . $this->db->table('order_totals') . "
				WHERE order_id=" . $order_id . " AND type<>'total'";
		$result = $this->db->query($sql);
		$total = $result->row['total'];
		$text = $this->currency->format($total, $order_info['currency'], $order_info['value']);

		$sql = "UPDATE " . $this->db->table('order_totals') . "
		        SET `value`='" . $subtotal . "', `text` = '" . $text . "'
				WHERE order_id=" . $order_id . " AND type='total'";
		$this->db->query($sql);

		$this->cache->delete('product');

	}


	/**
	 * @param int $order_id
	 */
	public function deleteOrder($order_id){
		if($this->config->get('config_stock_subtract')){
			$order_query = $this->db->query("SELECT *
											FROM `" . $this->db->table("orders") . "`
											WHERE order_status_id > '0' AND order_id = '" . (int)$order_id . "'");

			if($order_query->num_rows){
				$product_query = $this->db->query("SELECT *
													FROM " . $this->db->table("order_products") . "
													WHERE order_id = '" . (int)$order_id . "'");

				foreach($product_query->rows as $product){
					$this->db->query("UPDATE `" . $this->db->table("products") . "`
										SET quantity = (quantity + " . (int)$product['quantity'] . ")
										WHERE product_id = '" . (int)$product['product_id'] . "'");

					$option_query = $this->db->query("SELECT *
														FROM " . $this->db->table("order_options") . "
														WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

					foreach($option_query->rows as $option){
						$this->db->query("UPDATE " . $this->db->table("product_option_values") . "
											SET quantity = (quantity + " . (int)$product['quantity'] . ")
											WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM `" . $this->db->table("orders") . "` WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("order_history") . " WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("order_products") . " WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("order_options") . " WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("order_downloads") . " WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("order_totals") . " WHERE order_id = '" . (int)$order_id . "'");
	}

	/**
	 * @param int $order_id
	 * @param array $data
	 * @throws AException
	 */
	public function addOrderHistory($order_id, $data){
		$this->db->query("UPDATE `" . $this->db->table("orders") . "`
							SET order_status_id = '" . (int)$data['order_status_id'] . "',
								date_modified = NOW()
							WHERE order_id = '" . (int)$order_id . "'");

		if($data['append']){
			$this->db->query("INSERT INTO " . $this->db->table("order_history") . "
      		                    SET order_id = '" . (int)$order_id . "',
      		                        order_status_id = '" . (int)$data['order_status_id'] . "',
      		                        notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "',
      		                        comment = '" . $this->db->escape(strip_tags($data['comment'])) . "',
      		                        date_added = NOW()");
		}

		if($data['notify']){
			$order_query = $this->db->query("SELECT *, os.name AS status
        	                                FROM `" . $this->db->table("orders") . "` o
        	                                LEFT JOIN " . $this->db->table("order_statuses") . " os ON (o.order_status_id = os.order_status_id AND os.language_id = o.language_id)
        	                                LEFT JOIN " . $this->db->table("languages") . " l ON (o.language_id = l.language_id)
        	                                WHERE o.order_id = '" . (int)$order_id . "'");

			if($order_query->num_rows){
				//load language specific for the order in admin section 
				$language = new ALanguage(Registry::getInstance(), $order_query->row['code'], 1);
				$language->load($order_query->row['filename']);
				$language->load('mail/order');

				$this->load->model('setting/store');

				$subject = sprintf($language->get('text_subject'), $order_query->row['store_name'], $order_id);

				$message = $language->get('text_order') . ' ' . $order_id . "\n";
				$message .= $language->get('text_date_added') . ' ' . dateISO2Display($order_query->row['date_added'], $language->get('date_format_short')) . "\n\n";
				$message .= $language->get('text_order_status') . "\n\n";
				$message .= $order_query->row['status'] . "\n\n";
				//send link to order only for registered custemers
				if($order_query->row['customer_id']){
					$message .= $language->get('text_invoice') . "\n";
					$message .= html_entity_decode($order_query->row['store_url'] . 'index.php?rt=account/invoice&order_id=' . $order_id, ENT_QUOTES, 'UTF-8') . "\n\n";
				}
				//give link on order page for quest
				elseif($this->config->get('config_guest_checkout') && $order_query->row['email']){
					$order_token = AEncryption::mcrypt_encode($order_id.'~~~'.$order_query->row['email']);
					if($order_token){
						$message .= $language->get('text_invoice') . "\n";
						$message .= html_entity_decode($order_query->row['store_url'] . 'index.php?rt=account/invoice&ot=' . $order_token, ENT_QUOTES, 'UTF-8') . "\n\n";
					}
				}

				if($data['comment']){
					$message .= $language->get('text_comment') . "\n\n";
					$message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
				}

				$message .= $language->get('text_footer');

				if($this->dcrypt->active){
					$customer_email = $this->dcrypt->decrypt_field($order_query->row['email'], $order_query->row['key_id']);
				} else{
					$customer_email = $order_query->row['email'];
				}

				$mail = new AMail($this->config);
				$mail->setTo($customer_email);
				$mail->setFrom($this->config->get('store_main_email'));
				$mail->setSender($order_query->row['store_name']);
				$mail->setSubject($subject);
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$mail->send();
			}
		}
	}

	/**
	 * @param $order_id
	 * @return array|bool
	 * @throws AException
	 */
	public function getOrder($order_id){
		$order_query = $this->db->query("SELECT *
										 FROM `" . $this->db->table("orders") . "`
										 WHERE order_id = '" . (int)$order_id . "'");

		if($order_query->num_rows){
			//Decrypt order data
			$order_row = $this->dcrypt->decrypt_data($order_query->row, 'orders');

			$this->load->model('localisation/country');
			$this->load->model('localisation/zone');
			$country_row = $this->model_localisation_country->getCountry($order_row['shipping_country_id']);
			if($country_row){
				$shipping_iso_code_2 = $country_row['iso_code_2'];
				$shipping_iso_code_3 = $country_row['iso_code_3'];
			} else{
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_row = $this->model_localisation_zone->getZone($order_row['shipping_zone_id']);
			if($zone_row){
				$shipping_zone_code = $zone_row['code'];
			} else{
				$shipping_zone_code = '';
			}

			$country_row = $this->model_localisation_country->getCountry($order_row['payment_country_id']);
			if($country_row){
				$payment_iso_code_2 = $country_row['iso_code_2'];
				$payment_iso_code_3 = $country_row['iso_code_3'];
			} else{
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_row = $this->model_localisation_zone->getZone($order_row['payment_zone_id']);
			if($zone_row){
				$payment_zone_code = $zone_row['code'];
			} else{
				$payment_zone_code = '';
			}

			$order_data = array(
					'order_id'                => $order_row['order_id'],
					'invoice_id'              => $order_row['invoice_id'],
					'invoice_prefix'          => $order_row['invoice_prefix'],
					'store_id'                => $order_row['store_id'],
					'store_name'              => $order_row['store_name'],
					'store_url'               => $order_row['store_url'],
					'customer_id'             => $order_row['customer_id'],
					'customer_group_id'       => $order_row['customer_group_id'],
					'firstname'               => $order_row['firstname'],
					'lastname'                => $order_row['lastname'],
					'telephone'               => $order_row['telephone'],
					'fax'                     => $order_row['fax'],
					'email'                   => $order_row['email'],
					'shipping_firstname'      => $order_row['shipping_firstname'],
					'shipping_lastname'       => $order_row['shipping_lastname'],
					'shipping_company'        => $order_row['shipping_company'],
					'shipping_address_1'      => $order_row['shipping_address_1'],
					'shipping_address_2'      => $order_row['shipping_address_2'],
					'shipping_postcode'       => $order_row['shipping_postcode'],
					'shipping_city'           => $order_row['shipping_city'],
					'shipping_zone_id'        => $order_row['shipping_zone_id'],
					'shipping_zone'           => $order_row['shipping_zone'],
					'shipping_zone_code'      => $shipping_zone_code,
					'shipping_country_id'     => $order_row['shipping_country_id'],
					'shipping_country'        => $order_row['shipping_country'],
					'shipping_iso_code_2'     => $shipping_iso_code_2,
					'shipping_iso_code_3'     => $shipping_iso_code_3,
					'shipping_address_format' => $order_row['shipping_address_format'],
					'shipping_method'         => $order_row['shipping_method'],
					'shipping_method_key'     => $order_row['shipping_method_key'],
					'payment_firstname'       => $order_row['payment_firstname'],
					'payment_lastname'        => $order_row['payment_lastname'],
					'payment_company'         => $order_row['payment_company'],
					'payment_address_1'       => $order_row['payment_address_1'],
					'payment_address_2'       => $order_row['payment_address_2'],
					'payment_postcode'        => $order_row['payment_postcode'],
					'payment_city'            => $order_row['payment_city'],
					'payment_zone_id'         => $order_row['payment_zone_id'],
					'payment_zone'            => $order_row['payment_zone'],
					'payment_zone_code'       => $payment_zone_code,
					'payment_country_id'      => $order_row['payment_country_id'],
					'payment_country'         => $order_row['payment_country'],
					'payment_iso_code_2'      => $payment_iso_code_2,
					'payment_iso_code_3'      => $payment_iso_code_3,
					'payment_address_format'  => $order_row['payment_address_format'],
					'payment_method'          => $order_row['payment_method'],
					'payment_method_key'      => $order_row['payment_method_key'],
					'comment'                 => $order_row['comment'],
					'total'                   => $order_row['total'],
					'order_status_id'         => $order_row['order_status_id'],
					'language_id'             => $order_row['language_id'],
					'currency_id'             => $order_row['currency_id'],
					'currency'                => $order_row['currency'],
					'value'                   => $order_row['value'],
					'coupon_id'               => $order_row['coupon_id'],
					'date_modified'           => $order_row['date_modified'],
					'date_added'              => $order_row['date_added'],
					'ip'                      => $order_row['ip']
			);

			if(has_value($order_row['payment_method_data'])){
				$order_data['payment_method_data'] = $order_row['payment_method_data'];
			}

			return $order_data;
		} else{
			return false;
		}
	}

	/**
	 * @param array $data
	 * @param string $mode
	 * @return array
	 */
	public function getOrders($data = array(), $mode = 'default'){
		$language_id = $this->language->getLanguageID();

		if($data['store_id']){
			$store_id = (int)$data['store_id'];
		} else{
			$store_id = (int)$this->config->get('config_store_id');
		}

		if($mode == 'total_only'){
			$total_sql = 'count(*) as total';
		} else{
			$total_sql = "o.order_id,
						CONCAT(o.firstname, ' ', o.lastname) AS name,
						(SELECT os.name
						 FROM " . $this->db->table("order_statuses") . " os
						 WHERE os.order_status_id = o.order_status_id
						    AND os.language_id = '" . (int)$language_id . "') AS status,
						 o.order_status_id,
						 o.date_added,
						 o.total,
						 o.currency,
						 o.value";
		}

		$sql = "SELECT " . $total_sql . "
			    FROM `" . $this->db->table("orders") . "` o";

		if(has_value($data['filter_product_id'])){
			$sql .= " LEFT JOIN  `" . $this->db->table("order_products") . "` op ON o.order_id = op.order_id ";
		}

		if($data['filter_order_status_id'] == 'all'){
			$sql .= " WHERE o.order_status_id >= 0";
		} else if(has_value($data['filter_order_status_id'])){
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else{
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if(has_value($data['filter_product_id'])){
			$sql .= " AND op.product_id = '" . (int)$data['filter_product_id'] . "'";
		}
		if(has_value($data['filter_coupon_id'])){
			$sql .= " AND o.coupon_id = '" . (int)$data['filter_coupon_id'] . "'";
		}

		if(has_value($data['filter_customer_id'])){
			$sql .= " AND o.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}

		if(has_value($data['filter_order_id'])){
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if(has_value($data['filter_name'])){
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%' collate utf8_general_ci";
		}

		if(has_value($data['filter_date_added'])){
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if(has_value($store_id)){
			$sql .= " AND o.store_id = '" . $store_id . "'";
		}

		if(has_value($data['filter_total'])){
			$data['filter_total'] = trim($data['filter_total']);
			//check if compare signs are used in the request 
			$compare = '';
			if(in_array(substr($data['filter_total'], 0, 2), array('>=', '<='))){
				$compare = substr($data['filter_total'], 0, 2);
				$data['filter_total'] = substr($data['filter_total'], 2, strlen($data['filter_total']));
				$data['filter_total'] = trim($data['filter_total']);
			} else if(in_array(substr($data['filter_total'], 0, 1), array('>', '<', '='))){
				$compare = substr($data['filter_total'], 0, 1);
				$data['filter_total'] = substr($data['filter_total'], 1, strlen($data['filter_total']));
				$data['filter_total'] = trim($data['filter_total']);
			}

			$data['filter_total'] = (float)$data['filter_total'];
			//if we compare, easier select
			if($compare){
				$sql .= " AND FLOOR(CAST(o.total as DECIMAL(15,4))) " . $compare . "  FLOOR(CAST(" . $data['filter_total'] . " as DECIMAL(15,4)))";
			} else{
				$currencies = $this->currency->getCurrencies();
				$temp = $temp2 = array($data['filter_total'], ceil($data['filter_total']), floor($data['filter_total']));
				foreach($currencies as $currency1){
					foreach($currencies as $currency2){
						if($currency1['code'] != $currency2['code']){
							$temp[] = floor($this->currency->convert($data['filter_total'], $currency1['code'], $currency2['code']));
							$temp2[] = ceil($this->currency->convert($data['filter_total'], $currency1['code'], $currency2['code']));
						}
					}
				}
				$sql .= " AND ( FLOOR(o.total) IN  (" . implode(",", $temp) . ")
								OR FLOOR(CAST(o.total as DECIMAL(15,4)) * CAST(o.value as DECIMAL(15,4))) IN  (" . implode(",", $temp) . ")
								OR CEIL(o.total) IN  (" . implode(",", $temp2) . ")
								OR CEIL(CAST(o.total as DECIMAL(15,4)) * CAST(o.value as DECIMAL(15,4))) IN  (" . implode(",", $temp2) . ") )";
			}
		}

		//If for total, we done bulding the query
		if($mode == 'total_only'){
			$query = $this->db->query($sql);
			return $query->row['total'];
		}

		$sort_data = array('o.order_id',
				'name',
				'status',
				'o.date_added',
				'o.total');

		if(isset($data['sort']) && in_array($data['sort'], $sort_data)){
			$sql .= " ORDER BY " . $data['sort'];
		} else{
			$sql .= " ORDER BY o.order_id";
		}

		if(isset($data['order']) && (strtoupper($data['order']) == 'DESC')){
			$sql .= " DESC";
		} else{
			$sql .= " ASC";
		}

		if(isset($data['start']) || isset($data['limit'])){
			if($data['start'] < 0){
				$data['start'] = 0;
			}

			if($data['limit'] < 1){
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
		$result_rows = array();
		foreach($query->rows as $row){
			$result_rows[] = $this->dcrypt->decrypt_data($row, 'orders');
		}
		return $result_rows;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function getTotalOrders($data = array()){
		return $this->getOrders($data, 'total_only');
	}

	/**
	 * @param int $product_id
	 * @return int
	 */
	public function getOrderTotalWithProduct($product_id){
		if(!(int)$product_id){
			return array();
		}
		$sql = "SELECT count(DISTINCT op.order_id, op.order_product_id) as total
				FROM " . $this->db->table('order_products') . " op
				WHERE  op.product_id = '" . (int)$product_id . "'";

		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	/**
	 * @param int $order_id
	 * @return string
	 */
	public function generateInvoiceId($order_id){
		$query = $this->db->query("SELECT MAX(invoice_id) AS invoice_id FROM `" . $this->db->table("orders") . "`");

		if($query->row['invoice_id']){
			$invoice_id = (int)$query->row['invoice_id'] + 1;
		} elseif($this->config->get('starting_invoice_id')){
			$invoice_id = $this->config->get('starting_invoice_id');
		} else{
			$invoice_id = 1;
		}

		$this->db->query("UPDATE `" . $this->db->table("orders") . "`
							SET invoice_id = '" . (int)$invoice_id . "',
								invoice_prefix = '" . $this->db->escape($this->config->get('invoice_prefix')) . "',
								date_modified = NOW()
							WHERE order_id = '" . (int)$order_id . "'");

		return $this->config->get('invoice_prefix') . $invoice_id;
	}

	/**
	 * @param int $order_id
	 * @param int $order_product_id
	 * @return array
	 */
	public function getOrderProducts($order_id, $order_product_id = 0){
		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("order_products") . "
									WHERE order_id = '" . (int)$order_id . "'
									" . ((int)$order_product_id ? " AND order_product_id='" . (int)$order_product_id . "'" : ''));
		return $query->rows;
	}

	/**
	 * @param int $order_id
	 * @param int $order_product_id
	 * @return array
	 */
	public function getOrderOptions($order_id, $order_product_id){
		$query = $this->db->query("SELECT op.*, po.element_type, po.attribute_id, po.product_option_id, pov.subtract
									FROM " . $this->db->table("order_options") . " op
									LEFT JOIN " . $this->db->table("product_option_values") . " pov
										ON op.product_option_value_id = pov.product_option_value_id
									LEFT JOIN " . $this->db->table("product_options") . " po
										ON pov.product_option_id = po.product_option_id
									WHERE op.order_id = '" . (int)$order_id . "'
										AND op.order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}
	/**
	 * @param int $order_option_id
	 * @return array
	 */
	public function getOrderOption($order_option_id){
		$query = $this->db->query("SELECT op.*, po.element_type, po.attribute_id, po.product_option_id, pov.subtract
									FROM " . $this->db->table("order_options") . " op
									LEFT JOIN " . $this->db->table("product_option_values") . " pov
										ON op.product_option_value_id = pov.product_option_value_id
									LEFT JOIN " . $this->db->table("product_options") . " po
										ON pov.product_option_id = po.product_option_id
									WHERE op.order_option_id = '" . (int)$order_option_id . "'");
		return $query->row;
	}

	/**
	 * @param int $order_id
	 * @return array
	 */
	public function getOrderTotals($order_id){
		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("order_totals") . "
									WHERE order_id = '" . (int)$order_id . "'
									ORDER BY sort_order");

		return $query->rows;
	}

	/**
	 * @param int $order_id
	 * @return array
	 */
	public function getOrderHistory($order_id){
		$language_id = $this->language->getContentLanguageID();
		$default_language_id = $this->language->getDefaultLanguageID();

		$query = $this->db->query("SELECT oh.date_added,
										COALESCE( os1.name, os1.name) AS status,
										oh.comment,
										oh.notify
									FROM " . $this->db->table("order_history") . " oh
									LEFT JOIN " . $this->db->table("order_statuses") . " os1 ON oh.order_status_id = os1.order_status_id  
										 AND os1.language_id = '" . (int)$language_id . "'
									LEFT JOIN " . $this->db->table("order_statuses") . " os2 ON oh.order_status_id = os2.order_status_id
										 AND os2.language_id = '" . (int)$default_language_id . "'
									WHERE oh.order_id = '" . (int)$order_id . "' 
									ORDER BY oh.date_added");

		return $query->rows;
	}

	/**
	 * @param int $order_id
	 * @return array
	 */
	public function getOrderDownloads($order_id){
		$query = $this->db->query("SELECT op.product_id, op.name as product_name, od.*
								   FROM " . $this->db->table("order_downloads") . " od
								   LEFT JOIN " . $this->db->table("order_products") . " op
								   		ON op.order_product_id = od.order_product_id
								   WHERE od.order_id = '" . (int)$order_id . "'
								   ORDER BY op.order_product_id, od.sort_order, od.name");
		$output = array();
		foreach($query->rows as $row){
			$output[$row['product_id']]['product_name'] = $row['product_name'];
			// get download_history
			$result = $this->db->query("SELECT *
										FROM " . $this->db->table("order_downloads_history") . "
										WHERE order_id = '" . (int)$order_id . "' AND order_download_id = '" . $row['order_download_id'] . "'
										ORDER BY `time` DESC");
			$row['download_history'] = $result->rows;

			$output[$row['product_id']]['downloads'][] = $row;
		}
		return $output;
	}

	/**
	 * @param int $order_id
	 * @return int
	 */
	public function getTotalOrderDownloads($order_id){
		$query = $this->db->query("SELECT COUNT(*) as total
								   FROM " . $this->db->table("order_downloads") . " od
								   WHERE od.order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	/**
	 * @param int $store_id
	 * @return int
	 */
	public function getTotalOrdersByStoreId($store_id){
		$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM `" . $this->db->table("orders") . "`
      	                            WHERE store_id = '" . (int)$store_id . "'");

		return $query->row['total'];
	}

	/**
	 * @param int $order_status_id
	 * @return int
	 */
	public function getOrderHistoryTotalByOrderStatusId($order_status_id){
		$query = $this->db->query("SELECT oh.order_id
	  	                            FROM " . $this->db->table("order_history") . " oh
	  	                            LEFT JOIN `" . $this->db->table("orders") . "` o ON (oh.order_id = o.order_id)
	  	                            WHERE oh.order_status_id = '" . (int)$order_status_id . "' AND o.order_status_id > '0'
	  	                            GROUP BY order_id");

		return $query->num_rows;
	}

	/**
	 * @param int $order_status_id
	 * @return int
	 */
	public function getTotalOrdersByOrderStatusId($order_status_id){
		$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM `" . $this->db->table("orders") . "`
      	                            WHERE order_status_id = '" . (int)$order_status_id . "' AND order_status_id > '0'");
		return $query->row['total'];
	}

	/**
	 * @param int $language_id
	 * @return int
	 */
	public function getTotalOrdersByLanguageId($language_id){
		$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM `" . $this->db->table("orders") . "`
      	                            WHERE language_id = '" . (int)$language_id . "' AND order_status_id > '0'");
		return $query->row['total'];
	}

	/**
	 * @param int $currency_id
	 * @return int
	 */
	public function getTotalOrdersByCurrencyId($currency_id){
		$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM `" . $this->db->table("orders") . "`
      	                            WHERE currency_id = '" . (int)$currency_id . "' AND order_status_id > '0'");
		return $query->row['total'];
	}

	/**
	 * @return int
	 */
	public function getTotalSales(){
		$query = $this->db->query("SELECT SUM(total) AS total
      	                            FROM `" . $this->db->table("orders") . "`
      	                            WHERE order_status_id > '0'");
		return $query->row['total'];
	}

	/**
	 * @param int $year
	 * @return int
	 */
	public function getTotalSalesByYear($year){
		$query = $this->db->query("SELECT SUM(total) AS total
      	                            FROM `" . $this->db->table("orders") . "`
      	                            WHERE order_status_id > '0' AND YEAR(date_added) = '" . (int)$year . "'");

		return $query->row['total'];
	}
}
