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

final class ARouter {
	/**
	 * @var Registry
	 */
	protected $registry;
	/**
	 * @var string
	 */
	protected $rt;
	/**
	 * @var string
	 */
	protected $request_type;
	/**
	 * @var string
	 */
	protected $controller;
	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @param Registry $registry
	 */
	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function __destruct() {
		$this->rt = ''; 	
	}

    public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	/**
	 * @param $rt
	 * @throws AException
	 */
	public function processRoute( $rt ){
		$this->rt = $rt;		
		if ( empty($this->rt) ){
			throw new AException(AC_ERR_LOAD, 'Error: Route is undefined!');
		}
	
		return $this->_route();
	}

	/**
	 * @return string
	 */
	public function getRequestType(){
		return $this->request_type;
	}

	/**
	 * @return string
	 */
	public function getController(){
		return $this->controller;
	}

	/**
	 * @param string $rt
	 * @return string
	 */
	public function resetController($rt = ''){
		if ($rt){
			$this->controller = $rt;
		}
		return $this->controller;
	}

	/**
	 * @return string
	 */
	public function getMethod(){
		return $this->method;
	}

	private function _route() {
        $path_nodes = explode('/', $this->rt);
		//Identify what resource do we load explicitely. Page, Responce or API type        
		//Check the path. If started with p/, r/ or a/ -> This is explicit call of page, responce or API
		if ($path_nodes[0] == 'p' ) {
			$this->request_type = 'page';	
			$this->rt = preg_replace('/^p\//', '', $this->rt);
		} else if ($path_nodes[0] == 'r' ) {
			$this->request_type = 'response';		
			$this->rt = preg_replace('/^r\//', '', $this->rt);
		} else if ($path_nodes[0] == 'a' ) {
			$this->request_type = 'api';		
			$this->rt = preg_replace('/^a\//', '', $this->rt);		
		} else if ($path_nodes[0] == 'task') {
			$this->request_type = 'task';
			$this->rt = preg_replace('/^task\//', '', $this->rt);
		} else {
			//find implicit path of controller
			//Pages section has priority
			if ( $this->_detect_controller("pages") ){			
				$this->request_type = 'page';	
			}
			else if ( $this->_detect_controller("responses") ){
				$this->request_type = 'response';	
			}	
			else if ( $this->_detect_controller("api") ){
				$this->request_type = 'api';		
			}
		} 		

		if ( $this->request_type == 'page' ){			
			$page_controller = new APage($this->registry);			
			
			if (!defined('IS_ADMIN') || !IS_ADMIN ) {	
				//Load required controller for storefront
				$page_controller->addPreDispatch('common/maintenance');	
				$page_controller->addPreDispatch('common/seo_url');
			} else {
				//Load required controller for admin
				$page_controller->addPreDispatch('common/home/login');
				$page_controller->addPreDispatch('common/ant');
				$page_controller->addPreDispatch('common/home/permission');
			}
			//Validate controller only. If does not exist process not found 
			if ( $this->_detect_controller("pages") ){
		    	// Build the page	
				$page_controller->build($this->rt);
			} else {
				$page_controller->build('error/not_found');
			}
		}
		else if ( $this->request_type == 'response' ) {
			$resp_controller = new ATypeResponse($this->registry);	
			if (!defined('IS_ADMIN') || !IS_ADMIN ) {	
				//Load required controller for storefront
				$resp_controller->addPreDispatch('common/maintenance/response');	
			} else {
				//Load required controller for admin
				$resp_controller->addPreDispatch('responses/common/access/login');
				$resp_controller->addPreDispatch('responses/common/access/permission');
			}	
			//Validate controller only. If does not exist process not found 
			if ( $this->_detect_controller("responses") ){
			    // Build the response	
				$resp_controller->build($this->rt);	
			} else {
				$resp_controller->build('error/not_found');
			}					
				
		}
		else if ( $this->request_type == 'api' ) {
			$api_controller = new AAPI($this->registry);	
			if (!defined('IS_ADMIN') || !IS_ADMIN ) {	
				//CORS preflight request
				$api_controller->addPreDispatch('api/common/preflight');
				//validate access
				$api_controller->addPreDispatch('api/common/access');
			} else {
				//CORS preflight request
				$api_controller->addPreDispatch('api/common/preflight');
				//Validate Admin access, login and permissions
				$api_controller->addPreDispatch('api/common/access');
				$api_controller->addPreDispatch('api/common/access/login');
				$api_controller->addPreDispatch('api/common/access/permission');
			}
			//Validate controller only. If does not exist process not found 
			if ( $this->_detect_controller("api") ){
		    	// Build the response	
				$api_controller->build($this->rt);		
			} else {
				$api_controller->build('error/not_found');
			}						
		}
		else if ( $this->request_type == 'task' ) {
			$task_controller = new ATypeTask($this->registry);
			if (!defined('IS_ADMIN') || !IS_ADMIN ) { // do not allow to call task controllers from SF-side
				$resp_controller = new ATypeResponse($this->registry);
				$resp_controller->build('error/not_found');
			} else {
				//Load required controller for admin and check authorization
				$resp_controller = new ATypeResponse($this->registry);
				$resp_controller->addPreDispatch('responses/common/access/login');
				$resp_controller->addPreDispatch('responses/common/access/permission');
			}
			//Validate controller only. If does not exist process not found
			if ( $this->_detect_controller("task") ){
				// Build the response
				$task_controller->build($this->rt);
			} else {
				$resp_controller = new ATypeResponse($this->registry);
				$resp_controller->build('error/not_found');
			}
		}
		else {
			//Security: this is not main controller. Do not allow to run it. 			
			$this->request_type = 'page';
			$this->controller = 'error/not_found';
			$this->method = '';			
			$page_controller = new APage($this->registry);	
			$page_controller->build( $this->controller );
		}
		
	}

	/**
	 * @param $type
	 * @return bool
	 */
	private function _detect_controller ( $type ) {
        //looking for controller in admin/storefront section
        $dir_app = DIR_APP_SECTION.'controller/' . $type . '/';
        $path_nodes = explode('/', $this->rt);
        $path_build = '';

        // looking for controller in extensions section
        $result = $this->registry->get('extensions')->isExtensionController( $type . '/'.$this->rt );
		if ( $result ) {
			$extension_id = $result['extension'];
            // set new path if controller was found in admin/storefront section && in extensions section 
            $current_section = IS_ADMIN ? DIR_EXT_ADMIN : DIR_EXT_STORE;
            $dir_app = DIR_EXT . $extension_id . $current_section . 'controller/' . $type . '/';
        }		
		//process path and try to locate the controller
        foreach ($path_nodes as $path_node) {
			$path_build .= $path_node;
			if (is_dir($dir_app . $path_build)) {
				$path_build .= '/';
				array_shift($path_nodes);
				continue;
			}

			if (is_file($dir_app .  $path_build . '.php')) {
				//Controller found. Save informaion and return TRUE
				//Set controller and method for future use
				$this->controller = $type . '/' . $path_build;
        		//Last part is the method of function to call
				$method_to_call = array_shift($path_nodes);				
				if ($method_to_call) {
					$this->method = $method_to_call;
				} else {
					//Set default method
					$this->method = 'main';
				}				
				return true;
			}
		}
		return false;
	}

}