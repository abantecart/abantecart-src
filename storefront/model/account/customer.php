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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ModelAccountCustomer extends Model {

	public function addCustomer($data) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "customers SET store_id = '" . (int)$this->config->get('config_store_id') . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', password = '" . $this->db->escape(AEncryption::getHash($data['password'])) . "', newsletter = '" . (int)$data['newsletter'] . "', customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "', status = '1', date_added = NOW()");
      	
		$customer_id = $this->db->getLastId();
			
      	$this->db->query("INSERT INTO " . DB_PREFIX . "addresses SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "'");
		
		$address_id = $this->db->getLastId();

      	$this->db->query("UPDATE " . DB_PREFIX . "customers SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
		
		if (!$this->config->get('config_customer_approval')) {
			$this->db->query("UPDATE " . DB_PREFIX . "customers SET approved = '1' WHERE customer_id = '" . (int)$customer_id . "'");
		}		
	}
	
	public function editCustomer($data) {
		$this->db->query("UPDATE " . DB_PREFIX . "customers SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function editPassword($email, $password) {
      	$this->db->query("UPDATE " . DB_PREFIX . "customers SET password = '" . $this->db->escape(AEncryption::getHash($password)) . "' WHERE email = '" . $this->db->escape($email) . "'");
	}

	public function editNewsletter($newsletter) {
		$this->db->query("UPDATE " . DB_PREFIX . "customers SET newsletter = '" . (int)$newsletter . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}
			
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customers WHERE customer_id = '" . (int)$customer_id . "'");
		
		return $query->row;
	}
	
	public function getTotalCustomersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customers WHERE email = '" . $this->db->escape($email) . "'");
		
		return $query->row['total'];
	}
	
	public function validateRegistrationData( $data ) {
		$error = array();
	
    	if ((strlen(utf8_decode($data['firstname'])) < 1) || (strlen(utf8_decode($data['firstname'])) > 32)) {
      		$error['firstname'] = $this->language->get('error_firstname');
    	}

    	if ((strlen(utf8_decode($data['lastname'])) < 1) || (strlen(utf8_decode($data['lastname'])) > 32)) {
      		$error['lastname'] = $this->language->get('error_lastname');
    	}

		$pattern = '/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i';

    	if ((strlen(utf8_decode($data['email'])) > 96) || (!preg_match($pattern, $data['email']))) {
      		$error['email'] = $this->language->get('error_email');
    	}

    	if ($this->getTotalCustomersByEmail($data['email'])) {
      		$error['warning'] = $this->language->get('error_exists');
    	}
		
    	if ((strlen(utf8_decode($data['telephone'])) < 3) || (strlen(utf8_decode($data['telephone'])) > 32)) {
      		$error['telephone'] = $this->language->get('error_telephone');
    	}

    	if ((strlen(utf8_decode($data['address_1'])) < 3) || (strlen(utf8_decode($data['address_1'])) > 128)) {
      		$error['address_1'] = $this->language->get('error_address_1');
    	}

    	if ((strlen(utf8_decode($data['city'])) < 3) || (strlen(utf8_decode($data['city'])) > 128)) {
      		$error['city'] = $this->language->get('error_city');
    	}

    	if ($data['country_id'] == 'FALSE') {
      		$error['country'] = $this->language->get('error_country');
    	}
		
    	if ($data['zone_id'] == 'FALSE') {
      		$error['zone'] = $this->language->get('error_zone');
    	}

    	if ((strlen(utf8_decode($data['password'])) < 4) || (strlen(utf8_decode($data['password'])) > 20)) {
      		$error['password'] = $this->language->get('error_password');
    	}

    	if ($data['confirm'] != $data['password']) {
      		$error['confirm'] = $this->language->get('error_confirm');
    	}
		
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/content');
			
			$content_info = $this->getContent($this->config->get('config_account_id'));
			
			if ($content_info) {
    			if (!isset($data['agree'])) {
      				$error['warning'] = sprintf($this->language->get('error_agree'), $content_info['title']);
    			}
			}
		}
		
    	return $error;
	}
	
	
	public function validateEditData( $data ) {
		$error = array();	
		if ((strlen(utf8_decode($data['firstname'])) < 1) || (strlen(utf8_decode($data['firstname'])) > 32)) {
			$error['firstname'] = $this->language->get('error_firstname');
		}

		if ((strlen(utf8_decode($data['lastname'])) < 1) || (strlen(utf8_decode($data['lastname'])) > 32)) {
			$error['lastname'] = $this->language->get('error_lastname');
		}

		$pattern = '/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i';

		if ((strlen(utf8_decode($data['email'])) > 96) || (!preg_match($pattern, $data['email']))) {
			$error['email'] = $this->language->get('error_email');
		}
		
		if (($this->customer->getEmail() != $data['email']) && $this->getTotalCustomersByEmail($data['email'])) {
			$error['warning'] = $this->language->get('error_exists');
		}

		if ((strlen(utf8_decode($data['telephone'])) < 3) || (strlen(utf8_decode($data['telephone'])) > 32)) {
			$error['telephone'] = $this->language->get('error_telephone');
		}

    	return $error;
	}
	
}
?>