<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011, 2012 Belavier Commerce LLC

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

class ALanguage {
	public $entries = array();
	public $language_details;
	public $current_languages_scope = array(); //This is and array of available languages for calling scope
	public $is_admin = 0;
	public $error = '';

	protected $code = '';
	/**
	 * @var $db ADb
	 */
	protected $db;
	/**
	 * @var $cache ACache
	 */
	protected $cache;
	protected $registry;
	/**
	 * @var $loader ALoader
	 */
	protected $loader;
	protected $language_path;

	protected $available_languages = array(); //Aray of awailable languges configured in abantecart
	protected $current_language = array(); //current used main language array data

	public function __construct($registry, $code = '', $section = '') {
		$this->registry = $registry;
		if ($section === '') {
			$this->is_admin = ((!defined('IS_ADMIN') || !IS_ADMIN) ? 0 : 1);
		} else {
			$this->is_admin = $section;
		}
		$root_path = defined(INSTALL) ? DIR_ABANTECART : DIR_ROOT . '/';
		if ($this->is_admin) {
			$this->language_path = $root_path . 'admin/language/';
		} else {
			$this->language_path = $root_path . 'storefront/language/';
		}

		//Load available languages;
		$this->loader = $registry->get('load');

		$result = $this->loader->model('localisation/language', 'silent');
		if ($result !== FALSE) {
			$model = $registry->get('model_localisation_language');
			$this->available_languages = $model->getLanguages();
		} else {
			if (defined('INSTALL')) {
				$this->loader->model('install', 'silent');
				$model = $registry->get('model_install');
				$this->available_languages = $model->getLanguages();
			} else {
				//problem no languages available
				$err = new AError('Error: no languages available in AbanteCart !', AC_ERR_LOAD);
				$err->toLog()->toDebug();
				throw new AException(AC_ERR_LOAD, 'Error: Can not Load any language!');
			}
		}

		//If No language code, we need to detect language, set site language to use and set content language separately
		if (!$code) {
			$this->setCurrentLanguage();
			//session language contains main language code
			$this->code = $registry->get('session')->data['language'];
		} else {
			$this->code = $code;
		}

		$this->db = $registry->get('db');
		$this->cache = $registry->get('cache');
		$this->language_details = $this->getLanguageDetails($this->code);

		$this->entries = array();
	}

	/* Maim Language API methods */

	// NOTE; Template language variables do not use ->get and loaded automaticaly in controller class. 
	//		 There is no way to get acccess to used definitions and not possible to validate missing values  

	/*
	* Get single language definition
	* Input:
	*           Language definition key
	*           RT (block) for corresponding key (optional). Block will be loaded to memory if not yet loaded
	* Note: If RT is not provided definition key will be looked up in the stack of all loaded language blocks
	* Return: Definition value
	*/
	public function get($key, $block = '') {
		if (empty($key)) {
			return;
		}
		$return_text = '';
		//if no specific area specified return main language
		if (!empty($block)) {
			if (!$this->_is_loaded($block)) {
				$this->_load($block);
			}
			$return_text = $this->_get_language_value($key, $block);
		} else {
			$backtrace = debug_backtrace();
			$return_text = $this->_get_last_language_value($key, $backtrace);
		}
		if ( empty($return_text) ) {
			$return_text = $key;
		}
		return $return_text;
	}

	/*
	* Get all language definitions
	* Input:
	*           RT (block) for corresponding key. Block will be loaded to memory if not yet loaded
	* Note: If RT is not provided definition keys will be taken from main language section (ex: english.xml) if avaiblable
	* Return: Array with key/definision
	*/
	public function getASet($block = '') {
		//if no specific area specified return main language set
		if (empty($block) && empty($this->current_languages_scope)) {
			$block = $this->language_details['filename'];
		} else if (!empty($block) && !$this->_is_loaded($block)) {
			$this->_load($block);
		}
		return $this->_get_language_set($block);
	}

	/*
	* Load language definitions for provided RT(block) into memory
	* Main method called by default from controllers to load language definitions per RT
	* wrapper for loading language via hook
	* Input:
	*           RT (block) for corresponding key.
	*			Load mode. silent - No error if XML file is misssing.
	* Note: If RT is not provided definition keys will be taken from main language section (ex: english.xml) if avaiblable
	* Return: Array with key/definision loaded
	*/
	public function load($block = '', $mode = '') {
		//If $filename is not provided load current language main file
		if (!$block) {
			$block = $this->current_language['filename'];
		}
		$this->registry = Registry::getInstance();
		if ($this->registry->has('extensions')) {
			$result = $this->registry->get('extensions')->hk_load($this, $block, $mode);
		} else {
			$result = $this->_load($block, $mode);
		}
		return $result;
	}

