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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
/**
 * @property ALanguageManager $language
 * @property ADB $db
 */
class AFormManager {
	protected $registry;
	public $errors = 0; // errors during process
	private $form_id;
	private $form_fields = array ();
	private $form_field_groups = array ();
	private $field_types = array ('I', 'T', 'C', 'H', 'S', 'M', 'R', 'G' ); // array for check field element type
	

	public function __construct($form_name = '') {
		if (! IS_ADMIN) { // forbid for non admin calls
			throw new AException ( AC_ERR_LOAD, 'Error: permission denied to change forms' );
		}
		
		$this->registry = Registry::getInstance ();
		
		//check if form with same name exists
		$sql = "SELECT form_id FROM " . $this->db->table("forms") . " WHERE form_name='" . $this->db->escape ( $form_name ) . "'";
		$result = $this->db->query ( $sql );
		$this->form_id = ( int ) $result->row ['form_id'];
		
		if ($this->form_id) {
			// field groups of form
			$sql = "SELECT group_id FROM " . $this->db->table("form_groups") . " WHERE form_id='" . $this->form_id . "'";
			$result = $this->db->query ( $sql );
			if ($result->num_rows) {
				$this->form_field_groups [] = ( int ) $result->row ['group_id'];
			}
			
			// fields of form
			$sql = "SELECT field_id FROM " . $this->db->table("fields") . " WHERE form_id='" . $this->form_id . "'";
			$result = $this->db->query ( $sql );
			if ($result->num_rows) {
				$this->form_fields [] = ( int ) $result->row ['field_id'];
			}
		
		}
	
	}
	
	public function __get($key) {
		return $this->registry->get ( $key );
	}
	
	public function __set($key, $value) {
		$this->registry->set ( $key, $value );
	}
	
	private function _getFieldGroupIdByName($group_name = '') {
		if (! $group_name || ! $this->form_id) {
			return null;
		}
		$sql = "SELECT group_id FROM " . $this->db->table("form_groups") . " WHERE group_name = '" . $this->db->escape ( $group_name ) . "' AND form_id = '" . $this->form_id . "'";
		$result = $this->db->query ( $sql );
		return ( int ) $result->row ['group_id'];
	}

	/**
	 * @param string $field_group_id
	 * @param int $language_id
	 * @return null|array
	 */
	private function _getFieldGroupDescription($field_group_id = '', $language_id = 0) {
		$language_id = ( int ) $language_id;
		$field_group_id = ( int ) $field_group_id;
		if (! $field_group_id || ! $language_id) {
			return null;
		}
		$sql = "SELECT * FROM " . $this->db->table("fields_group_descriptions") . " WHERE group_id = '" . $field_group_id . "' AND language_id = '" . $language_id . "'";
		$result = $this->db->query ( $sql );
		return $result->row;
	}
	
	private function _getFieldIdByName($field_name = '') {
		if (! $field_name || ! $this->form_id) {
			return null;
		}
		$sql = "SELECT field_id FROM " . $this->db->table("fields") . " WHERE field_name = '" . $this->db->escape ( $field_name ) . "' AND form_id= '" . $this->form_id . "'";
		$result = $this->db->query ( $sql );
		return ( int ) $result->row ['field_id'];
	}
	
	private function _getFieldDescription($field_id = '', $language_id = 0) {
		$language_id = ( int ) $language_id;
		$field_id = ( int ) $field_id;
		if (! $field_id || ! $this->form_id || ! $language_id) {
			return null;
		}
		$sql = "SELECT * FROM " . $this->db->table("field_descriptions") . " WHERE field_id = '" . $field_id . "' AND language_id = '" . $language_id . "'";
		$result = $this->db->query ( $sql );
		return $result->row;
	}
	
	private function _getFormDescription($language_id = 0) {
		$language_id = ( int ) $language_id;
		if (! $this->form_id || ! $language_id) {
			return null;
		}
		$sql = "SELECT * FROM " . $this->db->table("form_descriptions") . " WHERE form_id = '" . $this->form_id . "' AND language_id = '" . $language_id . "'";
		$result = $this->db->query ( $sql );
		return $result->row;
	}
	
