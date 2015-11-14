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


/** @noinspection PhpUndefinedClassInspection
 * @property ModelToolImage $model_tool_image
 * @property  ExtensionsAPI $extensions
 */
class AResource {
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
	 * @var array
	 */
	public $data = array();

	public $obj_list = array('products', 'categories', 'manufacturers', 'product_option_value');

	/**
	 * @param string $type
	 */
	public function __construct( $type ) {
		$this->registry = Registry::getInstance();
		//NOTE: Storefront can not access all resource at once. Resource type required
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

    public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

    protected function _loadType() {
		$cache_name = 'resources.'.$this->type;
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $type_data = $this->cache->get($cache_name,'', (int)$this->config->get('config_store_id'));
        if (empty($type_data['type_id'])) {
            $sql = "SELECT * "
                 . "FROM ".$this->db->table("resource_types") . " "
                 . "WHERE type_name = '" . $this->db->escape($this->type) . "'";
            $query = $this->db->query($sql);
            $type_data = $query->row;
            $this->cache->set($cache_name, $type_data,'', (int)$this->config->get('config_store_id') );
        }
        $this->type_id = $type_data['type_id'];
        $this->type_dir = $type_data['default_directory'];
        $this->type_icon = $type_data['default_icon'];
        $this->access_type = $type_data['access_type'];
        $this->file_types = $type_data['file_types'];
	}

    public function getTypeId() {
        return $this->type_id;
    }

    public function getType() {
        return $this->type;
    }

    public function getTypeDir() {
        return $this->type_dir;
    }

    public function getTypeIcon() {
        return $this->type_icon;
    }

    public function getTypeAccess() {
        return $this->access_type;
    }

    public function getTypeFileTypes() {
        return $this->file_types;
    }

	/**
	 * @param int $resource_id
	 * @return string
	 */
	public function getHexPath( $resource_id ) {
        $result = rtrim(chunk_split(dechex($resource_id), 2, '/'), '/');
	    return $result;
    }

	/**
	 * @param string $path
	 * @return null|number
	 */
	public function getIdFromHexPath( $path ) {
	    if(empty($path)){ return null; }
	    if(strpos($path,'/')!==false){
		    $ext = pathinfo($path,PATHINFO_EXTENSION);
		    $path = str_replace(array('.'.$ext,'/'),'',$path);
            $result = hexdec( $path );
	    }else{
		    $result = $this->_getIdByName($path);
	    }
	    return $result;
    }

	/**
	 * @param string $filename
	 * @return int
	 */
	private function _getIdByName($filename){
		$sql = "SELECT resource_id
                FROM " . $this->db->table("resource_descriptions") . " 
                WHERE name like '%".$this->db->escape($filename)."%'
                ORDER BY language_id";
        $query = $this->db->query($sql);
        return $query->row['resource_id'];
	}

	/**
	 * @param int $resource_id
	 * @param int $language_id
	 * @return array
	 */
	public function getResource ( $resource_id, $language_id = 0 ) {
		//Return resource details
		$resource_id = (int)$resource_id;
	    if ( !$resource_id) {
			return array();
	    }
        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
        }

		//attempt to load cache 
        $cache_name = 'resources.'. $resource_id;
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resource = $this->cache->get($cache_name );

        if (is_null($resource)) {

            $where = "WHERE rl.resource_id = ". $this->db->escape($resource_id);

            $sql = "SELECT
                        rd.*,
                        COALESCE(rd.resource_path,rdd.resource_path) as resource_path,
				        COALESCE(rd.resource_code,rdd.resource_code) as resource_code,
                        rt.type_name,
                        rt.default_icon
                    FROM " . $this->db->table("resource_library") . " rl " . "
                    LEFT JOIN " . $this->db->table("resource_descriptions") . " rd ON (rl.resource_id = rd.resource_id)
                    LEFT JOIN " . $this->db->table("resource_descriptions") . " rdd ON (rl.resource_id = rdd.resource_id AND rdd.language_id = '".$this->language->getDefaultLanguageID()."')
                    LEFT JOIN " . $this->db->table("resource_types") . " rt ON (rl.type_id = rt.type_id )
                    " . $where;

            $query = $this->db->query($sql);
            $result = $query->rows;
            $resource = array();
            foreach ( $result as $r ) {
                $resource[ $r['language_id'] ] = $r;
            }
            $this->cache->set($cache_name, $resource,'', (int)$this->config->get('config_store_id'));
        }

        $result = array();
		if ( !empty($resource[ $language_id ]) ) {
			$result = $resource[ $language_id ];
		} else if ( !empty( $resource ) ) {
			reset($resource);
			list($key, $result) = each($resource);
		}

        return $result;
	}

