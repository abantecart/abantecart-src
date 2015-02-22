<?php  
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright 2011-2015 Belavier Commerce LLC

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
class ControllerApiCommonAccess extends AControllerAPI {
	
	public function main() {
		//check if any restriction on caller IP
		if ( !$this->_validate_ip() ) {
		    return $this->dispatch('api/error/no_access');				
		}
		//validate if API enabled and KEY matches. 
		if ( $this->config->get('config_admin_api_status')	) {
			if ($this->config->get('config_admin_api_key') && 
					( $this->config->get('config_admin_api_key') == $this->request->post['api_key'] ||
					  $this->config->get('config_admin_api_key') == $this->request->get['api_key'] )
				) {
				return null;
			} else if ( !$this->config->get('config_admin_api_key') ) {
				return null;
			}
		}	
		return $this->dispatch('api/error/no_access');
	}	
	
	private function _validate_ip () {
		if (!has_value($this->config->get('config_admin_access_ip_list'))) {
			return true;
		}
		
		$ips = array_map('trim', explode(",", $this->config->get('config_admin_access_ip_list')));
		if ( in_array($_SERVER['REMOTE_ADDR'], $ips) ){
			return true;
		}
		return false;
	}
	
	public function login() {
		$request = $this->rest->getRequestParams();
		//allow access to listed controlles with no login
		if (isset($request['rt']) && !isset($request['token'])) {
			$route = '';
			$part = explode('/', $request['rt']);

			if (isset($part[ 0 ])) {
				$route .= $part[ 0 ];
			}
			if (isset($part[ 1 ])) {
				$route .= '/' . $part[ 1 ];
			}
			$ignore = array(
				'api/index/login',
				'api/common/access',
				'api/error/not_found',
				'api/error/no_access',
				'api/error/no_permission',
			);

			if (!in_array($route, $ignore)) {
				return $this->dispatch('api/index/login');
			}
		} else {
			if ( !$this->user->isLoggedWithToken( $request['token'] )) {
				return $this->dispatch('api/index/login');
			}
		}
	}

	public function permission() {
		$request = $this->rest->getRequestParams();
		
		if ( $this->extensions->isExtensionController($request['rt']) ) return null;

		if ( isset($request['rt']) ) {
			$route = '';
			$part = explode('/', $request['rt']);

			if (isset($part[ 0 ])) {
				$route .= $part[ 0 ];
			}
			if (isset($part[ 1 ])) {
				$route .= '/' . $part[ 1 ];
			}
			$ignore = array(
				'api/index/login',
				'api/common/access',
				'api/error/not_found',
				'api/error/no_access',
				'api/error/no_permission',
			);

			if (!in_array($route, $ignore)) {
				if (!$this->user->canAccess($route)) {
					return $this->dispatch('api/error/no_permission');
				}
			}
		}
	}	
	
}



