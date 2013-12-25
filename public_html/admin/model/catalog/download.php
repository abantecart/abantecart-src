<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2013 Belavier Commerce LLC

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
class ModelCatalogDownload extends Model {
	public function addDownload($data) {
		$this->db->query("INSERT INTO " . $this->db->table('downloads') . "
        	              SET filename  = '" . $this->db->escape($data[ 'filename' ]) . "',
        	                  mask = '" . $this->db->escape($data[ 'mask' ]) . "',
      	                  	  max_downloads = '" . (int)$data[ 'max_downloads' ] . "',
      	                  	  expire_days = '" . (int)$data[ 'expire_days' ] . "',
      	                  	  sort_order = '" . (int)$data[ 'sort_order' ] . "',
      	                  	  activate = '" . $this->db->escape($data[ 'activate' ]) . "',
      	                  	  activate_order_status_id = '" . (int)$data[ 'activate_order_status_id' ] . "',
      	                  	  status = '" . (int)$data[ 'status' ] . "',
      	                  	  date_added = NOW()");

		$download_id = $this->db->getLastId();

		$this->language->replaceDescriptions('download_descriptions',
											 array('download_id' => (int)$download_id),
											 array($this->language->getContentLanguageID() => array('name' => $data[ 'name' ] )));

		$this->addDownloadAttributeValues($download_id, $data['attributes'][0]);
		// assign download to product
		if (isset($data['product_id'])) {
			$this->mapDownload($download_id, $data['product_id']);
		}

		return $download_id;
	}

	public function editDownload($download_id, $data) {
		if(!(int) $download_id || !$data){
			return false;
		}

		$this->db->query("UPDATE " . $this->db->table('downloads')."
		        	              SET
		        	                  filename  = '" . $this->db->escape($data[ 'filename' ]) . "',
		        	                  mask = '" . $this->db->escape($data[ 'mask' ]) . "',
		      	                  	  max_downloads = '" . (int)$data[ 'max_downloads' ] . "',
		      	                  	  ".(isset($data['shared']) ? "shared = ".(int)$data['shared'].", " : '')."
		      	                  	  expire_days = '" . (int)$data[ 'expire_days' ] . "',
		      	                  	  sort_order = '" . (int)$data[ 'sort_order' ] . "',
		      	                  	  activate = '" . $this->db->escape($data[ 'activate' ]) . "',
		      	                  	  activate_order_status_id = '" . (int)$data[ 'activate_order_status_id' ] . "',
		      	                  	  status = '" . (int)$data[ 'status' ] . "'
		      	           WHERE download_id = ".(int)$download_id);




		if (!empty($data[ 'name' ])) {
			$this->language->replaceDescriptions('download_descriptions',
												 array('download_id' => (int)$download_id),
												 array($this->language->getContentLanguageID() => array('name'=>$data[ 'name' ])) );
		}

		
		if (!empty($data['attributes'])) {
			$this->editDownloadAttributes($download_id, $data['attributes'][$download_id]);
		}

		if (isset($data['product_id'])) {
			$this->mapDownload($download_id,$data['product_id']);
		}
		
	}

	public function mapDownload($download_id, $product_id){
		$download_id = (int)$download_id;
		$product_id = (int)$product_id;
		if(!$product_id || !$download_id){
			return false;
		}

		$this->db->query("DELETE FROM " . $this->db->table('products_to_downloads')."
						  WHERE product_id = '" . (int)$product_id . "'
							AND download_id = '" . (int)$download_id."'");

		$this->db->query("INSERT INTO " . $this->db->table('products_to_downloads') . "
							SET
								product_id = '" . (int)$product_id . "',
								download_id = '" . (int)$download_id . "'");

	}
	public function unmapDownload($download_id, $product_id){
		$download_id = (int)$download_id;
		$product_id = (int)$product_id;
		if(!$product_id || !$download_id){
			return false;
		}

		$this->db->query("DELETE FROM " . $this->db->table('products_to_downloads') . "
						  WHERE product_id = '" . (int)$product_id . "'
							AND download_id = '" . (int)$download_id."'");
	}

	public function getDownloadMapList($download_id){
		$download_id = (int)$download_id;
		if(!$download_id){
			return array();
		}
		$output = array();

		$result = $this->db->query("SELECT  pd.product_id, pd.name
									  FROM " . $this->db->table('products_to_downloads') . " ptd
									  LEFT JOIN " . $this->db->table('product_descriptions') . " pd
										ON (pd.product_id = ptd.product_id AND pd.language_id = '".$this->language->getContentLanguageID()."')
									  WHERE ptd.download_id = '" . (int)$download_id."'");
		foreach($result->rows as $row){
			$output[$row['product_id']] = $row['name'];
		}
		return $output;
	}







	public function deleteDownload($download_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "downloads WHERE download_id = '" . (int)$download_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "download_descriptions WHERE download_id = '" . (int)$download_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "download_attribute_values WHERE download_id = '" . (int)$download_id . "'");
	}

	public function getDownload($download_id) {
		$query = $this->db->query("SELECT d.download_id,
										  dc.name,
										  filename,
										  mask,
										  max_downloads,
		      	                  	  	  expire_days,
		      	                  	  	  sort_order,
		      	                  	  	  activate,
		      	                  	      activate_order_status_id,
		      	                  	  	  status,
		      	                  	      date_added,
		      	                  	      date_modified
									FROM " . $this->db->table('downloads') . " d
									LEFT JOIN ".$this->db->table('download_descriptions')." dc
										ON d.download_id=dc.download_id AND dc.language_id = '".(int)$this->language->getContentLanguageID()."'
									WHERE d.download_id = '" . (int)$download_id . "'");
		return $query->row;
	}

	public function getDownloads($data = array(), $mode = 'default') {

		if (!empty($data[ 'content_language_id' ])) {
			$language_id = ( int )$data[ 'content_language_id' ];
		} else {
			$language_id = (int)$this->config->get('storefront_language_id');
		}

		if ($mode == 'total_only') {
			$total_sql = 'count(*) as total';
		}
		else {
			$total_sql = 'dd.*, d.*';
		}

		$sql = "SELECT $total_sql
				FROM " . DB_PREFIX . "downloads d
                LEFT JOIN " . DB_PREFIX . "download_descriptions dd
                	ON (d.download_id = dd.download_id AND dd.language_id = '" . $language_id . "')";

		if (!empty($data[ 'subsql_filter' ])){
			$sql .= " WHERE " . $data[ 'subsql_filter' ];
		}

		//If for total, we done bulding the query
		if ($mode == 'total_only') {
			$query = $this->db->query($sql);
			return $query->row[ 'total' ];
		}

		$sort_data = array(
			'name' => 'dd.name',
			'remaining' => 'd.remaining',
		);

		if (isset($data[ 'sort' ]) && in_array($data[ 'sort' ], array_keys($sort_data))) {
			$sql .= " ORDER BY " . $data[ 'sort' ];
		} else {
			$sql .= " ORDER BY dd.name";
		}

		if (isset($data[ 'order' ]) && (strtoupper($data[ 'order' ]) == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data[ 'start' ]) || isset($data[ 'limit' ])) {
			if ($data[ 'start' ] < 0) {
				$data[ 'start' ] = 0;
			}

			if ($data[ 'limit' ] < 1) {
				$data[ 'limit' ] = 20;
			}

			$sql .= " LIMIT " . (int)$data[ 'start' ] . "," . (int)$data[ 'limit' ];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalDownloads($data = array()) {
		return $this->getDownloads($data, 'total_only');
	}

	public function getDownloadDescriptions($download_id) {
		$download_description_data = array();

		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "download_descriptions
									WHERE download_id = '" . (int)$download_id . "'");

		foreach ($query->rows as $result) {
			$download_description_data[ $result[ 'language_id' ] ] = array( 'name' => $result[ 'name' ] );
		}

		return $download_description_data;
	}

	public function addDownloadAttributeValues($download_id, $data) {
		$attr_mngr = new AAttribute_Manager('download_attribute');
		$attribute_info = $attr_mngr->getAttributeTypeInfo('download_attribute');
		$attributes = $attr_mngr->getAttributes(array('attribute_type_id'=>$attribute_info['attribute_type_id']));

		foreach($attributes as $attribute){
			if(isset($data[$attribute['attribute_id']])){
				$value = serialize($data[$attribute['attribute_id']]);
				$this->db->query("INSERT INTO ".$this->db->table('download_attribute_values')." (attribute_id, download_id, attribute_value_ids)
								 VALUES ('".$attribute['attribute_id']."', '".$download_id."', '".$value."')");
			}
		}
	}

	public function editDownloadAttributes($download_id, $data) {
		$attr_mngr = new AAttribute_Manager('download_attribute');
		$attribute_info = $attr_mngr->getAttributeTypeInfo('download_attribute');
		$attributes = $attr_mngr->getAttributes(array('attribute_type_id'=>$attribute_info['attribute_type_id']));

		foreach($attributes as $attribute){
			if(isset($data[$attribute['attribute_id']])){
				$value = serialize($data[$attribute['attribute_id']]);

				$this->db->query( "DELETE
								   FROM ".$this->db->table('download_attribute_values')."
								   WHERE attribute_id = '".$attribute['attribute_id']."' AND download_id = '".$download_id."'");

				$this->db->query("INSERT INTO ".$this->db->table('download_attribute_values')."
								SET attribute_value_ids = '".$this->db->escape($value)."',
									attribute_id = '".$attribute['attribute_id']."',
									download_id = '".$download_id."'");
			}
		}
	}

	public function getDownloadAttributes($download_id) {
		$attr_mngr = new AAttribute_Manager('download_attribute');
		$attribute_info = $attr_mngr->getAttributeTypeInfo('download_attribute');
		$attributes = $attr_mngr->getAttributes(array('attribute_type_id'=>$attribute_info['attribute_type_id']));
		$ids = array();
		foreach($attributes as $attribute){
			$ids[] = (int)$attribute['attribute_id'];
			$attribute['values'] = $attr_mngr->getAttributeValues($attribute['attribute_id']);
			$output[$attribute['attribute_id']] = $attribute;
		}
		if($ids){
			$result = $this->db->query( "SELECT attribute_id, attribute_value_ids as value
										  FROM ".$this->db->table('download_attribute_values')."
										  WHERE attribute_id IN (".implode(',',$ids).") AND download_id = '".$download_id."'");

			foreach($result->rows as $row){
				if(isset($output[$row['attribute_id']])){
					$output[$row['attribute_id']]['selected_values'] = unserialize($row['value']);
				}
			}
		}
		return $output;
	}


//todo
	public function editOrderDownload($order_download_id){
		$this->db->query("UPDATE " . DB_PREFIX . "order_downloads
						  SET `filename` = '" . $this->db->escape($data[ 'download' ]) . "',
							   mask = '" . $this->db->escape(basename($data[ 'mask' ])) . "'
						  WHERE order_download_id='".(int)$order_download_id."'");
	}
}
