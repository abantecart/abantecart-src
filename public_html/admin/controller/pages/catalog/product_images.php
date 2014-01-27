<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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
class ControllerPagesCatalogProductImages extends AController {
	private $error = array(); 
	public $data = array();
     
  	public function main() {

          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('catalog/product');
    	$this->document->setTitle( $this->language->get('heading_title') );
		$this->loadModel('catalog/product');
		$this->loadModel('tool/image');

		if (isset($this->request->get['product_id'])) {
      		$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			if ( !$product_info ) {
				$this->session->data['warning'] = $this->language->get('error_product_not_found');
				$this->redirect($this->html->getSecureURL('catalog/product'));
			}
    	}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
			$this->model_catalog_product->updateProductImages($this->request->get['product_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('catalog/product_images', '&product_id=' . $this->request->get['product_id'] ));
		}

		$this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);

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
			'text'      => $this->language->get('text_edit') .'&nbsp;'. $this->language->get('text_product') . ' - '. $this->data['product_description'][$this->session->data['content_language_id']]['name'],
			'separator' => ' :: '
		 ));
		 $this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('catalog/product_images', '&product_id=' . $this->request->get['product_id'] ),
			'text'      => $this->language->get('tab_media'),
			'separator' => ' :: '
		 ));

		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

		$this->data['active'] = 'images';
		//load tabs controller
		$tabs_obj = $this->dispatch('pages/catalog/product_tabs', array( $this->data ) );
		$this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$this->data['button_add_image'] = $this->html->buildButton(array(
			'text' => $this->language->get('button_add_image'),
			'style' => 'button1',
		));

		$this->data['action'] = $this->html->getSecureURL('catalog/product_images', '&product_id=' . $this->request->get['product_id'] );
		$this->data['form_title'] = $this->language->get('text_edit')  .'&nbsp;'. $this->language->get('text_product');
		$this->data['update'] = '';
		$form = new AForm('HS');

		$form->setForm(array(
		    'form_name' => 'productFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'productFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'productFrm',
		    'action' => $this->data['action'],
	    ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_save'),
		    'style' => 'button1',
	    ));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'cancel',
		    'text' => $this->language->get('button_cancel'),
		    'style' => 'button2',
	    ));

		$this->view->batchAssign( $this->data );
		$this->view->assign('help_url', $this->gen_help_url('product_media') );
		$this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');

		$this->addChild('responses/common/resource_library/get_resources_html', 'resources_html', 'responses/common/resource_library_scripts.tpl');
        $resources_scripts = $this->dispatch(
            'responses/common/resource_library/get_resources_scripts',
            array(
                'object_name' => 'products',
                'object_id' => $this->request->get['product_id'],
	            'types' => array('image','audio','video','pdf')
            )
        );
		$this->view->assign('resources_scripts', $resources_scripts->dispatchGetOutput());

		$this->processTemplate('pages/catalog/product_images.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}