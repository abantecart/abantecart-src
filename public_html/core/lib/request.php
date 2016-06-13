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

final class ARequest {
	public $get = array();
	public $post = array();
	public $cookie = array();
	public $files = array();
	public $server = array();

    private $http;
    private $version;
    private $browser;
    private $browser_version;
    private $platform;
    private $device_type;

  	public function __construct() {
		$_GET = $this->clean($_GET);
		$_POST = $this->clean($_POST);
		$_COOKIE = $this->clean($_COOKIE);
		$_FILES = $this->clean($_FILES);
		$_SERVER = $this->clean($_SERVER);
		
		$this->get = $_GET;
		$this->post = $_POST;
		$this->cookie = $_COOKIE;
		$this->files = $_FILES;
		$this->server = $_SERVER;

		//check if there is any encrypted data
		if ( has_value($this->get['__e'])) {
			$this->get = array_replace_recursive($this->get, $this->decodeURI($this->get['__e'])); 
		}
		if ( has_value($this->post['__e'])) {
			$this->post = array_replace_recursive($this->post, $this->decodeURI($this->post['__e'])); 
		}
        $this->_detectBrowser();
	}
	
	//todo: Include PHP module filter to process input params. http://us3.php.net/manual/en/book.filter.php
	/**
	 * function returns variable value from $_GET first
	 * @param string $key
	 * @return string | null
	 */
  	public function get_or_post( $key ) {
		if ( isset($this->get[$key]) ){
			return $this->get[$key];
		} else if ( isset($this->post[$key]) ) {
			return $this->post[$key];
		} 
		return null;
	}

	/**
	 * function returns variable value from $_POST first
	 * @param string $key
	 * @return string | null
	 */
	public function post_or_get( $key ) {
		if ( isset($this->post[$key]) ){
			return $this->post[$key];
		} else if ( isset($this->get[$key]) ) {
			return $this->get[$key];
		}
		return null;
	}

	/**
	 * Prevent hacks and non-browser requests with non-encoded data.
	 * @param string|array $data
	 * @return array|string
	 */
	public function clean($data) {
    	if (is_array($data)) {
	  		foreach ($data as $key => $value) {
				unset($data[$key]);
	    		$data[$this->clean($key)] = $this->clean($value);
	  		}
		} else { 
	  		$data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
		}
		return $data;
	}

	/**
	 * @param string  - base64 $uri
	 * @return array
	 */
	public function decodeURI($uri) {
		$params = array();
		$open_uri = base64_decode($uri);
		//clean data
		$open_uri = $this->xss_clean($open_uri);

    	$split_parameters = explode('&', $open_uri);
    	for($i = 0; $i < count($split_parameters); $i++) {
        	$final_split = explode('=', $split_parameters[$i]);
        	$params[$final_split[0]] = $final_split[1];
    	}	
    	return $params;
	} 

	private function _detectBrowser() {

		$nua = strToLower( $_SERVER['HTTP_USER_AGENT']);
		
		$agent['http'] = isset($nua) ? $nua : "";
		$agent['version'] = 'unknown';
		$agent['browser'] = 'unknown';
		$agent['platform'] = 'unknown';
		$agent['device_type'] = '';
		
		$oss = array('win', 'mac', 'linux', 'unix');
		foreach ($oss as $os) {
			if (strstr($agent['http'], $os)) {
				$agent['platform'] = $os;
				break;
			}
		}

		$browsers = array("mozilla","msie","gecko","firefox","konqueror","safari","netscape","navigator","opera","mosaic","lynx","amaya","omniweb");

		for ($i=0; $i<count($browsers); $i++){
			if(strlen( stristr($nua, $browsers[$i]) )>0){
				$agent["browser"] = $browsers[$i];
				$n = stristr($nua, $agent["browser"]);
				$j=strpos($nua, $agent["browser"])+$n+strlen($agent["browser"])+1;
			}
		}

		//http://en.wikipedia.org/wiki/List_of_user_agents_for_mobile_phones - list of useragents
		$devices = array("iphone","android","blackberry","ipod","ipad","htc","symbian","webos","opera mini", "windows phone os", "iemobile");
		
		for ($i=0; $i<count($devices); $i++){
		   if (stristr($nua, $devices[$i])) {
		   	  $agent["device_type"] = $devices[$i];
			  break;
		   }
		}
		
		$this->browser = $agent['browser'];
		$this->device_type = $agent['device_type'];
		$this->http = $agent['http'];
		$this->platform = $agent['platform'];
		$this->version = $agent['version'];

    }

    public function getBrowser(){
        return $this->browser;
    }

    public function getBrowserVersion(){
        return $this->browser_version;
    }

    public function getDeviceType(){
        return $this->device_type;
    }

    public function getHttp(){
        return $this->http;
    }

    public function getPlatform(){
        return $this->platform;
    }

    public function getVersion(){
        return $this->version;
    }

	/**
	 * @return bool
	 */
	public function is_POST(){
		return ($this->server['REQUEST_METHOD']=='POST' ? true : false);
	}

	/**
	 * @return bool
	 */
	public function is_GET(){
		return ($this->server['REQUEST_METHOD']=='GET' ? true : false);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function deleteCookie($name){
		if(empty($name)){
			return false;
		}
		$path =  dirname($this->server[ 'PHP_SELF' ]);
		setcookie($name, null, -1, $path);
		unset($this->cookie[$name], $_COOKIE[$name]);
		return true;
	}

	public function xss_clean($data){
		// Fix &entity\n;
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

		do{
			// Remove really unwanted tags
			$old_data = $data;
			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		}
		while ($old_data !== $data);

		return $data;
	}
}
