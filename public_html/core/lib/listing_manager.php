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

class AListingManager extends AListing {
	protected $registry;
	public    $errors = 0;
	protected   $custom_block_id;
	public    $data_sources;

	//NOTE: This class is loaded in INIT for admin only
	public function __construct($custom_block_id) {
		parent::__construct($custom_block_id);
		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to access class AListingManager');
		}
	}

	public function saveCustomListItem($data) {
		$custom_block_id = (int)$this->custom_block_id;
		if( !isset($data['data_type']) && isset( $data['listing_datasource'] ) ){
			$listing_properties = $this->getListingDataSources();
			$data['data_type'] = $listing_properties[$data['listing_datasource']]['data_type'];
		}

		$result = $this->db->query("SELECT *
									FROM  " . $this->db->table("custom_lists") . " 
									WHERE custom_block_id = '".$custom_block_id."'
											AND id='".$data['id']."'
											AND data_type='".$data['data_type']."'");

		if($result->num_rows && $custom_block_id){
			$this->db->query(  "UPDATE " . $this->db->table("custom_lists") . " 
								SET custom_block_id = '".$custom_block_id."'
								".( !is_null($data['sort_order']) ? ", sort_order = '".(int)$data['sort_order']."'" : "")."
								WHERE custom_block_id = '".$custom_block_id."'
									  AND id='".$data['id']."'
										AND data_type='".$data['data_type']."'");
		}else{
			$this->db->query("INSERT INTO " . $this->db->table("custom_lists") . " 
								( custom_block_id,
								  data_type,
								  id,
								  sort_order,
								  date_added )
							  VALUES ('".$custom_block_id."',
							          '".$data['data_type']."',
							          '".(int)$data['id']."',
							          '" . ( int )$data [ 'sort_order' ] . "',
								      NOW())");
		}

		return true;
	}
	
	// delete one item from custom list of custom listing block
	public function deleteCustomListItem($data) {

		$listing_properties = $this->getListingDataSources();
		if( !isset($data['data_type']) && isset( $data['listing_datasource'] ) ){
			$data['data_type'] = $listing_properties[$data['listing_datasource']]['data_type'];
		}

		$sql = "DELETE FROM  " . $this->db->table("custom_lists") . " 
									WHERE custom_block_id = '".(int)$this->custom_block_id."'
											AND id='".$data['id']."'
											AND data_type='".$data['data_type']."'";
		$this->db->query( $sql);
	}

	// delete all custom list of custom listing block
	public function deleteCustomListing() {
		$sql = "DELETE FROM  " . $this->db->table("custom_lists") . " WHERE custom_block_id = '".(int)$this->custom_block_id."'";
		$this->db->query( $sql );
	}
}