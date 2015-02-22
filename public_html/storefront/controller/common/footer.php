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
class ControllerCommonFooter extends AController {
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		
		$this->loadLanguage('common/header');
		$this->data['text_copy'] = $this->config->get('store_name') .' &copy; '. date('Y', time());
		
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

		if ($this->config->get('config_google_analytics_code')) {
			$this->data['google_analytics'] =  $this->config->get('config_google_analytics_code');
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
		
		$this->data['text_project_label'] = $this->language->get('text_powered_by') . ' ' . project_base();

		$this->view->assign('scripts_bottom', $this->document->getScriptsBottom());		

		$this->view->batchAssign($this->data);
		$this->processTemplate('common/footer.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}
}
