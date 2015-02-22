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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

/**
 * Class ModelExtensionBannerManager
 */

/** @noinspection PhpUndefinedClassInspection */
class ModelExtensionBannerManager extends Model {
	/**
	 * @param array $data
	 * @return int
	 */
	public function addBanner($data = array()) {

		if (isset($data['start_date'])) {
			$data['start_date'] = "DATE('" . $data['start_date'] . "')";
		} else {
			$data['start_date'] = "NULL";
		}

		if (isset($data['end_date'])) {
			$data['end_date'] = "DATE('" . $data['end_date'] . "')";
		} else {
			$data['end_date'] = "NULL";
		}

		$sql = "INSERT INTO " . $this->db->table("banners") . " 
				(`status`,`banner_type`,`banner_group_name`,`start_date`,`end_date`,`blank`,`sort_order`,`target_url`,`date_added`)
				VALUES ('" . (int)$data['status'] . "',
						'" . (int)$data['banner_type'] . "',
						'" . $this->db->escape($data['banner_group_name']) . "',
						" . $data['start_date'] . ",
						" . $data['end_date'] . ",
						'" . (int)$data['blank'] . "',
						'" . (int)$data['sort_order'] . "',
						'" . $this->db->escape($data['target_url']) . "', NOW() )";
		$this->db->query($sql);
		$banner_id = $this->db->getLastId();
		// for graphic banners remap resources
		if ((int)$data['banner_type'] == 1) {
			$sql = "UPDATE " . $this->db->table("resource_map") . " SET object_id='" . $banner_id . "' WHERE object_name='banners' AND object_id='-1'";
			$this->db->query($sql);
		}
		$this->language->replaceDescriptions('banner_descriptions',
				array('banner_id' => (int)$banner_id),
				array((int)$this->session->data['content_language_id'] => array(
						'name' => $data['name'],
						'meta' => $data['meta'],
						'description' => $data['description'],
				)));

		return $banner_id;
	}

	/**
	 * @param int $banner_id
	 * @param int $language_id
	 * @return array
	 */
	public function getBanner($banner_id, $language_id = 0 ) {
		$banner_id = (int)$banner_id;
		$language_id = (int)$language_id;
		if (!$language_id) {
			$language_id = (int)$this->language->getContentLanguageID();
		}
		// check is description presents
		$sql = "SELECT DISTINCT language_id
				FROM " . $this->db->table("banner_descriptions") . " 
				WHERE banner_id='" . $banner_id . "'
				ORDER BY language_id ASC";
		$result = $this->db->query($sql);
		$counts = array();
		foreach ($result->rows as $row) {
			$counts[] = $row['language_id'];
		}
		if (!in_array($language_id, $counts)) {
			$language_id = $counts[0];
		}

		$sql = "SELECT  bd.*, b.*
				FROM " . $this->db->table("banners") . " b
				LEFT JOIN " . $this->db->table("banner_descriptions") . " bd ON (bd.banner_id = b.banner_id AND bd.language_id = '" . $language_id . "')
				WHERE b.banner_id='" . $banner_id . "'";
		$result = $this->db->query($sql);
		return $result->row;
	}

	/**
	 * @return array
	 */
	public function getBannerGroups() {
		// check is description presents
		$sql = "SELECT DISTINCT TRIM(banner_group_name) as banner_group_name
				FROM " . $this->db->table("banners") . " 
				ORDER BY TRIM(banner_group_name) ASC";
		$result = $this->db->query($sql);
		return $result->rows;
	}

	/**
	 * @param int $banner_id
	 * @param array $data
	 */
	public function editBanner($banner_id, $data) {
		$banner_id = (int)$banner_id;
		$language_id = (int)$this->language->getContentLanguageID();
		if (isset($data['start_date']) && !$data['start_date']) {
			$data['start_date'] = 'NULL';
		}
		if (isset($data['end_date']) && !$data['end_date']) {
			$data['end_date'] = 'NULL';
		}

		$flds = array('name', 'description', 'meta');
		foreach ($flds as $field_name) {
			if (isset($data[$field_name])) {
				$update[$field_name] = $data[$field_name];
			}
		}

		if (count($update)) {
			$this->language->replaceDescriptions('banner_descriptions',
					array('banner_id' => (int)$banner_id),
					array($language_id => $update));
		}
		$flds = array('status' => 'int',
				'banner_type' => 'int',
				'banner_group_name' => '',
				'start_date' => 'time',
				'end_date' => 'time',
				'blank' => 'int',
				'sort_order' => 'int',
				'target_url' => '');
		$sql = "UPDATE " . $this->db->table("banners") . " 
				SET ";
		$tmp = array();
		foreach (array_keys($flds) as $field_name) {
			if (isset($data[$field_name])) {
				$quote = ($flds[$field_name] == 'int' || ($flds[$field_name] == 'time') && $data[$field_name] == 'NULL') ? null : "'";
				$new_value = $flds[$field_name] == 'int' ? (int)$data[$field_name] : $this->db->escape($data[$field_name]);
				$tmp[] = "`" . $field_name . "` = " . $quote . $new_value . $quote . "\n";
			}
		}
		$sql .= implode(', ', $tmp);
		$sql .= " WHERE banner_id='" . $banner_id . "'";
		if ($tmp) {
			$this->db->query($sql);
		}
	}

