<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright  2011-2015 Belavier Commerce LLC

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
 * @property ExtensionsApi $extensions
 * @property ADB $db
 * @property ACache $cache
 * @property AConfig $config
 * @property ALanguage $language
 * @property ALoader $load
 * @property ModelToolUpdater $model_tool_updater
 * @property Ahtml $html
 * @property AUser $user
 * @property AMessage $messages
 * @property ModelSettingStore $model_setting_store
 * */
class AExtensionManager {
	/**
	 * @var Registry
	 */
	protected $registry;
	/**
	 * @var array
	 */
	public $errors = array();
	/**
	 * @var array extension type list that manager can to install-uninstall
	 */
	protected $extension_types = array('extension', 'extensions', 'payment', 'shipping', 'template');

	public function __construct() {
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

	/**
	 * @param array $data
	 * @return int extension_id
	 */
	public function add($data) {
		if (is_array($data)) {
			// check colision
			$data['type'] = $data['type'] == 'extension' ? 'extensions' : $data['type'];
			$type = ($data['type'] ? $data['type'] : 'extensions');
			$key = $data['key'];
			$status = $data['status'];
			$priority = $data['priority'];
			$version = $data['version'];
			$license_key = $data['license_key'];
			$category = $data['category'];

		} else {
			$key = $data;
			$type = 'extensions';
		}
		$sql = "SELECT extension_id FROM " . $this->db->table("extensions") . " WHERE `key`= '" . $this->db->escape($key) . "'";
		$res = $this->db->query($sql);
		if ($res->num_rows) {
			return $res->row['extension_id'];
		}

		$this->db->query("INSERT INTO " . $this->db->table("extensions") . " 
					 	 SET `type` = '" . $this->db->escape($type) . "',
							 `key` = '" . $this->db->escape($key) . "',
							 `category` = '" . $this->db->escape($category) . "',
							 `status` = '" . $this->db->escape($status) . "',
							 `priority` = '" . $this->db->escape($priority) . "',
							 `version` = '" . $this->db->escape($version) . "',
							 `license_key` = '" . $this->db->escape($license_key) . "',
							 `date_added` = NOW()");
		return $this->db->getLastId();
	}

	/**
	 * Function gets parent extensions id and text id from extension dependencies table
	 * @param string $extension_txt_id
	 * @return array
	 */
	public function getParentsExtensionTextId($extension_txt_id) {
		$info = $this->extensions->getExtensionInfo($extension_txt_id);
		$extension_id = (int)$info['extension_id'];
		if (!$extension_id) return false;

		$result = $this->db->query("SELECT e.key, ed.extension_parent_id, e.status
										FROM " . $this->db->table("extension_dependencies") . " ed
										LEFT JOIN " . $this->db->table("extensions") . " e ON ed.extension_parent_id = e.extension_id
										WHERE ed.extension_id = '" . $extension_id . "'");
		return $result->rows;
	}

	/**
	 * @param string $parent_extension_txt_id
	 * @return array
	 */
	public function getChildrenExtensions($parent_extension_txt_id) {
		$info = $this->extensions->getExtensionInfo($parent_extension_txt_id);
		$extension_id = (int)$info['extension_id'];
		if (!$extension_id) return array();

		$result = $this->db->query("SELECT e.*
										FROM " . $this->db->table("extension_dependencies") . " ed
										LEFT JOIN " . $this->db->table("extensions") . " e ON ed.extension_id = e.extension_id
										WHERE ed.extension_parent_id = '" . $extension_id . "'");
		return $result->rows;
	}

	/**
	 * @param $extension_txt_id
	 * @param $extension_parent_txt_id
	 * @internal param int $extension_id
	 * @internal param int $extension_parent_id
	 * @return bool
	 */
	public function addDependant($extension_txt_id, $extension_parent_txt_id) {
		$info = $this->extensions->getExtensionInfo($extension_parent_txt_id);
		$extension_parent_id = (int)$info['extension_id'];
		$info = $this->extensions->getExtensionInfo($extension_txt_id);
		$extension_id = (int)$info['extension_id'];
		if (!$extension_id || !$extension_parent_id) return false;

		$result = $this->db->query("SELECT *
									FROM " . $this->db->table("extension_dependencies") . " 
									WHERE extension_id = '" . $extension_id . "' AND extension_parent_id = '" . $extension_parent_id . "'");
		if (!$result->num_rows) {
			$sql = "INSERT INTO " . $this->db->table("extension_dependencies") . " (extension_id, extension_parent_id )
							VALUES ('" . $extension_id . "', '" . $extension_parent_id . "')";
			$this->db->query($sql);
		}
		return true;
	}

	/**
	 * function delete extension dependants from table by given id's
	 * @param string $extension_txt_id
	 * @param string $extension_parent_txt_id
	 * @internal param string $extension_id
	 * @internal param string $extension_parent_id
	 * @return bool
	 */
	public function deleteDependant($extension_txt_id = '', $extension_parent_txt_id = '') {

		$info = $this->extensions->getExtensionInfo($extension_parent_txt_id);
		$extension_parent_id = $info ? (int)$info['extension_id'] : 0;

		$info = $this->extensions->getExtensionInfo($extension_txt_id);
		$extension_id = $info ? (int)$info['extension_id'] : 0;


		if (!$extension_id && !$extension_parent_id) return false;

		$sql = "DELETE FROM " . $this->db->table("extension_dependencies") . " 
				WHERE ";
		if ($extension_id) {
			$where[] = "extension_id = '" . $extension_id . "'";
		}
		if ($extension_parent_id) {
			$where[] = "extension_parent_id = '" . $extension_parent_id . "'";
		}
		$sql .= implode(' AND ', $where);
		$this->db->query($sql);

		return true;
	}

	/**
	 * Save extention settings into database
	 * @param string $extension_txt_id
	 * @param array $data
	 * @return bool
	 */
	public function editSetting($extension_txt_id, $data) {

		if (empty($data)) return false;
		if (empty($extension_txt_id)) {
			$error = new AError ("Error: Can't edit setting because field \"extension_txt_id\" is empty. Settings array: " . implode(",", array_keys($data)));
			$error->toLog()->toDebug();
			return false;
		}
		// parameters that placed in extension table
		$masks = array('status', 'version', 'date_installed', 'priority', 'license_key');

		$keys = array_keys($data);
		unset($keys['store_id']);
		// check if settings required and it is not status
		$ext = new ExtensionUtils($extension_txt_id,(int)$data['store_id']);
		if(isset($data['one_field']) && !isset($data[$extension_txt_id . "_status"])){
			$validate = $ext->validateSettings($data);
			if(!$validate['result']){ // check is all required settings are set
				if(!isset($validate['errors'])){
					$this->errors[] = "Can't save setting because value is empty. ";
				}else{
					$this->load->language($extension_txt_id.'/'.$extension_txt_id);
					foreach($validate['errors'] as $field_id => $error_text){
						$this->errors[] = $error_text ? $error_text : $this->language->get($field_id.'_validation_error') ;
					}
				}
				return false;
			}
		}
		unset($data['one_field']); //remove sign to prevent writing into settings table
		$this->db->query("DELETE FROM " . $this->db->table("settings") . " 
						  WHERE `group` = '" . $this->db->escape($extension_txt_id) . "'
						        AND `key` IN ('" . implode("', '", $keys) . "')
						        AND `store_id` = '" . (int)$data['store_id'] . "' ");

		foreach ($data as $key => $value) {
			$setting_name = str_replace($extension_txt_id . "_", '', $key);
			//check if setting is multi-value (array) and save serialized value. 
			if (is_array($value)) {
				//validate values in array. If setting is array of all members = 0 save only single value of 0
				//This is to match standard post format in regular form submit
				$concat = implode('',$value);
				if (preg_match('/[^0]/',$concat)) {
					$value = serialize($value);
				} else {
					$value = 0;
				}
			}
			// status check
			if ($setting_name == 'status') {
				//when try to enable extension
				if ($value == 1) { // check is parent extension enabled
					$validate = $ext->validateSettings($data); // check is all required settings are set and valid
					if(!$validate['result']){
						$value = 0; // disable extension
						if(!isset($validate['errors'])){
							$error = "Cannot enable extension \"" . $extension_txt_id . "\". Please fill all required fields on settings edit page. ";
							$this->messages->saveError('App Error',$error);
							$this->errors[] = $error;
							$error = new AError ($error);
							$error->toLog()->toDebug();
						}else{
							$this->load->language($extension_txt_id.'/'.$extension_txt_id);
							foreach($validate['errors'] as $field_id => $error_text){
								$error = $error_text ? $error_text : $this->language->get($field_id.'_validation_error') ;
								$this->messages->saveError('App Error: '.$field_id, $error);
								$this->errors[] = $error;
								$error = new AError ($error);
								$error->toLog()->toDebug();
							}
						}
					}else{
						// if all fine with required fields - check childen
						$parents = $this->getParentsExtensionTextId($extension_txt_id);
						$enabled = $this->extensions->getEnabledExtensions();
						foreach ($parents as $parent) {
							if (!in_array($parent['key'], $enabled)) {
								$error = "Cannot enable extension \"" . $extension_txt_id . "\". It's depends on extension \"" . $parent['key'] . "\" which not enabled. ";
								$this->messages->saveError('Extension App Error',$error);
								$this->errors[] = $error;
								$error = new AError ($error);
								$error->toLog()->toDebug();
								//prevents enabling
								$value = 0;
								break;
							}
						}
					}

				} else { // When try to disable disable dependants too
					if ($this->isExtensionInstalled($extension_txt_id)) {
						$children_keys = array();
						$children = $this->getChildrenExtensions($extension_txt_id);

						foreach ($children as $child) {
							if ($this->config->get($child['key'] . "_status") == 1) {
								$children_keys[] = $this->db->escape($child['key']);
							}
						}
						if ($children_keys) {
							foreach ($children_keys as $child) {
								$sql = "UPDATE " . $this->db->table("settings") . " 
									SET `value` = 0
									WHERE `group` = '" . $child . "'
									AND `key`= '" . $child . "_status'";
								$this->db->query($sql);
							}
							$sql = "UPDATE " . $this->db->table("extensions") . " 
									SET `" . $setting_name . "` = '" . $this->db->escape($value) . "'
									WHERE  `key` IN ('" . implode("','", $children_keys) . "')";
							$this->db->query($sql);
						}
					}
				}
			}
			
			//Special case. 
			//Check that we have single mode RL with ID 
			if ( has_value($data[$key."_resource_id"]) && !has_value($value) ) {
				//save ID if resource path is missing
				$value = $data[$key."_resource_id"];
			} 
			//skip saving ???
			
			// now re-insert settings
			$this->db->query("INSERT INTO " . $this->db->table("settings") . " 
							  SET `store_id` = '" . (int)$data['store_id'] . "',
							      `group` = '" . $this->db->escape($extension_txt_id) . "',
							      `key` = '" . $this->db->escape($key) . "',
							      `value` = '" . $this->db->escape($value) . "'");
			if (in_array($setting_name, $masks)) {
				$sql = "UPDATE " . $this->db->table("extensions") . " 
						SET `" . $setting_name . "` = '" . $this->db->escape($value) . "'
						WHERE  `key` = '" . $this->db->escape($extension_txt_id) . "'";
				$this->db->query($sql);
			}
		}
		// update date of changes in extension list
		$sql = "UPDATE " . $this->db->table("extensions") . " 
						SET `date_modified` = NOW()
						WHERE  `key` = '" . $this->db->escape($extension_txt_id) . "'";
		$this->db->query($sql);
		$this->cache->delete('admin_menu');
		$this->cache->delete('settings');

		return true;
	}

	/**
	 * method deletes all settings of extension with language definitions
	 * @param string $group - extension text id
	 */
	public function deleteSetting($group) {
		$this->db->query("DELETE FROM " . $this->db->table("settings") . " WHERE `group` = '" . $this->db->escape($group) . "';");
		$this->db->query("DELETE FROM " . $this->db->table("language_definitions") . " WHERE `block` = '" . $this->db->escape($group) . "_" . $this->db->escape($group)."';");
		$this->cache->delete('settings');
		$this->cache->delete('language_definitions');
	}

	/**
	 * extension install actions, db queries, copying files etc
	 *
	 * @param string $name
	 * @param SimpleXMLElement $config
	 * @return bool|null
	 */
	public function install($name, $config) {

		$ext = new ExtensionUtils($name);
		// gets extension_id for install.php
		$extension_info = $this->getExtensionsList(array('search' => $name));
		$extension_id = $extension_info->row['extension_id'];

		$validate = $this->validateCoreVersion($extension_info->row['key'], $config);
		$errors = $ext->getError();

		if ($errors) {
			$this->session->data['error'] = implode("<br>", $errors);
		}
		if (!$validate) {
			$error = new AError ($errors);
			$error->toLog()->toDebug();
			return false;
		}

		//install default settings
		$default_settings = $ext->getDefaultSettings();
		$settings = array(
			$name . '_status' => 0,
			$name . '_layout' => (string)$config->layout,
			$name . '_priority' => (string)$config->priority,
			$name . '_date_installed' => date("Y-m-d H:i:s", time()),
		);

		$settings = array_merge($settings,$default_settings);

		//write info about install into install log
		$install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
		$install_upgrade_history->addRows(array('date_added' => date("Y-m-d H:i:s", time()),
			'name' => $name,
			'version' => $settings[$name . '_version'],
			'backup_file' => '',
			'backup_date' => '',
			'type' => 'install',
			'user' => $this->user->getUsername()));

		// add dependencies into database for required extensions only
		if (isset($config->dependencies->item)) {
			foreach ($config->dependencies->item as $item) {
				if ((boolean)$item['required']) {
					$this->addDependant($name, (string)$item);
				}
			}
		}

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


		//save default settings for all stores
		$this->load->model('setting/store');
		$stores = $this->model_setting_store->getStores();
		foreach($stores as $store){
			$settings['store_id'] = $store['store_id'];
			$this->editSetting($name, $settings);
		}
		return null;
	}

	/**
	 * @param string $name
	 * @param SimpleXmlElement $config
	 * @return bool|null
	 */
	public function uninstall($name, $config) {

		// check dependencies
		/*
		$ext = new ExtensionUtils($name);
		$validate = $ext->checkDependants();
		*/
		$validate = $this->checkDependants($name);
		if (!$validate) {
			$this->session->data['error'] = implode("<br>", $this->errors);
			return false;
		}

		//write info about install into install log
		$info = $this->extensions->getExtensionInfo($name);

		if ($info['type'] == 'payment' && $this->config->get($name . '_status')) {
			$this->load->language('extension/extensions');
			$this->session->data['error'] = $this->language->get('error_payment_uninstall');
			return false;
		}

		$install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
		$install_upgrade_history->addRows(array('date_added' => date("Y-m-d H:i:s", time()),
			'name' => $name,
			'version' => $info['version'],
			'backup_file' => '',
			'backup_date' => '',
			'type' => 'uninstall',
			'user' => $this->user->getUsername()));

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
		$this->editSetting($name, array('status' => 0));
		//uninstall settings
		$this->deleteSetting($name);
		return null;
	}

	/**
	 * @param string $extension_txt_id
	 * @return bool
	 */
	public function delete($extension_txt_id) {
		if(!trim($extension_txt_id)){
			$this->log->write('Error! Abantecart tried to delete by empty extension_txt_id');
			return false;
		}

		$info = $this->extensions->getExtensionInfo($extension_txt_id);
		$install_upgrade_history = new ADataset('install_upgrade_history', 'admin');
		$install_upgrade_history->addRows(array('date_added' => date("Y-m-d H:i:s", time()),
			'name' => $extension_txt_id,
			'version' => $info['version'],
			'backup_file' => '',
			'backup_date' => '',
			'type' => 'delete',
			'user' => $this->user->getUsername()));
		$this->db->query("DELETE FROM " . $this->db->table("extensions") . " WHERE `type` = '" . $info['type'] . "' AND `key` = '" . $this->db->escape($extension_txt_id) . "'");
		$this->deleteDependant($extension_txt_id);

		$this->session->data['package_info']['ftp'] = false;
		$pmanager = new APackageManager();
		$result = $pmanager->removeDir(DIR_EXT . $extension_txt_id);

		if (!$result) {
			$message = "Error: Can't to delete file or directory: '" . DIR_EXT . $extension_txt_id . "'. No file permissions, change permissions to 777 with your FTP access";
			$this->session->data['error'] = $message;
		}

		// refresh data about updates
		$this->load->model('tool/updater');
		$this->model_tool_updater->check4updates();
		return true;
	}

	/**
	 * @param string $extension_txt_id
	 * @return bool
	 */
	public function validate($extension_txt_id) {

		$result = $this->validateFreeSpace($extension_txt_id);
		if (!$result) {
			return false;
		}
		$result = $this->isExtensionInstalled($extension_txt_id);
		if ($result) {
			return false;
		}
		// get config.xml
		$config = getExtensionConfigXml($extension_txt_id);

		$result = $this->validateCoreVersion($extension_txt_id, $config);
		if (!$result) {
			return false;
		}
		$result = $this->validatePhpModules($extension_txt_id, $config);
		if (!$result) {
			return false;
		}
		$result = $this->validateDependencies($extension_txt_id, $config);
		if (!$result) {
			return false;
		}
		return true;
	}


	/**
	 *  is dependencies present
	 * @param string $extension_txt_id
	 * @param SimpleXmlElement $config
	 * @return bool
	 */
	public function validateDependencies($extension_txt_id, $config) {

		$extensions = $this->extensions->getEnabledExtensions();
		$all_extensions = $this->extensions->getExtensionsList();
		$versions = array();
		foreach ($all_extensions->rows as $ext) {
			$versions[$ext['key']] = $ext['version'];
		}
		if (!isset($config->dependencies->item)) return true;
		foreach ($config->dependencies->item as $item) {
			$required = (boolean)$item['required'];
			$version = (string)$item['version'];
			$prior_version = (string)$item['prior_version'];

			$item = (string)$item;
			// check existing of required
			if ($required && !in_array($item, $extensions)) {
				$this->errors[] = sprintf('<b>%s</b> extension cannot be installed: <b>%s</b> extension required and must be installed and enabled!', $extension_txt_id, $item);
			}
			// if extension installed - check version that need
			if ($version) {
				if ($required && (!versionCompare($version, $versions[$item], '>=') || !versionCompare($prior_version, $versions[$item], '<='))) {
					$this->errors[] = sprintf('<b>%s</b> extension cannot be installed: <b>%s</b> extension versions <b>' . $prior_version . ' - ' . $version . '</b> are required', $extension_txt_id, $item);
				}
			}
			if (sizeof($this->errors) > 0) {
				return false;
			}
		}

		return true;
	}


	/**
	 *  is dependendants installed?
	 * @param string $extension_txt_id
	 * @return bool
	 */
	public function checkDependants($extension_txt_id) {
		$extensions = $this->extensions->getInstalled('exts');
		foreach ($extensions as $extension) {
			if ($extension == $extension_txt_id) continue;
			$config = getExtensionConfigXml($extension);
			if (!isset($config->dependencies->item)) continue;
			foreach ($config->dependencies->item as $item) {
				$required = (boolean)$item['required'];
				$item = (string)$item;
				if ($item == $extension_txt_id && $required) {
					$this->errors[] = sprintf('<b>%s</b> extension cannot be uninstalled: <b>%s</b> extension depends on it. Please uninstall it first.', $extension_txt_id, $extension);
					return false;
				}
			}
		}
		return true;
	}


	/**
	 *  check free space
	 * //TODO: is really needed?
	 */
	public function validateFreeSpace() {
		return true;
	}


	/**
	 *  is extension already installed ( extension upgrade )
	 * @param string $extension_txt_id
	 * @return bool
	 */
	public function isExtensionInstalled($extension_txt_id) {
		$installed = $this->config->get($extension_txt_id . '_status');
		return $installed === null ? false : true;
	}


	/**
	 *  is extension support current core version
	 * @param string $extension_txt_id
	 * @param SimpleXmlElement $config
	 * @return bool
	 */
	public function validateCoreVersion($extension_txt_id, $config) {

		if (!isset($config->cartversions->item)) {
			$this->errors[] = 'Error: config file of extension does not contain any information about versions of AbanteCart where it can be run.';
			return false;
		}
		$cart_versions = array();
		foreach ($config->cartversions->item as $item) {
			$version = (string)$item;
			$cart_versions[] = $version;
		}
		// check is cart version presents on extension cart version list
		foreach ($cart_versions as $version) {
			$result = versionCompare(VERSION, $version, '>=');
			if ($result) {
				return true;
			}
		}
		// if not - seek cart earlier version then current cart version in the list
		foreach ($cart_versions as $version) {
			$result = versionCompare($version, VERSION, '<');
			if ($result) {
				$error_text = 'Extension <b>%s</b> written for earlier version of Abantecart (v.%s) lower that you have. ';
				$error_text .= 'Probably all will be OK.';
				$error_text = sprintf($error_text, $extension_txt_id, implode(', ', $cart_versions));
				$this->session->data['error'] = $error_text;
				$this->messages->saveWarning($extension_txt_id . ' extension warning', $error_text);
				return true;
			}
		}


		$error_text = '<b>%s</b> extension cannot be installed. AbanteCart version incompability. ';
		$error_text .= sizeof($cart_versions) > 1 ? 'Versions <b>%s</b> are required.' : 'Version <b>%s</b> is required.';
		$this->errors[] = sprintf($error_text, $extension_txt_id, implode(', ', $cart_versions));
		return false;
	}


	/**
	 *  is hosting support all php modules used by extension
	 */
	/**
	 * @param string $extension_txt_id
	 * @param SimpleXmlElement $config
	 * @return bool
	 */
	public function validatePhpModules($extension_txt_id, $config) {
		if (!isset($config->phpmodules->item)) return true;
		foreach ($config->phpmodules->item as $item) {
			$item = (string)$item;
			if (!extension_loaded($item)) {
				$this->errors[] = sprintf('<b>%s</b> extension cannot be installed: <b>%s</b> php module required', $extension_txt_id, $item);
				return false;
			}
		}
		return true;
	}

}