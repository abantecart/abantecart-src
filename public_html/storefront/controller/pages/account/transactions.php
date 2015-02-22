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
class ControllerPagesAccountTransactions extends AController {
	public $data = array();
	
	/**
	 * Main Controller function to show transaction hitory. 
	 * Note: Regular orders are considered in the transactions. 
	 */
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	if (!$this->customer->isLogged()) {
      		$this->session->data['redirect'] = $this->html->getSecureURL('account/transactions');

	  		$this->redirect($this->html->getSecureURL('account/login'));
    	}
 
    	$this->document->setTitle( $this->language->get('heading_title') );

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
        	'href'      => $this->html->getURL('account/transactions'),
        	'text'      => $this->language->get('text_transactions'),
        	'separator' => $this->language->get('text_separator')
      	 ));
				
		$this->loadModel('account/customer');

		$trans_total = $this->model_account_customer->getTotalTransactions();
		
		$balance = $this->customer->getBalance();
		$this->data['balance_amount'] = $this->currency->format($balance);
				
		if ($trans_total) {
			$this->data['action'] = $this->html->getURL('account/transactions');
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
				$limit = $limit>50 ? 50 : $limit;
			} else {
				$limit = $this->config->get('config_catalog_limit');
			}
			
      		$trans = array();
			
			$results = $this->model_account_customer->getTransactions(($page - 1) * $limit, $limit);
      		
			foreach ($results as $result) {
        		$trans[] = array(
								'customer_transaction_id'   => $result['customer_transaction_id'],
								'order_id'       => $result['order_id'],
								'section'     => $result['section'],
								'credit'      => $this->currency->format($result['credit']),
								'debit'      => $this->currency->format($result['debit']),
								'transaction_type' => $result['transaction_type'],
								'description' => $result['description'],
								'date_added' => dateISO2Display($result['date_added'],$this->language->get('date_format_short'))
        		);
      		}

			$this->data['transactions'] = $trans;

			$this->data['pagination_bootstrap'] = HtmlElementFactory::create( array (
										'type' => 'Pagination',
										'name' => 'pagination',
										'text'=> $this->language->get('text_pagination'),
										'text_limit' => $this->language->get('text_per_page'),
										'total'	=> $trans_total,
										'page'	=> $page,
										'limit'	=> $limit,
										'url' => $this->html->getURL('account/transactions', '&limit=' . $limit . '&page={page}'),
										'style' => 'pagination'));


			$this->data['continue'] =  $this->html->getSecureURL('account/account');

			$this->view->setTemplate('pages/account/transactions.tpl');
    	} else {
			$this->data['continue'] = $this->html->getSecureURL('account/account');

			$this->view->setTemplate('pages/account/transactions.tpl');
		}

		$this->data['button_continue'] = HtmlElementFactory::create( array ('type' => 'button',
																		   'name' => 'continue_button',
																		   'text'=> $this->language->get('button_continue'),
																		   'style' => 'button'));

		$this->view->batchAssign($this->data);
		$this->processTemplate();
        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}

