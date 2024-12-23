<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
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
     * @param int $form_id
     * @return array
     * @throws AException
     */
    public function getFormById($form_id)
    {
        if (!$form_id) {
            return [];
        }
        $sql = "SELECT f.*, fd.language_id, fd.description
                FROM " . $this->db->table('forms') . " f
                LEFT JOIN " . $this->db->table('form_descriptions') . " fd
                    ON f.form_id = fd.form_id
                WHERE f.form_id = " . (int)$form_id . "
                    AND fd.language_id = " . (int)$this->language->getContentLanguageID();
        $results = $this->db->query($sql);
        return $results->row;
    }

    public function getForms($data = [], $mode = 'default')
    {
        $language_id = (int)$data['content_language_id'] ?: $this->language->getContentLanguageID();

        if ($data || $mode == 'total_only') {
            $filter = $data['filter'] ?? [];
            $sql = "SELECT " . ($mode == 'total_only' ? 'COUNT(*) as total' : $this->db->getSqlCalcTotalRows() . ' *') . " 
                    FROM " . $this->db->table("forms") . " f 
                    LEFT JOIN " . $this->db->table("form_descriptions") . " fd 
                        ON (f.form_id = fd.form_id)
                    WHERE fd.language_id = '" . $language_id . "'";
            if ($data['subsql_filter']) {
                $sql .= " AND " . $data['subsql_filter'];;
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
                        $sql .= " (LCASE(fd.description) LIKE '%" . $this->db->escape(mb_strtolower($keyword), true) . "%'";
                        $sql .= " OR LCASE(f.form_name) LIKE '%" . $this->db->escape(mb_strtolower($keyword), true) . "%')";
                    }
                    $sql .= " )";
                } else {
                    if ($match == 'all') {
                        $sql .= " AND (";
                        foreach ($keywords as $k => $keyword) {
                            $sql .= $k > 0 ? " AND" : "";
                            $sql .= " (LCASE(fd.description) LIKE '%" . $this->db->escape(mb_strtolower($keyword), true) . "%'";
                            $sql .= " OR LCASE(f.form_name) LIKE '%" . $this->db->escape(mb_strtolower($keyword), true) . "%')";
                        }
                        $sql .= " )";
                    } else {
                        if ($match == 'exact') {
                            $sql .= " AND (LCASE(fd.description) LIKE '%" . $this->db->escape(mb_strtolower($filter['keyword']), true) . "%'";
                            $sql .= " OR LCASE(f.form_name) LIKE '%" . $this->db->escape(mb_strtolower($filter['keyword']), true) . "%')";
                        }
                    }
                }
            }

            if (isset($filter['status'])) {
                $sql .= " AND f.status = '" . (int)$filter['status'] . "'";
            }

            if ($mode == 'total_only') {
                $result = $this->db->query($sql);
                return $result->row['total'];
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

    public function getTotalForms($data = [])
    {
        return $this->getForms($data, 'total_only');
    }

    public function getFormDescriptions($form_id)
    {
        $form_description_data = [];

        $result = $this->db->query(
            "SELECT * 
             FROM " . $this->db->table("form_descriptions") . " 
             WHERE form_id = " . (int)$form_id
        );

        foreach ($result->rows as $row) {
            $form_description_data[$row['language_id']] = $row;
        }
        return $form_description_data;
    }

    public function addForm($data)
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
            $form_id = $this->db->getLastId();
            $langs = $this->language->getAvailableLanguages();
            foreach ($langs as $lang) {
                $this->addFormDescription($form_id, $data['form_description'], $lang['language_id']);
            }
            return $form_id;
        }
        return false;
    }

    public function addFormDescription($form_id, $description, $language_id)
    {
        $description = (string)$description;
        $this->language->replaceDescriptions(
            'form_descriptions',
            [
                'form_id' => (int)$form_id
            ],
            [
                (int)$language_id => [
                    'description' => $description
                ]
            ]
        );

        $this->_deleteCache();
    }

    public function deleteForm($form_id)
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table("forms") . "
			WHERE form_id = " . (int)$form_id
        );
        $this->deleteFormDescription($form_id);
        $this->removeFields($form_id);
        $this->_deleteCache();
    }

    public function deleteFormDescription($form_id)
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table("form_descriptions") . "
			WHERE form_id = " . (int)$form_id
        );
    }

    public function updateForm($data)
    {
        if ($this->getFormNameById($data['form_id']) !== false) {
            $cols = [];
            if (isset($data['form_name'])) {
                $data['form_name'] = str_replace(' ', '_', $this->db->escape($data['form_name']));
                $cols[] = 'form_name = "' . $data['form_name'] . '"';
            }

            if (isset($data['controller_path'])) {
                $cols[] = 'controller = "' . $this->db->escape($data['controller_path']) . '"';
                $cols[] = 'success_page = "' . $this->db->escape($data['controller_path']) . '"';
            }

            if (has_value($data['success_page'])) {
                $cols[] = 'success_page = "' . $this->db->escape($data['success_page']) . '"';
            } else {
                if (isset($data['controller_path'])) {
                    $cols[] = 'success_page = "' . $this->db->escape($data['controller_path']) . '"';
                } else {
                    $cols[] = 'success_page = ""';
                }
            }

            if (isset($data['form_status'])) {
                $cols[] = 'status = "' . (int)$data['form_status'] . '"';
            }

            if (!empty($cols)) {
                $this->db->query(
                    'UPDATE ' . $this->db->table("forms") . '
					SET ' . implode(',', $cols) . '
					WHERE form_id = "' . (int)$data['form_id'] . '"'
                );
            }

            if (isset($data['form_description'])) {
                $this->updateFormDescription($data['form_id'], $data['form_description']);
            }
            $this->_deleteCache();
            return true;
        }
        return false;
    }

    public function updateFormDescription($form_id, $description)
    {
        if ($description) {
            $this->language->updateDescriptions(
                'form_descriptions',
                [
                    'form_id' => (int)$form_id
                ],
                [
                    (int)$this->session->data['content_language_id'] => [
                        'description' => $description
                    ]
                ]
            );
        }
    }

    public function updateFormFieldData($data)
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
        $this->updateFieldDescription($data['field_id'], $data);
        $this->_deleteCache();
        return true;
    }

    public function updateFieldDescription($field_id, $data)
    {
        if (!$field_id || !$data) {
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
                ['field_id' => (int)$field_id],
                [(int)$this->language->getContentLanguageID() => $update]
            );
        }

        $this->_deleteCache();
        return true;
    }

    public function updateFieldValues($data, $language_id)
    {
        if (!$language_id) {
            return null;
        }
        $values = [];
        if (!empty($data['field_value_id'])) {
            foreach ($data['field_value_id'] as $key => $status) {
                $value_data = [
                    'name'       => $data['name'][$key],
                    'sort_order' => $data['sort_order'][$key],
                ];
                //Check if new, delete or update
                if ($status == 'delete' && strpos($key, 'new') === false) {
                    continue;
                } else {
                    $values[] = $value_data;
                }
            }
        }

        if (!empty($values)) {
            $this->deleteFieldValue($data['field_id'], $language_id);
            $this->addFieldValue($data['field_id'], $values, $language_id);
        }

        $this->_deleteCache();

    }

    public function addFieldValue($field_id, $data, $language_id)
    {
        if (!$language_id || !$field_id) {
            return null;
        }
        $this->language->replaceDescriptionsSerialized(
            'field_values',
            ['field_id' => (int)$field_id],
            [(int)$language_id => ['value' => serialize($data)]],
            ['value' => ['name']]
        );
        $this->_deleteCache();
    }

    public function deleteFieldValue($field_id, $language_id)
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table("field_values") . "
			WHERE field_id = " . (int)$field_id . " AND language_id = " . (int)$language_id
        );
        $this->_deleteCache();
    }

    public function deleteFieldValueByOptValue($opt_value)
    {
        $opt_value = $this->db->escape($opt_value);
        if ($opt_value) {
            $this->db->query(
                "DELETE FROM " . $this->db->table("field_values") . "
				WHERE opt_value = '" . $opt_value . "'"
            );
        }
    }

    public function getFormIdByName($name)
    {
        $results = $this->db->query(
            "SELECT form_id FROM " . $this->db->table("forms") . "
			WHERE form_name = '" . $name . "'
			LIMIT 1"
        );
        return $results->row['form_id'] ?: false;
    }

    public function getFormNameById($form_id)
    {
        $results = $this->db->query(
            "SELECT form_name 
            FROM " . $this->db->table("forms") . "
			WHERE form_id = " . (int)$form_id . " 
			LIMIT 1"
        );
        return $results->row['form_name'] ?: false;
    }

    public function addField($form_id, $data)
    {
        $data['field_name'] = str_replace(' ', '_', $this->db->escape($data['field_name']));
        if ($this->isFieldNameUnique($form_id, $data['field_name'])) {
            $sql = "INSERT INTO " . $this->db->table('fields') . "
				SET
					form_id = " . (int)$form_id . ",
					field_name = '" . $data['field_name'] . "',
					element_type = '" . $this->db->escape($data['element_type']) . "',
			";
            $sql .= " sort_order = " . (int)$data['sort_order'] . ", ";
            $sql .= " required = '" . ((int)$data['required'] ? 'Y' : 'N') . "', ";
            $sql .= " status = " . (int)$data['status'];

            $this->db->query($sql);
            $field_id = $this->db->getLastId();
            $this->addFieldDescription(
                $field_id,
                $data,
                (int)$this->session->data['content_language_id']
            );
            $this->_deleteCache();
            return true;
        }
        return false;
    }

    public function addFieldDescription($field_id, $data, $language_id)
    {
        $data['field_description'] = (string)$data['field_description'];
        $data['field_note'] = (string)$data['field_note'];

        $this->language->replaceDescriptions(
            'field_descriptions',
            ['field_id' => (int)$field_id],
            [
                (int)$language_id => [
                    'name'        => $data['field_description'],
                    'description' => $data['field_note'],
                ],
            ]
        );

    }

    public function isFieldNameUnique($form_id, $field_name, $field_id = 0)
    {

        $result = $this->db->query(
            "SELECT field_id 
            FROM " . $this->db->table("fields") . "
			WHERE form_id = '" . (int)$form_id . "'
			    " . ($field_id ? " AND field_id<>'" . (int)$field_id . "'" : '') . "
			AND field_name = '" . $field_name . "'
			LIMIT 1"
        );
        return !(bool)$result->num_rows;
    }

    public function getFields($form_id)
    {

        $fields = [];

        $query = $this->db->query("
            SELECT f.*, fd.name, fd.description
            FROM " . $this->db->table("fields") . " f
            LEFT JOIN " . $this->db->table("field_descriptions") . " fd
                ON ( f.field_id = fd.field_id 
                    AND fd.language_id = '" . (int)$this->language->getContentLanguageID() . "' )
            WHERE f.form_id = '" . (int)$form_id . "'
            ORDER BY f.sort_order"
        );

        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $fields[$row['field_id']] = $row;
                $query = $this->db->query("
					SELECT *
					FROM " . $this->db->table("field_values") . "
					WHERE field_id = '" . $row['field_id'] . "'
					AND language_id = '" . (int)$this->language->getContentLanguageID() . "'"
                );
                if ($query->num_rows) {
                    $fields[$row['field_id']]['values'] = unserialize($query->row['value']);
                }
            }
        }
        return $fields;
    }

    public function getField($field_id)
    {
        $field = [];

        $query = $this->db->query("
            SELECT f.*, fd.name, fd.description, fd.error_text
            FROM " . $this->db->table("fields") . " f
            LEFT JOIN " . $this->db->table("field_descriptions") . " fd 
                ON ( f.field_id = fd.field_id 
                    AND fd.language_id = '" . (int)$this->language->getContentLanguageID() . "' )
            WHERE f.field_id = '" . (int)$field_id . "'
            ORDER BY f.sort_order 
            LIMIT 1"
        );

        if ($query->row) {
            $field = $query->row;
            $field['settings'] = unserialize($field['settings']);
            $query = $this->db->query("
				SELECT *
				FROM " . $this->db->table("field_values") . "
				WHERE field_id = '" . $query->row['field_id'] . "'
				AND language_id = '" . (int)$this->language->getContentLanguageID() . "'
				LIMIT 1"
            );

            if ($query->num_rows) {
                $field['values'] = unserialize($query->row['value']);
            }
        }
        return $field;
    }

    public function removeField($form_id, $field_id)
    {
        $this->db->query(
            'DELETE FROM ' . $this->db->table("fields") . '
			WHERE form_id = "' . (int)$form_id . '"
			AND field_id = "' . (int)$field_id . '"'
        );
        $this->removeFieldValues($field_id);
    }

    public function removeFieldValues($field_id)
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table("field_values") . " WHERE field_id = " . (int)$field_id
        );
        $this->_deleteCache();
    }

    public function removeFields($form_id)
    {

        $res = $this->db->query(
            'SELECT field_id FROM ' . $this->db->table("fields") . '
			WHERE form_id = "' . (int)$form_id . '"'
        );

        if ($res->num_rows > 0) {
            $this->db->query(
                'DELETE FROM ' . $this->db->table("fields") . '
				WHERE form_id = "' . (int)$form_id . '"'
            );

            foreach ($res->rows as $row) {
                $this->removeFieldValues($row['field_id']);
            }
        }
    }

    protected function _validate($data)
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