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

class AControllerAPI extends AController {
	protected $rest;
	protected $error = array();
	protected $data = array();

	public function __construct($registry, $instance_id, $controller, $parent_controller = '') {
		parent::__construct($registry, $instance_id, $controller, $parent_controller);
		$this->rest = new ARest;
	}

	public function main() {
		//call methods based on REST re	quest type
		switch($this->rest->getRequestMethod() ) {
			case 'get':
				return $this->get();
				break;
			case 'post':
				return $this->post();
				break;
			case 'put':
				return $this->put();
            	break;
			case 'delete':
				return $this->delete();
				break;
			default:
				$this->rest->sendResponse(405);
				return null;
				break;
		}		
	}

	//Abstract Methods
	public function get() {
		$this->rest->sendResponse(405);
		return null;
	}
	
	public function post() {
		$this->rest->sendResponse(405);
		return null;
	}
	
	public function put() {
		$this->rest->sendResponse(405);
		return null;
	}
	
	public function delete() {
		$this->rest->sendResponse(405);
		return null;
	}

}