	/**
	 * @param int $banner_id
	 * @return bool
	 */
	public function deleteBanner($banner_id) {
		$banner_id = (int)$banner_id;
		if (!$banner_id) return false;

		$sql[] = "DELETE FROM " . $this->db->table("banners") . " WHERE banner_id = '" . $banner_id . "'";
		$sql[] = "DELETE FROM " . $this->db->table("banner_descriptions") . " WHERE banner_id = '" . $banner_id . "'";
		$sql[] = "DELETE FROM " . $this->db->table("resource_map") . " WHERE object_name = 'banners'  AND object_id = '" . $banner_id . "'";
		foreach ($sql as $s) {
			$this->db->query($s);
		}
		return true;
	}

	/**
	 * @param array $filter
	 * @param string $mode
	 * @return array
	 */
	public function getBanners($filter, $mode = '') {
		if (!empty($data['content_language_id'])) {
			$language_id = (int)$data['content_language_id'];
		} else {
			$language_id = (int)$this->language->getContentLanguageID();
		}

		if ($mode == 'total_only') {
			$sql = "SELECT COUNT(*) as total
						FROM " . $this->db->table('banners') . " b
						LEFT JOIN " . $this->db->table('banner_descriptions') . " bd ON (b.banner_id = bd.banner_id AND bd.language_id = '" . $language_id . "')";
		} else {
			$sql = "SELECT bd.*, b.*
						FROM " . $this->db->table("banners") . " b
						LEFT JOIN " . $this->db->table("banner_descriptions") . " bd ON (b.banner_id = bd.banner_id AND bd.language_id = '" . $language_id . "')";
		}

		if (!empty($filter['subsql_filter'])) {
			$sql .= " WHERE " . $filter['subsql_filter'];
		}


		$sort_data = array(
				'name' => 'bd.name',
				'status' => 'b.status',
				'sort_order' => 'b.sort_order',
				'date_modified' => 'b.date_modified'
		);

		if (isset($filter['sort']) && in_array($filter['sort'], array_keys($sort_data))) {
			$sql .= " ORDER BY " . $sort_data[$filter['sort']];
		} else {
			$sql .= " ORDER BY bd.name";
		}

		if (isset($filter['order']) && ($filter['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($filter['start']) || isset($filter['limit'])) {
			if ($filter['start'] < 0) {
				$filter['start'] = 0;
			}
			if ($filter['limit'] < 1) {
				$filter['limit'] = 20;
			}
			$sql .= $mode != 'total_only' ? " LIMIT " . (int)$filter['start'] . "," . (int)$filter['limit'] : '';
		}
		$result = $this->db->query($sql);

		$output = array();
		if ($mode == 'total_only') {
			$output = $result->row['total'];
		} else {
			foreach ($result->rows as $row) {
				if ($row['name']) {
					$output[] = $row;
				} else {
					$output[] = $this->getBanner($row['banner_id'], 1);
				}
			}
		}
		return $output;
	}

	/**
	 * @param array $filter
	 * @param string $mode
	 * @return int|array
	 */
	public function getBannersStat($filter, $mode = '') {
		if (!empty($data['content_language_id'])) {
			$language_id = ( int )$this->language->getContentLanguageID();
		} else {
			$language_id = (int)$this->config->get('storefront_language_id');
		}

		if ($mode == 'total_only') {
			$sql = "SELECT COUNT(*) as total
						FROM " . $this->db->table("banners") . " b
						LEFT JOIN " . $this->db->table("banner_descriptions") . " bd ON (b.banner_id = bd.banner_id)";
		} else {

			$sql = "SELECT `banner_id`, `type`, count(`type`) as cnt
						FROM " . $this->db->table("banner_stat") . " 
						GROUP BY `banner_id`, `type`";
			$result = $this->db->query($sql);
			$stats = array();
			foreach ($result->rows as $row) {
				$type = $row['type'] == '1' ? 'viewed' : 'clicked';
				$stats[$row['banner_id']][$type] = $row['cnt'];
			}

			$sql = "SELECT b.banner_id,
								bd.name,
								b.banner_group_name
						FROM " . $this->db->table("banners") . " b
						LEFT JOIN " . $this->db->table("banner_descriptions") . " bd ON (b.banner_id = bd.banner_id) ";
		}

		$sql .= " WHERE bd.language_id = '" . $language_id . "'";
		if (!empty($filter['subsql_filter'])) {
			$sql .= " AND " . $filter['subsql_filter'];
		}

		$result = $this->db->query($sql);
		$index = array();
		if ($mode != 'total_only') {
			foreach ($result->rows as &$row) {
				$row['clicked'] = isset($stats[$row['banner_id']]['clicked']) ? $stats[$row['banner_id']]['clicked'] : 0;
				$row['viewed'] = isset($stats[$row['banner_id']]['viewed']) ? $stats[$row['banner_id']]['viewed'] : 0;
				if ($row['viewed'] > 0) {
					$row['percent'] = round($row['clicked'] * 100 / $row['viewed'], 2);
				}
				$index[] = $row['percent'];
			}
			unset($row);
			$output = $result->rows;
			// resort by percents
			array_multisort($index, SORT_DESC, $output);
		} else {
			$output = (int)$result->row['total'];
		}

		return $output;
	}
}