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
class ControllerPagesAccountPassword extends AController {
	public $error = array();
	     
  	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	if (!$this->customer->isLogged()) {
      		$this->session->data['redirect'] = $this->html->getSecureURL('account/password');
      		$this->redirect($this->html->getSecureURL('account/login'));
    	}

    	$this->document->setTitle( $this->language->get('heading_title') );
			  
    	if ($this->request->is_POST() && $this->_validate()) {
			$this->loadModel('account/customer');
			
			$this->model_account_customer->editPassword($this->customer->getLoginName(), $this->request->post['password']);
 
      		$this->session->data['success'] = $this->language->get('text_success');
	  
	  		$this->redirect($this->html->getSecureURL('account/account'));
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
        	'href'      => $this->html->getURL('account/password'),
        	'text'      => $this->language->get('heading_title'),
        	'separator' => $this->language->get('text_separator')
      	 ));
			
        $this->view->assign('error_warning', $this->error['warning'] );
        $this->view->assign('error_current_password', $this->error['current_password'] );
        $this->view->assign('error_password', $this->error['password'] );
        $this->view->assign('error_confirm', $this->error['confirm'] );

	    $form = new AForm();
        $form->setForm(array('form_name' => 'PasswordFrm'));
        $form_open = $form->getFieldHtml(
            array(
                 'type' => 'form',
                 'name' => 'PasswordFrm',
                 'action' => $this->html->getSecureURL('account/password')));
    	$this->view->assign('form_open', $form_open);

        $current_password = $form->getFieldHtml( array(
                                               'type' => 'password',
		                                       'name' => 'current_password',
		                                       'value' => '',
		                                       'required' => true ));
        $password = $form->getFieldHtml( array(
                                               'type' => 'password',
		                                       'name' => 'password',
		                                       'value' => '',
		                                       'required' => true ));
		$confirm = $form->getFieldHtml( array(
                                               'type' => 'password',
		                                       'name' => 'confirm',
		                                       'value' => '',
		                                       'required' => true ));
		$submit = $form->getFieldHtml( array(
                                               'type' => 'submit',
		                                       'name' => $this->language->get('button_continue'),
		                                       'icon' => 'fa fa-check',
		                                        ));

		$this->view->assign('current_password', $current_password );
		$this->view->assign('password', $password );
		$this->view->assign('submit', $submit );
		$this->view->assign('confirm', $confirm );
		$this->view->assign('back', $this->html->getSecureURL('account/account') );

		$back = HtmlElementFactory::create( array ('type' => 'button',
		                                           'name' => 'back',
			                                       'text'=> $this->language->get('button_back'),
			                                       'icon' => 'fa fa-arrow-left',
			                                       'style' => 'button'));
		$this->view->assign('button_back', $back);

        $this->processTemplate('pages/account/password.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
  
  	private function _validate() {
    	if ( empty($this->request->post['current_password']) 
    			|| !$this->customer->login( $this->customer->getLoginName(), $this->request->post['current_password'] )) {
      		$this->error['current_password'] = $this->language->get('error_current_password');
		}

    	if ( mb_strlen($this->request->post['password']) < 4 || mb_strlen($this->request->post['password']) > 20 ) {
      		$this->error['password'] = $this->language->get('error_password');
    	}

    	if ($this->request->post['confirm'] != $this->request->post['password']) {
      		$this->error['confirm'] = $this->language->get('error_confirm');
    	}

	    $this->extensions->hk_ValidateData($this);
	
		if (!$this->error) {
	  		return TRUE;
		} else {
			$this->error['warning'] = $this->language->get('gen_data_entry_error');
	  		return FALSE;
		}
  	}
}
