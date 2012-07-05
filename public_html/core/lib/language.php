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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

final class ALanguage {
    public $entries = array();
    public $language_details;
    public $current_languages_scope = array(); //This is and array of available languages for calling scope
    public $is_admin = 0;
       
    private $code = '';
    private $db;
    private $cache;
    private $registry;
    private $loader;
    private $language_path;

	private $available_languages = array(); //Aray of awailable languges configured in abantecart 
	private $current_language = array(); //current used main language array data

    public function __construct($registry, $code = '', $section = '') {
        $this->registry = $registry;
        if ($section == ''){
        	$this->is_admin = ((!defined('IS_ADMIN') || !IS_ADMIN) ? 0 : 1);
        } else {
        	$this->is_admin = $section;
        }
        if ( $this->is_admin ) {
        	$this->language_path = DIR_ROOT . '/admin/language/';
        } else {
        	$this->language_path = DIR_ROOT . '/storefront/language/';        
        }
                
		//Load available languages;
	    $this->loader = $registry->get('load');
	    $result = $this->loader->model('localisation/language','silent');
        if($result!==FALSE){
            $model = $registry->get('model_localisation_language');
            $this->available_languages = $model->getLanguages();
        }

		//If No language code, need Language Detection, set site language to use and set content language separately
		if (!$code){
			$this->setCurrentLanguage();   
		}
        
        $this->db = $registry->get('db');
        $this->cache = $registry->get('cache');
        $this->code = $code ? $code : $registry->get('session')->data['language'];
        $this->language_details = $this->get_language_details();

        $this->entries = array();
    }

	// Set the scope of available languages for the caller
	public function set_language_scope($look_in_list) {
		$this->current_languages_scope = $look_in_list;
	} 

    // Get single language entry
    public function get($key, $filename = '') {
    	if ( empty($key) ) { return; }
    	//if no specific area specified return main language
    	if ( !empty($filename) ) { 
    		return $this->_get_language_value( $key, $filename );
    	} else {
        	$backtrace = debug_backtrace();
    		return $this->_find_last_language_value( $key , $backtrace );
    	}    
    }

    public function getASet($filename = '') {
    	//if no specific area specified return main language set
    	if ( empty($filename) && empty($this->current_languages_scope) ) {
    		    $filename = $this->language_details['filename'];
    	}
        return $this->_get_language_set($filename);
    }


	public function _get_language_set ( $lang_set ) {
    	$entries = array();
		//if no rt look in all languages for last available translation
		if ( empty ($lang_set)) {

            $look_in_list = $this->current_languages_scope;
            //look in all languages and merge
            if ( empty($look_in_list) ) {
                $look_in_list = array_keys($this->entries);
            }

			foreach ($look_in_list as $lang_set) {
                if ( !empty($this->entries[$lang_set]) )
				    $entries = array_merge($entries, $this->entries[$lang_set]);
			}
		}
		else {
			$entries = $this->entries[$lang_set];
		}
		return $entries;
	}

    //Returns array of all available languages in the abantecart system
    public function getAvailableLanguages () {
		return $this->available_languages;
	}

    //Returns array of active (status=1) languages in the abantecart system
    public function getActiveLanguages() {
		$active_languages = array();
		foreach ($this->available_languages as $result) {
			if ($result['status'] == 1) {
				//TODO. Check if this is an extension and if it is enabled. Skip if extension diabled.
				$active_languages[] =  array(
					'name'  => $result['name'],
					'code'  => $result['code'],
					'image' => $result['image']
				);
			}
		}
		return $active_languages;
	}

    //Detect language used by the client's browser
    public function getClientBrowserLanguage () {
		$detect = '';
    	$request = $this->registry->get('request');

		if (isset($request->server['HTTP_ACCEPT_LANGUAGE']) && ($request->server['HTTP_ACCEPT_LANGUAGE'])) {
			$browser_languages = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE']);

			foreach ($browser_languages as $browser_language) {
				foreach ($this->available_languages as $key => $value) {
					$locale = explode(',', $value['locale']);

					if (in_array($browser_language, $locale)) {
						$detect = $key;
					}
				}
			}
		}
    	return $detect;
    }

