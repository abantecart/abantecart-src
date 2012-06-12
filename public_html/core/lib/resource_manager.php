<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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

class AResourceManager extends AResource {
	protected $registry;
	
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

    public function setType( $type ) {
        if ( $type ) {
			$this->type = $type;
			//get type details
	        $this->_loadType();
		}

        if ( !$this->type_id ) {
			$message = "Error: Incorrect or missing resource type ";
			$error = new AError ( $message );
			$error->toLog()->toDebug();
		}
    }

    public function getResourceTypes() {
		return $this->getAllResourceTypes();
	}

	public function addResourceType () {
        $cache_name = 'resources.types';
        $this->cache->delete($cache_name,'',(int)$this->config->get('config_store_id'));
	}

    public function deleteResourceType() {
        $cache_name = 'resources.types';
        $this->cache->delete($cache_name,'',(int)$this->config->get('config_store_id'));
    }

    /**
     * upload resources to directory with type name (example: image)
     *
     * @param $resource
     * @return resource id
     */
    public function addResource( $resource ) {

        if ( !$this->type_id ) {
			$message = "Error: Incorrect or missing resource type. Please set type using setType() method ";
			$error = new AError ( $message );
			$error->toLog()->toDebug();
            return false;
		}

        $sql = "INSERT INTO " . DB_PREFIX . "resource_library
                    SET type_id = '".$this->type_id."',
                        created = NOW()";
        $this->db->query($sql);
        $resource_id = $this->db->getLastId();

        if ( !empty($resource['resource_path']) ) {
            $resource_path = $this->getHexPath($resource_id) . substr(strrchr($resource['resource_path'], '.'), 0);
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
            $sql = "INSERT INTO " . DB_PREFIX . "resource_descriptions
                        SET resource_id = '".$resource_id."',
                            language_id = '".(int)$language_id."',
                            name = '".$this->db->escape($resource['name'][$language_id])."',
                            title = '".$this->db->escape($resource['title'][$language_id])."',
                            description = '".$this->db->escape($resource['description'][$language_id])."',
                            resource_path = '".$this->db->escape($resource_path)."',
                            resource_code = '".$this->db->escape($resource['resource_code'])."',
                            created = NOW()";
            $this->db->query($sql);
        }

        $this->cache->delete('resources.'.$this->type);

        return $resource_id;

    }

