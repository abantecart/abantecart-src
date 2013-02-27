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
class ControllerBlocksCategory extends AController {
	protected $category_id = 0;
	protected $path = array();
	
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->view->assign('heading_title', $this->language->get('heading_title') );
		
		$this->loadModel('catalog/category');
		
		if (isset($this->request->get['path'])) {
			$this->path = explode('_', $this->request->get['path']);
			
			$this->category_id = end($this->path);
		}
		
		$this->view->assign('category', $this->getCategories(0) );
		// framed needs to show frames for generic block.
		//If tpl used by listing block framed was set by listing block settings
		$this->view->assign('block_framed',true);
												
		$this->processTemplate('blocks/category.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
	
	protected function getCategories($parent_id, $current_path = '') {
		$category_id = array_shift($this->path);
		
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
			
			$children = '';
			
			if ($category_id == $result['category_id']) {
				$children = $this->getCategories($result['category_id'], $new_path);
			}
			$cname = $result['name'];
			if ($this->category_id == $result['category_id']) {
				$cname = '<b>' . $cname . '</b>';
			}
			$stdout .= '<a href="' . $this->html->getSEOURL('product/category', '&path=' . $new_path, '&encode')  . '">'.$cname.'</a>';
			
        	$stdout .= $children;
        
        	$stdout .= '</li>'; 
		}
 
		if ($results) {
			$stdout .= '</ul>';
		}
		
		return $stdout;
	}		
}
?>