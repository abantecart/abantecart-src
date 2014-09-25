<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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
/**
 * Class ACustomer
 */
final class ACustomer {
	/**
	 * @var int
	 */
	private $customer_id;
	/**
	 * @var string
	 */
	private $loginname;
	/**
	 * @var string
	 */
	private $firstname;
	/**
	 * @var string
	 */
	private $lastname;
	/**
	 * @var string
	 */
	private $email;
	/**
	 * @var string
	 */
	private $telephone;
	/**
	 * @var string
	 */
	private $fax;
	/**
	 * @var int
	 */
	private $newsletter;
	/**
	 * @var int
	 */
	private $customer_group_id;
	/**
	 * @var int
	 */
	private $address_id;
	/**
	 * @var AConfig
	 */
	private $config;
	/**
	 * @var ACache
	 */
	private $cache;
	/**
	 * @var ADB
	 */
	private $db;
	/**
	 * @var ARequest
	 */
	private $request;
	/**
	 * @var ASession
	 */
	private $session;
	/**
	 * @var ADataEncryption
	 */
	private $dcrypt;

	/**
	 * @param  Registry $registry
	 */
	public function __construct($registry) {
		$this->cache = $registry->get('cache');
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->dcrypt = $registry->get('dcrypt');


		if (isset($this->session->data['customer_id'])) { 
			$customer_query = $this->db->query("SELECT * FROM " . $this->db->table("customers") . " WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");
			
			if ($customer_query->num_rows) {
				$this->customer_id = $customer_query->row['customer_id'];
				$this->loginname = $customer_query->row['loginname'];
				$this->firstname = $customer_query->row['firstname'];
				$this->lastname = $customer_query->row['lastname'];
				if ( $this->dcrypt->active ) {
					$this->email = $this->dcrypt->decrypt_field( $customer_query->row['email'], $customer_query->row['key_id'] );
					$this->telephone = $this->dcrypt->decrypt_field( $customer_query->row['telephone'], $customer_query->row['key_id'] );
					$this->fax = $this->dcrypt->decrypt_field( $customer_query->row['fax'], $customer_query->row['key_id'] );
				} else {
					$this->email = $customer_query->row['email'];
					$this->telephone = $customer_query->row['telephone'];
					$this->fax = $customer_query->row['fax'];					
				}
				$this->newsletter = (int)$customer_query->row['newsletter'];
				$this->customer_group_id = $customer_query->row['customer_group_id'];
				$this->address_id = $customer_query->row['address_id'];
							
      			$this->db->query("UPDATE " . $this->db->table("customers") . " SET cart = '" . $this->db->escape(serialize($this->session->data['cart'])) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "'");
			} else {
				$this->logout();
			}
  		}
	}

