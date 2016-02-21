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
if (!defined('DIR_CORE')){
	header('Location: static_pages/');
}

class AIMManager extends AIM{
	protected $registry;
	public $errors = array ();
	//add sendpoints list for admin-side
	/**
	 * @var array
	 * * NOTE:
	 * each key of array is text_id of sendpoint.
	 * To get sendpoint title needs to request language definition in format im_sendpoint_name_{sendpoint_text_id}
	 * All core sendpoint titles saved in common/im language block for both sides (admin & storefront)
	 * Values of array is language definitions key that stores in the same block. This values can have %s that will be replaced by sendpoint text variables.
	 * for ex. message have url to product page. Text will have #storefront#rt=product/product&product_id=%s and customer will receive full url to product.
	 * Some sendpoints have few text variables, for ex. order status and order status name
	 * For additional sendpoints ( from extensions) you can store language keys wherever you want.
	 */
	public $admin_sendpoints = array (
			'product_creates'      => array (
					'cp' => 'im_product_created_admin_text'),
			'product_updates'      => array (
					'cp' => 'im_product_updated_admin_text'),
			/* for future
			'product_out_of_stock' => array (
					'cp' => 'im_product_out_of_stock_admin_text'),
			'order_updates'        => array (
								'sf' => 'im_order_updates_text_to_customer',
								'cp' => 'im_order_updates_text_to_admin'),
			*/
			'product_review'       => array (
					'sf' => '',
					'cp' => 'im_product_review_text_to_admin'),
	);

	//NOTE: This class is loaded in INIT for admin only
	public function __construct(){
		parent::__construct();
		if (!IS_ADMIN){ // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to access class AIMManager');
		}
	}

	public function send($sendpoint, $text_vars = array ()){
		return parent::send($sendpoint, $text_vars);
	}

	public function getUserIMs($user_id, $store_id){
		$user_id = (int)$user_id;
		$store_id = (int)$store_id;
		if (!$user_id){
			return array ();
		}

		$sql = "SELECT *
				FROM " . $this->db->table('user_notifications') . "
				WHERE user_id=" . $user_id . "
					AND store_id = '" . $store_id . "'
				ORDER BY `sendpoint`, `protocol`";
		$result = $this->db->query($sql);

		$output = array ();
		foreach ($result->rows as $row){
			$section = (int)$row['section'] ? 'admin' : 'storefront';
			$sendpoint = $row['sendpoint'];
			unset($row['sendpoint']);
			$output[$section][$sendpoint][] = $row;
		}
		return $output;
	}

	public function getUserSendPointSettings($user_id, $section, $sendpoint, $store_id){
		$user_id = (int)$user_id;
		$store_id = (int)$store_id;

		if (!$user_id || !$sendpoint || !in_array($section, array ('admin', 'storefront'))){
			return array ();
		}

		$section = $section == 'admin' ? 1 : 0;

		$sql = "SELECT *
				FROM " . $this->db->table('user_notifications') . "
				WHERE user_id=" . $user_id . "
					AND store_id = '" . $store_id . "'
					AND sendpoint = '" . $this->db->escape($sendpoint) . "'
					AND section = '" . $section . "'
				ORDER BY `protocol`";
		$result = $this->db->query($sql);

		$output = array ();
		foreach ($result->rows as $row){
			$output[$row['protocol']] = $row['uri'];
		}
		return $output;
	}

	public function validateUserSettings($settings){
		$this->errors = array ();
		if (!$settings){
			return null;
		}
		//get all installed drivers
		$drivers = $this->getIMDriverObjects(array ('status' => ''));
		$supported_protocols = array_keys($drivers);
		foreach ($settings as $protocol => $uri){

			//ignore non-supported protocols
			if (!in_array($protocol, $supported_protocols) || !$uri){
				continue;
			}
			$driver = $drivers[$protocol];
			if (!$driver->validateURI($uri)){
				$this->errors[$protocol] = implode('<br>', $driver->errors);
			}
		}

		if ($this->errors){
			return false;
		} else{
			return true;
		}
	}

	public function saveIMSettings($user_id, $section, $sendpoint, $store_id, $settings = array ()){

		$user_id = (int)$user_id;
		$store_id = (int)$store_id;
		$settings = (array)$settings;
		if (!$user_id || !$sendpoint || !in_array($section, array ('admin', 'storefront'))){
			return false;
		}

		$section = $section == 'admin' ? 1 : 0;

		foreach ($settings as $protocol => $uri){
			$sql = "DELETE FROM " . $this->db->table('user_notifications') . "
				WHERE user_id=" . $user_id . "
					AND store_id = '" . $store_id . "'
					AND section = '" . $section . "'
					AND sendpoint = '" . $this->db->escape($sendpoint) . "'
					AND protocol='" . $this->db->escape($protocol) . "'";
			$this->db->query($sql);

			$sql = "INSERT INTO " . $this->db->table('user_notifications') . "
					(user_id, store_id, section, sendpoint, protocol, uri, date_added)
					VALUES ('" . $user_id . "',
							'" . $store_id . "',
							'" . $section . "',
							'" . $this->db->escape($sendpoint) . "',
							'" . $this->db->escape($protocol) . "',
							'" . $this->db->escape($uri) . "',
							NOW())";
			$this->db->query($sql);
		}

		return true;
	}

	public function getIMDriversList(){
		$filter = array (
				'category' => 'Communication'
		);
		$extensions = $this->extensions->getExtensionsList($filter);
		$driver_list = array ();
		foreach ($extensions->rows as $ext){
			$driver_txt_id = $ext['key'];
			//NOTE! all IM drivers MUST have class by these path
			try{
				include_once(DIR_EXT . $driver_txt_id . '/core/lib/' . $driver_txt_id . '.php');
			} catch(AException $e){
			}
			$classname = preg_replace('/[^a-zA-Z]/', '', $driver_txt_id);

			if (!class_exists($classname)){
				continue;
			}

			$driver = new $classname();
			$driver_list[$driver->getProtocol()][$driver_txt_id] = $driver->getName();
		}
		return $driver_list;
	}


}