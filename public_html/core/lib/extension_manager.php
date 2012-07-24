<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright  2011 Belavier Commerce LLC

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

class AExtensionManager {
	protected $registry;
	public $errors = 0;
	protected $extension_types = array( 'extensions', 'payment', 'shipping', 'template' );

	public function __construct($tmpl_id = '', $page_id = '', $layout_id = '') {
		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to access extension manager');
		}

		$this->registry = Registry::getInstance();
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	public function getInstalled($type = '') {
		return $this->extensions->getInstalled($type);
	}


	public function getExtensionInfo($key = '') {
		return $this->extensions->getExtensionInfo($key);
	}

	public function getExtensionsList($data = array()) {
		return $this->extensions->getExtensionsList($data);
	}

	public function add($data) {
		if (is_array($data)) {
			$type = in_array($data[ 'type' ], $this->extension_types)
					? $data[ 'type' ] : 'extensions';
			$key = $data[ 'key' ];
			$status = $data[ 'status' ];
			$priority = $data[ 'priority' ];
			$version = $data[ 'version' ];
			$license_key = $data[ 'license_key' ];
			$category = $data[ 'category' ];

		} else {
			$key = $data;
			$type = 'extensions';
		}

		$this->db->query("INSERT INTO " . DB_PREFIX . "extensions
					 	 SET `type` = '" . $this->db->escape($type) . "',
							 `key` = '" . $this->db->escape($key) . "',
							 `category` = '" . $this->db->escape($category) . "',
							 `status` = '" . $this->db->escape($status) . "',
							 `priority` = '" . $this->db->escape($priority) . "',
							 `version` = '" . $this->db->escape($version) . "',
							 `license_key` = '" . $this->db->escape($license_key) . "',
							 `create_date` = NOW()");
	}

	public function editSetting($group, $data) {

		if (empty($data)) return;
		if (empty($group)) {
			$error = new AError ("Error: Can't edit setting because field \"group\" is empty. Settings array: " . implode(",", array_keys($data)));
			$error->toLog()->toDebug();
			return;
		}

		$masks = array( 'status', 'version', 'date_installed', 'priority', 'license_key' );

		$keys = array_keys($data);
		unset($keys['store_id']);

		$this->db->query("DELETE FROM " . DB_PREFIX . "settings
						  WHERE `group` = '" . $this->db->escape($group) . "'
						        AND `key` IN ('" . implode("', '", $keys) . "')
						        AND `store_id` = '".(int)$data['store_id']."' ");

		foreach ($data as $key => $value) {
			$setting_name = str_replace($group . "_", '', $key);
			$this->db->query("INSERT INTO " . DB_PREFIX . "settings
							  SET `store_id` = '".(int)$data['store_id']."',
							      `group` = '" . $this->db->escape($group) . "',
							      `key` = '" . $this->db->escape($key) . "',
							      `value` = '" . $this->db->escape($value) . "'");
			if (in_array($setting_name, $masks)) {
				$sql = "UPDATE " . DB_PREFIX . "extensions
						SET `" . $setting_name . "` = '" . $this->db->escape($value) . "'
						WHERE  `key` = '" . $this->db->escape($group) . "'";
				$this->db->query($sql);
			}
		}
		// update date of changes in extension list
		$sql = "UPDATE " . DB_PREFIX . "extensions
						SET `update_date` = NOW()
						WHERE  `key` = '" . $this->db->escape($group) . "'";
		$this->db->query($sql);
		$this->cache->delete('admin_menu');
		$this->cache->delete('settings');
	}
	// method deletes all settings of extension
	public function deleteSetting($group) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "settings WHERE `group` = '" . $this->db->escape($group) . "'");
        $this->cache->delete('settings');
	}

	/**
	 * extension install actions
	 * db queries
	 * copying files
	 * etc
	 *
	 * @return null
	 */
	public function install($name, $config) {

		$ext = new ExtensionUtils($name);
		// gets extension_id for install.php
		$extension_info = $this->getExtensionsList(array('search'=>$name));
		$extension_id = $extension_info->row['extension_id'];

		$validate = $ext->validateCoreVersion();
		$errors = $ext->getError();

		if($errors){
			$this->session->data['error'] = implode("<br>",$errors);
		}
		if(!$validate){
			$error = new AError ( $errors );
			$error->toLog ()->toDebug ();
			return false;
		}

		//install default settings
		$settings = array(
			$name . '_layout' => (string)$config->layout,
			$name . '_priority' => (string)$config->priority,
			$name . '_date_installed' => date("Y-m-d H:i:s", time()),
		);

		if (isset($config->settings->item)) {
			foreach ($config->settings->item as $item) {
				$settings[ (string)$item[ 'id' ] ] = htmlentities((string)$item->default_value,ENT_QUOTES,'UTF-8');
			}
		}

		//write info about install into install log
		$install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
		$install_upgrade_history->addRows(array( 'date_added' => date("Y-m-d H:i:s", time()),
		                                       'name' => $name,
		                                       'version' => $settings[ $name . '_version' ],
		                                       'backup_file' => '',
		                                       'backup_date' => '',
		                                       'type' => 'install',
		                                       'user' => $this->registry->get('user')->getUsername() ));


		// running sql install script if it exists
		if (isset($config->install->sql)) {
			$file = DIR_EXT . str_replace('../', '', $name) . '/' . (string)$config->install->sql;
			if (is_file($file)) {
				$this->db->performSql($file);
			}
		}
		// running php install script if it exists
		if (isset($config->install->trigger)) {
			$file = DIR_EXT . str_replace('../', '', $name) . '/' . (string)$config->install->trigger;
			if (is_file($file)) {
				include($file);
			}
		}

		// refresh data about updates
		$this->load->model('tool/updater');
		$this->model_tool_updater->check4updates();

		//save settings
		$this->editSetting($name, $settings);

	}

	public function uninstall($name, $config) {
		//TODO: need to check dependencies for uninstalled extension

		// check dependencies
		$ext = new ExtensionUtils($name);
		$validate = $ext->checkDependants();
		if(!$validate){
			$this->session->data['error'] = implode("<br>",$ext->getError());
			return false;
		}

		//write info about install into install log
		$info = $this->extensions->getExtensionInfo($name);

		if ($info[ 'type' ] == 'payment' && $this->config->get($name . '_status')) {
			$this->load->language('extension/extensions');
			$this->session->data[ 'error' ] = $this->language->get('error_payment_uninstall');
			return false;
		}

		$install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
		$install_upgrade_history->addRows(array( 'date_added' => date("Y-m-d H:i:s", time()),
		                                       'name' => $name,
		                                       'version' => $info[ 'version' ],
		                                       'backup_file' => '',
		                                       'backup_date' => '',
		                                       'type' => 'uninstall',
		                                       'user' => $this->registry->get('user')->getUsername() ));

		if (isset($config->uninstall->sql)) {
			$file = DIR_EXT . str_replace('../', '', $name) . '/' . (string)$config->uninstall->sql;
			if (is_file($file)) {
				$this->db->performSql($file);
			}
		}
		// running php uninstall script if it exists
		if (isset($config->uninstall->trigger)) {
			$file = DIR_EXT . str_replace('../', '', $name) . '/' . (string)$config->uninstall->trigger;
			if (is_file($file)) {
				include($file);
			}
		}

		//set status to off
		$this->editSetting($name, array( 'status' => 0 ));
		//uninstall settings
		$this->deleteSetting($name);
	}


	public function delete($name) {
		$info = $this->extensions->getExtensionInfo($name);
		$install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
		$install_upgrade_history->addRows(array( 'date_added' => date("Y-m-d H:i:s", time()),
		                                       'name' => $name,
		                                       'version' => $info[ 'version' ],
		                                       'backup_file' => '',
		                                       'backup_date' => '',
		                                       'type' => 'delete',
		                                       'user' => $this->registry->get('user')->getUsername() ));

		$this->db->query("DELETE FROM " . DB_PREFIX . "extensions WHERE `type` = '" . $info[ 'type' ] . "' AND `key` = '" . $this->db->escape($name) . "'");

		$this->registry->get('session')->data[ 'package_info' ][ 'ftp' ] = false;
		$pmanager = new APackageManager();
		$result = $pmanager->removeDir(DIR_EXT . $name);

		if (!$result) {
			$message = "Error: Can't to delete file or directory: '" . DIR_EXT . $name . "'. No file permissions, change permissions to 777 with your FTP access";
			$this->session->data[ 'error' ] = $message;
		}

		// refresh data about updates
		$this->load->model('tool/updater');
		$this->model_tool_updater->check4updates();
	}
}