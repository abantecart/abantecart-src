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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
/** @noinspection PhpUndefinedClassInspection */
/**
 * Class ModelAccountCustomer
 * @property ModelCatalogContent $model_catalog_content
 */
class ModelAccountCustomer extends Model {
	/**
	 * @param array $data
	 * @return int
	 */
	public function addCustomer($data) {
		$key_sql = '';
		if ( $this->dcrypt->active ) {
			$data = $this->dcrypt->encrypt_data($data, 'customers');
			$key_sql = ", key_id = '" . (int)$data['key_id'] . "'";
		}
		if(!(int)$data['customer_group_id']){
			$data['customer_group_id'] = (int)$this->config->get('config_customer_group_id');
		}
		if(!isset($data['status'])){
			if($this->config->get('config_customer_email_activation')){ // if need to activate via email  - disable status
				$data['status'] = 0;
			}else{
				$data['status'] = 1;
			}
		}
		if(isset($data['approved'])){
			$data['approved'] = (int)$data['approved'];
		}else{
			if(!$this->config->get('config_customer_approval')){
				$data['approved'] = 1;
			}
		}
				
		// delete subscription accounts for given email
		$subscriber = $this->db->query("SELECT customer_id
										FROM " . $this->db->table("customers") . "
										WHERE LOWER(`email`) = LOWER('" . $this->db->escape($data['email']) . "')
											AND customer_group_id IN (SELECT customer_group_id
																		  FROM ".$this->db->table('customer_groups')."
																		  WHERE `name` = 'Newsletter Subscribers')");
		foreach($subscriber->rows as $row){
			$this->db->query("DELETE FROM " . $this->db->table("customers") . " WHERE customer_id = '" . (int)$row['customer_id'] . "'");
			$this->db->query("DELETE FROM " . $this->db->table("addresses") . " WHERE customer_id = '" . (int)$row['customer_id'] . "'");
		}

    
      	$sql = "INSERT INTO " . $this->db->table("customers") . "
			  SET	store_id = '" . (int)$this->config->get('config_store_id') . "',
					loginname = '" . $this->db->escape($data['loginname']) . "',
					firstname = '" . $this->db->escape($data['firstname']) . "',
					lastname = '" . $this->db->escape($data['lastname']) . "',
					email = '" . $this->db->escape($data['email']) . "',
					telephone = '" . $this->db->escape($data['telephone']) . "',
					fax = '" . $this->db->escape($data['fax']) . "',
					password = '" . $this->db->escape(AEncryption::getHash($data['password'])) . "',
					newsletter = '" . (int)$data['newsletter'] . "',
					customer_group_id = '" .(int)$data['customer_group_id'] . "',
					approved = '".(int)$data['approved']."',
					status = '".(int)$data['status']."'". $key_sql . ",
					ip = '". $this->db->escape($data['ip']) ."',
					date_added = NOW()";
		$this->db->query($sql);
		$customer_id = $this->db->getLastId();
			
		$key_sql = '';
		if ( $this->dcrypt->active ) {
			$data = $this->dcrypt->encrypt_data($data, 'addresses');
			$key_sql = ", key_id = '" . (int)$data['key_id'] . "'";
		}
      	$this->db->query("INSERT INTO " . $this->db->table("addresses") . " 
      					  SET 	customer_id = '" . (int)$customer_id . "', 
      					  		firstname = '" . $this->db->escape($data['firstname']) . "', 
      					  		lastname = '" . $this->db->escape($data['lastname']) . "', 
      					  		company = '" . $this->db->escape($data['company']) . "', 
      					  		address_1 = '" . $this->db->escape($data['address_1']) . "', 
      					  		address_2 = '" . $this->db->escape($data['address_2']) . "', 
      					  		city = '" . $this->db->escape($data['city']) . "', 
      					  		postcode = '" . $this->db->escape($data['postcode']) . "', 
      					  		country_id = '" . (int)$data['country_id'] . "'" . 
      					  		$key_sql . ",
      					  		zone_id = '" . (int)$data['zone_id'] . "'");
		
		$address_id = $this->db->getLastId();
      	$this->db->query("UPDATE " . $this->db->table("customers") . " SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");

		if(!$data['approved']){
			$language = new ALanguage($this->registry);
			$language->load('account/create');

			//notify administrator of pending customer approval
			$msg_text = sprintf($language->get('text_pending_customer_approval'), $data['firstname'].' '.$data['lastname'],$customer_id);
			$msg = new AMessage();
			$msg->saveNotice($language->get('text_new_customer'), $msg_text);
		}

		return $customer_id;
	}

	/**
	 * @param array $data
	 */
	public function editCustomer($data) {
		$key_sql = '';
		if ( $this->dcrypt->active ) {
			$data = $this->dcrypt->encrypt_data($data, 'customers');
			$key_sql = ", key_id = '" . (int)$data['key_id'] . "'";
		}

    	//update login only if needed
    	$loginname = '';
    	if ( !empty($data['loginname'] ) ) {
    		$loginname = " loginname = '" . $this->db->escape($data['loginname'])  . "', ";
    	}

		$this->db->query("UPDATE " . $this->db->table("customers") . " 
						  SET 	firstname = '" . $this->db->escape($data['firstname']) . "', 
						  		lastname = '" . $this->db->escape($data['lastname']) . "', " . $loginname . "
						  		email = '" . $this->db->escape($data['email']) . "', 
						  		telephone = '" . $this->db->escape($data['telephone']) . "', 
						  		fax = '" . $this->db->escape($data['fax']) . "'"
						  		. $key_sql .
						  		" WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}

	/**
	 * @param string $loginname
	 * @param string $password
	 */
	public function editPassword($loginname, $password) {
		$password = AEncryption::getHash($password);
      	$this->db->query("UPDATE " . $this->db->table("customers") . " SET password = '" . $this->db->escape($password) . "' WHERE loginname = '" . $this->db->escape($loginname) . "'");
	}

	/**
	 * @param int $newsletter
	 * @param int $customer_id - optional parameter for unsubscribe page!
	 */
	public function editNewsletter($newsletter,$customer_id=0) {
		$customer_id = (int)$customer_id ? (int)$customer_id : (int)$this->customer->getId();
		$this->db->query("UPDATE " . $this->db->table("customers") . " SET newsletter = '" . (int)$newsletter . "' WHERE customer_id = '" . $customer_id . "'");
	}

	/**
	 * @param $customer_id
	 * @param $status
	 * @return bool
	 */
	public function editStatus($customer_id, $status) {
		$customer_id = (int)$customer_id;
		$status = (int)$status;
		if(!$customer_id){ return false; }
		$this->db->query( "UPDATE " . $this->db->table("customers") . "
						   SET status = '" . (int)$status . "'
						   WHERE customer_id = '" . $customer_id . "'" );
		return true;
	}

	/**
	 * @param int $customer_id
	 * @return array
	 */
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT * FROM " . $this->db->table("customers") . " WHERE customer_id = '" . (int)$customer_id . "'");
		$result_row = $this->dcrypt->decrypt_data($query->row, 'customers');
		return $result_row;
	}

	/**
	 * @param string $email
	 * @param bool $no_subscribers - sign that needed list without subscribers
	 * @return int
	 */
	public function getTotalCustomersByEmail($email, $no_subscribers=true) {
		$sql = "SELECT COUNT(*) AS total
				FROM " . $this->db->table("customers") . "
				WHERE LOWER(`email`) = LOWER('" . $this->db->escape($email) . "')";
		if($no_subscribers){
			$sql .= " AND customer_group_id NOT IN (SELECT customer_group_id FROM ".$this->db->table('customer_groups')." WHERE `name` = 'Newsletter Subscribers')";
		}
		$query = $this->db->query($sql);
		
		return $query->row['total'];
	}

	/**
	 * @param string $email
	 * @return array
	 */
	public function getCustomerByEmail($email) {
		//assuming that data is not encrypted. Can not call these otherwise
		$query = $this->db->query("SELECT * FROM " . $this->db->table("customers") . " WHERE LOWER(`email`) = LOWER('" . $this->db->escape($email) . "')");
		return $query->row;
	}

	/**
	 * @param string $loginname
	 * @param string $email
	 * @return array
	 */
	public function getCustomerByLoginnameAndEmail($loginname, $email) {
		$query = $this->db->query("SELECT * FROM " . $this->db->table("customers") . " WHERE LOWER(`loginname`) = LOWER('" . $this->db->escape($loginname) . "')");
		//validate it is correct row by matchign decrypted email;
		$result_row = $this->dcrypt->decrypt_data($query->row, 'customers');		
		if ( strtolower($result_row['email']) == strtolower($email) ) {
			return $result_row;
		} else {
			return array();
		}				
	}

	/**
	 * @param string $lastname
	 * @param string $email
	 * @return array
	 */
	public function getCustomerByLastnameAndEmail($lastname, $email) {
		$query = $this->db->query("SELECT * FROM " . $this->db->table("customers") . " WHERE LOWER(`lastname`) = LOWER('" . $this->db->escape($lastname) . "')");
		//validate if we have row with matchign decrypted email;		
		$result_row = array();
		foreach ($query->rows as $result) {
			if ( strtolower($email) == strtolower($this->dcrypt->decrypt_field($result['email'], $result['key_id'])) ) {
				$result_row = $result;
				break;
			}
		}	
				
		if ( count($result_row) ) {
			$result_row = $this->dcrypt->decrypt_data($result_row, 'customers');
			return $result_row;
		} else {
			return array();
		}				
	}

	/**
	 * @param string $loginname
	 * @return bool
	 */
	public function is_unique_loginname( $loginname ) {
		if( empty($loginname) ) {
			return false;
		}
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                           FROM " . $this->db->table("customers") . "
      	                           WHERE LOWER(`loginname`) = LOWER('" . $loginname . "')");
      	if ($query->row['total'] > 0) {
      		return false;
      	} else {
      		return true;
      	}                           
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function validateRegistrationData( $data ) {
		$error = array();
		//If captcha enabled, validate
		if($this->config->get('config_account_create_captcha')) {
			if($this->config->get('config_recaptcha_secret_key')) {
				require_once DIR_VENDORS . '/google_recaptcha/autoload.php';
				$recaptcha = new \ReCaptcha\ReCaptcha($this->config->get('config_recaptcha_secret_key'));
				$resp = $recaptcha->verify(	$data['g-recaptcha-response'],
											$this->request->server['REMOTE_ADDR']);
				if (!$resp->isSuccess() && $resp->getErrorCodes()) {
					$error['captcha'] = $this->language->get('error_captcha');
				}
			} else {
				if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $data['captcha'])) {
					$error['captcha'] = $this->language->get('error_captcha');
				}
			}
		}

		if ( $this->config->get('prevent_email_as_login')) {
			//validate only if email login is not allowed
			$login_name_pattern = '/^[\w._-]+$/i';
    		if ( mb_strlen($data['loginname']) < 5
					|| mb_strlen($data['loginname']) > 64
					|| !preg_match($login_name_pattern, $data['loginname'])
			) {
      			$error['loginname'] = $this->language->get('error_loginname');
    		//validate uniqunes of login name
   		 	} else if ( !$this->is_unique_loginname($data['loginname']) ) {
   		   		$error['loginname'] = $this->language->get('error_loginname_notunique');
   		 	}			
		} 
	
    	if ((mb_strlen($data['firstname']) < 1) || (mb_strlen($data['firstname']) > 32)) {
      		$error['firstname'] = $this->language->get('error_firstname');
    	}

    	if ((mb_strlen($data['lastname']) < 1) || (mb_strlen($data['lastname']) > 32)) {
      		$error['lastname'] = $this->language->get('error_lastname');
    	}

    	if ((mb_strlen($data['email']) > 96) || (!preg_match(EMAIL_REGEX_PATTERN, $data['email']))) {
      		$error['email'] = $this->language->get('error_email');
    	}

    	if ($this->getTotalCustomersByEmail($data['email'])) {
      		$error['warning'] = $this->language->get('error_exists');
    	}

		if ( mb_strlen($data['telephone']) > 32 ) {
			$error['telephone'] = $this->language->get('error_telephone');
		}

		if ((mb_strlen($data['address_1']) < 3) || (mb_strlen($data['address_1']) > 128)) {
			$error['address_1'] = $this->language->get('error_address_1');
		}

		if ((mb_strlen($data['city']) < 3) || (mb_strlen($data['city']) > 128)) {
			$error['city'] = $this->language->get('error_city');
		}
		if ((mb_strlen($data['postcode']) < 3) || (mb_strlen($data['postcode']) > 128)) {
			$error['postcode'] = $this->language->get('error_postcode');
		}

		if ($data['country_id'] == 'FALSE') {
			$error['country'] = $this->language->get('error_country');
		}

		if ($data['zone_id'] == 'FALSE') {
			$error['zone'] = $this->language->get('error_zone');
		}

		if ((mb_strlen($data['password']) < 4) || (mb_strlen($data['password']) > 20)) {
			$error['password'] = $this->language->get('error_password');
		}

		if ($data['confirm'] != $data['password']) {
			$error['confirm'] = $this->language->get('error_confirm');
		}

		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/content');

			$content_info = $this->model_catalog_content->getContent($this->config->get('config_account_id'));

			if ($content_info) {
				if (!isset($data['agree'])) {
					$error['warning'] = sprintf($this->language->get('error_agree'), $content_info['title']);
				}
			}
		}

		
    	return $error;
	}
	/**
	 * @param array $data
	 * @return array
	 */
	public function validateSubscribeData( $data ) {
		$error = array();

		if($this->config->get('config_recaptcha_secret_key')) {
			require_once DIR_VENDORS . '/google_recaptcha/autoload.php';
			$recaptcha = new \ReCaptcha\ReCaptcha($this->config->get('config_recaptcha_secret_key'));
			$resp = $recaptcha->verify(	$data['g-recaptcha-response'],
										$this->request->server['REMOTE_ADDR']);
			if (!$resp->isSuccess() && $resp->getErrorCodes()) {
				$error['captcha'] = $this->language->get('error_captcha');
			}
		} else {
			if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $data['captcha'])) {
				$error['captcha'] = $this->language->get('error_captcha');
			}
		}

    	if ((mb_strlen($data['firstname']) < 1) || (mb_strlen($data['firstname']) > 32)) {
      		$error['firstname'] = $this->language->get('error_firstname');
    	}

    	if ((mb_strlen($data['lastname']) < 1) || (mb_strlen($data['lastname']) > 32)) {
      		$error['lastname'] = $this->language->get('error_lastname');
    	}

    	if ((mb_strlen($data['email']) > 96) || (!preg_match(EMAIL_REGEX_PATTERN, $data['email']))) {
      		$error['email'] = $this->language->get('error_email');
    	}

		if ( $this->getTotalCustomersByEmail($data['email'])) {
			$error['warning'] = $this->language->get('error_subscriber_exists');
		}

    	return $error;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function validateEditData( $data ) {
		$error = array();	
		
		//validate loginname only if cannot match email and if it is set. Edit of loginname not allowed
		if ( $this->config->get('prevent_email_as_login') && isset($data['loginname']) ) {
			//validate only if email login is not allowed
			$login_name_pattern = '/^[\w._-]+$/i';
    		if ((mb_strlen($data['loginname']) < 5) || (mb_strlen($data['loginname']) > 64)
    			|| (!preg_match($login_name_pattern, $data['loginname'])) ) {
      			$error['loginname'] = $this->language->get('error_loginname');
    		//validate uniqunes of login name
   		 	} else if ( !$this->is_unique_loginname($data['loginname']) ) {
   		   		$error['loginname'] = $this->language->get('error_loginname_notunique');
   		 	}			
		} 
		
		if ((mb_strlen($data['firstname']) < 1) || (mb_strlen($data['firstname']) > 32)) {
			$error['firstname'] = $this->language->get('error_firstname');
		}

		if ((mb_strlen($data['lastname']) < 1) || (mb_strlen($data['lastname']) > 32)) {
			$error['lastname'] = $this->language->get('error_lastname');
		}

		if ((mb_strlen($data['email']) > 96) || (!preg_match(EMAIL_REGEX_PATTERN, $data['email']))) {
			$error['email'] = $this->language->get('error_email');
		}
		
		if (($this->customer->getEmail() != $data['email']) && $this->getTotalCustomersByEmail($data['email'])) {
			$error['warning'] = $this->language->get('error_exists');
		}

		if ($data['telephone'] && (mb_strlen($data['telephone']) < 3 || mb_strlen($data['telephone']) > 32)) {
			$error['telephone'] = $this->language->get('error_telephone');
		}

		if ( count($error) && empty( $error['warning'] ) ) {
			$error['warning'] = $this->language->get('gen_data_entry_error');
		}
		
    	return $error;
	}

	public function getTotalTransactions() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . $this->db->table("customer_transactions") . "` WHERE customer_id = '" . (int)$this->customer->getId() . "'" );
		
		return $query->row['total'];
	}
	
	public function getTransactions($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}
		
		$query = $this->db->query("SELECT 
			t.customer_transaction_id, 
			t.order_id, 
			t.section, 
			t.credit, 
			t.debit, 
			t.transaction_type, 
			t.description, 
			t.date_added 
			FROM `" . $this->db->table("customer_transactions") . "` t 
			WHERE customer_id = '" . (int)$this->customer->getId() . "' 
			ORDER BY t.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
	
		return $query->rows;
	}

	public function getSubscribersCustomerGroupId() {
		$query = $this->db->query("SELECT customer_group_id	FROM `" . $this->db->table("customer_groups") . "` WHERE `name` = 'Newsletter Subscribers' LIMIT 0,1");
		$result = !$query->row['customer_group_id'] ? (int)$this->config->get('config_customer_group_id') :  $query->row['customer_group_id'];
		return $result;
	}

}
