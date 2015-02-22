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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}
class ModelLocalisationLanguageDefinitions extends Model {
    /**
     * @param array $data
     * @return bool|int
     */
    public function addLanguageDefinition($data) {
		if (!is_array($data) || !$data) return false;

		$update_data = array();
		foreach ($data as $key => $val) {
			$update_data[$this->db->escape($key)] = htmlspecialchars_decode(trim($val));
		}

		if (empty($update_data['language_key'])
				|| empty($update_data['language_value'])
				|| empty($update_data['language_id'])
				|| empty($update_data['block'])
		) {

			$message = 'Tring to write new language definition but data is wrong.
			   language_key: ' . $update_data['language_key'] . ',
			   language_value: ' . $update_data['language_value'] . ',
			   block: ' . $update_data['block'] . ',
			   section: ' . (int)$update_data['section'] . ',
			   language_id: ' . (int)$update_data['language_id'] . '.';
			$this->messages->saveWarning('New language definition adding error.', $message);
			return false;
		}
		unset($update_data['language_definition_id']);

		//Handle special case of main block (english.xml, spanish.xml … )
		//do not auto translate this case. autotranslate will not save to right key 
		$autotranslate = true;
		if ( $this->language->isMainBlock($update_data['block'], $update_data['language_id']) ) {
			$autotranslate = false;
		} else if ( $this->language->isMainBlock($update_data['block'] ) ) {
			$autotranslate = false;
			//this is a main block in other language. Need to get right block name
			$lang_det = $this->language->getLanguageDetailsByID( $update_data['language_id'] );
			$update_data['block'] = $lang_det['filename'];
		}		
	
		//save definition. 
		$this->language->replaceDescriptions('language_definitions',
			array('section' => (int)$update_data['section'],
				'block' => $update_data['block'],
				'language_key' => $update_data['language_key']
			),
			array($update_data['language_id'] => array(
				'section' => (int)$update_data['section'],
				'block' => $update_data['block'],
				'language_key' => $update_data['language_key'],
				'language_value' => $update_data['language_value']
			)),
			$autotranslate
			);

		$this->cache->delete('lang');
		$this->cache->delete('language_definitions');
		$this->cache->delete('admin_menu');

		return true;
	}

