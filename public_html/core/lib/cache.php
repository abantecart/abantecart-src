<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
/**
 * Class ACache
 */

final class ACache {
	/**
	 * @var int
	 */
	private $expire = 86400; //one day
	/**
	 * @var Registry
	 */
	private $registry;

	public function __construct(){
		$this->registry = Registry::getInstance();

		if(!is_writeable(DIR_CACHE)){
			$error_text = '';
			$log = $this->registry->get('log');
			if(!is_object($log) || !method_exists($log, 'write')){
				$error_text = 'Error: Unable to access or write to cache directory ' . DIR_CACHE;
				$log = new ALog(DIR_SYSTEM . 'logs/error.txt');
				$this->registry->set('log', $log);
			}
			$log->write($error_text);
			//try to add message for admin (check if for install-process too)
			$db = $this->registry->get('db');

			if(is_object($db) && method_exists($db, 'query')){
				$error_text .= ' Cache feature was disabled. Check permissions on directory and enable setting back.';
				$m = new AMessage();
				$m->saveError('AbanteCart Warning', $error_text);
				//also disable caching in config
				$sql = "UPDATE ".$db->table('settings')."
						SET `value` = '0'
						WHERE `key` = 'config_cache_enable'";
				$db->query($sql);
			}

		}
	}

	private function _is_expired($filename){
		if(!is_file($filename)){
			return true;
		}
		//check for modification
		$ctime = filectime($filename);
		$mtime = filemtime($filename);

		if( $ctime && $mtime && $mtime != $ctime ){
			return true;
		}

		return (time() - filemtime($filename) > $this->expire);
	}

	/**
	 * force to get cache data based on params and ignore disable cache setting
	 * @param string $key
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @return mixed|null
	 */
	public function force_get($key, $language_id = '', $store_id = '' ) {
		return $this->get($key, $language_id, $store_id, true );
	}

	/**
	 * get cache data based on params.
	 * @param string $key
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @param bool $disabled_override
	 * @return mixed|null
	 */
	public function get($key, $language_id = '', $store_id = '', $disabled_override = false) {
		//clean up if disabled cache
		if (!$disabled_override && !$this->registry->get('config')->get('config_cache_enable')){
			$this->delete($key, $language_id, $store_id );
			return null;
		}
		//load cache file name for the section (key)
		$cache_file_full_name = $this->_build_name($key, $language_id, $store_id).'.cache';

		//if file expired or not exists
		if ($this->_is_expired($cache_file_full_name) && file_exists($cache_file_full_name)) {
			$this->_remove($cache_file_full_name);
			return null;
		}elseif(file_exists($cache_file_full_name)) { // if all good
			if(filesize($cache_file_full_name)>0){
				$handle = fopen($cache_file_full_name, 'r');
				$cache = fread($handle, filesize($cache_file_full_name));
				fclose($handle);
				$output = unserialize($cache);
			}else{
				$output = '';
			}
			return $output;
		}

		return null;
  	}

	/**
	 * force to set cache data based on params and ignore disable cache setting
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @return null
	 */
	public function force_set( $key, $value, $language_id = '', $store_id = '' ) {
		return $this->set($key, $value, $language_id, $store_id, true );
	}

	/**
	 * set cache parameter
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @param bool $create_override
	 * @return null
	 */
	public function set($key, $value, $language_id = '', $store_id = '', $create_override = false) {

    	$this->delete($key, $language_id, $store_id );
    	if($this->registry->get('request')->get['rt']=='tool/cache'){
    		return null;
    	}
    	//validate key for / and \ 
    	if(strstr($key, '/') || strstr($key, '\\') ){
    		return null;    	
    	}
		if ($create_override || $this->registry->get('config')->get('config_cache_enable')){	
			//build new cache file name
			$filename = $this->_build_name($key, $language_id, $store_id).'.cache';

			//create subdirectory if needed
			$this->_test_create_directory($key);
			$handle = fopen($filename, 'w');
		   	fwrite($handle, serialize($value));				
		   	fclose($handle);
		}
  	}

	/**
	 * @param string $key
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @return bool
	 */
	public function delete($key, $language_id = '', $store_id = '') {
  		$section = substr($key, 0,strpos($key, '.'));
  		if ($section) {
  			//delete match within directory
			$files = glob($this->_build_name($key, $language_id, $store_id) . '.*', GLOB_NOSORT);
  		} else {
  			//delete whole content of directory for section/key
  			$files = glob(DIR_CACHE . $key . '/*', GLOB_NOSORT);
  		}
		if(!$files){
			return true;
		}

		$result = true;
        foreach ($files as $file) {
			if(pathinfo($file,PATHINFO_FILENAME) == 'index.html'){ continue; }
            $res = $this->_remove($file);
	        $result = !$res ? false : $result;
        }
		return $result;
  	}

//HTML Cache related methods:
	/**
	 * Read HTML valid cache file
	 * @param string $file_path
	 * @return string
	 */
	public function get_html_cache($file_path){
		if(empty($file_path)) {
			return '';
		}
		// filepath
		$filename = $file_path .'.html';

		if($this->_is_expired($filename) && is_file($filename)){
			$this->_remove($filename);
		}

		if( is_file($filename) ){
			$h = fopen($filename, 'r');
			$html_cache = fread($h, filesize($filename));
			fclose($h);
			return $html_cache;
		}
		return '';
	}

