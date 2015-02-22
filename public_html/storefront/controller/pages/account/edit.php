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
class ControllerPagesAccountEdit extends AController {
	private $error = array();
    public $data;
    
	public function main() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->html->getSecureURL('account/edit');

			$this->redirect($this->html->getSecureURL('account/login'));
		}

		$this->document->setTitle( $this->language->get('heading_title') );
		
		$this->loadModel('account/customer');

		$request_data = $this->request->post;
		if ( $this->request->is_POST()) {
			$this->error = $this->model_account_customer->validateEditData($request_data);
			//if no update for loginname do not allow edit of username/loginname
			if ( !$this->customer->isLoginnameAsEmail() ) {
				$request_data['loginname'] = null;
			} else {
			//if allow login as email, need to set loginname = email in case email changed 
				if (!$this->config->get('prevent_email_as_login')) {
					$request_data['loginname'] = $request_data['email'];
				}
			}
			
    		if ( !$this->error ) {		
				$this->model_account_customer->editCustomer($request_data);
				$this->session->data['success'] = $this->language->get('text_success');
				$this->redirect($this->html->getSecureURL('account/account'));
			}
		}

		//check if existing customer has loginname = email. Redirect if not allowed
		$reset_loginname = false;
		if ($this->config->get('prevent_email_as_login') && $this->customer->isLoginnameAsEmail() ) {
		    $this->error['warning'] = $this->language->get('loginname_update_required');
		    $reset_loginname = true;
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
        	'href'      => $this->html->getURL('account/edit'),
        	'text'      => $this->language->get('text_edit'),
        	'separator' => $this->language->get('text_separator')
      	 ));
		
		$this->view->assign('error_warning', $this->error['warning'] );
		$this->view->assign('error_loginname', $this->error['loginname'] );
		$this->view->assign('error_firstname', $this->error['firstname'] );
		$this->view->assign('error_lastname', $this->error['lastname'] );
		$this->view->assign('error_email', $this->error['email'] );
		$this->view->assign('error_telephone', $this->error['telephone'] );

	
		if ($this->request->is_GET()) {
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		}

		if (isset($request_data['loginname'])) {
			$loginname = $request_data['loginname'];
		} elseif (isset($customer_info)) {
            $loginname = $customer_info['loginname'];
		}

		if (isset($request_data['firstname'])) {
			$firstname = $request_data['firstname'];
		} elseif (isset($customer_info)) {
            $firstname = $customer_info['firstname'];
		}

		if (isset($request_data['lastname'])) {
			$lastname = $request_data['lastname'];
		} elseif (isset($customer_info)) {
            $lastname = $customer_info['lastname'];
		}

		if (isset($request_data['email'])) {
			$email = $request_data['email'];
		} elseif (isset($customer_info)) {
            $email = $customer_info['email'] ;
		}

		if (isset($request_data['telephone'])) {
			$telephone = $request_data['telephone'];
		} elseif (isset($customer_info)) {
            $telephone = $customer_info['telephone'];
		}

		if (isset($request_data['fax'])) {
			$fax = $request_data['fax'];
		} elseif (isset($customer_info)) {
            $fax = $customer_info['fax'];
		}
        $form = new AForm();
        $form->setForm(array( 'form_name' => 'AccountFrm' ));
        $this->data['form'][ 'form_open' ] = $form->getFieldHtml(
                                                                array(
                                                                       'type' => 'form',
                                                                       'name' => 'AccountFrm',
                                                                       'action' => $this->html->getSecureURL('account/edit')));

		$this->data['reset_loginname'] = $reset_loginname;
		
		if($reset_loginname) {
			$this->data['form'][ 'loginname' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'loginname',
		                                                               'value' => $loginname,
		                                                               'style' => 'highlight',
		                                                               'required' => true ));	
		} else {
			$this->data['form'][ 'loginname' ] = $loginname;
		}
		
		$this->data['form'][ 'firstname' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'firstname',
		                                                               'value' => $firstname,
		                                                               'required' => true ));
		$this->data['form'][ 'lastname' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'lastname',
		                                                               'value' => $lastname,
		                                                               'required' => true ));
		$this->data['form'][ 'email' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'email',
		                                                               'value' => $email,
		                                                               'required' => true ));
		$this->data['form'][ 'telephone' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'telephone',
		                                                               'value' => $telephone
		                                                                ));
		$this->data['form'][ 'fax' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'fax',
		                                                               'value' => $fax,
		                                                               'required' => false ));
		$this->data['form'][ 'continue' ] = $form->getFieldHtml( array(
                                                                       'type' => 'submit',
                                                                       'icon' => 'fa fa-check',
		                                                               'name' => $this->language->get('button_continue') ));
		$this->data['form'][ 'back' ] = $form->getFieldHtml( array(
                                                                    'type' => 'button',
		                                                            'name' => 'back',
			                                                        'style' => 'button',
			                                                        'icon' => 'fa fa-arrow-left',
		                                                            'text' => $this->language->get('button_back') ));
		$this->data['back'] = $this->html->getSecureURL('account/account');
		$this->view->batchAssign($this->data);
        $this->processTemplate('pages/account/edit.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
