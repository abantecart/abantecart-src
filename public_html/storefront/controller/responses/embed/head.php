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
class ControllerResponsesEmbedHead extends AController {
	public function main() {

		//is this an embed mode
		if($this->config->get('embed_mode') == true){
			$cart_rt = 'r/checkout/cart/embed';
		} else{
			$cart_rt = 'checkout/cart';
		}

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		
		$this->loadLanguage('common/header');
		
		$this->view->assign('template', $this->config->get('config_storefront_template'));
		$this->view->assign('retina', $this->config->get('config_retina_enable'));
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->view->assign('base', HTTPS_SERVER);
		} else {
			$this->view->assign('base', HTTP_SERVER);
		}

		$this->view->assign('lang', $this->language->get('code'));
		$this->view->assign('direction', $this->language->get('direction'));
		$this->view->assign('links', $this->document->getLinks());	
		$this->view->assign('styles', $this->document->getStyles());
		$this->view->assign('scripts', $this->document->getScripts());		
		
		$this->view->assign('store', $this->config->get('store_name'));
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
		    $this->view->assign('ssl', 1);
        }
		$this->view->assign('cart_url', $this->html->getURL($cart_rt));
        $this->view->assign('cart_ajax', (int) $this->config->get('config_cart_ajax'));
        $this->view->assign('cart_ajax_url', $this->html->getURL('r/product/product/addToCart'));
        $this->view->assign('search_url', $this->html->getURL('product/search'));

        $this->view->assign('call_to_order_url', $this->html->getURL('content/contact'));

		if($this->config->get('config_maintenance') && isset($this->session->data['merchant'])){
			$this->view->assign('maintenance_warning',$this->language->get('text_maintenance_notice'));
		}
		
		$this->processTemplate('embed/head.tpl');

		//Log Online Customers
		$ip = '';
		if (isset($this->request->server['REMOTE_ADDR'])) {
		        $ip = $this->request->server['REMOTE_ADDR'];
		}
		$url = '';
		if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
		        $url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
		}
		$referer = '';
		if (isset($this->request->server['HTTP_REFERER'])) {
		        $referer = $this->request->server['HTTP_REFERER'];
		}
		$customer_id = '';
		if ( is_object($this->customer)) {
			$customer_id = $this->customer->getId();
		}
		$this->loadModel('tool/online_now');		
		$this->model_tool_online_now->setOnline($ip, $customer_id, $url, $referer);
 
        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}	
}