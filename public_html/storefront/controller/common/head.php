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
class ControllerCommonHead extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		//if enabled system check for all 0 or for storefront only 2
		if(!$this->config->get('config_system_check') || $this->config->get('config_system_check') == 2 ) {
			//run system check to make sure system is stable to run the request
			//for storefront log messages. nothing is shown to users
			run_system_check($this->registry, 'log');
		}
		
		$this->loadLanguage('common/header');
		
		$this->view->assign('title', $this->document->getTitle());
		$this->view->assign('keywords', $this->document->getKeywords());
		$this->view->assign('description', $this->document->getDescription());
		$this->view->assign('template', $this->config->get('config_storefront_template'));
		$this->view->assign('retina', $this->config->get('config_retina_enable'));

		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->view->assign('base', HTTPS_SERVER);
		} else {
			$this->view->assign('base', HTTP_SERVER);
		}
		
		$icon_rl = $this->config->get('config_icon');
		if($icon_rl){		
			//see if we have a resource ID or path
			if (is_numeric($icon_rl)) {
				$resource = new AResource('image');
			    $image_data = $resource->getResource( $icon_rl );
			    if ( is_file(DIR_RESOURCE . $image_data['image']) ) {
			    	$icon_rl = 'resources/'.$image_data['image'];
			    } else {
			    	$icon_rl = $image_data['resource_code'];
			    }
			} else	
			if(!is_file(DIR_RESOURCE.$icon_rl)){
				$this->messages->saveWarning('Check favicon.','Warning: please check favicon in your store settings. Current path is "'.DIR_RESOURCE.$icon_rl.'" but file does not exists.');
				$icon_rl ='';
			}
		}
		$this->view->assign('icon', $icon_rl);
		$this->view->assign('lang', $this->language->get('code'));
		$this->view->assign('direction', $this->language->get('direction'));
		$this->view->assign('links', $this->document->getLinks());	
		$this->view->assign('styles', $this->document->getStyles());
		$this->view->assign('scripts', $this->document->getScripts());		
		$this->view->assign('breadcrumbs', $this->document->getBreadcrumbs());
		
		$this->view->assign('store', $this->config->get('store_name'));
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
		    $this->view->assign('ssl', 1);
        }
		$this->view->assign('cart_url', $this->html->getURL('checkout/cart'));
        $this->view->assign('cart_ajax', (int) $this->config->get('config_cart_ajax'));
        $this->view->assign('cart_ajax_url', $this->html->getURL('r/product/product/addToCart'));
        $this->view->assign('search_url', $this->html->getURL('product/search'));

        $this->view->assign('call_to_order_url', $this->html->getURL('content/contact'));

		//load template debug resources if needed
		$this->view->assign('template_debug_mode', $this->config->get('storefront_template_debug'));

		$this->processTemplate('common/head.tpl');

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