    /**
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function editLanguageDefinition($id, $data) {
		if (empty($id) || !is_array($data) || !$data) return false;

		//NOTE: On edit we only care about new definition value.
		//Other details are loaded from definition 
		$lang_value = (string)$data['language_value'];
		$update_data = $this->getLanguageDefinition($id);

		//Handle special case of main block (english.xml, spanish.xml … )
		//do not auto translate this case. autotranslate will not save to right key 
		$autotranslate = true;
		if ( $this->language->isMainBlock($update_data['block'], $update_data['language_id']) ) {
			$autotranslate = false;
		} else if ( $this->language->isMainBlock($update_data['block'] ) ) {
			$autotranslate = false;
			//this is a main block in other language. Need to get right block name
			$lang_det = $this->language->getLanguageDetailsByID( $update_data['language_id'] );
			$update_data['block'] = $lang_det['filename'];
		}		

		if (isset($update_data['language_key'])) {
			$this->language->replaceDescriptions('language_definitions',
				array('section' => (int)$update_data['section'],
					'block' => $update_data['block'],
					'language_key' => $update_data['language_key']
				),
				array((int)$update_data['language_id'] => array(
					'section' => (int)$update_data['section'],
					'block' => $update_data['block'],
					'language_key' => $update_data['language_key'],
					'language_value' => html_entity_decode($lang_value, ENT_QUOTES, 'UTF-8')
				)),
				$autotranslate
				);
		} else {
			$this->language->replaceDescriptions('language_definitions',
				array('section' => (int)$update_data['section'],
					'block' => $update_data['block'],
					'language_key' => $update_data['language_key']
				),
				array((int)$update_data['language_id'] => array(
					'language_value' => html_entity_decode($lang_value, ENT_QUOTES, 'UTF-8')
				)),
				$autotranslate
				);
		}

		$this->cache->delete('lang');
		$this->cache->delete('language_definitions');
		$this->cache->delete('admin_menu');
        return true;
	}

    /**
     * @param int $id
     */
    public function deleteLanguageDefinition($id) {
		$result = $this->db->query("SELECT language_id, `section`, `language_key`, `block`
						FROM " . $this->db->table("language_definitions") . " 
						WHERE language_definition_id = '" . (int)$id . "'");
		foreach ($result->rows as $row) {
			$this->db->query("DELETE FROM " . $this->db->table("language_definitions") . " 
							  WHERE `section` = '" . $row['section'] . "'
							  		AND `block` = '" . $row['block'] . "'
							  		AND `language_key` = '" . $row['language_key'] . "'");
		}
		$this->cache->delete('lang');
		$this->cache->delete('language_definitions');
		$this->cache->delete('admin_menu');
	}

    /**
     * @param int $id
     * @return array
     */
    public function getLanguageDefinition($id) {
		$query = $this->db->query("SELECT DISTINCT *
									FROM " . $this->db->table("language_definitions") . " 
									WHERE language_definition_id = '" . (int)$id . "'");
		return $query->row;
	}

    /**
     * @param string $key
     * @param int $language_id
     * @param string $block
     * @param int $section
     * @return int
     */
    public function getLanguageDefinitionIdByKey($key, $language_id, $block, $section) {
		$query = $this->db->query("SELECT language_definition_id
									FROM " . $this->db->table("language_definitions") . " 
									WHERE language_key = '" . $this->db->escape($key) . "'
										AND block='" . $this->db->escape($block) . "'
										AND language_id='" . $this->db->escape($language_id) . "'
										AND section='" . (int)$section . "'");
		return (int)$query->row['language_definition_id'];
	}

    /**
     * @param string $key
     * @param string $block
     * @param int $section
     * @return array
     */
    public function getAllLanguageDefinitionsIdByKey($key, $block, $section) {
		$query = $this->db->query("SELECT language_definition_id
									FROM " . $this->db->table("language_definitions") . " 
									WHERE language_key = '" . $this->db->escape($key) . "'
										AND block='" . $this->db->escape($block) . "'
										AND section='" . (int)$section . "'");

		return $query->rows;
	}

    /**
     * @param array $data
     * @param string $mode
     * @return array
     */
    public function getLanguageDefinitions($data = array(), $mode = 'default') {

		if ($data || $mode == 'total_only') {
			$filter = (isset($data['filter']) ? $data['filter'] : array());

			if ($mode == 'total_only') {
				$sql = "SELECT count(*) as total
						FROM " . $this->db->table("language_definitions") . " ld
						LEFT JOIN " . $this->db->table("languages") . " l ON l.language_id = ld.language_id";
			} else {
				$sql = "SELECT ld.*, l.name as language_name, l.code as language_code
						FROM " . $this->db->table("language_definitions") . " ld
						LEFT JOIN " . $this->db->table("languages") . " l ON l.language_id = ld.language_id";
			}

			if (has_value($filter['section'])) {
                $filter['section'] = $filter['section']=='admin' ? 1 :0;
				$sql .= " WHERE `section` = '" . (int)$filter['section'] . "' ";
			} else {
				$sql .= " WHERE 1=1 ";
			}

			$data['language_id'] = isset($data['language_id']) ? (int)$data['language_id'] : (int)$this->request->get['language_id'];

			if ($data['language_id'] > 0) {
				$sql .= " AND ld.language_id = '" . $data['language_id'] . "'";
			}

			if (!empty($data['subsql_filter'])) {
				$sql .= " AND " . $data['subsql_filter'];
			}

			if (isset($filter['language_key']) && !is_null($filter['language_key'])) {
				$sql .= " AND `language_key` LIKE '%" . $this->db->escape($filter['language_key']) . "%' ";
			}

			if (isset($filter['name']) && !is_null($filter['name'])) {
				$sql .= " AND LOWER(l.name) LIKE '%" . $this->db->escape(mb_strtolower($filter['name'])) . "%' ";
			}

			//If for total, we done bulding the query
			if ($mode == 'total_only') {
				$query = $this->db->query($sql);
				return $query->row['total'];
			}

			$sort_data = array(
				'date_modified',
				'language_key',
				'language_value',
				'block'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY date_modified DESC, language_key, block";
			}

			if (isset($data['order']) && (strtoupper($data['order']) == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);
			$result = $query->rows;

			/* !!!for future use
		   // mark not defined
		   if(mode!='only_total'){
			   $languages = $this->language->getAvailableLanguages();
			   $language_count = sizeof($languages);
			   foreach($result as $k=>$definition){
				   $sql = "SELECT COUNT( DISTINCT language_id) as cnt
						   FROM " . DB_PREFIX . "language_definitions
						   WHERE section = '".$this->db->escape($definition['section'])."'
							   AND block = '".$this->db->escape($definition['block'])."'
							   AND language_key = '".$this->db->escape($definition['language_key'])."'";
				   $count = $this->db->query( $sql );
				   $count = $count->row;
				   if($count['cnt']!=$language_count){
					   $result[$k]['error'] = true;
				   }
			   }
		   }*/

			return $result;
		} else {
			$language_data = $this->cache->get('language_definitions');

			if (!$language_data) {
				$query = $this->db->query("SELECT *
				                           FROM " . $this->db->table("language_definitions") . " 
				                           WHERE language_id=" . (int)$this->config->get('admin_language_id') . "
				                           ORDER BY date_modified DESC, language_key, block");

				foreach ($query->rows as $result) {
					$language_data[$result['code']] = array(
						'language_definition_id' => $result['language_definition_id'],
						'language_id' => $result['language_id'],
						'section' => $result['section'],
						'block' => $result['block'],
						'language_key' => $result['language_key'],
						'language_value' => $result['language_value'],
						'date_modified' => $result['date_modified'],
					);
				}
				$this->cache->set('language_definitions', $language_data);
			}


			return $language_data;
		}
	}

    /**
     * @param array $data
     * @return array
     */
    public function getTotalDefinitions($data = array()) {
		return $this->getLanguageDefinitions($data, 'total_only');
	}

	/**
	 * Load needed data and build form for definitions add or edit
	 * @param ARequest $request - Data from request object
	 * @param array $data  - from requester
	 * @param AForm $form  - form object
	 * @return array ($data imputed processed and returned back)
	 */
	public function buildFormData( &$request, &$data, &$form ) {
		
		$fields = array( 'language_key', 'language_value', 'block', 'section' );
		$language_definition_id = $request->get['language_definition_id'];
		$view_mode = 'all';
		//if existing definition disable edit for some fields
		$disable_attr = '';
		if (has_value($language_definition_id)) {
			$disable_attr = ' readonly ';
		}
		
		$languages = $this->language->getAvailableLanguages();

		$content_lang_id = $this->language->getContentLanguageID();
		//load current content language to data
		foreach ($languages as $lang) {
			if($view_mode != 'all' && $lang['language_id'] != $content_lang_id ) {
				continue;
			}
			$data['languages'][$lang['language_id']] = $lang;
		}

		$def_det = array();
		$all_defs = array();
		//!!!! ATTENTION: Important to understand this process flow. See comments
		if (has_value($language_definition_id)) {
			// 1. language_definition_id is provieded, load definition based on ID
			$def_det = $this->getLanguageDefinition($language_definition_id);
			if ( empty($def_det) ) {
				//this is incorrect ID redirect to create new
				$parms = '&view_mode='.$view_mode;
				return ( array( 'redirect_params' => $parms ));				
			}
			
			//special case then main file is edited (english, russian, etc ).
			//Candidate for improvment. Rename these files to main.xml 			
			$main_block = $this->language->isMainBlock($def_det['block'], $def_det['language_id']);	

			// 2. make sure we load all the langaues from XML in case they were not used yet.
			foreach ($languages as $lang) {				
				$new_lang_obj = new ALanguageManager($this->registry, $lang['code'], $def_det['section']);
				if ($main_block) {
					$block_path = $lang['filename'];
					$block = $lang['filename'];
				} else {
					$block = $def_det['block'];
					$block_path = $new_lang_obj->convert_block_to_file($def_det['block']);
					if ( empty($block_path)) {
						$block_path = $block;
					}
				}		
				if($block_path){
					$new_lang_obj->_load($block_path);				
					//now load definition for all languages to be available in the template		
					$all_defs[] = $this->LoadDefinitionSetEmpty( $def_det['section'], $block, $def_det['language_key'], $lang['language_id']); 
				}
			}
			// 3. Redirect to correct definition for current content language selected
			//if different from selected language_definition_id
			if ( $def_det['language_id'] != $content_lang_id ) {
				if ($main_block) {
					$block = $data['languages'][$content_lang_id]['filename'];
				} else {
					$block = $def_det['block'];
				}
				$new_def = $this->LoadDefinitionSetEmpty( $def_det['section'], $block, $def_det['language_key'], $content_lang_id);
				//if exists redirect with correct language_definition_id for content language 
				if (has_value($new_def['language_definition_id'])) {
					$parms = '&view_mode='.$view_mode;	
					$parms .= '&language_definition_id='.$new_def['language_definition_id'];
					return ( array( 'redirect_params' => $parms ));		
				} else {
					//alow to create new one with blank defintion value
					$def_det = $new_def;
				}
			}
		}			
 
		foreach ($fields as $field) {
			if (isset($request->post[ $field ])) {
				$data[ $field ] = $request->post[ $field ];
			} elseif (isset($def_det)) {
				$data[ $field ] = $def_det[ $field ];
			} else {
				$data[ $field ] = '';
			}
		}

		$data[ 'form' ][ 'submit' ] = $form->getFieldHtml(array(
			'type' => 'button',
			'name' => 'submit',
			'text' => $this->language->get('button_save'),
			'style' => 'button1',
		));
		$data[ 'form' ][ 'cancel' ] = $form->getFieldHtml(array(
			'type' => 'button',
			'name' => 'cancel',
			'text' => $this->language->get('button_cancel'),
			'style' => 'button2',
		));

		if ($disable_attr) {
			$section_txt = $this->language->get('text_storefront');
			if ($data['section']){
				$section_txt = $this->language->get('text_admin');
			}
			$data['form']['fields']['section'] = $section_txt . 
				$form->getFieldHtml(array(	
					'type' => 'hidden',
					'name' => 'section',
					'value' =>  $data['section'],
				));	
			$data['form']['fields']['block'] = $data['block'] . 
				$form->getFieldHtml(array(	
					'type' => 'hidden',
					'name' => 'block',
					'value' =>  $data['block'],
				));	
			$data['form']['fields']['language_key'] = $data['language_key'] .
				$form->getFieldHtml(array(	
					'type' => 'hidden',
					'name' => 'language_key',
					'value' =>  $data['language_key'],
				));	
		} else { 
			$data['form']['fields']['section'] = $form->getFieldHtml(array(
			    'type' => 'selectbox',
			    'name' => 'section',
			    'options' => array(
			    	1 => $this->language->get('text_admin'),
			    	0 => $this->language->get('text_storefront'),
			    ),
			    'value' => $data['section'],
			    'required' => true,
			));
	
			$data['form']['fields']['block'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'block',
				'value' => $data['block'],
				'required' => true
			));
			$data['form']['fields']['language_key'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'language_key',
				'value' => $data['language_key'],
				'required' => true
			));
		}

		if ($main_block) {
				$data['form']['fields']['main_block'] = $form->getFieldHtml(array(	
					'type' => 'hidden',
					'name' => 'main_block',
					'value' =>  1,
				));			
		}



		//load all language fields for this definition to be awailable in the template
		foreach ($data['languages'] as $i) {
			$value = '';
			$id = '';
			if (!empty($request->post['language_value'][ $i['language_id'] ])) {
				$value = $request->post['language_value'][ $i['language_id'] ];
				foreach ($all_defs as $ii) {
					if ($ii['language_id'] == $i['language_id']) {
						$id = $ii['language_definition_id'];
						break;
					}
				}
			} else if (!empty($all_defs)) {
				foreach ($all_defs as $ii) {
					if ($ii['language_id'] == $i['language_id']) {

						$value = $ii['language_value'];
						$id = $ii['language_definition_id'];
						break;
					}
				}
			}
			$data['form']['fields']['language_value'][$i['language_id']] = $form->getFieldHtml(array(
				'type' => 'textarea',
				'name' => 'language_value[' . $i['language_id'] . ']',
				'value' => $value,
				'required' => true,
				'style' => 'large-field',
			)). $form->getFieldHtml(array(
				'type' => 'hidden',
				'name' => 'language_definition_id[' . $i['language_id'] . ']',
				'value' => $id,
			));
		}
		return(array());
	}

    /**
     * Special load for definition, with value or set empty value if not found
     * @param string $section
     * @param string $block
     * @param string $lang_key
     * @param int $lang_id
     * @return array 
     */
	public function LoadDefinitionSetEmpty( $section, $block, $lang_key, $lang_id) {
		$ret_arr = array();
		$ret_arr = $this->getLanguageDefinitions(
		    array(
		    	'subsql_filter' => "section = '" . $section . "'
		 					AND block = '" . $block . "' 
		 					AND language_key = '" . $lang_key . "'  ",
		 		'language_id' =>  $lang_id
		 		)
		 );
		 if(isset($ret_arr[0])) {
		 	return $ret_arr[0]; 
		 } else {
		 	//not found any details return only some values (new)
		 	$ret_arr['block'] = $block;
		 	$ret_arr['section'] = $section;
		 	$ret_arr['language_key'] = $lang_key;
		 	$ret_arr['language_id'] = $lang_id;
		 	$ret_arr['language_definition_id'] = '';
		 	return $ret_arr;
		 } 

	}

}
