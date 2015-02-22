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
class ControllerPagesCatalogProductExtensions extends AController {
	private $error = array();
	public $data = array();
     
  	public function main() {

          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('catalog/product');
    	$this->document->setTitle( $this->language->get('heading_title') );
		$this->loadModel('catalog/product');

		if (isset($this->request->get['product_id']) && $this->request->is_GET()) {
      		$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			if ( !$product_info ) {
				$this->session->data['warning'] = $this->language->get('error_product_not_found');
				$this->redirect($this->html->getSecureURL('catalog/product'));
			}
    	}
		  
		$this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
		$this->data['heading_title'] = $this->language->get('text_edit')  .'&nbsp;'. $this->language->get('text_product');

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}
		
    	$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('catalog/product'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));
		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $this->request->get['product_id'] ),
			'text'      => $this->language->get('text_edit')  .'&nbsp;'. $this->language->get('text_product') . ' - '. $this->data['product_description'][$this->session->data['content_language_id']]['name'],
			'separator' => ' :: '
		 ));
		 $this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('catalog/product_extensions', '&product_id=' . $this->request->get['product_id'] ),
			'text'      => $this->language->get('tab_extensions'),
			'separator' => ' :: '
		 ));

		$this->data['active'] = 'extensions';
		//load tabs controller
		$tabs_obj = $this->dispatch('pages/catalog/product_tabs', array( $this->data ) );
		$this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

        $this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');

		$this->view->assign('help_url', $this->gen_help_url('product_extensions') );
        $this->view->batchAssign( $this->data );
		$this->processTemplate('pages/catalog/product_extensions.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}