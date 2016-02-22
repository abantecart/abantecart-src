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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

/**
 * Class AIM for Instant Messages
 * @property ACustomer $customer
 * @property ALanguage $language
 * @property ADB $db
 * @property ALog $log
 * @property ALoader $load
 * @property AHtml $html
 * @property ExtensionsAPI $extensions
 * @property ASession $session
 * @property AConfig $config
 * @property ModelAccountCustomer $model_account_customer
 */

class AIM {
	private $registry;
	private $protocols = array('email', 'sms');
	/**
	 * @var array for StoreFront side ONLY!
	 * NOTE:
	 * each key of array is text_id of sendpoint.
	 * To get sendpoint title needs to request language definition in format im_sendpoint_name_{sendpoint_text_id}
	 * All sendpoint titles must to be saved in common/im language block for both sides! (admin + storefront)
	 * Values of array is language definitions key that stores in the same block. This values can have %s that will be replaced by sendpoint text variables.
	 * for ex. message have url to product page. Text will have #storefront#rt=product/product&product_id=%s and customer will receive full url to product.
	 * Some sendpoints have few text variables, for ex. order status and order status name
	 * For additional sendpoints ( from extensions) you can store language keys wherever you want.
	 */
	public $sendpoints = array(
		'order_updates' => array(
				'sf' => 'im_order_updates_text_to_customer',
				'cp' => 'im_order_updates_text_to_admin'),
		'account_updates' => array(
				'sf' => 'im_account_updates_text_to_customer',
				'cp' => ''),
		'newsletter' => array(
				'sf' => 'im_newsletter_text_to_customer',
				'cp' => ''),
		'product_review' => array(
				'sf' => '',
				'cp' => 'im_product_review_text_to_admin'),
		'product_out_of_stock' => array (
				'sf' => '',
				'cp' => 'im_product_out_of_stock_admin_text')
	);
	public function __construct() {
		$this->registry = Registry::getInstance();
		$this->load->language('common/im');

	}
	/**
	 * @param $key
	 * @return mixed
	 */
	public function __get($key) {
		return $this->registry->get ( $key );
	}
	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key, $value) {
		$this->registry->set ( $key, $value );
	}

	/**
	 * @return array
	 */
	public function getProtocols(){
		return	$this->protocols;
	}

	/**
	 * @param string $name
	 */
	public function addProtocol($name){
		if($name && !in_array($name, $this->protocols)){
			$this->protocols[] = $name;
		}
	}

	/**
	 * Note: method can be called from admin side
	 * @param array $filter_arr
	 * @return array
	 */
	public function getIMDriverObjects( $filter_arr = array('status' => 1)){
		$filter = array(
				'category' => 'Communication'
				);
		//returns all drivers for admin side settings page
		if(has_value($filter_arr['status'])){
			$filter['status'] = (int)$filter_arr['status'];
		}

		$extensions = $this->extensions->getExtensionsList( $filter );
		$driver_list = array('email' => new AMailIM());

		if($filter_arr['status']){
			$active_drivers = array();
			foreach($this->protocols as $protocol){
				if($this->config->get('config_storefront_' . $protocol . '_status')){
					$active_drivers[] = $this->config->get('config_'.$protocol.'_driver');
				}
			}
		}

		foreach($extensions->rows as $ext){
			$driver_txt_id = $ext['key'];

			//skip non-installed
			if(!has_value($this->config->get($driver_txt_id.'_status'))){
				continue;
			}

			//skip non-active drivers
			if($filter_arr['status'] && !in_array($driver_txt_id, $active_drivers)){
				continue;
			}

			//NOTE! all IM drivers MUST have class by these path
			try{
				include_once(DIR_EXT . $driver_txt_id . '/core/lib/' . $driver_txt_id . '.php');
			}catch(AException $e){}
			$classname = preg_replace('/[^a-zA-Z]/','',$driver_txt_id);

			if(!class_exists($classname)){
				continue;
			}

			$driver = new $classname();
			$driver_list[$driver->getProtocol()] = $driver;
		}
		return $driver_list;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function addSendPoint($name){
		if($name && !in_array($name, $this->sendpoints)){
			$this->sendpoints[] = $name;
		}else{
			$error = new AError('SendPoint '.$name.' cannot be added into points list. Probably it already present there.');
			$error->toLog()->toMessages();
			return false;
		}
		return true;
	}

	public function send($sendpoint, $text_vars = array()){
		if(!IS_ADMIN){
			$sendpoints_list = $this->sendpoints;
			$this->load->model('account/customer');
			$customer_im_settings = $this->getCustomerNotificationSettings();
		}else{
			$sendpoints_list = $this->admin_sendpoints;
			//this method forbid to use for sending notifications to custromers from admin-side
			$customer_im_settings = array();
		}
		//check sendpoint
		if(!in_array($sendpoint,array_keys($sendpoints_list))){
			$error = new AError('IM error: sendpoint '.$sendpoint.' not found in preset of IM class. Nothing sent.');
			$error->toLog()->toMessages();
			return false;
		}
		$sendpoint_info = $sendpoints_list[$sendpoint];

		foreach($this->protocols as $protocol){
			$driver = null;

			//check protocol status
			if($protocol=='email'){
				//email notifications always enabled
				$protocol_status = 1;
			}elseif((int)$this->config->get('config_storefront_'.$protocol.'_status')
					||
					(int)$this->config->get('config_admin_'.$protocol.'_status')){
				$protocol_status = 1;
			}else{
				$protocol_status = 0;
			}

			if(!$protocol_status){
				continue;
			}

			if($protocol=='email'){
				//see AMailAIM class below
				$driver = new AMailIM();
			}else{
				$driver_txt_id = $this->config->get('config_' . $protocol . '_driver');

				//if driver not set - skip protocol
				if (!$driver_txt_id){
					continue;
				}

				if(!$this->config->get($driver_txt_id . '_status')){
					$error = new AError('Cannot send notification. Communication driver '.$driver_txt_id.' is disabled!');
					$error->toLog()->toMessages();
					continue;
				}

				//use safe usage
				$driver_file = DIR_EXT . $driver_txt_id . '/core/lib/' . $driver_txt_id . '.php';
				if(!is_file($driver_file)){
					$error = new AError('Cannot find file '.$driver_file.' to send notification.');
					$error->toLog()->toMessages();
					continue;
				}
				try{
					include_once($driver_file);
					//if class of driver
					$classname = preg_replace('/[^a-zA-Z]/', '', $driver_txt_id);
					if (!class_exists($classname)){
						$error = new AError('IM-driver ' . $driver_txt_id . ' load error.');
						$error->toLog()->toMessages();
						continue;
					}

					$driver = new $classname();
				} catch(AException $e){}
			}
			//if driver cannot be initialized - skip protocol
			if($driver===null){
				continue;
			}

			//send notification to customer
			if($customer_im_settings[$sendpoint][$protocol]){
				if ($this->config->get('config_storefront_' . $protocol . '_status') || $protocol == 'email'){
					//check is notification for this protocol and sendpoint allowed

					$text = $this->_get_message_text($sendpoint_info['sf'], $text_vars);
					$to = $this->_get_customer_im_uri($protocol);

					if ($text && $to){
						//use safe call
						try{
							$driver->send($to, $text);
						} catch(AException $e){}
					}
				}
			}

			//send notification to admins
			if($this->config->get('config_admin_'.$protocol.'_status') || $protocol=='email'){
				$text = $this->_get_message_text($sendpoint_info['cp'], $text_vars);
				//NOTE! all admins will receipt IMs
				$to = $this->_get_admin_im_uri($sendpoint, $protocol);

				if ($text && $to){
					//use safe call
					try{
						$driver->sendFew($to, $text);
					}catch(AException $e){}
				}
			}

			unset($driver);
		}
	}

	private function getCustomerNotificationSettings(){
		$settings = array();
		$protocols = $this->protocols;
		//todo: in the future should to create separate configurable sendpoints list for guests
		$sendpoints = $this->sendpoints;
		if($this->customer->isLogged()){
			$settings = $this->model_account_customer->getCustomerNotificationSettings();
		}
		//for guests before order creation
		elseif($this->session->data['guest']){
			//get im settings for guest
			$guest_data = $this->session->data['guest'];

			foreach($sendpoints as $sendpoint=>$row){
				foreach($protocols as $protocol){
					if( $guest_data[$protocol] ){
						//allow to send notifications only when it allowed by global IM settings
						if((int)$this->config->get('config_storefront_'.$protocol.'_status')
							&& (int)$this->config->get('config_im_guest_' . $protocol . '_status')){
							$protocol_status = 1;
						}else{
							$protocol_status = 0;
						}
						$settings[$sendpoint][$protocol] = $protocol_status;
					}
				}
			}
		}//for guests after order placement
		elseif($this->session->data['order_id']){

			$p = array();
			foreach($protocols as $protocol){
				$p[] = $this->db->escape($protocol);
			}

			$sql = "SELECT DISTINCT odt.`type_id`, odt.`name` as protocol, od.data
					FROM ".$this->db->table('order_data_types')." odt
					LEFT JOIN ".$this->db->table('order_data')." od
						ON (od.type_id = odt.type_id AND od.order_id = '".(int)$this->session->data['order_id']."')
					WHERE odt.`name` IN ('".implode("', '",$p)."')";
			$result = $this->db->query($sql);

			if($result->rows){
				foreach ($result->rows as $row){
					$guest_data = unserialize($row['data']);
					foreach ($sendpoints as $sendpoint => $r){
						$settings[$sendpoint][$row['protocol']] = (int)$guest_data['status'];
					}
				}
			}
		}

		return $settings;
	}

	private function _get_message_text($text_key, $text_vars){
		$text = $this->language->get($text_key);
		//check is text_key have value. If does not - abort sending
		if($text == $text_key){
			return '';
		}
		//put text vars into language definitions (replace %s with given vars)
		//check %s inside of text and compare it with text vars count to prevent error in vsprintf function below
		$s_num = substr_count($text, '%s');
		$text_var_count = sizeof($text_vars);
		if($s_num != $text_var_count){
			$diff = $s_num - $text_var_count;
			if($diff>0){
				while($diff>0){
					$text_vars[] = '';
					$diff--;
				}
			}else{
				while($diff<0){
					array_pop($text_vars);
					$diff++;
				}
			}
		}

		$text = vsprintf($text,$text_vars);

		//process formatted url like #storefront#rt=...
		$text = $this->html->convertLinks($text);
		return $text;
	}

	/**
	 * @param string $protocol
	 * @return string
	 */
	private function _get_customer_im_uri($protocol){

		$customer_id = (int)$this->customer->getId();
		//for registered customers - get adress from database
		if($customer_id){
			$sql = "SELECT *
					FROM ".$this->db->table('customers')."
					WHERE customer_id=".(int)$this->customer->getId();
			$customer_info = $this->db->query($sql, true);
			return $customer_info->row[$protocol];
		}elseif($this->session->data['order_id']){
			//if guests - get im-data from order_data tables

			$sql = "SELECT *
					FROM ".$this->db->table('order_data')." od
					WHERE `type_id` in ( SELECT DISTINCT type_id
										 FROM ".$this->db->table('order_data_types')."
										 WHERE `name`='".$this->db->escape($protocol)."' )
						AND order_id = '".(int)$this->session->data['order_id']."'";
			$result = $this->db->query($sql);

			if($result->row['data']){
				$im_info = unserialize($result->row['data']);
				if($im_info['status'] && $im_info['uri']){
					return $im_info['uri'];
				}
			}
			return '';
		}
		//when guest checkout - take uri from session
		elseif($this->session->data['guest'][$protocol]){
			return $this->session->data['guest'][$protocol];
		}

	}

	private function _get_admin_im_uri($sendpoint, $protocol){
		$section = IS_ADMIN===true ? 1 : 0;
		$output = array();
		$sql = "SELECT *
				FROM ".$this->db->table('user_notifications')."
				WHERE protocol='".$this->db->escape($protocol)."'
					AND sendpoint = '".$this->db->escape($sendpoint)."'
					AND section = '".$section."'
					AND store_id = '".(int)$this->config->get('config_store_id')."'";
		$result = $this->db->query($sql);
		foreach($result->rows as $row){
			$uri = trim($row['uri']);
			if($uri){
				$output[] = $uri;
			}
		}

		return $output;
	}



}


