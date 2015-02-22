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
class ControllerPagesAccountForgotten extends AController {
	private $error = array();
	public $data = array();
	public function main() {
		$this->extensions->hk_InitData($this,__FUNCTION__);
		$this->password();
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function password() {

        $this->extensions->hk_InitData($this,__FUNCTION__);

		if ($this->customer->isLogged()) {
			$this->redirect($this->html->getSecureURL('account/account'));
		}

		$this->document->setTitle( $this->language->get('heading_title') );
		
		$this->loadModel('account/customer');
		
		$cust_detatils = array();
		if ($this->request->is_POST() && $this->_find_customer('password', $cust_detatils)) {
			//extra check that we have csutomer details 
			if (!empty($cust_detatils['email'])) {
				$this->loadLanguage('mail/account_forgotten');
				
				$password = substr(md5(rand()), 0, 7);
				
				$subject = sprintf($this->language->get('text_subject'), $this->config->get('store_name'));
				
				$message  = sprintf($this->language->get('text_greeting'), $this->config->get('store_name')) . "\n\n";
				$message .= $this->language->get('text_password') . "\n\n";
				$message .= $password;
	
				$mail = new AMail( $this->config );
				$mail->setTo($cust_detatils['email']);
				$mail->setFrom($this->config->get('store_main_email'));
				$mail->setSender($this->config->get('store_name'));
				$mail->setSubject($subject);
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$mail->send();
				
				$this->model_account_customer->editPassword($cust_detatils['loginname'], $password);
				
				$this->session->data['success'] = $this->language->get('text_success');
				$this->redirect($this->html->getSecureURL('account/login'));				
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
        	'href'      => $this->html->getURL('account/forgotten/password'),
        	'text'      => $this->language->get('text_forgotten'),
        	'separator' => $this->language->get('text_separator')
      	 ));
        
		$this->view->assign('error', $this->error['message'] );
		$this->view->assign('action', $this->html->getSecureURL('account/forgotten') );
        $this->view->assign('back', $this->html->getSecureURL('account/account') );


		$form = new AForm();
        $form->setForm(array( 'form_name' => 'forgottenFrm' ));
        $this->data['form'][ 'form_open' ] = $form->getFieldHtml(
                                                                array(
                                                                       'type' => 'form',
                                                                       'name' => 'forgottenFrm',
                                                                       'action' => $this->html->getSecureURL('account/forgotten/password')));
		
		
		//verify loginname if non email login used or data encryption is ON
		if( $this->config->get('prevent_email_as_login') || $this->dcrypt->active ){
			$this->data['form']['fields'][ 'loginname' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'loginname',
		                                                               'value' => $this->request->post['loginname'] ));
		    $this->data['help_text'] =  $this->language->get('text_loginname_email');                                                       
		} else {
		    $this->data['help_text'] =  $this->language->get('text_email');       		
		}
		
		$this->data['form']['fields'][ 'email' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'email',
		                                                               'value' => $this->request->post['email'] ));
		
