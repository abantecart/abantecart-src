<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

/** @noinspection PhpMultipleClassDeclarationsInspection */

class AResource
{
    /** @var array */
    public $data = [];
    public $obj_list = [
        'products',
        'categories',
        'manufacturers',
        'product_option_value',
        'storefront_menu_item',
        'field'
    ];
    /** @var Registry */
    protected $registry;
    /** @var string */
    protected $type;
    /** @var int */
    protected $type_id;
    /** @var string */
    protected $type_dir;
    /** @var string */
    protected $type_icon;
    /** @var string */
    protected $access_type;
    /** @var array */
    protected $file_types;

    /** @var ExtensionsApi */
    protected $extensions;
    /** @var ALoader */
    protected $load;
    /** @var AHtml */
    protected $html;
    /** @var AConfig */
    protected $config;
    /** @var ACache */
    protected $cache;
    /** @var ADB */
    protected $db;
    /** @var ALanguage|ALanguageManager */
    protected $language;

    /**
     * @param string $type
     *
     * @throws AException
     */
    public function __construct(string $type = 'image')
    {
        $this->registry = Registry::getInstance();
        $this->extensions = $this->registry->get('extensions');
        $this->load = $this->registry->get('load');
        $this->html = $this->registry->get('html');
        $this->config = $this->registry->get('config');
        $this->cache = $this->registry->get('cache');
        $this->db = $this->registry->get('db');
        $this->language = $this->registry->get('language');


        //NOTE: Storefront cannot access all resources at once. Resource type required
        if ($type) {
            $this->type = $type;
            //get type details
            $this->_loadType();
        }

        if (!$this->type_id) {
            $backtrace = debug_backtrace();
            $message = "Error: Incorrect or missing resource type." . $backtrace[0]['file'] . ":" . $backtrace[0]['line'];
            $error = new AWarning($message);
            $error->toLog()->toDebug();
        }
    }

    protected function _loadType()
    {
        $cache_key = 'resources.' . $this->type;
        $cache_key = preg_replace('/[^a-zA-Z0-9.]/', '', $cache_key)
            . '.store_' . (int)$this->config->get('config_store_id');
        $type_data = $this->cache->pull($cache_key);
        if ($type_data === false || empty($type_data['type_id'])) {
            $sql = "SELECT * 
                    FROM " . $this->db->table("resource_types") . " 
                    WHERE type_name = '" . $this->db->escape($this->type) . "'";
            $query = $this->db->query($sql);
            $type_data = $query->row;
            $this->cache->push($cache_key, $type_data);
        }
        $this->type_id = (int)$type_data['type_id'];
        $this->type_dir = str_replace('/', DS, $type_data['default_directory']);
        $this->type_icon = $type_data['default_icon'];
        $this->access_type = $type_data['access_type'];
        $this->file_types = $type_data['file_types'];
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * @return string
     */
    public function getTypeIcon()
    {
        return $this->type_icon;
    }

    /**
     * @return string
     */
    public function getTypeAccess()
    {
        return $this->access_type;
    }

    /**
     * @return array
     */
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
        return rtrim(chunk_split(dechex($resource_id), 2, '/'), '/');
    }