	private function _getFieldValues($field_id = 0) {
		$field_id = ( int ) $field_id;
		$language_id = ( int ) $language_id;
		if (! $field_id) {
			return null;
		}
		
		$sql = "SELECT * FROM " . $this->db->table("field_values") . " WHERE field_id = '" . ( int ) $field_id . "' AND language_id = '" . $language_id . "'";
		$result = $this->db->query ( $sql );
		return $result->row;
	}
	
	private function _getLanguageIdByName($language_name = '') {
		$language_name = mb_strtolower ( $language_name, 'UTF-8' );
		$query = "SELECT language_id FROM " . $this->db->table("languages") . " 
					WHERE LOWER(name) = '" . $this->db->escape ( $language_name ) . "'";
		$result = $this->db->query ( $query );
		return $result->row ? $result->row ['language_id'] : 0;
	}
	
	public function loadXML($data) {
		// Input possible with XML string, File or both.
		// We process both one at a time. XML string processed first		
		if ($data ['xml']) {
			/**
			 * @var $xml_obj SimpleXmlElement
			 */
			$xml_obj = simplexml_load_string ( $data ['xml'] );
			if (! $xml_obj) {
				$err = "Failed loading XML data string";
				foreach ( libxml_get_errors () as $error ) {
					$err .= "  " . $error->message;
				}
				$error = new AError ( $err );
				$error->toLog ()->toDebug ();
			} else {
				$this->_processXML ( $xml_obj );
			}
		}
		
		if (isset ( $data ['file'] ) && is_file ( $data ['file'] )) {
			$xml_obj = simplexml_load_file ( $data ['file'] );
			if (! $xml_obj) {
				$err = "Failed loading XML file " . $data ['file'];
				foreach ( libxml_get_errors () as $error ) {
					$err .= "  " . $error->message;
				}
				$error = new AError ( $err );
				$error->toLog ()->toDebug ();
			} else {
				$this->_processXML ( $xml_obj );
			}
		}
	}

