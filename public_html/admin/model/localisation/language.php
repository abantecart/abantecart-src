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
class ModelLocalisationLanguage extends Model {
	public function addLanguage($data) {
		$this->db->query("INSERT INTO " . $this->db->table("languages") . " 
							SET name = '" . $this->db->escape($data['name']) . "',
								code = '" . $this->db->escape($data['code']) . "',
								locale = '" . $this->db->escape($data['locale']) . "',
								directory = '" . $this->db->escape($data['directory']) . "',
								filename = '" . $this->db->escape($data['directory']) . "',
								sort_order = '" . $this->db->escape($data['sort_order']) . "',
								status = '" . (int)$data['status'] . "'");
		
		$this->cache->delete('language');
		
		$language_id = $this->db->getLastId();
		
		//add menu items for new language
		$menu = new AMenu_Storefront();
		$menu->addLanguage( (int)$language_id );

		//language data is copied/translated in a seprate process.
		return $language_id;
	}
	
	public function editLanguage($language_id, $data) {
		$update_data = array();
		foreach ( $data as $key => $val ) {
			$update_data[] = "`$key` = '" . $this->db->escape($val) . "' ";
		}
		$this->db->query("UPDATE " . $this->db->table("languages") . " SET ".implode(',', $update_data)." WHERE language_id = '" . (int)$language_id . "'");
				
		$this->cache->delete('language');
	}
	
	public function deleteLanguage($language_id) {
		$this->db->query("DELETE FROM " . $this->db->table("languages") . " WHERE language_id = '" . (int)$language_id . "'");
		
		$this->language->deleteAllLanguageEntries($language_id);

		//too many changes and better clear all cache
		$this->cache->delete('*');
				
		//delete menu items for given language
		$menu = new AMenu_Storefront();
		$menu->deleteLanguage( (int)$language_id );
	}
	
	public function getLanguage($language_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . $this->db->table("languages") . " WHERE language_id = '" . (int)$language_id . "'");
		$result = $query->row;
		if(!$result['image']){
			if(file_exists(DIR_ROOT.'/admin/language/'.$result['directory'].'/flag.png')){
				$result['image'] = HTTP_ABANTECART.'admin/language/'.$result['directory'].'/flag.png';
			}
		}else{
			$result['image'] = HTTP_ABANTECART.$result['image'];
		}
		return $query->row;
	}

	public function getLanguages($data = array(), $mode = 'default') {
        if ($data || $mode == 'total_only') {
        	$filter = (isset($data['filter']) ? $data['filter'] : array());
			if ($mode == 'total_only') {
				$sql = "SELECT count(*) as total FROM " . $this->db->table("languages") . " ";
			}
			else {
				$sql = "SELECT * FROM " . $this->db->table("languages") . " ";
			}
			
			if (isset($filter['status']) && !is_null($filter['status'])) { 
				$sql .= " WHERE `status` = '".$this->db->escape( $filter['status'] )."' ";			
			} else {
				$sql .= " WHERE `status` like '%' ";			
			}

			if (isset($filter['name']) && !is_null($filter['name'])) {
				$sql .= " AND `name` LIKE '%".$this->db->escape( $filter['name'] )."%' ";
			}
			
			if ( !empty($data['subsql_filter']) ) {
				$sql .= " AND ".$data['subsql_filter'];
			}

			//If for total, we done bulding the query
			if ($mode == 'total_only') {
			    $query = $this->db->query($sql);
		    	return $query->row['total'];
			}

			$sort_data = array(
				'name',
				'code',
				'sort_order'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY sort_order, name";	
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
			foreach($result as $i=>$row){
				if(empty($row['image'])){
					if(file_exists(DIR_ROOT.'/admin/language/'.$row['directory'].'/flag.png')){
						$result[$i]['image'] = 'admin/language/'.$row['directory'].'/flag.png';
					}
				}else{
					$result[$i]['image'] = $row['image'];
				}
			}
			return $result;
		} else {
			$language_data = $this->cache->get('language');
		
			if (!$language_data) {
				$query = $this->db->query( "SELECT *
											FROM " . $this->db->table("languages") . " 
											ORDER BY sort_order, name");
	
    			foreach ($query->rows as $result) {
					if(empty($result['image'])){
						if(file_exists(DIR_ROOT.'/admin/language/'.$result['directory'].'/flag.png')){
							$result['image'] = 'admin/language/'.$result['directory'].'/flag.png';
						}
					}

      				$language_data[$result['code']] = array(
        				'language_id' => $result['language_id'],
        				'name'        => $result['name'],
        				'code'        => $result['code'],
						'locale'      => $result['locale'],
						'image'       => $result['image'],
						'directory'   => $result['directory'],
						'filename'    => $result['filename'],
						'sort_order'  => $result['sort_order'],
						'status'      => $result['status']
      				);
    			}
				$this->cache->set('language', $language_data);
			}
		
			return $language_data;			
		}
	}

	public function getTotalLanguages( $data = array() ) {
		return $this->getLanguages( $data, 'total_only' );
	}
}
