<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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
				$message = "Error: Incorrect or missing resource type ".$this->request->get['resource_id'];
				$error = new AError ( $message );
				$error->toLog()->toDebug();
			}
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

        $sql = "INSERT INTO " . DB_PREFIX . "resource_library
                    SET type_id = '".$this->type_id."',
                        created = NOW()";
        $this->db->query($sql);
        $resource_id = $this->db->getLastId();

        if ( !empty($resource['resource_path']) ) {
            $resource_path = $this->getHexPath($resource_id) . strtolower(substr(strrchr($resource['resource_path'], '.'), 0));
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
													 'created' => date('Y-m-d H:i:s')
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

        $resource = parent::getResource($resource_id);
        if ( isset($data['resource_code']) )
            $_update['resource_code'] = $data['resource_code'];

        $fields = array('name', 'title', 'description');
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
            return null;
        }

        if ( $resource['resource_path'] && is_file( DIR_RESOURCE . $resource['type_name'] . '/' . $resource['resource_path']) ) {
            unlink( DIR_RESOURCE.$resource['type_name'].'/'.$resource['resource_path'] );
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "resource_map WHERE resource_id = '".(int)$resource_id."' ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "resource_descriptions WHERE resource_id = '".(int)$resource_id."' ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "resource_library WHERE resource_id = '".(int)$resource_id."' ");

        $this->cache->delete('resources.'. $resource_id);
        $this->cache->delete('resources.'. $resource['type_name']);

        return true;
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

		$sql = "SELECT resource_id FROM " . DB_PREFIX . "resource_map
                WHERE resource_id = '".(int)$resource_id."'
                      AND object_name = '".$this->db->escape($object_name)."'
                      AND object_id = '".(int)$object_id."'";
        $result = $this->db->query($sql);

        if ( $result->num_rows ) return null;

		//need to get sort order
		$sql = "SELECT MAX(sort_order) as sort_order
				FROM " . DB_PREFIX . "resource_map
				WHERE object_name = '".$this->db->escape($object_name)."'
					  AND object_id = '".(int)$object_id."'";
		$result = $this->db->query($sql);
		$new_sort_order = $result->row['sort_order']+1;

        $sql = "INSERT INTO " . DB_PREFIX . "resource_map
                    SET resource_id = '".(int)$resource_id."',
                        object_name = '".$this->db->escape($object_name)."',
                        object_id = '".(int)$object_id."',
                        sort_order = '".(int)$new_sort_order."',
                        created = NOW()";
        $this->db->query($sql);

        $this->cache->delete('resources.'. $resource_id);
        $this->cache->delete('resources.'. $object_name.'.'.$resource_id);
        $this->cache->delete('resources.'. $resource['type_name']);
	}

	/**
	 * @param string $object_name
	 * @param int $object_id
	 * @param int $resource_id
	 * @return null
	 */
	public function unmapResource (  $object_name, $object_id, $resource_id ) {

        $resource = $this->getResource($resource_id);
        if ( empty($resource) ) {
            return null;
        }

		$sql = "DELETE FROM " . DB_PREFIX . "resource_map
                    WHERE resource_id = '".(int)$resource_id."'
                        AND object_name = '".$this->db->escape($object_name)."'
                        AND object_id = '".(int)$object_id."'";
        $this->db->query($sql);

        $this->cache->delete('resources.'. $resource_id);
		$this->cache->delete('resources.'. $object_name.'.'.$resource_id);
        $this->cache->delete('resources.'. $resource['type_name']);
	}

	/**
	 * @param array $data
	 * @param string $object_name
	 * @param int $object_id
	 */
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
			$this->cache->delete('resources.'. $object_name.'.'.$resource_id);
            $this->cache->delete('resources.'. $resource['type_name']);
        }
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

    //TODO: add caching if keyword not defined in search data
	/**
	 * @param array $search_data
	 * @param bool $total
	 * @return array|int
	 */
	public function getResourcesList($search_data, $total = false) {

        $select = "SELECT rl.resource_id,
        				  rd.name,
        				  rd.title,
        				  rd.description,
        				  COALESCE(rd.resource_path,rdd.resource_path) as resource_path,
        				  COALESCE(rd.resource_code,rdd.resource_code) as resource_code,
        				  (SELECT COUNT(resource_id) FROM " . DB_PREFIX . "resource_map rm1 WHERE rm1.resource_id = rd.resource_id) as mapped ";
        $where = " WHERE 1 ";

		if ( !empty($search_data['language_id']) ) {
			$language_id = (int)$search_data['language_id'];
		} else {
			$language_id = (int)$this->language->getContentLanguageID();
		}

        $join = " LEFT JOIN " . DB_PREFIX . "resource_descriptions rd ON (rl.resource_id = rd.resource_id AND rd.language_id = '".$language_id."') ";
        $join .= " LEFT JOIN " . DB_PREFIX . "resource_descriptions rdd ON (rl.resource_id = rdd.resource_id AND rdd.language_id = '".$this->language->getDefaultLanguageID()."') ";
        $order = " ORDER BY rl.resource_id";

        if ( !empty($search_data['object_name']) || !empty($search_data['object_id']) ) {
            $select .= ", rm.sort_order";
            $join .= " LEFT JOIN " . DB_PREFIX . "resource_map rm ON (rl.resource_id = rm.resource_id) ";
            $order = " ORDER BY rm.sort_order, rl.resource_id";
        }

        if ( !empty($search_data['keyword']) ) {
            $where .= " AND ( LCASE(rd.name) LIKE '%" . $this->db->escape(strtolower($search_data['keyword'])) . "%'";
			$where .= " OR LCASE(rd.title) LIKE '%" . $this->db->escape(strtolower($search_data['keyword'])) . "%' )";
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

	/**
	 * @param int $resource_id
	 * @param int $language_id
	 * @return array
	 */
	public function getResourceObjects($resource_id, $language_id = 0) {

        $resource_objects = array();

        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
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
	 * @param int $language_id
	 * @return array
	 */
	protected function getResourceProducts($resource_id, $language_id = 0) {

        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
        }

        $cache_name = 'resources.products.'. $resource_id;
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resource_objects = $this->cache->get($cache_name, $language_id, (int)$this->config->get('config_store_id'));
        if ( is_null($resource_objects) ) {
            $sql = "SELECT rm.object_id, pd.name
                    FROM " . DB_PREFIX . "resource_map rm
                    LEFT JOIN " . DB_PREFIX . "product_descriptions pd ON ( rm.object_id = pd.product_id AND pd.language_id = '".(int)$language_id."')
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
            $language_id = $this->config->get('storefront_language_id');
        }

        $cache_name = 'resources.product_option_value.'. $resource_id;
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resource_objects = $this->cache->get($cache_name, $language_id, (int)$this->config->get('config_store_id'));
        if ( is_null($resource_objects) ) {
            $sql = "SELECT rm.object_id, pd.name, pov.product_id
                    FROM " . DB_PREFIX . "resource_map rm
                    LEFT JOIN " . DB_PREFIX . "product_option_value_descriptions pd ON ( rm.object_id = pd.product_option_value_id )
                    LEFT JOIN " . DB_PREFIX . "product_option_values pov ON ( pd.product_option_value_id = pov.product_option_value_id AND pd.language_id = '".(int)$language_id."')
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
                'object_name' => $this->language->get('text_product_option_value'),
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
            $language_id = $this->config->get('storefront_language_id');
        }

        $cache_name = 'resources.categories.'. $resource_id;
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resource_objects = $this->cache->get($cache_name, $language_id, (int)$this->config->get('config_store_id'));
        if ( is_null($resource_objects) ) {
            $sql = "SELECT rm.object_id, cd.name
                FROM " . DB_PREFIX . "resource_map rm
                LEFT JOIN " . DB_PREFIX . "category_descriptions cd ON ( rm.object_id = cd.category_id AND cd.language_id = '".(int)$language_id."')
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
            $language_id = $this->config->get('storefront_language_id');
        }

        $cache_name = 'resources.manufacturers.'. $resource_id;
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