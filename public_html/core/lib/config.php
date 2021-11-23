<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

final class AConfig
{
    public $data;
    private $cnfg = [];
    /**
     * @var Registry
     */
    private $registry;
    public $groups = ['details', 'general', 'checkout', 'appearance', 'mail', 'im', 'api', 'system'];

    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->_load_settings();
    }

    /**
     * get data from config
     *
     * @param $key - data key
     *
     * @return mixed - requested data; null if no data wit such key
     */
    public function get($key)
    {
        return ($this->cnfg[$key] ?? null);
    }

    /**
     * add data to config.
     *
     * @param $key - access key
     * @param $value - data to store in config
     *
     * @return void
     */
    public function set($key, $value)
    {
        $this->cnfg[$key] = $value;
    }

    /**
     * check if data exist in config
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->cnfg[$key]);
    }

    /**
     * load data from config and merge with current data set
     *
     * @param $filename
     *
     * @return void
     * @throws AException
     *
     */
    public function load($filename)
    {
        $file = DIR_CONFIG.$filename.'.php';

        if (file_exists($file)) {
            $cfg = [];

            /** @noinspection PhpIncludeInspection */
            require($file);

            $this->data = array_merge($this->data, $cfg);
        } else {
            throw new AException(AC_ERR_LOAD, 'Error: Could not load config '.$filename.'!');
        }
    }

    private function _load_settings()
    {
        /**
         * @var ACache $cache
         */
        $cache = $this->registry->get('cache');
        /**
         * @var ADB
         */
        $db = $this->registry->get('db');

        //detect URL for the store
        $url = str_replace('www.', '', $_SERVER['HTTP_HOST']).get_url_path($_SERVER['REQUEST_URI']);
        if (defined('INSTALL')) {
            $url = str_replace('install/', '', $url);
        }

        //enable cache storage based on configuration
        $cache->enableCache();
        //set configured driver.
        $cache_driver = 'file';
        if (defined('CACHE_DRIVER')) {
            $cache_driver = CACHE_DRIVER;
        }
        if (!$cache->setCacheStorageDriver($cache_driver)) {
            $error = new AError ('Cache storage driver '.$cache_driver.' can not be loaded!');
            $error->toLog()->toDebug()->toMessages();
        }

        // Load default store settings
        $settings = $cache->pull('settings');
        if (empty($settings)) {
            // set global settings (without extensions settings)
            $sql = "SELECT se.*
                    FROM ".$db->table("settings")." se
                    WHERE se.store_id = '0'
                        AND se.`group` NOT IN (SELECT `key` FROM ".$db->table("extensions").")";
            $query = $db->query($sql);
            $settings = $query->rows;
            foreach ($settings as &$setting) {
                if ($setting['key'] == 'config_url') {
                    $parsed_url = parse_url($setting['value']);
                    if (empty($parsed_url['scheme'])) {
                        $parsed_url['scheme'] = "http";
                    }
                    $setting['value'] = $parsed_url['scheme'].'://'.$parsed_url['host'].$parsed_url['path'];
                }
                if ($setting['key'] == 'config_ssl_url' && $setting['value']) {
                    $parsed_url = parse_url($setting['value']);
                    if (empty($parsed_url['scheme'])) {
                        $parsed_url['scheme'] = "https";
                    }
                    $setting['value'] = $parsed_url['scheme'].'://'.$parsed_url['host'].$parsed_url['path'];
                }
                $this->cnfg[$setting['key']] = $setting['value'];
            }
            unset($setting); //unset temp reference

            //fix for rare issue on a database and creation of empty cache
            if (!empty($settings) && $this->cnfg['config_cache_enable']) {
                $cache->push('settings', $settings);
            }
        } else {
            foreach ($settings as $setting) {
                $this->cnfg[$setting['key']] = $setting['value'];
            }
        }
        //set current store id as default 0 for now. It will be reset if other store detected below
        $this->cnfg['config_store_id'] = 0;

        // if storefront and not default store try to load setting for given URL
        /* Example:
            Specific store config -> http://localhost/abantecart123
            Generic config -> http://localhost
        */
        $config_url = preg_replace("(^https?://)", "", $this->cnfg['config_url']);
        $config_url = preg_replace("(^://)", "", $config_url);

        $domain = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        if ($url != $config_url) {
            // if requested url not a default store URL - do check other stores.
                $foundStoreId = null;
                $subPath = '/'.trim($_SERVER['REQUEST_URI'], "/");
                $subPath = $subPath ? explode("/", $subPath) : [''];

                //get all store IDs by domain name
                $sql = "SELECT DISTINCT `store_id`, REPLACE(REPLACE(`value`,'http://',''),'https://','') as uri 
                        FROM ".$db->table('settings')."
                        WHERE `group`='details'
                            AND
                                ( (`key` = 'config_url' AND (`value` LIKE '%".$db->escape($domain)."%'))
                            XOR (`key` = 'config_ssl_url' AND (`value` LIKE '%".$db->escape($domain)."%')) ) 
                        ORDER BY LENGTH(`value`) DESC";
                $result = $db->query($sql);

                // seek by full path first (index page of store)
                foreach ($result->rows as $row) {
                    $sp = '';
                    foreach($subPath as $sPath) {
                        $sp .= $sPath.'/';
                        $testUri = $domain.'/'.$sp;
                        $testUri = str_replace('//', '/', $testUri);
                        if (str_replace('www.', '', $row['uri']) == $testUri) {
                            $foundStoreId = (int) $row['store_id'];
                            break 2;
                        }
                    }
                }
                //if path not found in list - seek by path parts (index page + seo-keyword)
                if (is_null($foundStoreId)) {
                    foreach ($result->rows as $row) {
                        $tmp = $subPath;
                        while (count($tmp) > 0) {
                            $testUri = $domain.'/'.implode("/", $tmp).'/';
                            if (strlen($row['uri']) > strlen($testUri)) {
                                //do not allow dig too deep into uri subfolders
                                break 1;
                            }
                            if (is_int(strpos($row['uri'], $testUri))) {
                                $foundStoreId = (int) $row['store_id'];
                                break 2;
                            }
                            array_pop($tmp);
                        }
                    }
                }

                //get all settings for specific store
                $sql = "SELECT se.`key`, se.`value`, st.store_id
                      FROM ".$db->table('settings')." se
                      RIGHT JOIN ".$db->table('stores')." st ON se.store_id = st.store_id
                      WHERE se.store_id = ".(int) $foundStoreId."
                            AND st.status = 1
                            AND TRIM(se.`group`) NOT IN
                                                    (SELECT TRIM(`key`) as `key`
                                                    FROM ".$db->table("extensions").")";
                $query = $db->query($sql);
                $store_settings = [];
                if ($query->num_rows) {
                    $store_settings = array_column($query->rows, 'value', 'key');
                    $store_settings['store_id'] = $foundStoreId;
                }
                if ($store_settings) {
                    $this->prepareSettings($domain, $store_settings);
                }
            if ($store_settings) {
                //store found by URL, load settings
                $this->cnfg = array_merge($this->cnfg, $store_settings);
                $this->cnfg['config_store_id'] = (int) $store_settings['store_id'];
            } else {
                //write to log when system check enabled
                if (php_sapi_name() != 'cli'
                    && ($this->cnfg['config_system_check'] == 0
                        || ($this->cnfg['config_system_check'] == 1 && IS_ADMIN === true)
                        || ($this->cnfg['config_system_check'] == 2 && IS_ADMIN !== true))
                ) {
                    $warning = new AWarning(
                        'Warning: Accessing store with non-configured or unknown domain ( '.$url.' ).'."\n"
                        .' Check setting of your store domain URL in System Settings.'
                        .' Loading default store configuration for now.'
                    );
                    $warning->toLog();
                }
                //set config url to current domain
                $this->cnfg['config_url'] = 'http://'.REAL_HOST.get_url_path($_SERVER['PHP_SELF']);
            }

            if (!$this->cnfg['config_url']) {
                $this->cnfg['config_url'] = 'http://'.REAL_HOST.get_url_path($_SERVER['PHP_SELF']);
            }
        }

        //load specific store if admin and set current_store_id for admin operation on store
        if (IS_ADMIN) {
            //Check if admin has specific store in session or selected
            $session = $this->registry->get('session');
            $store_id = $this->registry->get('request')->get['store_id'];
            if (isset($store_id)) {
                $this->cnfg['current_store_id'] = (int) $store_id;
            }
            //reload store settings if not what is loaded now
//???needed for settings page
            if (isset($this->cnfg['current_store_id'])) {
                $this->reloadSettings($this->cnfg['current_store_id']);
                $this->cnfg['config_store_id'] = $this->cnfg['current_store_id'];
            }
        }

        //get template for storefront
        $tmpl_id = $this->cnfg['config_storefront_template'];

        //disable cache when it disabled in settings
        if (!$this->cnfg['config_cache_enable']) {
            $cache->disableCache();
        }

        // load extension settings
        $cache_suffix = IS_ADMIN ? 'admin' : $this->cnfg['config_store_id'];
        $settings = $cache->pull('settings.extension.'.$cache_suffix);
        if (empty($settings)) {
            // all extensions settings of store
            $sql = "SELECT se.*, e.type as extension_type, e.key as extension_txt_id
                    FROM ".$db->table('settings')." se
                    LEFT JOIN ".$db->table('extensions')." e ON se.`group` = e.`key`
                    WHERE se.store_id='".(int) $this->cnfg['config_store_id']."' AND e.extension_id IS NOT NULL
                    ORDER BY se.store_id ASC, se.group ASC";
            $query = $db->query($sql);
            foreach ($query->rows as $row) {
                //skip settings for non-active template except status (needed for extensions list in admin)
                if ($row['extension_type'] == 'template' && $tmpl_id != $row['group']
                    && $row['key'] != $row['extension_txt_id'].'_status'
                ) {
                    continue;
                }
                $settings[] = $row;
            }
            //fix for rare issue on a database and creation of empty cache
            if (!empty($settings)) {
                $cache->push('settings.extension.'.$cache_suffix, $settings);
            }
        }

        //add encryption key to settings, otherwise use from database (backwards compatibility)
        if (defined('ENCRYPTION_KEY')) {
            $setting['encryption_key'] = ENCRYPTION_KEY;
        }

        foreach ($settings as $setting) {
            $this->cnfg[$setting['key']] = $setting['value'];
        }
    }

    /**
     * function prepares config for work with sub-path based multistore
     *
     * @param $domain
     * @param $store_settings
     */
    private function prepareSettings($domain, &$store_settings)
    {
        //replace base url with url without fake(store-alias-path) subfolder
        $autoUri = rtrim($domain.dirname($_SERVER['SCRIPT_NAME']), '/');
        $autoUri .= $autoUri ? '/' : '';
        foreach (['config_url', 'config_ssl_url'] as $confUrl) {
            $get =& $this->registry->get('request')->get;
            if ($store_settings[$confUrl]) {
                $parsed = parse_url($store_settings[$confUrl]);
                $protocol = $parsed['scheme'];
                if (str_replace('www.', '', $store_settings[$confUrl]) != $protocol.'://'.$autoUri) {
                    //remove fake(store-alias-path) prefix from seo-key which we got from .htaccess (_route_)
                    $diff =
                        substr(str_replace('www.', '', $store_settings[$confUrl]), strlen($protocol.'://'.$autoUri));
                    $store_settings['seo_prefix'] = $diff;
                    if (isset($get['_route_'])) {
                        //this covers both cases (when store url with slash at the end of url and without)
                        $get['_route_'] = str_replace(INDEX_FILE, '', $get['_route_']);
                        $get['_route_'] = str_replace($diff, '', rtrim($get['_route_'], '/').'/');
                        if (!$get['_route_']) {
                            unset($get['_route_']);
                        }
                    }
                    $store_settings[$confUrl] = $protocol.'://'.$autoUri;
                }
            }
        }
    }

    public function reloadSettings($store_id = 0)
    {
        //we don't use cache here cause domain may be different and we cannot change cache from control panel
        $db = $this->registry->get('db');
        $sql = "SELECT se.`key`, se.`value`, st.store_id
                    FROM ".$db->table('settings')." se
                    RIGHT JOIN ".$db->table('stores')." st 
                            ON se.store_id = st.store_id
                    WHERE se.store_id = ".(int) $store_id." AND st.status = 1
                    AND se.`group` NOT IN (SELECT `key` FROM ".$db->table("extensions").");";
        $query = $db->query($sql);
        $store_settings = $query->rows;
        foreach ($store_settings as $row) {
            if ($row['key'] != 'config_url') {
                $this->cnfg[$row['key']] = $row['value'];
            }
        }
    }
}