	/**
	 * @param string $loginname
	 * @param string $password
	 * @return bool
	 */
	public function login($loginname, $password) {
		
		$approved_only = '';
		if ($this->config->get('config_customer_approval')) {
			$approved_only = " AND approved = '1'";
		} 
		
		$customer_query = $this->db->query("SELECT *
											FROM " . $this->db->table("customers") . "
											WHERE loginname = '" . $this->db->escape($loginname) . "'
											AND password = '" . $this->db->escape(AEncryption::getHash($password)) . "'
											AND status = '1'" . $approved_only);

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

			$this->loginname = $loginname;			
			$this->customer_id = $customer_query->row['customer_id'];
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			if ( $this->dcrypt->active ) {
			    $this->email = $this->dcrypt->decrypt_field( $customer_query->row['email'], $customer_query->row['key_id'] );
			    $this->telephone = $this->dcrypt->decrypt_field( $customer_query->row['telephone'], $customer_query->row['key_id'] );
			    $this->fax = $this->dcrypt->decrypt_field( $customer_query->row['fax'], $customer_query->row['key_id'] );
			} else {
			    $this->email = $customer_query->row['email'];
			    $this->telephone = $customer_query->row['telephone'];
			    $this->fax = $customer_query->row['fax'];					
			}			
			$this->newsletter = $customer_query->row['newsletter'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->address_id = $customer_query->row['address_id'];
            $this->cache->delete('storefront_menu');
	  		return TRUE;
    	} else {
      		return FALSE;
    	}
  	}

	/**
	 * @void
	 */
	public function logout() {
		unset($this->session->data['customer_id']);

		$this->customer_id = '';
		$this->loginname = '';
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

	/**
	 * @param string $token
	 * @return bool|int
	 */
	public function isLoggedWithToken( $token ) {
		if ( (isset($this->session->data['token']) && !isset( $token ))
			|| ( (isset( $token ) && (isset($this->session->data['token']) && ( $token != $this->session->data['token'])))) ) {
			return FALSE;
		} else {
			return $this->customer_id;
		}
	}

	/**
	 * @return int
	 */
	public function isLogged() {
    	return $this->customer_id;
  	}

	/**
	 * @return int
	 */
	public function getId() {
    	return $this->customer_id;
  	}

	/**
	* Validate if loginname is the same as email.
	* @param none
	* @return bool
	*/
  	public function isLoginnameAsEmail() {
  		if ( $this->loginname == $this->email) {
  			return true;
  		} else {
			return false;
  		}
  	}

	/**
	 * @return string
	 */
	public function getFirstName() {
		return $this->firstname;
  	}

	/**
	 * @return string
	 */
	public function getLastName() {
		return $this->lastname;
  	}

	/**
	 * @return string
	 */
	public function getLoginName() {
		return $this->loginname;
  	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
  	}

	/**
	 * @return string
	 */
	public function getTelephone() {
		return $this->telephone;
  	}

	/**
	 * @return string
	 */
	public function getFax() {
		return $this->fax;
  	}

	/**
	 * @return mixed
	 */
	public function getNewsletter() {
		return $this->newsletter;	
  	}

	/**
	 * @return int
	 */
	public function getCustomerGroupId() {
		return $this->customer_group_id;	
  	}

	/**
	 * @return int
	 */
	public function getAddressId() {
		return $this->address_id;	
  	}

	/**
	 * @param array $data_array
	 * @param string $format
	 * @param array $locate
	 * @return string
	 */
	public function getFormatedAdress( $data_array, $format = '', $locate = array() ) {
		$data_array = (array)$data_array;
  		// Set default format
		if ( $format == '' ) {
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}
		//Set default varialble to be set for address based on the data
		if ( count($locate) <= 0) {
			$locate = array();
			foreach ($data_array as $key => $value) {
				$locate[] = "{" .$key . "}";
			}		
		}
		
		return str_replace(array( "\r\n", "\r", "\n" ), '<br />', preg_replace(array( "/\s\s+/", "/\r\r+/", "/\n\n+/" ), '<br />', trim(str_replace($locate, $data_array, $format))));
  
  	}

	/**
	 * Customer Transactions Section. Track account balance transactions.
	 * Return customer account balance in customer currency based on debit/credit calcualtion
	 *
	 * @return float|bool
	 */
  	public function getBalance() {
  		if ( !$this->isLogged() ) {
  			return false;
  		}

		$cache_name = 'balance.'.(int)$this->getId();
		$balance = $this->cache->get($cache_name);
		if(is_null($balance)){
			$query = $this->db->query("SELECT sum(credit) - sum(debit) as balance
										FROM " . $this->db->table("customer_transactions") . "
										WHERE customer_id = '" . (int)$this->getId() . "'");
			$balance = $query->row['balance'];
			$this->cache->set($cache_name,$balance);
		}
		return $balance;
  	} 
  	
	/**
	* Record debit transaction 
	* @param array $tr_details - amount, order_id, transaction_type, description, comments, creator
	* @return bool
	*/ 	
  	public function debitTransaction( $tr_details ) {
		return $this->_record_transaction('debit', $tr_details);		
  	} 
  	
	/**
	* Record credit transaction 
	* @param array $tr_details - amount, order_id, transaction_type, description, comments, creator
	* @return bool
	*/ 	
  	public function creditTransaction( $tr_details ) {	
		return $this->_record_transaction('credit', $tr_details);	
  	}

	/**
	 * @param string $type
	 * @param array $tr_details - amount, order_id, transaction_type, description, comments, creator
	 * @return bool
	 */
	private function _record_transaction ( $type, $tr_details) {

  		if ( !$this->isLogged() ) {
  			return false;
  		}
		if ( !has_value($tr_details['transaction_type']) || !has_value($tr_details['created_by'])  ) {
  			return false;
  		}

  		if ( $type == 'debit' ) {
  			$amount = 'debit = ' . (float)$tr_details['amount'];
  		} else if ( $type == 'credit' ) {
  			$amount = 'credit = ' . (float)$tr_details['amount'];
  		} else {
  			return false;
  		}
  	
      	$this->db->query("INSERT INTO " . $this->db->table("customer_transactions") . "
      	                SET customer_id 		= '" . (int)$this->getId() . "',
      	                	order_id 			= '" . (int)$tr_details['order_id'] . "',
      	                    transaction_type 	= '" . $this->db->escape($tr_details['transaction_type']) . "',
      	                    description 		= '" . $this->db->escape($tr_details['description']) . "',
      	                    comment 			= '" . $this->db->escape($tr_details['comment']) . "',
							". $amount . ",
							section				= '" . ((int)$tr_details['section'] ? (int)$tr_details['section'] : 0) . "',
      	                    created_by 			= '" . (int)$tr_details['created_by'] . "',
      	                    date_added = NOW()");
  		$this->cache->delete('balance.'.(int)$this->getId());
  		if ( $this->db->getLastId() ) {
  			return true;	
  		}
  		return false;
  	}
  		
}