	/*
	* Returns array of all available languages in the abantecart system
	* NOTE: These include active and inactive languages
	*		If only active languages needed use getActiveLanguages()
	*/
	public function getAvailableLanguages() {
		return $this->available_languages;
	}

	/*
	* Returns array of all active (status=1) languages
	*/
	public function getActiveLanguages() {
		$active_languages = array();
		foreach ($this->available_languages as $result) {
			if ($result['status'] == 1) {
				$active_languages[] = $result;
			}
		}
		return $active_languages;
	}

	/*
	* Load all information about specified language from language table.
	* Input: Language code
	* Return: Array with language details
	*/
	public function getLanguageDetails($code) {
		if (empty($code)) return;

		foreach ($this->available_languages as $lang) {
			if ($lang['code'] == $code) {
				return $lang;
			}
		}
		return array();
	}

	/*
	* Detect language used by the client's browser
	* Return: language code for detected locale
	*/
	public function getClientBrowserLanguage() {
		$request = $this->registry->get('request');

		if (isset($request->server['HTTP_ACCEPT_LANGUAGE']) && ($request->server['HTTP_ACCEPT_LANGUAGE'])) {
			$browser_languages = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE']);

			foreach ($browser_languages as $browser_language) {
				foreach ($this->getActiveLanguages() as $key => $value) {
					$locale = explode(',', $value['locale']);

					if (preg_grep("/$browser_language/i", $locale)) {
						return $key;
					}
				}
			}
		}
		return;
	}

	/*
	* Function to decide what language to use
	* Result: Selected language set to be current and saved to session
	*/
	public function setCurrentLanguage() {
		$config = $this->registry->get('config');
		$session = $this->registry->get('session');
		$request = $this->registry->get('request');

		//build code based array
		$languages = array();
		foreach ($this->getActiveLanguages() as $lng) {
			$languages[$lng['code']] = $lng;
		}

		//language code is provided as input. Higher priority
		$request_lang = isset($request->get['language']) ? $request->get['language'] : '';
		$request_lang = isset($request->post['language']) ? $request->post['language'] : $request_lang;
		unset($request->get['language'], $request->post['language']);

		if ($request_lang && array_key_exists($request_lang, $languages)) {
			$lang_code = $request_lang;
			//Session based language
		} elseif (isset($session->data['language']) && array_key_exists($session->data['language'], $languages)) {
			$lang_code = $session->data['language'];
			//Cookie based language
		} elseif (isset($request->cookie['language']) && array_key_exists($request->cookie['language'], $languages)) {
			$lang_code = $request->cookie['language'];
			//Try autodetect the language based on the browser languages
		} elseif ($detect = $this->getClientBrowserLanguage()) {
			$lang_code = $detect;
		} else {
			$lang_code = $config->get('config_storefront_language');
		}

		// check if is code of enabled language
		if (!isset($languages[$lang_code])) {
			$lang_code = key($languages);
			$error = new AError('Error! Default language with code "' . $lang_code . '" is not available or disabled. Loading ' . $languages[$lang_code]['name'] . ' language to keep system operating. Check your settings for default language.');
			$error->toLog()->toDebug()->toMessages();
		}

		if (!isset($session->data['language']) || $session->data['language'] != $lang_code) {
			$session->data['language'] = $lang_code;
		}

		if (!isset($request->cookie['language']) || $request->cookie['language'] != $lang_code) {
			setcookie('language', $lang_code, time() + 60 * 60 * 24 * 30, '/', $request->server['HTTP_HOST']);
		}
		//set current language
		$this->current_language = $languages[$lang_code];
		$config->set('storefront_language_id', $this->current_language['language_id']);

		if ($this->is_admin) {
			// set up language for content separately (admin only)
			if (isset($request->get['content_language_code'])) {
				$session->data['content_language'] = $request->get['content_language_code'];
			} else {
				$session->data['content_language'] = !isset($session->data['content_language']) ? $lang_code : $session->data['content_language'];
			}
			if (!$session->data['content_language']) {
				$session->data['content_language'] = $config->get('admin_language');
			}
			$session->data['content_language_id'] = $languages[$session->data['content_language']]['language_id'];
		}
	}

