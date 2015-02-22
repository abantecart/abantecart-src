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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}
class ControllerResponsesCommonAccess extends AController {

	public function login() {

		if (isset($this->request->get[ 'rt' ]) && !isset($this->request->get[ 'token' ])) {
			$route = '';

			$part = explode('/', $this->request->get[ 'rt' ]);

			if (isset($part[ 0 ])) {
				$route .= $part[ 0 ];
			}

			if (isset($part[ 1 ])) {
				$route .= '/' . $part[ 1 ];
			}

			$ignore = array(
				'common/access',
				'common/captcha',
				'error/ajaxerror/login',
				'error/ajaxerror/not_found',
				'error/ajaxerror/permission',
			);

			if (!in_array($route, $ignore)) {
				if (!isset($this->request->get[ 'token' ]) || !isset($this->session->data[ 'token' ]) || ($this->request->get[ 'token' ] != $this->session->data[ 'token' ])) {
					return $this->dispatch('responses/error/ajaxerror/login');
				}
			}
		} else {
			if (!isset($this->request->get[ 'token' ]) || !isset($this->session->data[ 'token' ]) || ($this->request->get[ 'token' ] != $this->session->data[ 'token' ])) {
				return $this->dispatch('responses/error/ajaxerror/login');
			}
		}
	}

	public function permission() {
		if ($this->extensions->isExtensionController($this->request->get[ 'rt' ])){ return null; }
		if (isset($this->request->get[ 'rt' ])) {
			$route = '';
			$rt = $this->request->get[ 'rt' ];
			if(in_array(substr($rt,0,2),array('p/','r/'))){
				$rt = substr($rt,2);
			}
			$part = explode('/', $rt);

			if (isset($part[ 0 ])) {
				$route .= $part[ 0 ];
			}

			if (isset($part[ 1 ])) {
				$route .= '/' . $part[ 1 ];
			}

			$ignore = array(
				'common/access',
				'common/captcha',
				'error/ajaxerror/login',
				'error/ajaxerror/not_found',
				'error/ajaxerror/permission',
			);

			if (!in_array($route, $ignore)) {
				if (!$this->user->canAccess($route)) {
					return $this->dispatch('responses/error/ajaxerror/permission');
				}
			}
		}
	}
}
