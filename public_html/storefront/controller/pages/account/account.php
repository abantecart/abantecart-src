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

class ControllerPagesAccountAccount extends AController {
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->html->getSecureURL('account/account');
	  		$this->redirect($this->html->getSecureURL('account/login'));
    	} 

		$this->loadLanguage('account/account');

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
        $this->data['transactions'] = $this->html->getSecureURL('account/transactions');
        $this->data['wishlist'] = $this->html->getSecureURL('account/wishlist');
        
		$this->loadLanguage('common/header');
        $this->data['logout'] = $this->html->getSecureURL('account/logout');

		$this->data['customer_name'] = $this->customer->getFirstName();

		$balance = $this->customer->getBalance();

		$this->data['balance_amount'] = $this->currency->format($balance);
		if($balance!=0 || ($balance==0 && $this->config->get('config_zero_customer_balance'))){
			$this->data['balance'] = $this->language->get('text_balance_checkout').' '.$this->data['balance_amount'];
		}
		
		$this->data['total_wishlist'] = count($this->customer->getWishList());		


		$this->loadModel('account/address');
		$this->data['total_adresses'] = $this->model_account_address->getTotalAddresses();

		$this->data['total_downloads'] = $this->download->getTotalDownloads();

		$this->loadModel('account/order');
		$this->data['total_orders'] = $this->model_account_order->getTotalOrders();

		$this->view->batchAssign($this->data);

		$this->processTemplate('pages/account/account.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
}