	/*
	* Default site language Code
	*/
	public function getDefaultLanguageCode() {
		return $this->is_admin ? $this->registry->get('config')->get('admin_language') : $this->registry->get('config')->get('config_storefront_language');
	}

	/*
	* Default site language ID
	*/
	public function getDefaultLanguageID() {
		$info = $this->getDefaultLanguage();
		return $info['language_id'];
	}

	/*
	* Default site language info
	*/
	public function getDefaultLanguage() {
		//build code based array
		$languages = array();
		foreach ($this->available_languages as $lng) {
			$languages[$lng['code']] = $lng;
		}
		return $languages[$this->getDefaultLanguageCode()];
	}

	/*
	* Current site language Code
	*/
	public function getLanguageCode() {
		return $this->current_language['code'];
	}

	/*
	* Current site language ID
	*/
	public function getLanguageID() {
		return $this->current_language['language_id'];
	}

	/*
	* Current site language details Array
	*/
	public function getCurrentLanguage() {
		return $this->current_language;
	}

	/*
	* Current content language ID (admin only)
	*/
	public function getContentLanguageID() {
		$session = $this->registry->get('session');
		return $session->data['content_language_id'];
	}

	/*
	* Current content language Code (admin only)
	*/
	public function getContentLanguageCode() {
		$session = $this->registry->get('session');
		return $session->data['content_language'];
	}

	/*
	* Read XML language file and return array with difinitions
	* Input: Full file path to language XML file 
	* Return: Array with key/definition 
	*/
	public function ReadXmlFile($file) {
		$definitions = array();
		if (file_exists($file)) {
			$xml = simplexml_load_file($file);
			if (isset($xml->definition))
				foreach ($xml->definition as $item) {
					$definitions[(string)$item->key] = (string)$item->value;
				}
		}
		return $definitions;
	}

	/*
	* Set scope of available language blocks for the caller
	*/
	public function set_language_scope($block_list) {
		$this->current_languages_scope = $block_list;
	}

	/* END Main Language API methods */

