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
		$data = $this->dcrypt->encrypt_data($data, 'customers');
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
      	                    date_added = NOW()");
      	
      	$customer_id = $this->db->getLastId();
      	
      	if (isset($data['addresses'])) {		
      		foreach ($data['addresses'] as $address) {
      			//encrypt address data
      			$address = $this->dcrypt->encrypt_data($address, 'addresses');
      			$this->db->query("INSERT INTO " . $this->db->table("addresses") . "
      			                  SET customer_id = '" . (int)$customer_id . "',
      			                        firstname = '" . $this->db->escape($address['firstname']) . "',
      			                        lastname = '" . $this->db->escape($address['lastname']) . "',
      			                        company = '" . $this->db->escape($address['company']) . "',
      			                        address_1 = '" . $this->db->escape($address['address_1']) . "',
      			                        address_2 = '" . $this->db->escape($address['address_2']) . "',
      			                        city = '" . $this->db->escape($address['city']) . "',
      			                        postcode = '" . $this->db->escape($address['postcode']) . "',
      			                        country_id = '" . (int)$address['country_id'] . "',
      			                        zone_id = '" . (int)$address['zone_id'] . "'");
			}
		}

		return $customer_id;
	}
	
	public function editCustomer($customer_id, $data) {
		//encrypt address data
		$data = $this->dcrypt->encrypt_data($data, 'customers');
		$this->db->query("UPDATE " . $this->db->table("customers") . "
						SET loginname = '" . $this->db->escape($data['loginname']) . "',
							firstname = '" . $this->db->escape($data['firstname']) . "',
							lastname = '" . $this->db->escape($data['lastname']) . "',
							email = '" . $this->db->escape($data['email']) . "',
							telephone = '" . $this->db->escape($data['telephone']) . "',
							fax = '" . $this->db->escape($data['fax']) . "',
							newsletter = '" . (int)$data['newsletter'] . "',
							customer_group_id = '" . (int)$data['customer_group_id'] . "',
							status = '" . (int)$data['status'] . "',
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
      			$address = $this->dcrypt->encrypt_data($address, 'addresses');      		
				$this->db->query("INSERT INTO " . $this->db->table("addresses"). "
								  SET customer_id = '" . (int)$customer_id . "',
								        firstname = '" . $this->db->escape($address['firstname']) . "',
								        lastname = '" . $this->db->escape($address['lastname']) . "',
								        company = '" . $this->db->escape($address['company']) . "',
								        address_1 = '" . $this->db->escape($address['address_1']) . "',
								        address_2 = '" . $this->db->escape($address['address_2']) . "',
								        city = '" . $this->db->escape($address['city']) . "',
								        postcode = '" . $this->db->escape($address['postcode']) . "',
								        country_id = '" . (int)$address['country_id'] . "',
								        zone_id = '" . (int)$address['zone_id'] . "'");
			}
		}
	}

	public function editCustomerField($customer_id, $field, $value) {

		$data = array('loginname', 'firstname', 'lastname', 'email', 'telephone', 'fax', 'newsletter', 'customer_group_id', 'status', 'approved' );
		if ( in_array($field, $data) )
			$value_arr = $this->dcrypt->encrypt_data(array($field => $value), 'customers');
			$this->db->query("UPDATE " . $this->db->table("customers") . "
							  SET $field = '" . $this->db->escape($value_arr[$field]) . "'
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
		
	public function getCustomers($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name,
						cg.name AS customer_group
				FROM " . $this->db->table("customers") . " c
				LEFT JOIN " . $this->db->table("customer_groups") . " cg ON (c.customer_group_id = cg.customer_group_id) ";

		$implode = array();
		
		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%' collate utf8_general_ci";
		}
		
		if (isset($data['filter_c.email']) && !is_null($data['filter_c.email'])) {
			$implode[] = "c.email LIKE '%" . $this->db->escape($data['filter_c.email']) . "%' collate utf8_general_ci";
		}
		
		if (isset($data['filter_customer_group_id']) && !is_null($data['filter_customer_group_id'])) {
			$implode[] = "cg.customer_group_id = '" . $this->db->escape($data['filter_customer_group_id']) . "'";
		}	
		
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}	
		
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
		}		
		
		if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.approved',
			'c.date_added'
		);	
			
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
		
		$query = $this->db->query($sql);
		$result_rows = array();
		foreach ($query->rows as $row) {
			$result_rows[] = $this->dcrypt->decrypt_data($row, 'customers');	
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
	
	public function getTotalCustomers($data = array()) {
      	$sql = "SELECT COUNT(*) as total
      	        FROM " . $this->db->table("customers") . " c
      	        LEFT JOIN " . $this->db->table("customer_groups") . " cg ON (c.customer_group_id = cg.customer_group_id) ";

		$implode = array();

		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%' collate utf8_general_ci";
		}

		if (isset($data['filter_email']) && !is_null($data['filter_email'])) {
			$implode[] = "c.email LIKE '%" . $this->db->escape($data['filter_email']) . "%' collate utf8_general_ci";
		}

		if (isset($data['filter_customer_group_id']) && !is_null($data['filter_customer_group_id'])) {
			$implode[] = "cg.customer_group_id = '" . $this->db->escape($data['filter_customer_group_id']) . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);
				
		return $query->row['total'];
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