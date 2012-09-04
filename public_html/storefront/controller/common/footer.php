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
class ControllerCommonFooter extends AController {
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		
		$this->loadLanguage('common/header');
		$this->data['text_powered_by'] =  $this->language->get('text_powered_by');
		$this->data['text_copy'] = $this->config->get('store_name') .' &copy; '. date('Y', time());


		$this->data['text_home'] =  $this->language->get('text_home');
		$this->data['text_special'] =  $this->language->get('text_special');
		$this->data['text_contact'] =  $this->language->get('text_contact');
		$this->data['text_sitemap'] =  $this->language->get('text_sitemap');
		$this->data['text_bookmark'] =  $this->language->get('text_bookmark');
    	$this->data['text_account'] =  $this->language->get('text_account');
    	$this->data['text_login'] =  $this->language->get('text_login');
    	$this->data['text_logout'] =  $this->language->get('text_logout');
    	$this->data['text_cart'] =  $this->language->get('text_cart');
    	$this->data['text_checkout'] =  $this->language->get('text_checkout');
		
		$this->data['home'] =  $this->html->getURL('index/home');
		$this->data['special'] =  $this->html->getURL('product/special');
		$this->data['contact'] =  $this->html->getURL('content/contact');
    	$this->data['sitemap'] =  $this->html->getURL('content/sitemap');
    	$this->data['account'] =  $this->html->getSecureURL('account/account');
		$this->data['logged'] =  $this->customer->isLogged();
		$this->data['login'] =  $this->html->getSecureURL('account/login');
		$this->data['logout'] =  $this->html->getURL('account/logout');
    	$this->data['cart'] =  $this->html->getURL('checkout/cart');
		$this->data['checkout'] =  $this->html->getSecureURL('checkout/shipping');

		if ($this->config->get('google_analytics_status')) {
			$this->data['google_analytics'] =  html_entity_decode($this->config->get('google_analytics_code'), ENT_QUOTES, 'UTF-8');
		} else {
			$this->data['google_analytics'] =  '';
		}

		$children = $this->getChildren();
		foreach($children as $child){
			 if($child['block_txt_id']=='donate'){
				 $this->data['donate'] = 'donate_'.$child['instance_id'];
			 }
			 if($child['block_txt_id']=='credit_cards'){
				 $this->data['credit_cards'] = 'credit_cards_'.$child['instance_id'];
			 }
		}

		$this->view->batchAssign($this->data);
		$this->processTemplate('common/footer.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}
}
?>