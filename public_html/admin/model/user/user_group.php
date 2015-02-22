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
class ModelUserUserGroup extends Model {

	public function addUserGroup($data) {

		if(!isset($data['permission'])){
			$controllers = $this->getAllControllers();
			$types = array('access','modify');
			foreach($types as $type){
				foreach($controllers as $controller){
					$data['permission'][$type][$controller] = 0;
				}
			}
		}
		$this->db->query("INSERT INTO " . $this->db->table("user_groups") . " 
						  SET name = '" . $this->db->escape($data['name']) . "',
							  permission = '" . (isset($data['permission']) ? serialize($data['permission']) : '') . "'");
		return $this->db->getLastId();
	}
	
	public function editUserGroup($user_group_id, $data) {

		$user_group_id = !$user_group_id ? $this->addUserGroup($data['name']) : $user_group_id;
		$user_group = $this->getUserGroup($user_group_id);

		$update = array();
		if ( isset($data['name']) )	$update[] = "name = '".$this->db->escape($data['name'])."'";

		if ( isset($data['permission']) )	{
			$p = $user_group['permission'];

			if ( isset($data['permission']['access']) ) {
				foreach( $data['permission']['access'] as $controller => $value ){
						$value = !in_array($value, array(null,0,1)) ? 0 : $value;
						$p['access'][$controller] = $value;
						if(!isset($p['modify'][ $controller ]) && !isset($data['permission']['modify'][$controller])){
							$p['modify'][ $controller ] = 0;
						}
				}
			}
			if ( isset($data['permission']['modify'])){
				foreach( $data['permission']['modify'] as $controller => $value ){
						$value = !in_array($value, array(null,0,1)) ? 0 : $value;
						$p['modify'][ $controller ] = $value;
						if(!isset($p['access'][ $controller ]) && !isset($data['permission']['access'][$controller])){
							$p['access'][ $controller ] = 0;
						}
				}
			}
			$update[] = "permission = '".serialize($p)."'";
		}

		if ( !empty($update) ){
			$this->db->query("UPDATE " . $this->db->table("user_groups") . " SET ". implode(',', $update) ." WHERE user_group_id = '" . (int)$user_group_id . "'");
		}
	}
	
	public function deleteUserGroup($user_group_id) {
		$this->db->query("DELETE FROM " . $this->db->table("user_groups") . " WHERE user_group_id = '" . (int)$user_group_id . "'");
	}

	public function addPermission($user_id, $type, $page) {
		$user_query = $this->db->query("SELECT DISTINCT user_group_id FROM " . $this->db->table("users") . " WHERE user_id = '" . (int)$user_id . "'");
		
		if ($user_query->num_rows) {
			$user_group_query = $this->db->query("SELECT DISTINCT * FROM " . $this->db->table("user_groups") . " WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");
		
			if ($user_group_query->num_rows) {
				$data = unserialize($user_group_query->row['permission']);
				$data[$type][$page] = 1;
				$this->db->query("UPDATE " . $this->db->table("user_groups") . " SET permission = '" . serialize($data) . "' WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");
			}
		}
	}
	
	public function getUserGroup($user_group_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . $this->db->table("user_groups") . " WHERE user_group_id = '" . (int)$user_group_id . "'");
		
		$user_group = array(
			'name'       => $query->row['name'],
			'permission' => unserialize($query->row['permission'])
		);
		return $user_group;
	}
	
	public function getUserGroups($data = array()) {
		$sql = "SELECT *
				FROM " . $this->db->table("user_groups") . " 
				ORDER BY name";
			
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
	
	public function getTotalUserGroups() {
      	$sql = "SELECT COUNT(*) AS total FROM " . $this->db->table("user_groups") . " ";
      	$query = $this->db->query($sql);

		return $query->row['total'];
	}

	/**
	 * method returns array with all controllers of admin section
	 * @param string $order
	 * @return array
	 */
	public function getAllControllers($order = 'asc'){

		$ignore = array('index/home',
						'common/layout',
						'common/login',
						'common/logout',
						'error/not_found',
						'error/permission',
						'common/footer',
						'common/header',
						'common/menu');

		$controllers_list = array();
		$files_pages = glob( DIR_APP_SECTION . 'controller/pages/*/*.php');
		$files_response = glob( DIR_APP_SECTION . 'controller/responses/*/*.php');
		$files = array_merge( $files_pages, $files_response);

		// looking for controllers inside core
		foreach ($files as $file) {
			$data = explode('/', dirname($file));
			$controller = end($data) . '/' . basename($file, '.php');
			if (!in_array($controller, $ignore)) {
				$controllers_list[] = $controller;
			}
		}
		// looking for controllers inside extensions
		$files_pages = glob( DIR_EXT . '/*/admin/controller/pages/*/*.php');
		$files_response = glob(  DIR_EXT . '/*/admin/controller/responses/*/*.php');
		$files = array_merge( $files_pages, $files_response);
		foreach ($files as $file) {
			$data = explode('/', dirname($file));
			$controller = end($data) . '/' . basename($file, '.php');
			if (!in_array($controller, $ignore)) {
				$controllers_list[] = $controller;
			}
		}

		$controllers_list = array_unique($controllers_list);
		sort($controllers_list,SORT_STRING);
		if($order=='desc'){
			$controllers_list = array_reverse($controllers_list);
		}
		return $controllers_list;
	}
}
