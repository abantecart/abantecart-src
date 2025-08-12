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

class ModelToolFormsManager extends Model
{

    public $error = [];

    /**
     * @param int $formId
     * @return array
     * @throws AException
     */
    public function getFormById(int $formId)
    {
        if (!$formId) {
            return [];
        }
        $sql = "SELECT f.*, fd.language_id, fd.description
                FROM " . $this->db->table('forms') . " f
                LEFT JOIN " . $this->db->table('form_descriptions') . " fd
                    ON f.form_id = fd.form_id
                WHERE f.form_id = " . (int)$formId . "
                    AND fd.language_id = " . (int)$this->language->getContentLanguageID();
        $results = $this->db->query($sql);
        return $results->row;
    }

    /**
     * @param array $data
     * @return array|false|mixed
     * @throws AException
     */
    public function getForms(array $data = [])
    {
        $language_id = (int)$data['language_id'] ?: $this->language->getContentLanguageID();

        if ($data) {
            $filter = $data['filter'] ?? [];
            $sql = "SELECT " . $this->db->getSqlCalcTotalRows() . " *  
                    FROM " . $this->db->table("forms") . " f 
                    LEFT JOIN " . $this->db->table("form_descriptions") . " fd 
                        ON (f.form_id = fd.form_id)
                    WHERE fd.language_id = '" . $language_id . "'";
            if ($data['subsql_filter']) {
                $sql .= " AND " . $data['subsql_filter'];
            }
            $match = '';
            if ($filter['match']) {
                $match = $filter['match'];
            }

            if ($filter['keyword']) {
                $keywords = explode(' ', $filter['keyword']);
                if ($match == 'any') {
                    $sql .= " AND (";
                    foreach ($keywords as $k => $keyword) {
                        $sql .= $k > 0 ? " OR" : "";
                        $sql .= " (fd.description LIKE '%" . $this->db->escape($keyword, true) . "%'";
                        $sql .= " OR f.form_name LIKE '%" . $this->db->escape($keyword, true) . "%')";
                    }
                    $sql .= " )";
                } else {
                    if ($match == 'all') {
                        $sql .= " AND (";
                        foreach ($keywords as $k => $keyword) {
                            $sql .= $k > 0 ? " AND" : "";
                            $sql .= " (fd.description LIKE '%" . $this->db->escape($keyword, true) . "%'";
                            $sql .= " OR f.form_name LIKE '%" . $this->db->escape($keyword, true) . "%')";
                        }
                        $sql .= " )";
                    } else {
                        if ($match == 'exact') {
                            $sql .= " AND (fd.description LIKE '%" . $this->db->escape($filter['keyword'], true) . "%'";
                            $sql .= " OR f.form_name LIKE '%" . $this->db->escape($filter['keyword'], true) . "%')";
                        }
                    }
                }
            }

            if (isset($filter['status'])) {
                $sql .= " AND f.status = '" . (int)$filter['status'] . "'";
            }

            $sort_data = [
                'name'        => 'f.form_name',
                'description' => 'fd.description',
                'status'      => 'f.status',
            ];

            if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
                $sql .= " ORDER BY " . $sort_data[$data['sort']];
            } else {
                $sql .= " ORDER BY f.form_name ";
            }

            $sql .= " " . (in_array($data['order'], ['ASC', 'DESC']) ? $data['order'] : "ASC");
            if (isset($data['start']) || isset($data['limit'])) {
                $data['start'] = max(0, (int)$data['start']);
                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }
                $sql .= " LIMIT " . $data['start'] . "," . (int)$data['limit'];
            }
            $result = $this->db->query($sql);
            if ($result->num_rows) {
                $result->rows[0]['total_num_rows'] = $this->db->getTotalNumRows();
            }
            return $result->rows;
        } else {
            $cache_key = 'extensions.forms_manager.lang_' . $language_id;
            $form_data = $this->cache->pull($cache_key);
            if ($form_data === false) {
                $result = $this->db->query(
                    "SELECT *
                    FROM " . $this->db->table("forms") . " f
                    LEFT JOIN " . $this->db->table("form_descriptions") . " fd 
                        ON (f.form_id = fd.form_id)
                    WHERE fd.language_id = '" . $language_id . "'
                    ORDER BY f.form_name ASC"
                );
                $form_data = $result->rows;
                $this->cache->push($cache_key, $form_data);
            }
            return $form_data;
        }
    }

    /**
     * @param array $data
     * @return false|int
     * @throws AException
     */
    public function addForm(array $data)
    {
        $data['form_name'] = str_replace(' ', '_', $this->db->escape($data['form_name']));
        if (!$this->getFormIdByName($data['form_name'])) {
            $data = $this->_validate($data);
            $sql = "INSERT INTO " . $this->db->table("forms") . "
                    SET
                        form_name = '" . $this->db->escape($data['form_name']) . "',
                        controller = '" . $this->db->escape($data['controller_path']) . "',
                        success_page = '" . $this->db->escape($data['success_page']) . "',
                        status = 1";
            $this->db->query($sql);
            $form_id = (int)$this->db->getLastId();
            $langs = $this->language->getAvailableLanguages();
            foreach ($langs as $lang) {
                $this->addFormDescription($form_id, (string)$data['form_description'], $lang['language_id']);
            }
            return $form_id;
        }
        return false;
    }

    /**
     * @param int $formId
     * @param string $description
     * @param int $languageId
     * @return bool
     * @throws AException
     */
    public function addFormDescription(int $formId, string $description, int $languageId)
    {
        if (!$formId || !$description || !$languageId) {
            return false;
        }
        $this->language->replaceDescriptions(
            'form_descriptions',
            [
                'form_id' => $formId
            ],
            [
                $languageId => [
                    'description' => $description
                ]
            ]
        );
        $this->_deleteCache();
        return true;
    }

    /**
     * @param int $formId
     * @return void
     * @throws AException
     */
    public function deleteForm(int $formId)
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table("forms") . "
			WHERE form_id = " . $formId
        );
        $this->deleteFormDescription($formId);
        $this->removeFields($formId);
        $this->_deleteCache();
    }

    /**
     * @param int $formId
     * @return void
     * @throws AException
     */
    public function deleteFormDescription(int $formId)
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table("form_descriptions") . " WHERE form_id = " . $formId
        );
    }

    /**
     * @param array $data
     * @return bool
     * @throws AException
     */
    public function updateForm(array $data)
    {
        if ($this->getFormNameById((int)$data['form_id']) !== false) {
            $cols = [];
            if (isset($data['form_name'])) {
                $data['form_name'] = str_replace(' ', '_', $this->db->escape($data['form_name']));
                $cols[] = 'form_name = "' . $data['form_name'] . '"';
            }

            if (isset($data['controller_path'])) {
                $cols[] = 'controller = "' . $this->db->escape($data['controller_path']) . '"';
                if (!$data['success_page']) {
                    $cols[] = 'success_page = "' . $this->db->escape($data['controller_path']) . '"';
                }
            }

            if (isset($data['success_page'])) {
                $cols[] = 'success_page = "' . $this->db->escape($data['success_page']) . '"';
            }

            if (isset($data['form_status'])) {
                $cols[] = 'status = "' . (int)$data['form_status'] . '"';
            }

            if ($cols) {
                $sql = "UPDATE " . $this->db->table("forms") . "
                        SET " . implode(',', $cols) . "
                        WHERE form_id = " . (int)$data['form_id'];
                $this->db->query($sql);
            }

            if (isset($data['form_description'])) {
                $this->updateFormDescription((int)$data['form_id'], (string)$data['form_description']);
            }
            $this->_deleteCache();
            return true;
        }
        return false;
    }

    /**
     * @param int $formId
     * @param string $description
     * @return bool
     * @throws AException
     */
    public function updateFormDescription(int $formId, string $description)
    {
        if (!$formId || !$description) {
            return false;
        }

        $this->language->updateDescriptions(
            'form_descriptions',
            [
                'form_id' => $formId
            ],
            [
                $this->language->getContentLanguageID() => [
                    'description' => $description
                ]
            ]
        );
        return true;
    }

    /**
     * @param array $data
     * @return bool
     * @throws AException
     */
    public function updateFormFieldData(array $data)
    {
        if (!(int)$data['field_id']) {
            return false;
        }

        $columns = [
            'field_name',
            'element_type',
            'sort_order',
            'required',
            'status',
            'settings',
            'regexp_pattern',
        ];

        if (has_value($data['field_name'])) {
            $data['field_name'] = str_replace(' ', '_', $this->db->escape($data['field_name']));
            if (!$data['field_name']) {
                return false;
            }
        }

        if (has_value($data['sort_order'])) {
            $data['sort_order'] = (int)$data['sort_order'];
        }

        if (has_value($data['required'])) {
            $data['required'] = ((int)$data['required']) ? 'Y' : 'N';
        }

        if (has_value($data['status'])) {
            $data['status'] = ((int)$data['status']) ? 1 : 0;
        }

        if ($data['regexp_pattern']) {
            $data['regexp_pattern'] = $this->db->escape($data['regexp_pattern']);
        }

        if (has_value($data['settings'])) {
            $data['settings'] = $this->db->escape(serialize($data['settings']));
        }
        $update = [];
        foreach ($columns as $colName) {
            if (has_value($data[$colName])) {
                $update[] = $colName . " = '" . $data[$colName] . "'";
            }
        }

        if ($update) {
            $sql = "UPDATE " . $this->db->table('fields') . "
					SET " . implode(",\n ", $update) . "
					WHERE field_id = '" . (int)$data['field_id'] . "' 
					    AND form_id = '" . (int)$data['form_id'] . "'";
            $this->db->query($sql);
        }
        $this->updateFieldDescription((int)$data['field_id'], $data);
        $this->_deleteCache();
        return true;
    }

    /**
     * @param int $fieldId
     * @param array $data
     * @return bool
     * @throws AException
     */
    public function updateFieldDescription(int $fieldId, array $data)
    {
        if (!$fieldId || !$data) {
            return false;
        }

        $columns = [
            'name'        => 'field_description',
            'description' => 'field_note',
            'error_text'  => 'error_text',
        ];

        $update = [];
        foreach ($columns as $alias => $colName) {
            if ($data[$colName]) {
                $update[$alias] = $data[$colName];
            }
        }

        if ($update) {
            $this->language->replaceDescriptions(
                'field_descriptions',
                ['field_id' => (int)$fieldId],
                [$this->language->getContentLanguageID() => $update]
            );
        }

        $this->_deleteCache();
        return true;
    }

    /**
     * @param array $data
     * @param int $languageId
     * @return bool
     * @throws AException
     */
    public function updateFieldValues(array $data, int $languageId)
    {
        if (!$languageId) {
            return false;
        }
        $values = [];
        if ((array)$data['field_value_id']) {
            foreach ((array)$data['field_value_id'] as $key => $status) {
                $value_data = [
                    'name'       => $data['name'][$key],
                    'sort_order' => $data['sort_order'][$key],
                ];
                //Check if new, delete or update
                if ($status == 'delete' && !str_contains($key, 'new')) {
                    continue;
                } else {
                    $values[] = $value_data;
                }
            }
        }

        if ($values) {
            $this->deleteFieldValue((int)$data['field_id'], $languageId);
            $this->addFieldValue((int)$data['field_id'], $values, $languageId);
        }

        $this->_deleteCache();
        return true;
    }

    /**
     * @param int $fieldId
     * @param array $data
     * @param int $languageId
     * @return bool
     * @throws AException
     */
    public function addFieldValue(int $fieldId, array $data, int $languageId)
    {
        if (!$languageId || !$fieldId || !$data) {
            return false;
        }
        $this->language->replaceDescriptionsSerialized(
            'field_values',
            ['field_id' => $fieldId],
            [$languageId => ['value' => serialize($data)]],
            ['value' => ['name']]
        );
        $this->_deleteCache();
        return true;
    }

    /**
     * @param int $fieldId
     * @param int $languageId
     * @return true
     * @throws AException
     */
    public function deleteFieldValue(int $fieldId, int $languageId)
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table("field_values") . "
			WHERE field_id = " . $fieldId . " AND language_id = " . $languageId
        );
        $this->_deleteCache();
        return true;
    }

    /**
     * @param string $name
     * @return false|int
     * @throws AException
     */
    public function getFormIdByName(string $name)
    {
        $results = $this->db->query(
            "SELECT form_id 
            FROM " . $this->db->table("forms") . "
			WHERE form_name = '" . $this->db->escape($name) . "'
			LIMIT 1"
        );
        return (int)$results->row['form_id'] ?: false;
    }

    /**
     * @param int $formId
     * @return false|mixed
     * @throws AException
     */
    public function getFormNameById(int $formId)
    {
        $results = $this->db->query(
            "SELECT form_name 
            FROM " . $this->db->table("forms") . "
			WHERE form_id = " . $formId . " 
			LIMIT 1"
        );
        return $results->row['form_name'] ?: false;
    }

    /**
     * @param int $formId
     * @param array $data
     * @return bool
     * @throws AException
     */
    public function addField(int $formId, array $data)
    {
        if (!$formId || !$data) {
            return false;
        }

        $data['field_name'] = str_replace(' ', '_', $this->db->escape($data['field_name']));
        if ($this->isFieldNameUnique($formId, $data['field_name'])) {
            $sql = "INSERT INTO " . $this->db->table('fields') . "
				SET
					form_id = " . (int)$formId . ",
					field_name = '" . $data['field_name'] . "',
					element_type = '" . $this->db->escape($data['element_type']) . "',
			";
            $sql .= " sort_order = " . (int)$data['sort_order'] . ", ";
            $sql .= " required = '" . ((int)$data['required'] ? 1 : 0) . "', ";
            $sql .= " status = " . (int)$data['status'];

            $this->db->query($sql);
            $field_id = (int)$this->db->getLastId();
            $this->addFieldDescription(
                $field_id,
                $data,
                $this->language->getContentLanguageID()
            );
            $this->_deleteCache();
            return true;
        }
        return false;
    }

    /**
     * @param int $fieldId
     * @param array $data
     * @param int $language_id
     * @return bool
     * @throws AException
     */
    public function addFieldDescription(int $fieldId, array $data, int $language_id)
    {
        if (!$fieldId || !$data) {
            return false;
        }
        $language_id = $language_id ?: $this->language->getContentLanguageID();
        $data['field_description'] = (string)$data['field_description'];
        $data['field_note'] = (string)$data['field_note'];

        $this->language->replaceDescriptions(
            'field_descriptions',
            ['field_id' => (int)$fieldId],
            [
                $language_id => [
                    'name'        => $data['field_description'],
                    'description' => $data['field_note'],
                ],
            ]
        );
        return true;
    }

    /**
     * @param int $formId
     * @param string $fieldName
     * @param int $fieldId
     * @return bool
     * @throws AException
     */
    public function isFieldNameUnique(int $formId, string $fieldName, int $fieldId = 0)
    {
        $result = $this->db->query(
            "SELECT field_id 
            FROM " . $this->db->table("fields") . "
			WHERE form_id = '" . $formId . "'
			    " . ($fieldId ? " AND field_id<>'" . $fieldId . "'" : '') . "
			AND field_name = '" . $this->db->escape($fieldName) . "'
			LIMIT 1"
        );
        return !$result->num_rows;
    }

    /**
     * @param int $formId
     * @return array
     * @throws AException
     */
    public function getFields(int $formId)
    {
        $fields = [];
        if (!$formId) {
            return [];
        }

        $query = $this->db->query("
            SELECT f.*, fd.name, fd.description
            FROM " . $this->db->table("fields") . " f
            LEFT JOIN " . $this->db->table("field_descriptions") . " fd
                ON ( f.field_id = fd.field_id 
                    AND fd.language_id = '" . (int)$this->language->getContentLanguageID() . "' )
            WHERE f.form_id = '" . $formId . "'
            ORDER BY f.sort_order"
        );

        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $fields[(int)$row['field_id']] = $row;
                $query = $this->db->query("
					SELECT *
					FROM " . $this->db->table("field_values") . "
					WHERE field_id = '" . (int)$row['field_id'] . "'
					AND language_id = '" . $this->language->getContentLanguageID() . "'"
                );
                if ($query->num_rows) {
                    $fields[$row['field_id']]['values'] = unserialize($query->row['value']);
                }
            }
        }
        return $fields;
    }

    /**
     * @param int $fieldId
     * @return array|false
     * @throws AException
     */
    public function getField(int $fieldId)
    {
        if (!$fieldId) {
            return false;
        }
        $field = [];

        $query = $this->db->query("
            SELECT f.*, fd.name, fd.description, fd.error_text
            FROM " . $this->db->table("fields") . " f
            LEFT JOIN " . $this->db->table("field_descriptions") . " fd 
                ON ( f.field_id = fd.field_id 
                    AND fd.language_id = '" . $this->language->getContentLanguageID() . "' )
            WHERE f.field_id = '" . (int)$fieldId . "'
            ORDER BY f.sort_order 
            LIMIT 1"
        );

        if ($query->row) {
            $field = $query->row;
            $field['settings'] = unserialize($field['settings']);
            $query = $this->db->query("
				SELECT *
				FROM " . $this->db->table("field_values") . "
				WHERE field_id = '" . (int)$query->row['field_id'] . "'
				AND language_id = '" . $this->language->getContentLanguageID() . "'
				LIMIT 1"
            );

            if ($query->num_rows) {
                $field['values'] = unserialize($query->row['value']);
            }
        }
        return $field;
    }

    /**
     * @param int $formId
     * @param int $fieldId
     * @return bool
     * @throws AException
     */
    public function removeField(int $formId, int $fieldId)
    {
        if (!$formId || !$fieldId) {
            return false;
        }
        $this->db->query(
            'DELETE FROM ' . $this->db->table("fields") . '
			WHERE form_id = "' . $formId . '"
			AND field_id = "' . $fieldId . '"'
        );
        $this->removeFieldValues($fieldId);
        return true;
    }

    /**
     * @param int $fieldId
     * @return bool
     * @throws AException
     */
    public function removeFieldValues(int $fieldId)
    {
        if (!$fieldId) {
            return false;
        }
        $this->db->query(
            "DELETE FROM " . $this->db->table("field_values") . " 
            WHERE field_id = " . $fieldId
        );
        $this->_deleteCache();
        return true;
    }

    /**
     * @param int $formId
     * @return bool
     * @throws AException
     */
    public function removeFields(int $formId)
    {
        if (!$formId) {
            return false;
        }
        $res = $this->db->query(
            'SELECT field_id FROM ' . $this->db->table("fields") . '
			WHERE form_id = ' . $formId
        );

        if ($res->num_rows > 0) {
            $this->db->query(
                'DELETE FROM ' . $this->db->table("fields") . '
				WHERE form_id = ' . $formId
            );
            foreach ($res->rows as $row) {
                $this->removeFieldValues((int)$row['field_id']);
            }
        }
        return true;
    }

    /**
     * @param array $data
     * @return array
     * @throws AException
     */

    protected function _validate(array $data)
    {
        $data['controller_path'] = $this->db->escape($data['controller_path']);
        $data['success_page'] = $this->db->escape($data['success_page']) ?: $data['controller_path'];
        return $data;
    }

    protected function _deleteCache()
    {
        $this->cache->remove('forms');
    }
}