<?php 
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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
	/**
	 * @var AForm
	 */
	private $form;
  	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );  

	    $this->form = new AForm('ContactUsFrm');
		$this->form->loadFromDb('ContactUsFrm');
	    $form = $this->form->getForm();

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validate()) {
		    // move all uploaded files to their directories
		    $file_pathes = $this->form->processFileUploads($this->request->files);

			$mail = new AMail( $this->config );
			$mail->setTo($this->config->get('store_main_email'));
	  		$mail->setFrom($this->request->post['email']);
	  		$mail->setSender($this->request->post['first_name']);
	  		$mail->setSubject(sprintf($this->language->get('email_subject'), $this->request->post['name']));
	  		$msg = $this->request->post['enquiry'];
			if($file_pathes){
				$msg .= "\r\n".$this->language->get('entry_attached').": \r\n";
				foreach($file_pathes as $file_info){
					$basename = pathinfo(str_replace(' ','_',$file_info['path']),PATHINFO_BASENAME);
					$msg .= "\t" .$file_info['display_name'] . ': ' . $basename . " (". round(filesize($file_info['path'])/1024,2) ."Kb)\r\n";
					$mail->addAttachment($file_info['path'], $basename);
				}
			}
			$mail->setText(strip_tags(html_entity_decode($msg, ENT_QUOTES, 'UTF-8')));
      		$mail->send();
		    //get success_page
		    if($form['success_page']){
			    $success_url = $this->html->getSecureURL($form['success_page']);
		    }else{
			    $success_url = $this->html->getSecureURL('content/contact/success');
		    }
	  		$this->redirect($success_url);
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
		$this->view->assign('continue_button', $continue);

		$this->processTemplate('common/success.tpl' );

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	/**
	 * @return bool
	 */
	private function _validate() {
		$aform_errors = $this->form->validateFormData($this->request->post);
	    $this->error = array_merge($this->form->validateFormData($this->request->post),$this->error);
		
		if (!$this->error) {
	  		return TRUE;
		} else {
			$this->form->setErrors($this->error);
	  		return FALSE;
		}  	  
  	}
}
