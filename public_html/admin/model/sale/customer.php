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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
/**
 * Class ModelSaleCustomer
 */
class ModelSaleCustomer extends Model {
	/**
	 * @param $data
	 * @return int
	 */
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
      	                    status = '" . (int)$data['status'] . "',
      	                    approved = '" . (int)$data['approved'] . "'"
      	                    .$key_sql . ",
      	                    date_added = NOW()");
      	
      	return $this->db->getLastId();
	}

	/**
	 * @param int $customer_id
	 * @param array $address
	 * @return int
	 */
	public function addAddress($customer_id, $address=array()){
		if(!(int)$customer_id || !$address || !is_array($address)){
			return false;
		}
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

		return (int)$this->db->getLastId();
	}


	/**
	 * @param int $customer_id
	 * @param array $data
	 */
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

	}

	/**
	 * @param int $customer_id
	 * @param int $address_id
	 * @param array $address
	 * @return bool
	 */
	public function editAddress($customer_id, $address_id, $address){

		if(!(int)$customer_id || !(int)$address_id || !$address || !is_array($address)){
			return false;
		}

		$this->deleteAddress($customer_id, $address_id);
		//encrypt address data
		$key_sql = '';
		if ( $this->dcrypt->active ) {
			$address = $this->dcrypt->encrypt_data($address, 'addresses');
			$key_sql = ", key_id = '" . (int)$address['key_id'] . "'";
		}
		$this->db->query("INSERT INTO " . $this->db->table("addresses"). "
						  SET   address_id = '" . (int)$address_id . "',
						  		customer_id = '" . (int)$customer_id . "',
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

	/**
	 * @param int $customer_id
	 * @param int $address_id
	 * @return bool
	 */
	public function deleteAddress($customer_id, $address_id){
		if(!(int)$customer_id || !(int)$address_id ){
			return false;
		}

		$this->db->query("DELETE FROM " . $this->db->table("addresses") . " WHERE customer_id = '" . (int)$customer_id . "' AND address_id = '".(int)$address_id."'");
	}

	/**
	 * @param int $customer_id
	 * @param string $field
	 * @param mixed $value
	 */
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

	/**
	 * @param int $customer_id
	 * @param string $default_address_id
	 */
	public function setDefaultAddress($customer_id, $default_address_id) {
      	if ($customer_id && $default_address_id) {
        	$this->db->query("UPDATE " . $this->db->table("customers") . "
        	                  SET address_id = '" . (int)$default_address_id . "'
        	                  WHERE customer_id = '" . (int)$customer_id . "'");
      	}
	}

	/**
	 * @param int $address_id
	 * @param string $field_name
	 * @param mixed $value
	 */
	public function editAddressField($address_id, $field_name, $value) {

		$data = array('firstname', 'lastname', 'company', 'address_1', 'address_2', 'city', 'postcode', 'country_id', 'zone_id' );
		if ( in_array($field_name, $data) )
			if ( $this->dcrypt->active && in_array($field_name, $this->dcrypt->getEcryptedFields("addresses")) ) {
				//check key_id to use
				$query_key = $this->db->query( "SELECT key_id
												FROM " . $this->db->table("addresses") . "
							  					WHERE customer_id = '" . (int)$address_id . "'");
				$key_id = $query_key->rows[0]['key_id'];
				$value = $this->dcrypt->encrypt_field($value, $key_id);
			}
			$this->db->query("UPDATE " . $this->db->table("addresses") . "
							  SET ".$field_name." = '" . $this->db->escape($value) . "'
							  WHERE address_id = '" . (int)$address_id . "'");
	}

	/**
	 * @param int $customer_id
	 * @return array
	 */
	public function getAddressesByCustomerId($customer_id) {
		$address_data = array();
		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("addresses") . "
									WHERE customer_id = '" . (int)$customer_id . "'");
	
		foreach ($query->rows as $result) {
			$result = $this->dcrypt->decrypt_data($result, 'addresses');
			
			$this->load->model('localisation/country');
			$this->load->model('localisation/zone');
			
			$country_row = $this->model_localisation_country->getCountry($result['country_id']);						
			if ( $country_row ) {
				$country = $country_row['name'];
				$iso_code_2 = $country_row['iso_code_2'];
				$iso_code_3 = $country_row['iso_code_3'];
				$address_format = $country_row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_row = $this->model_localisation_zone->getZone($result['zone_id']);			
			if ( $zone_row ) {
				$zone = $zone_row['name'];
				$code = $zone_row['code'];
			} else {
				$zone = '';
				$code = '';
			}		
		
			$address_data[$result['address_id']] = array(
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

	/**
	 * @param int $customer_id
	 */
	public function deleteCustomer($customer_id) {
		$this->db->query("DELETE FROM " . $this->db->table("customers") . " WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("addresses") . " WHERE customer_id = '" . (int)$customer_id . "'");
	}

	/**
	 * @param int $customer_id
	 * @return array
	 */
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT DISTINCT *,
									(SELECT COUNT(order_id)
										FROM " . $this->db->table("orders") . "
										WHERE customer_id = '" . (int)$customer_id . "'
												AND order_status_id>0) as orders_count
								   FROM " . $this->db->table("customers") . "
								   WHERE customer_id = '" . (int)$customer_id . "'");

		$result_row = $this->dcrypt->decrypt_data($query->row, 'customers');
		return $result_row;
	}

	/**
	 * @param array $data
	 * @return array|int
	 */
	public function getTotalCustomers($data = array()) {
		return $this->getCustomers($data, 'total_only');
	}

	/**
	 * @param array $data
	 * @param string $mode
	 * @return array|int
	 */
	public function getCustomers($data = array(), $mode = 'default') {
		
		if ( $mode == 'total_only' && !$this->dcrypt->active ) {
			$sql = "SELECT COUNT(*) as total ";
		} else {
			$sql = "SELECT c.customer_id,
			  	c.firstname,
  				c.lastname,
  				c.loginname,
  				c.email,
  				c.status,
  				c.approved,
  				c.customer_group_id,
				CONCAT(c.firstname, ' ', c.lastname) AS name,
				cg.name AS customer_group
				";
		}
		if ( $mode != 'total_only'){
			$sql .= ", COUNT(o.order_id) as orders_count  ";
		}

		if ( $this->dcrypt->active ) {
			$sql .= ", c.key_id ";
		}

		$sql .= " FROM " . $this->db->table("customers") . " c
				LEFT JOIN " . $this->db->table("customer_groups") . " cg ON (c.customer_group_id = cg.customer_group_id) ";
		if ( $mode != 'total_only'){
			$sql .= " LEFT JOIN " . $this->db->table("orders") . " o ON (c.customer_id = o.customer_id AND o.order_status_id>0) ";
		}

		$implode = array();
		$filter = (isset($data['filter']) ? $data['filter'] : array());

		if (has_value($filter['name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($filter['name']) . "%' collate utf8_general_ci";
		}

		if (has_value($filter['name_email'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname, ' ', c.email) LIKE '%" . $this->db->escape($filter['name_email']) . "%' collate utf8_general_ci";
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
		// select only subscribers (group + customers with subscription)
		if (has_value($filter['all_subscribers'])) {
			$implode[] = "( (c.newsletter=1 AND c.status = 1 AND c.approved = 1) OR
						(c.newsletter=1 AND cg.customer_group_id = '".(int)$this->getSubscribersCustomerGroupId()."'))";
		}

		// select only customers without newsletter subscribers
		if (has_value($filter['only_customers'])) {
			$implode[] = "cg.customer_group_id NOT IN (".(int)$this->getSubscribersCustomerGroupId().") ";
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
		if( has_value($this->session->data['current_store_id']) ) {
			$implode[] =  "c.store_id = " . (int)$this->session->data['current_store_id'];
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
				'name'              => 'name',
				'loginname'         => 'c.loginname',
				'lastname'          => 'c.lastname',
				'email'             => 'c.email',
				'customer_group'    => 'customer_group',
				'status'            => 'c.status',
				'approved'          => 'c.approved',
				'date_added'        => 'c.date_added',
				'orders_count'      => 'orders_count'
		);	

		if ( $mode != 'total_only'){
			$sql .= " GROUP BY c.customer_id ";
		}

		//Total culculation for encrypted mode 
		// NOTE: Performance slowdown might be noticed or larger search results	
		if ( $mode != 'total_only' ) {

			$sql .= " ORDER BY ".($sort_data[ $data['sort'] ] ? $sort_data[ $data['sort'] ] : 'name');

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

	/**
	 * @param array $data
	 * @param string $field
	 * @param mixed $value
	 * @return array
	 */
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

	/**
	 * @param int $customer_id
	 */
	public function approve($customer_id) {
		$this->db->query("UPDATE " . $this->db->table("customers") . "
						  SET approved = '1'
						        WHERE customer_id = '" . (int)$customer_id . "'");
	}

	/**
	 * @return array
	 */
	public function getCustomersByNewsletter() {
		$store_based = '';
		if( has_value($this->session->data['current_store_id']) ) {
			$store_based = " AND store_id = " . (int)$this->session->data['current_store_id'];
		}

		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("customers") . "
									WHERE newsletter = '1' " . $store_based . "
									ORDER BY firstname, lastname, email");
	
		$result_rows = array();
		foreach ($query->rows as $row) {
			$result_rows[] = $this->dcrypt->decrypt_data($row, 'customers');	
		}		
		return $result_rows;
	}

	/**
	 * @param string $keyword
	 * @return array
	 */
	public function getCustomersByKeyword($keyword) {
		$store_based = '';
		if( has_value($this->session->data['current_store_id']) ) {
			$store_based = " AND store_id = " . (int)$this->session->data['current_store_id'];
		}

		if ($keyword) {
			$query = $this->db->query("SELECT *
									   FROM " . $this->db->table("customers") . "
									   WHERE LCASE(CONCAT(firstname, ' ', lastname)) LIKE '%" . $this->db->escape(strtolower($keyword)) . "%'
									        OR LCASE(email) LIKE '%" . $this->db->escape(strtolower($keyword)) . "%' " . $store_based . "
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
	/**
	 * @param array $emails
	 * @return array
	 */
	public function getCustomersByEmails($emails) {
		$store_based = '';
		if( has_value($this->session->data['current_store_id']) ) {
			$store_based = " and store_id = " . (int)$this->session->data['current_store_id'];
		}

		$emails = (array)$emails;
		if ($emails) {
			$sql = "SELECT *
				   FROM " . $this->db->table("customers") . "
				   WHERE ";
			foreach($emails as $email){
				$where[] = "LCASE(email) LIKE '%" . $this->db->escape(strtolower($email)) . "%'";
			}
			$sql .= implode(' OR ', $where) . $store_based;
			$sql .= "ORDER BY firstname, lastname, email";

			$query = $this->db->query($sql);
			$result_rows = array();
			foreach ($query->rows as $row) {
				$result_rows[] = $this->dcrypt->decrypt_data($row, 'customers');
			}
			return $result_rows;
		} else {
			return array();
		}
	}

	/**
	 * @param int $product_id
	 * @return array
	 */
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

	/**
	 * @param int $customer_id
	 * @return array
	 */
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

	/**
	 * @param string $loginname
	 * @param string $customer_id
	 * @return bool
	 */
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

	/**
	 * @return int
	 */
	public function getTotalCustomersAwaitingApproval() {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                           FROM " . $this->db->table("customers") . "
      	                           WHERE status = '0' OR approved = '0'");

		return $query->row['total'];
	}

	/**
	 * @param int $customer_id
	 * @return int
	 */
	public function getTotalAddressesByCustomerId($customer_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM " . $this->db->table("addresses") . "
      	                            WHERE customer_id = '" . (int)$customer_id . "'");
		
		return $query->row['total'];
	}

	/**
	 * @param int $country_id
	 * @return int
	 */
	public function getTotalAddressesByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
									FROM " . $this->db->table("addresses") . "
									WHERE country_id = '" . (int)$country_id . "'");
		
		return (int)$query->row['total'];
	}

	/**
	 * @param int $zone_id
	 * @return int
	 */
	public function getTotalAddressesByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
									FROM " . $this->db->table("addresses") . "
									WHERE zone_id = '" . (int)$zone_id . "'");
		
		return (int)$query->row['total'];
	}

	/**
	 * @param int $customer_group_id
	 * @return int
	 */
	public function getTotalCustomersByCustomerGroupId($customer_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
								   FROM " . $this->db->table("customers") . "
								   WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		
		return (int)$query->row['total'];
	}

	/**
	 * @param array $data
	 * @param string $mode
	 * @return array|int
	 */
	public function getAllSubscribers($data=array(), $mode = 'default'){
		$data['filter']['all_subscribers'] = 1;
		return $this->getCustomers($data, $mode);
	}

	/**
	 * @param array $data
	 * @param string $mode
	 * @return array|int
	 */
	public function getOnlyNewsletterSubscribers($data=array(), $mode = 'default'){
		$data['filter']['customer_group_id'] = $this->getSubscribersCustomerGroupId();
		return $this->getCustomers($data, $mode);
	}

	/**
	 * @param array $data
	 * @param string $mode
	 * @return array|int
	 */
	public function getOnlyCustomers($data=array(), $mode = 'default'){
		$data['filter']['only_customers'] = 1;
		return $this->getCustomers($data, $mode);
	}

	/**
	 * @return int
	 */
	public function getSubscribersCustomerGroupId() {
		$query = $this->db->query("SELECT customer_group_id
									FROM `" . $this->db->table("customer_groups") . "`
									WHERE `name` = 'Newsletter Subscribers'
									LIMIT 0,1");
		$result = !$query->row['customer_group_id'] ? (int)$this->config->get('config_customer_group_id') :  $query->row['customer_group_id'];
		return $result;
	}

}