	/**
	 * Write HTML Cache file
	 * @param string $file_path
	 * @param string $content
	 * @return bool
	 */
	public function save_html_cache($file_path, $content){
		$html_cache_dir = DIR_CACHE. "html_cache/"; 
		//create html cache directory if not yet there. 
		if(!is_dir($html_cache_dir)) {
			if(!is_writeable(DIR_CACHE)){
				return false;
			}
			//create directory and set empty index.php to limit access
			make_writable_dir($html_cache_dir);
			touch($html_cache_dir.'index.php');
		}	
		if(is_writeable($html_cache_dir) && $file_path){
			$filename = $file_path.'.html';
			$this->_remove($filename);
			//auto create all directories 
			if(make_writable_path(dirname($filename))){
			    $h = fopen($filename, 'w');
			    fwrite($h, $content);				
			    fclose($h);	
			    chmod($filename,0777);
				touch($filename, time());
			    return true;		
			} else {
				return false;
			}
		} else {
			return false;
		}

	}

	public function delete_html_cache($path){
		if(!$path){
			return false;
		}
		//if needs to delete all html-cache
		if($path=='*'){
			$files = glob(DIR_CACHE . 'html_cache/*/*/*', GLOB_NOSORT);

			if ($files) {
	            foreach ($files as $file) {
					if(pathinfo($file,PATHINFO_FILENAME) == 'index.html'){ continue; }
					$this->_remove($file);
	            }
			}
		}

		var_dump($this->registry->get('language'));
		//!!!!!
		//remove cache of specified path. This can be only option of the path. Remove all under this path.


		return true;
	}


	/**
	 * @param string $key
	 * @void
	 */
	private function _test_create_directory ($key) {
		//get section by first part of the key
		$section = substr($key, 0,strpos($key, '.'));
		//if no match use key as section
		if ( !$section ) {
			$section = $key;
		}
		if (!is_file(DIR_CACHE . $section) && !is_dir(DIR_CACHE . $section)) {
			mkdir(DIR_CACHE . $section, 0777, true);
			//change mode for nested directories
			$this->_chmod_dir(DIR_CACHE . $section, 0777);
			//prevent direct access to this directory
			touch(DIR_CACHE . $section.'/index.php');
		}
	}

	/**
	 * @param string $key
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @return string
	 */
	private function _build_name($key, $language_id = '', $store_id = ''){
		//get section by first part of the key
		$section = substr($key, 0,strpos($key, '.'));
		$suffix = $this->_build_suffix($language_id, $store_id);
		//if no match use key as seciton 
		if ( !$section ) {
			$section = $key;	
		}
		return DIR_CACHE . $section . '/' . $key . ($suffix ? '.'.$suffix : '');
	}

	/**
	 * @param mixed $language_id
	 * @param mixed $store_id
	 * @return string
	 */
	private function _build_suffix($language_id = '', $store_id = ''){
		$suffix = '';
		if($language_id){
			$language_id = (int)$language_id;
		}
		if($store_id){
			$store_id = (int)$store_id;
		}
		if($language_id || $store_id){
			$suffix = $language_id.'_'.(int)$store_id;
		}
		return $suffix;
  	}


	/**
	 * @param string $file
	 * @return null
	 * @void
	 */
	private function _remove($file){
		if(empty($file) || !is_file($file)){
			return false;
		}
		unlink($file);
		//double check that the cache file to be removed
		if (file_exists($file)){
			$err_text = sprintf('Error: Cannot delete cache file: %s! Check file or directory permissions.', $file);
			$error = new AError($err_text);
			$error->toLog()->toDebug();
			return false;
		}
		return true;
	}

	/**
	 * Change mode recursive
	 *
	 * @param string $path
	 * @param int $dirmode
	 * @return bool
	 */
	private function _chmod_dir($path, $dirmode) {
	    if (is_dir($path) ) {
	        if (!chmod($path, $dirmode)) {
	            $dirmode_str=decoct($dirmode);
				$this->registry->get('log')->write("Failed applying filemode '".$dirmode_str."' on directory '".$path."'\n  -> the directory '".$path."' will be skipped from recursive chmod\n");
	            return false;
	        }
	    } elseif (is_link($path)) {
			$this->registry->get('log')->write("link '".$path."' is skipped\n");
			return false;
	    }
		return true;
	}
}
