<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * @property ALanguageManager $language
 * @property ADB $db
 */
class AFormManager
{
    public $errors = 0; // errors during process
    protected $registry;
    protected $db;
    protected $form_id;

    // array for check field element type
    public static $fieldTypes = ['I', 'T', 'C', 'H', 'S', 'M', 'R', 'G'];

    public function __construct(string $formName = '')
    {
        if (!IS_ADMIN) { // forbid for non admin calls
            throw new AException (AC_ERR_LOAD, 'Error: permission denied to change forms');
        }

        $this->registry = Registry::getInstance();
        $this->db = $this->registry->get('db');

        //check if form with same name exists
        $sql = "SELECT form_id 
                FROM " . $this->db->table("forms") . " 
                WHERE form_name='" . $this->db->escape($formName) . "'
                LIMIT 1";
        $result = $this->db->query($sql);
        $this->form_id = (int)$result->row ['form_id'];
    }

    /**
     * @param string $groupName
     * @return int|void
     * @throws AException
     */
    protected function _getFieldGroupIdByName(string $groupName = '')
    {
        if (!$groupName || !$this->form_id) {
            return;
        }
        $sql = "SELECT group_id 
                FROM " . $this->db->table("form_groups") . " 
                WHERE group_name = '" . $this->db->escape($groupName) . "' 
                    AND form_id = '" . $this->form_id . "'";
        $result = $this->db->query($sql);
        return (int)$result->row ['group_id'];
    }

    /**
     * @param string $fieldName
     * @return false|int
     * @throws AException
     */
    protected function getFieldIdByName(string $fieldName = '')
    {
        if (!$fieldName || !$this->form_id) {
            return false;
        }
        $sql = "SELECT field_id 
                FROM " . $this->db->table("fields") . " 
                WHERE field_name = '" . $this->db->escape($fieldName) . "' 
                    AND form_id= '" . $this->form_id . "'";
        $result = $this->db->query($sql);
        return (int)$result->row['field_id'];
    }

    /**
     * @param int $field_id
     * @param int $language_id
     * @return array|false
     * @throws AException
     */
    protected function getFieldDescription(int $field_id, int $language_id)
    {
        if (!$field_id || !$this->form_id || !$language_id) {
            return false;
        }
        $sql = "SELECT * 
                FROM " . $this->db->table("field_descriptions") . " 
                WHERE field_id = '" . $field_id . "' AND language_id = '" . $language_id . "'";
        $result = $this->db->query($sql);
        return $result->row;
    }

    /**
     * @param int $language_id
     * @return array|false
     * @throws AException
     */
    protected function getFormDescription(int $language_id)
    {
        if (!$this->form_id || !$language_id) {
            return false;
        }
        $sql = "SELECT * 
                FROM " . $this->db->table("form_descriptions") . " 
                WHERE form_id = '" . $this->form_id . "' AND language_id = '" . $language_id . "'";
        $result = $this->db->query($sql);
        return $result->row;
    }

    /**
     * @param int $field_id
     * @param int $language_id
     *
     * @return array|false
     * @throws AException
     */
    protected function getFieldValues(int $field_id, int $language_id)
    {
        if (!$field_id || !$this->form_id || !$language_id) {
            return false;
        }

        $sql = "SELECT *
				FROM " . $this->db->table("field_values") . "
				WHERE field_id = '" . ( int )$field_id . "' AND language_id = '" . $language_id . "'";
        $result = $this->db->query($sql);
        return $result->row;
    }

    /**
     * @param string $languageName
     * @return false|int
     * @throws AException
     */
    protected function getLanguageIdByName(string $languageName = '')
    {
        $languageName = mb_strtolower($languageName);
        if (!$languageName) {
            return false;
        }
        $query = "SELECT language_id
				  FROM " . $this->db->table("languages") . "
				  WHERE LOWER(name) = '" . $this->db->escape($languageName) . "'";
        $result = $this->db->query($query);
        return (int)$result->row['language_id'] ?: false;
    }

