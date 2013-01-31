<?php 
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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

class ControllerApiIndexLogin extends AControllerAPI {

	public function get() {
		$this->rest->setResponseData( array( 'status' => 0, 'error' => 'Login attempt failed!') );	
		$this->rest->sendResponse(401);
		return;	
	}
	  
	public function post() {
		//This is login attempt
		$request = $this->rest->getRequestParams();
		if ( isset($request['token']) ) {
			//this is the request to authorized
			if ( $this->user->isLoggedWithToken( $request['token'] )) {
				$this->rest->setResponseData( array( 'status' => 1,  'request' => 'authorized' ) );	
				$this->rest->sendResponse(200);
				return;			
    		} else {
				$this->rest->setResponseData( array( 'status' => 0, 'request' => 'unauthorized' ) );	
				$this->rest->sendResponse(401);
				return;	    		
    		} 		 	
		
		} else {
			if ( isset($request['username']) && isset($request['password']) && $this->_validate($request['username'], $request['password']) ) {
				$this->session->data['token'] = AEncryption::getHash(mt_rand());
				$this->rest->setResponseData( array( 'status' => 1, 'success' => 'Logged in', 'token' => $this->session->data['token'] ) );	
				$this->rest->sendResponse(200);
				return;			
			} else {
				$this->rest->setResponseData( array( 'status' => 0, 'error' => 'Login attempt failed!') );	
				$this->rest->sendResponse(401);
				return;	
			}
		}				
	}

	private function _validate($username, $password) {
		if (isset($username) && isset($password) && !$this->user->login($username, $password)) {
			$this->messages->saveNotice($this->language->get('error_login_message').$this->request->server['REMOTE_ADDR'],$this->language->get('error_login_message_text').$username);
			return FALSE;			
		} else {
			return TRUE;		
		}
	} 
}  
?>