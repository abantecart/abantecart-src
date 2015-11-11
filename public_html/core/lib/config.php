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

final class AConfig {
	public $data;
	private $cnfg = array();
	/**
	 * @var Registry
	 */
	private $registry;
	public $groups = array( 'details', 'general', 'checkout', 'appearance', 'mail', 'api', 'system' );

	public function __construct($registry) {
		$this->registry = $registry;
		$this->_load_settings();
	}

	/**
	 * get data from config
	 *
	 * @param $key - data key
	 * @return mixed - requested data; null if no data wit such key
	 */
	public function get($key) {
		return (isset($this->cnfg[ $key ]) ? $this->cnfg[ $key ] : NULL);
	}

	/**
	 * add data to config.
	 *
	 * @param $key - access key
	 * @param $value - data to store in config
	 * @return void
	 */
	public function set($key, $value) {
		$this->cnfg[ $key ] = $value;
	}

	/**
	 * check if data exist in config
	 *
	 * @param $key
	 * @return bool
	 */
	public function has($key) {
		return isset($this->cnfg[ $key ]);
	}

	/**
	 * load data from config and merge with current data set
	 *
	 * @throws AException
	 * @param $filename
	 * @return void
	 */
	public function load($filename) {
		$file = DIR_CONFIG . $filename . '.php';

		if (file_exists($file)) {
			$cfg = array();

			/** @noinspection PhpIncludeInspection */
			require($file);

			$this->data = array_merge($this->data, $cfg);
		} else {
			throw new AException(AC_ERR_LOAD, 'Error: Could not load config ' . $filename . '!');
		}
	}

