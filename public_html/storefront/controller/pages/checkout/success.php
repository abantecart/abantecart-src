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
class ControllerPagesCheckoutSuccess extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();
			
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);	
			unset($this->session->data['coupon']);
		}	
									   
		$this->document->setTitle( $this->language->get('heading_title') );
		
		$this->document->resetBreadcrumbs(); 

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('index/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	 )); 

		
      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('checkout/cart'),
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	 ));
		
		if ($this->customer->isLogged()) {
			$this->document->addBreadcrumb( array ( 
				'href'      => $this->html->getURL('checkout/shipping'),
				'text'      => $this->language->get('text_shipping'),
				'separator' => $this->language->get('text_separator')
			 ));
	
			$this->document->addBreadcrumb( array ( 
				'href'      => $this->html->getURL('checkout/payment'),
				'text'      => $this->language->get('text_payment'),
				'separator' => $this->language->get('text_separator')
			 ));
	
			$this->document->addBreadcrumb( array ( 
				'href'      => $this->html->getURL('checkout/confirm'),
				'text'      => $this->language->get('text_confirm'),
				'separator' => $this->language->get('text_separator')
			 ));
		} else {
			$this->document->addBreadcrumb( array ( 
				'href'      => $this->html->getURL('checkout/guest'),
				'text'      => $this->language->get('text_guest'),
				'separator' => $this->language->get('text_separator')
			 ));
	
			$this->document->addBreadcrumb( array ( 
				'href'      => $this->html->getURL('checkout/guest/confirm'),
				'text'      => $this->language->get('text_confirm'),
				'separator' => $this->language->get('text_separator')
			 ));			
		}
		
      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('checkout/success'),
        	'text'      => $this->language->get('text_success'),
        	'separator' => $this->language->get('text_separator')
      	 ));
		
    	$this->view->assign('heading_title', $this->language->get('heading_title'));
		if($this->session->data['account']=='guest'){

			$this->view->assign('text_message',
								sprintf( $this->language->get('text_message_guest'),$this->html->getURL('content/contact')));
		}else{
			$this->view->assign('text_message',
								sprintf( $this->language->get('text_message'),
										$this->html->getSecureURL('account/account'),
										$this->html->getSecureURL('account/history'),
										$this->html->getURL('content/contact')));
		}
    	$this->view->assign('button_continue', $this->language->get('button_continue'));
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
}
?>