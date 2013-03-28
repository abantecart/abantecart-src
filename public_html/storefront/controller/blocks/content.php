<?php  
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2013 Belavier Commerce LLC

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
class ControllerBlocksContent extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
        
		$this->data = array();
		$this->data['heading_title'] =  $this->language->get('heading_title');        
		$this->data['text_home'] =  $this->language->get('text_home');
		$this->loadLanguage('common/header');
		// build static links
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

		$this->view->batchAssign($this->data);

		//build dynamic content (pages) links
		$this->loadModel('catalog/content');

		$contents = $this->_buildTree($this->model_catalog_content->getContents(),0,0);

        $this->view->assign('contents', $contents );
        $this->view->assign('contact', $this->html->getURL('content/contact') );
        $this->view->assign('sitemap', $this->html->getURL('content/sitemap') );

		$this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		
	}
	private function _buildTree($array=array(), $parent_id=0, $level=0){
		$output = array();
		$array = !is_array($array) ? array() : $array;
		foreach($array as $content){
			if($parent_id==$content['parent_content_id']){
				$content['title'] = str_repeat('&nbsp;&nbsp;',$level).$content['title'];
				$content['href']  = $this->html->getSEOURL('content/content', '&content_id=' . $content['content_id'], '&encode');
				$child = $this->_buildTree($array,$content['content_id'],($level+1));
				if($child){
					$content['children'] = $child;
				}

				// prevent rewriting if two items have the same sort_order
				while(isset($output[$content['sort_order']])){
					$content['sort_order']++;
				}
				$output[$content['sort_order']] = $content;
			}
		}
		//resort by sort_order
		ksort($output);
		$tmp = $output;
		$output = array();
		foreach($tmp as $list){
			$output[] = array('title'=>$list['title'],
							  'href' => $list['href']);
			if(isset($list['children'])){
				foreach($list['children'] as $child){
					$output[] = array('title'=>$child['title'],
									  'href' => $child['href']);
				}
			}
		}

	return $output;
	}
}
?>