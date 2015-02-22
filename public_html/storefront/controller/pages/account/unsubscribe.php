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
class ControllerPagesAccountUnsubscribe extends AController {
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if(has_value($this->request->get['customer_id'])
				&& (int)$this->request->get['customer_id']>0
				&& has_value($this->request->get['email'])){
			$this->loadModel('account/customer');
			$customer = $this->model_account_customer->getCustomer((int)$this->request->get['customer_id']);
			//check is customer_id exists and compare his email with given
			if($customer && $customer['email']==$this->request->get['email']){
					$this->model_account_customer->editNewsletter(0,(int)$this->request->get['customer_id']);
			}else{
				//othewise - redirect to index page
				$this->html->redirect($this->html->getSecureURL('index/home'));
			}
		}else{
			$this->html->redirect($this->html->getSecureURL('index/home'));
		}


    	$this->document->setTitle( $this->language->get('heading_title') );
		$this->document->resetBreadcrumbs();
      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('index/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	 ));


        $this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_message'] = $this->language->get('text_message');

		
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['continue'] = $this->html->getURL('index/home');

		$continue = HtmlElementFactory::create( array ('type' => 'button',
		                                               'name' => 'continue_button',
			                                           'text'=> $this->language->get('button_continue'),
			                                           'style' => 'button'));
		$this->data['continue_button'] = $continue;
		$this->view->batchAssign($this->data);
		$this->processTemplate('common/unsubscribe.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		unset($this->session->data['success']);
  	}
}