		$this->data['form'][ 'continue' ] = $form->getFieldHtml( array(
                                                                       'type' => 'submit',
		                                                               'name' => $this->language->get('button_continue') ));
		$this->data['form'][ 'back' ] = $form->getFieldHtml( array(
                                                                    'type' => 'button',
		                                                            'name' => 'back',
			                                                        'style' => 'button',
		                                                            'text' => $this->language->get('button_back') ));
		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/account/forgotten.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}

	public function loginname() {

        $this->extensions->hk_InitData($this,__FUNCTION__);

		if ($this->customer->isLogged()) {
			$this->redirect($this->html->getSecureURL('account/account'));
		}

		$this->document->setTitle( $this->language->get('heading_title_loginname') );
		
		$this->loadModel('account/customer');
		
		$cust_detatils = array();
		if ($this->request->is_POST() && $this->_find_customer('loginname', $cust_detatils)) {
			//extra check that we have csutomer details 
			if (!empty($cust_detatils['email'])) {
				$this->loadLanguage('mail/account_forgotten_login');
				
				$subject = sprintf($this->language->get('text_subject'), $this->config->get('store_name'));
				
				$message  = sprintf($this->language->get('text_greeting'), $this->config->get('store_name')) . "\n\n";
				$message .= $this->language->get('text_your_loginname') . "\n\n";
				$message .= $cust_detatils['loginname'];
	
				$mail = new AMail( $this->config );
				$mail->setTo($cust_detatils['email']);
				$mail->setFrom($this->config->get('store_main_email'));
				$mail->setSender($this->config->get('store_name'));
				$mail->setSubject($subject);
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$mail->send();
				
				$this->session->data['success'] = $this->language->get('text_success_loginname');
				$this->redirect($this->html->getSecureURL('account/login'));				
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
        	'href'      => $this->html->getURL('account/forgotten/loginname'),
        	'text'      => $this->language->get('text_forgotten_loginname'),
        	'separator' => $this->language->get('text_separator')
      	 ));
        
		$this->view->assign('error', $this->error['message'] );
		$this->view->assign('action', $this->html->getSecureURL('account/forgotten') );
        $this->view->assign('back', $this->html->getSecureURL('account/account') );


		$form = new AForm();
        $form->setForm(array( 'form_name' => 'forgottenFrm' ));
        $this->data['form'][ 'form_open' ] = $form->getFieldHtml(
                                                                array(
                                                                       'type' => 'form',
                                                                       'name' => 'forgottenFrm',
                                                                       'action' => $this->html->getSecureURL('account/forgotten/loginname')));
		
		$this->data['help_text'] =  $this->language->get('text_lastname_email');                                                       
		$this->data['heading_title'] =  $this->language->get('heading_title_loginname');                                                       
				
		$this->data['form']['fields'][ 'lastname' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'lastname',
		                                                               'value' => $this->request->post['lastname'] ));
		$this->data['form']['fields'][ 'email' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'email',
		                                                               'value' => $this->request->post['email'] ));
		
		$this->data['form'][ 'continue' ] = $form->getFieldHtml( array(
                                                                       'type' => 'submit',
		                                                               'name' => $this->language->get('button_continue') ));
		$this->data['form'][ 'back' ] = $form->getFieldHtml( array(
                                                                    'type' => 'button',
		                                                            'name' => 'back',
			                                                        'style' => 'button',
		                                                            'text' => $this->language->get('button_back') ));
		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/account/forgotten.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}

	private function _find_customer($mode, &$cust_details ) {
		$email = $this->request->post['email'];
		$loginname = $this->request->post['loginname'];
		$lastname = $this->request->post['lastname'];
		//email is always required 
		if (!isset($email) || empty($email) ) {
			$this->error['message'] = $this->language->get('error_email');
			return FALSE;
		}		
		
		//locate customer based on login name
		if( $this->config->get('prevent_email_as_login') || $this->dcrypt->active ){
			if ( $mode == 'password'){
				if (!empty($loginname)) {
					$cust_details = $this->model_account_customer->getCustomerByLoginnameAndEmail($loginname, $email);	
				} else {
					$this->error['message'] = $this->language->get('error_loginname');
					return FALSE;			
				}
			} else if ( $mode == 'loginname') {
				if (!empty($lastname)) {
					$cust_details = $this->model_account_customer->getCustomerByLastnameAndEmail($lastname, $email);	
				} else {
					$this->error['message'] = $this->language->get('error_lastname');
					return FALSE;			
				}			
			}
		} else {
			//get customer by email
			$cust_details = $this->model_account_customer->getCustomerByEmail($email);
		}
		
		if ( !count($cust_details) ) {
			$this->error['message'] = $this->language->get('error_not_found');
			return FALSE;			
		} else {
			return TRUE;	
		}		
	}
}
