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
class ModelSaleCustomer extends Model {
	public function addCustomer($data) {
		//encrypt customer data
		$key_sql = '';
		if ( $this->dcrypt->active ) {
			$data = $this->dcrypt->encrypt_data($data, 'customers');
			$key_sql = ", key_id = '" . (int)$data['key_id'] . "'";
		}
      	$this->db->query("INSERT INTO " . $this->db->table("customers") . "
      	                SET loginname = '" . $this->db->escape($data['loginname']) . "',
      	                	firstname = '" . $this->db->escape($data['firstname']) . "',
      	                    lastname = '" . $this->db->escape($data['lastname']) . "',
      	                    email = '" . $this->db->escape($data['email']) . "',
      	                    telephone = '" . $this->db->escape($data['telephone']) . "',
      	                    fax = '" . $this->db->escape($data['fax']) . "',
      	                    newsletter = '" . (int)$data['newsletter'] . "',
      	                    customer_group_id = '" . (int)$data['customer_group_id'] . "',
      	                    password = '" . $this->db->escape(AEncryption::getHash($data['password'])) . "',
      	                    status = '" . (int)$data['status'] . "'"
      	                    .$key_sql . ", 
      	                    date_added = NOW()");
      	
      	$customer_id = $this->db->getLastId();
      	
      	if (isset($data['addresses'])) {		
      		foreach ($data['addresses'] as $address) {
      			//encrypt address data
				$key_sql = '';
				if ( $this->dcrypt->active ) {
					$address = $this->dcrypt->encrypt_data($address, 'addresses');
					$key_sql = ", key_id = '" . (int)$address['key_id'] . "'";
				}
      			$this->db->query("INSERT INTO " . $this->db->table("addresses") . "
      			                  SET customer_id = '" . (int)$customer_id . "',
      			                        firstname = '" . $this->db->escape($address['firstname']) . "',
      			                        lastname = '" . $this->db->escape($address['lastname']) . "',
      			                        company = '" . $this->db->escape($address['company']) . "',
      			                        address_1 = '" . $this->db->escape($address['address_1']) . "',
      			                        address_2 = '" . $this->db->escape($address['address_2']) . "',
      			                        city = '" . $this->db->escape($address['city']) . "',
      			                        postcode = '" . $this->db->escape($address['postcode']) . "',
      			                        country_id = '" . (int)$address['country_id'] . "'"
      			                        .$key_sql . ", 
      			                        zone_id = '" . (int)$address['zone_id'] . "'");
			}
		}

		return $customer_id;
	}
	
	public function editCustomer($customer_id, $data) {
		//encrypt address data
		$key_sql = '';
		if ( $this->dcrypt->active ) {
			$data = $this->dcrypt->encrypt_data($data, 'customers');
			$key_sql = ", key_id = '" . (int)$data['key_id'] . "'";
		}
		$this->db->query("UPDATE " . $this->db->table("customers") . "
						SET loginname = '" . $this->db->escape($data['loginname']) . "',
							firstname = '" . $this->db->escape($data['firstname']) . "',
							lastname = '" . $this->db->escape($data['lastname']) . "',
							email = '" . $this->db->escape($data['email']) . "',
							telephone = '" . $this->db->escape($data['telephone']) . "',
							fax = '" . $this->db->escape($data['fax']) . "',
							newsletter = '" . (int)$data['newsletter'] . "',
							customer_group_id = '" . (int)$data['customer_group_id'] . "',
							status = '" . (int)$data['status'] . "'"
							 .$key_sql . ", 
							approved = '" . (int)$data['approved'] . "'
						WHERE customer_id = '" . (int)$customer_id . "'");
	
      	if ($data['password']) {
        	$this->db->query("UPDATE " . $this->db->table("customers") . "
        	                  SET password = '" . $this->db->escape(AEncryption::getHash($data['password'])) . "'
        	                  WHERE customer_id = '" . (int)$customer_id . "'");
      	}
      	
      	$this->db->query("DELETE FROM " . $this->db->table("addresses") . " WHERE customer_id = '" . (int)$customer_id . "'");
      	
      	if (isset($data['addresses'])) {
      		foreach ($data['addresses'] as $address) {	
      			//encrypt address data
				$key_sql = '';
				if ( $this->dcrypt->active ) {
					$address = $this->dcrypt->encrypt_data($address, 'addresses');
					$key_sql = ", key_id = '" . (int)$addresses['key_id'] . "'";
				}
				$this->db->query("INSERT INTO " . $this->db->table("addresses"). "
								  SET customer_id = '" . (int)$customer_id . "',
								        firstname = '" . $this->db->escape($address['firstname']) . "',
								        lastname = '" . $this->db->escape($address['lastname']) . "',
								        company = '" . $this->db->escape($address['company']) . "',
								        address_1 = '" . $this->db->escape($address['address_1']) . "',
								        address_2 = '" . $this->db->escape($address['address_2']) . "',
								        city = '" . $this->db->escape($address['city']) . "',
								        postcode = '" . $this->db->escape($address['postcode']) . "',
								        country_id = '" . (int)$address['country_id'] . "'"
								        .$key_sql . ",
								        zone_id = '" . (int)$address['zone_id'] . "'");
			}
		}
	}

	public function editCustomerField($customer_id, $field, $value) {

		$data = array('loginname', 'firstname', 'lastname', 'email', 'telephone', 'fax', 'newsletter', 'customer_group_id', 'status', 'approved' );
		if ( in_array($field, $data) )

			if ( $this->dcrypt->active && in_array($field, $this->dcrypt->getEcryptedFields("customers")) ) {
				//check key_id to use 
				$query_key = $this->db->query("select key_id from " . $this->db->table("customers") . "
							  WHERE customer_id = '" . (int)$customer_id . "'");
				$key_id = $query_key->rows[0]['key_id'];		
				$value = $this->dcrypt->encrypt_field($value, $key_id);
			}
			$this->db->query("UPDATE " . $this->db->table("customers") . "
							  SET $field = '" . $this->db->escape($value) . "'
							  WHERE customer_id = '" . (int)$customer_id . "'");

      	if ($field == 'password') {
        	$this->db->query("UPDATE " . $this->db->table("customers") . "
        	                  SET password = '" . $this->db->escape(AEncryption::getHash($value)) . "'
        	                  WHERE customer_id = '" . (int)$customer_id . "'");
      	}
	}
	
	public function getAddressesByCustomerId($customer_id) {
		$address_data = array();
		
		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("addresses") . "
									WHERE customer_id = '" . (int)$customer_id . "'");
	
		foreach ($query->rows as $result) {
			$result = $this->dcrypt->decrypt_data($result, 'addresses');
			$country_query = $this->db->query("SELECT *
												FROM `" . $this->db->table("countries") . "`
												WHERE country_id = '" . (int)$result['country_id'] . "'");
			
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_query = $this->db->query("SELECT *
										    FROM `" . $this->db->table("zones") . "`
										    WHERE zone_id = '" . (int)$result['zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$code = $zone_query->row['code'];
			} else {
				$zone = '';
				$code = '';
			}		
		
			$address_data[] = array(
				'address_id'     => $result['address_id'],
				'firstname'      => $result['firstname'],
				'lastname'       => $result['lastname'],
				'company'        => $result['company'],
				'address_1'      => $result['address_1'],
				'address_2'      => $result['address_2'],
				'postcode'       => $result['postcode'],
				'city'           => $result['city'],
				'zone_id'        => $result['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $code,
				'country_id'     => $result['country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
		}		
		
		return $address_data;
	}	
	
	public function deleteCustomer($customer_id) {
		$this->db->query("DELETE FROM " . $this->db->table("customers") . " WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("addresses") . " WHERE customer_id = '" . (int)$customer_id . "'");
	}
	
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT DISTINCT *
								   FROM " . $this->db->table("customers") . "
								   WHERE customer_id = '" . (int)$customer_id . "'");

		$result_row = $this->dcrypt->decrypt_data($query->row, 'customers');
		return $result_row;
	}
	
	public function getTotalCustomers($data = array()) {
		return $this->getCustomers($data, 'total_only');
	}	
		
	public function getCustomers($data = array(), $mode = 'default') {
		
		if ( $mode == 'total_only' && !$this->dcrypt->active ) {
			$sql = "SELECT COUNT(*) as total ";
		} else {
			$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name,
						cg.name AS customer_group ";
		}
		$sql .= " FROM " . $this->db->table("customers") . " c
				LEFT JOIN " . $this->db->table("customer_groups") . " cg ON (c.customer_group_id = cg.customer_group_id) ";		

		$implode = array();
		$filter = (isset($data['filter']) ? $data['filter'] : array());
		
		if (has_value($filter['name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($filter['name']) . "%' collate utf8_general_ci";
		}
	    //more specific login, last and first name search
		if (has_value($filter['loginname'])) {
			$implode[] = "LOWER(c.loginname) = LOWER('" .$this->db->escape($filter['loginname']) . "') collate utf8_general_ci";
		}
		if (has_value($filter['firstname'])) {
			$implode[] = "LOWER(c.firstname) LIKE LOWER('" .$this->db->escape($filter['firstname']) . "%') collate utf8_general_ci";
		}
		if (has_value($filter['lastname'])) {
			$implode[] = "LOWER(c.lastname) LIKE LOWER('" .$this->db->escape($filter['lastname']) . "%') collate utf8_general_ci";
		}
		//select differently if encrypted
		if ( !$this->dcrypt->active ) {
			if (has_value($filter['email'])) {
				$implode[] = "c.email LIKE '%" . $this->db->escape($filter['email']) . "%' collate utf8_general_ci";
			}
			if (has_value($filter['telephone'])) {
				$implode[] = "c.telephone LIKE '%" . $this->db->escape($filter['telephone']) . "%' collate utf8_general_ci";
			}
		}
		
		if (has_value($filter['customer_group_id'])) {
			$implode[] = "cg.customer_group_id = '" . $this->db->escape($filter['customer_group_id']) . "'";
		}	
		
		if (has_value($filter['status'])) {
			$implode[] = "c.status = '" . (int)$filter['status'] . "'";
		}	
		
		if (has_value($filter['approved'])) {
			$implode[] = "c.approved = '" . (int)$filter['approved'] . "'";
		}		
		
		if (has_value($filter['date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($filter['date_added']) . "')";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		//If for total, we done bulding the query
		if ($mode == 'total_only' && !$this->dcrypt->active) {
			$query = $this->db->query($sql);
			return $query->row['total'];
		}
					
		$sort_data = array(
			'name',
			'c.loginname',
			'c.lastname',
			'c.email',
			'customer_group',
			'c.status',
			'c.approved',
			'c.date_added'
		);	
			
		//Total culculation for encrypted mode 
		// NOTE: Performance slowdown might be noticed or larger search results	
		if ( $mode != 'total_only' ) {
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY name";	
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
		}	
		
		$query = $this->db->query($sql);
		$result_rows = $query->rows;
		if ( $this->dcrypt->active ) {
			if (has_value($filter['email'])) {
				$result_rows = $this->_filter_by_encrypted_field($result_rows, 'email', $filter['email']);
			}
			if (has_value($filter['telephone'])) {
				$result_rows = $this->_filter_by_encrypted_field($result_rows, 'telephone', $filter['telephone']);
			}			
		}		

		if ($mode == 'total_only') {
			//we get here only if in data encryption mode
			return count($result_rows);
		}
		//finaly decrypt data and return result
		for ($i = 0; $i < count($result_rows); $i++) {
			$result_rows[$i] = $this->dcrypt->decrypt_data($result_rows[$i], 'customers');	
		}
		
		return $result_rows;	
	}

	private function _filter_by_encrypted_field($data, $field, $value) {
		if ( !count($data) ) {
			return array();
		}
		if ( !has_value($field) || !has_value($value) ) {
			return $data;
		}
		$result_rows = array(); 
		foreach ($data as $result) {
			if ( $this->dcrypt->active ) {
				$fvalue = $this->dcrypt->decrypt_field($result[$field], $result['key_id']);
			} else {
				$fvalue = $result[$field];
			}
			if ( !(strpos (strtolower($fvalue), strtolower($value)) === false) ) {
				$result_rows[] = $result;
			}
		}	
		return $result_rows;
	}
	
	public function approve($customer_id) {
		$this->db->query("UPDATE " . $this->db->table("customers") . "
						  SET approved = '1'
						        WHERE customer_id = '" . (int)$customer_id . "'");
	}
	
	public function getCustomersByNewsletter() {
		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("customers") . "
									WHERE newsletter = '1'
									ORDER BY firstname, lastname, email");
	
		$result_rows = array();
		foreach ($query->rows as $row) {
			$result_rows[] = $this->dcrypt->decrypt_data($row, 'customers');	
		}		
		return $result_rows;
	}
	
	public function getCustomersByKeyword($keyword) {
		if ($keyword) {
			$query = $this->db->query("SELECT *
									   FROM " . $this->db->table("customers") . "
									   WHERE LCASE(CONCAT(firstname, ' ', lastname)) LIKE '%" . $this->db->escape(strtolower($keyword)) . "%'
									        OR LCASE(email) LIKE '%" . $this->db->escape(strtolower($keyword)) . "%'
									   ORDER BY firstname, lastname, email");
	
			$result_rows = array();
			foreach ($query->rows as $row) {
				$result_rows[] = $this->dcrypt->decrypt_data($row, 'customers');	
			}		
			return $result_rows;
		} else {
			return array();	
		}
	}
	
	public function getCustomersByProduct($product_id) {
		if ($product_id) {
			$query = $this->db->query("SELECT DISTINCT `email`
										FROM `" . $this->db->table("orders") . "` o
										LEFT JOIN " . $this->db->table("order_products") . " op ON (o.order_id = op.order_id)
										WHERE op.product_id = '" . (int)$product_id . "' AND o.order_status_id <> '0'");
	
			$result_rows = array();
			foreach ($query->rows as $row) {
				$result_rows[] = $this->dcrypt->decrypt_data($row, 'orders');	
			}		
			return $result_rows;
		} else {
			return array();	
		}
	}
	
	public function getAddresses($customer_id) {
		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("addresses") . "
									WHERE customer_id = '" . (int)$customer_id . "'");
	
		$result_rows = array();
		foreach ($query->rows as $row) {
			$result_rows[] = $this->dcrypt->decrypt_data($row, 'addresses');	
		}		
		return $result_rows;
	}
	
	public function is_unique_loginname( $loginname, $customer_id = '' ) {
		if( empty($loginname) ) {
			return false;
		}
		//exclude diven customer from checking
		$not_current_customer = '';
		if ( has_value($customer_id) ) {
			$not_current_customer = "AND customer_id <> '$customer_id'";
		}
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                           FROM " . $this->db->table("customers") . "
      	                           WHERE LOWER(`loginname`) = LOWER('" . $loginname . "') " . $not_current_customer);
      	if ($query->row['total'] > 0) {
      		return false;
      	} else {
      		return true;
      	}                           
	}
		
	public function getTotalCustomersAwaitingApproval() {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                           FROM " . $this->db->table("customers") . "
      	                           WHERE status = '0' OR approved = '0'");

		return $query->row['total'];
	}
	
	public function getTotalAddressesByCustomerId($customer_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM " . $this->db->table("addresses") . "
      	                            WHERE customer_id = '" . (int)$customer_id . "'");
		
		return $query->row['total'];
	}
	
	public function getTotalAddressesByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
									FROM " . $this->db->table("addresses") . "
									WHERE country_id = '" . (int)$country_id . "'");
		
		return $query->row['total'];
	}	
	
	public function getTotalAddressesByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
									FROM " . $this->db->table("addresses") . "
									WHERE zone_id = '" . (int)$zone_id . "'");
		
		return $query->row['total'];
	}
	
	public function getTotalCustomersByCustomerGroupId($customer_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
								   FROM " . $this->db->table("customers") . "
								   WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		
		return $query->row['total'];
	}	
}
?>