    public function loadXML(array $data)
    {
        // Input possible with XML string, File or both.
        // We process both one at a time. XML string processed first		
        if ($data['xml']) {
            /** @var $xml_obj SimpleXmlElement */
            $xml_obj = simplexml_load_string($data['xml']);
            if (!$xml_obj) {
                $err = "Failed loading XML data string";
                foreach (libxml_get_errors() as $error) {
                    $err .= "  " . $error->message;
                }
                $error = new AError ($err);
                $error->toLog()->toDebug();
            } else {
                $this->processXML($xml_obj);
            }
        }

        if (isset($data['file']) && is_file($data['file'])) {
            $xml_obj = simplexml_load_file($data['file']);
            if (!$xml_obj) {
                $err = "Failed loading XML file " . $data ['file'];
                foreach (libxml_get_errors() as $error) {
                    $err .= "  " . $error->message;
                }
                $error = new AError ($err);
                $error->toLog()->toDebug();
            } else {
                $this->processXML($xml_obj);
            }
        }
    }

    /**
     * @param SimpleXmlElement $xml_obj
     *
     * @throws AException
     */
    protected function processXML($xml_obj)
    {
        $forms = $xml_obj->xpath('/forms');
        //process each layout 
        foreach ($forms as $form) {
            /**
             * @var SimpleXMLElement|DOMNode|stdClass $form
             */
            $form = $form->form;
            /* Determine an action tag in all patent elements. Action can be insert, update and delete
               Default action (if not provided) is update
               ->>> action = insert 
                    Before loading the layout, determine if same layout exists with same name, template and type comdination.
                    If does exists, return and log error 
               ->>> action = update (default) 
                    Before loading the layout, determine if same layout exists with same name, template and type comdination.
                    If does exists, write new settings over existing
               ->>> action = delete 
                    Delete the element provided from database and delete relationships to other elements linked to currnet one
                    
                NOTE: Parent level delete action is cascaded to all children elements
                
                TODO: Need to use transaction sql here to prevent partial load or partial delete in case of error
            */

            //check if form with same name exists
            $this->__construct($form->form_name);

            if (!$this->form_id && in_array($form->action, ["", null, "update"])) {
                $form->action = 'insert';
            }

            $form->status = strtolower($form->status) == 'active' ? 1 : 0;

            if ($form->action == "delete") {
                if ($this->form_id) {
                    $sql = [
                        "DELETE FROM " . $this->db->table("field_values") . " 
                        WHERE field_id IN 
                            ( SELECT field_id 
                                FROM " . $this->db->table("fields") . " 
                                WHERE form_id = '" . $this->form_id . "')"
                    ];
                    $sql[] = "DELETE FROM " . $this->db->table("field_descriptions") . " 
                                WHERE field_id IN 
                                    ( SELECT field_id 
                                      FROM " . $this->db->table("fields") . " 
                                      WHERE form_id = '" . $this->form_id . "')";
                    $sql[] = "DELETE FROM " . $this->db->table("field_group_descriptions") . " 
                                WHERE group_id IN 
                                    ( SELECT group_id 
                                        FROM " . $this->db->table("form_groups") . " 
                                        WHERE form_id = '" . $this->form_id . "')";
                    $sql[] = "DELETE FROM " . $this->db->table("field_groups") . " 
                                WHERE group_id IN 
                                    ( SELECT group_id 
                                        FROM " . $this->db->table("form_groups") . " 
                                        WHERE form_id = '" . $this->form_id . "')";
                    $sql[] = "DELETE FROM " . $this->db->table("form_groups") . " WHERE form_id  = '" . $this->form_id . "'";
                    $sql[] = "DELETE FROM " . $this->db->table("fields") . " WHERE form_id = '" . $this->form_id . "'";
                    $sql[] = "DELETE FROM " . $this->db->table("pages_forms") . " WHERE form_id = '" . $this->form_id . "'";
                    $sql[] = "DELETE FROM " . $this->db->table("form_descriptions") . " WHERE form_id = '" . $this->form_id . "'";
                    $sql[] = "DELETE FROM " . $this->db->table("forms") . " WHERE form_id = '" . $this->form_id . "'";

                    foreach ($sql as $query) {
                        $this->db->query($query);
                    }
                }
            } elseif ($form->action == 'insert') {
                // if form exists
                if ($this->form_id) {
                    $error_text = 'Error: cannot insert form (name: "' . $form->form_name . '") because it already exists in database.';
                    $error = new AError ($error_text);
                    $error->toLog()->toDebug();
                    $this->errors = 1;
                    continue;
                }

                $query = "INSERT INTO " . $this->db->table("forms") . " (`form_name`, `controller`, `success_page`, `status`) 
							VALUES (
							'" . $this->db->escape($form->form_name) . "',
							'" . $this->db->escape($form->controller) . "',
							'" . $this->db->escape($form->success_page) . "',
							'" . $this->db->escape($form->status) . "')";
                $this->db->query($query);
                $this->form_id = (int)$this->db->getLastId();

                if ($form->form_descriptions->form_description) {
                    foreach ($form->form_descriptions->form_description as $form_description) {
                        $languageId = $this->getLanguageIdByName((string)$form_description->language);
                        if (!$languageId) {
                            $error_text = 'Error: cannot insert form description because it language: "'
                                . $form_description->language . '" is not exists in database.';
                            $error = new AError ($error_text);
                            $error->toLog()->toDebug();
                            $this->errors = 1;
                            continue 2;
                        }
                        $this->language->replaceDescriptions(
                            'form_descriptions',
                            ['form_id' => (int)$this->form_id],
                            [
                                $languageId => [
                                    'description' => (string)$form_description->description,
                                ],
                            ]
                        );
                    }
                }

                if ($form->fields->field) {
                    foreach ($form->fields->field as $field) {
                        $this->processFieldXML($field);
                    }
                }

                if ($form->fields->field_groups->field_group) {
                    foreach ($form->fields->field_groups->field_group as $field_group) {
                        $this->_processFieldGroupXML($field_group);
                    }
                }
            } // update form info
            else {
                $query = "UPDATE " . $this->db->table("forms") . "
							SET `form_name` = '" . $this->db->escape($form->form_name) . "',
								 `controller`='" . $this->db->escape($form->controller) . "',
								 `success_page` = '" . $this->db->escape($form->success_page) . "',
								 `status` = '" . $this->db->escape($form->status) . "'
						WHERE form_id = '" . $this->form_id . "'";
                $this->db->query($query);

                if ($form->form_descriptions->form_description) {
                    foreach ($form->form_descriptions->form_description as $form_description) {
                        $languageId = $this->getLanguageIdByName((string)$form_description->language);
                        if (!$languageId) {
                            $error_text = 'Error: cannot update form description because it language: "'
                                . $form_description->language . '" is not exists in database.';
                            $error = new AError ($error_text);
                            $error->toLog()->toDebug();
                            $this->errors = 1;
                            continue 2;
                        }

                        $this->language->replaceDescriptions(
                            'form_descriptions',
                            ['form_id' => (int)$this->form_id],
                            [
                                $languageId => [
                                    'description' => (string)$form_description->description,
                                ],
                            ]
                        );
                    }
                }

                if ($form->fields->field) {
                    foreach ($form->fields->field as $field) {
                        $this->processFieldXML($field);
                    }
                }

                if ($form->fields->field_groups->field_group) {
                    foreach ($form->fields->field_groups->field_group as $field_group) {
                        $this->_processFieldGroupXML($field_group);
                    }
                }
            }

        } //end of form manipulation
        return null;
    }