	/**
	 * @param SimpleXmlElement $xml_obj
	 * @return null
	 */
	private function _processXML($xml_obj) {
		$forms = $xml_obj->xpath ( '/forms' );
		//process each layout 
		foreach ( $forms as $form ) {
			$form = $form->form;
			/* Determin an action tag in all patent elements. Action can be insert, update and delete 
		       Default action (if not provided) is update
		       ->>> action = insert 
					Before loading the layout, determin if same layout exists with same name, template and type comdination. 
					If does exists, return and log error 
		       ->>> action = update (default) 
					Before loading the layout, determin if same layout exists with same name, template and type comdination. 
					If does exists, write new settings over existing
		       ->>> action = delete 
					Delete the element provided from databse and delete relationships to other elements linked to currnet one
					
				NOTE: Parent level delete action is cascaded to all childer elements 
				
				TODO: Need to use transaction sql here to prevent partual load or partual delete in case of error
			*/
			
			//check if form with same name exists
			$this->__construct ( $form->form_name );
			
			if (! $this->form_id && in_array ( $form->action, array ("", null, "update" ) )) {
				$form->action = 'insert';
			}
			
			$form->status = strtolower ( $form->status ) == 'active' ? 1 : 0;
			
			if ($form->action == "delete") {
				if ($this->form_id) {
					$sql = array ();
					$sql [] = "DELETE FROM " . $this->db->table("field_values") . " WHERE field_id IN ( SELECT field_id FROM " . $this->db->table("fields") . " WHERE form_id = '" . $this->form_id . "')";
					$sql [] = "DELETE FROM " . $this->db->table("field_descriptions") . " WHERE field_id IN ( SELECT field_id FROM " . $this->db->table("fields") . " WHERE form_id = '" . $this->form_id . "')";
					$sql [] = "DELETE FROM " . $this->db->table("fields_group_descriptions") . " WHERE group_id IN ( SELECT group_id FROM " . $this->db->table("form_groups") . " WHERE form_id = '" . $this->form_id . "')";
					$sql [] = "DELETE FROM " . $this->db->table("fields_groups") . " WHERE group_id IN ( SELECT group_id FROM " . $this->db->table("form_groups") . " WHERE form_id = '" . $this->form_id . "')";
					$sql [] = "DELETE FROM " . $this->db->table("form_groups") . " WHERE form_id  = '" . $this->form_id . "'";
					$sql [] = "DELETE FROM " . $this->db->table("fields") . " WHERE form_id = '" . $this->form_id . "'";
					$sql [] = "DELETE FROM " . $this->db->table("pages_forms") . " WHERE form_id = '" . $this->form_id . "'";
					$sql [] = "DELETE FROM " . $this->db->table("form_descriptions") . " WHERE form_id = '" . $this->form_id . "'";
					$sql [] = "DELETE FROM " . $this->db->table("forms") . " WHERE form_id = '" . $this->form_id . "'";
					
					foreach ( $sql as $query ) {
						$this->db->query ( $query );
					}
				}
				continue; // well done
			

			} elseif ($form->action == 'insert') {
				// if form exists
				if ($this->form_id) {
					$errmessage = 'Error: cannot insert form (name: "' . $form->form_name . '") because it already exists in database.';
					$error = new AError ( $errmessage );
					$error->toLog ()->toDebug ();
					$this->errors = 1;
					continue;
				}
				
				$query = "INSERT INTO " . $this->db->table("forms") . " (`form_name`, `controller`, `success_page`, `status`) 
							VALUES ('" . $this->db->escape ( $form->form_name ) . "','" . $this->db->escape ( $form->controller ) . "','" . $this->db->escape ( $form->success_page ) . "','" . $this->db->escape ( $form->status ) . "')";
				$this->db->query ( $query );
				$this->form_id = $this->db->getLastId ();
				
				if ($form->form_descriptions->form_description) {
					foreach ( $form->form_descriptions->form_description as $form_description ) {
						$language_id = $this->_getLanguageIdByName ( $form_description->language );
						if (! $language_id) {
							$errmessage = 'Error: cannot insert form description because it language: "' . $form_description->language . '" is not exists in database.';
							$error = new AError ( $errmessage );
							$error->toLog ()->toDebug ();
							$this->errors = 1;
							continue 2;
						}
						$this->language->replaceDescriptions('form_descriptions',
														 array('form_id' => (int)$this->form_id),
														 array($language_id => array(
																					'description' => (string)$form_description->description
														 )) );
						/*
						$query = "INSERT INTO " . $this->db->table("form_descriptions") . " (form_id, language_id,description) 
									VALUES ('" . $this->form_id . "','" . $language_id . "','" . $this->db->escape ( $form_description->description ) . "')";
						$this->db->query ( $query );*/
					}
				}
				
				if ($form->fields->field) {
					foreach ( $form->fields->field as $field ) {
						$this->_processFieldXML ( $field );
					}
				}
				
				if ($form->fields->field_groups->field_group) {
					foreach ( $form->fields->field_groups->field_group as $field_group ) {
						$this->_processFieldGroupXML ( $field_group );
					}
				}
			} else { // update form info
				

				$query = "UPDATE " . $this->db->table("forms") . " 
							SET `form_name` = '" . $this->db->escape ( $form->form_name ) . "',
								 `controller`='" . $this->db->escape ( $form->controller ) . "',
								 `success_page` = '" . $this->db->escape ( $form->success_page ) . "',
								 `status` = '" . $this->db->escape ( $form->status ) . "'
						WHERE form_id = '" . $this->form_id . "'";
				$this->db->query ( $query );
				
				if ($form->form_descriptions->form_description) {
					foreach ( $form->form_descriptions->form_description as $form_description ) {
						$language_id = $this->_getLanguageIdByName ( $form_description->language );
						if (! $language_id) {
							$errmessage = 'Error: cannot update form description because it language: "' . $form_description->language . '" is not exists in database.';
							$error = new AError ( $errmessage );
							$error->toLog ()->toDebug ();
							$this->errors = 1;
							continue 2;
						}

						$this->language->replaceDescriptions('form_descriptions',
															 array('form_id' => (int)$this->form_id),
															 array($language_id => array(
																						'description' => (string)$form_description->description
															 )) );
					}
				}
				
				if ($form->fields->field) {
					foreach ( $form->fields->field as $field ) {
						$this->_processFieldXML ( $field );
					}
				}
				
				if ($form->fields->field_groups->field_group) {
					foreach ( $form->fields->field_groups->field_group as $field_group ) {
						$this->_processFieldGroupXML ( $field_group );
					}
				}
			}
		
		} //end of form manipulation
		return null;
	}
	
