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
class ControllerPagesCatalogProductTabs extends AController {
	private $error = array();
	public $data = array();
     
  	public function main() {

        //Load input argumets for gid settings
        $this->data = func_get_arg(0);
        if (!is_array($this->data)) {
            throw new AException (AC_ERR_LOAD, 'Error: Could not create grid. Grid definition is not array.');
        }
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('catalog/product');
		$product_id = $this->request->get['product_id'];
		$product_id = !$product_id && $this->data['product_id'] ? $this->data['product_id'] : $product_id;

        $this->data['link_general'] = $this->html->getSecureURL('catalog/product/update', '&product_id=' . $product_id );
        $this->data['link_images'] = $this->html->getSecureURL('catalog/product_images', '&product_id=' . $product_id );
        $this->data['link_options'] = $this->html->getSecureURL('catalog/product_options', '&product_id=' . $product_id );
        $this->data['link_files'] = $this->html->getSecureURL('catalog/product_files', '&product_id=' . $product_id );
        $this->data['link_relations'] = $this->html->getSecureURL('catalog/product_relations', '&product_id=' . $product_id );
        $this->data['link_promotions'] = $this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $product_id );
        $this->data['link_extensions'] = $this->html->getSecureURL('catalog/product_extensions', '&product_id=' . $product_id );
        $this->data['link_layout'] = $this->html->getSecureURL('catalog/product_layout', '&product_id=' . $product_id );

		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/catalog/product_tabs.tpl');

		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
}