    /**
     * @param string $path
     *
     * @return null|int
     * @throws AException
     * @throws AException
     */
    public function getIdFromHexPath($path)
    {
        if (empty($path)) {
            return null;
        }
        if (str_contains($path, DS)) {
            //find first in file to solve the "tar.gz" problem
            if (preg_match("/\.tar\.gz$/i", $path)) {
                $ext = 'tar.gz';
            } else {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
            }
            $path = str_replace(['.' . $ext, DS], '', $path);
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
     * @throws AException
     */
    public function getIdByName(string $filename = '')
    {
        if (!$filename) {
            return 0;
        }
        $sql = "SELECT resource_id
                FROM " . $this->db->table("resource_descriptions") . "
                WHERE name like '%" . $this->db->escape($filename) . "%'
                ORDER BY language_id";
        $query = $this->db->query($sql);
        return (int)$query->row['resource_id'];
    }

    /**
     * function returns URL to resource.
     *
     * @param int $resourceId - NOTE: can be zero to show default_image
     * @param int $width
     * @param int $height
     * @param string|int $languageId
     *
     * @return string
     * @throws AException
     *
     */
    public function getResourceThumb($resourceId, $width, $height, $languageId = 0)
    {
        $width = (int)$width;
        $height = (int)$height;
        if (!$width || !$height) {
            return '';
        }

        if (!$languageId) {
            $languageId = $this->language->getDefaultLanguageID();
        }

        if ($resourceId) {
            $resourceInfo = $this->getResource($resourceId, $languageId);
            //check if a resource has descriptions. if not - try to get it for the default language
            if (!$resourceInfo['name'] && $languageId != $this->language->getDefaultLanguageID()) {
                $resourceInfo = $this->getResource($resourceId, $this->language->getDefaultLanguageID());
            }
            return $this->getResizedImageURL($resourceInfo, $width, $height);
        } else {
            return '';
        }
    }

    /**
     * @param int $resource_id
     * @param int $language_id
     *
     * @return array
     * @throws AException
     */
    public function getResource($resource_id, $language_id = 0)
    {
        //Return resource details
        $resource_id = (int)$resource_id;
        if (!$resource_id) {
            return [];
        }
        if (!$language_id) {
            $language_id = $this->config->get('storefront_language_id');
        }

        //attempt to load cache
        $cache_key = 'resources.' . $resource_id;
        $cache_key = preg_replace('/[^a-zA-Z0-9.]/', '', $cache_key);
        $resource = $this->cache->pull($cache_key);
        if ($resource === false) {
            $where = "WHERE rl.resource_id = " . $this->db->escape($resource_id);
            $sql = "SELECT  rd.*,
                            COALESCE(rd.resource_path,rdd.resource_path) as resource_path,
                            COALESCE(rd.resource_code,rdd.resource_code) as resource_code,
                            rt.type_name,
                            rt.default_directory as type_dir,
                            rt.default_icon
                    FROM " . $this->db->table("resource_library") . " rl " . "
                    LEFT JOIN " . $this->db->table("resource_descriptions") . " rd
                        ON (rl.resource_id = rd.resource_id)
                    LEFT JOIN " . $this->db->table("resource_descriptions") . " rdd
                        ON (rl.resource_id = rdd.resource_id 
                            AND rdd.language_id = '" . $this->language->getDefaultLanguageID() . "')
                    LEFT JOIN " . $this->db->table("resource_types") . " rt
                        ON (rl.type_id = rt.type_id )
                    " . $where;

            $query = $this->db->query($sql);
            $result = $query->rows;
            $resource = [];
            foreach ($result as $r) {
                $resource[$r['language_id']] = $r;
            }
            $this->cache->push($cache_key, $resource);
        }

        $result = [];
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
     * @param array $resourceInfo - resource details
     * @param int $width - if 0 - original size
     * @param int $height - if 0 - original size
     *
     * @return string
     * @throws AException
     * @since 1.2.7
     *
     */
    public function getResizedImageURL(array $resourceInfo, $width = 0, $height = 0)
    {
        $resource_id = (int)$resourceInfo['resource_id'];
        $resourceInfo['default_icon'] = $resourceInfo['default_icon'] ?? '';
        //get original file path and details
        $origin_path = DIR_RESOURCE . $this->type_dir . str_replace('/', DS, $resourceInfo['resource_path']);
        $info = pathinfo($origin_path);
        $extension = $info['extension'] ?? '';
        if (in_array($extension, ['ico', 'svg', 'svgz'])) {
            // returns ico-file as original
            return $this->buildResourceURL($resourceInfo['resource_path'], 'full');
        }

        $type_image = is_file(DIR_IMAGE . 'icon_resource_' . $this->type . '.png')
            ? 'icon_resource_' . $this->type . '.png'
            : '';

        //is this a resource with code ?
        if (!empty($resourceInfo['resource_code'])) {
            //we have resource code, nothing to do
            return $resourceInfo['resource_code'];
        }
        //is this image resource
        switch ($this->type) {
            case 'image' :
                if (!$resourceInfo['default_icon']) {
                    $resourceInfo['default_icon'] = 'no_image.jpg';
                }
                if (!$resourceInfo['resource_path']) {
                    $origin_path = '';
                }
                break;
            default :
                //this is a non-image type return original
                if (!$resourceInfo['default_icon'] && !$type_image) {
                    $resourceInfo['default_icon'] = 'no_image.jpg';
                    $origin_path = '';
                } elseif ($type_image) {
                    $resourceInfo['default_icon'] = $type_image;
                    $origin_path = '';
                } else {
                    return $this->buildResourceURL($resourceInfo['resource_path'], 'full');
                }
        }

        $width = (int)$width;
        $height = (int)$height;
        if (!$width || !$height) {
            //if no size, return the original
            return $this->buildResourceURL($resourceInfo['resource_path'], 'full');
        }

        //resource name MUST be provided here, if missing use resource ID.
        if (!$resourceInfo['name']) {
            $resourceInfo['name'] = $resource_id ?: '';
        }
        $name = preg_replace('/[^a-zA-Z0-9]/', '_', $resourceInfo['name']);

        if (!is_file($origin_path) || !$resource_id) {
            //missing original resource. oops
            /** @var ModelToolImage $mdl */
            $mdl = $this->load->model('tool/image');
            return $mdl->resize($resourceInfo['default_icon'], $width, $height);
        } else {
            //Build thumbnail's path similar to a resource library path
            $sub_path = 'thumbnails' . DS
                . str_replace('/', DS, dirname($resourceInfo['resource_path'])) . DS
                . $name . '-'
                . $resource_id . '-'
                . $width . 'x' . $height;
            $new_image = $sub_path . '.' . $extension;
            if (!check_resize_image(
                $origin_path, $new_image, $width, $height, $this->config->get('config_image_quality')
            )) {
                $warning = new AWarning('Resize image error. File: ' . $origin_path);
                $warning->toLog()->toDebug();
                return null;
            }
            //do retina version
            if ($this->config->get('config_retina_enable')) {
                $new_image2x = $sub_path . '@2x.' . $extension;
                if (!check_resize_image(
                    $origin_path, $new_image2x, $width * 2, $height * 2, $this->config->get('config_image_quality')
                )) {
                    $warning = new AWarning('Resize image error. File: ' . $origin_path);
                    $warning->toLog()->toDebug();
                }
            }
            //hook here to affect this image
            $this->extensions->hk_ProcessData($this, __FUNCTION__);
            //prepend URL and return
            $http_path = $this->data['http_dir'] ?? '';
            if (!$http_path) {
                $http_path = HTTPS_IMAGE;
            }
            return $http_path . str_replace(DS, '/', $new_image);
        }
    }

    /**
     * @param string $resource_path (hashed resource path from a database)
     * @param string $mode full (with http and domain) or relative (from store url up)
     *
     * @return string
     */
    public function buildResourceURL($resource_path, $mode = 'full')
    {
        if ($mode == 'full') {
            $this->extensions->hk_ProcessData($this, __FUNCTION__);
            $http_path = $this->data['http_dir'] ?? '';
            if (!$http_path) {
                $http_path = HTTPS_DIR_RESOURCE;
            }
            return $http_path . $this->type_dir . $resource_path;
        } else {
            return "/resources/" . $this->type_dir . $resource_path;
        }
    }

    /**
     * @return array
     * @throws AException
     */
    public function getAllResourceTypes()
    {
        //attempt to load cache
        $cache_key = 'resources.types.store_' . (int)$this->config->get('config_store_id');
        $types = $this->cache->pull($cache_key);
        if ($types !== false) {
            return $types;
        }

        $sql = "SELECT * FROM " . $this->db->table("resource_types") . " ";
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
     * @param int $width
     * @param int $height
     * @param bool $noimage
     *
     * @return array
     * @throws AException
     * @throws AException
     */
    public function getMainThumb($object_name, $object_id, $width, $height, $noimage = true)
    {
        $sizes = [
            'thumb' => [
                'width'  => $width,
                'height' => $height,
            ],
        ];
        $result = $this->getResourceAllObjects($object_name, $object_id, $sizes, 1, $noimage);
        $output = [];
        if ($result) {
            $output = [
                'origin'      => $result['origin'],
                'thumb_html'  => $result['thumb_html'],
                'title'       => $result['title'],
                'description' => $result['description'],
                'width'       => $width,
                'height'      => $height,
            ];
            if ($result['thumb_url']) {
                $output['thumb_url'] = $result['thumb_url'];
            }
        }
        return $output;
    }

    /**
     * method returns all resources of an object by id and name
     *
     * @param string $object_name
     * @param string $object_id
     * @param array $sizes
     * @param int $limit
     * @param bool $noimage - replace missing image with no_image_jpg
     *
     * @return array
     * @throws AException
     */
    public function getResourceAllObjects(
        $object_name,
        $object_id,
        $sizes = ['orig' => [], 'main' => [], 'thumb' => [], 'thumb2' => []],
        $limit = 0,
        $noimage = true
    )
    {
        if (!$object_id || !$object_name) {
            return [];
        }
        $limit = (int)$limit;
        $results = $this->getResources($object_name, $object_id);
        if (!$results && !$limit) {
            return [];
        }

        if ($limit && !$noimage) {
            $slice_limit = min($limit, sizeof($results));
            $results = array_slice($results, 0, $slice_limit);
        }

        /** @var ModelToolImage $mdl */
        $mdl = $this->load->model('tool/image');
        if (!$sizes || !is_array($sizes['main']) || !is_array($sizes['thumb'])) {
            if (!is_array($sizes['main'])) {
                $sizes['main'] = [
                    'width'  => $this->config->get('config_image_product_width'),
                    'height' => $this->config->get('config_image_product_height'),
                ];
            }
            if (!is_array($sizes['thumb'])) {
                $sizes['thumb'] = [
                    'width'  => $this->config->get('config_image_thumb_width'),
                    'height' => $this->config->get('config_image_thumb_height'),
                ];
            }
        }

        $resources = [];
        if (!$results && $noimage && $this->getType() == 'image') {
            $results = [
                [
                    'resource_path' => 'no_image.jpg',
                ],
            ];
        }

        if (!$results) {
            return [];
        }

        foreach ($results as $k => $result) {
            $thumb_url = $thumb2_url = '';
            $resourceInfo = $result['resource_id']
                ? $this->getResource((int)$result['resource_id'], $this->config->get('storefront_language_id'))
                : $result;
            $origin = $resourceInfo['resource_path'] ? 'internal' : 'external';
            if ($origin == 'internal') {
                $this->extensions->hk_ProcessData($this, __FUNCTION__);
                $http_path = $this->data['http_dir'];
                if (!$http_path) {
                    $http_path = HTTPS_DIR_RESOURCE;
                }

                $direct_url = $http_path . $this->getTypeDir() . $resourceInfo['resource_path'];
                $res_full_path = '';
                if ($this->getType() == 'image') {
                    $res_full_path = DIR_RESOURCE . $this->getTypeDir() . str_replace('/', DS, $resourceInfo['resource_path']);
                    if ($sizes['main']) {
                        $main_url = $this->getResizedImageURL(
                            $resourceInfo,
                            $sizes['main']['width'],
                            $sizes['main']['height']
                        );
                    } else {
                        // return href for the image with size as-is
                        $main_url = $http_path . $this->getTypeDir() . $resourceInfo['resource_path'];
                        //get original image size
                        $actual_sizes = get_image_size($res_full_path);
                        $sizes['main'] = $actual_sizes;
                    }
                    if ($sizes['thumb']) {
                        $thumb_url = $this->getResizedImageURL(
                            $resourceInfo,
                            $sizes['thumb']['width'],
                            $sizes['thumb']['height']
                        );
                    }

                    if (!$thumb_url && $sizes['thumb']) {
                        $thumb_url = $mdl->resize(
                            str_replace('/', DS, $resourceInfo['resource_path']),
                            $sizes['thumb']['width'],
                            $sizes['thumb']['height']
                        );
                    }
                    //thumb2 - big thumbnails
                    if ($sizes['thumb2']) {
                        $thumb2_url = $this->getResizedImageURL(
                            $resourceInfo,
                            $sizes['thumb2']['width'],
                            $sizes['thumb2']['height']
                        );
                    }
                    if (!$thumb2_url && $sizes['thumb2']) {
                        $thumb2_url = $mdl->resize(
                            str_replace('/', DS, $resourceInfo['resource_path']),
                            $sizes['thumb2']['width'],
                            $sizes['thumb2']['height']
                        );
                    }
                    try {
                        $origin_path = DIR_RESOURCE . $this->getTypeDir() . str_replace('/', DS, $resourceInfo['resource_path']);
                        $img = new AImage($origin_path);
                        $sizes['orig'] = $img->getInfo();
                    } catch (Exception|Error) {
                    }
                } else {
                    $main_url = $direct_url;
                    $thumb_url = $this->getResizedImageURL(
                        $resourceInfo,
                        $sizes['thumb']['width'],
                        $sizes['thumb']['height']
                    );
                }

                $resources[$k] = [
                    'resource_id'   => $resourceInfo['resource_id'],
                    'origin'        => $origin,
                    'direct_url'    => $direct_url,
                    'info'          => $sizes['orig'],
                    //set a full path to the original file only for images (see above)
                    'resource_path' => $res_full_path,
                    'main_url'      => $main_url,
                    'main_width'    => $sizes['main']['width'],
                    'main_height'   => $sizes['main']['height'],
                    'main_html'     => $this->html->buildResourceImage(
                        [
                            'url'    => $http_path . 'image/' . $resourceInfo['resource_path'],
                            'width'  => $sizes['main']['width'],
                            'height' => $sizes['main']['height'],
                            'attr'   => 'alt="' . html2view($resourceInfo['title']) . '"',
                        ]
                    ),
                    'thumb_url'     => $thumb_url,
                    'thumb_width'   => $sizes['thumb']['width'],
                    'thumb_height'  => $sizes['thumb']['height'],
                    'thumb_html'    => $this->html->buildResourceImage(
                        [
                            'url'    => $thumb_url,
                            'width'  => $sizes['thumb']['width'],
                            'height' => $sizes['thumb']['height'],
                            'attr'   => 'alt="' . html2view($resourceInfo['title']) . '" ',
                        ]
                    ),
                ];
                if ($sizes['thumb2']) {
                    $resources[$k]['thumb2_url'] = $thumb2_url;
                    $resources[$k]['thumb2_width'] = $sizes['thumb2']['width'];
                    $resources[$k]['thumb2_height'] = $sizes['thumb2']['height'];
                    $resources[$k]['thumb2_html'] = $this->html->buildResourceImage(
                        [
                            'url'    => $thumb2_url,
                            'width'  => $sizes['thumb2']['width'],
                            'height' => $sizes['thumb2']['height'],
                            'attr'   => 'alt="' . html2view($resourceInfo['title']) . '"',
                        ]
                    );
                }
                $resources[$k]['description'] = $resourceInfo['description'];
                $resources[$k]['title'] = $resourceInfo['title'];
            } else {
                $resources[$k] = [
                    'origin'      => $origin,
                    'main_html'   => $resourceInfo['resource_code'],
                    'thumb_html'  => $resourceInfo['resource_code'],
                    'title'       => $resourceInfo['title'],
                    'description' => $resourceInfo['description'],
                ];
            }

            if ($limit && count($resources) == $limit) {
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
     * @param int $language_id
     *
     * @return array
     * @throws AException
     */
    public function getResources($object_name, $object_id, $language_id = 0)
    {
        //Allow loading resources only for 1 object and id combination
        if (!$object_name || !$object_id) {
            return [];
        }
        $language_id = $language_id ?: $this->config->get('storefront_language_id');

        //attempt to load cache
        $cache_key = 'resources.object.'
            . md5(
                $this->type . '.'
                . $object_name . '.'
                . $object_id . '.'
                . $this->config->get('config_url') . '.'
                . $language_id
            );
        $resources = $this->cache->pull($cache_key);
        if ($resources !== false) {
            return $resources;
        }

        $where = "WHERE rm.object_name = '" . $this->db->escape($object_name) . "' "
            . " and rm.object_id = '" . $this->db->escape($object_id) . "' "
            . " and rl.type_id = " . $this->db->escape($this->type_id);

        $sql = "SELECT
                    rl.resource_id,
                    rd.name,
                    rd.title,
                    rd.description,
                    COALESCE(rd.resource_path,rdd.resource_path) as resource_path,
                    COALESCE(rd.resource_code,rdd.resource_code) as resource_code,
                    rm.default,
                    rm.sort_order
                FROM " . $this->db->table("resource_library") . " rl " . "
                LEFT JOIN " . $this->db->table("resource_map") . " rm
                    ON rm.resource_id = rl.resource_id " . "
                LEFT JOIN " . $this->db->table("resource_descriptions") . " rd
                    ON (rl.resource_id = rd.resource_id AND rd.language_id = '" . $language_id . "')
                LEFT JOIN " . $this->db->table("resource_descriptions") . " rdd
                    ON (rl.resource_id = rdd.resource_id 
                        AND rdd.language_id = '" . $this->language->getDefaultLanguageID() . "')
                " . $where . "
                ORDER BY rm.sort_order ASC";

        $query = $this->db->query($sql);
        $resources = $query->rows;
        $this->cache->push($cache_key, $resources);
        return $resources;
    }

    /**
     * @return string
     */
    public function getTypeDir()
    {
        return $this->type_dir;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $object_name
     * @param array $object_ids
     * @param int $width
     * @param int $height
     * @param bool|true $noimage
     *
     * @return array
     * @throws AException
     * @since 1.2.7
     *
     */
    public function getMainThumbList($object_name, $object_ids = [], $width = 0, $height = 0, $noimage = true)
    {
        $width = (int)$width;
        $height = (int)$height;
        if (is_array($object_ids) && !count($object_ids)) {
            return [];
        }

        if (!$object_name || !$object_ids || !is_array($object_ids) || !$width || !$height) {
            $this->registry->get('log')->write(
                __METHOD__ . " Wrong input parameters.\n " . var_export(func_get_args(), true)
            );
            return [];
        }
        //cleanup ids
        $tmp = [];
        foreach ($object_ids as $object_id) {
            $object_id = (int)$object_id;
            if ($object_id) {
                $tmp[] = $object_id;
            }
        }
        $object_ids = array_unique($tmp);
        unset($tmp);

        if (!$object_ids) {
            return [];
        }

        $language_id = $this->language->getLanguageID();
        $default_language_id = $this->language->getDefaultLanguageID();

        //attempt to load cache
        $cache_key = 'resources.list.'
            . $this->type
            . '.' . md5(
                implode('.', $object_ids)
                . implode('.', func_get_args())
                . $this->config->get('config_url')
                . $language_id
            );

        $output = $this->cache->pull($cache_key);
        if ($output !== false) {
            return $output;
        }

        //get resource list
        $sql = "SELECT
                rm.object_id,
                rl.resource_id,
                COALESCE(rd.name,rdd.name) as name,
                COALESCE(rd.title,rdd.title) as title,
                COALESCE(rd.description,rdd.description) as description,
                COALESCE(rd.resource_path,rdd.resource_path) as resource_path,
                COALESCE(rd.resource_code,rdd.resource_code) as resource_code,
                rm.default,
                rm.sort_order
            FROM " . $this->db->table("resource_library") . " rl " . "
            LEFT JOIN " . $this->db->table("resource_map") . " rm
                ON rm.resource_id = rl.resource_id " . "
            LEFT JOIN " . $this->db->table("resource_descriptions") . " rd
                ON (rl.resource_id = rd.resource_id
                    AND rd.language_id = '" . $language_id . "')
            LEFT JOIN " . $this->db->table("resource_descriptions") . " rdd
                ON (rl.resource_id = rdd.resource_id
                    AND rdd.language_id = '" . $default_language_id . "')
            WHERE rm.object_name = '" . $this->db->escape($object_name) . "'
                 AND rl.type_id = " . $this->type_id . "
                 AND rm.object_id IN (" . implode(", ", $object_ids) . ")
            ORDER BY rm.object_id ASC, rm.sort_order ASC, rl.resource_id ASC";
        $result = $this->db->query($sql);

        $output = $selected_ids = [];
        foreach ($result->rows as $row) {
            $object_id = $row['object_id'];
            //filter only the first resource per object (main)
            if (isset($output[$object_id])) {
                continue;
            }

            $origin = $row['resource_path'] ? 'internal' : 'external';
            $output[$object_id] = [
                'resource_id' => $row['resource_id'],
                'origin'      => $origin,
                'title'       => $row['title'],
                'description' => $row['description'],
                'width'       => $width,
                'height'      => $height,
            ];
            //for external resources
            if ($origin == 'external') {
                $output[$object_id]['thumb_html'] = $row['resource_code'];
            } //for internal resources
            else {
                $thumb_url = $this->getResizedImageURL($row, $width, $height);
                $output[$object_id]['thumb_html'] = $this->html->buildResourceImage(
                    [
                        'url'    => $thumb_url,
                        'width'  => $width,
                        'height' => $height,
                        'attr'   => 'alt="' . html2view($row['title']) . '" title="' . html2view($row['title']) . '" ',
                    ]
                );
                $output[$object_id]['thumb_url'] = $thumb_url;
            }
            $selected_ids[] = $object_id;
        }

        //if some of the objects have no thumbnail
        $diff = array_diff($object_ids, $selected_ids);
        if ($diff) {
            foreach ($diff as $object_id) {
                //when need to show the default image
                if ($noimage) {
                    $thumb_url = $this->getResizedImageURL(['resource_id' => 0], $width, $height);

                    $output[$object_id] = [
                        'origin'      => 'internal',
                        'title'       => '',
                        'description' => '',
                        'width'       => $width,
                        'height'      => $height,
                        'thumb_url'   => $thumb_url,
                        'thumb_html'  => $this->html->buildResourceImage(
                            [
                                'url'    => $thumb_url,
                                'width'  => $width,
                                'height' => $height,
                                'attr'   => 'alt=""',
                            ]
                        ),
                    ];
                } else {
                    $output[$object_id] = [];
                }
            }
        }

        $this->cache->push($cache_key, $output);
        return $output;
    }

    /**
     * @param string $object_name
     * @param string $object_id
     * @param int $width
     * @param int $height
     * @param bool $noimage
     *
     * @return array
     * @throws AException
     */
    public function getMainImage($object_name, $object_id, $width, $height, $noimage = true)
    {
        $sizes = [
            'main' => [
                'width'  => $width,
                'height' => $height,
            ],
        ];
        $result = $this->getResourceAllObjects($object_name, $object_id, $sizes, 1, $noimage);
        $output = [];
        if ($result) {
            $output = [
                'origin'      => $result['origin'],
                'main_html'   => $result['main_html'],
                'description' => $result['description'],
                'title'       => $result['title'],
                'width'       => $width,
                'height'      => $height,
            ];
            if ($result['main_url']) {
                $output['main_url'] = $result['main_url'];
            }
        }
        return $output;
    }
}