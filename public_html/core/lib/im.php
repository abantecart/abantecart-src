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
 */

class AIM {
	private $registry;
	private $protocols = array('sms');

	public $sendpoints = array(
		'order_created' => array(
				'sf' => 'im_order_created_text_to_customer',
				'cp' => 'im_order_created_text_to_admin')
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

	public function getIMDrivers(){
		$extensions = $this->extensions->getExtensionsList(array( 'category' => 'IM-drivers', 'status' => 1 ));

		$driver_list = array();
		foreach($extensions->rows as $ext){

			$driver_txt_id = $ext['key'];
			//NOTE! all IM drivers MUST have class by these path
			try{
				include_once(DIR_EXT . $driver_txt_id . '/core/lib/' . $driver_txt_id . '.php');
			}catch(AException $e){}
			$classname = preg_replace('/[^a-zA-Z]/','',$driver_txt_id);

			if(!class_exists($classname)){
				continue;
			}

			$driver = new $classname();
			$driver_list[$driver->getProtocol()][$driver_txt_id] = $driver->getName();
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
			$this->log->write('SendPoint '.$name.' cannot be added into points list. Probably it already present there.');
			return false;
		}
		return true;
	}

	public function send($sendpoint){
		//check sendpoint
		if(!in_array($sendpoint,array_keys($this->sendpoints))){
			$this->log->write('IM error: sendpoint '.$sendpoint.' not found in preset of IM class. Nothing sent.');
			return false;
		}
		$sendpoint_info = $this->sendpoints[$sendpoint];

		foreach($this->protocols as $protocol){
			$driver_txt_id = $this->config->get('config_'.$protocol.'_driver');

			//if driver not set - skip protocol
			if(!$driver_txt_id){
				continue;
			}
			try{
				include_once(DIR_EXT . $driver_txt_id . '/core/lib/' . $driver_txt_id . '.php');
			}catch(AException $e){}

			//if class of driver
			$classname = preg_replace('/[^a-zA-Z]/','',$driver_txt_id);
			if(!class_exists($classname)){
				$error = new AError('IM-driver '.$driver_txt_id.' load error.');
				$error->toLog()->toMessages();
				continue;
			}

			$driver = new $classname();

			//for customer
			if($this->config->get('config_storefront_'.$protocol.'_status')){
				$text = $this->_get_message_text($sendpoint_info['sf']);
				$to = $this->_get_customer_im_address($protocol);
				if ($text && $to){
					//use safe call
					try{
						$driver->send($to, $text);
					}catch(AException $e){}
				}
			}

			//for admins
			if($this->config->get('config_admin_'.$protocol.'_status')){
				$text = $this->_get_message_text($sendpoint_info['cp']);
				//NOTE! all admins will receipt IMs
				$to = $this->_get_admin_im_addresses($protocol);
				if ($text && $to){
					foreach ($to as $admin_im){
						//use safe call
						try{
							$driver->send($admin_im, $text);
						}catch(AException $e){}
					}
				}
			}

			unset($driver);
		}
	}

	private function _get_message_text($text_key){
		$text = $this->language->get($text_key);
		//check is text_key have value. If does not - skip sending
		if($text == $text_key){
			return '';
		}
		//process formatted url like #storefront#rt=...
		$text = $this->html->convertLinks($text);
		return $text;
	}

	private function _get_customer_im_address($protocol){
		if(!(int)$this->customer->getId()){
			return array();
		}
		$sql = "SELECT * FROM ".$this->db->table('customers')." WHERE customer_id=".(int)$this->customer->getId();

		$customer_info = $this->db->query($sql, true);
		return $customer_info->row[$protocol];
	}


//todo
	private function _get_admin_im_addresses($protocol){


		return array();
	}

}
