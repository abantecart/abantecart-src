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
class ControllerPagesAccountSuccess extends AController {
	public function main() {

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
        	'href'      => $this->html->getURL('account/account'),
        	'text'      => $this->language->get('text_account'),
        	'separator' => $this->language->get('text_separator')
      	 ));

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('account/success'),
        	'text'      => $this->language->get('text_success'),
        	'separator' => $this->language->get('text_separator')
      	 ));

        $this->view->assign('heading_title', $this->language->get('heading_title') );

		if (!$this->config->get('config_customer_approval')) {
    		$this->view->assign('text_message', sprintf($this->language->get('text_message'), $this->html->getURL('content/contact')) );
		} else {
			$this->view->assign('text_message', sprintf($this->language->get('text_approval'), $this->config->get('store_name'), $this->html->getURL('content/contact')) );
		}
		
        $this->view->assign('button_continue', $this->language->get('button_continue') );
		
		if ( $this->session->data['redirect'] ) {
			$this->view->assign('continue', $this->session->data['redirect'] );
			unset($this->session->data['redirect']);
		} else {
			if ($this->cart->hasProducts()) {
	            $this->view->assign('continue', $this->html->getURL('checkout/cart') );
			} else {
	            $this->view->assign('continue', $this->html->getURL('account/account') );
			}
		}

		$continue = HtmlElementFactory::create( array ('type' => 'button',
		                                               'name' => 'continue_button',
			                                           'text'=> $this->language->get('button_continue'),
			                                           'style' => 'button'));
		$this->view->assign('continue_button', $continue->getHtml());

		$this->processTemplate('common/success.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
}
?>