	private function _processFieldXML($field, $field_group_id = '', $field_group_sort_order = 0) {
		if (! $this->form_id) {
			return null;
		}
		$field_group_id = ( int ) $field_group_id;
		
		$field_id = $this->_getFieldIdByName ( $field->field_name );
		if ($field->action == "insert" && $field_id) {
			$errmessage = 'Error: cannot insert form field (name: "' . $field->field_name . '") because it exists.';
			$error = new AError ( $errmessage );
			$error->toLog ()->toDebug ();
			$this->errors = 1;
			return null;
		}
		
		if ($field->action == "delete") {
			if (! $field_id) {
				return null;
			}
			
			$sql = array ();
			$sql [] = "DELETE FROM " . $this->db->table("field_values") . " WHERE field_id = '" . $field_id . "'";
			$sql [] = "DELETE FROM " . $this->db->table("field_descriptions") . " WHERE field_id = '" . $field_id . "'";
			$sql [] = "DELETE FROM " . $this->db->table("fields_groups") . " WHERE field_id = '" . $field_id . "'";
			$sql [] = "DELETE FROM " . $this->db->table("fields") . " WHERE field_id = '" . $field_id . "'";
			foreach ( $sql as $query ) {
				$this->db->query ( $query );
			}
			return null;
		}
		// checks
		if (! in_array ( $field->element_type, $this->field_types )) {
			$errmessage = 'Error: cannot insert(update) form field because it element type: "' . $field->element_type . '" is unknown.';
			$error = new AError ( $errmessage );
			$error->toLog ()->toDebug ();
			$this->errors = 1;
			return null;
		}

		if (! $field_id) { // if new field
			$sql = array ();
			$query = "INSERT INTO " . $this->db->table("fields") . " (form_id, field_name, element_type, sort_order, attributes, required, status) 
						VALUES ('" . $this->form_id . "', 
								'" . $this->db->escape ( $field->field_name ) . "',
								'" . $this->db->escape ( $field->element_type ) . "',
								'" . ( int ) $field->sort_order . "',
								'" . $this->db->escape ( $field->attributes ) . "',
								'" . $this->db->escape ( $field->required ) . "',
								'" . $this->db->escape ( $field->status ) . "')";
			$this->db->query ( $query );
			$field_id = $this->db->getLastId ();
			
			if ($field_group_id) {
				$sql [] = "INSERT INTO " . $this->db->table("fields_groups") . " (field_id, group_id, sort_order) 
						VALUES ('" . $field_id . "', '" . $field_group_id . "',	'" . $field_group_sort_order . "')";
			
			}
			
			if ($field->field_descriptions->field_description) {
				foreach ( $field->field_descriptions->field_description as $field_description ) {
					$language_id = $this->_getLanguageIdByName ( $field_description->language );
					if (! $language_id) {
						$errmessage = 'Error: cannot insert field description because it language: "' . $field_description->language . '" is not exists in database.';
						$error = new AError ( $errmessage );
						$error->toLog ()->toDebug ();
						$this->errors = 1;
						continue;
					}
					$sql [] = "INSERT INTO " . $this->db->table("field_descriptions") . " (field_id,language_id,name,description) 
								VALUES ('" . $field_id . "',
										'" . $language_id . "',
										'" . $this->db->escape ( $field_description->name ) . "',
										'" . $this->db->escape ( $field_description->description ) . "')";
				}
			}
			
			if ($field->field_values->field_value) {
				foreach ( $field->field_values->field_value as $field_value ) {
					$language_id = $this->_getLanguageIdByName ( $field_value->language );
					if (! $language_id) {
						$errmessage = 'Error: cannot insert field values because it language: "' . $field_description->language . '" is not exists in database.';
						$error = new AError ( $errmessage );
						$error->toLog ()->toDebug ();
						$this->errors = 1;
						continue;
					}
					$sql [] = "INSERT INTO " . $this->db->table("field_values") . " (`field_id`, `opt_value`, `value`, `default`, `sort_order`, `language_id`) 
								VALUES ('" . $field_id . "',
										'" . $this->db->escape ( $field_value->opt_value ) . "',
										'" . $this->db->escape ( $field_value->value ) . "',
										'" . $this->db->escape ( $field_value->default ) . "',
										'" . ( int ) $field_value->sort_order . "',																				
										'" . $language_id . "')";
				}
			
			}
			
			foreach ( $sql as $query ) {
				$this->db->query ( $query );
			}
		
		} else { //if need to update field
			$sql = array ();
			$sql [] = "UPDATE " . $this->db->table("fields") . " SET    
						 		element_type = '" . $this->db->escape ( $field->element_type ) . "',
								sort_order = '" . ( int ) $field->sort_order . "',
								attributes = '" . $this->db->escape ( $field->attributes ) . "',
								required = '" . $this->db->escape ( $field->required ) . "',
								status = '" . $this->db->escape ( $field->status ) . "'
						WHERE form_id = '" . $this->form_id . "' and field_id = '" . $field_id . "'";
			
			if ($field_group_id) {
				// check is field in group
				$query = "SELECT field_id FROM " . $this->db->table("fields_groups") . " WHERE field_id = '" . $field_id . "'";
				$result = $this->db->query ( $query );
				$exists = $result->num_rows;
				
				if ($exists) {
					$sql [] = "UPDATE " . $this->db->table("fields_groups") . " SET group_id = '" . $field_group_id . "', sort_order = '" . $field_group_sort_order . "'
								WHERE field_id = '" . $field_id . "'";
				} else {
					$sql [] = "INSERT INTO " . $this->db->table("fields_groups") . " (field_id, group_id, sort_order) 
								VALUES ('" . $field_id . "', '" . $field_group_id . "',	'" . $field_group_sort_order . "')";
				}
			}
			
			if ($field->field_descriptions->field_description) {
				foreach ( $field->field_descriptions->field_description as $field_description ) {
					$language_id = $this->_getLanguageIdByName ( $field_description->language );
					if (! $language_id) {
						$errmessage = 'Error: cannot update field description because it language: "' . $field_description->language . '" is not exists in database.';
						$error = new AError ( $errmessage );
						$error->toLog ()->toDebug ();
						$this->errors = 1;
						continue;
					}
					
					$exists = $this->_getFieldDescription ( $field_id, $language_id );
					if (! $exists) {
						$sql [] = "INSERT INTO " . $this->db->table("field_descriptions") . " (field_id, language_id, name, description) 
									VALUES ('" . $field_id . "',
											'" . $language_id . "',
											'" . $this->db->escape ( $field_description->name ) . "',
											'" . $this->db->escape ( $field_description->description ) . "')";
					} else {
						$sql [] = "UPDATE " . $this->db->table("field_descriptions") . " 
										SET name = '" . $this->db->escape ( $field_description->name ) . "',
											description = '" . $this->db->escape ( $field_description->description ) . "'
										WHERE language_id = '" . $language_id . "'AND field_id = '" . $field_id . "'";
					}
				}
			}
			
			if ($field->field_values->field_value) {
				$sql [] = "DELETE FROM " . $this->db->table("field_values") . " WHERE field_id = '" . $field_id . "'";
				foreach ( $field->field_values->field_value as $field_value ) {
					$language_id = $this->_getLanguageIdByName ( $field_value->language );
					if (! $language_id) {
						$errmessage = 'Error: cannot update field values because it language: "' . $field_description->language . '" is not exists in database.';
						$error = new AError ( $errmessage );
						$error->toLog ()->toDebug ();
						$this->errors = 1;
						continue;
					}
					$sql [] = "INSERT INTO " . $this->db->table("field_values") . " (`field_id`, `opt_value`, `value`, `default`, `sort_order`, `language_id`) 
								VALUES ('" . $field_id . "',
										'" . $this->db->escape ( $field_value->opt_value ) . "',
										'" . $this->db->escape ( $field_value->value ) . "',
										'" . $this->db->escape ( $field_value->default ) . "',
										'" . ( int ) $field_value->sort_order . "',																				
										'" . $language_id . "')";
				}
			
			}
			
			foreach ( $sql as $query ) {
				$this->db->query ( $query );
			}
		}
	
	}
	
