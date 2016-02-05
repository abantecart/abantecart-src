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

class AIMManager extends AIM {
	protected $registry;
	public    $errors = array();

	//NOTE: This class is loaded in INIT for admin only
	public function __construct() {
		parent::__construct();
		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to access class AIMManager');
		}

		//override sendpoints list for admin-side
		$this->sendpoints = array(
				'order_updated' => array(
						'sf' => 'im_order_updated_text_to_customer',
						'cp' => 'im_order_updated_text_to_admin'),
				'product_created' => array(
						'sf' => '',
						'cp' => 'im_product_created_admin_text'),
				'product_updated' => array(
						'sf' => '',
						'cp' => 'im_product_updated_admin_text'),
		);


	}

	public function getUserIMs($user_id, $store_id){
		$user_id = (int)$user_id;
		$store_id = (int)$store_id;
		if(!$user_id){
			return array();
		}

		$sql="SELECT *
				FROM ".$this->db->table('user_notifications')."
				WHERE user_id=".$user_id."
					AND store_id = '".$store_id."'
				ORDER BY `sendpoint`, `protocol`";
		$result = $this->db->query($sql);

		$output = array();
		foreach($result->rows as $row){
			$sendpoint = $row['sendpoint'];
			unset($row['sendpoint']);
			$output[$sendpoint][] = $row;
		}
		return $output;
	}

	public function getUserSendPointSettings($user_id, $sendpoint, $store_id){
		$user_id = (int)$user_id;
		$store_id = (int)$store_id;
		if(!$user_id || !$sendpoint){
			return array();
		}

		$sql="SELECT *
				FROM ".$this->db->table('user_notifications')."
				WHERE user_id=".$user_id."
					AND store_id = '".$store_id."'
					AND sendpoint = '".$this->db->escape($sendpoint)."'
				ORDER BY `protocol`";
		$result = $this->db->query($sql);

		$output = array();
		foreach($result->rows as $row){
			$output[$row['protocol']] = $row['uri'];
		}
		return $output;
	}

	public function validateUserSettings(){

		return true;
	}

	public function saveIMSettings($user_id, $sendpoint, $store_id, $settings = array()){

		$user_id = (int)$user_id;
		$store_id = (int)$store_id;
		$settings = (array)$settings;
		if(!$user_id || !$sendpoint){
			return false;
		}

		foreach($settings as $protocol=> $uri){
			$sql = "DELETE FROM " . $this->db->table('user_notifications') . "
				WHERE user_id=" . $user_id . "
					AND store_id = '" . $store_id . "'
					AND sendpoint = '" . $this->db->escape($sendpoint) . "'
					AND protocol='".$this->db->escape($protocol)."'";
			$this->db->query($sql);

			$sql = "INSERT INTO " . $this->db->table('user_notifications') . "
					(user_id, store_id, sendpoint, protocol, uri, date_added)
					VALUES ('" . $user_id . "',
							'" . $store_id . "',
							'" . $this->db->escape($sendpoint) . "',
							'" . $this->db->escape($protocol) . "',
							'" . $this->db->escape($uri) . "',
							NOW())";
			$this->db->query($sql);
		}

		return true;
	}

}