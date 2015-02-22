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
		//$_SERVER = $this->clean($_SERVER);
		
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
		$parms = array();
		$open_uri = base64_decode($uri);
    	$split_parameters = explode('&', $open_uri);

    	for($i = 0; $i < count($split_parameters); $i++) {
        	$final_split = explode('=', $split_parameters[$i]);
        	$parms[$final_split[0]] = $final_split[1];
    	}	
    	return $parms;	
	} 

    private function _detectBrowser() {

        $nua = strToLower( $_SERVER['HTTP_USER_AGENT']);

        $agent['http'] = isset($_SERVER["HTTP_USER_AGENT"]) ? strtolower($_SERVER["HTTP_USER_AGENT"]) : "";
        $agent['version'] = 'unknown';
        $agent['browser'] = 'unknown';
        $agent['b_version'] = 0;
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

        $l = strlen($nua);
        for ($i=0; $i<count($browsers); $i++){
          if(strlen( stristr($nua, $browsers[$i]) )>0){
           $agent["b_version"] = "";
           $agent["browser"] = $browsers[$i];
           $j=strpos($nua, $agent["browser"])+$n+strlen($agent["browser"])+1;
           for (; $j<=$l; $j++){
             $s = substr ($nua, $j, 1);
             if(is_numeric($agent["b_version"].$s) )
             $agent["b_version"] .= $s;
             else
             break;
           }
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
        $this->browser_version = $agent['b_version'];
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
}
