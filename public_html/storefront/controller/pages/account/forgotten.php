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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesAccountForgotten extends AController {
	private $error = array();
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if ($this->customer->isLogged()) {
			$this->redirect($this->html->getSecureURL('account/account'));
		}

		$this->document->setTitle( $this->language->get('heading_title') );
		
		$this->loadModel('account/customer');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validate()) {
			$this->loadLanguage('mail/account_forgotten');
			
			$password = substr(md5(rand()), 0, 7);
			
			$subject = sprintf($this->language->get('text_subject'), $this->config->get('store_name'));
			
			$message  = sprintf($this->language->get('text_greeting'), $this->config->get('store_name')) . "\n\n";
			$message .= $this->language->get('text_password') . "\n\n";
			$message .= $password;

			$mail = new AMail( $this->config );
			$mail->setTo($this->request->post['email']);
			$mail->setFrom($this->config->get('store_main_email'));
			$mail->setSender($this->config->get('store_name'));
			$mail->setSubject($subject);
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
			
			$this->model_account_customer->editPassword($this->request->post['email'], $password);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->html->getSecureURL('account/login'));
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
        	'href'      => $this->html->getURL('account/forgotten'),
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
                                                                       'action' => $this->html->getSecureURL('account/forgotten')));

		$this->data['form'][ 'email' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'email',
		                                                               'value' => '' ));
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

	private function _validate() {
		if (!isset($this->request->post['email'])) {
			$this->error['message'] = $this->language->get('error_email');
		} elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['message'] = $this->language->get('error_email');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>