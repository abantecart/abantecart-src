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
class ModelCheckoutExtension extends Model {
	public function getExtensions($type, $sort_order = '') {
		$output = array();
		$query = $this->db->query("SELECT e.*, s.value as status
									FROM " . $this->db->table("extensions") . " e
									LEFT JOIN " . $this->db->table("settings") . " s ON ( TRIM(s.`group`) = TRIM(e.`key`) AND TRIM(s.`key`) = CONCAT(TRIM(e.`key`),'_status') )
									WHERE e.`type` = '" . $this->db->escape($type) . "'
										AND s.`value`='1' AND s.store_id = '".$this->config->get('config_store_id')."'");
		if($query->rows){

			foreach($query->rows as $row){
				$sort_order = $this->config->get($row['key'].'_sort_order');

				$sort_order = empty($sort_order) ? 1000 : (int)$sort_order;

				while(isset($output[$sort_order])){
					$sort_order++;
				}
				$output[$sort_order] = $row;
			}
		}
		ksort($output,SORT_NUMERIC);
		return $output;
	}
	
	public function getExtensionsByPosition($type, $position) {
		$extension_data = array();
		
		$query = $this->db->query("SELECT e.*, s.value as status
									FROM " . $this->db->table("extensions") . " e
									LEFT JOIN " . $this->db->table("settings") . " s ON ( TRIM(s.`group`) = TRIM(e.`key`) AND TRIM(s.`key`) = CONCAT(TRIM(e.`key`),'_status') )
									WHERE e.`type` = '" . $this->db->escape($type) . "'
										AND s.`value`='1' AND s.store_id = '".$this->config->get('config_store_id')."'");
		
		foreach ($query->rows as $result) {
			if ($this->config->get($result['key'] . '_status') && ($this->config->get($result['key'] . '_position') == $position)) {
				$extension_data[] = array(
					'code'       => $result['key'],
					'sort_order' => $this->config->get($result['key'] . '_sort_order')
				);
			}
		}
		
		$sort_order = array(); 
	  
		foreach ($extension_data as $key => $value) {
      		$sort_order[$key] = $value['sort_order'];
    	}

    	array_multisort($sort_order, SORT_ASC, $extension_data);

    	return $extension_data;
	}


	public function getSettings($extension_name, $store_id = 0 ) {
		$data = array();
		if ( $store_id == 0 ) {
			$store_id = $this->config->get('config_store_id');
		} 

		$query = $this->db->query(
			"SELECT *
			FROM " . $this->db->table("settings") . " 
			WHERE `group` = '" . $this->db->escape($extension_name) . "'
					AND store_id = '".(int)$store_id."'" );
		foreach ($query->rows as $result) {
			$value = $result['value'];
			if (is_serialized($value)) {
				$value = unserialize($value);
			}
			$data[$result['key']] = $value;
		}
		return $data;
	}

	/*
		Function to get image details based on RL path or RL ID
	*/
	public function getSettingImage( $rl_image ) {
		$image_data = array();
		if ( !has_value( $rl_image ) ) {
			return array();
		} 
		
		$resource = new AResource('image');
		if (is_numeric($rl_image)) {
		    // consider this is a pure image resource ID 
		    $image_data = $resource->getResource( $rl_image );
		} else {
		    $image_data = $resource->getResource( $resource->getIdFromHexPath(str_replace('image/', '', $rl_image)) );
		}
	
		return $image_data;
	}

}
?>