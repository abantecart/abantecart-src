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
class ControllerPagesContentSitemap extends AController {
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
        	'href'      => $this->html->getURL('content/sitemap'),
        	'text'      => $this->language->get('heading_title'),
        	'separator' => $this->language->get('text_separator')
      	 ));	
		
		$this->loadModel('catalog/category');
		
		$this->loadModel('tool/seo_url');
		
		$this->view->assign('category', $this->getCategories(0));

        $this->view->assign('special', $this->html->getSecureURL('product/special'));
        $this->view->assign('account', $this->html->getSecureURL('account/account'));
        $this->view->assign('edit', $this->html->getSecureURL('account/edit'));
        $this->view->assign('password', $this->html->getSecureURL('account/password'));
        $this->view->assign('address', $this->html->getSecureURL('account/address'));
        $this->view->assign('history', $this->html->getSecureURL('account/history'));
        $this->view->assign('download', $this->html->getSecureURL('account/download'));
        $this->view->assign('checkout', $this->html->getSecureURL('checkout/shipping'));

        $this->view->assign('cart', $this->html->getURL('checkout/cart'));
        $this->view->assign('search', $this->html->getURL('product/search'));
        $this->view->assign('contact', $this->html->getURL('content/contact'));

		$this->loadModel('catalog/content');
		
		$contents = array();
    	$content_pages = $this->model_catalog_content->getContents();

		if($content_pages){
			foreach ($content_pages as $result) {
				$contents[] = array(
					'title' => $result['title'],
					'href'  => $this->html->getSEOURL('content/content', '&content_id=' . $result['content_id'])
				);
			}
		}
        $this->view->assign('contents', $contents);
		
		$this->processTemplate('pages/content/sitemap.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
	
	protected function getCategories($parent_id, $current_path = '') {
		$stdout = '';
		
		$results = $this->model_catalog_category->getCategories($parent_id);
		
		if ($results) {
			$stdout .= '<ul>';
    	}
		
		foreach ($results as $result) {	
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}
			
			$stdout .= '<li>';
			
			$stdout .= '<a href="' . $this->html->getSEOURL('product/category', '&path=' . $new_path)  . '">' . $result['name'] . '</a>';
			
        	$stdout .= $this->getCategories($result['category_id'], $new_path);
        
        	$stdout .= '</li>'; 
		}
 
		if ($results) {
			$stdout .= '</ul>';
		}
		
		return $stdout;
	}	
}
?>