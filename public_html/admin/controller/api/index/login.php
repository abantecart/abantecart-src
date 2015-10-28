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

class ControllerApiIndexLogin extends AControllerAPI {

	public function get() {
		$request = $this->rest->getRequestParams();
		$this->_validate_token($request['token']);
	}
	  
	public function post() {
		//This is login attempt
		$request = $this->rest->getRequestParams();
		if ( isset($request['token']) ) {
			//this is the request to authorized
			$this->_validate_token($request['token']);
		} else {
			if ( isset($request['username']) && isset($request['password']) && $this->_validate($request['username'], $request['password']) ) {
				if(!session_id()) {
					$this->rest->setResponseData( array( 'status' => 0, 'error' => 'Unable to get session ID.') );	
					$this->rest->sendResponse(501);		
					return null;	
				}
				$this->session->data['token'] = session_id();
				$this->rest->setResponseData( array( 'status' => 1, 'success' => 'Logged in', 'token' => $this->session->data['token'] ) );	
				$this->rest->sendResponse(200);
			} else {
				$this->rest->setResponseData( array( 'status' => 0, 'error' => 'Login attempt failed!') );	
				$this->rest->sendResponse(401);
			}
		}				
	}

	private function _validate($username, $password) {
		if (isset($username) && isset($password) && !$this->user->login($username, $password)) {
			$this->loadLanguage('common/login');
			$this->messages->saveNotice("API " . $this->language->get('error_login_message').$this->request->server['REMOTE_ADDR'],$this->language->get('error_login_message_text').$username);
			return FALSE;			
		} else {
			return TRUE;		
		}
	} 
	
	private function _validate_token ( $token ){
		if ( isset( $token ) && $this->user->isLoggedWithToken( $token )) {
				$this->rest->setResponseData( array( 'status' => 1,  'request' => 'authorized' ) );	
				$this->rest->sendResponse(200);
    	} else {
				$this->rest->setResponseData( array( 'status' => 0, 'request' => 'unauthorized' ) );	
				$this->rest->sendResponse(401);
		}	
	}
	
}  
?>