	private function _load_settings() {
		/**
		 * @var ACache
		 */
		$cache = $this->registry->get('cache');
		/**
		 * @var ADB
		 */
		$db = $this->registry->get('db');

		//detect URL for the store
		$url = str_replace('www.', '', $_SERVER['HTTP_HOST']) . get_url_path($_SERVER['PHP_SELF']);
		if (defined('INSTALL')) {
			$url = str_replace('install/', '', $url);
		}

		// Load default store settings
		$settings = $cache->force_get('settings', '', 0);
		if (empty($settings)) {
			// set global settings (without extensions settings)
			$sql = "SELECT se.*
					FROM " . $db->table("settings") . " se
					LEFT JOIN " . $db->table("extensions") . " e ON TRIM(se.`group`) = TRIM(e.`key`)
					WHERE se.store_id='0' AND e.extension_id IS NULL";
			$query = $db->query($sql);
			$settings = $query->rows;
			foreach ($settings as &$setting) {
				if($setting['key']=='config_url'){
					$parsed_url = parse_url($setting['value']);
					$setting['value'] = 'http://'.$parsed_url['host'].$parsed_url['path'];
				}
				if($setting['key']=='config_ssl_url'){
					$parsed_url = parse_url($setting['value']);
					$setting['value'] = 'https://'.$parsed_url['host'].$parsed_url['path'];
				}
				$this->cnfg[$setting['key']] = $setting['value'];
			}
			unset($setting); //unset temp reference
			//fix for rare issue on a database and creation of empty cache
			if(!empty($settings)){			
				$cache->force_set('settings', $settings, '', 0);
			}
		} else {
			foreach ($settings as $setting) {
				$this->cnfg[$setting['key']] = $setting['value'];
			}
		}

		// if storefront and not default store try to load setting for given URL
		/* Example: 
			Specific store config -> http://localhost/abantecart123 
			Generic config -> http://localhost
		*/
		$config_url = preg_replace("(^https?://)", "", $this->cnfg['config_url'] );
		$config_url = preg_replace("(^://)", "", $config_url );
		if (
			!(is_int(strpos($config_url,$url))) &&
			!(is_int(strpos($url, $config_url))) 			
		) { 
			// if requested url not a default store URL - do check other stores.
			$cache_name = 'settings.store.' . md5('http://' . $url);
			$store_settings = $cache->force_get($cache_name);
			if (empty($store_settings)) {
				$sql = "SELECT se.`key`, se.`value`, st.store_id
		   			  FROM " . $db->table('settings')." se
		   			  RIGHT JOIN " . $db->table('stores')." st ON se.store_id=st.store_id
		   			  LEFT JOIN " . $db->table('extensions')." e ON TRIM(se.`group`) = TRIM(e.`key`)
		   			  WHERE se.store_id = (SELECT DISTINCT store_id FROM " . $db->table('settings')."
		   			                       WHERE `group`='details'
		   			                       AND
		   			                       ( (`key` = 'config_url' AND (`value` LIKE '%" . $db->escape($url) . "'))
		   			                       XOR
		   			                       (`key` = 'config_ssl_url' AND (`value` LIKE '%" . $db->escape($url) . "')) )
		                                   LIMIT 0,1)
		   					AND st.status = 1 AND e.extension_id IS NULL";

				$query = $db->query($sql);
				$store_settings = $query->rows;
				//fix for rare issue on a database and creation of empty cache
				if(!empty($store_settings)){
					$cache->force_set($cache_name, $store_settings);
				}
			}
			
			if ($store_settings) {
				//store found by URL, load settings
				foreach ($store_settings as $row) {
					$value = $row['value'];
					$this->cnfg[$row['key']] = $value;
				}
				$this->cnfg['config_store_id'] = $store_settings[0]['store_id'];
			} else {
				$warning = new AWarning('Warning: Accessing store with unconfigured or unknown domain ( '.$url.' ).'."\n".' Check setting of your store domain URL in System Settings . Loading default store configuration for now.');
				$warning->toLog()->toMessages();
				//set config url to current domain
				$this->cnfg['config_url'] = 'http://' . REAL_HOST . get_url_path($_SERVER['PHP_SELF']);
			}

			if (!$this->cnfg['config_url']) {
				$this->cnfg['config_url'] = 'http://' . REAL_HOST . get_url_path($_SERVER['PHP_SELF']);
			}

		}

		//still no store? load default store or session based
		if (is_null($this->cnfg['config_store_id'])) {
			$this->cnfg['config_store_id'] = 0;			
			if (IS_ADMIN) {
				//if admin and specific store selected 
				$session = $this->registry->get('session');
				$store_id = $this->registry->get('request')->get['store_id'];
				if (has_value($store_id)) {
					$this->cnfg['current_store_id'] = $this->cnfg['config_store_id'] = (int)$store_id;
				} else if(has_value($session->data['current_store_id'])) {
					$this->cnfg['config_store_id'] = $session->data['current_store_id'];	
				}
			}
			$this->_reload_settings($this->cnfg['config_store_id']);
		}else{
			$this->cnfg['current_store_id'] = $this->cnfg['config_store_id'];
		}
		
		//get template for storefront
		$tmpl_id = $this->cnfg['config_storefront_template'];
		
		// load extension settings
		$cache_suffix = IS_ADMIN ? 'admin' : $this->cnfg['config_store_id'];
		$settings = $cache->force_get('settings.extension.' . $cache_suffix);
		if (empty($settings)) {
			// all extensions settings of store
			$sql = "SELECT se.*, e.type as extension_type, e.key as extension_txt_id
					FROM " . $db->table('settings')." se
					LEFT JOIN " . $db->table('extensions')." e ON (TRIM(se.`group`) = TRIM(e.`key`))
					WHERE se.store_id='" . (int)$this->cnfg['config_store_id'] . "' AND e.extension_id IS NOT NULL
					ORDER BY se.store_id ASC, se.group ASC";

			$query = $db->query($sql);
			foreach( $query->rows as $row ){
				//skip settings for non-active template except status (needed for extensions list in admin)
				if( $row['extension_type'] == 'template' && $tmpl_id!=$row['group'] && $row['key'] != $row['extension_txt_id'].'_status' ){ continue;}
				$settings[] = $row;
			}
			//fix for rare issue on a database and creation of empty cache
			if(!empty($settings)){
				$cache->force_set('settings.extension.' . $cache_suffix, $settings);
			}
		}

		//add encryption key to settings, overwise use from database (backwards compatability) 
		if (defined('ENCRYPTION_KEY')) {
			$setting['encryption_key'] = ENCRYPTION_KEY;
		}

		foreach ($settings as $setting) {
			$this->cnfg[ $setting['key']] = $setting['value'];
		}
	}

	private function _reload_settings( $store_id = 0 ) {
		//we don't use cache here cause domain may be different and we cannot change cache from control panel
		$db = $this->registry->get('db');
		$sql = "SELECT se.`key`, se.`value`, st.store_id
					  FROM " . $db->table('settings')." se
					  RIGHT JOIN " . $db->table('stores')." st ON se.store_id=st.store_id
					  LEFT JOIN " . $db->table('extensions')." e ON TRIM(se.`group`) = TRIM(e.`key`)
					  WHERE se.store_id = $store_id AND st.status = 1 AND e.extension_id IS NULL";

		$query = $db->query($sql);
		$store_settings = $query->rows;
		foreach ($store_settings as $row) {
			if ($row['key'] != 'config_url') {
				$this->cnfg[ $row['key']] = $row['value'];
			}
		}
	}
}
