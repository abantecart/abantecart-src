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
class ControllerPagesAccountLogin extends AController {
	private $error = array();
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if ($this->customer->isLogged()) {
            $this->redirect($this->html->getSecureURL('account/account'));
    	}
	
    	$this->document->setTitle( $this->language->get('heading_title') );
						
		if (($this->request->is_POST())) {
			if (isset($this->request->post['account'])) {
				$this->session->data['account'] = $this->request->post['account'];
				
				if ($this->request->post['account'] == 'register') {
					$this->redirect($this->html->getSecureURL('account/create'));
				}
				
				if ($this->request->post['account'] == 'guest') {
					$this->redirect($this->html->getSecureURL('checkout/guest_step_1'));
				}
			}
			//support old email based login
			$loginname = ( isset($this->request->post['loginname']) ) ? $this->request->post['loginname'] : $this->request->post['email'];
			$password = $this->request->post['password'];			
			if (isset($loginname) && isset($password) && $this->_validate($loginname, $password)) {
				unset($this->session->data['guest']);
				unset($this->session->data['account']);

				$address_id = $this->customer->getAddressId();
				$this->loadModel('account/address');
				$address =  $this->model_account_address->getAddress($address_id);
				$this->tax->setZone($address['country_id'], $address['zone_id']);

				if ( $this->session->data['redirect'] ) {
					$redirect_url = $this->session->data['redirect'];
					unset($this->session->data['redirect']);
					$this->redirect( $redirect_url );
				} else {
					$this->redirect($this->html->getSecureURL('account/account'));
				} 
			}
		// activation of account via email-code
    	} elseif ( has_value($this->request->get['activation']) ){
			if( $this->session->data['activation'] ){
				$customer_id = (int)$this->session->data['activation']['customer_id'];
				//if activation code presents in session
				if($this->request->get['activation'] == $this->session->data['activation']['code']){
					$this->loadModel('account/customer');
					$customer_info = $this->model_account_customer->getCustomer( $customer_id );

					// if account exists
					if( $customer_info ){
						if(!$customer_info['status'] ){ //if disabled - activate!
							$this->model_account_customer->editStatus( $customer_id, 1 );
							$this->session->data['success'] = $this->language->get('text_success_activated');
						}else{
							$this->session->data['success'] = $this->language->get('text_already_activated');
						}
					}
				}else{
					if($this->request->get['email']){
						$this->error['message'] = sprintf( $this->language->get('text_resend_activation_email'),
																				 "\n".$this->html->getSecureURL('account/success/sendcode',
																						 						'&email='.$this->request->get['email'])
																				);
					}
				}
			}elseif(has_value($this->request->get['email'])){ // if session expired - show link for resend activation code to email
				if($this->request->get['email']){
					$this->error['message'] = sprintf( $this->language->get('text_resend_activation_email'),
															 "\n".$this->html->getSecureURL('account/success/sendcode',
																	 						'&email='.$this->request->get['email'])
															);
				}
			}

		}
		
      	$this->document->resetBreadcrumbs();

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('index/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	 ));
 
      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('account/account'),
        	'text'      => $this->language->get('text_account'),
        	'separator' => $this->language->get('text_separator')
      	 ));
		
      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('account/login'),
        	'text'      => $this->language->get('text_login'),
        	'separator' => $this->language->get('text_separator')
      	 ));

        $this->view->assign('error', '' );
        if (isset($this->error['message'])) {
            $this->view->assign('error', $this->error['message'] );
		}

		$form = new AForm();
        $form->setForm(array( 'form_name' => 'accountFrm' ));
        $this->data['form1']['form_open'] = $form->getFieldHtml(
                                                                array(
                                                                       'type' => 'form',
                                                                       'name' => 'accountFrm',
                                                                       'action' => $this->html->getSecureURL('account/login')));
		
		$this->data['form1']['register'] = $form->getFieldHtml( array(
                                                                       'type' => 'radio',
                                                                       'id' => 'account',
		                                                               'name' => 'account',
		                                                               'options' => array(
			                                                                            'register'=>$this->language->get('text_account')),
			                                                           'value' => ( isset($this->session->data['account']) ? $this->session->data['account'] : 'register'),
		                                                               )
		                                                        );
		$this->data['form1']['guest'] = $form->getFieldHtml( array(
                                                                       'type' => 'radio',
                                                                       'id' => 'account',
		                                                               'name' => 'account',
		                                                               'options' => array(
			                                                                            'guest'=>$this->language->get('text_guest')),
			                                                           'value' => ( $this->session->data['account']=='guest' ? 'guest' : ''),
		                                                               )
		                                                        );
		$this->data['form1']['continue'] = $form->getFieldHtml( array(
                                                                       'type' => 'submit',
		                                                               'name' => $this->language->get('button_continue'), 
		                                                               'icon' => 'fa fa-check'
		                                                               ));



		//second form
		$form = new AForm();
        $form->setForm(array( 'form_name' => 'loginFrm' ));
        $this->data['form2']['form_open'] = $form->getFieldHtml(
                                                                array(
                                                                       'type' => 'form',
                                                                       'name' => 'loginFrm',
                                                                       'action' => $this->html->getSecureURL('account/login')));
	
		if($this->config->get('prevent_email_as_login')){
			$this->data['noemaillogin'] = true;
		}

		$this->data['form2']['loginname'] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'loginname',
		                                                               'value' => $loginname
		                                                               ));
		//support old email based loging. Remove in the future
		$this->data['form2']['email'] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'email',
		                                                               'value' => $loginname
		                                                               ));
		$this->data['form2']['password'] = $form->getFieldHtml( array(
                                                                       'type' => 'password',
		                                                               'name' => 'password'));
		$this->data['form2']['login_submit'] = $form->getFieldHtml(array(
																		'type' => 'submit',
																		'name' => $this->language->get('button_login'),
																		'icon' => 'fa fa-lock'
																		));

		$this->view->assign('success', '' );
        if (isset($this->session->data['success'])) {
    		$this->view->assign('success', $this->session->data['success']);
			unset($this->session->data['success']);
		}

        $this->data['forgotten_pass'] = $this->html->getSecureURL('account/forgotten/password');
        $this->data['forgotten_login'] = $this->html->getSecureURL('account/forgotten/loginname');
        $this->data['guest_checkout'] = ($this->config->get('config_guest_checkout') && $this->cart->hasProducts() && !$this->cart->hasDownload());

		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/account/login.tpl');
		
        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
  
  	private function _validate($loginname, $password) {
    	if (!$this->customer->login( $loginname, $password )) {
      		$this->error['message'] = $this->language->get('error_login');
    	}else{
		    $this->loadModel('account/address');
			$address = $this->model_account_address->getAddress($this->customer->getAddressId());

		    $this->session->data['country_id'] = $address['country_id'];
		    $this->session->data['zone_id'] = $address['zone_id'];
	
			//check if existing customer has loginname = email. Redirect if not allowed
			if ($this->config->get('prevent_email_as_login') && $this->customer->isLoginnameAsEmail() ) {
				$this->redirect($this->html->getSecureURL('account/edit'));
			}
	    }
	
    	if (!$this->error) {
      		return TRUE;
    	} else {
      		return FALSE;
    	}  	
  	}
}
?>