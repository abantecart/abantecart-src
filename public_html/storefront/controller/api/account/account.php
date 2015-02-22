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
class ControllerApiAccountAccount extends AControllerAPI {
	public function post() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$request = $this->rest->getRequestParams();
		
		if (!$this->customer->isLoggedWithToken( $request['token'] )) {
			$this->rest->setResponseData( array( 'error' => 'Not logged in or Login attempt failed!' ) );	
			$this->rest->sendResponse(401);
			return null;
    	} 

		//load language from main section
		$this->loadLanguage('account/account');
		$data['title'] = $this->language->get('heading_title');
		
		$data['customer_id'] =  $this->customer->getId();
		$data['firstname'] =  $this->customer->getFirstName();
		$data['lastname'] =  $this->customer->getLastName();
		$data['email'] =  $this->customer->getEmail();		
        $data['information'] = 'a/account/edit';
        $data['history'] = 'a/account/history';
        $data['newsletter'] = 'a/account/logout';

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		$this->rest->setResponseData( $data );
		$this->rest->sendResponse( 200 );
	}
	
}