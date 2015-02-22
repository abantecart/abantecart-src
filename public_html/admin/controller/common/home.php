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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerCommonHome extends AController {

	public function login() {

		if (isset($this->request->get['rt']) && !isset($this->request->get['token'])) {
			$route = '';
						
			$part = explode('/', $this->request->get['rt']);
			
			if (isset($part[0])) {
				$route .= $part[0];
			}
			
			if (isset($part[1])) {
				$route .= '/' . $part[1];
			}
			
			$ignore = array(
				'index/login',
				'index/logout',
				'index/forgot_password',
				'error/not_found',
				'error/permission'
			);
									
			if (!in_array($route, $ignore)) {
				return $this->dispatch('pages/index/login');
			}
		} else {
			if (!isset($this->request->get['token'])
			    || !isset($this->session->data['token'])
			    || ($this->request->get['token'] != $this->session->data['token'] )) {
				//clear session data
                $this->session->clear();
				return $this->dispatch('pages/index/login');
			}
		}

	}
	
	public function permission() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        if (isset($this->request->get['rt'])) {
			$route = '';
			
			$part = explode('/', $this->request->get['rt']);
			
			if (isset($part[0])) {
				$route .= $part[0];
			}
			
			if (isset($part[1])) {
				$route .= '/' . $part[1];
			}

			$ignore = array(
				'index/home',
				'index/login',
				'index/logout',
				'index/forgot_password',
				'index/edit_details',
				'error/not_found',
				'error/permission',	
				'error/token'		
			);			

	       	if (!in_array($route, $ignore)) {
				if (!$this->user->canAccess($route)) {
					return $this->dispatch('pages/error/permission');
				}
			}
		}

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
?>