	// load language
	public function _load($filename, $mode = '') {
		if (empty($filename)) return;
		//Check if we already have language loaded. Skip and return the language set
		if ($this->_is_loaded($filename)) {
			$this->current_languages_scope[] = $filename;
			$load_data = $this->_get_language_set($filename);
			if ($this->language_details['filename'] != $filename
					&& $this->entries[$this->language_details['filename']]
					&& $load_data
			) {
				$load_data = array_merge($this->entries[$this->language_details['filename']], $load_data);
			}
			$this->entries[$filename] = $load_data;
			return $load_data;
		}

		$block_name = str_replace('/', '_', $filename);
		$cache_file = 'lang.' . $this->code . '.' . (($this->is_admin) ? 'a' : 's') . '.' . $filename;

		$cache_file = str_replace('/', '_', $cache_file);
		if ($this->cache) {
			$load_data = $this->cache->get($cache_file);
		}

		if (is_null($load_data)) {
			$directory = $this->language_details['directory'];
			// nothing in cache. Start loading
			ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' ' . $filename . ' no cache, so loading');
			$_ = $this->_load_from_db($this->language_details['language_id'], $block_name, $this->is_admin);
			if (!$_) {
				// nothing in the database. This block (rt) was never accessed before for this language. Need to load definitions
				$_ = $this->_load_from_xml($filename, $directory, $mode);
				$this->_save_to_db($block_name, $_);
			} else {
				//We have something in database, look for missing or new values.
				//Do this silently in case language file is misssing, Not a big problem
				$xml_vals = $this->_load_from_xml($filename, $directory, 'silent');
				if (count($xml_vals) > count($_)) {
					//we have missing value in language XML. Probably newly added
					foreach ($xml_vals as $key => $value) {
						//missing value for $key
						if (empty($_[$key])) {
							$_[$key] = $value;
							$this->_write_missing_definition(
								array('language_id' => $this->language_details['language_id'],
									'section' => $this->is_admin,
									'block' => $block_name,
									'language_key' => $key,
									'language_value' => $value)
							);
						}
					}
				}
			}

			$load_data = $_;
			if ($this->cache) {
				$this->cache->set($cache_file, $load_data);
			}
		}
		//Merge with main array and override if not matching main language.
		if ($this->language_details['filename'] != $filename
				&& $this->entries[$this->language_details['filename']] && $load_data
		) {
			$load_data = array_merge($this->entries[$this->language_details['filename']], $load_data);
		}
		ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' ' . $filename . ' is loaded');
		$this->entries[$filename] = $load_data;
		//add filename to scope
		$this->current_languages_scope[] = $filename;
		return $this->entries[$filename];
	}

	//load all definisions for provided RT(block)
	protected function _get_language_set($block) {
		$entries = array();
		//if no rt look in all languages for last available translation
		if (empty ($block)) {

			$look_in_list = $this->current_languages_scope;
			//look in all languages and merge
			if (empty($look_in_list)) {
				$look_in_list = array_keys($this->entries);
			}

			foreach ($look_in_list as $block) {
				if (!empty($this->entries[$block]))
					$entries = array_merge($entries, $this->entries[$block]);
			}
		} else {
			$entries = $this->entries[$block];
		}
		return $entries;
	}

	//Find language ID by provided language code
	protected function _get_language_id($code) {
		foreach ($this->available_languages as $lang) {
			if ($lang['code'] == $code) {
				return $lang['language_id'];
			}
		}
	}

	//Find language code by provided language ID
	protected function _get_language_code($ID) {
		foreach ($this->available_languages as $lang) {
			if ($lang['language_id'] == $ID) {
				return $lang['code'];
			}
		}
	}

	//Check if block was laoded yet into memory
	protected function _is_loaded($block) {
		if (isset ($this->entries[$block]) && count($this->entries[$block]) > 0) {
			return true;
		}
		return false;
	}

	protected function _load_from_db($language_id, $block_name, $section) {
		if (empty ($language_id) || empty($block_name)) {
			return array();
		}
		$lang_array = array();
		$sql = "SELECT * FROM `" . DB_PREFIX . "language_definitions`
                WHERE language_id = '" . (int)$language_id . "'
                              AND section =" . (int)$section . " AND block='" . $block_name . "'";
		$language_query = $this->db->query($sql);
		if ($language_query->num_rows) {
			foreach ($language_query->rows as $language) {
				$lang_array[$language['language_key']] = $language['language_value'];
			}
		}
		return $lang_array;
	}

	protected function _save_to_db($block, $lang_defns) {
		if (!$lang_defns) return false;

		ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' ' . $block . ' saving to database');

		$sql = "INSERT INTO " . DB_PREFIX . "language_definitions ";
		$sql .= "(language_id,block,section,language_key,language_value,create_date) VALUES ";
		$values = array();
		foreach ($lang_defns as $k => $v) {
			//preventing duplication sql-error by unique index
			$check_array = array(
				'language_id' => (int)$this->language_details['language_id'],
				'block' => $this->db->escape($block),
				'section' => $this->is_admin,
				'language_key' => $this->db->escape($k),
				'language_value' => $this->db->escape($v)
			);
			if ($this->_is_definition_in_db($check_array)) {
				continue;
			}

			$values[] = "('" . (int)$this->language_details['language_id'] . "',
                          '" . $this->db->escape($block) . "',
                          '" . $this->is_admin . "',
                          '" . $this->db->escape($k) . "',
                          '" . $this->db->escape($v) . "',
                          NOW() )";
		}
		if ($values) {
			$sql = $sql . implode(', ', $values);
			$this->db->query($sql);
		}
	}

	//Detect file for default or extension language
	protected function _detect_language_xml_file($filename, $language_dir_name = 'english') {
		if (empty($filename)) {
			return;
		}
		$file_path = $this->language_path . $language_dir_name . '/' . $filename . '.xml';
		if ($this->registry->has('extensions')
				&& $result = $this->registry->get('extensions')->isExtensionLanguageFile($filename, $language_dir_name, $this->is_admin)
		) {
			if (is_file($file_path)) {
				$warning = new AWarning("Extension <b>{$result['extension']}</b> overrides language file <b>$filename</b>");
				$warning->toDebug();
			}
			$file_path = $result['file'];
		}
		return $file_path;
	}

	// Load definition values from XML
	protected function _load_from_xml($filename, $directory, $mode) {
		if (!$filename) {
			return;
		}
		$definitions = array();
		ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' ' . $filename . ' prepare loading language from XML');

		//get default extension language file
		$default_language_info = $this->getDefaultLanguage();
		if ($filename == $directory) { // for common language file (english.xml. russian.xml, etc)
			$file_name = $default_language_info['filename'];
			$mode = 'silent';
		} else {
			$file_name = $filename;
		}
		$default_file_path = $this->_detect_language_xml_file($file_name, $default_language_info['directory']);
		// if default language file path wrong - takes english
		if (!file_exists($default_file_path)) {
			$file_name = $filename == $directory ? 'english' : $file_name;
			$default_file_path = $this->_detect_language_xml_file($file_name, 'english');
		}

		// get path to actual language
		$file_path = $this->_detect_language_xml_file($filename, $this->language_details['directory']);

		if (file_exists($file_path)) {
			ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' loading XML file ' . $file_path);
			$definitions = $this->ReadXmlFile($file_path);
		} else if (file_exists($default_file_path)) {
			ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' loading XML file ' . $default_file_path);
			$definitions = $this->ReadXmlFile($default_file_path);
		} else {
			if ($mode != 'silent') {
				$error = new AError('Missing default English definition XML file for ' . $filename . ' !');
				$error->toLog()->toDebug();
			}
		}

		//skip if not required and language file does not exist for silent mode.
		if (empty($definitions) && $mode != 'silent') {
			$error = new AError('Could not load language ' . $filename . ' from file "' . $file_path . '"!');
			$error->toLog()->toDebug();
		}
		return $definitions;
	}

	// Call to get specific definition value for RT(block). 
	protected function _get_language_value($key, $filename) {
		if (empty ($filename) || empty ($key)) {
			return;
		}
		return $this->entries[$filename][$key];
	}

	// Call to get specific definition value back traced in all available RTs(blocks)
	protected function _get_last_language_value($key, $backtrace) {
		if (empty ($key)) {
			return;
		}

		//look in all blocks for last available translation based on list or all
		if (isset ($this->current_languages_scope) && sizeof($this->current_languages_scope) > 0) {
			$rev_language_blocks = array_reverse($this->current_languages_scope);
		} else {
			$rev_language_blocks = array_reverse(array_keys($this->entries));
		}
		$lang_value = '';
		foreach ($rev_language_blocks as $block) {
			$lang_value = $this->_get_language_value($key, $block);
			if (isset ($lang_value)) {
				break;
			}
		}

		// if value empty - write message based on the setting
		if (empty($lang_value) && $this->registry->get('config')->get('warn_lang_text_missing')) {
			$caller_file = $backtrace[0]['file'];
			$caller_file_line = $backtrace[0]['line'];
			$rt = $this->registry->get('request')->get['rt'];
			$this->registry->get('messages')->saveWarning('Language definition "' . $key . '" is missing for "' . $this->available_languages[$this->code]['name'] . '"', 'AbanteCart engine cannot find value of language definition with key "' . $key . '" in ' . $caller_file . ' line ' . $caller_file_line . ($rt ? ' (rt=' . $rt . ')' : '') . '.  Please add it in #admin#rt=localisation/language_definitions or run language translate process in #admin#rt=localisation/language');
		}
		return $lang_value;
	}

	protected function _write_missing_definition($data) {
		if ($this->is_admin) {
			$this->loader->model('localisation/language_definitions');
			$model = $this->registry->get('model_localisation_language_definitions');
			$model->addLanguageDefinition($data);
		} else {
			foreach ($data as $key => $val) {
				$update_data[$this->db->escape($key)] = $this->db->escape($val);
			}

			if (!$this->_is_definition_in_db($update_data)) {
				$sql = "INSERT INTO " . DB_PREFIX . "language_definitions
                                (`" . implode("`, `", array_keys($update_data)) . "`)
                                VALUES ('" . implode("', '", $update_data) . "') ";
				$this->db->query($sql);
				$this->cache->delete('lang');
				$this->cache->delete('language_definitions');
				$this->cache->delete('storefront_menu');
			}
		}
		if ($this->registry->get('config')->get('warn_lang_text_missing')) {
			$this->registry->get('messages')->saveNotice('Missing language definition "' . $data['language_key'] . '" was loaded for "' . $this->available_languages[$this->code]['name'] . '" language', 'Missing language definition with key "' . $data['language_key'] . '" for block "' . $data['block'] . '" was automatically added. Please check this at #admin#rt=localisation/language_definitions to see or change value.');
		}
	}

	protected function _is_definition_in_db($data) {
		$sql = "SELECT *
                     FROM " . DB_PREFIX . "language_definitions
                     WHERE language_id = '" . $data['language_id'] . "'
                           AND  block = '" . $data['block'] . "'
                           AND section =  '" . $data['section'] . "'
                           AND language_key =  '" . $data['language_key'] . "'
                           AND language_value =  '" . $data['language_value'] . "'";
		$exist = $this->db->query($sql);
		return ($exist->num_rows ? true : false);
	}

}