    public function updateResource( $resource_id, $data ) {

        $resource = parent::getResource($resource_id);
        if ( isset($data['resource_code']) )
            $_update[] = "resource_code = '".$this->db->escape($data['resource_code'])."'";

        $fields = array('name', 'title', 'description');
        foreach ( $data['name'] as $language_id => $name ) {
             $update = $_update;

            foreach ( $fields as $f ) {
                if ( isset($data[$f][$language_id]) )
                    $update[] = "$f = '".$this->db->escape($data[$f][$language_id])."'";
            }

            if ( !empty($update) ) {
	            $exist = $this->db->query( "SELECT *
											FROM " . DB_PREFIX . "resource_descriptions
										    WHERE resource_id = '" . (int)$resource_id . "' AND language_id = '" . (int)$language_id . "' ");
				if($exist->num_rows){
					 $this->db->query( "UPDATE " . DB_PREFIX . "resource_descriptions
										SET ". implode(',', $update) ."
										WHERE resource_id = '" . (int)$resource_id . "'
											AND language_id = '" . (int)$language_id . "' ");
				}else{
					 $this->db->query( "INSERT INTO " . DB_PREFIX . "resource_descriptions
										SET ". implode(',', $update) .",
										 resource_id = '" . (int)$resource_id . "',
										 language_id = '" . (int)$language_id . "' ");
				}



            }
        }


        $this->cache->delete('resources.'. $resource_id);
        $this->cache->delete('resources.'.$resource['type_name']);
        return true;
    }

    /**
     * remove resource with option to delete the file
     *
     * @param $resource_id
     * @return bool
     */
    public function deleteResource($resource_id) {

        //TODO: check if resource is mapped to object before delete

        $resource = $this->getResource($resource_id);
        if ( empty($resource) ) {
            return;
        }

        if ( $resource['resource_path'] ) {
            unlink( DIR_RESOURCE . $resource['type_name'] . '/' . $resource['resource_path'] );
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "resource_map WHERE resource_id = '".(int)$resource_id."' ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "resource_descriptions WHERE resource_id = '".(int)$resource_id."' ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "resource_library WHERE resource_id = '".(int)$resource_id."' ");

        $this->cache->delete('resources.'. $resource_id);
        $this->cache->delete('resources.'. $resource['type_name']);

        return true;
    }

	public function mapResource (  $object_name, $object_id, $resource_id ) {

        $resource = $this->getResource($resource_id);
        if ( empty($resource) ) {
            return;
        }

		$sql = "SELECT resource_id FROM " . DB_PREFIX . "resource_map
                WHERE resource_id = '".(int)$resource_id."'
                      AND object_name = '".$this->db->escape($object_name)."'
                      AND object_id = '".(int)$object_id."'";
        $result = $this->db->query($sql);

        if ( $result->num_rows ) return;

        $sql = "INSERT INTO " . DB_PREFIX . "resource_map
                    SET resource_id = '".(int)$resource_id."',
                        object_name = '".$this->db->escape($object_name)."',
                        object_id = '".(int)$object_id."',
                        created = NOW()";
        $this->db->query($sql);

        $this->cache->delete('resources.'. $resource_id);
        $this->cache->delete('resources.'. $resource['type_name']);
	}

	public function unmapResource (  $object_name, $object_id, $resource_id ) {

        $resource = $this->getResource($resource_id);
        if ( empty($resource) ) {
            return;
        }

		$sql = "DELETE FROM " . DB_PREFIX . "resource_map
                    WHERE resource_id = '".(int)$resource_id."'
                        AND object_name = '".$this->db->escape($object_name)."'
                        AND object_id = '".(int)$object_id."'";
        $this->db->query($sql);

        $this->cache->delete('resources.'. $resource_id);
        $this->cache->delete('resources.'. $resource['type_name']);
	}

    public function updateSortOrder ( $data, $object_name, $object_id ) {
        foreach ( $data as $resource_id => $sort_order ) {
            $resource = $this->getResource($resource_id);
            if ( empty($resource) ) {
                continue;
            }
            $sql = "UPDATE " . DB_PREFIX . "resource_map
                    SET sort_order = '".(int)$sort_order."'
                    WHERE resource_id = '".(int)$resource_id."'
                            AND object_name = '".$this->db->escape($object_name)."'
                            AND object_id = '".(int)$object_id."'";
            $this->db->query($sql);

            $this->cache->delete('resources.'. $resource_id);
            $this->cache->delete('resources.'. $resource['type_name']);
        }
    }

    public function getResource ( $resource_id, $language_id = '' ) {
        if ( !$resource_id ) {
			return;
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

    //TODO: add caching if keyword not defined in search data
    public function getResourcesList($search_data, $total = false) {

        $select = "SELECT rd.*, (SELECT COUNT(resource_id) FROM " . DB_PREFIX . "resource_map rm1 WHERE rm1.resource_id = rd.resource_id) as mapped ";
        $where = " WHERE 1 ";
        $join = " LEFT JOIN " . DB_PREFIX . "resource_descriptions rd ON (rl.resource_id = rd.resource_id) ";
        $order = "ORDER BY rl.resource_id";

        if ( !empty($search_data['object_name']) || !empty($search_data['object_id']) ) {
            $select .= ", rm.sort_order";
            $join .= " LEFT JOIN " . DB_PREFIX . "resource_map rm ON (rl.resource_id = rm.resource_id) ";
            $order = "ORDER BY rm.sort_order";
        }

        if ( !empty($search_data['keyword']) ) {
            $where .= " AND ( LCASE(rd.name) LIKE '%" . $this->db->escape(strtolower($search_data['keyword'])) . "%'";
			$where .= " OR LCASE(rd.title) LIKE '%" . $this->db->escape(strtolower($search_data['keyword'])) . "%' )";
        }
        if ( !empty($search_data['language_id']) ) {
            $where .= " AND rd.language_id = '".(int)$search_data['language_id']."'";
        } else {
            $where .= " AND rd.language_id = '".(int)$this->config->get('storefront_language_id')."'";
        }
        if ( !empty($search_data['type_id']) ) {
            $where .= " AND rl.type_id = '".(int)$search_data['type_id']."'";
        }
        if ( !empty($search_data['object_name']) ) {
            $where .= " AND rm.object_name = '".$this->db->escape($search_data['object_name'])."'";
        }
        if ( !empty($search_data['object_id']) ) {
            $where .= " AND rm.object_id = '".(int)$search_data['object_id']."'";
        }

        $sql = $select . " FROM " . DB_PREFIX . "resource_library rl" . $join . $where . $order;

        if ( !empty($search_data['page']) && !$total ) {
            if ( $search_data['page'] < 1 ) {
                $search_data['page'] = 1;
            }
            if ( $search_data['limit'] < 1 || $search_data['limit'] > 12  ) {
                $search_data['limit'] = 12;
            }
            $sql .= " LIMIT ". (($search_data['page'] - 1) * $search_data['limit']) . ", ".$search_data['limit'] ;
        }

		$query = $this->db->query($sql);
        if ( $total ) {
		    return $query->num_rows;
        } else {
            return $query->rows;
        }

    }

    public function getResourceObjects($resource_id, $language_id = '') {

        $resource_objects = array();

        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
        }

        $objects = $this->getAllObjects();
        foreach ( $objects as $object ) {
            if (is_callable(array($this, 'getResource'.$object))) {
		        $result = call_user_func_array(array($this, 'getResource'.$object), array($resource_id, $language_id));
	            if($result){
                    $resource_objects[$object] = $result;
	            }
            }
        }

		return $resource_objects;
    }

    protected function getResourceProducts($resource_id, $language_id = '') {

        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
        }

        $cache_name = 'resources.'. $resource_id . '.products';
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resource_objects = $this->cache->get($cache_name, $language_id, (int)$this->config->get('config_store_id'));
        if ( is_null($resource_objects) ) {
            $sql = "SELECT rm.object_id, pd.name
                    FROM " . DB_PREFIX . "resource_map rm
                    LEFT JOIN " . DB_PREFIX . "product_descriptions pd ON ( rm.object_id = pd.product_id )
                    WHERE rm.resource_id = '".(int)$resource_id."'
                        AND rm.object_name = 'products'
                        AND pd.language_id = '".(int)$language_id."'";
            $query = $this->db->query($sql);
            $resource_objects = $query->rows;
            $this->cache->set($cache_name, $resource_objects, $language_id, (int)$this->config->get('config_store_id') );
        }

        $result = array();
        foreach ( $resource_objects as $row ) {
            $result[] = array(
                'object_id' => $row['object_id'],
                'name' => $row['name'],
                'url' => $this->html->getSecureURL('catalog/product/update', '&product_id='.$row['object_id'] )
            );
        }

        return $result;
    }
    protected function getResourceOptionValue($resource_id, $language_id = '') {

        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
        }

        $cache_name = 'resources.'. $resource_id . '.product_option_value';
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resource_objects = $this->cache->get($cache_name, $language_id, (int)$this->config->get('config_store_id'));
        if ( is_null($resource_objects) ) {
            $sql = "SELECT rm.object_id, pd.name
                    FROM " . DB_PREFIX . "resource_map rm
                    LEFT JOIN " . DB_PREFIX . "product_option_value_descriptions pd ON ( rm.object_id = pd.product_option_value_id )
                    WHERE rm.resource_id = '".(int)$resource_id."'
                        AND rm.object_name = 'product_option_value'
                        AND pd.language_id = '".(int)$language_id."'";
            $query = $this->db->query($sql);
            $resource_objects = $query->rows;
            $this->cache->set($cache_name, $resource_objects, $language_id, (int)$this->config->get('config_store_id') );
        }

        $result = array();
        foreach ( $resource_objects as $row ) {
            $result[] = array(
                'object_id' => $row['object_id'],
                'name' => $row['name'],
                'url' => $this->html->getSecureURL('catalog/product_options', '&option_id='.$row['object_id'] )
            );
        }

        return $result;
    }

    protected function getResourceCategories($resource_id, $language_id = '') {

        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
        }

        $cache_name = 'resources.'. $resource_id . '.categories';
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resource_objects = $this->cache->get($cache_name, $language_id, (int)$this->config->get('config_store_id'));
        if ( is_null($resource_objects) ) {
            $sql = "SELECT rm.object_id, cd.name
                FROM " . DB_PREFIX . "resource_map rm
                LEFT JOIN " . DB_PREFIX . "category_descriptions cd ON ( rm.object_id = cd.category_id )
                WHERE rm.resource_id = '".(int)$resource_id."'
                    AND rm.object_name = 'categories'
                    AND cd.language_id = '".(int)$language_id."'";
            $query = $this->db->query($sql);
            $resource_objects = $query->rows;
            $this->cache->set($cache_name, $resource_objects, $language_id, (int)$this->config->get('config_store_id'));
        }

        $result = array();
        foreach ( $resource_objects as $row ) {
            $result[] = array(
                'object_id' => $row['object_id'],
                'name' => $row['name'],
                'url' => $this->html->getSecureURL('catalog/category/update', '&category_id='.$row['object_id'] )
            );
        }

        return $result;
    }

    protected function getResourceManufacturers($resource_id, $language_id = '') {

        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
        }

        $cache_name = 'resources.'. $resource_id . '.manufacturers';
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resource_objects = $this->cache->get($cache_name, $language_id, (int)$this->config->get('config_store_id'));
        if ( is_null($resource_objects) ) {
            $sql = "SELECT rm.object_id, m.name
                FROM " . DB_PREFIX . "resource_map rm
                LEFT JOIN " . DB_PREFIX . "manufacturers m ON ( rm.object_id = m.manufacturer_id )
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
                'name' => $row['name'],
                'url' => $this->html->getSecureURL('catalog/manufacturer/update', '&manufacturer_id='.$row['object_id'] )
            );
        }

        return $result;
    }
	
}