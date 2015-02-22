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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}
class ControllerApiAccountEdit extends AControllerAPI {
	private $v_error = array();
	public $data;

	public function post() {
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$request_data = $this->rest->getRequestParams();

		if (!$this->customer->isLoggedWithToken($request_data[ 'token' ])) {
			$this->rest->setResponseData(array( 'error' => 'Not logged in or Login attempt failed!' ));
			$this->rest->sendResponse(401);
			return null;
		}

		$this->loadModel('account/customer');
		$this->loadLanguage('account/edit');
		$this->loadLanguage('account/success');

		//TODO Think of way to validate and block machine registrations (non-human)				
		$this->v_error = $this->model_account_customer->validateEditData($request_data);
		if (!$this->v_error) {
			$this->model_account_customer->editCustomer($request_data);
			$this->model_account_customer->editNewsletter($request_data[ 'newsletter' ]);
			$this->data[ 'status' ] = 1;
			$this->data[ 'text_message' ] = $this->language->get('text_success');
		} else {

			$this->data[ 'status' ] = 0;
			$this->data[ 'error_warning' ] = $this->v_error[ 'warning' ];
			$this->data[ 'error_firstname' ] = $this->v_error[ 'firstname' ];
			$this->data[ 'error_lastname' ] = $this->v_error[ 'lastname' ];
			$this->data[ 'error_email' ] = $this->v_error[ 'email' ];
			$this->data[ 'error_telephone' ] = $this->v_error[ 'telephone' ];
			return $this->_build_responce();
		}

		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->rest->setResponseData($this->data);
		$this->rest->sendResponse(200);
	}

	public function get() {
		$request_data = $this->rest->getRequestParams();

		if (!$this->customer->isLoggedWithToken($request_data[ 'token' ])) {
			$this->rest->setResponseData(array( 'error' => 'Not logged in or Login attempt failed!' ));
			$this->rest->sendResponse(401);
			return null;
		}

		return $this->_build_responce();
	}

	private function _build_responce() {
		//Get all required data fileds for registration. 
		$this->loadLanguage('account/create');
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$request_data = $this->rest->getRequestParams();
		$this->loadModel('account/customer');
		$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

		if (isset($request_data[ 'firstname' ])) {
			$firstname = $request_data[ 'firstname' ];
		} elseif (isset($customer_info)) {
			$firstname = $customer_info[ 'firstname' ];
		}

		if (isset($request_data[ 'lastname' ])) {
			$lastname = $request_data[ 'lastname' ];
		} elseif (isset($customer_info)) {
			$lastname = $customer_info[ 'lastname' ];
		}

		if (isset($request_data[ 'email' ])) {
			$email = $request_data[ 'email' ];
		} elseif (isset($customer_info)) {
			$email = $customer_info[ 'email' ];
		}

		if (isset($request_data[ 'telephone' ])) {
			$telephone = $request_data[ 'telephone' ];
		} elseif (isset($customer_info)) {
			$telephone = $customer_info[ 'telephone' ];
		}

		if (isset($request_data[ 'fax' ])) {
			$fax = $request_data[ 'fax' ];
		} elseif (isset($customer_info)) {
			$fax = $customer_info[ 'fax' ];
		}

		if (isset($request_data[ 'newsletter' ])) {
			$newsletter = $request_data[ 'newsletter' ];
		} elseif (isset($customer_info)) {
			$newsletter = $customer_info[ 'newsletter' ];
		}


		$this->data[ 'fields' ][ 'firstname' ] = array( 'type' => 'input',
			'name' => 'firstname',
			'value' => $firstname,
			'required' => true,
			'error' => $this->v_error[ 'firstname' ] );
		$this->data[ 'fields' ][ 'lastname' ] = array(
			'type' => 'input',
			'name' => 'lastname',
			'value' => $lastname,
			'required' => true,
			'error' => $this->v_error[ 'lastname' ] );
		$this->data[ 'fields' ][ 'email' ] = array(
			'type' => 'input',
			'name' => 'email',
			'value' => $email,
			'required' => true,
			'error' => $this->v_error[ 'email' ] );
		$this->data[ 'fields' ][ 'telephone' ] = array(
			'type' => 'input',
			'name' => 'telephone',
			'value' => $telephone,
			'error' => $this->v_error[ 'telephone' ] );
		$this->data[ 'fields' ][ 'fax' ] = array(
			'type' => 'input',
			'name' => 'fax',
			'value' => $fax,
			'required' => false );

		$this->data[ 'fields' ][ 'newsletter' ] = array(
			'type' => 'selectbox',
			'name' => 'newsletter',
			'value' => $newsletter,
			'required' => false );

		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->rest->setResponseData($this->data);
		$this->rest->sendResponse(200);

	}


}