    //function to decide what language to use
    public function setCurrentLanguage(){
    	$config = $this->registry->get('config');
    	$session = $this->registry->get('session');
    	$request = $this->registry->get('request');

    	//build code based array
		$languages = array();
		foreach ( $this->available_languages as $lng ) {
			$languages[ $lng['code'] ] = $lng;
		}

		//language code is porvided as input. Higher priority
		if (isset($request->get['language']) && array_key_exists($request->get['language'], $languages)) {
			$lang_code = $request->get['language'];
		//Session based language
		} elseif (isset($session->data['language']) && array_key_exists($session->data['language'], $languages)) {
			$lang_code = $session->data['language'];
		//Cookie based language
		} elseif (isset($request->cookie['language']) && array_key_exists($request->cookie['language'], $languages)) {
			$lang_code = $request->cookie['language'];
		//Try autodetect the language based on the browser languages
		} elseif ($detect = $his->getClientBrowserLanguage()) {
			$lang_code = $detect;
		} else {
			$lang_code = $config->get('config_storefront_language');
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
		$config->set('config_storefront_language', $this->current_language['code']);

		// set up language for content separately
		if(isset($request->get['content_language_code'])){
			$session->data['content_language'] = $request->get['content_language_code'];
		} else {
			$session->data['content_language'] = !isset($session->data['content_language']) ? $lang_code : $session->data['content_language'];
		}
		if(!$session->data['content_language']){
			$session->data['content_language'] = $config->get('admin_language');
		}
		$session->data['content_language_id'] = $languages[$session->data['content_language']]['language_id'];
    }

	//Current site language ID
    public function getLanguageID(){
    	return $this->current_language['language_id'];
	}

	//Current site language Code
    public function getLanguageCode(){
    	return $this->current_language['code'];
	}

 	//Current site language Data Array
    public function getCurrentLanguage(){
    	return $this->current_language;
	}

	//Current content language ID 
    public function getContentLanguageID(){
    	$session = $this->registry->get('session');
    	return $session->data['content_language_id'];
	}

	//Current content language Code
    public function getContentLanguageCode(){
    	$session = $this->registry->get('session');
    	return $session->data['content_language'];
	}

    //Function to populate all tables that have language linked data with new language data. Copy from default if source language is not provided
    public function fillMissingLanguageEntries( $language_id, $source_language = '' ) {
    	if (!$this->is_admin) {
    	    return;
    	}
		if (empty($language_id)) {
		    return;
		}
		$ret_str = '';
		$tables = $this->_get_language_based_tables();
        foreach ($tables as $table_name) {
        	$pkeys = array();
			//Set special case table and
			if ( strstr($table_name['table_name'], 'language_definitions') ) {
				array_push($pkeys, 'language_definition_id', 'language_id', 'section', 'block', 'language_key');
				#PR  ????? Temporarly skip this table in below line.
				#Problem: Not all XML section are loaded initialy and clone will not work correctly for language_definitions
				#Solution: TODO: Load all XML section on cart and extension installation
				continue;
			} else if ( strstr($table_name['table_name'], 'orders') || strstr($table_name['table_name'], 'languages') ) {
				continue;
			} else {
	        	#get primary keys
	        	$pkeys = $this->_table_get_keys( $table_name['table_name'] );
			}

		    $ret_str .= $this->_clone_language_rows($table_name['table_name'], $pkeys, $language_id, $source_language);
        }
    	return $ret_str;
    }

    //Clone record to all availabe langauges from specified language ID.
    public function cloneToAllLanguages( $table, $source_language) {
    	if (!$this->is_admin) {
    			return;
    	}
    	$ret_str = '';
    	// for each langauge Call _clone_language_rows
		foreach ( $this->available_languages as $lng ) {
			$language_id = $lng['id'];
			if ($language_id == $source_language) {
				continue;
			}
			$pkeys = array();
			$pkeys = $this->_table_get_keys( $table );

			$ret_str .= $this->_clone_language_rows($table, $pkeys, $language_id, $source_language);
		}
		return $ret_str;
    }

	//Clone language_definition text that is present in source language and missing in destination
	public function cloneMissingDefinitions( $block, $language_id, $source_language) {
        $tables = $this->_get_language_based_tables();
        foreach ($tables as $table_name) {
        	$pkeys = array();
			//Set special case table and
			if ( strstr($table_name['table_name'], 'language_definitions') ) {
				ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' '. $block .' conning missing text from ' . $source_language);
				array_push($pkeys, 'language_definition_id', 'language_id', 'section', 'block', 'language_key');
				$section = $this->is_admin ? 1 : 0;
				$specific_sql = " AND block = '" . $block . "' AND section = '" . $section . "'";
				return $this->_clone_language_rows($table_name['table_name'], $pkeys, $language_id, $source_language, $specific_sql);
			}
    	}
    }

