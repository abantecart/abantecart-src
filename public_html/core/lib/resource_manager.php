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
 * Class AResourceManager
 * @property ADB $db
 * @property AHtml $html
 * @property ACache $cache
 * @property AConfig $config
 * @property ALanguageManager $language
 */
class AResourceManager extends AResource {
	protected $registry;
	public $error = array();

	public function __construct() {
		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to change resources');
		}
		$this->registry = Registry::getInstance();
	}

    public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	/**
	 * @param string $type
	 */
	public function setType( $type ) {
        if ( $type ) {
			$this->type = $type;
			//get type details
	        $this->_loadType();

			if ( !$this->type_id ) {
				$message = "Error: Incorrect or missing resource type ".$type;
				$error = new AError ( $message );
				$error->toLog()->toDebug();
			}
		}
    }

    public function getResourceTypes() {
		return $this->getAllResourceTypes();
	}
    public function getResourceTypeByName($type_name) {
		$all_types = $this->getAllResourceTypes();
		foreach($all_types as $t){
			if($t['type_name']==$type_name){
				return $t;
			}
		}
		return array();
	}

	public function addResourceType () {
        $cache_name = 'resources.types';
        $this->cache->delete($cache_name,'',(int)$this->config->get('config_store_id'));
	}

    public function deleteResourceType() {
        $cache_name = 'resources.types';
        $this->cache->delete($cache_name,'',(int)$this->config->get('config_store_id'));
    }

	public function buildResourcePath($resource_id, $file_path){
		if(!(int)$resource_id || empty($file_path)){
			return false;
		}
		$resource_path = $this->getHexPath($resource_id) . strtolower(substr(strrchr($file_path, '.'), 0));
		$resource_dir = dirname($resource_path);
		if ( !is_dir(DIR_RESOURCE . $this->type_dir . $resource_dir ) ) {
			$path = '';
			$directories = explode('/', $resource_dir);
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
				if (!is_dir(DIR_RESOURCE . $this->type_dir . $path)) {
					@mkdir(DIR_RESOURCE . $this->type_dir . $path, 0777);
					chmod(DIR_RESOURCE . $this->type_dir . $path, 0777);
				}
			}
		}
		if (is_file(DIR_RESOURCE . $this->type_dir . $resource_path)) {
			unlink(DIR_RESOURCE . $this->type_dir . $resource_path);
		}
		return $resource_path;
	}

    /**
     * upload resources to directory with type name (example: image)
     *
     * @param array $resource
     * @return int resource id
     */
    public function addResource( $resource ) {

        if ( !$this->type_id ) {
			$message = "Error: Incorrect or missing resource type. Please set type using setType() method ";
			$error = new AError ( $message );
			$error->toLog()->toDebug();
            return false;
		}

        $sql = "INSERT INTO " . $this->db->table("resource_library") . " 
                    SET type_id = '".$this->type_id."',
                        date_added = NOW()";
        $this->db->query($sql);
        $resource_id = $this->db->getLastId();

        if ( !empty($resource['resource_path']) ) {
			$resource_path = $this->buildResourcePath($resource_id, $resource['resource_path']);
			if($resource_path===false){
				$message = "Error: Incorrect or missing resource path. Please set correct path to build internal path of resource. ";
				$error = new AError ( $message );
				$error->toLog()->toDebug();
				return false;
			}

            if ( !rename(DIR_RESOURCE . $this->type_dir . $resource['resource_path'], DIR_RESOURCE . $this->type_dir . $resource_path ) ) {
                $message = "Error: Cannot move resource to resources directory.";
                $error = new AError ( $message );
                $error->toLog()->toDebug();
                return false;
            }
        } else {
            $resource_path = '';
        }

        foreach ( $resource['name'] as $language_id => $name ) {
			if($this->config->get('translate_override_existing') && $language_id != $resource['language_id'] ){
				continue;
			}

			$this->language->replaceDescriptions('resource_descriptions',
												 array('resource_id' => (int)$resource_id),
												 array((int)$language_id => array(
													 'name' => $resource['name'][$language_id],
													 'title' => $resource['title'][$language_id],
													 'description' => $resource['description'][$language_id],
													 'resource_path' => $resource_path,
													 'resource_code' => $resource['resource_code'],
													 'date_added' => date('Y-m-d H:i:s')
												 )) );
        }

        $this->cache->delete('resources.'.$this->type);

        return $resource_id;

    }

	/**
	 * @param int $resource_id
	 * @param array $data
	 * @return bool
	 */
	public function updateResource( $resource_id, $data ) {

		$resource_id = (int)$resource_id;
		if(!$resource_id){ return false;}

        $resource = parent::getResource($resource_id);
        if ( isset($data['resource_code']) ){
            $_update['resource_code'] = $data['resource_code'];
		}

        $fields = array('name', 'title', 'description');
		if( $data['name'] ){
			foreach ( $data['name'] as $language_id => $name ) {
				if($this->config->get('translate_override_existing') && $language_id != $data['language_id'] ){
					continue;
				}

				 $update = $_update;

				foreach ( $fields as $f ) {
					if ( isset($data[$f][$language_id]) )
						$update[$f] = $data[$f][$language_id];
				}

				$this->language->replaceDescriptions('resource_descriptions',
													 array('resource_id' => (int)$resource_id),
													 array((int)$language_id => $update) );
			}
		}
		if($data['resource_path']){
			$sql = "UPDATE ".$this->db->table('resource_descriptions')."
					SET resource_path='".$this->db->escape($data['resource_path'])."'
					WHERE resource_id =  ".$resource_id;
			$this->db->query( $sql );
			$this->deleteThumbnail($resource_id);
		}


        $this->cache->delete('resources.'. $resource_id);
        $this->cache->delete('resources.'.$resource['type_name']);
        return true;
    }

	public function deleteThumbnail($resource_id='') {
		$resource_id = (int)$resource_id;
		if(!$resource_id){
			return false;
		}

		//get $resource with all translations
		$resource = $this->getResource($resource_id);
		//skip when resource is html-code or does not exists
		if(!$resource['resource_path'] || !$resource){
			return false;
		}

		foreach($resource['name'] as $lang_id => $name){
			$name = preg_replace('/[^a-zA-Z0-9]/', '_', $name);
			$filemask = DIR_IMAGE . 'thumbnails/' . dirname($resource['resource_path']) . '/' . $name . '-' . $resource_id . '-*';

			$file_list = glob($filemask, GLOB_NOSORT);

			if ($file_list){
				foreach ($file_list as $thumb){
					if (is_file($thumb)){
						unlink($thumb);
					}
				}
			}

		}
		return true;
  	}

    /**
     * remove resource with option to delete the file
     *
     * @param $resource_id
     * @return bool
     */
    public function deleteResource($resource_id) {
        $resource = $this->getResource($resource_id);
        if ( !$resource ) {
            return false;
        }
		if( $this->isMapped($resource_id) ){
			return false;
		}

        if ( $resource['resource_path'] && is_file( DIR_RESOURCE . $resource['type_name'] . '/' . $resource['resource_path']) ) {
            unlink( DIR_RESOURCE.$resource['type_name'].'/'.$resource['resource_path'] );
        }
	    //remove thumbnail before removing
	    $this->deleteThumbnail($resource_id);

        $this->db->query("DELETE FROM " . $this->db->table("resource_map") . " WHERE resource_id = '".(int)$resource_id."' ");
        $this->db->query("DELETE FROM " . $this->db->table("resource_descriptions") . " WHERE resource_id = '".(int)$resource_id."' ");
        $this->db->query("DELETE FROM " . $this->db->table("resource_library") . " WHERE resource_id = '".(int)$resource_id."' ");

        $this->cache->delete('resources.'. $resource_id);
        $this->cache->delete('resources.'. $resource['type_name']);

        return true;
    }

	/**
	 * @param array $resource_ids
	 * @param string $object_name
	 * @param int $object_id
	 * @return bool|null
	 */
	public function deleteResources($resource_ids, $object_name='', $object_id=0) {
		if(!$resource_ids || !is_array($resource_ids)){
			return null;
		}
		$result = true;
		$ids = array();
		foreach( $resource_ids as $resource_id ){
			$resource_id = (int)$resource_id;
			$resource = $this->getResource($resource_id);
			if ( !$resource ) {
				continue;
			}
			$mapped_cnt = $this->isMapped($resource_id);
			if($mapped_cnt==1 && $this->isMapped($resource_id,$object_name,$object_id)){
				$res = $this->unmapResource( $object_name,$object_id, $resource_id);
				if(!$res){
					$this->error[] = $resource['name'][ $this->language->getContentLanguageID() ] .' cannot be deleted. Unlink it first.';
					$result = false;
					continue;
				}
			}elseif( $mapped_cnt ){
				$this->error[] = $resource['name'][ $this->language->getContentLanguageID() ] .' cannot be deleted. Unlink it first.';
				$result = false;
				continue;
			}

			$ids[] = $resource_id;
			$this->cache->delete('resources.'. $resource_id);

			if ( $resource['resource_path'] && is_file( DIR_RESOURCE . $resource['type_name'] . '/' . $resource['resource_path']) ) {
				unlink( DIR_RESOURCE.$resource['type_name'].'/'.$resource['resource_path'] );
			}
		}

		if(!$ids){
			return $result;
		}

		$ids = implode(', ',$ids);
		$this->db->query("DELETE FROM " . $this->db->table("resource_map") . " WHERE resource_id IN (".$ids.")");
		$this->db->query("DELETE FROM " . $this->db->table("resource_descriptions") . " WHERE resource_id IN (".$ids.")");
		$this->db->query("DELETE FROM " . $this->db->table("resource_library") . " WHERE resource_id IN (".$ids.")");

        $this->cache->delete('resources.'. $resource['type_name']);
        return $result;
    }

	/**
	 * @param string $object_name
	 * @param int $object_id
	 * @param int $resource_id
	 * @return null
	 */
	public function mapResource (  $object_name, $object_id, $resource_id ) {

        $resource = $this->getResource($resource_id);
        if ( empty($resource) ) {
            return null;
        }

		$sql = "SELECT resource_id FROM " . $this->db->table("resource_map") . " 
                WHERE resource_id = '".(int)$resource_id."'
                      AND object_name = '".$this->db->escape($object_name)."'
                      AND object_id = '".(int)$object_id."'";
        $result = $this->db->query($sql);

        if ( $result->num_rows ) return null;

		//need to get sort order
		$sql = "SELECT MAX(sort_order) as sort_order
				FROM " . $this->db->table("resource_map") . " 
				WHERE object_name = '".$this->db->escape($object_name)."'
					  AND object_id = '".(int)$object_id."'";
		$result = $this->db->query($sql);
		$new_sort_order = $result->row['sort_order']+1;

        $sql = "INSERT INTO " . $this->db->table("resource_map") . " 
                    SET resource_id = '".(int)$resource_id."',
                        object_name = '".$this->db->escape($object_name)."',
                        object_id = '".(int)$object_id."',
                        sort_order = '".(int)$new_sort_order."',
                        date_added = NOW()";
        $this->db->query($sql);

        $this->cache->delete('resources.'. $resource_id);
        $this->cache->delete('resources.'. $object_name.'.'.$resource_id);
        $this->cache->delete('resources.'. $resource['type_name']);
	}

	/**
	 * @param array $resource_ids
	 * @param string $object_name
	 * @param int $object_id
	 * @return bool|null
	 */
	public function mapResources (  $resource_ids, $object_name, $object_id ) {
		if(!$object_name && !(int)$object_id){ return null; }
		if(!$resource_ids || !is_array($resource_ids)){
			return null;
		}
		$ids = array();
		foreach($resource_ids as $id){
			$resource = $this->getResource($id);
			if ( empty($resource) ) {
				continue;
			}
			//skip already mapped
			$sql = "SELECT resource_id
			 		FROM " . $this->db->table("resource_map") . " 
					WHERE resource_id = '".(int)$id."'
						  AND object_name = '".$this->db->escape($object_name)."'
						  AND object_id = '".(int)$object_id."'";
			$result = $this->db->query($sql);

			if ( $result->num_rows ) { continue; }

			$ids[] = (int)$id;
			$this->cache->delete('resources.'. $id);
			$this->cache->delete('resources.'. $object_name.'.'.$id);
			$this->cache->delete('resources.'. $resource['type_name']);
		}

		foreach( $ids as $resource_id ){
			//need to get sort order
			$sql = "SELECT MAX(sort_order) as sort_order
					FROM " . $this->db->table("resource_map") . " 
					WHERE object_name = '".$this->db->escape($object_name)."'
						  AND object_id = '".(int)$object_id."'";
			$result = $this->db->query($sql);
			$new_sort_order = $result->row['sort_order']+1;

			$sql = "INSERT INTO " . $this->db->table("resource_map") . " 
						SET resource_id = '".(int)$resource_id."',
							object_name = '".$this->db->escape($object_name)."',
							object_id = '".(int)$object_id."',
							sort_order = '".(int)$new_sort_order."',
							date_added = NOW()";
			$this->db->query($sql);
		}
		return true;
	}

	/**
	 * @param string $object_name
	 * @param int $object_id
	 * @param int $resource_id
	 * @return bool
	 */
	public function unmapResource (  $object_name, $object_id, $resource_id ) {

        $resource = $this->getResource($resource_id);
        if ( empty($resource) ) {
            return false;
        }

		$sql = "DELETE FROM " . $this->db->table("resource_map") . " 
                    WHERE resource_id = '".(int)$resource_id."'
                        AND object_name = '".$this->db->escape($object_name)."'
                        AND object_id = '".(int)$object_id."'";
        $this->db->query($sql);

        $this->cache->delete('resources.'. $resource_id);
		$this->cache->delete('resources.'. $object_name.'.'.$resource_id);
        $this->cache->delete('resources.'. $resource['type_name']);

		return true;
	}

	/**
	 * @param array $resource_ids
	 * @param string $object_name
	 * @param int $object_id
	 * @return bool|null
	 */
	public function unmapResources ( $resource_ids, $object_name, $object_id ) {
		if(!$object_name && !(int)$object_id){ return null; }
		if(!$resource_ids || !is_array($resource_ids)){
			return null;
		}
		$ids = array();
		foreach($resource_ids as $id){
			$resource = $this->getResource($id);
			if ( empty($resource) ) {
				continue;
			}
			$ids[] = (int)$id;
			$this->cache->delete('resources.'. $id);
			$this->cache->delete('resources.'. $object_name.'.'.$id);
			$this->cache->delete('resources.'. $resource['type_name']);
		}

		$sql = "DELETE FROM " . $this->db->table("resource_map") . " 
				WHERE resource_id IN (".implode(", ",$ids).")
					AND object_name = '".$this->db->escape($object_name)."'
					AND object_id = '".(int)$object_id."'";
        $this->db->query($sql);
		return true;
	}

	/**
	 * @param array $data
	 * @param string $object_name
	 * @param int $object_id
	 * @return bool
	 */
	public function updateSortOrder ( $data, $object_name, $object_id ) {
		if(!$data || !is_array($data) ){
			return false;
		}

        foreach ( $data as $resource_id => $sort_order ) {
            $resource = $this->getResource($resource_id);
            if ( empty($resource) ) {
                continue;
            }
            $sql = "UPDATE " . $this->db->table("resource_map") . " 
                    SET sort_order = '".(int)$sort_order."'
                    WHERE resource_id = '".(int)$resource_id."'
                            AND object_name = '".$this->db->escape($object_name)."'
                            AND object_id = '".(int)$object_id."'";
            $this->db->query($sql);

            $this->cache->delete('resources.'. $resource_id);
			$this->cache->delete('resources.'. $object_name.'.'.$resource_id);
            $this->cache->delete('resources.'. $resource['type_name']);
        }
		return true;
    }

	/**
	 * @param int $resource_id
	 * @param int $language_id
	 * @return array|null
	 */
	public function getResource ( $resource_id, $language_id = 0 ) {
        if ( !$resource_id ) {
			return null;
	    }
        if ( $language_id ) {
            return parent::getResource($resource_id, $language_id);
        }

        $languages = $this->language->getAvailableLanguages();
        $resource = parent::getResource($resource_id);
        unset($resource['name'], $resource['title'], $resource['description']);
        foreach ( $languages as $lang ) {
            $result = parent::getResource($resource_id, $lang['language_id']);
            $resource['name'][ $lang['language_id'] ] = $result['name'];
            $resource['title'][ $lang['language_id'] ] = $result['title'];
            $resource['description'][ $lang['language_id'] ] = $result['description'];
        }

        return $resource;
    }

	/**
	 * @param array $data
	 * @return int
	 */
	public function getTotalResources($data) {
		return $this->getResourcesList($data,'total_only'); 
	}

	/**
	 * @param array $data
	 * @param string $mode
	 * @return array
	 */
	public function getResourcesList($data, $mode = 'default') {

		if ( (int)$data['language_id'] ) {
			$language_id = (int)$data['language_id'];
		} else {
			$language_id = (int)$this->language->getContentLanguageID();
		}

		if ($mode == 'total_only') {
			$top_sql = " count(*) as total ";
		}
		else {
			$top_sql = "  rl.resource_id,
        				  COALESCE(rl.date_added, '', rl.date_added) as date_added,
        				  rd.name,
        				  rd.title,
        				  rd.description,
        				  COALESCE(rd.resource_path,rdd.resource_path) as resource_path,
        				  COALESCE(rd.resource_code,rdd.resource_code) as resource_code,
        				  (SELECT COUNT(resource_id) FROM " . $this->db->table("resource_map") . " rm1 WHERE rm1.resource_id = rd.resource_id) as mapped ";
		}
		$sql = $where = $join = '';		
        $join = " LEFT JOIN " . $this->db->table("resource_descriptions") . " rd ON (rl.resource_id = rd.resource_id AND rd.language_id = '".$language_id."') ";
        $join .= " LEFT JOIN " . $this->db->table("resource_descriptions") . " rdd ON (rl.resource_id = rdd.resource_id AND rdd.language_id = '".$this->language->getDefaultLanguageID()."') ";

        if ( $data['sort'] == 'sort_order' || !empty($data['object_name']) || !empty($data['object_id']) ) {
            $top_sql .= ", rm.sort_order";
            $join .= " LEFT JOIN " . $this->db->table("resource_map") . " rm ON (rl.resource_id = rm.resource_id) ";
            $order = " ORDER BY rm.sort_order, rl.resource_id";
        }

        if ( !empty($data['keyword']) ) {
        	$where .= ($where ? " AND" : ' WHERE ');
            $where .= " ( LCASE(rd.name) LIKE '%" . $this->db->escape(strtolower($data['keyword'])) . "%'";
			$where .= " OR LCASE(rd.title) LIKE '%" . $this->db->escape(strtolower($data['keyword'])) . "%' )";
        }

        if ( !empty($data['type_id']) ) {
        	$where .= ($where ? " AND " : ' WHERE ');
            $where .= " rl.type_id = '".(int)$data['type_id']."'";
        }
        if ( !empty($data['object_name']) ) {
        	$where .= ($where ? " AND " : ' WHERE ');
            $where .= " rm.object_name = '".$this->db->escape($data['object_name'])."'";
        }
        if ( !empty($data['object_id']) ) {
        	$where .= ($where ? " AND " : ' WHERE ');
            $where .= " rm.object_id = '".(int)$data['object_id']."'";
        }
				
		$sql = "SELECT ". $top_sql ." FROM " . $this->db->table("resource_library") . " rl" . $join . $where;

		if ( !empty($data['subsql_filter']) ) {
			$sql .= ($where ? " AND " : 'WHERE ').$data['subsql_filter'];
		}

		//If for total, we done bulding the query
		if ($mode == 'total_only') {
		    $query = $this->db->query($sql);
		    return $query->row['total'];
		}

		$sort_data = array(
		    'name' => 'rd.name',
		    'date_added' => 'rl.date_added',
		    'sort_order' => 'rm.sort_order'
		);	
		
		if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data)) ) {
			$sql .= " ORDER BY " . $sort_data[$data['sort']];
		} else {
			$sql .= " ORDER BY rd.name ";
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
				$data['limit'] = 12;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
		return $query->rows;
	}

	/**
	 * @param int $resource_id
	 * @param int $language_id
	 * @return array
	 */
	public function getResourceObjects($resource_id, $language_id = 0) {

        $resource_objects = array();

        if ( !$language_id ) {
            $language_id = $this->language->getContentLanguageID();
        }

        $objects = $this->getAllObjects();
        foreach ( $objects as $object ) {
            if (is_callable(array($this, 'getResource'.$object))) {
		        $result = call_user_func_array(array($this, 'getResource'.$object), array($resource_id, $language_id));
	            if($result){
		            $key = $this->language->get('text_'.$object);
		            $key = !$key ? $object : $key;
                    $resource_objects[$key] = $result;
	            }
            }
        }

		return $resource_objects;
    }

	/**
	 * @param int $resource_id
	 * @param string $object_name
	 * @param int $object_id
	 * @return bool|int
	 */
	public function isMapped($resource_id, $object_name='', $object_id=0) {
		if (!has_value($resource_id)) {
			return null;
		}

		if( ($object_name && !(int)$object_id)  || (!$object_name && (int)$object_id) ){
			return null;
		}

		$sql = "SELECT count(*) as total
				FROM " . $this->db->table('resource_map'). " rm
				WHERE rm.resource_id = '".(int)$resource_id."'";
		if($object_name){
			$sql .=	" AND rm.object_name = '".$this->db->escape($object_name)."' AND object_id = ".(int)$object_id;
		}
		
		$query = $this->db->query($sql);
		if ($query->row['total'] > 0) {
			return ($object_name ? true : (int)$query->row['total']);
		} else {
			return false;
		}
	}

	/**
	 * @param int $resource_id
	 * @param int $language_id
	 * @return array
	 */
	protected function getResourceProducts($resource_id, $language_id = 0) {

        if ( !$language_id ) {
            $language_id = $this->language->getContentLanguageID();
        }

        $cache_name = 'resources.products.'. $resource_id;
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resource_objects = $this->cache->get($cache_name, $language_id, (int)$this->config->get('config_store_id'));
        if ( is_null($resource_objects) ) {
            $sql = "SELECT rm.object_id, 'products' as object_name, pd.name
                    FROM " . $this->db->table("resource_map") . " rm
                    LEFT JOIN " . $this->db->table("product_descriptions") . " pd ON ( rm.object_id = pd.product_id AND pd.language_id = '".(int)$language_id."')
                    WHERE rm.resource_id = '".(int)$resource_id."'
                        AND rm.object_name = 'products'";
            $query = $this->db->query($sql);
            $resource_objects = $query->rows;
            $this->cache->set($cache_name, $resource_objects, $language_id, (int)$this->config->get('config_store_id') );
        }

        $result = array();
        foreach ( $resource_objects as $row ) {
            $result[] = array(
                'object_id' => $row['object_id'],
                'object_name' => $row['object_name'],
                'name' => $row['name'],
                'url' => $this->html->getSecureURL('catalog/product/update', '&product_id='.$row['object_id'] )
            );
        }

        return $result;
    }

	/**
	 * @param int $resource_id
	 * @param int $language_id
	 * @return array
	 */
	protected function getResourceProduct_Option_Value($resource_id, $language_id = 0) {

        if ( !$language_id ) {
            $language_id = $this->language->getContentLanguageID();
        }

        $cache_name = 'resources.product_option_value.'. $resource_id;
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resource_objects = $this->cache->get($cache_name, $language_id, (int)$this->config->get('config_store_id'));
        if ( is_null($resource_objects) ) {
            $sql = "SELECT rm.object_id, 'product_option_value' as object_name, pd.name, pov.product_id
                    FROM " . $this->db->table("resource_map") . " rm
                    LEFT JOIN " . $this->db->table("product_option_value_descriptions") . " pd ON ( rm.object_id = pd.product_option_value_id )
                    LEFT JOIN " . $this->db->table("product_option_values") . " pov ON ( pd.product_option_value_id = pov.product_option_value_id AND pd.language_id = '".(int)$language_id."')
                    WHERE rm.resource_id = '".(int)$resource_id."'
                        AND rm.object_name = 'product_option_value'";
            $query = $this->db->query($sql);
            $resource_objects = $query->rows;
            $this->cache->set($cache_name, $resource_objects, $language_id, (int)$this->config->get('config_store_id') );
        }

        $result = array();
        foreach ( $resource_objects as $row ) {
            $result[] = array(
                'object_id' => $row['object_id'],
                'object_name' => $row['object_name'],
                'object_title' => $this->language->get('text_product_option_value'),
                'name' => $row['name'],
                'url' => $this->html->getSecureURL('catalog/product_options', '&product_id='.$row['product_id'] )
            );
        }

        return $result;
    }

	/**
	 * @param int $resource_id
	 * @param int $language_id
	 * @return array
	 */
	protected function getResourceCategories($resource_id, $language_id = 0) {

        if ( !$language_id ) {
            $language_id = $this->language->getContentLanguageID();
        }

        $cache_name = 'resources.categories.'. $resource_id;
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resource_objects = $this->cache->get($cache_name, $language_id, (int)$this->config->get('config_store_id'));
        if ( is_null($resource_objects) ) {
            $sql = "SELECT rm.object_id, 'categories' as object_name, cd.name
                FROM " . $this->db->table("resource_map") . " rm
                LEFT JOIN " . $this->db->table("category_descriptions") . " cd ON ( rm.object_id = cd.category_id AND cd.language_id = '".(int)$language_id."')
                WHERE rm.resource_id = '".(int)$resource_id."'
                    AND rm.object_name = 'categories'";
            $query = $this->db->query($sql);
            $resource_objects = $query->rows;
            $this->cache->set($cache_name, $resource_objects, $language_id, (int)$this->config->get('config_store_id'));
        }

        $result = array();
        foreach ( $resource_objects as $row ) {
            $result[] = array(
                'object_id' => $row['object_id'],
				'object_name' => $row['object_name'],
                'name' => $row['name'],
                'url' => $this->html->getSecureURL('catalog/category/update', '&category_id='.$row['object_id'] )
            );
        }

        return $result;
    }

	/**
	 * @param int $resource_id
	 * @param int $language_id
	 * @return array
	 */
	protected function getResourceManufacturers($resource_id, $language_id = 0) {

        if ( !$language_id ) {
            $language_id = $this->language->getContentLanguageID();
        }

        $cache_name = 'resources.manufacturers.'. $resource_id;
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resource_objects = $this->cache->get($cache_name, $language_id, (int)$this->config->get('config_store_id'));
        if ( is_null($resource_objects) ) {
            $sql = "SELECT rm.object_id, 'manufacturers' as object_name, m.name
					FROM " . $this->db->table("resource_map") . " rm
					LEFT JOIN " . $this->db->table("manufacturers") . " m ON ( rm.object_id = m.manufacturer_id )
					WHERE rm.resource_id = '".(int)$resource_id."'
						AND rm.object_name = 'manufacturers'";
            $query = $this->db->query($sql);
            $resource_objects = $query->rows;
            $this->cache->set($cache_name, $resource_objects, $language_id, (int)$this->config->get('config_store_id') );
        }

        $result = array();
        foreach ( $resource_objects as $row ) {
            $result[] = array(
                'object_id' => $row['object_id'],
				'object_name' => $row['object_name'],
                'name' => $row['name'],
                'url' => $this->html->getSecureURL('catalog/manufacturer/update', '&manufacturer_id='.$row['object_id'] )
            );
        }

        return $result;
    }
	
}