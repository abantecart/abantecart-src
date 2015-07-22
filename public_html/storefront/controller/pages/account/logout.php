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
class ControllerPagesAccountLogout extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	if ($this->customer->isLogged() || $this->customer->isUnauthCustomer()) {
      		$this->customer->logout();
	  		$this->cart->clear();
			
			unset($this->session->data['shipping_address_id']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_address_id']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['merchant']);

		    if($this->config->get('config_tax_store')){
				$country_id = $this->config->get('config_country_id');
				$zone_id = $this->config->get('config_zone_id');
			}else{
				$country_id = $zone_id = 0;
			}
			$this->tax->setZone( $country_id, $zone_id );
			
      		$this->redirect($this->html->getSecureURL('account/logout'));
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
        	'href'      => $this->html->getURL('account/logout'),
        	'text'      => $this->language->get('text_logout'),
        	'separator' => $this->language->get('text_separator')
      	 ));	
		
    	$this->view->assign('continue', $this->html->getURL('index/home') );
		$continue = HtmlElementFactory::create( array ('type' => 'button',
		                                               'name' => 'continue_button',
			                                           'text'=> $this->language->get('button_continue'),
			                                           'style' => 'button'));
		$this->view->assign('continue_button', $continue);

		if($this->config->get('embed_mode') == true){
		    //load special headers
	        $this->addChild('responses/embed/head', 'head');
	        $this->addChild('responses/embed/footer', 'footer');
		    $this->processTemplate('embed/common/success.tpl');
		} else {
	    	$this->processTemplate('common/success.tpl');
	    }

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
}
