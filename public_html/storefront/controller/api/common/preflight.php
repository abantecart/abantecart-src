<?php  
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
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
class ControllerApiCommonPreflight extends AControllerAPI {
	
	public function main() {
		// This might require future imptovment. 
		if ( $_SERVER["REQUEST_METHOD"] == 'OPTIONS') {
			$this->registry->get('response')->addHeader("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
			$this->registry->get('response')->addHeader("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");	
			$this->registry->get('response')->addHeader("Access-Control-Allow-Credentials: true");
    		$this->registry->get('response')->addHeader("Access-Control-Max-Age: 60");	

			$this->rest->sendResponse( 200 );	
		}
	}	
}



