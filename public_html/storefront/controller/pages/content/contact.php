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
class ControllerPagesContentContact extends AController {
	private $error = array(); 
	private $form;
  	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );  

	    $this->form = new AForm('ContactUsFrm');
		$this->form->loadFromDb('ContactUsFrm');

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validate()) {
			$mail = new AMail( $this->config );
			$mail->setTo($this->config->get('store_main_email'));
	  		$mail->setFrom($this->request->post['email']);
	  		$mail->setSender($this->request->post['first_name']);
	  		$mail->setSubject(sprintf($this->language->get('email_subject'), $this->request->post['name']));
	  		$mail->setText(strip_tags(html_entity_decode($this->request->post['enquiry'], ENT_QUOTES, 'UTF-8')));
      		$mail->send();

	  		$this->redirect($this->html->getSecureURL('content/contact/success'));
    	}

	    if (($this->request->server['REQUEST_METHOD'] == 'POST')){
	 	  foreach($this->request->post as $name => $value){
			  $this->form->assign($name,$value);
		  }
	    }

      	$this->document->resetBreadcrumbs();

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('index/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	 ));

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('content/contact'),
        	'text'      => $this->language->get('heading_title'),
        	'separator' => $this->language->get('text_separator')
      	 ));	


		$this->view->assign('form_output', $this->form->getFormHtml() );

		$this->view->assign('action', $this->html->getURL('content/contact'));
		$this->view->assign('store', $this->config->get('store_name'));
    	$this->view->assign('address', nl2br($this->config->get('config_address')) );
    	$this->view->assign('telephone', $this->config->get('config_telephone') );
    	$this->view->assign('fax', $this->config->get('config_fax') );
	
		$this->processTemplate('pages/content/contact.tpl' );

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	public function success() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);


		$this->document->setTitle( $this->language->get('heading_title') ); 

      	$this->document->resetBreadcrumbs();

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('index/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	 ));

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('content/contact'),
        	'text'      => $this->language->get('heading_title'),
        	'separator' => $this->language->get('text_separator')
      	 ));	
		
    	$this->view->assign('continue', $this->html->getURL('index/home'));
		$continue = HtmlElementFactory::create( array ('type' => 'button',
		                                               'name' => 'continue_button',
			                                           'text'=> $this->language->get('button_continue'),
			                                           'style' => 'button'));
		$this->view->assign('continue_button', $continue->getHtml());

		$this->processTemplate('common/success.tpl' );

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

  	private function _validate() {

    	if ((strlen(utf8_decode($this->request->post['first_name'])) < 3) || (strlen(utf8_decode($this->request->post['first_name'])) > 32)) {
		    $this->error['first_name'] = $this->language->get('error_name');
    	}

		$pattern = '/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i';

    	if (!preg_match($pattern, $this->request->post['email'])) {
      		$this->error['email'] = $this->language->get('error_email');
    	}

    	if ((strlen(utf8_decode($this->request->post['enquiry'])) < 10) || (strlen(utf8_decode($this->request->post['enquiry'])) > 3000)) {
      		$this->error['enquiry'] = $this->language->get('error_enquiry');
    	}

    	if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
      		$this->error['captcha'] = $this->language->get('error_captcha');
    	}
		
		if (!$this->error) {
	  		return TRUE;
		} else {
			$this->form->setErrors($this->error);
	  		return FALSE;
		}  	  
  	}
}
?>
