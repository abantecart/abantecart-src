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
class ModelUserUser extends Model {
	public function addUser($data) {
		$this->db->query("INSERT INTO " . $this->db->table("users") . " 
						  SET username = '" . $this->db->escape($data['username']) . "',
						      password = '" . $this->db->escape(AEncryption::getHash($data['password'])) . "',
						      firstname = '" . $this->db->escape($data['firstname']) . "',
						      lastname = '" . $this->db->escape($data['lastname']) . "',
						      email = '" . $this->db->escape($data['email']) . "',
						      user_group_id = '" . (int)$data['user_group_id'] . "',
						      status = '" . (int)$data['status'] . "',
						      date_added = NOW()");
		return $this->db->getLastId();
	}
	
	public function editUser($user_id, $data) {

		$fields = array('username', 'firstname', 'lastname', 'email', 'user_group_id', 'status',);
		$update = array();
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = $f." = '".$this->db->escape($data[$f])."'";
		}
		if ( !empty($data['password']) )
				$update[] = "password = '". $this->db->escape(AEncryption::getHash($data['password'])) ."'";

		if ( !empty($update) ){
			$sql = "UPDATE " . $this->db->table("users") . " SET ". implode(',', $update) ." WHERE user_id = '" . (int)$user_id . "'";
			$this->db->query( $sql );
		}
	}
	
	public function deleteUser($user_id) {
		$this->db->query("DELETE FROM " . $this->db->table("users") . " WHERE user_id = '" . (int)$user_id . "'");
	}
	
	public function getUser($user_id) {
		$query = $this->db->query("SELECT * FROM " . $this->db->table("users") . " WHERE user_id = '" . (int)$user_id . "'");
	
		return $query->row;
	}
	
	public function getUsers($data = array(), $mode = 'default') {
		if ($mode == 'total_only') {
			$sql = "SELECT count(*) as total FROM " . $this->db->table("users") . " ";
		} else {
			$sql = "SELECT * FROM " . $this->db->table("users") . " ";		
		}
		if ( !empty($data['subsql_filter']) )
			$sql .= " WHERE ".$data['subsql_filter'];

	    //If for total, we done bulding the query
		if ($mode == 'total_only') {
	    	$query = $this->db->query($sql);
	    	return $query->row['total'];
	    }

		$sort_data = array(
			'username',
			'user_group_id',
			'status',
			'date_added'
		);	
			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY username";	
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
	}

	public function getTotalUsers($data = array()) {
 		return $this->getUsers($data, 'total_only');
	}

	public function getTotalUsersByGroupId($user_group_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . $this->db->table("users") . " WHERE user_group_id = '" . (int)$user_group_id . "'");
		
		return $query->row['total'];
	}
}
