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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesCatalogCategoryTabs extends AController {

	public $data = array();
     
  	public function main() {

        //Load input argumets for gid settings
        $this->data = func_get_arg(0);
        if (!is_array($this->data)) {
            throw new AException (AC_ERR_LOAD, 'Error: Could not create grid. Grid definition is not array.');
        }
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('catalog/category');

		$this->data['groups'] = array('general', 'data' );

		foreach ($this->data['groups'] as $group) {
			$this->data['link_' . $group] = $this->html->getSecureURL('catalog/category/'.($this->data['category_id'] ? 'update' : 'insert'),
																	 ($this->data['category_id'] ? '&category_id='.$this->data['category_id'] : '')). '#'.$group;
		}

		if($this->data['category_id']){
			$this->data['groups'][] = 'layout';
			$this->data['link_layout'] = $this->html->getSecureURL('catalog/category/edit_layout', '&category_id='.$this->data['category_id']);
		}

		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/catalog/category_tabs.tpl');

		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
}

