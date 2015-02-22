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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ModelLocalisationWeightClass extends Model {
	public function addWeightClass($data) {
		$this->db->query("INSERT INTO " . $this->db->table("weight_classes") . " SET value = '" . (float)$data['value'] . "'");
		
		$weight_class_id = $this->db->getLastId();
		
		foreach ($data['weight_class_description'] as $language_id => $value) {

			$this->language->replaceDescriptions('weight_class_descriptions',
											 array('weight_class_id' => (int)$weight_class_id),
											 array($language_id => array(
																		'title' => $value['title'],
																		'unit' => $value['unit']
											 )) );
		}
		
		$this->cache->delete('weight_class');

		return $weight_class_id;
	}
	
	public function editWeightClass($weight_class_id, $data) {
		if ( isset($data['value']) )
			$this->db->query("UPDATE " . $this->db->table("weight_classes") . " SET value = '" . (float)$data['value'] . "' WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		if ( isset($data['weight_class_description']) ) {
			foreach ($data['weight_class_description'] as $language_id => $value) {
				$update = array();
				if ( isset($value['title']) ) $update["title"] = $value['title'];
				if ( isset($value['unit']) ) $update["unit"] = $value['unit'];
				if ( !empty($update) ){
					$this->language->replaceDescriptions('weight_class_descriptions',
														 array('weight_class_id' => (int)$weight_class_id),
														 array($language_id => $update ));
				}
			}
		}
		
		$this->cache->delete('weight_class');	
	}
	
	public function deleteWeightClass($weight_class_id) {
		$this->db->query("DELETE FROM " . $this->db->table("weight_classes") . " WHERE weight_class_id = '" . (int)$weight_class_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("weight_class_descriptions") . " WHERE weight_class_id = '" . (int)$weight_class_id . "'");
		
		$this->cache->delete('weight_class');
	}
	
	public function getWeightClasses($data = array()) {

		if ( !empty($data['content_language_id']) ) {
			$language_id = ( int )$data['content_language_id'];
		} else {
			$language_id = (int)$this->session->data['content_language_id'];
		}

		if ($data) {
			$sql = "SELECT *, wc.weight_class_id
					FROM " . $this->db->table("weight_classes") . " wc
					LEFT JOIN " . $this->db->table("weight_class_descriptions") . " wcd
						ON (wc.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . $language_id . "') ";
		
			$sort_data = array(
				'title',
				'unit',
				'value'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY title";	
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
			$weight_class_data = $this->cache->get('weight_class', $language_id);

			if (!$weight_class_data) {
				$query = $this->db->query(
					"SELECT *, wc.weight_class_id
					FROM " . $this->db->table("weight_classes") . " wc
					LEFT JOIN " . $this->db->table("weight_class_descriptions") . " wcd
						ON (wc.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . $language_id . "')");
				$weight_class_data = $query->rows;
				$this->cache->set('weight_class', $weight_class_data, $language_id);
			}
			return $weight_class_data;
		}
	}
	
	public function getWeightClass($weight_class_id) {
		$query = $this->db->query("SELECT *, wc.weight_class_id
								   FROM " . $this->db->table("weight_classes") . " wc
								   LEFT JOIN " . $this->db->table("weight_class_descriptions") . " wcd
								        ON (wc.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->session->data['content_language_id'] . "')
								   WHERE wc.weight_class_id = '" . (int)$weight_class_id . "'");
		
		return $query->row;
	}

	public function getWeightClassDescriptionByUnit($unit) {
		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("weight_class_descriptions") . " 
									WHERE unit = '" . $this->db->escape($unit) . "'
										AND language_id = '" . (int)$this->session->data['content_language_id'] . "'");
		
		return $query->row;
	}
	
	public function getWeightClassDescriptions($weight_class_id) {
		$weight_class_data = array();
		
		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("weight_class_descriptions") . " 
									WHERE weight_class_id = '" . (int)$weight_class_id . "'");
				
		foreach ($query->rows as $result) {
			$weight_class_data[$result['language_id']] = array(
				'title' => $result['title'],
				'unit'  => $result['unit']
			);
		}
		
		return $weight_class_data;
	}
			
	public function getTotalWeightClasses() {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM " . $this->db->table("weight_classes") . " ");
		return $query->row['total'];
	}		
}
?>