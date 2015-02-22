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
class ControllerApiCommonCountry extends AControllerAPI {
	protected $data = array();
	
	public function get() {
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadModel('localisation/country');
        $this->data = $this->model_localisation_country->getCountries();

        $this->extensions->hk_InitData($this,__FUNCTION__);
        
		$this->rest->setResponseData( $this->data );
		$this->rest->sendResponse( 200 );	
	}

}