	// wrapper for loading language via hook
	public function load($filename = '',$mode = '') {
		//If $filename is not provided load current language main file
		if (!$filename) {
			$filename = $this->current_language['filename'];
		}
  		$this->registry = Registry::getInstance();
        if ( $this->registry->has('extensions') ) {
	        $result = $this->registry->get('extensions')->hk_load($this, $filename, $mode);
        } else {
        	$result = $this->_load($filename, $mode);
        }
		return $result;
    }

	// load language
    public function _load($filename, $mode = '') {
    	//Check if we already have language loaded. Skip and return the language set    	        	 
    	if ( isset ($this->entries[$filename]) && count($this->entries[$filename]) > 0) {
            $this->current_languages_scope[] = $filename;
		    $load_data = $this->getASet($filename);
		    if ( $this->language_details['filename'] != $filename
        	     && $this->entries[$this->language_details['filename']]
		         && $load_data ) {
                $load_data = array_merge($this->entries[$this->language_details['filename']], $load_data);
    	    }
		    $this->entries[$filename] = $load_data;
		    return $load_data;
    	}
    
        $block_name = str_replace('/', '_', $filename);
        $cache_file = 'lang.' . $this->code . '.' . (($this->is_admin) ? 'a' : 's') . '.' . $filename;

        $cache_file = str_replace('/', '_', $cache_file);
	    if($this->cache){
            $load_data = $this->cache->get($cache_file);
	    }

        if ( is_null($load_data) ) {
        	ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' '. $filename .' no cache, so loading');
            $_ = array();
			$_ = $this->_load_from_db($this->language_details['language_id'], $block_name, $this->is_admin);
            if ( !$_ ) {
                $_ = $this->_load_from_xml($filename, $mode);
                $this->_save_to_db($block_name, $_);
        		//Populate all missing text definitions from english. English should be present all the time
       		 	$this->cloneMissingDefinitions( $block_name, $this->language_details['language_id'], 1);
       		 	//reload again with final result
                $_ = $this->_load_from_db($this->language_details['language_id'], $block_name, $this->is_admin);
            }
            $load_data = $_;
			if($this->cache){
                $this->cache->set($cache_file, $load_data);
			}
        }
        //Merge with main array and override if not matching main language.
        if ( $this->language_details['filename'] != $filename
        	 && $this->entries[$this->language_details['filename']] && $load_data ) {
            $load_data = array_merge($this->entries[$this->language_details['filename']], $load_data);
        }
        ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' '. $filename .' is loaded');
        $this->entries[$filename] = $load_data;
        //add filename to scope
        $this->current_languages_scope[] = $filename;
        return $this->entries[$filename];
    }

    private function get_language_details() {
        $language_detail = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "languages where code='" . $this->code . "'");

        foreach ($query->rows as $result) {
            $language_detail = array(
                'language_id' => $result['language_id'],
                'name' => $result['name'],
                'code' => $result['code'],
                'locale' => $result['locale'],
                'directory' => $result['directory'],
                'filename' => $result['filename']
            );
        }
        return $language_detail;
    }

    private function _load_from_db ($language_id, $block_name, $section) {
    	if ( empty ($language_id) || empty($block_name) ) {
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

    private function _save_to_db($block, $lang_defns) {
	    if(!$lang_defns) return false;

		ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' '. $block .' saving to database');

	    $sql = "INSERT INTO " . DB_PREFIX . "language_definitions ";
	    $sql .= "(language_id,block,section,language_key,language_value,create_date) VALUES ";
	    $values=array();
        foreach ($lang_defns as $k => $v) {
            $values[] = "('" . (int)$this->language_details['language_id'] . "',
                          '" . $block . "',
                          '" . $this->is_admin . "',
                          '" . mysql_real_escape_string($k) . "',
                          \"" . mysql_real_escape_string($v) . "\",
                          NOW() )";
        }
	    $sql = $sql . implode(', ',$values);
	    $this->db->query($sql);
    }

	//Convert block name from database to XML file name
	public function convert_block_to_file ( $block ) {
		$filename = $block;
		$parts = explode('_', $filename);
		$test_array = array();
		array_push ($test_array, $parts[0]);
		for ($i = 1; $i < count($parts); $i++) {
			$total_arr = count($test_array);
			for ($j = 0; $j < $total_arr; $j++) {
					array_push ($test_array, $test_array[$j] . '_' . $parts[$i]);
					$test_array[$j] .= '/' . $parts[$i];
			}
		}
		//check what path is valid
		foreach ($test_array as $filename ) {
			$file_path = $this->_detect_language_xml_file($filename);
			if ( file_exists($file_path) ) {
				return $filename;
			}
		}
		return;
	}

	//Detect file for default or extension language
	private function _detect_language_xml_file($filename, $language_dir_name = 'english' ){
		if( empty($filename) ){
			return;
		}
        $file_path = $this->language_path . $language_dir_name. '/' . $filename . '.xml';
        if ( $this->registry->has('extensions')
        	 && $result = $this->registry->get('extensions')->isExtensionLanguageFile($filename, $language_dir_name, $this->is_admin) ) {
            if ( is_file($file_path) ) {
                $warning = new AWarning("Extension <b>{$result['extension']}</b> overrides language file <b>$filename</b>" );
                $warning->toDebug();
            }
            $file_path = $result['file'];
        }
        return $file_path;
	}

    private function _load_from_xml($filename, $mode){
        $definitions = array();
		ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' '. $filename .' prepare loading language from XML');

		//get default extension language file
		$default_file_path = $this->_detect_language_xml_file( $filename );

        // get path to actual language
		$file_path = $this->_detect_language_xml_file( $filename, $this->language_details['directory'] );

        if ( file_exists($file_path) ) {
            ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' loading XML file '. $file_path);
        	$definitions = $this->_read_xml_file( $file_path );
        } else if ( file_exists($default_file_path) ) {
            ADebug::checkpoint('ALanguage ' . $this->language_details['name'] . ' loading XML file '. $default_file_path);
        	$definitions = $this->_read_xml_file( $default_file_path );
        }

		//skip if not required and language file does not exist for silent mode.
        if (empty($definitions) && $mode != 'silent' ) {
	        $error = new AError('Could not load language ' . $filename . ' from file "'.$file.'"!');
            $error->toLog()->toDebug();
        }
        return $definitions;
    }

	private function _read_xml_file ( $file ) {
		$definitions = array();
        if (file_exists($file) ) {
            $xml = simplexml_load_file($file);
	        if ( isset($xml->definition) )
            foreach( $xml->definition as $item) {
	            $definitions[(string)$item->key] = (string)$item->value;
            }
        }
		return $definitions;
	}

    private function _find_last_language_value ( $key, $backtrace ) {
    	if ( empty ($key) ) {
    		return;
		}
    	//look in all languages for last available translation based on list or all
    	if ( isset ($this->current_languages_scope) && sizeof($this->current_languages_scope) > 0  ) {
    		$rev_languages = array_reverse($this->current_languages_scope);
    	} else {
    		$rev_languages = array_reverse( array_keys($this->entries) );
    	}
        $lang_value = '';
		foreach ($rev_languages as $lang_set) {
			$lang_value = $this->_get_language_value($key, $lang_set);
		    if ( isset ( $lang_value ) ) {
		    	break;
		    }
		}

	    // if definition not found - get it from english
	    if(empty($lang_value)){
	       if($this->code!='en'){
				//first of all need to check a new definition in xml-file(for ex after cart upgrade)
				$new_language = new ALanguage($this->registry,$this->code);
				foreach($rev_languages as $block){
					$definitions = $new_language->_load_from_xml($block,'silent');
					if(!empty($definitions[$key])){
						$lang_value = $definitions[$key];
						$this->_write_missing_definition(
														array('language_id'=> $this->available_languages[$this->code]['language_id'],
															  'section' => $this->is_admin,
															  'block'=> str_replace('/','_',$block),
															  'language_key'=>$key,
															  'language_value'=>$lang_value));
					}
				}
				// if still empty -  check this key in english
				if(empty($lang_value) && $this->code!='en' && isset($this->available_languages['en']) ){
					$new_language = new ALanguage($this->registry,'en');
					foreach($this->current_languages_scope as $block){
						$definitions = $new_language->_load_from_xml($block,'silent');
						if(!empty($definitions[$key])){
							$lang_value = $definitions[$key];
							$this->_write_missing_definition(
															array('language_id'=> $this->available_languages[$this->code]['language_id'],
																  'section' => $this->is_admin,
																  'block'=> str_replace('/','_',$block),
																  'language_key'=>$key,
																  'language_value'=>$lang_value));
						}
					}
				}
	       }elseif($this->code=='en'){
		       $lng = $this->is_admin ? $this->registry->get('config')->get('admin_language') : $this->registry->get('config')->get('config_storefront_language');
		       if( $lng=='en'){
		       foreach($this->current_languages_scope as $block){
						$definitions = $this->_load_from_xml($block,'silent');
						if(!empty($definitions[$key])){
							$lang_value = $definitions[$key];
							$this->_write_missing_definition(
															array('language_id'=> $this->available_languages[$this->code]['language_id'],
																  'section' => $this->is_admin,
																  'block'=> str_replace('/','_',$block),
																  'language_key'=>$key,
																  'language_value'=>$lang_value));
						}
					}
		       }
	       }
	    }
	    // if value empty anyway - write message
	    if(empty($lang_value)){
        	$caller_file = $backtrace[0]['file'];
        	$caller_file_line = $backtrace[0]['line'];
            $rt = $this->registry->get('request')->get['rt'];
		    $this->registry->get('messages')->saveWarning('Language definition "'. $key.'" is absent for "'.$this->available_languages[$this->code]['name'].'"', 'AbanteCart engine cannot find value of language definition with key "'.$key.'" in ' . $caller_file . ' line ' . $caller_file_line. ($rt ? ' (rt='.$rt.')' : '').'.  Please add it manually in #admin#rt=localisation/language_definitions of control panel.' );
	    }
		return $lang_value;
    }
	private function _write_missing_definition($data){
		if($this->is_admin){
			$this->loader->model('localisation/language_definitions');
			$model = $this->registry->get('model_localisation_language_definitions');
			$model->addLanguageDefinition( $data);
		}else{
			foreach ( $data as $key=>$val ) {
				$update_data[$this->db->escape($key)] = "'" . $this->db->escape($val) . "'";
			}

			$sql = "INSERT INTO " . DB_PREFIX . "language_definitions
							(".implode(', ',array_keys($update_data)).")
							VALUES (".implode(', ', $update_data).") ";
			$this->db->query($sql);
			$this->cache->delete('lang');
			$this->cache->delete('language_definitions');
			$this->cache->delete('storefront_menu');
		}
		$this->registry->get('messages')->saveNotice('Language definition "'. $data['language_key'].'" was added into database for "'.$this->available_languages[$this->code]['name'].'" language',
		                                             'Language definition with key "'.$data['language_key'].'" and block "'.$data['block'].'" was automatically added  into database as missed.
													 Please check this at #admin#rt=localisation/language_definitions of control panel.' );
	}

    private function _get_language_value ( $key,  $filename ) {
    	if (empty ($filename) || empty ($key)) { return; }
    	return $this->entries[$filename][$key];
    }


    //Duplicate row from default langauge to new
    private function _clone_language_rows( $table, $pkeys, $new_language, $from_language = '', $specific_sql = '') {
    	//Allow heavy load work only from admin or specific block select
    	if (!$this->is_admin && empty($specific_sql) ) {
    			return;
    	}
    	if (empty($from_language)) {
	    	#for now assume it default langauge is id=1;
    		$from_language = 1;
    	}

    	if ( empty($table) || empty ($pkeys) || empty($new_language) ) {
    		return;
    	}
    	// Locate autoincrement column
    	$autoincrenent_sql = "SHOW COLUMNS FROM ".$table." where Extra = 'auto_increment'";
		$autoincrement = $this->db->query($autoincrenent_sql);
		$auto_column = $autoincrement->row['Field'];
    	//Build a keys string for select
    	$keys_str = '';
    	$tcount = 0;
    	foreach ( $pkeys as $key ) {
    		if ( !empty($keys_str) ) { $keys_str .= ","; }
    		$keys_str .= "$key ";
    	}
    	$sql = "SELECT " . $keys_str . " FROM " . $table . " WHERE language_id = " . $from_language . $specific_sql;
    	$tables_query = $this->db->query($sql);
    	if ($tables_query->num_rows) {
    		$langs = array();
    		foreach ( $this->available_languages as $lang )
			{
				$langs[$lang['language_id']] = $lang['filename'];
			}

    		foreach ($tables_query->rows as $row) {
	    		#Check if to be save data exists for new language
	    		$sql1 = "SELECT * FROM " . $table . " WHERE language_id = " . $new_language;
	    		$sql2 = "SELECT * FROM " . $table . " WHERE language_id = " . $from_language;
	    		$where_sql_1 = $where_sql_2 = '';
		    	foreach ( $pkeys as $key ) {
    				//Skip language_id and autoincrement from the key. autoincrement is unique by itself.
		    		if ($key != 'language_id' && $key != $auto_column) {
	    				if ( in_array($row[$key], $langs) ) {
	    					$where_sql_1 .= " AND $key = '" . $langs[$new_language] . "'";
	    					$where_sql_2 .= " AND $key = '" . $langs[$from_language] . "'";
	    				}
	    				else {

	    					$where_sql_1 .= " AND $key = '" . $row[$key] . "'";
	    					$where_sql_2 .= " AND $key = '" . $row[$key] . "'";
	    				}
		    		}
	    		}

				$sql1 .= $where_sql_1;
	    		$sql2 .= $where_sql_2;

				$check_query = $this->db->query($sql1);
	    		if ($check_query->num_rows <= 0) {
	    			ADebug::variable('class ALanguage missing language data: ', $sql1);
	    			//we have no data, clone it
	    			$insert_flds = '';
	    			$insert_str = '';
	    			$origin_query = $this->db->query($sql2);
	    			foreach ($origin_query->rows as $drow) {
		    			foreach ($drow as $fld_name => $value) {
		    				if ( !empty($insert_flds) ) {
		    					$insert_flds .= ", ";
		    					$insert_str .= ", ";
		    				}
		    				if ( $fld_name == 'language_id') {
		    					$value = $new_language;
		    				}
		    				if ( $fld_name == $auto_column) {
		    					$value = '';
		    				}

		    				$insert_flds .= $fld_name;
		    				$insert_str .= "'" . $this->db->escape($value) . "'";
		    			}
	    			}

	    			if ( !empty($insert_flds) && !empty($insert_str) ) {
						$insrt_sql = "INSERT INTO " . $table . "(" . $insert_flds . ") VALUES (" . $insert_str . ")";
						ADebug::variable('class ALanguage cloning data: ', $insrt_sql);
						$this->db->query($insrt_sql);
						$tcount++;
					}
	    		}
    		}
    		if ($tcount > 0) {
    			$this->cache->delete('lan.*');
    		}
    	}
    	return "Total: " . $tcount . " language entries cloned for table ". $table . "<br>";
    }
	
	#select all tables that use language_id field   
    private function _get_language_based_tables() {
        $cache_file = 'tables.language_based_tables';
        $load_data = array();
	    if($this->cache){
            $load_data = $this->cache->get($cache_file);
	    }
        if (!$load_data) {
			$sql = "SELECT DISTINCT table_name 
			    	FROM information_schema.columns 
			    	WHERE column_name = 'language_id' AND table_schema='" . DB_DATABASE . "'";
			$load_sql = $this->db->query($sql);
			$load_data = $load_sql->rows;
			if($this->cache){
                $this->cache->set($cache_file, $load_data);
			}
        }  
        return $load_data;
	}  
       
    private function _table_get_keys($table_name) {
        $pkeys = array();
	    #get primary keys 
	    $sql = "SHOW INDEX FROM " . $table_name . "
	        	WHERE Key_name = 'PRIMARY'";
	    $primary_query = $this->db->query($sql);
	    foreach($primary_query->rows as $value) {
	        $pkeys[] = $value['Column_name'];
	    }	
		return $pkeys;  
    }
        
}