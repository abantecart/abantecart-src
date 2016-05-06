<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if(!defined('DIR_CORE')){
	header('Location: static_pages/');
}

/**
 * Class ACustomer
 */
final class ACustomer{
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
	 * @var string
	 */
	private $customer_group_name;
	/**
	 * @var bool
	 */
	private $customer_tax_exempt;
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
	 * @var Array (unauthenticated customer details)
	 */
	private $unauth_customer = array();

	/**
	 * @param  Registry $registry
	 */
	public function __construct($registry){
		$this->cache = $registry->get('cache');
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->dcrypt = $registry->get('dcrypt');
		$this->load = $registry->get('load');

		if(isset($this->session->data['customer_id'])){
			$customer_query = $this->db->query(
				"SELECT c.*, cg.* FROM " . $this->db->table("customers") . " c
					LEFT JOIN " . $this->db->table("customer_groups") . " cg on c.customer_group_id = cg.customer_group_id
					WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' 
					AND status = '1'"
			);

			if($customer_query->num_rows){
				$this->customer_id = $customer_query->row['customer_id'];
				$this->loginname = $customer_query->row['loginname'];
				$this->firstname = $customer_query->row['firstname'];
				$this->lastname = $customer_query->row['lastname'];
				if($this->dcrypt->active){
					$this->email = $this->dcrypt->decrypt_field($customer_query->row['email'], $customer_query->row['key_id']);
					$this->telephone = $this->dcrypt->decrypt_field($customer_query->row['telephone'], $customer_query->row['key_id']);
					$this->fax = $this->dcrypt->decrypt_field($customer_query->row['fax'], $customer_query->row['key_id']);
				} else{
					$this->email = $customer_query->row['email'];
					$this->telephone = $customer_query->row['telephone'];
					$this->fax = $customer_query->row['fax'];
				}
				$this->newsletter = (int)$customer_query->row['newsletter'];
				$this->customer_group_id = $customer_query->row['customer_group_id'];
				$this->customer_group_name = $customer_query->row['name'];
				$this->customer_tax_exempt = $customer_query->row['tax_exempt'];
				$this->address_id = $customer_query->row['address_id'];

			} else{
				$this->logout();
			}
		} elseif(isset($this->request->cookie['customer'])){
			//we have unauthenticated customer
			$encryption = new AEncryption($this->config->get('encryption_key'));
			$this->unauth_customer = unserialize($encryption->decrypt($this->request->cookie['customer']));
			//customer is not valid or not from the same store (under the same domain)
			if(
				$this->unauth_customer['script_name'] != $this->request->server['SCRIPT_NAME']
				|| !$this->isValidEnabledCustomer()
			){
				//clean up
				$this->unauth_customer = array();
				//expire unauth cookie
				unset($_COOKIE['customer']);
				setcookie('customer', '', time() - 3600, dirname($this->request->server['PHP_SELF']));
			}
			//check if unauthenticated customer cart content was found and merge with session
			$saved_cart = $this->getCustomerCart();
			if(!empty($saved_cart) && count($saved_cart)) {
				$this->mergeCustomerCart($saved_cart);
			}
		}
		
		//Update online customers' activity
		$ip = '';
		if (isset($this->request->server['REMOTE_ADDR'])) {
		        $ip = $this->request->server['REMOTE_ADDR'];
		}
		$url = '';
		if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
		        $url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
		}
		$referer = '';
		if (isset($this->request->server['HTTP_REFERER'])) {
		        $referer = $this->request->server['HTTP_REFERER'];
		}		
		$customer_id = '';
		if($this->isLogged()) {
			$customer_id = $this->getId();		
		} else if($this->isUnauthCustomer()){
			$customer_id = $this->isUnauthCustomer();
		}	
		$this->load->model('tool/online_now');		
		$registry->get('model_tool_online_now')->setOnline($ip, $customer_id, $url, $referer);
		//EOF Custmer Construct				
	}

	/**
	 * @param string $loginname
	 * @param string $password
	 * @return bool
	 */
	public function login($loginname, $password){

		$approved_only = '';
		if($this->config->get('config_customer_approval')){
			$approved_only = " AND approved = '1'";
		}

		$customer_query = $this->db->query("SELECT *
											FROM " . $this->db->table("customers") . "
											WHERE LOWER(loginname)  = LOWER('" . $this->db->escape($loginname) . "')
											AND password = '" . $this->db->escape(AEncryption::getHash($password)) . "'
											AND status = '1'" . $approved_only);
		if($customer_query->num_rows){
			$this->customer_id = $this->session->data['customer_id'] = $customer_query->row['customer_id'];

			//load customer saved cart and merge with session cart before login
			$cart = $this->getCustomerCart();
			$this->mergeCustomerCart($cart);
			//save merged cart
			$this->saveCustomerCart();

			$this->loginname = $loginname;
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			if($this->dcrypt->active){
				$this->email = $this->dcrypt->decrypt_field($customer_query->row['email'], $customer_query->row['key_id']);
				$this->telephone = $this->dcrypt->decrypt_field($customer_query->row['telephone'], $customer_query->row['key_id']);
				$this->fax = $this->dcrypt->decrypt_field($customer_query->row['fax'], $customer_query->row['key_id']);
			} else{
				$this->email = $customer_query->row['email'];
				$this->telephone = $customer_query->row['telephone'];
				$this->fax = $customer_query->row['fax'];
			}
			$this->newsletter = $customer_query->row['newsletter'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			
			$this->address_id = $customer_query->row['address_id'];

			//set cookie for unauthenticated user (expire in 1 year)
			$encryption = new AEncryption($this->config->get('encryption_key'));
			$cutomer_data = $encryption->encrypt(serialize(array(
					'first_name'  => $this->firstname,
					'customer_id' => $this->customer_id,
					'script_name' => $this->request->server['SCRIPT_NAME']
			)));
			//Set cookie for this customer to track unauthenticated activity, expire in 1 year
			setcookie(	'customer',
						$cutomer_data, 
						time() + 60 * 60 * 24 * 365, 
						dirname($this->request->server['PHP_SELF']), 
						null,
						(defined('HTTPS') && HTTPS),
						true
					);
		
			return true;
		} else{
			return false;
		}
	}

	/**
	 * @void
	 */
	public function logout(){
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
		$this->customer_group_name = '';
		$this->customer_tax_exempt = '';
		$this->address_id = '';

		//expire unauth cookie
		unset($_COOKIE['customer']);
		setcookie('customer', '', time() - 3600, dirname($this->request->server['PHP_SELF']));
	}

	/**
	 * @param string $token
	 * @return bool|int
	 */
	public function isLoggedWithToken($token){
		if((isset($this->session->data['token']) && !isset($token))
				|| ((isset($token) && (isset($this->session->data['token']) && ($token != $this->session->data['token']))))
		){
			return false;
		} else{
			return $this->customer_id;
		}
	}

	/**
	 * @return int
	 */
	public function isUnauthCustomer(){
		return $this->unauth_customer['customer_id'];
	}

	/**
	 * @return string
	 */
	public function getUnauthName(){
		return $this->unauth_customer['first_name'];
	}


	/**
	 * @return int
	 */
	public function isLogged(){
		return $this->customer_id;
	}

	/**
	 * @return bool
	 */
	public function isTaxExempt(){
		if($this->customer_tax_exempt) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @return int
	 */
	public function getId(){
		return $this->customer_id;
	}

	/**
	 * Validate if loginname is the same as email.
	 * @param none
	 * @return bool
	 */
	public function isLoginnameAsEmail(){
		if($this->loginname == $this->email){
			return true;
		} else{
			return false;
		}
	}

	/**
	 * @return string
	 */
	public function getFirstName(){
		return $this->firstname;
	}

	/**
	 * @return string
	 */
	public function getLastName(){
		return $this->lastname;
	}

	/**
	 * @return string
	 */
	public function getLoginName(){
		return $this->loginname;
	}

	/**
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getTelephone(){
		return $this->telephone;
	}

	/**
	 * @return string
	 */
	public function getFax(){
		return $this->fax;
	}

	/**
	 * @return mixed
	 */
	public function getNewsletter(){
		return $this->newsletter;
	}

	/**
	 * @return int
	 */
	public function getCustomerGroupId(){
		return $this->customer_group_id;
	}

	/**
	 * @return int
	 */
	public function getAddressId(){
		return $this->address_id;
	}

	/**
	 * @deprecated
	 * @since 1.2.7
	 */
	public function getFormatedAdress($data_array, $format = '', $locate = array()){
		return $this->getFormattedAddress($data_array, $format, $locate);
	}

	/**
	 * @since 1.2.7
	 * @param array $data_array
	 * @param string $format
	 * @param array $locate
	 * @return string
	 */
	public function getFormattedAddress($data_array, $format = '', $locate = array()){
		$data_array = (array)$data_array;
		// Set default format
		if($format == ''){
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}
		//Set default varialble to be set for address based on the data
		if(count($locate) <= 0){
			$locate = array();
			foreach($data_array as $key => $value){
				$locate[] = "{" . $key . "}";
			}
		}

		return str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($locate, $data_array, $format))));

	}

	/**
	 * Customer Transactions Section. Track account balance transactions.
	 * Return customer account balance in customer currency based on debit/credit calculation
	 *
	 * @return float|bool
	 */
	public function getBalance(){
		if(!$this->isLogged()){
			return false;
		}

		$query = $this->db->query("SELECT sum(credit) - sum(debit) as balance
									FROM " . $this->db->table("customer_transactions") . "
									WHERE customer_id = '" . (int)$this->getId() . "'");
		$balance = (float)$query->row['balance'];
		return $balance;
	}

	/**
	 * Record debit transaction
	 * @param array $tr_details - amount, order_id, transaction_type, description, comments, creator
	 * @return bool
	 */
	public function debitTransaction($tr_details){
		return $this->_record_transaction('debit', $tr_details);
	}

	/**
	 * Record credit transaction
	 * @param array $tr_details - amount, order_id, transaction_type, description, comments, creator
	 * @return bool
	 */
	public function creditTransaction($tr_details){
		return $this->_record_transaction('credit', $tr_details);
	}

	/**
	 * Record cart content
	 */
	public function saveCustomerCart(){
		$customer_id = $this->customer_id;
		$store_id = (int)$this->config->get('config_store_id');
		if(!$customer_id){
			$customer_id = $this->unauth_customer['customer_id'];
		}
		if(!$customer_id){
			return null;
		}

		//before write get cart-info from db to non-override cart for other stores of multistore
		$result = $this->db->query("SELECT cart
									FROM " . $this->db->table("customers") . "
									WHERE customer_id = '" . (int)$customer_id . "' AND status = '1'");
		$cart = unserialize($result->row['cart']);
		//check is format of cart old or new
		$new = $this->_is_new_cart_format( $cart );

		if(!$new){
			$cart = array(); //clean cart from old format
		}
		$cart['store_' . $store_id] = $this->session->data['cart'];
		$this->db->query("UPDATE " . $this->db->table("customers") . "
       	                  SET
       	                        cart = '" . $this->db->escape(serialize($cart)) . "',
       	                        ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
       	                  WHERE customer_id = '" . (int)$customer_id . "'");
	}

	/**
	 * Confirm that current customer is valid
	 * @param none
	 * @return bool
	 */
	public function isValidEnabledCustomer(){
		$customer_id = $this->customer_id;
		if(!$customer_id){
			$customer_id = $this->unauth_customer['customer_id'];
		}
		if(!$customer_id){
			return false;
		}

		$sql = "SELECT cart
	    		FROM " . $this->db->table("customers") . "
	    		WHERE customer_id = '" . (int)$customer_id . "' AND status = '1'";
		$result = $this->db->query($sql);
		if($result->num_rows){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Get cart content
	 * @param none
	 * @return array()
	 */
	public function getCustomerCart(){
		$store_id = (int)$this->config->get('config_store_id');
		$customer_id = $this->customer_id;
		if(!$customer_id){
			$customer_id = $this->unauth_customer['customer_id'];
		}
		if(!$customer_id){
			return array();
		}

		$cart = array();
		$sql = "SELECT cart
	    		FROM " . $this->db->table("customers") . "
	    		WHERE customer_id = '" . (int)$customer_id . "' AND status = '1'";

		$result = $this->db->query($sql);
		if($result->num_rows){
			//load customer saved cart
			if(($result->row['cart']) && (is_string($result->row['cart']))){
				$cart = unserialize($result->row['cart']);
				//check is format of cart old or new
				$new = $this->_is_new_cart_format( $cart );
				if(isset($cart['store_' . $store_id])){
					$cart = $cart['store_' . $store_id];
				}elseif($new){
					$cart = array();
				}
				//clean products
				if($cart){
					$cart_products = array();
					foreach($cart as $key=>$val){
						$k = explode(':',$key);
						$cart_products[] = (int)$k[0]; // <-product_id
					}
					$sql = "SELECT product_id
							FROM " . $this->db->table('products_to_stores') . " pts
							WHERE store_id = '" . $store_id . "' AND product_id IN (" . implode(', ', $cart_products) . ")";

					$result = $this->db->query($sql);
					$products = array();
					foreach($result->rows as $row){
						$products[] = $row['product_id'];
					}

					$diff = array_diff($cart_products, $products);
					foreach($diff as $p){
						unset($cart[$p]);
					}
				}
			}
		}
		return $cart;
	}

	/**
	 * Merge cart from session and cart from database content
	 * @param array - cart from database
	 * @void
	 */
	public function mergeCustomerCart($cart){
		$store_id = (int)$this->config->get('config_store_id');
		$cart = !is_array($cart) ? array() : $cart;
		//check is format of cart old or new
		$new = $this->_is_new_cart_format( $cart );

		if($new){
			$cart = $cart['store_' . $store_id];
		}
		$cart = !is_array($cart) ? array() : $cart; // for case when data format is new but cart for store does not yet created

		if($cart && !is_array($this->session->data['cart'])){
			$this->session->data['cart'] = array();
		}
		foreach($cart as $key => $value){
			if(!array_key_exists($key, $this->session->data['cart'])){
				$this->session->data['cart'][$key] = $value;
			}
		}

	}

	/**
	 * Clear cart from database content
	 * @return bool
	 */
	public function clearCustomerCart(){

		$cart = array();
		$customer_id = $this->customer_id;
		if(!$customer_id){
			$customer_id = $this->unauth_customer['customer_id'];
		}
		if(!$customer_id){
			return false;
		}
		$this->db->query("UPDATE " . $this->db->table("customers") . "
						SET
							cart = '" . $this->db->escape(serialize($cart)) . "'
						WHERE customer_id = '" . (int)$customer_id . "'");
		return true;
	}


	/**
	 * Recognize cart data format. New format is cart-per-store
	 * @param array $cart_data
	 * @return bool
	 */
	private function _is_new_cart_format($cart_data = array()){
		if(empty($cart_data)){
			return false;
		}
		$keys = array_keys($cart_data);
		if (is_array($keys) && !empty($keys)) {
			foreach($keys as $k){
				if(is_int(strpos($k, 'store_'))){
					return true;
				}
			}
		}
		return false;
	}


	/**
	 * Add item to wishlist
	 * @param int $product_id
	 * @return null
	 */
	public function addToWishList($product_id){
		if(!has_value($product_id) || !is_numeric($product_id)){
			return null;
		}
		$whishlist = $this->getWishList();
		$whishlist[$product_id] = time();
		$this->saveWishList($whishlist);
		return null;
	}

	/**
	 * Remove item from wish list
	 * @param int $product_id
	 * @return null
	 */
	public function removeFromWishList($product_id){
		if(!has_value($product_id) || !is_numeric($product_id)){
			return null;
		}
		$whishlist = $this->getWishList();
		unset($whishlist[$product_id]);
		$this->saveWishList($whishlist);
		return null;
	}

	/**
	 * Record wish list content
	 * @param array $whishlist
	 * @return null
	 */
	public function saveWishList($whishlist = array()){
		$customer_id = $this->customer_id;
		if(!$customer_id){
			$customer_id = $this->unauth_customer['customer_id'];
		}
		if(!$customer_id){
			return null;
		}
		$this->db->query("UPDATE " . $this->db->table("customers") . "
							SET
								wishlist = '" . $this->db->escape(serialize($whishlist)) . "',
								ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
							WHERE customer_id = '" . (int)$customer_id . "'");
	}

	/**
	 * Get cart content
	 * @param none
	 * @return array()
	 */
	public function getWishList(){
		$customer_id = $this->customer_id;
		if(!$customer_id){
			$customer_id = $this->unauth_customer['customer_id'];
		}
		if(!$customer_id){
			return array();
		}
		$customer_query = $this->db->query("SELECT wishlist
											FROM " . $this->db->table("customers") . "
											WHERE customer_id = '" . (int)$customer_id . "' AND status = '1'");
		if($customer_query->num_rows){
			//load customer saved cart
			if(($customer_query->row['wishlist']) && (is_string($customer_query->row['wishlist']))){
				return unserialize($customer_query->row['wishlist']);
			}
		}
		return array();
	}

	/**
	 * @param string $type
	 * @param array $tr_details - amount, order_id, transaction_type, description, comments, creator
	 * @return bool
	 */
	private function _record_transaction($type, $tr_details){

		if(!$this->isLogged()){
			return false;
		}
		if(!has_value($tr_details['transaction_type']) || !has_value($tr_details['created_by'])){
			return false;
		}

		if($type == 'debit'){
			$amount = 'debit = ' . (float)$tr_details['amount'];
		} else if($type == 'credit'){
			$amount = 'credit = ' . (float)$tr_details['amount'];
		} else{
			return false;
		}

		$this->db->query("INSERT INTO " . $this->db->table("customer_transactions") . "
      	                SET customer_id 		= '" . (int)$this->getId() . "',
      	                	order_id 			= '" . (int)$tr_details['order_id'] . "',
      	                    transaction_type 	= '" . $this->db->escape($tr_details['transaction_type']) . "',
      	                    description 		= '" . $this->db->escape($tr_details['description']) . "',
      	                    comment 			= '" . $this->db->escape($tr_details['comment']) . "',
							" . $amount . ",
							section				= '" . ((int)$tr_details['section'] ? (int)$tr_details['section'] : 0) . "',
      	                    created_by 			= '" . (int)$tr_details['created_by'] . "',
      	                    date_added = NOW()");

		if($this->db->getLastId()){
			return true;
		}
		return false;
	}


}