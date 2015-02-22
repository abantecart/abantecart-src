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
class ControllerCommonHeader extends AController {
	public $data = array();
	public function main() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->data['title'] = $this->document->getTitle();
        $this->data['template'] = $this->config->get('config_storefront_template');
		$this->data['breadcrumbs'] = $this->document->getBreadcrumbs();
		$this->data['store'] = $this->config->get('store_name');

        $this->data['logo'] = $this->config->get('config_logo');
		//see if we have a resource ID	
		if (is_numeric($this->data['logo'])) {
			$resource = new AResource('image');
		    $image_data = $resource->getResource( $this->data['logo'] );
		    if ( is_file(DIR_RESOURCE . $image_data['image']) ) {
		    	$this->data['logo'] = 'resources/'.$image_data['image'];
		    } else {
		    	$this->data['logo'] = $image_data['resource_code'];
		    }
		}
        
		$this->data['text_special'] = $this->language->get('text_special');
		$this->data['text_contact'] = $this->language->get('text_contact');
		$this->data['text_sitemap'] = $this->language->get('text_sitemap');
		$this->data['text_bookmark'] = $this->language->get('text_bookmark');
		$this->data['text_category'] = $this->language->get('text_category');
		$this->data['text_advanced'] = $this->language->get('text_advanced');
		$this->data['entry_search'] = $this->language->get('entry_search');
		
		$this->data['button_go'] = $this->language->get('button_go');

		$this->data['homepage'] = HTTPS===true ? $this->config->get('config_ssl_url') : $this->config->get('config_url');
		$this->data['special'] = $this->html->getURL('product/special');
		$this->data['contact'] = $this->html->getURL('content/contact');
    	$this->data['sitemap'] = $this->html->getURL('content/sitemap');
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['checkout'] = $this->html->getSecureURL('checkout/shipping');

		if (isset($this->request->get['category_id'])) {
			$this->data['category_id'] = $this->request->get['category_id'];
		} elseif (isset($this->request->get['path'])) {
			$path = explode('_', $this->request->get['path']);
			$this->data['category_id'] = end($path);
		} else {
			$this->data['category_id'] = '';
		}
		
		$this->data['advanced'] = $this->html->getURL('product/search');
		$this->data['action'] = $this->html->getURL('index/home');

		if (!isset($this->request->get['rt'])) {
			$this->data['redirect'] = $this->html->getURL('index/home');
		} else {
			$this->loadModel('tool/seo_url');
			
			$data = $this->request->get;
			unset($data['_route_']);
			$route = $data['rt'];
			unset($data['rt']);
			
			$url = '';
			if ($data) {
				$url = '&' . urldecode(http_build_query($data));
			}
			$this->data['redirect'] = $this->html->getSEOURL( $route,  $url, '&encode');
		}
		
		$this->data['language_code'] = $this->session->data['language'];
		
		$this->data['languages'] = array();
		$this->data['languages'] = $this->language->getActiveLanguages();	
		
		$this->data['currency_code'] = $this->currency->getCode();
		$this->loadModel('localisation/currency');
		$this->data['currencies'] = array();

		$this->data['search'] = $this->html->buildInput(
										array (
			                                    'name'=>'filter_keyword',
			                                    'value'=> (isset($this->request->get['keyword']) ? $this->request->get['keyword'] : $this->language->get('text_keyword')),
			                                    'attr'=> (!isset($this->request->get['keyword']) ? ' onclick="this.value=\'\'" ' : ''),

										));
		 
		$results = $this->model_localisation_currency->getCurrencies();	
		
		foreach ($results as $result) {
			if ($result['status']) {
   				$this->data['currencies'] = array('title' => $result['title'],
												  'code'  => $result['code']);
			}
		}
		$this->view->batchAssign($this->data);
		$this->processTemplate('common/header.tpl');
        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