//email driver for notification class use
final class AMailIM{
	public $errors = array();
	private $registry;
	private $config;
	private $language;
	public function __construct(){
		$this->registry = Registry::getInstance();
		$this->language = $this->registry->get('language');
		$this->language->load('common/im');
		$this->config = $this->registry->get('config');
	}

	public function getProtocol(){
		return 'email';
	}

	public function getProtocolTitle(){
		return $this->language->get('im_protocol_email_title') ;
	}

	public function getName(){
		return 'Email';
	}

	public function send($to, $text){

		$to = trim($to);
		$text = trim($text);
		if(!$to || !$text){
			return false;
		}

		$mail = new AMail($this->config);
		$mail->setTo($to);
		$mail->setFrom($this->config->get('store_main_email'));
		$mail->setSender($this->config->get('store_name'));
		$mail->setSubject($this->config->get('store_name').' '.$this->language->get('im_text_notification'));
		$mail->setHtml($text);
		$mail->setText($text);
		$mail->send();
		unset($mail);

	}

	/**
	 * @param array $to
	 * @param $text
	 */
	public function sendFew($to, $text){
		foreach($to as $uri){
			$this->send($uri, $text);
		}
	}

	public function validateURI($email){
		$this->errors = array();
		if((mb_strlen($email) > 96) || (!preg_match(EMAIL_REGEX_PATTERN, $email))){
			$this->errors[] = sprintf($this->language->get('im_error_mail_address'),$email);
		}

		if($this->errors){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * Function builds form element for storefront side (customer account page)
	 *
	 * @param AForm $form
	 * @param string $value
	 * @return object
	 */
	public function getURIField($form, $value=''){
		return '';
	}
}
