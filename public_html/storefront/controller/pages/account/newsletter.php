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
class ControllerPagesAccountNewsletter extends AController {
	public $data=array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->html->getSecureURL('account/newsletter');
	  
	  		$this->redirect($this->html->getSecureURL('account/login'));
    	} 
		
		$this->document->setTitle( $this->language->get('heading_title') );
				
		if ($this->request->is_POST()) {
			$this->loadModel('account/customer');
			
			$this->model_account_customer->editNewsletter($this->request->post['newsletter']);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->html->getURL('account/account'));
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
        	'href'      => $this->html->getURL('account/newsletter'),
        	'text'      => $this->language->get('text_newsletter'),
        	'separator' => $this->language->get('text_separator')
      	 ));
        $form = new AForm();
        $form->setForm(array( 'form_name' => 'newsFrm' ));
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
                                               'type' => 'form',
                                               'name' => 'newsFrm',
                                               'action' => $this->html->getSecureURL('account/newsletter') ));
        $this->data['form']['newsletter'] = $form->getFieldHtml( array(
                                                 'type' => 'radio',
		                                         'name' => 'newsletter',
		                                         'value' => $this->customer->getNewsletter(),
		                                         'options' => array(
                                                                    '1' => $this->language->get('text_yes'),
                                                                    '0' => $this->language->get('text_no'),
                                                                      ) ));
		$this->data['form'][ 'continue' ] = $form->getFieldHtml( array(
                                                                       'type' => 'submit',
                                                                       'icon' => 'fa fa-check',
		                                                               'name' => $this->language->get('button_continue') ));

       	$this->data['back'] = $this->html->getURL('account/account');
		$back = HtmlElementFactory::create( array ('type' => 'button',
		                                           'name' => 'back',
			                                       'text'=> $this->language->get('button_back'),
			                                       'icon' => 'fa fa-arrow-left',
			                                       'style' => 'button'));
		$this->data['form']['back'] = $back;

		$this->view->batchAssign( $this->data );

		$this->processTemplate('pages/account/newsletter.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
}