    /**
     * @param stdClass $field
     * @param int $fieldGroupId
     * @param int $fieldGroupSortOrder
     * @return bool
     * @throws AException
     */
    protected function processFieldXML($field, $fieldGroupId = 0, $fieldGroupSortOrder = 0)
    {
        if (!$this->form_id) {
            return false;
        }
        $fieldGroupId = ( int )$fieldGroupId;

        $field_id = $this->getFieldIdByName((string)$field->field_name);
        if ($field->action == "insert" && $field_id) {
            $error_text = 'Error: cannot insert form field (name: "' . $field->field_name . '") because it exists.';
            $error = new AError ($error_text);
            $error->toLog()->toDebug();
            $this->errors = 1;
            return false;
        }

        if ($field->action == "delete") {
            if (!$field_id) {
                return false;
            }

            $sql = [];
            $sql[] = "DELETE FROM " . $this->db->table("field_values") . " WHERE field_id = '" . $field_id . "'";
            $sql[] = "DELETE FROM " . $this->db->table("field_descriptions") . " WHERE field_id = '" . $field_id . "'";
            $sql[] = "DELETE FROM " . $this->db->table("field_groups") . " WHERE field_id = '" . $field_id . "'";
            $sql[] = "DELETE FROM " . $this->db->table("fields") . " WHERE field_id = '" . $field_id . "'";
            foreach ($sql as $query) {
                $this->db->query($query);
            }
            return false;
        }
        // checks
        if (!in_array($field->element_type, static::$fieldTypes)) {
            $error_text = 'Error: cannot insert(update) form field because it element type: "' . $field->element_type . '" is unknown.';
            $error = new AError ($error_text);
            $error->toLog()->toDebug();
            $this->errors = 1;
            return false;
        }

        $sql = [];
        // if new field
        if (!$field_id) {
            $query = "INSERT INTO " . $this->db->table("fields") . " (form_id, field_name, element_type, sort_order, attributes, required, status) 
						VALUES ('" . $this->form_id . "', 
								'" . $this->db->escape($field->field_name) . "',
								'" . $this->db->escape($field->element_type) . "',
								'" . ( int )$field->sort_order . "',
								'" . $this->db->escape($field->attributes) . "',
								'" . $this->db->escape($field->required) . "',
								'" . $this->db->escape($field->status) . "')";
            $this->db->query($query);
            $field_id = $this->db->getLastId();

            if ($fieldGroupId) {
                $sql[] = "INSERT INTO " . $this->db->table("field_groups") . " (field_id, group_id, sort_order) 
						   VALUES ('" . $field_id . "', '" . $fieldGroupId . "',	'" . $fieldGroupSortOrder . "')";
            }
            if ($field->field_descriptions->field_description) {
                foreach ($field->field_descriptions->field_description as $field_description) {
                    $language_id = $this->getLanguageIdByName((string)$field_description->language);
                    if (!$language_id) {
                        $error_text = 'Error: cannot insert field description because it language: "' . $field_description->language . '" is not exists in database.';
                        $error = new AError ($error_text);
                        $error->toLog()->toDebug();
                        $this->errors = 1;
                        continue;
                    }
                    $sql[] = "INSERT INTO " . $this->db->table("field_descriptions") . " (field_id,language_id,name,description) 
								VALUES ('" . $field_id . "',
										'" . $language_id . "',
										'" . $this->db->escape($field_description->name) . "',
										'" . $this->db->escape($field_description->description) . "')";
                }
            }

            if ($field->field_values->field_value) {
                foreach ($field->field_values->field_value as $field_value) {
                    $language_id = $this->getLanguageIdByName((string)$field_value->language);
                    if (!$language_id) {
                        $error_text = 'Error: cannot insert field values because it language: "' . $field_value->language . '" is not exists in database.';
                        $error = new AError ($error_text);
                        $error->toLog()->toDebug();
                        $this->errors = 1;
                        continue;
                    }
                    $sql[] = "INSERT INTO " . $this->db->table("field_values") . " (`field_id`, `opt_value`, `value`, `default`, `sort_order`, `language_id`) 
								VALUES ('" . $field_id . "',
										'" . $this->db->escape($field_value->opt_value) . "',
										'" . $this->db->escape($field_value->value) . "',
										'" . $this->db->escape($field_value->default) . "',
										'" . ( int )$field_value->sort_order . "',
										'" . $language_id . "')";
                }
            }
        } //update field
        else {
            $sql[] = "UPDATE " . $this->db->table("fields") . " SET    
						 		element_type = '" . $this->db->escape($field->element_type) . "',
								sort_order = '" . ( int )$field->sort_order . "',
								attributes = '" . $this->db->escape($field->attributes) . "',
								required = '" . $this->db->escape($field->required) . "',
								status = '" . $this->db->escape($field->status) . "'
						WHERE form_id = '" . $this->form_id . "' and field_id = '" . $field_id . "'";

            if ($fieldGroupId) {
                // check is field in group
                $query = "SELECT field_id FROM " . $this->db->table("field_groups") . " WHERE field_id = '" . $field_id . "'";
                $result = $this->db->query($query);
                $exists = $result->num_rows;
                if ($exists) {
                    $sql[] = "UPDATE " . $this->db->table("field_groups") . " 
                                SET group_id = '" . $fieldGroupId . "', sort_order = '" . $fieldGroupSortOrder . "'
								WHERE field_id = '" . $field_id . "'";
                } else {
                    $sql[] = "INSERT INTO " . $this->db->table("field_groups") . " (field_id, group_id, sort_order) 
								VALUES ('" . $field_id . "', '" . $fieldGroupId . "',	'" . $fieldGroupSortOrder . "')";
                }
            }

            if ($field->field_descriptions->field_description) {
                foreach ($field->field_descriptions->field_description as $field_description) {
                    $language_id = $this->getLanguageIdByName((string)$field_description->language);
                    if (!$language_id) {
                        $error_text = 'Error: cannot update field description because it language: "'
                            . $field_description->language . '" is not exists in database.';
                        $error = new AError ($error_text);
                        $error->toLog()->toDebug();
                        $this->errors = 1;
                        continue;
                    }

                    $exists = $this->getFieldDescription((int)$field_id, (int)$language_id);
                    if (!$exists) {
                        $sql[] = "INSERT INTO " . $this->db->table("field_descriptions") . " 
                                    (field_id, language_id, name, description) 
									VALUES ('" . $field_id . "',
											'" . $language_id . "',
											'" . $this->db->escape($field_description->name) . "',
											'" . $this->db->escape($field_description->description) . "')";
                    } else {
                        $sql[] = "UPDATE " . $this->db->table("field_descriptions") . " 
                                    SET name = '" . $this->db->escape($field_description->name) . "',
                                        description = '" . $this->db->escape($field_description->description) . "'
                                    WHERE language_id = '" . $language_id . "'AND field_id = '" . $field_id . "'";
                    }
                }
            }

            if ($field->field_values->field_value) {
                $sql[] = "DELETE FROM " . $this->db->table("field_values") . " WHERE field_id = '" . $field_id . "'";
                foreach ($field->field_values->field_value as $field_value) {
                    $language_id = $this->getLanguageIdByName((string)$field_value->language);
                    if (!$language_id) {
                        $error_text = 'Error: cannot update field values because it language: "' .
                            $field_value->language . '" is not exists in database.';
                        $error = new AError ($error_text);
                        $error->toLog()->toDebug();
                        $this->errors = 1;
                        continue;
                    }
                    $sql[] = "INSERT INTO " . $this->db->table("field_values") . " 
                                (`field_id`, `opt_value`, `value`, `default`, `sort_order`, `language_id`) 
								VALUES ('" . $field_id . "',
										'" . $this->db->escape($field_value->opt_value) . "',
										'" . $this->db->escape($field_value->value) . "',
										'" . $this->db->escape($field_value->default) . "',
										'" . ( int )$field_value->sort_order . "',
										'" . $language_id . "')";
                }
            }
        }
        foreach ($sql as $query) {
            $this->db->query($query);
        }
        return true;
    }

