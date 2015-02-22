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
		
		$this->view->assign('categories_html', $this->_buildCategoriesTree(0));
        $this->view->assign('special', $this->html->getSEOURL('product/special'));
        $this->view->assign('account', $this->html->getSEOURL('account/account'));
        $this->view->assign('edit',    $this->html->getSEOURL('account/edit'));
        $this->view->assign('password',$this->html->getSEOURL('account/password'));
        $this->view->assign('address', $this->html->getSEOURL('account/address'));
        $this->view->assign('history', $this->html->getSEOURL('account/history'));
        $this->view->assign('download',$this->html->getSEOURL('account/download'));
        $this->view->assign('checkout',$this->html->getSEOURL('checkout/shipping'));

        $this->view->assign('cart',    $this->html->getSecureURL('checkout/cart'));
        $this->view->assign('search',  $this->html->getURL('product/search'));
        $this->view->assign('contact', $this->html->getSecureURL('content/contact'));

		$this->loadModel('catalog/content');
    	$content_pages = $this->model_catalog_content->getContents();
        $this->view->assign('contents', $this->_buildContentTree($content_pages));
		
		$this->processTemplate('pages/content/sitemap.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
	
	protected function _buildCategoriesTree($parent_id, $current_path = '') {
		$output = '';
		
		$results = $this->model_catalog_category->getCategories($parent_id);
		
		if ($results) {
			$output .= '<ul class="list-group">';
    	}
		
		foreach ($results as $result) {	
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}
			
			$output .= '<li class="list-group-item"><a href="' . $this->html->getSEOURL('product/category', '&path=' . $new_path, true)  . '">' . $result['name'] . '</a>';
        	$output .= $this->_buildCategoriesTree($result['category_id'], $new_path);
        	$output .= '</li>';
		}
		if ($results) {
			$output .= '</ul>';
		}
		return $output;
	}

	protected function _buildContentTree($contents, $parent_id=0, $deep=0){
		$output = array();
		if(!is_array($contents)){
			return $output;
		}

		foreach($contents as $content){
			if($content['parent_content_id'] != $parent_id){
				continue;
			}

			$output[] = array(
								'title' => str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;',$deep).$content['title'],
								'href'  => $this->html->getSEOURL('content/content', '&content_id=' . $content['content_id'],true)
							);
			$output = array_merge($output, $this->_buildContentTree($contents,$content['content_id'],($deep+1)));
		}
		return $output;
	}

}
