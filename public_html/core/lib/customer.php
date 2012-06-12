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

final class ACustomer {
	private $customer_id;
	private $firstname;
	private $lastname;
	private $email;
	private $telephone;
	private $fax;
	private $newsletter;
	private $customer_group_id;
	private $address_id;
	private $cache;
	
  	public function __construct($registry) {
		$this->cache = $registry->get('cache');
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
				
		if (isset($this->session->data['customer_id'])) { 
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customers WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");
			
			if ($customer_query->num_rows) {
				$this->customer_id = $customer_query->row['customer_id'];
				$this->firstname = $customer_query->row['firstname'];
				$this->lastname = $customer_query->row['lastname'];
				$this->email = $customer_query->row['email'];
				$this->telephone = $customer_query->row['telephone'];
				$this->fax = $customer_query->row['fax'];
				$this->newsletter = $customer_query->row['newsletter'];
				$this->customer_group_id = $customer_query->row['customer_group_id'];
				$this->address_id = $customer_query->row['address_id'];
							
      			$this->db->query("UPDATE " . DB_PREFIX . "customers SET cart = '" . $this->db->escape(serialize($this->session->data['cart'])) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "'");
			} else {
				$this->logout();
			}
  		}
	}
		
  	public function login($email, $password) {
		if (!$this->config->get('config_customer_approval')) {
			$customer_query = $this->db->query("SELECT *
												FROM " . DB_PREFIX . "customers
												WHERE email = '" . $this->db->escape($email) . "'
													AND password = '" . $this->db->escape(AEncryption::getHash($password)) . "'
													AND status = '1'");
		} else {
			$customer_query = $this->db->query("SELECT *
												FROM " . DB_PREFIX . "customers
												WHERE email = '" . $this->db->escape($email) . "'
													AND password = '" . $this->db->escape(AEncryption::getHash($password)) . "'
													AND status = '1'
													AND approved = '1'");
		}
		
		if ($customer_query->num_rows) {
			$this->session->data['customer_id'] = $customer_query->row['customer_id'];	
		    
			if (($customer_query->row['cart']) && (is_string($customer_query->row['cart']))) {
				$cart = unserialize($customer_query->row['cart']);
				
				foreach ($cart as $key => $value) {
					if (!array_key_exists($key, $this->session->data['cart'])) {
						$this->session->data['cart'][$key] = $value;
					} else {
						$this->session->data['cart'][$key]['qty'] += $value['qty'];
					}
				}			
			}
			
			$this->customer_id = $customer_query->row['customer_id'];
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			$this->email = $customer_query->row['email'];
			$this->telephone = $customer_query->row['telephone'];
			$this->fax = $customer_query->row['fax'];
			$this->newsletter = $customer_query->row['newsletter'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->address_id = $customer_query->row['address_id'];
            $this->cache->delete('storefront_menu');
	  		return TRUE;
    	} else {
      		return FALSE;
    	}
  	}
  
  	public function logout() {
		unset($this->session->data['customer_id']);

		$this->customer_id = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->email = '';
		$this->telephone = '';
		$this->fax = '';
		$this->newsletter = '';
		$this->customer_group_id = '';
		$this->address_id = '';
		$this->cache->delete('storefront_menu');
  	}

	public function isLoggedWithToken( $token ) {
		if ( (isset($this->session->data['token']) && !isset( $token ))
			|| ( (isset( $token ) && (isset($this->session->data['token']) && ( $token != $this->session->data['token'])))) ) {
			return FALSE;
		} else {
			return $this->customer_id;
		}
	}
  
  	public function isLogged() {
    	return $this->customer_id;
  	}

  	public function getId() {
    	return $this->customer_id;
  	}
      
  	public function getFirstName() {
		return $this->firstname;
  	}
  
  	public function getLastName() {
		return $this->lastname;
  	}
  
  	public function getEmail() {
		return $this->email;
  	}
  
  	public function getTelephone() {
		return $this->telephone;
  	}
  
  	public function getFax() {
		return $this->fax;
  	}
	
  	public function getNewsletter() {
		return $this->newsletter;	
  	}

  	public function getCustomerGroupId() {
		return $this->customer_group_id;	
  	}
	
  	public function getAddressId() {
		return $this->address_id;	
  	}
  	
  	public function getFormatedAdress( $data_array, $format = '', $params = array() ) {
  		// Set default format
		if ( $format == '' ) {
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}
		//Set default prams for address
		if ( count($params) <= 0) {
			$params = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);
		}
		
		return str_replace(array( "\r\n", "\r", "\n" ), '<br />', preg_replace(array( "/\s\s+/", "/\r\r+/", "/\n\n+/" ), '<br />', trim(str_replace($params, $data_array, $format))));
  
  	}
  	
}
?>