    /**
     * @param stdClass $fieldGroup
     * @return bool
     * @throws AException
     */
    protected function _processFieldGroupXML($fieldGroup)
    {
        // get group_id
        $fieldGroupId = $this->_getFieldGroupIdByName($fieldGroup->name);
        if (!$fieldGroupId && in_array($fieldGroup->action, ["", null, "update"])) {
            $fieldGroup->action = 'insert';
        }

        if ($fieldGroup->action == 'delete') {
            if ($fieldGroupId) {
                $sql = [];
                $sql[] = "DELETE FROM " . $this->db->table("field_group_descriptions") . " 
                            WHERE group_id  = '" . $fieldGroupId . "'";
                $sql[] = "DELETE FROM " . $this->db->table("fields") . " 
                            WHERE field_id IN 
                                ( SELECT field_id 
                                    FROM " . $this->db->table("field_groups") . " 
                                    WHERE group_id = '" . $fieldGroupId . "')";
                $sql[] = "DELETE FROM " . $this->db->table("field_groups") . " WHERE group_id = '" . $fieldGroupId . "'";
                $sql[] = "DELETE FROM " . $this->db->table("form_groups") . " WHERE form_id  = '" . $this->form_id . "'";
                foreach ($sql as $query) {
                    $this->db->query($query);
                }
            } else {
                $error_text = 'Error: cannot delete field group because it is not exists in database.';
                $error = new AError ($error_text);
                $error->toLog()->toDebug();
                $this->errors = 1;
            }
            return false;
        }

        if ($fieldGroup->action == 'insert' && $fieldGroupId) {
            $error_text = 'Error: cannot insert field group because it is already exists.';
            $error = new AError ($error_text);
            $error->toLog()->toDebug();
            $this->errors = 1;
            return false;
        }

        if ($fieldGroup->action == 'insert') {
            $query = "INSERT INTO " . $this->db->table("form_groups") . " (`form_id`, `group_name`, `sort_order`, `status`)
					    VALUES ('" . $this->form_id . "',
					  			'" . $this->db->escape($fieldGroup->name) . "',
					  			'" . ( int )$fieldGroup->sort_order . "',
					  			'" . ( int )$fieldGroup->status . "')";
            $this->db->query($query);
            $fieldGroupId = $this->db->getLastId();
        } else {
            $query = "UPDATE " . $this->db->table("form_groups") . " 
						SET `sort_order`='" . ( int )$fieldGroup->sort_order . "',
							`status`='" . ( int )$fieldGroup->status . "'
							WHERE group_id = '" . ( int )$fieldGroupId . "'";
            $this->db->query($query);
        }

        // process group description
        if ($fieldGroup->field_group_descriptions->field_group_description) {
            foreach ($fieldGroup->field_group_descriptions->field_group_description as $field_group_description) {
                $language_id = $this->getLanguageIdByName((string)$field_group_description->language);
                if (!$language_id) {
                    $error_text = 'Error: cannot update field group description because it language: "' . $field_group_description->language . '" is not exists in database.';
                    $error = new AError ($error_text);
                    $error->toLog()->toDebug();
                    $this->errors = 1;
                    continue;
                }

                $this->language->replaceDescriptions('field_group_descriptions',
                    ['group_id' => (int)$fieldGroupId],
                    [
                        $language_id => [
                            'name'        => $field_group_description->name,
                            'description' => $field_group_description->description,
                        ],
                    ]
                );
            }
        }

        //then process fields in that group
        if ($fieldGroup->fields->field) {
            foreach ($fieldGroup->fields->field as $field) {
                $this->processFieldXML($field, $fieldGroupId, $fieldGroup->sort_order);
            }
        }
        return true;
    }
}
