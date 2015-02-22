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
class ControllerPagesAccountHistory extends AController {
	public $data = array();
	
	/**
	 * Main controller function to show order hitory
	 */
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	if (!$this->customer->isLogged()) {
      		$this->session->data['redirect'] = $this->html->getSecureURL('account/history');

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
        	'href'      => $this->html->getURL('account/history'),
        	'text'      => $this->language->get('text_history'),
        	'separator' => $this->language->get('text_separator')
      	 ));
				
		$this->loadModel('account/order');

		$order_total = $this->model_account_order->getTotalOrders();
		
		if ($order_total) {
			$this->data['action'] = $this->html->getURL('account/history');
			
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
			
      		$orders = array();
			
			$results = $this->model_account_order->getOrders(($page - 1) * $limit, $limit);
      		
			foreach ($results as $result) {
        		$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
				$button = HtmlElementFactory::create( array (  'type' => 'button',
															   'name' => 'button_edit',
															   'text'=> $this->language->get('button_view'),
															   'style' => 'btn-default',
															   'icon' => 'fa fa-info',
				                                               'attr'  => ' onclick = "viewOrder('.$result['order_id'].');" ' ));
        		$orders[] = array(
								'order_id'   => $result['order_id'],
								'name'       => $result['firstname'] . ' ' . $result['lastname'],
								'status'     => $result['status'],
								'date_added' => dateISO2Display($result['date_added'], $this->language->get('date_format_short')),
								'products'   => $product_total,
								'total'      => $this->currency->format($result['total'], $result['currency'], $result['value']),
								'href'       => $this->html->getSecureURL('account/invoice', '&order_id=' . $result['order_id']),
								'button'     => $button->getHtml()
        		);
      		}

            $this->data['order_url'] = $this->html->getSecureURL('account/invoice');
			$this->data['orders'] =$orders;

			$this->data['pagination_bootstrap'] = HtmlElementFactory::create( array (
										'type' => 'Pagination',
										'name' => 'pagination',
										'text'=> $this->language->get('text_pagination'),
										'text_limit' => $this->language->get('text_per_page'),
										'total'	=> $order_total,
										'page'	=> $page,
										'limit'	=> $limit,
										'url' => $this->html->getURL('account/history', '&limit=' . $limit . '&page={page}'),
										'style' => 'pagination'));


			$this->data['continue'] =  $this->html->getSecureURL('account/account');

			$this->view->setTemplate('pages/account/history.tpl');
    	} else {
			$this->data['continue'] = $this->html->getSecureURL('account/account');

			$this->view->setTemplate('pages/error/not_found.tpl');
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
