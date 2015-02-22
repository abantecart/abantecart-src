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
class ControllerApiCommonZone extends AControllerAPI {
	protected $data = array();
	
	public function get() {
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$request_data = $this->rest->getRequestParams();
		
		if ( !isset($request_data['country_id']) && !is_int($request_data['country_id']) ) {
			$this->rest->setResponseData( array('Error' => 'Missing on incorrect one of required parameters') );
			$this->rest->sendResponse(200);
			return null;
		}
		
		$this->loadModel('localisation/zone');

    	$this->data = $this->model_localisation_zone->getZonesByCountryId( $request_data['country_id'] );

        $this->extensions->hk_InitData($this,__FUNCTION__);
        
		$this->rest->setResponseData( $this->data );
		$this->rest->sendResponse( 200 );	
	}

}