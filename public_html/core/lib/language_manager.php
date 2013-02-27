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

class ALanguageManager extends Alanguage {
	//NOTE: This class is loaded in INIT for admin only
	/**
	 * @param Registry $registry
	 * @param string $code
	 * @param string $section
	 */
	public function __construct($registry, $code = '', $section = '') {
		parent::__construct($registry, $code, $section);
		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to access class ALanguageManager');
		}
	}

	#### Language Descriptions admin API Section #####

	/*
	* Insert new definitions and translate if configured
	* Arguments:
	* 	table name (database table name with no prefix)
	*   unique index to perform select
	*		Format: [key] => [value]
	*   text data array
	*		Format: [language id][key] => [value]
	*/
	public function addDescriptions($table_name, $index, $txt_data) {
		if (empty($table_name) || empty($index) || !is_array($txt_data) || count($txt_data) <= 0) return null;
		//Insert data provided per language in $data array
		$this->_do_insert_descriptions($table_name, $index, $txt_data);
		//translate to other languages
		$this->_do_translate_descriptions($table_name, $index, $txt_data);
		return null;
	}

	/*
	* Insert new definitions and translate if configured of serialized data by given keys
	* Arguments:
	* 	table name (database table name with no prefix)
	*   unique index to perform select
	*		Format: [key] => [value]
	*   text data array
	*		Format: [language id][key] => [value]
	*  ser_keys array with key's list of serialized array one of txt_data value
	* 		Format: $txt_data[language id][key] = array(key1,key2,key3)
	*/
	public function addDescriptionsSerialized($table_name, $index, $txt_data, $ser_keys = array()) {
		if (empty($table_name) || empty($index) || !is_array($txt_data) || count($txt_data) <= 0) return null;
		//Insert data provided per language in $data array
		$this->_do_insert_descriptions($table_name, $index, $txt_data);
		//translate to other languages
		$this->_do_translate_descriptions($table_name, $index, $txt_data, $ser_keys);
		return null;
	}

	/*
	* Update definitions and translate if configured
	* Arguments:
	* 	table name (database table name with no prefix)
	*   unique index to perform select
	*		Format: [key] => [value]
	*   text data array
	*		Format: [language id][key] => [value]
	*/
	public function updateDescriptions($table_name, $index, $txt_data) {
		if (empty($table_name) || empty($index) || !is_array($txt_data) || count($txt_data) <= 0) return null;
		//update provided lang data
		$this->_do_update_descriptions($table_name, $index, $txt_data);
		//translate to other languages
		$this->_do_translate_descriptions($table_name, $index, $txt_data);
		return null;
	}

	/*
	* Update definitions and translate if configured of serialized data by given keys
	* Arguments:
	* 	table name (database table name with no prefix)
	*   unique index to perform select
	*		Format: [key] => [value]
	*   text data array
	*		Format: [language id][key] => [value]
	*  ser_keys array with key's list of serialized array one of txt_data value
	* 		Format: $txt_data[language id][key] = array(key1,key2,key3)
	*/
	public function updateDescriptionsSerialized($table_name, $index, $txt_data, $ser_keys = array()) {
		if (empty($table_name) || empty($index) || !is_array($txt_data) || count($txt_data) <= 0) return null;
		//update provided lang data
		$this->_do_update_descriptions($table_name, $index, $txt_data);
		//translate to other languages
		$this->_do_translate_descriptions($table_name, $index, $txt_data, $ser_keys);
		return null;
	}

	/*
	* Insert or Update definitions and translate if configured
	* More stable aproach, but add extra select. If sure that you do update use updateDescriptions
	* Arguments:
	* 	table name (database table name with no prefix)
	*   unique index to perform select
	*		Format: [key] => [value]
	*   text data array
	*		Format: [language id][key] => [value]
	*/
	public function replaceDescriptions($table_name, $index, $txt_data) {
		if (empty($table_name) || empty($index) || !is_array($txt_data) || count($txt_data) <= 0) return null;

		//see if exists and update if it does. Do this per language
		foreach ($txt_data as $lang_id => $lang_data) {
			$select_index = $index;
			$select_index[ 'language_id' ] = $lang_id;
			if (count($this->getDescriptions($table_name, $select_index)) > 0) {
				$this->_do_update_descriptions($table_name, $index, array( $lang_id => $lang_data ));
			} else {
				$this->_do_insert_descriptions($table_name, $index, array( $lang_id => $lang_data ));
			}
		}
		//translate to other languages
		$this->_do_translate_descriptions($table_name, $index, $txt_data);
		return null;
	}

	/*
	* Insert or Update definitions and translate if configured of serialized data by given keys
	* More stable aproach, but add extra select. If sure that you do update use updateDescriptionsSerialized
	* Arguments:
	* 	table name (database table name with no prefix)
	*   unique index to perform select
	*		Format: [key] => [value]
	*   text data array
	*		Format: [language id][key] => [value]
	*  ser_keys array with key's list of serialized array one of txt_data value
	* 		Format: $txt_data[language id][key] = array(key1,key2,key3)
	*/
	public function replaceDescriptionsSerialized($table_name, $index, $txt_data, $ser_keys = array()) {
		if (empty($table_name) || empty($index) || !is_array($txt_data) || count($txt_data) <= 0) return null;

		//see if exists and update if it does. Do this per language
		foreach ($txt_data as $lang_id => $lang_data) {
			$select_index = $index;
			$select_index[ 'language_id' ] = $lang_id;
			if (count($this->getDescriptions($table_name, $select_index)) > 0) {
				$this->_do_update_descriptions($table_name, $index, array( $lang_id => $lang_data ));
			} else {
				$this->_do_insert_descriptions($table_name, $index, array( $lang_id => $lang_data ));
			}
		}
		//translate to other languages
		$this->_do_translate_descriptions($table_name, $index, $txt_data, $ser_keys);
		return null;
	}


	/*
	* Insert or Update definitions and translate if configured based on array of values
	* This is the case when unique index represents multiple [key] => [value] conbinations
	* Example: product_tags table 
	* Arguments:
	* 	table name (database table name with no prefix)
	*   unique index to perform select
	*		Format: [key] => [value]
	*   text data array
	*		Format: [language id] => [key] => {'value1', 'value2', … }  
	*/
	public function replaceMultipleDescriptions($table_name, $index, $txt_data) {
		if (empty($table_name) || empty($index) || !is_array($txt_data) || count($txt_data) <= 0){ return null; }

		/**
		 * @var AConfig $config
		 */
		$config = $this->registry->get('config');

		//see if exists and update if it does. Do this per language
		foreach ($txt_data as $lang_id => $lang_data) {
			$select_index = $index;
			$select_index[ 'language_id' ] = $lang_id;
			if (count($this->getDescriptions($table_name, $select_index)) > 0) {
				//delete before insert
				$this->deleteDescriptions($table_name, $select_index);
			} 
			//insert for each [key] => [value] set 
			foreach ($lang_data as $key => $values) {
				if(count($values)){
					foreach ($values as $value) {
						if ( has_value($value) ) {
							$this->_do_insert_descriptions($table_name, $index, array( $lang_id => array($key => $value) ));
						}
				 	}
				}
			}
			
			//check if we need to translate
			if ($config->get('auto_translate_status')) {
				//locate source language based on translation setting
				$src_lang_code = $config->get('translate_src_lang_code');
				$src_lang_id = $this->_get_language_id($src_lang_code);
				if (empty($txt_data[ $src_lang_id ])) {
					//this is not source language. exit
					return null;
				}
				//translate all active languages
				foreach ($this->getActiveLanguages() as $lang) {
					$language_id = $lang[ 'language_id' ];
					//skip source language and just inputed languages (updated before)
					if ($lang[ 'code' ] == $src_lang_code || !empty($txt_data[ $language_id ])) {
						continue;
					}
					$dest_lang_code = $this->getLanguageCodeByLocale($lang[ 'locale' ]);
					//get existing data and check if we create or update
					$newindex = array_merge($index, array( 'language_id' => $language_id ));
					$descriptions = $this->getDescriptions($table_name, $newindex);
					if (count($descriptions) && $config->get('translate_override_existing')) {
						//clean up if we need to translate again
						$this->deleteDescriptions($table_name, $newindex);
						$descriptions = array();
					} 

					if ( count($descriptions) <= 0) {
						//translate and save
						foreach ($txt_data[ $src_lang_id ] as $key => $values) {
							foreach ($values as $value) {
							if (!empty($value)) {
								$new_value = $this->translate($src_lang_code, $value, $dest_lang_code);
								$this->_do_insert_descriptions($table_name, $index, array( $language_id => array($key => $new_value) ));
							}
							}
						}
					}
				}
			}
			
		}
		return null;
	}

	/*
	* Delete definitions
	* Arguments:
	* 	table name (database table name with no prefix)
	*   unique index to perform select
	*		Format: [key] => [value]
	*/
	public function deleteDescriptions($table_name, $index) {
		if (empty($table_name) || empty($index)) return null;

		$del_index = array();
		foreach ($index as $i => $v) {
			if (has_value($v)) {
				$del_index[ ] = "$i = '" . $this->db->escape($v) . "'";
			}
		}
		$sql = "DELETE FROM " . DB_PREFIX . $table_name . " ";
		$sql .= "WHERE " . implode(" AND ", $del_index);
		$this->db->query($sql);

		return null;
	}

	/*
	* Select definitions
	* Arguments:
	* 	table name (database table name with no prefix)
	*   unique index to perform select
	*		Format: [key] => [value]
	*/
	public function getDescriptions($table_name, $index) {
		if (empty($table_name) || empty($index)) return array();

		$sel_index = array();
		foreach ($index as $i => $v) {
			if (has_value($v)) {
				$sel_index[ ] = "$i = '" . $this->db->escape($v) . "'";
			}
		}
		$sql = "SELECT * FROM " . DB_PREFIX . $table_name . " ";
		$sql .= "WHERE " . implode(" AND ", $sel_index);
		$query_result = $this->db->query($sql);

		if ($query_result->num_rows) {
			return $query_result->rows;
		} else {
			return array();
		}
	}

	// Perform SQL update
	/**
	 * @param $table_name
	 * @param $index
	 * @param $txt_data
	 * @return null
	 */
	private function _do_update_descriptions($table_name, $index, $txt_data) {
		if (empty($table_name) || empty($index) || empty($txt_data)) return null;

		foreach ($txt_data as $lang_id => $lang_data) {
			if (!$lang_data) continue;
			$update_index = array();
			foreach ($index as $i => $v) {
				if (has_value($v)) {
					$update_index[ ] = "$i = '" . $this->db->escape($v) . "'";
				}
			}
			$update_index[ ] = "language_id = '" . $this->db->escape($lang_id) . "'";

			$update_data = array();
			foreach ($lang_data as $i => $v) {
				$update_data[ ] = "$i = '" . $this->db->escape($v) . "'";
			}

			$sql = "UPDATE " . DB_PREFIX . $table_name . " ";
			$sql .= "SET " . implode(", ", $update_data) . " WHERE " . implode(" AND ", $update_index);
			$this->db->query($sql);
		}
		return null;
	}

	// Perform SQL insert
	private function _do_insert_descriptions($table_name, $index, $txt_data) {
		if (empty($table_name) || empty($index) || empty($txt_data)) return null;

		foreach ($txt_data as $lang_id => $lang_data) {
			//sanitize data 
			$load_data = array();
			foreach ($lang_data as $key => $value) {
				$lang_data[ $key ] = $this->db->escape($value);
			}
			$load_data = array_merge($lang_data, $index, array( 'language_id' => $lang_id ));
			$sql = "INSERT INTO " . DB_PREFIX . $table_name . " ";
			$sql .= "(`" . implode("`, `", array_keys($load_data)) . "`) VALUES ('" . implode("', '", $load_data) . "') ";
			$this->db->query($sql);
		}
		return null;
	}

	//translate descriptions
	/**
	 * @param string $table_name
	 * @param array $index
	 * @param array $txt_data
	 * @param array $serialized_roadmap
	 * @return bool|null
	 */
	private function _do_translate_descriptions($table_name, $index, $txt_data, $serialized_roadmap = array()) {
		/**
		 * @var AConfig $config
		 */
		$config = $this->registry->get('config');
		//check if translation is ON
		if (!$config->get('auto_translate_status')) {
			return null;
		}

		//locate source language based on translation setting
		$src_lang_code = $config->get('translate_src_lang_code');
		$src_lang_id = $this->_get_language_id($src_lang_code);

		if (empty($txt_data[ $src_lang_id ])) {
			return false;
		}
		//translate all active languages
		foreach ($this->getActiveLanguages() as $lang) {
			$language_id = $lang[ 'language_id' ];
			$new_txt_data = array();
			$update_txt_data = array();
			//skip source language and just inputed languages (updated before)
			if ($lang[ 'code' ] == $src_lang_code || !empty($txt_data[ $language_id ])) {
				continue;
			}
			$dest_lang_code = $this->getLanguageCodeByLocale($lang[ 'locale' ]);
			//get existing data and check if we create or update
			$newindex = array_merge($index, array( 'language_id' => $language_id ));
			$descriptions = $this->getDescriptions($table_name, $newindex);
			if (count($descriptions)) {
				//updates expect only 1 row per this index
				foreach ($descriptions[ 0 ] as $key => $value) {
					$txt_to_translate = $txt_data[ $src_lang_id ][ $key ];
					//check if force override settings is enabled and skip if transltion esits
					if (($config->get('translate_override_existing') && !empty($txt_to_translate))
							|| (empty($value) && !empty($txt_to_translate))
					) {
						if (in_array($key, array_keys($serialized_roadmap))) {
							$unserialized_data = unserialize($txt_to_translate);
							if ($unserialized_data !== false) {
								$new_unserialized_data = $this->_translateSerializedData($unserialized_data, $serialized_roadmap[ $key ], $src_lang_code, $dest_lang_code);
								$update_txt_data[ $language_id ][ $key ] = serialize($new_unserialized_data);
							} else {
								$update_txt_data[ $language_id ][ $key ] = $txt_to_translate;
							}
						} else {
							$update_txt_data[ $language_id ][ $key ] = $this->translate($src_lang_code, $txt_to_translate, $dest_lang_code);
						}
					}
				}
			} else {
				//insert only
				//translate source text
				foreach ($txt_data[ $src_lang_id ] as $key => $value) {
					if (!empty($value)) {
						if (in_array($key, array_keys($serialized_roadmap))) {
							$unserialized_data = unserialize($value);
							if ($unserialized_data !== false) {
								$new_unserialized_data = $this->_translateSerializedData($unserialized_data, $serialized_roadmap[ $key ], $src_lang_code, $dest_lang_code);
								$new_txt_data[ $language_id ][ $key ] = serialize($new_unserialized_data);
							} else {
								$new_txt_data[ $language_id ][ $key ] = $value;
							}
						} else {
							$new_txt_data[ $language_id ][ $key ] = $this->translate($src_lang_code, $value, $dest_lang_code);
						}
					}
				}
			}

			//we have somethign to save
			if (count($new_txt_data)) {
				$this->_do_insert_descriptions($table_name, $index, $new_txt_data);
			}
			if (count($update_txt_data)) {
				$this->_do_update_descriptions($table_name, $index, $update_txt_data);
			}
		}
		return null;
	}

	#### END Language Descriptions admin API Section #####

	//Function to delete all data in tables that have language id specified
	public function deleteAllLanguageEntries($language_id) {
		if (empty($language_id)) return null;

		$tables = $this->_get_language_based_tables();
		foreach ($tables as $table_name) {
			//Skip history based tables
			if (strstr($table_name[ 'table_name' ], 'orders')) {
				continue;
			}
			$this->db->query("DELETE FROM " . $table_name[ 'table_name' ] . " WHERE language_id = '" . (int)$language_id . "'");
		}
	}

	//Clone record to all availabe langauges from specified language ID.
	public function cloneToAllLanguages($table, $source_language) {
		$ret_str = '';
		// for each langauge Call _clone_language_rows
		foreach ($this->available_languages as $lng) {
			$language_id = $lng[ 'language_id' ];
			if ($language_id == $source_language) {
				continue;
			}
			$pkeys = array();
			$pkeys = $this->_get_primary_keys($table);

			$ret_str .= $this->_clone_language_rows($table, $pkeys, $language_id, $source_language);
		}
		return $ret_str;
	}

	//Convert block name from database to XML file name
	public function convert_block_to_file($block) {
		$filename = $block;
		$parts = explode('_', $filename);
		$test_array = array();
		array_push($test_array, $parts[ 0 ]);
		for ($i = 1; $i < count($parts); $i++) {
			$total_arr = count($test_array);
			for ($j = 0; $j < $total_arr; $j++) {
				array_push($test_array, $test_array[ $j ] . '_' . $parts[ $i ]);
				$test_array[ $j ] .= '/' . $parts[ $i ];
			}
		}
		//check what path is valid
		foreach ($test_array as $filename) {
			$file_path = $this->_detect_language_xml_file($filename);
			if (file_exists($file_path)) {
				return $filename;
			}
		}
		return false;
	}


	//Function to populate all tables that have language linked data with new language data. Copy from default if source language is not provided
	public function fillMissingLanguageEntries($language_id, $source_language_id = 1, $translate_method = '') {
		if (empty($language_id)) {
			return null;
		}

		//First make sure source language definition XML files are all loaded (refreshed)
		$this->definitionAutoLoad((int)$source_language_id, 'all', 'all', 'add');
		//Now make sure destination language definition XML files are all loaded (refreshed)
		$this->definitionAutoLoad((int)$language_id, 'all', 'all', 'add');

		$ret_str = '';
		$tables = $this->_get_language_based_tables();
		foreach ($tables as $table_name) {
			$pkeys = array();
			//Set special case table and
			if (strstr($table_name[ 'table_name' ], 'language_definitions')) {
				array_push($pkeys, 'language_definition_id', 'language_id', 'section', 'block', 'language_key');
			} else if (strstr($table_name[ 'table_name' ], 'orders') || strstr($table_name[ 'table_name' ], 'languages')) {
				//skip these tables
				continue;
			} else {
				#get primary keys
				$pkeys = $this->_get_primary_keys($table_name[ 'table_name' ]);
			}

			$ret_str .= $this->_clone_language_rows($table_name[ 'table_name' ], $pkeys, $language_id, $source_language_id, '', $translate_method);
		}
		return $ret_str;
	}

	/*
	 * method for reloading definitions from xml-file to database
	 */

	/**
	 * @param $language_id -  if 0 - all languages
	 * @param string $section  - 1 or 0 - admin or storefront
	 * @param string $specific_block - name of the block
	 * @param string $mode - mode can be the types: update, add
	 */
	public function definitionAutoLoad($language_id, $section, $specific_block, $mode = 'add', $language_key = '') {

		if ((int)$language_id === 0 && $language_id != 'all') {
			$this->error = 'Can\'t to reload definitions when language id is unknown ("' . $language_id . '").';
			return false;
		}
		if (!in_array($section, array( 1, 0, 'all', 'admin', 'storefront' ), true)) {
			$this->error = 'Can\'t to reload definitions when section is not in array( 1, 0, "all" ).';
			return false;
		}

		if (!in_array($mode, array( 'add', 'update' ))) {
			$this->error = 'Can\'t to reload definitions when mode is unknown("' . $mode . '"). Only "add" or "update" are permitted.';
			return false;
		}
		ini_set('max_execution_time', 600);
		$sections = $section == 'all' ? array( 'admin', 'storefront' ) : '';
		$sections = in_array($section, array( 'admin', 1 ), true) ? array( 'admin' ) : $sections;
		$sections = in_array($section, array( 'storefront', 0 ), true) ? array( 'storefront' ) : $sections;

		foreach ($this->available_languages as $lang) {
			if ($language_id != 'all' && $language_id == $lang[ 'language_id' ]) {
				$language_ids[ $lang[ 'directory' ] ] = $lang[ 'language_id' ];
				$language_codes[ $lang[ 'directory' ] ] = $lang[ 'code' ];
				break;
			} else {
				$language_ids[ $lang[ 'directory' ] ] = $lang[ 'language_id' ];
				$language_codes[ $lang[ 'directory' ] ] = $lang[ 'code' ];
			}
		}

		if ($mode == 'update') {
			//For update mode delete updated languages
			$sql = "DELETE FROM " . DB_PREFIX . "language_definitions ";
			$sql .= " WHERE language_id IN ('" . implode("', '", $language_ids) . "') ";
			if ($language_key) {
				$sql .= "AND language_key='" . $language_key . "' ";
			}
			if ($specific_block != 'all') {
				$sql .= "AND block='" . $this->db->escape(str_replace('/', '_', $specific_block)) . "' ";
			}
			if ($section != 'all') {
				$section = $section == 'admin' ? 1 : 0;
				$sql .= "AND section='" . (int)$section . "' ";
			}

			$this->db->query($sql);
		}

		if (is_object($this->cache)) {
			$this->cache->delete('lang');
			$this->cache->delete('language_definitions');
			$this->cache->delete('storefront_menu');
		}

		foreach ($language_ids as $lang_name => $lang_id) {
			//get list of lang blocks for every language
			if ($specific_block == 'all') {
				if (($language_blocks = $this->getAllLanguageBlocks($lang_name)) === false) {
					continue;
				}
			} else {
				// create list of language blocks when $block is set
				$language_blocks = array();
				$blocks = $this->getAllLanguageBlocks($lang_name);
				foreach ($sections as $sect) {
					foreach ($blocks[ $sect ] as $rt) {
						if ($rt == $specific_block) {
							$language_blocks[ $sect ][ ] = $rt;
							break;
						}
					}
					foreach ($blocks[ 'extensions' ][ $sect ] as $rt) {
						if ($rt == $specific_block) {
							$language_blocks[ 'extensions' ][ $sect ][ ] = $rt;
							break;
						}
					}
					$language_blocks[ $sect ] = !isset($language_blocks[ $sect ]) ? array() : $language_blocks[ $sect ];
					$language_blocks[ 'extensions' ][ $sect ] = !isset($language_blocks[ 'extensions' ][ $sect ]) ? array() : $language_blocks[ 'extensions' ][ $sect ];
				}
			}

			foreach ($sections as $sect) {
				$alang = new ALanguage($this->registry, $language_codes[ $lang_name ], ($sect == 'admin' ? 1 : 0));
				// load into db extensions definitions
				foreach ($language_blocks[ 'extensions' ][ $sect ] as $rt) {
					if ($specific_block != 'all' && $rt != $specific_block) {
						continue;
					}
					$alang->load($rt, 'silent');
				}

				// load into db core admin & storefront
				foreach ($language_blocks[ $sect ] as $rt) {
					$alang->load($rt, 'silent');
				}
			}
		}

	}

	public function getAllLanguageBlocks($language_name = 'english') {
		if (empty($language_name)) {
			$this->error = "Can't get language blocks because language name is empty.";
			return false;
		}
		$result = array( 'admin' => array(),
			'storefront' => array(),
			'extensions' => array( 'admin' => array(),
				'storefront' => array() )
		);
		// admin
		$lang_dir = DIR_LANGUAGE . $language_name;
		$xml_files = array();
		$xml_files = getFilesInDir($lang_dir, 'xml');
		foreach ($xml_files as $file) {
			$result[ 'admin' ][ ] = str_replace('.xml', '', str_replace($lang_dir . '/', '', $file));
		}
		//storefront
		$lang_dir = DIR_STOREFRONT . 'language/' . $language_name;
		$xml_files = getFilesInDir($lang_dir, 'xml');
		foreach ($xml_files as $file) {
			$result[ 'storefront' ][ ] = str_replace('.xml', '', str_replace($lang_dir . '/', '', $file));
		}

		// extensions
		$extensions_dirs = glob(DIR_EXT . '*', GLOB_ONLYDIR);

		foreach ($extensions_dirs as $extension_dir) {
			//$extension_name = pathinfo($extension_dir,PATHINFO_BASENAME);
			$lang_dir = $extension_dir . '/admin/language/' . $language_name;
			if (is_dir($lang_dir)) {
				$xml_files = getFilesInDir($lang_dir, 'xml');
				foreach ($xml_files as $file) {
					$result[ 'extensions' ][ 'admin' ][ ] = str_replace('.xml', '', str_replace($lang_dir . '/', '', $file));
				}
			}

			$lang_dir = $extension_dir . '/storefront/language/' . $language_name;
			if (is_dir($lang_dir)) {
				$xml_files = getFilesInDir($lang_dir, 'xml');
				foreach ($xml_files as $file) {
					$result[ 'extensions' ][ 'storefront' ][ ] = str_replace('.xml', '', str_replace($lang_dir . '/', '', $file));
				}
			}
		}
		return $result;
	}

	/*
	* Clone language_definition text that is present in source language and missing in destination
	* Possibly USLESS NOW ????
	*/
	public function cloneMissingDefinitions($block, $language_id, $source_language) {
		$pkeys = array();
		ADebug::checkpoint('ALanguage ' . $this->language_details[ 'name' ] . ' ' . $block . ' clone missing text from ' . $source_language);
		array_push($pkeys, 'language_definition_id', 'language_id', 'section', 'block', 'language_key');
		$section = $this->is_admin ? 1 : 0;
		$specific_sql = " AND block = '" . $block . "' AND section = '" . $section . "'";
		return $this->_clone_language_rows(DB_PREFIX . 'language_definitions', $pkeys, $language_id, $source_language, $specific_sql);
	}

	/**
	* Translate provided text to requested language
	* Configured method is used (default translation is a COPY)
	* Arguments:
	* 	source language code (2 char ISO 639-1 Code)
	*   UTF8 text to translate
	*   destination language code (2 char ISO 639-1 Code)
	*	translate_method (optional)
	*
	* Return:
	*	UTF8 Translation text
	*
	 * @param string $source_lang_code - two-letters language code
	 * @param string $src_text
	 * @param string $dest_lang_code - two-letters language code
	 * @param string $translate_method
	 * @return null
	 * @throws AException
	 */
	public function translate($source_lang_code, $src_text, $dest_lang_code, $translate_method = '') {
		$this->registry->get('extensions')->hk_InitData($this, __FUNCTION__);
		$result_txt = '';
		if (empty($source_lang_code) || empty($src_text) || empty($dest_lang_code)) {
			return null;
		}
		//check what method is selected for translation
		if (empty($translate_method)) {
			$translate_method = $this->registry->get('config')->get('translate_method');
		}
		$extensions = $this->registry->get('extensions')->getEnabledExtensions();
		if (in_array($translate_method, $extensions)) {
			$ex_class = DIR_EXT . $translate_method . '/core/translator.php';
			if (file_exists($ex_class)) {
				require_once($ex_class);
			} else {
				throw new AException(AC_ERR_LOAD, 'Error: Could not load translations class ' . $ex_class . '!');
			}

			/** @noinspection PhpUndefinedClassInspection */
			$translate_driver = new translator($this->registry->get('config'));
			/** @noinspection PhpUndefinedMethodInspection */
			$result_txt = $translate_driver->translate($source_lang_code, $src_text, $dest_lang_code);
			if (!$result_txt) {
				$result_txt = $src_text;
			}
			ADebug::checkpoint("AlangugeManager: Translated text: $src_text from $source_lang_code to $dest_lang_code");
		} else {
			//fail over to default 'copy_source_text' method
			$result_txt = $src_text;
		}
		$this->registry->get('extensions')->hk_UpdateData($this, __FUNCTION__);
		return $result_txt;
	}

	private function _translateSerializedData($unserialized, $roadmap, $source_lang_code, $dest_lang_code, $translate_method = '') {

		if (empty($source_lang_code) || empty($unserialized) || empty($dest_lang_code)) {
			return null;
		}
		//check what method is selected for translation
		if (empty($translate_method)) {
			$translate_method = $this->registry->get('config')->get('translate_method');
		}

		foreach ($roadmap as $key) {
			$unserialized = $this->_translateArrayRecursive($unserialized, $key, $source_lang_code, $dest_lang_code, $translate_method);
		}


		return $unserialized;
	}

	private function _translateArrayRecursive($array, $translate_key, $source_lang_code, $dest_lang_code, $translate_method) {
		if (!is_array($array)) return $array;

		if (isset($array[ $translate_key ])) {
			$array[ $translate_key ] = $this->translate($source_lang_code, $array[ $translate_key ], $dest_lang_code, $translate_method);
			return $array;
		} else {
			foreach ($array as $key => $value) {
				if (is_array($value) && isset($value[ $translate_key ])) {
					$array[ $key ][ $translate_key ] = $this->translate($source_lang_code, $value[ $translate_key ], $dest_lang_code, $translate_method);
				} elseif (is_array($value)) {
					$array[ $key ] = $this->_translateArrayRecursive($value, $translate_key, $source_lang_code, $dest_lang_code, $translate_method);
				}
			}
		}
		return $array;
	}

	/*
	* Get available translation methods
	* Return Array [tranlate_code] => [translation name]
	*/
	public function getTranslationMethods() {
		$this->registry->get('extensions')->hk_InitData($this, __FUNCTION__);
		//default copy method
		$methods = array( 'copy_source_text' => 'Copy Source Language Text' );
		$translaters = $this->registry->get('extensions')->getExtensionsList(array( 'category' => 'Translaters', 'status' => 1 ));

		foreach ($translaters->rows as $translater) {
			$methods[ $translater[ 'key' ] ] = $this->registry->get('extensions')->getExtensionName($translater[ 'key' ]);
		}

		$this->registry->get('extensions')->hk_UpdateData($this, __FUNCTION__);

		return $methods;
	}


	//#PR Duplicate row from default langauge to new and translate if needed
	private function _clone_language_rows($table, $pkeys, $new_language, $from_language = 1, $specific_sql = '', $translate_method = '') {
		if (empty($table) || empty ($pkeys) || empty($new_language)) {
			return null;
		}
		// Locate autoincrement column
		$autoincrenent_sql = "SHOW COLUMNS FROM " . $table . " where Extra = 'auto_increment'";
		$autoincrement = $this->db->query($autoincrenent_sql);
		$auto_column = $autoincrement->row[ 'Field' ];

		//get all fields that are translatable
		$translatable_fields = array();
		$translatable_fields = $this->_get_translatable_fields($table);

		//Build a keys string for select
		$keys_str = '';
		$tcount = 0;
		foreach ($pkeys as $key) {
			if (!empty($keys_str)) {
				$keys_str .= ",";
			}
			$keys_str .= "$key ";
		}
		$sql = "SELECT " . $keys_str . " FROM " . $table . " WHERE language_id = " . $from_language . $specific_sql;
		$tables_query = $this->db->query($sql);
		if ($tables_query->num_rows) {
			$langs = array();
			foreach ($this->available_languages as $lang) {
				$langs[ $lang[ 'language_id' ] ] = $lang[ 'filename' ];
			}

			foreach ($tables_query->rows as $row) {
				#Check if to be save data exists for new language
				$sql1 = "SELECT * FROM " . $table . " WHERE language_id = " . $new_language;
				$sql2 = "SELECT * FROM " . $table . " WHERE language_id = " . $from_language;
				$where_sql_1 = $where_sql_2 = '';
				foreach ($pkeys as $key) {
					//Skip language_id and autoincrement from the key. autoincrement is unique by itself.
					if ($key != 'language_id' && $key != $auto_column) {
						if (in_array($row[ $key ], $langs)) {
							$where_sql_1 .= " AND $key = '" . $langs[ $new_language ] . "'";
							$where_sql_2 .= " AND $key = '" . $langs[ $from_language ] . "'";
						} else {

							$where_sql_1 .= " AND $key = '" . $row[ $key ] . "'";
							$where_sql_2 .= " AND $key = '" . $row[ $key ] . "'";
						}
					}
				}

				$sql1 .= $where_sql_1;
				$sql2 .= $where_sql_2;

				$check_query = $this->db->query($sql1);
				if ($check_query->num_rows <= 0) {
					ADebug::variable('class ALanguage missing language data: ', $sql1);
					//we have no data, clone it
					$insert_data = array();
					$origin_query = $this->db->query($sql2);
					foreach ($origin_query->rows as $drow) {
						foreach ($drow as $fld_name => $value) {
							if ($fld_name == 'language_id') {
								$value = $new_language;
							}
							if ($fld_name == $auto_column) {
								$value = '';
							}
							if (in_array($fld_name, $translatable_fields)) {
								//we need to translate
								$value = $this->translate($this->_get_language_code($from_language),
									$value,
									$this->_get_language_code($new_language),
									$translate_method);
							}
							$insert_data[ $fld_name ] = $this->db->escape($value);
						}
					}

					if (!empty($insert_data)) {
						$insrt_sql = "INSERT INTO " . $table . "(" . implode(',', array_keys($insert_data)) . ") VALUES ('" . implode("','", $insert_data) . "')";
						ADebug::variable('class ALanguage cloning data: ', $insrt_sql);

						if ($table == DB_PREFIX . 'language_definitions') {
							//#PR There are some key condition in definitions that can be duplicate (CASE: block = 'english' main language ) skip
							//We assume that main language XML need to be present 
							//TODO rename main language file to common.xml
							if (!$this->_is_definition_in_db($insert_data)) {
								$this->db->query($insrt_sql);
							} else {
								continue;
							}
						} else {
							$this->db->query($insrt_sql);
						}
						$tcount++;
					}
				}
			}
			if ($tcount > 0) {
				$this->cache->delete('lan.*');
			}
		}
		return "Total: " . $tcount . " language entries cloned for table " . $table . "<br>";
	}

	//#PR select all tables that use language_id field and related to language  
	private function _get_language_based_tables() {
		$cache_file = 'tables.language_based_tables';
		$load_data = array();
		if ($this->cache) {
			$load_data = $this->cache->get($cache_file);
		}
		if (!$load_data) {
			$sql = "SELECT DISTINCT table_name 
			    	FROM information_schema.columns 
			    	WHERE column_name = 'language_id' AND table_schema='" . DB_DATABASE . "'";
			$load_sql = $this->db->query($sql);
			$load_data = $load_sql->rows;
			if ($this->cache) {
				$this->cache->set($cache_file, $load_data);
			}
		}
		return $load_data;
	}

	//#PR select all fields for the table that can be translated   
	private function _get_translatable_fields($table_name) {
		if (empty($table_name)) return null;

		$cache_file = "tables.translatable.$table_name";
		$load_data = array();
		if ($this->cache) {
			$load_data = $this->cache->get($cache_file);
		}
		if (!$load_data) {
			$sql = "SELECT column_name
			    	FROM information_schema.columns 
			    	WHERE table_name='" . $this->db->escape($table_name) . "' and column_comment='translatable'";
			$load_sql = $this->db->query($sql);
			$load_data = $load_sql->rows;
			if ($this->cache) {
				$this->cache->set($cache_file, $load_data);
			}
		}
		return $load_data;
	}

	//#PR select al primary keys for the table
	private function _get_primary_keys($table_name) {
		$pkeys = array();
		#get primary keys
		$sql = "SHOW INDEX FROM " . $table_name . "
	        	WHERE Key_name = 'PRIMARY'";
		$primary_query = $this->db->query($sql);
		foreach ($primary_query->rows as $value) {
			$pkeys[ ] = $value[ 'Column_name' ];
		}
		return $pkeys;
	}

	public function getLanguageCodeByLocale($locale) {
		if (empty($locale)) return null;
		$locale = explode(",", $locale);
		$lc = $locale[ 1 ];
		if (strpos($lc, '_') === 2) {
			$lng_code = strtolower(substr($lc, 0, 2));
		} else {
			$lng_code = $lc;
		}
		return $lng_code;
	}

	public function getLanguageIdByCode($code) {
		foreach ($this->available_languages as $lang) {
			if (is_int(stripos($lang[ 'locale' ], $code))) {
				return $lang[ 'language_id' ];
			}
		}
		return null;
	}

}