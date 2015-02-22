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
class ModelLocalisationLengthClass extends Model {
	public function addLengthClass($data) {
		$this->db->query("INSERT INTO " . $this->db->table("length_classes") . " SET value = '" . (float)$data['value'] . "'");
		
		$length_class_id = $this->db->getLastId();
		
		foreach ($data['length_class_description'] as $language_id => $value) {
			$this->language->replaceDescriptions('length_class_descriptions',
											 array('length_class_id' => (int)$length_class_id),
											 array($language_id => array( 'title' => $value['title'],
																		  'unit' => $value['unit']
											 ) ));
		}
		
		$this->cache->delete('length_class');

		return $length_class_id;
	}
	
	public function editLengthClass($length_class_id, $data) {
		if ( isset($data['value']) )
			$this->db->query("UPDATE " . $this->db->table("length_classes") . " SET value = '" . (float)$data['value'] . "' WHERE length_class_id = '" . (int)$length_class_id . "'");

		if ( isset($data['length_class_description']) ) {
			foreach ($data['length_class_description'] as $language_id => $value) {
				$update = array();
				if ( isset($value['title']) ){
					$update["title"] = $value['title'];
				}
				if ( isset($value['unit']) ){
					$update["unit"] = $value['unit'];
				}
				if ( $update ){
					$this->language->replaceDescriptions('length_class_descriptions',
														 array('length_class_id' => (int)$length_class_id),
														 array($language_id => $update ));
				}
			}
		}
		
		$this->cache->delete('length_class');	
	}
	
	public function deleteLengthClass($length_class_id) {
		$this->db->query("DELETE FROM " . $this->db->table("length_classes") . " WHERE length_class_id = '" . (int)$length_class_id . "'");
		$this->db->query("DELETE FROM " . $this->db->table("length_class_descriptions") . " WHERE length_class_id = '" . (int)$length_class_id . "'");
		
		$this->cache->delete('length_class');
	}
	
	public function getLengthClasses($data = array()) {

		if ( !empty($data['content_language_id']) ) {
			$language_id = ( int )$data['content_language_id'];
		} else {
			$language_id = (int)$this->session->data['content_language_id'];
		}

		if ($data) {
			$sql = "SELECT *
					FROM " . $this->db->table("length_classes") . " wc
					LEFT JOIN " . $this->db->table("length_class_descriptions") . " wcd ON (wc.length_class_id = wcd.length_class_id AND wcd.language_id = '" . $language_id . "')";
		
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
			$length_class_data = $this->cache->get('length_class', $language_id);

			if (!$length_class_data) {
				$query = $this->db->query("SELECT *
											FROM " . $this->db->table("length_classes") . " wc
											LEFT JOIN " . $this->db->table("length_class_descriptions") . " wcd
												ON (wc.length_class_id = wcd.length_class_id AND wcd.language_id = '" . $language_id . "')");
	
				$length_class_data = $query->rows;
			
				$this->cache->set('length_class', $length_class_data, $language_id);
			}
			
			return $length_class_data;
		}
	}
	
	public function getLengthClass($length_class_id) {
		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("length_classes") . " wc
									LEFT JOIN " . $this->db->table("length_class_descriptions") . " wcd
										ON (wc.length_class_id = wcd.length_class_id AND wcd.language_id = '" . (int)$this->session->data['content_language_id'] . "')
									WHERE wc.length_class_id = '" . (int)$length_class_id . "'");
		
		return $query->row;
	}

	public function getLengthClassDescriptionByUnit($unit) {
		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("length_class_descriptions") . " 
									WHERE unit = '" . $this->db->escape($unit) . "'
										AND language_id = '" . (int)$this->session->data['content_language_id'] . "'");
		
		return $query->row;
	}
	
	public function getLengthClassDescriptions($length_class_id) {
		$length_class_data = array();
		
		$query = $this->db->query("SELECT *
									FROM " . $this->db->table("length_class_descriptions") . " 
									WHERE length_class_id = '" . (int)$length_class_id . "'");
				
		foreach ($query->rows as $result) {
			$length_class_data[$result['language_id']] = array(
				'title' => $result['title'],
				'unit'  => $result['unit']
			);
		}
		
		return $length_class_data;
	}
			
	public function getTotalLengthClasses() {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM " . $this->db->table("length_classes") . " ");
		
		return $query->row['total'];
	}		
}
?>