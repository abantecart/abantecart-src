<?php 
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2013 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>
  
 UPGRADE NOTE: 
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.  
------------------------------------------------------------------------------*/

class ControllerPagesAccountAccount extends AController {
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->html->getSecureURL('account/account');
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

		$this->document->setTitle( $this->language->get('heading_title') );

        $this->data['success'] = '';
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

        $this->data['information'] = $this->html->getSecureURL('account/edit');
        $this->data['password'] = $this->html->getSecureURL('account/password');
        $this->data['address'] = $this->html->getSecureURL('account/address');
        $this->data['history'] = $this->html->getSecureURL('account/history');
        $this->data['download'] = $this->html->getSecureURL('account/download');
        $this->data['newsletter'] = $this->html->getSecureURL('account/newsletter');

		$this->loadLanguage('common/header');
        $this->data['logout'] = $this->html->getSecureURL('account/logout');

		$this->data['customer_name'] = $this->customer->getFirstName();

		$balance = $this->customer->getBalance();

		if($balance!=0 || ($balance==0 && $this->config->get('config_zero_customer_balance'))){
			$this->data['balance'] = $this->language->get('text_balance_checkout').' '.$this->currency->format($balance);
		}

		$this->view->batchAssign($this->data);

		$this->processTemplate('pages/account/account.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
}