    public function getResourceThumb ( $resource_id, $width = '', $height = '', $language_id = '' ) {

        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
        }

        $resource = $this->getResource($resource_id, $language_id);
	    //check is resource have descriptions. if not - try to get it for default language
	    if(!$resource['name']){
		    $resource = $this->getResource($resource_id, $this->language->getDefaultLanguageID());
	    }

        switch( $this->type ) {
            case 'image' :
                if(!$resource['default_icon']){
                    $resource['default_icon'] = 'no_image.jpg';
                }
                break;
            default :
	            if(!$resource['default_icon']){
		            $resource['default_icon'] = 'no_image.jpg';
	            }
                $this->load->model('tool/image');
                $this->model_tool_image->resize($resource['default_icon'], $width, $height);
                return $this->model_tool_image->resize($resource['default_icon'], $width, $height);
        }

        if ( !empty($resource['resource_code']) ) {
            return $resource['resource_code'];
        }

	    $old_image = DIR_RESOURCE . $this->type_dir . $resource['resource_path'];
	    $info = pathinfo($old_image);
		$extension = $info['extension'];

	    if($extension!='ico'){
			if (!is_file($old_image)) {
			    $this->load->model('tool/image');
			    $this->model_tool_image->resize($resource['default_icon'], $width, $height);
                return $this->model_tool_image->resize($resource['default_icon'], $width, $height);
			}

			$name = preg_replace('/[^a-zA-Z0-9]/', '_', $resource['name']);
			//Build thumbnails path similar to resource library path
			$new_image = 'thumbnails/' . dirname($resource['resource_path']) . '/' . $name . '-' . $resource['resource_id'] . '-' . $width . 'x' . $height . '.' . $extension;
		    $this->_check_create_thumb($new_image, $old_image, $width, $height);

		    //do retina version
		    if($this->config->get('config_retina_enable')){
			    $new_image2x = 'thumbnails/' . dirname($resource['resource_path']) . '/' . $name . '-' . $resource['resource_id'] . '-' . $width . 'x' . $height . '@2x.' . $extension;
			    $this->_check_create_thumb($new_image2x, $old_image, $width*2, $height*2);
		    }

		    $this->extensions->hk_ProcessData($this, __FUNCTION__);
		    $http_path = $this->data['http_dir'];
		    if(!$http_path){
                if( HTTPS===true){
                    $http_path = HTTPS_IMAGE;
                } else{
                    $http_path = HTTP_IMAGE;
                }
            }

			return $http_path . $new_image;

	    } else { // returns ico-file as is
	    	return $this->buildResourceURL($resource['resource_path'], 'full');
	    }
    }
	private function _check_create_thumb($filename, $resource_filename, $width, $height){
		if (!file_exists(DIR_IMAGE . $filename) || (filemtime($resource_filename) > filemtime(DIR_IMAGE . $filename))) {
			$path = '';
			$directories = explode('/', dirname(str_replace('../', '', $filename)));
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!file_exists(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
					chmod(DIR_IMAGE . $path, 0777);
				}
			}

			$image = new AImage($resource_filename);
			$image->resize($width, $height);
			$image->save(DIR_IMAGE . $filename);
			unset($image);
		}
	}


	/**
	 * @param string $resource_path (hashed resource path from database) 
	 * @param string $mode full (with http and domain) or relative (from store url up)
	 * @return array
	 */
    public function buildResourceURL ( $resource_path, $mode = 'full' ) {

		if ( $mode == 'full') {
			$this->extensions->hk_ProcessData($this, __FUNCTION__);
			$http_path = $this->data['http_dir'];
			if(!$http_path){
				$http_path = HTTPS_DIR_RESOURCE;
			}
			return $http_path . $this->type_dir . $resource_path;
		} else {
			return "/resources/" . $this->type_dir . $resource_path;
		}
	}

	/**
	 * @param string $object_name
	 * @param string $object_id
	 * @param int $language_id
	 * @return array
	 */
	public function getResources ( $object_name, $object_id, $language_id = 0 ) {
		//Allow to load resources only for 1 object and id combination
	    if ( !has_value($object_name) || !has_value($object_id) ) {
			return array();
	    }

        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
        }

		//attempt to load cache 
        $cache_name = 'resources.'.$this->type
                      .'.'. $object_name
                      .'.'.$object_id;
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $resources = $this->cache->get($cache_name, $language_id, (int)$this->config->get('config_store_id'));
        if (!is_null($resources)) {
            return $resources;
        }

		$where = "WHERE rm.object_name = '" . $this->db->escape( $object_name ) . "' "
				. " and rm.object_id = '" . $this->db->escape( $object_id ) . "' "
				. " and rl.type_id = ". $this->db->escape( $this->type_id );

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
				LEFT JOIN " . $this->db->table("resource_map") . " rm ON rm.resource_id = rl.resource_id " . "
				LEFT JOIN " . $this->db->table("resource_descriptions") . " rd ON (rl.resource_id = rd.resource_id AND rd.language_id = '".$language_id."')
				LEFT JOIN " . $this->db->table("resource_descriptions") . " rdd ON (rl.resource_id = rdd.resource_id AND rdd.language_id = '".$this->language->getDefaultLanguageID()."')
				" . $where . "
				ORDER BY rm.sort_order ASC";
					
		$query = $this->db->query($sql);
		$resources = $query->rows;
        $this->cache->set($cache_name, $resources, $language_id, (int)$this->config->get('config_store_id'));
		
		return $resources;
	}

    public function getAllResourceTypes() {
        //attempt to load cache
        $cache_name = 'resources.types';
        $types = $this->cache->get($cache_name, '', (int)$this->config->get('config_store_id'));
        if (!is_null($types)) {
            return $types;
        }

		$sql = "SELECT * FROM " . $this->db->table("resource_types") . " ";
		$query = $this->db->query($sql);
		$types = $query->rows;
        $this->cache->set($cache_name, $types, '', (int)$this->config->get('config_store_id'));

		return $types;
	}

    //TODO: define where all object types will be kept and fetch them from storage
    public function getAllObjects() {
        return $this->obj_list;
    }

	/**
	 * method returns all resources of object by it's id and name
	 * @param string $object_name
	 * @param string $object_id
	 * @param array $sizes
	 * @param int $limit
	 * @param bool $noimage
	 * @return array
	 */
	public function getResourceAllObjects($object_name, $object_id, $sizes=array('main'=>array(),'thumb'=>array(), 'thumb2'=>array()), $limit=0, $noimage=true){
		if(!$object_id || !$object_name ) return array();
		$limit = (int)$limit;
		$results = $this->getResources($object_name, $object_id);
		if(!$results && !$limit){ return array(); }

		if($limit && !$noimage){
			$slice_limit = $limit>sizeof($results) ? sizeof($results) : $limit;
			$results = array_slice($results, 0, $slice_limit);
		}

		$this->load->model('tool/image');

		if(!$sizes || !is_array($sizes['main']) || !is_array($sizes['thumb']) ){
			if(!is_array($sizes['main'])){
				$sizes['main'] = array('width'=> $this->config->get('config_image_product_width'),
									   'height'=> $this->config->get('config_image_product_height'));
			}
			if(!is_array($sizes['thumb'])){
				$sizes['thumb'] = array('width'=> $this->config->get('config_image_thumb_width'),
										'height'=> $this->config->get('config_image_thumb_height'));
			}
		}

		$resources = array();

		if(!$results && $noimage){
			$results = array(array('resource_path'=>'no_image.jpg'));
		}

		if(!$results){
			return array();
		}

		foreach ($results as $k=>$result) {

			$resource_info = $result['resource_id'] ? $this->getResource($result['resource_id'], $this->config->get('storefront_language_id') ) : $result;
		 	$origin = $resource_info['resource_path'] ? 'internal' : 'external';
			
			if($origin=='internal'){
				$this->extensions->hk_ProcessData($this, __FUNCTION__);
				$http_path = $this->data['http_dir'];
				if(!$http_path){
					$http_path = HTTPS_DIR_RESOURCE;					
				}
				
				if($sizes['thumb']){
					$thumb_url = $this->getResourceThumb($result['resource_id'],$sizes['thumb']['width'],$sizes['thumb']['height']);
				}

				if(!$thumb_url && $sizes['thumb']){
					$thumb_url = $this->model_tool_image->resize($result['resource_path'],$sizes['thumb']['width'],$sizes['thumb']['height']);
				}
				//thumb2 - big thumbnails
				if($sizes['thumb2']){
					$thumb2_url = $this->getResourceThumb($result['resource_id'],$sizes['thumb2']['width'],$sizes['thumb2']['height']);
				}
				if(!$thumb2_url && $sizes['thum2b']){
					$thumb2_url = $this->model_tool_image->resize($result['resource_path'],$sizes['thumb2']['width'],$sizes['thumb2']['height']);
				}

				if($this->getTypeDir()=='image/'){
					if(!$sizes['main']){
						$main_url = $this->getResourceThumb($result['resource_id'],$sizes['main']['width'],$sizes['main']['height']);
					}else{ // return href for image with size as-is
						$main_url = $http_path.$this->getTypeDir().$result['resource_path'];
					}
				}else{
					$main_url = $http_path.$this->getTypeDir().$result['resource_path'];
				}

				$resources[$k] = array( 'origin' => $origin,
										'main_url' => $main_url,
										'main_html'=>$this->html->buildResourceImage( array('url' => $http_path.'image/'.$result['resource_path'],
										                                                    'width' => $sizes['main']['width'],
										                                                    'height' => $sizes['main']['height'],
										                                                    'attr' => 'alt="' . $resource_info['title'] . '"') ),
										'thumb_url' => $thumb_url,
										'thumb_html' => $this->html->buildResourceImage( array('url' => $thumb_url,
										                                                    'width' => $sizes['thumb']['width'],
										                                                    'height' => $sizes['thumb']['height'],
										                                                    'attr' => 'alt="' . $resource_info['title'] . '"') ));
				if($sizes['thumb2']){
					$resources[$k]['thumb2_url'] = $thumb2_url ;
					$resources[$k]['thumb2_html'] = $this->html->buildResourceImage(array('url'    => $thumb2_url,
										                                                  'width'  => $sizes['thumb2']['width'],
										                                                  'height' => $sizes['thumb2']['height'],
										                                                  'attr'   => 'alt="' . $resource_info['title'] . '"'));
				}
				$resources[$k]['description'] =  $resource_info['decription'];
				$resources[$k]['title'] = $resource_info['title'];

			}else{
				$resources[$k] = array( 'origin' => $origin,
										'main_html'=>$resource_info['resource_code'],
										'thumb_html' => $resource_info['resource_code'],
										'title' => $resource_info['title'],
										'description' =>  $resource_info['decription']);
			}
		}

		if($limit==1){ 
			$resources = $resources[0];
		}

		return $resources;
	}

	/**
	 * @param string $object_name
	 * @param string $object_id
	 * @param int $width
	 * @param int $height
	 * @param bool $noimage
	 * @return array
	 */
	public function getMainThumb($object_name, $object_id, $width, $height, $noimage=true ){
		$sizes = array('thumb' => array('width' => $width, 'height' => $height));
		$result = $this->getResourceAllObjects($object_name, $object_id, $sizes,1, $noimage);
		$output = array();
		if($result){
			$output = array( 'origin' => $result['origin'],
							 'thumb_html'=>$result['thumb_html'],
							 'title'=>$result['title'],
							 'description'=>$result['description']
			);
			if($result['thumb_url']) $output['thumb_url'] = $result['thumb_url'];
		}
		return $output;
	}

	/**
	 * @param string $object_name
	 * @param string $object_id
	 * @param int $width
	 * @param int $height
	 * @param bool $noimage
	 * @return array
	 */
	public function getMainImage($object_name, $object_id, $width, $height, $noimage=true){
		$sizes=array('main'=>array('width'=>$width, 'height'=> $height));
		$result =  $this->getResourceAllObjects($object_name, $object_id, $sizes,1, $noimage);
		$output = array();
		if($result){
			$output = array( 'origin' => $result['origin'],
							'main_html'=>$result['main_html'],
							'description'=>$result['description'],
							'title'=>$result['title']
			);
			if($result['main_url']) $output['main_url'] = $result['main_url'];
		}
		return $output;
	}

}