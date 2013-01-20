<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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
			)));

		$this->cache->delete('lang');
		$this->cache->delete('language_definitions');
		$this->cache->delete('admin_menu');

		return $this->db->getLastId();
	}

	public function editLanguageDefinition($id, $data) {
		if (empty($id) || !is_array($data) || !$data) return false;

		$lang_value = (string)$data['language_value'];
		$update_data = $this->getLanguageDefinition($id);

		if (isset($update_data['language_key'])) {
			$this->language->replaceDescriptions('language_definitions',
				array('section' => (int)$update_data['section'],
					'block' => $update_data['block'],
					'language_key' => $update_data['language_key']
				),
				array((int)$this->session->data['content_language_id'] => array(
					'section' => (int)$update_data['section'],
					'block' => $update_data['block'],
					'language_key' => $update_data['language_key'],
					'language_value' => html_entity_decode($lang_value, ENT_QUOTES, 'UTF-8')
				)));
		} else {
			$this->language->replaceDescriptions('language_definitions',
				array('section' => (int)$update_data['section'],
					'block' => $update_data['block'],
					'language_key' => $update_data['language_key']
				),
				array((int)$this->session->data['content_language_id'] => array(
					'language_value' => html_entity_decode($lang_value, ENT_QUOTES, 'UTF-8')
				)));
		}

		$this->cache->delete('lang');
		$this->cache->delete('language_definitions');
		$this->cache->delete('admin_menu');
	}

	public function deleteLanguageDefinition($id) {
		$result = $this->db->query("SELECT language_id, `section`, `language_key`, `block`
						FROM " . DB_PREFIX . "language_definitions
						WHERE language_definition_id = '" . (int)$id . "'");
		foreach ($result->rows as $row) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "language_definitions
							  WHERE `section` = '" . $row['section'] . "'
							  		AND `block` = '" . $row['block'] . "'
							  		AND `language_key` = '" . $row['language_key'] . "'");
		}
		$this->cache->delete('lang');
		$this->cache->delete('language_definitions');
		$this->cache->delete('admin_menu');
	}

	public function getLanguageDefinition($id) {
		$query = $this->db->query("SELECT DISTINCT *
									FROM " . DB_PREFIX . "language_definitions
									WHERE language_definition_id = '" . (int)$id . "'");

		return $query->row;
	}

	public function getLanguageDefinitionIdByKey($key, $language_id, $block, $section) {
		$query = $this->db->query("SELECT language_definition_id
									FROM " . DB_PREFIX . "language_definitions
									WHERE language_key = '" . $this->db->escape($key) . "'
										AND block='" . $this->db->escape($block) . "'
										AND language_id='" . $this->db->escape($language_id) . "'
										AND section='" . (int)$section . "'");
		return $query->row['language_definition_id'];
	}

	public function getAllLanguageDefinitionsIdByKey($key, $block, $section) {
		$query = $this->db->query("SELECT language_definition_id
									FROM " . DB_PREFIX . "language_definitions
									WHERE language_key = '" . $this->db->escape($key) . "'
										AND block='" . $this->db->escape($block) . "'
										AND section='" . (int)$section . "'");

		return $query->rows;
	}

	public function getLanguageDefinitions($data = array(), $mode = 'default') {

		if ($data || $mode == 'total_only') {
			$filter = (isset($data['filter']) ? $data['filter'] : array());
			if ($mode == 'total_only') {
				$sql = "SELECT count(*) as total
						FROM " . DB_PREFIX . "language_definitions ld
						LEFT JOIN " . DB_PREFIX . "languages l ON l.language_id = ld.language_id";
			} else {
				$sql = "SELECT ld.*, l.name as language_name, l.code as language_code
						FROM " . DB_PREFIX . "language_definitions ld
						LEFT JOIN " . DB_PREFIX . "languages l ON l.language_id = ld.language_id";
			}

			if (isset($filter['section']) && !empty($filter['section'])) {
				$sql .= " WHERE `section` = '" . $this->db->escape($filter['section']) . "' ";
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
				'update_date',
				'language_key',
				'language_value',
				'block'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY update_date DESC, language_key, block";
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
				                           FROM " . DB_PREFIX . "language_definitions
				                           WHERE language_id=" . (int)$this->config->get('admin_language_id') . "
				                           ORDER BY update_date DESC, language_key, block");

				foreach ($query->rows as $result) {
					$language_data[$result['code']] = array(
						'language_definition_id' => $result['language_definition_id'],
						'language_id' => $result['language_id'],
						'section' => $result['section'],
						'block' => $result['block'],
						'language_key' => $result['language_key'],
						'language_value' => $result['language_value'],
						'update_date' => $result['update_date'],
					);
				}
				$this->cache->set('language_definitions', $language_data);
			}


			return $language_data;
		}
	}

	public function getTotalDefinitions($data = array()) {
		return $this->getLanguageDefinitions($data, 'total_only');
	}
}

?>