	private function _processFieldGroupXML($field_group) {
		
		// get group_id
		$field_group_id = $this->_getFieldGroupIdByName ( $field_group->name );
		if (! $field_group_id && in_array ( $field_group->action, array ("", null, "update" ) )) {
			$field_group->action = 'insert';
		}
		
		if ($field_group->action == 'delete') {
			if ($field_group_id) {
				$sql = array ();
				$sql [] = "DELETE FROM " . $this->db->table("fields_group_descriptions") . " WHERE group_id  = '" . $field_group_id . "'";
				$sql [] = "DELETE FROM " . $this->db->table("fields") . " WHERE field_id IN ( SELECT field_id FROM " . $this->db->table("fields_groups") . " WHERE group_id = '" . $field_group_id . "')";
				$sql [] = "DELETE FROM " . $this->db->table("fields_groups") . " WHERE group_id = '" . $field_group_id . "'";
				$sql [] = "DELETE FROM " . $this->db->table("form_groups") . " WHERE form_id  = '" . $this->form_id . "'";
				foreach ( $sql as $query ) {
					$this->db->query ( $query );
				}
			} else {
				$errmessage = 'Error: cannot delete field group because it is not exists in database.';
				$error = new AError ( $errmessage );
				$error->toLog ()->toDebug ();
				$this->errors = 1;
			}
			return null;
		}
		
		if ($field_group->action == 'insert' && $field_group_id) {
			$errmessage = 'Error: cannot insert field group because it is already exists.';
			$error = new AError ( $errmessage );
			$error->toLog ()->toDebug ();
			$this->errors = 1;
			return null;
		}
		
		if ($field_group->action == 'insert') {
			$query = "INSERT INTO " . $this->db->table("form_groups") . " (`form_id`, `group_name`, `sort_order`, `status`)
					    VALUES ('" . $this->form_id . "',
					  			'" . $this->db->escape ( $field_group->name ) . "',
					  			'" . ( int ) $field_group->sort_order . "',
					  			'" . ( int ) $field_group->status . "')";
			$this->db->query ( $query );
			$field_group_id = $this->db->getLastId ();
		} else {
			$query = "UPDATE " . $this->db->table("form_groups") . " 
						SET `sort_order`='" . ( int ) $field_group->sort_order . "',
							`status`='" . ( int ) $field_group->status . "'
							WHERE group_id = '" . ( int ) $field_group_id . "'";
			$this->db->query ( $query );
		}
		
		// process group description
		if ($field_group->field_group_descriptions->field_group_description) {
			foreach ( $field_group->field_group_descriptions->field_group_description as $field_group_description ) {
				$sql = array ();
				$language_id = $this->_getLanguageIdByName ( $field_group_description->language );
				if (! $language_id) {
					$errmessage = 'Error: cannot update field group description because it language: "' . $field_group_description->language . '" is not exists in database.';
					$error = new AError ( $errmessage );
					$error->toLog ()->toDebug ();
					$this->errors = 1;
					continue;
				}
				// TODO need to check in the future what way is correct: with replaceDescription or direct insert
				//$exists = $this->_getFieldGroupDescription ( $field_group_id, $language_id );
				$this->language->replaceDescriptions('fields_group_descriptions',
								array('group_id' => (int)$field_group_id),
								array($language_id=>array(
										'name'=>$field_group_description->name,
										'description'=>$field_group_description->description)
								)
				);
				/*if (! $exists) {
					$sql [] = "INSERT INTO " . $this->db->table("fields_group_descriptions") . " (group_id, language_id, name, description) 
							VALUES ('" . $field_group_id . "',
									'" . $language_id . "',
									'" . $this->db->escape ( $field_group_description->name ) . "',
									'" . $this->db->escape ( $field_group_description->description ) . "')";
				} else {
					$sql [] = "UPDATE " . $this->db->table("fields_group_descriptions") . " 
								SET name = '" . $this->db->escape ( $field_group_description->name ) . "',
									description = '" . $this->db->escape ( $field_group_description->description ) . "'
								WHERE language_id = '" . $language_id . "'AND group_id = '" . $field_group_id . "'";
				}*/
			}
			foreach ( $sql as $query ) {
				$this->db->query ( $query );
			}
		}
		
		//then process fields in that group
		

		if ($field_group->fields->field) {
			foreach ( $field_group->fields->field as $field ) {
				$this->_processFieldXML ( $field, $field_group_id, $field_group->sort_order );
			}
		}
	
	}

}