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
class ControllerApiAccountCreate extends AControllerAPI {
	private $v_error = array();
	public $data;

	public function post() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
        //only support post params for create account
		$request_data = $this->rest->getRequestParams();
		
		if ($this->customer->isLoggedWithToken( $request['token'] )) {
			$this->rest->setResponseData( array( 'error' => 'Already Logged in. Can not create new account.' ) );	
			$this->rest->sendResponse(401);
			return null;
    	} 

		$this->loadModel('account/customer');
		$this->loadLanguage('account/create');
		$this->loadLanguage('account/success');
		//????? Think of way to validate and block machine registrations (non-human)		
		$this->v_error = $this->model_account_customer->validateRegistrationData($request_data);
    	if ( !$this->v_error ) {
			$this->model_account_customer->addCustomer( $request_data );

			unset($this->session->data['guest']);

			$this->customer->login($request_data['email'], $request_data['password']);
			
			$this->loadLanguage('mail/account_create');
			
			$subject = sprintf($this->language->get('text_subject'), $this->config->get('store_name'));
			
			$message = sprintf($this->language->get('text_welcome'), $this->config->get('store_name')) . "\n\n";
			
			if (!$this->config->get('config_customer_approval')) {
				$message .= $this->language->get('text_login') . "\n";
			} else {
				$message .= $this->language->get('text_approval') . "\n";
			}
			
			$message .= $this->html->getSecureURL('account/login') . "\n\n";
			$message .= $this->language->get('text_services') . "\n\n";
			$message .= $this->language->get('text_thanks') . "\n";
			$message .= $this->config->get('store_name');
			
			$mail = new AMail( $this->config );
			$mail->setTo( $request_data['email'] );
	  		$mail->setFrom( $this->config->get('store_main_email') );
	  		$mail->setSender( $this->config->get('store_name') );
	  		$mail->setSubject( $subject );
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
      		$mail->send();
      		$this->data['status'] = 1;
			if (!$this->config->get('config_customer_approval')) {
    			$this->data['text_message'] = sprintf($this->language->get('text_message'), '' );
			} else {
				$this->data['text_message'] = sprintf($this->language->get('text_approval'), $this->config->get('store_name'), ''	);
			}

    	} else { 

      		$this->data['status'] = 0;
      		$this->data['errors'] = $this->v_error;
	        $this->data['error_warning'] = $this->v_error['warning'];
	        $this->data['error_loginname'] = $this->v_error['loginname'];
			$this->data['error_firstname'] = $this->v_error['firstname'];
			$this->data['error_lastname'] = $this->v_error['lastname'];
			$this->data['error_email'] = $this->v_error['email'];
			$this->data['error_telephone'] = $this->v_error['telephone'];
			$this->data['error_password'] = $this->v_error['password'];
			$this->data['error_confirm'] = $this->v_error['confirm'];
			$this->data['error_address_1'] = $this->v_error['address_1'];
			$this->data['error_city'] = $this->v_error['city'];
			$this->data['error_country'] = $this->v_error['country'];
			$this->data['error_zone'] = $this->v_error['zone'];
			return $this->get();
		}
		
        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		$this->rest->setResponseData( $this->data );
		$this->rest->sendResponse( 200 );
	}
	
	public function get() {
		//Get all required data fileds for registration. 
		$this->loadLanguage('account/create');
        $this->extensions->hk_InitData($this,__FUNCTION__);
        
		$this->data['fields']['firstname'] = array(	'type' => 'input',
													'name' => 'firstname',
													'value' => $this->request->post['firstname'],
													'required' => true,
													'error' =>  $this->v_error['firstname']);
		$this->data['fields'][ 'lastname' ] = array(
                                                    'type' => 'input',
		                                            'name' => 'lastname',
		                                            'value' => $this->request->post['lastname'],
													'required' => true,
													'error' =>  $this->v_error['lastname']);
		$this->data['fields'][ 'email' ] = array(
                                                    'type' => 'input',
		                                            'name' => 'email',
		                                            'value' => $this->request->post['email'],
													'required' => true,
													'error' =>  $this->v_error['email']);
		$this->data['fields'][ 'telephone' ] = array(
                                                    'type' => 'input',
		                                            'name' => 'telephone',
		                                            'value' => $this->request->post['telephone'],
													'error' =>  $this->v_error['telephone']);
		$this->data['fields'][ 'fax' ] =  array(
                                                    'type' => 'input',
		                                           	'name' => 'fax',
		                                            'value' => $this->request->post['fax'],
		                                           	'required' => false );
		$this->data['fields'][ 'company' ] = array(
                                                  	'type' => 'input',
		                                           	'name' => 'company',
		                                          	'value' => $this->request->post['company'],
		                                            'required' => false );
		$this->data['fields'][ 'address_1' ] = array(
                                                    'type' => 'input',
		                                            'name' => 'address_1',
		                                            'value' => $this->request->post['address_1'],
													'required' => true,
													'error' =>  $this->v_error['address_1']);
		$this->data['fields'][ 'address_2' ] = array(
                                                    'type' => 'input',
		                                            'name' => 'address_2',
		                                            'value' => $this->request->post['address_2'],
		                                            'required' => false );
		$this->data['fields'][ 'city' ] = array(
                                                  	'type' => 'input',
		                                            'name' => 'city',
		                                            'value' => $this->request->post['city'],
													'required' => true,
													'error' =>  $this->v_error['city']);
		$this->data['fields'][ 'postcode' ] = array(
                                                    'type' => 'input',
		                                            'name' => 'postcode',
		                                            'value' => $this->request->post['postcode'],
		                                            'required' => false );
		$this->loadModel('localisation/country');
        $countries = $this->model_localisation_country->getCountries();
        $options = array("FALSE" => $this->language->get('text_select') );
        foreach($countries as $item){
            $options[ $item['country_id'] ] = $item['name'];
        }
	    $this->data['fields'][ 'country_id' ] = array(
                                                    'type' => 'selectbox',
		                                            'name' => 'country_id',
                                                    'options' => $options,
		                                            'value' => ( isset($this->request->post['country_id']) ? $this->request->post['country_id'] : $this->config->get('config_country_id')),
													'required' => true,
													'error' =>  $this->v_error['country_id']);
        $this->data['fields'][ 'zone_id' ] = array(
                                                    'type' => 'selectbox',
		                                            'name' => 'zone_id',
													'required' => true,
													'value' => $this->request->post['zone_id'],
													'error' =>  $this->v_error['lastname']);

		$this->data['fields'][ 'password' ] = array(
                                                    'type' => 'password',
		                                            'name' => 'password',
		                                            'value' => $this->request->post['password'],
													'required' => true,
													'error' =>  $this->v_error['lastname']);
		$this->data['fields'][ 'confirm' ] = array(
                                                    'type' => 'password',
		                                            'name' => 'confirm',
		                                            'value' => $this->request->post['confirm'],
													'required' => true,
													'error' =>  $this->v_error['lastname']);
		$this->data['fields'][ 'newsletter' ] = array(
                                                    'type' => 'radio',
		                                            'name' => 'newsletter',
		                                            'value' => (isset($this->request->post['newsletter']) ? $this->request->post['newsletter'] : -1),
		                                            'options' => array(
                                                     	'1' => $this->language->get('text_yes'),
                                                     	'0' => $this->language->get('text_no'),
                                                     ) );

		$this->data['fields'][ 'agree' ] = array(
                                                   	'type' => 'checkbox',
		                                           	'name' => 'agree',
		                                            'value' => 1,
		                                            'checked' => $agree );

		if ($this->config->get('config_account_id')) {
			$this->loadModel('catalog/content');
			$content_info = $this->model_catalog_content->getContent($this->config->get('config_account_id'));
			if ($content_info) {
				$text_agree = sprintf($this->language->get('text_agree'),
				                      $this->html->getURL('r/content/content/loadInfo', '&content_id=' . $this->config->get('config_account_id')),
				                      $content_info['title']);
			} else {
				$text_agree = '';
			}
		} else {
			$text_agree = '';
		}
        $this->data['text_agree'] = $text_agree;

       //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		$this->rest->setResponseData( $this->data );
		$this->rest->sendResponse( 200 );
	}	
	
}