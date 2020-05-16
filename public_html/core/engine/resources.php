<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

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

/** @noinspection PhpUndefinedClassInspection
 * @property ModelToolImage    $model_tool_image
 * @property  ExtensionsAPI    $extensions
 * @property  ALoader          $load
 * @property  AHtml            $html
 * @property  AConfig          $config
 * @property  ACache           $cache
 * @property  ADB              $db
 * @property  ALanguageManager $language
 */
class AResource
{
    /**
     * @var array
     */
    public $data = array();
    public $obj_list = array('products', 'categories', 'manufacturers', 'product_option_value');
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var int
     */
    protected $type_id;
    /**
     * @var string
     */
    protected $type_dir;
    /**
     * @var string
     */
    protected $type_icon;
    /**
     * @var string
     */
    protected $access_type;
    /**
     * @var array
     */
    protected $file_types;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $this->registry = Registry::getInstance();
        //NOTE: Storefront can not access all resource at once. Resource type required
        if ($type) {
            $this->type = $type;
            //get type details
            $this->_loadType();
        }

        if (!$this->type_id) {
            $backtrace = debug_backtrace();
            $message = "Error: Incorrect or missing resource type.".$backtrace[0]['file'].":".$backtrace[0]['line'];
            $error = new AWarning($message);
            $error->toLog()->toDebug();
        }

    }

    protected function _loadType()
    {
        $cache_key = 'resources.'.$this->type;
        $cache_key = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_key).'.store_'.(int)$this->config->get('config_store_id');
        $type_data = $this->cache->pull($cache_key);
        if ($type_data === false || empty($type_data['type_id'])) {
            $sql = "SELECT * "
                ."FROM ".$this->db->table("resource_types")." "
                ."WHERE type_name = '".$this->db->escape($this->type)."'";
            $query = $this->db->query($sql);
            $type_data = $query->row;
            $this->cache->push($cache_key, $type_data);
        }
        $this->type_id = (int)$type_data['type_id'];
        $this->type_dir = $type_data['default_directory'];
        $this->type_icon = $type_data['default_icon'];
        $this->access_type = $type_data['access_type'];
        $this->file_types = $type_data['file_types'];
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    public function getTypeId()
    {
        return $this->type_id;
    }

    public function getTypeIcon()
    {
        return $this->type_icon;
    }

    public function getTypeAccess()
    {
        return $this->access_type;
    }

    public function getTypeFileTypes()
    {
        return $this->file_types;
    }

    /**
     * @param int $resource_id
     *
     * @return string
     */
    public function getHexPath($resource_id)
    {
        $result = rtrim(chunk_split(dechex($resource_id), 2, '/'), '/');
        return $result;
    }

    /**
     * @param string $path
     *
     * @return null|number
     */
    public function getIdFromHexPath($path)
    {
        if (empty($path)) {
            return null;
        }
        if (strpos($path, '/') !== false) {
            //find first in file to solve tar.gz problem
            if (preg_match("/\.tar\.gz$/i", $path)) {
                $ext = 'tar.gz';
            } else {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
            }
            $path = str_replace(array('.'.$ext, '/'), '', $path);
            $result = hexdec($path);
        } else {
            $result = $this->getIdByName($path);
        }
        //function must return only integer!
        if (!is_int($result)) {
            return null;
        }
        return $result;
    }

    /**
     * @param string $filename
     *
     * @return int
     */
    public function getIdByName($filename)
    {
        $sql = "SELECT resource_id
				FROM ".$this->db->table("resource_descriptions")."
				WHERE name like '%".$this->db->escape($filename)."%'
				ORDER BY language_id";
        $query = $this->db->query($sql);
        return (int)$query->row['resource_id'];
    }

    /**
     * function returns URL to resource.
     *
     * @deprecated since 1.2.7
     *
     * @param int        $resource_id - NOTE: can be zero to show default_image
     * @param int        $width
     * @param int        $height
     * @param string|int $language_id
     *
     * @return string
     * @throws AException
     */
    public function getResourceThumb($resource_id, $width, $height, $language_id = '')
    {
        $width = (int)$width;
        $height = (int)$height;
        if (!$width || !$height) {
            return '';
        }

        if (!$language_id) {
            $language_id = $this->language->getDefaultLanguageID();
        }

        $rsrc_info = array();
        if ($resource_id) {
            $rsrc_info = $this->getResource($resource_id, $language_id);
            //check if resource have descriptions. if not - try to get it for default language
            if (!$rsrc_info['name'] && $language_id != $this->language->getDefaultLanguageID()) {
                $rsrc_info = $this->getResource($resource_id, $this->language->getDefaultLanguageID());
            }
            return $this->getResizedImageURL($rsrc_info, $width, $height);
        } else {
            return '';
        }

    }

    /**
     * @param int $resource_id
     * @param int $language_id
     *
     * @return array
     */
    public function getResource($resource_id, $language_id = 0)
    {
        //Return resource details
        $resource_id = (int)$resource_id;
        if (!$resource_id) {
            return array();
        }
        if (!$language_id) {
            $language_id = $this->config->get('storefront_language_id');
        }

        //attempt to load cache
        $cache_key = 'resources.'.$resource_id;
        $cache_key = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_key);
        $resource = $this->cache->pull($cache_key);
        if ($resource === false) {
            $where = "WHERE rl.resource_id = ".$this->db->escape($resource_id);
            $sql = "SELECT
						rd.*,
						COALESCE(rd.resource_path,rdd.resource_path) as resource_path,
						COALESCE(rd.resource_code,rdd.resource_code) as resource_code,
						rt.type_name,
						rt.default_icon
					FROM ".$this->db->table("resource_library")." rl "."
					LEFT JOIN ".$this->db->table("resource_descriptions")." rd
						ON (rl.resource_id = rd.resource_id)
					LEFT JOIN ".$this->db->table("resource_descriptions")." rdd
						ON (rl.resource_id = rdd.resource_id AND rdd.language_id = '".$this->language->getDefaultLanguageID()."')
					LEFT JOIN ".$this->db->table("resource_types")." rt
						ON (rl.type_id = rt.type_id )
					".$where;

            $query = $this->db->query($sql);
            $result = $query->rows;
            $resource = array();
            foreach ($result as $r) {
                $resource[$r['language_id']] = $r;
            }
            $this->cache->push($cache_key, $resource);
        }

        $result = array();
        if (!empty($resource[$language_id])) {
            $result = $resource[$language_id];
        } else {
            if (!empty($resource)) {
                reset($resource);
                $result = current($resource);
            }
        }

        return $result;
    }

    /**
     * function returns URL to resource if image it will resize.
     *
     * @since 1.2.7
     *
     * @param array $rsrc_info - resource details
     * @param int   $width
     * @param int   $height
     *
     * @return string
     * @throws AException
     */
    public function getResizedImageURL($rsrc_info = array(), $width, $height)
    {
        $resource_id = (int)$rsrc_info['resource_id'];
        //get original file path & details
        $origin_path = DIR_RESOURCE.$this->type_dir.$rsrc_info['resource_path'];
        $info = pathinfo($origin_path);
        $extension = $info['extension'];
        if (in_array($extension, array('ico', 'svg', 'svgz'))) {
            // returns ico-file as original
            return $this->buildResourceURL($rsrc_info['resource_path'], 'full');
        }

        $type_image = is_file(DIR_IMAGE.'icon_resource_'.$this->type.'.png') ? 'icon_resource_'.$this->type.'.png' : '';

        //is this a resource with code ?
        if (!empty($rsrc_info['resource_code'])) {
            //we have resource code, nothing to do
            return $rsrc_info['resource_code'];
        }
        //is this image resource
        switch ($this->type) {
            case 'image' :
                if (!$rsrc_info['default_icon']) {
                    $rsrc_info['default_icon'] = 'no_image.jpg';
                }
                if (!$rsrc_info['resource_path']) {
                    $origin_path = '';
                }
                break;
            default :
                //this is non image type return original
                if (!$rsrc_info['default_icon'] && !$type_image) {
                    $rsrc_info['default_icon'] = 'no_image.jpg';
                    $origin_path = '';
                } elseif ($type_image) {
                    $rsrc_info['default_icon'] = $type_image;
                    $origin_path = '';
                } else {
                    return $this->buildResourceURL($rsrc_info['resource_path'], 'full');
                }
        }

        $width = (int)$width;
        $height = (int)$height;
        if (!$width || !$height) {
            //if no size, return original
            return $this->buildResourceURL($rsrc_info['resource_path'], 'full');
        }

        //resource name MUST be provided here, if missing use resource ID.
        if (!$rsrc_info['name'] && $resource_id) {
            $rsrc_info['name'] = $resource_id;
        }
        $name = preg_replace('/[^a-zA-Z0-9]/', '_', $rsrc_info['name']);

        if (!is_file($origin_path) || !$resource_id) {
            //missing original resource. oops
            $this->load->model('tool/image');
            return $this->model_tool_image->resize($rsrc_info['default_icon'], $width, $height);
        } else {
            //Build thumbnails path similar to resource library path
            $sub_path = 'thumbnails/'.dirname($rsrc_info['resource_path']).'/'.$name.'-'.$resource_id.'-'.$width.'x'.$height;
            $new_image = $sub_path.'.'.$extension;
            if (!check_resize_image($origin_path, $new_image, $width, $height, $this->config->get('config_image_quality'))) {
                $warning = new AWarning('Resize image error. File: '.$origin_path);
                $warning->toLog()->toDebug();
                return null;
            }
            //do retina version
            if ($this->config->get('config_retina_enable')) {
                $new_image2x = $sub_path.'@2x.'.$extension;
                if (!check_resize_image($origin_path, $new_image2x, $width * 2, $height * 2, $this->config->get('config_image_quality'))) {
                    $warning = new AWarning('Resize image error. File: '.$origin_path);
                    $warning->toLog()->toDebug();
                }
            }
            //hook here to affect this image
            $this->extensions->hk_ProcessData($this, __FUNCTION__);
            //prepend URL and return
            $http_path = $this->data['http_dir'];
            if (!$http_path) {
                $http_path = HTTPS_IMAGE;
            }
            return $http_path.$new_image;
        }
    }

    /**
     * @param string $resource_path (hashed resource path from database)
     * @param string $mode          full (with http and domain) or relative (from store url up)
     *
     * @return string
     */
    public function buildResourceURL($resource_path, $mode = 'full')
    {

        if ($mode == 'full') {
            $this->extensions->hk_ProcessData($this, __FUNCTION__);
            $http_path = $this->data['http_dir'];
            if (!$http_path) {
                $http_path = HTTPS_DIR_RESOURCE;
            }
            return $http_path.$this->type_dir.$resource_path;
        } else {
            return "/resources/".$this->type_dir.$resource_path;
        }
    }

    /**
     * @return array
     */
    public function getAllResourceTypes()
    {
        //attempt to load cache
        $cache_key = 'resources.types.store_'.(int)$this->config->get('config_store_id');
        $types = $this->cache->pull($cache_key);
        if ($types !== false) {
            return $types;
        }

        $sql = "SELECT * FROM ".$this->db->table("resource_types")." ";
        $query = $this->db->query($sql);
        $types = $query->rows;
        $this->cache->push($cache_key, $types);

        return $types;
    }

    /**
     * @return array
     */
    public function getAllObjects()
    {
        return $this->obj_list;
    }

    /**
     * @param string $object_name
     * @param string $object_id
     * @param int    $width
     * @param int    $height
     * @param bool   $noimage
     *
     * @return array
     */
    public function getMainThumb($object_name, $object_id, $width, $height, $noimage = true)
    {
        $sizes = array('thumb' => array('width' => $width, 'height' => $height));
        $result = $this->getResourceAllObjects($object_name, $object_id, $sizes, 1, $noimage);
        $output = array();
        if ($result) {
            $output = array(
                'origin'      => $result['origin'],
                'thumb_html'  => $result['thumb_html'],
                'title'       => $result['title'],
                'description' => $result['description'],
                'width'       => $width,
                'height'      => $height,
            );
            if ($result['thumb_url']) {
                $output['thumb_url'] = $result['thumb_url'];
            }
        }
        return $output;
    }

    /**
     * method returns all resources of object by it's id and name
     *
     * @param string $object_name
     * @param string $object_id
     * @param array  $sizes
     * @param int    $limit
     * @param bool   $noimage
     *
     * @return array
     */
    public function getResourceAllObjects($object_name, $object_id, $sizes = array('main' => array(), 'thumb' => array(), 'thumb2' => array()), $limit = 0, $noimage = true)
    {
        if (!$object_id || !$object_name) {
            return array();
        }
        $limit = (int)$limit;
        $results = $this->getResources($object_name, $object_id);
        if (!$results && !$limit) {
            return array();
        }

        if ($limit && !$noimage) {
            $slice_limit = $limit > sizeof($results) ? sizeof($results) : $limit;
            $results = array_slice($results, 0, $slice_limit);
        }

        $this->load->model('tool/image');
        if (!$sizes || !is_array($sizes['main']) || !is_array($sizes['thumb'])) {
            if (!is_array($sizes['main'])) {
                $sizes['main'] = array(
                    'width'  => $this->config->get('config_image_product_width'),
                    'height' => $this->config->get('config_image_product_height'),
                );
            }
            if (!is_array($sizes['thumb'])) {
                $sizes['thumb'] = array(
                    'width'  => $this->config->get('config_image_thumb_width'),
                    'height' => $this->config->get('config_image_thumb_height'),
                );
            }
        }

        $resources = array();
        if (!$results && $noimage && $this->getType() == 'image') {
            $results = array(array('resource_path' => 'no_image.jpg'));
        }

        if (!$results) {
            return array();
        }

        foreach ($results as $k => $result) {
            $thumb_url = $thumb2_url = '';
            $rsrc_info = $result['resource_id'] ? $this->getResource($result['resource_id'], $this->config->get('storefront_language_id')) : $result;
            $origin = $rsrc_info['resource_path'] ? 'internal' : 'external';
            if ($origin == 'internal') {
                $this->extensions->hk_ProcessData($this, __FUNCTION__);
                $http_path = $this->data['http_dir'];
                if (!$http_path) {
                    $http_path = HTTPS_DIR_RESOURCE;
                }

                $direct_url = $http_path.$this->getTypeDir().$result['resource_path'];
                $res_full_path = '';
                if ($this->getType() == 'image') {
                    $res_full_path = DIR_RESOURCE.$this->getTypeDir().$result['resource_path'];
                    if ($sizes['main']) {
                        $main_url = $this->getResizedImageURL(
                            $result,
                            $sizes['main']['width'],
                            $sizes['main']['height']
                        );
                    } else {
                        // return href for image with size as-is
                        $main_url = $http_path.$this->getTypeDir().$result['resource_path'];
                        //get original image size
                        $actual_sizes = get_image_size($res_full_path);
                        $sizes['main'] = $actual_sizes;
                    }
                    if ($sizes['thumb']) {
                        $thumb_url = $this->getResizedImageURL(
                            $result,
                            $sizes['thumb']['width'],
                            $sizes['thumb']['height']
                        );
                    }

                    if (!$thumb_url && $sizes['thumb']) {
                        $thumb_url = $this->model_tool_image->resize(
                            $result['resource_path'],
                            $sizes['thumb']['width'],
                            $sizes['thumb']['height']
                        );
                    }
                    //thumb2 - big thumbnails
                    if ($sizes['thumb2']) {
                        $thumb2_url = $this->getResizedImageURL(
                            $result,
                            $sizes['thumb2']['width'],
                            $sizes['thumb2']['height']
                        );
                    }
                    if (!$thumb2_url && $sizes['thumb2']) {
                        $thumb2_url = $this->model_tool_image->resize(
                            $result['resource_path'],
                            $sizes['thumb2']['width'],
                            $sizes['thumb2']['height']
                        );
                    }

                } else {

                    $main_url = $direct_url;
                    $thumb_url = $this->getResizedImageURL(
                        $result,
                        $sizes['thumb']['width'],
                        $sizes['thumb']['height']
                    );
                }

                $resources[$k] = array(
                    'resource_id'   => $result['resource_id'],
                    'origin'        => $origin,
                    'direct_url'    => $direct_url,
                    //set full path to original file only for images (see above)
                    'resource_path' => $res_full_path,
                    'main_url'      => $main_url,
                    'main_width'    => $sizes['main']['width'],
                    'main_height'   => $sizes['main']['height'],
                    'main_html'     => $this->html->buildResourceImage(array(
                        'url'    => $http_path.'image/'.$result['resource_path'],
                        'width'  => $sizes['main']['width'],
                        'height' => $sizes['main']['height'],
                        'attr'   => 'alt="'.addslashes($rsrc_info['title']).'"',
                    )),
                    'thumb_url'     => $thumb_url,
                    'thumb_width'   => $sizes['thumb']['width'],
                    'thumb_height'  => $sizes['thumb']['height'],
                    'thumb_html'    => $this->html->buildResourceImage(array(
                        'url'    => $thumb_url,
                        'width'  => $sizes['thumb']['width'],
                        'height' => $sizes['thumb']['height'],
                        'attr'   => 'alt="'.addslashes($rsrc_info['title']).'"',
                    )),
                );
                if ($sizes['thumb2']) {
                    $resources[$k]['thumb2_url'] = $thumb2_url;
                    $resources[$k]['thumb2_width'] = $sizes['thumb2']['width'];
                    $resources[$k]['thumb2_height'] = $sizes['thumb2']['height'];
                    $resources[$k]['thumb2_html'] = $this->html->buildResourceImage(array(
                        'url'    => $thumb2_url,
                        'width'  => $sizes['thumb2']['width'],
                        'height' => $sizes['thumb2']['height'],
                        'attr'   => 'alt="'.addslashes($rsrc_info['title']).'"',
                    ));
                }
                $resources[$k]['description'] = $rsrc_info['description'];
                $resources[$k]['title'] = $rsrc_info['title'];
            } else {
                $resources[$k] = array(
                    'origin'      => $origin,
                    'main_html'   => $rsrc_info['resource_code'],
                    'thumb_html'  => $rsrc_info['resource_code'],
                    'title'       => $rsrc_info['title'],
                    'description' => $rsrc_info['description'],
                );
            }

            if($limit && count($resources) == $limit){
                break;
            }
        }

        if ($limit == 1) {
            $resources = $resources[0];
        }

        return $resources;
    }

    //TODO: define where all object types will be kept and fetch them from storage

    /**
     * @param string $object_name
     * @param string $object_id
     * @param int    $language_id
     *
     * @return array
     */
    public function getResources($object_name, $object_id, $language_id = 0)
    {
        //Allow to load resources only for 1 object and id combination
        if (!has_value($object_name) || !has_value($object_id)) {
            return array();
        }

        if (!$language_id) {
            $language_id = $this->config->get('storefront_language_id');
        }

        $store_id = (int)$this->config->get('config_store_id');

        //attempt to load cache
        $cache_key = 'resources.'.$this->type.'.'.$object_name.'.'.$object_id;
        $cache_key = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_key).'.store_'.$store_id.'_lang_'.$language_id;
        $resources = $this->cache->pull($cache_key);
        if ($resources !== false) {
            return $resources;
        }

        $where = "WHERE rm.object_name = '".$this->db->escape($object_name)."' "
            ." and rm.object_id = '".$this->db->escape($object_id)."' "
            ." and rl.type_id = ".$this->db->escape($this->type_id);

        $sql = "SELECT
					rl.resource_id,
					rd.name,
					rd.title,
					rd.description,
					COALESCE(rd.resource_path,rdd.resource_path) as resource_path,
					COALESCE(rd.resource_code,rdd.resource_code) as resource_code,
					rm.default,
					rm.sort_order
				FROM ".$this->db->table("resource_library")." rl "."
				LEFT JOIN ".$this->db->table("resource_map")." rm
					ON rm.resource_id = rl.resource_id "."
				LEFT JOIN ".$this->db->table("resource_descriptions")." rd
					ON (rl.resource_id = rd.resource_id AND rd.language_id = '".$language_id."')
				LEFT JOIN ".$this->db->table("resource_descriptions")." rdd
					ON (rl.resource_id = rdd.resource_id AND rdd.language_id = '".$this->language->getDefaultLanguageID()."')
				".$where."
				ORDER BY rm.sort_order ASC";

        $query = $this->db->query($sql);
        $resources = $query->rows;
        $this->cache->push($cache_key, $resources);
        return $resources;
    }

    public function getTypeDir()
    {
        return $this->type_dir;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @since 1.2.7
     *
     * @param string    $object_name
     * @param array     $object_ids
     * @param int       $width
     * @param int       $height
     * @param bool|true $noimage
     *
     * @return array
     * @throws AException
     */
    public function getMainThumbList($object_name, $object_ids = array(), $width = 0, $height = 0, $noimage = true)
    {
        $width = (int)$width;
        $height = (int)$height;
        if (!$object_name || !$object_ids || !is_array($object_ids) || !$width || !$height) {
            return array();
        }
        //cleanup ids
        $tmp = array();
        foreach ($object_ids as $object_id) {
            $object_id = (int)$object_id;
            if ($object_id) {
                $tmp[] = $object_id;
            }
        }
        $object_ids = array_unique($tmp);
        unset($tmp);

        if (!$object_ids) {
            return array();
        }

        $language_id = $this->language->getLanguageID();
        $default_language_id = $this->language->getDefaultLanguageID();

        $store_id = (int)$this->config->get('config_store_id');
        //attempt to load cache
        $cache_key = 'resources.list.'.$this->type.'.'.$object_name.'.'.$width.'x'.$height.'.'.md5(implode('.', $object_ids));
        $cache_key = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_key).'.store_'.$store_id.'_lang_'.$language_id;
        $output = $this->cache->pull($cache_key);
        if ($output !== false) {
            return $output;
        }

        //get resource list
        $sql = "
			SELECT
				rm.object_id,
				rl.resource_id,
				COALESCE(rd.name,rdd.name) as name,
				COALESCE(rd.title,rdd.title) as title,
				COALESCE(rd.description,rdd.description) as description,
				COALESCE(rd.resource_path,rdd.resource_path) as resource_path,
				COALESCE(rd.resource_code,rdd.resource_code) as resource_code,
				rm.default,
				rm.sort_order
			FROM ".$this->db->table("resource_library")." rl "."
			LEFT JOIN ".$this->db->table("resource_map")." rm
				ON rm.resource_id = rl.resource_id "."
			LEFT JOIN ".$this->db->table("resource_descriptions")." rd
				ON (rl.resource_id = rd.resource_id
					AND rd.language_id = '".$language_id."')
			LEFT JOIN ".$this->db->table("resource_descriptions")." rdd
				ON (rl.resource_id = rdd.resource_id
					AND rdd.language_id = '".$default_language_id."')
			WHERE rm.object_name = '".$this->db->escape($object_name)."'
				 AND rl.type_id = ".$this->type_id."
				 AND rm.object_id IN (".implode(", ", $object_ids).")
			ORDER BY rm.object_id ASC, rm.sort_order ASC, rl.resource_id ASC";
        $result = $this->db->query($sql);

        $output = $selected_ids = array();
        foreach ($result->rows as $row) {
            $object_id = $row['object_id'];
            //filter only first resource per object (main)
            if (isset($output[$object_id])) {
                continue;
            }

            $origin = $row['resource_path'] ? 'internal' : 'external';
            $output[$object_id] = array(
                'origin'      => $origin,
                'title'       => $row['title'],
                'description' => $row['description'],
                'width'       => $width,
                'height'      => $height,
            );
            //for external resources
            if ($origin == 'external') {
                $output[$object_id]['thumb_html'] = $row['resource_code'];
            } //for internal resources
            else {
                $thumb_url = $this->getResizedImageURL($row, $width, $height);
                $output[$object_id]['thumb_html'] = $this->html->buildResourceImage(
                    array(
                        'url'    => $thumb_url,
                        'width'  => $width,
                        'height' => $height,
                        'attr'   => 'alt="'.addslashes($row['title']).'"',
                    ));
                $output[$object_id]['thumb_url'] = $thumb_url;
            }
            $selected_ids[] = $object_id;
        }

        //if some of objects have no thumbnail
        $diff = array_diff($object_ids, $selected_ids);
        if ($diff) {
            foreach ($diff as $object_id) {
                //when need to show default image
                if ($noimage) {
                    $thumb_url = $this->getResizedImageURL(array('resource_id' => 0), $width, $height);

                    $output[$object_id] = array(
                        'origin'      => 'internal',
                        'title'       => '',
                        'description' => '',
                        'width'       => $width,
                        'height'      => $height,
                        'thumb_url'   => $thumb_url,
                        'thumb_html'  => $this->html->buildResourceImage(
                            array(
                                'url'    => $thumb_url,
                                'width'  => $width,
                                'height' => $height,
                                'attr'   => 'alt=""',
                            )),
                    );

                } else {
                    $output[$object_id] = array();
                }
            }
        }

        $this->cache->push($cache_key, $output);
        return $output;
    }

    /**
     * @param string $object_name
     * @param string $object_id
     * @param int    $width
     * @param int    $height
     * @param bool   $noimage
     *
     * @return array
     */
    public function getMainImage($object_name, $object_id, $width, $height, $noimage = true)
    {
        $sizes = array('main' => array('width' => $width, 'height' => $height));
        $result = $this->getResourceAllObjects($object_name, $object_id, $sizes, 1, $noimage);
        $output = array();
        if ($result) {
            $output = array(
                'origin'      => $result['origin'],
                'main_html'   => $result['main_html'],
                'description' => $result['description'],
                'title'       => $result['title'],
                'width'       => $width,
                'height'      => $height,
            );
            if ($result['main_url']) {
                $output['main_url'] = $result['main_url'];
            }
        }
        return $output;
    }

}
