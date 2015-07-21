<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
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

class ModelToolFormsManager extends Model {

	public $error = array();

	public function getFormById($form_id) {

		if ($form_id) {
			$q = 'SELECT f.*, fd.language_id, fd.description
				FROM ' . $this->db->table('forms') . ' f
				LEFT JOIN ' . $this->db->table('form_descriptions') . ' fd
				ON f.form_id = fd.form_id
				WHERE f.form_id = "' . (int)$form_id . '"
				AND fd.language_id = "' . (int)$this->session->data['content_language_id'] . '"';

			$results = $this->db->query($q);

			return $results->row;
		}
		return array();
	}

	public function getForms($data = array(), $mode = 'default') {

		if (!empty($data['content_language_id'])) {
			$language_id = (int)$data['content_language_id'];
		} else {
			$language_id = (int)$this->session->data['content_language_id'];
		}

		if ($data || $mode == 'total_only') {

			$filter = (isset($data['filter']) ? $data['filter'] : array());

			if ($mode == 'total_only') {
				$sql = "SELECT COUNT(*) as total FROM " . $this->db->table("forms") . " f LEFT JOIN " . $this->db->table("form_descriptions") . " fd ON (f.form_id = fd.form_id)";
			} else {
				$sql = "SELECT * FROM " . $this->db->table("forms") . " f LEFT JOIN " . $this->db->table("form_descriptions") . " fd ON (f.form_id = fd.form_id)";
			}

			$sql .= " WHERE fd.language_id = '" . $language_id . "'";

			if (!empty($data['subsql_filter'])) {
				$sql .= " AND " . $data['subsql_filter'];;
			}

			if (isset($filter['match']) && !is_null($filter['match'])) {
				$match = $filter['match'];
			}

			if (isset($filter['keyword']) && !is_null($filter['keyword'])) {
				$keywords = explode(' ', $filter['keyword']);

				if ($match == 'any') {
					$sql .= " AND (";
					foreach ($keywords as $k => $keyword) {
						$sql .= $k > 0 ? " OR" : "";
						$sql .= " (LCASE(fd.description) LIKE '%" . $this->db->escape(mb_strtolower($keyword)) . "%'";
						$sql .= " OR LCASE(f.form_name) LIKE '%" . $this->db->escape(mb_strtolower($keyword)) . "%')";
					}
					$sql .= " )";
				} else if ($match == 'all') {
					$sql .= " AND (";
					foreach ($keywords as $k => $keyword) {
						$sql .= $k > 0 ? " AND" : "";
						$sql .= " (LCASE(fd.description) LIKE '%" . $this->db->escape(mb_strtolower($keyword)) . "%'";
						$sql .= " OR LCASE(f.form_name) LIKE '%" . $this->db->escape(mb_strtolower($keyword)) . "%')";
					}
					$sql .= " )";
				} else if ($match == 'exact') {
					$sql .= " AND (LCASE(fd.description) LIKE '%" . $this->db->escape(mb_strtolower($filter['keyword'])) . "%'";
					$sql .= " OR LCASE(f.form_name) LIKE '%" . $this->db->escape(mb_strtolower($filter['keyword'])) . "%')";
				}
			}

			if (isset($filter['status']) && !is_null($filter['status'])) {
				$sql .= " AND f.status = '" . (int)$filter['status'] . "'";
			}

			//If for total, we done bulding the query
			if ($mode == 'total_only') {
				$query = $this->db->query($sql);
				return $query->row['total'];
			}

			$sort_data = array(
					'name' => 'f.form_name',
					'description' => 'fd.description',
					'status' => 'f.status',
			);

			if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
				$sql .= " ORDER BY " . $sort_data[$data['sort']];
			} else {
				$sql .= " ORDER BY f.form_name";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
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

			return $query->rows;
		} else {
			$form_data = $this->cache->get('forms_manager', $language_id);

			if (!$form_data) {
				$query = $this->db->query("SELECT *
											FROM " . $this->db->table("forms") . " f
											LEFT JOIN " . $this->db->table("form_descriptions") . " fd ON (f.form_id = fd.form_id)
											WHERE fd.language_id = '" . $language_id . "'
											ORDER BY f.form_name ASC");

				$form_data = $query->rows;

				$this->cache->set('forms_manager', $form_data, $language_id);
			}

			return $form_data;
		}
	}

	public function getTotalForms($data = array()) {
		return $this->getForms($data, 'total_only');
	}

	public function getFormDescriptions($form_id) {
		$form_description_data = array();

		$query = $this->db->query("SELECT * FROM " . $this->db->table("form_descriptions") . " WHERE form_id = '" . (int)$form_id . "'");

		foreach ($query->rows as $result) {
			$form_description_data[$result['language_id']] = array(
					'form_name' => $result['name'],
					'meta_keywords' => $result['meta_keywords'],
					'meta_description' => $result['meta_description'],
					'description' => $result['description']
			);
		}

		return $form_description_data;
	}

	public function addForm($data) {

		$data['form_name'] = str_replace(' ', '_', $this->db->escape($data['form_name']));

		if (!$this->getFormIdByName($data['form_name'])) {

			$data = $this->_validate($data);

			$q = 'INSERT INTO ' . $this->db->table("forms") . '
				SET
					form_name = "' . $data['form_name'] . '",
					controller = "' . $data['controller_path'] . '",
					success_page = "' . $data['success_page'] . '",
					status = 1';

			$this->db->query($q);
			$form_id = $this->db->getLastId();
			$langs = $this->language->getAvailableLanguages();
			foreach ($langs as $lang) {
				$this->addFormDescription($form_id, $data['form_description'], $lang['language_id']);
			}
			return $form_id;
		}
		return false;
	}

	public function addFormDescription($form_id, $description, $language_id) {

		if (empty($description)) {
			$description = '';
		}

		$this->language->replaceDescriptions('form_descriptions',
				array('form_id' => (int)$form_id),
				array((int)$language_id => array('description' => $description))
		);

		$this->_deleteCache();
	}

	public function deleteForm($form_id) {
		$this->db->query(
				'DELETE FROM ' . $this->db->table("forms") . '
			WHERE form_id = "' . (int)$form_id . '"'
		);
		$this->deleteFormDescription($form_id);
		$this->removeFields($form_id);
		$this->_deleteCache();
	}

	public function deleteFormDescription($form_id) {
		$this->db->query(
				'DELETE FROM ' . $this->db->table("form_descriptions") . '
			WHERE form_id = "' . (int)$form_id . '"'
		);
	}

	public function updateForm($data) {

		if ($this->getFormNameById($data['form_id']) !== false) {

			$cols = array();

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

			$this->_deleteCache($this->db->escape($data['form_name']));

			return true;
		}

		return false;
	}

	public function updateFormDescription($form_id, $description) {
		if ($description) {
			$this->language->updateDescriptions('form_descriptions',
					array('form_id' => (int)$form_id),
					array((int)(int)$this->session->data['content_language_id'] => array('description' => $description))
			);

		}
	}

	public function updateFormFieldData($data) {

		if (!(int)$data['field_id']) {
			return false;
		}

		$columns = array(
				'field_name',
				'element_type',
				'sort_order',
				'required',
				'status',
				'settings',
				'regexp_pattern'
		);

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

		if (has_value($data['regexp_pattern'])) {
			$data['regexp_pattern'] = $this->db->escape($data['regexp_pattern']);
		}


		if (has_value($data['settings'])) {
			$data['settings'] = $this->db->escape(serialize($data['settings']));
		}

		foreach ($columns as $colname) {
			if(has_value($data[$colname])){
				$update[] = $colname . " = '" . $data[$colname] . "'";
			}
		}

		if ($update) {

			$sql = "UPDATE " . $this->db->table('fields') . "
					SET " . implode(",\n ", $update) . "
					WHERE field_id = '" . (int)$data['field_id'] . "' AND form_id = '" . (int)$data['form_id'] . "'";

			$this->db->query($sql);
		}
		$this->updateFieldDescription($data['field_id'], $data);
		$this->_deleteCache();

	}

	public function updateFieldDescription($field_id, $data) {
		if (!$field_id || !$data) {
			return false;
		}

		$columns = array(
				'name' => 'field_description',
				'description' => 'field_note',
				'error_text' => 'error_text'
		);

		$update = array();
		foreach ($columns as $alias => $colname) {
			if ($data[$colname]) {
				$update[$alias] = $data[$colname];
			}

		}

		if ($update) {
			$this->language->replaceDescriptions(
					'field_descriptions',
					array('field_id' => (int)$field_id),
					array((int)$this->language->getContentLanguageID() => $update)
			);
		}

		$this->_deleteCache();
	}

	public function updateFieldValues($data, $language_id) {

		if (!$language_id) return null;
		$values = array();
		if (!empty($data['field_value_id'])) {
			foreach ($data['field_value_id'] as $key => $status) {
				$value_data = array(
						'name' => $data['name'][$key],
						'sort_order' => $data['sort_order'][$key],
				);

				//Check if new, delete or update
				if ($status == 'delete' && strpos($key, 'new') === FALSE) {
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

	public function addFieldValue($field_id, $data, $language_id) {
		if (!$language_id || !$field_id) {
			return null;
		}
		$this->language->replaceDescriptionsSerialized('field_values',
				array('field_id' => (int)$field_id),
				array((int)$language_id => array('value' => serialize($data))),
				array('value' => array('name'))
		);

		$this->_deleteCache();
	}

	public function deleteFieldValue($field_id, $language_id) {
		$this->db->query(
				'DELETE FROM ' . $this->db->table("field_values") . '
			WHERE field_id = "' . (int)$field_id . '" AND language_id = "' . (int)$language_id . '"'
		);
		$this->_deleteCache();
	}

	public function deleteFieldValueByOptValue($opt_value) {
		$opt_value = $this->db->escape($opt_value);
		if ($opt_value != '') {
			$this->db->query(
					'DELETE FROM ' . $this->db->table("field_values") . '
				WHERE opt_value = "' . $opt_value . '"'
			);
		}
	}

	public function getFormIdByName($name) {

		$results = $this->db->query(
				'SELECT form_id FROM ' . $this->db->table("forms") . '
			WHERE form_name = "' . $name . '" LIMIT 1'
		);

		if (!empty($results->row)) {
			return $results->row['form_id'];
		}
		return false;
	}

	public function getFormNameById($form_id) {
		$results = $this->db->query(
				'SELECT form_name FROM ' . $this->db->table("forms") . '
			WHERE form_id = "' . (int)$form_id . '" LIMIT 1'
		);

		if (!empty($results->row)) {
			return $results->row['form_name'];
		}
		return false;
	}

	public function addField($form_id, $data) {

		$data['field_name'] = str_replace(' ', '_', $this->db->escape($data['field_name']));

		if ($this->isFieldNameUnique($form_id, $data['field_name'])) {

			$sql = 'INSERT INTO ' . $this->db->table('fields') . '
				SET
					form_id = "' . (int)$form_id . '",
					field_name = "' . $data['field_name'] . '",
					element_type = "' . $this->db->escape($data['element_type']) . '",
			';

			$data['sort_order'] = isset($data['sort_order']) ? (int)$data['sort_order'] : 0;

			$sql .= 'sort_order = "' . $data['sort_order'] . '", ';

			$data['required'] = ((int)$data['required'] > 0) ? 'Y' : 'N';

			$sql .= 'required = "' . $data['required'] . '", ';

			$data['status'] = (int)$data['status'];

			$sql .= 'status = "' . $data['status'] . '"';

			$this->db->query($sql);

			$field_id = $this->db->getLastId();

			$this->addFieldDescription($field_id, $data, (int)$this->session->data['content_language_id']);
			$this->_deleteCache();

			return true;
		}

		return false;

	}

	public function addFieldDescription($field_id, $data, $language_id) {

		if (!$data['field_description']) {
			$data['field_description'] = '';
		}

		if (!$data['field_note']) {
			$data['field_note'] = '';
		}

		$this->language->replaceDescriptions(
				'field_descriptions',
				array('field_id' => (int)$field_id),
				array(
						(int)$language_id => array(
								'name' => $data['field_description'],
								'description' => $data['field_note']
						)
				)
		);

	}

	public function isFieldNameUnique($form_id, $field_name, $field_id=0) {

		$result = $this->db->query(
				"SELECT field_id FROM " . $this->db->table("fields") . "
			WHERE form_id = '" . (int)$form_id . "'
			".($field_id ? " AND field_id<>'".(int)$field_id."'" : '')."
			AND field_name = '" . $field_name . "'
			LIMIT 1"
		);

		if ($result->num_rows > 0) {
			return false;
		}
		return true;
	}

	public function getFields($form_id) {

		$fields = array();

		$query = $this->db->query("
            SELECT f.*, fd.name, fd.description
            FROM " . $this->db->table("fields") . " f
            LEFT JOIN " . $this->db->table("field_descriptions") . " fd
                ON ( f.field_id = fd.field_id AND fd.language_id = '" . (int)$this->language->getContentLanguageID() . "' )
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

	public function getField($field_id) {
		$field = array();

		$query = $this->db->query("
            SELECT f.*, fd.name, fd.description, fd.error_text
            FROM " . $this->db->table("fields") . " f
                LEFT JOIN " . $this->db->table("field_descriptions") . " fd ON ( f.field_id = fd.field_id AND fd.language_id = '" . (int)$this->session->data['content_language_id'] . "' )
            WHERE f.field_id = '" . (int)$field_id . "'

            ORDER BY f.sort_order LIMIT 1"
		);

		if (!empty($query->row)) {

			$field = $query->row;
			$field['settings'] = unserialize($field['settings']);
			$query = $this->db->query("
				SELECT *
				FROM " . $this->db->table("field_values") . "
				WHERE field_id = '" . $query->row['field_id'] . "'
				AND language_id = '" . (int)$this->session->data['content_language_id'] . "'
				LIMIT 1"
			);

			if ($query->num_rows) {
				$field['values'] = unserialize($query->row['value']);
			}
		}
		return $field;
	}

	public function removeField($form_id, $field_id) {
		$this->db->query(
				'DELETE FROM ' . $this->db->table("fields") . '
			WHERE form_id = "' . (int)$form_id . '"
			AND field_id = "' . (int)$field_id . '"'
		);
		$this->removeFieldValues($field_id);
	}

	public function removeFieldValues($field_id) {
		$this->db->query(
				'DELETE FROM ' . $this->db->table("field_values") . '
			WHERE field_id = "' . (int)$field_id . '"'
		);
		$this->_deleteCache();
	}

	public function removeFields($form_id) {

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

	private function _validate($data) {

		$data['controller_path'] = $this->db->escape($data['controller_path']);
		$data['success_page'] = $this->db->escape($data['success_page']);

		if (!$data['success_page']){
			$data['success_page'] = $data['controller_path'];
		}

		return $data;
	}

	private function _deleteCache($name = '') {
		$cache_name = 'forms';
		if ($name) {
			$cache_name .= '.' . $name;
		}
		$cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
		$this->cache->delete($cache_name);
	}
}