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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}
class ControllerPagesCatalogProductFiles extends AController {
    private $error = array();
    public $data = array();

    public function main() {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/files');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadModel('catalog/product');
		$product_id = $this->request->get['product_id'];		

        if (has_value($product_id) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $product_info = $this->model_catalog_product->getProduct($product_id);
            if (!$product_info) {
                $this->session->data['warning'] = $this->language->get('error_product_not_found');
                $this->redirect($this->html->getSecureURL('catalog/product'));
            }
        }

		//Downloads disabled. Warn user
		if(!$this->config->get('config_download')){
			$this->error['warning'] = $this->html->convertLinks($this->language->get('error_downloads_disabled'));
		}

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->_validateForm()) {
			
			//

            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->html->getSecureURL('catalog/product_files', '&product_id=' . $product_id));
        }

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($product_id);
        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('catalog/product'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $product_id),
            'text' => $this->language->get('text_edit') . '&nbsp;' . $this->language->get('text_product') . ' - ' . $this->data['product_description'][$this->session->data['content_language_id']]['name'],
            'separator' => ' :: '
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('catalog/product_files', '&product_id=' . $product_id),
            'text' => $this->language->get('tab_files'),
            'separator' => ' :: '
        ));

        $this->data['active'] = 'files';
		//load tabs controller
		$tabs_obj = $this->dispatch('pages/catalog/product_tabs', array( $this->data ) );
		$this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

        $this->loadModel('catalog/download');
        $this->data['downloads'] = array();
        /*$results = $this->model_catalog_download->getDownloads();
        foreach ($results as $r) {
            $this->data['downloads'][$r['download_id']] = $r['name'];
        }
		*/
		
        $this->data['action'] = $this->html->getSecureURL('catalog/product_files', '&product_id=' . $product_id);
        $this->data['form_title'] = $this->language->get('text_edit') . '&nbsp;' . $this->language->get('text_product');
        $this->data['update'] = $this->html->getSecureURL('listing_grid/product/update_files_field', '&id=' . $product_id);
        $form = new AForm('HS');

        $prod_files = $this->model_catalog_product->getProductDownloadsDetails($product_id);

		$this->data['product_id'] = $form->getFieldHtml(array(
            'type' => 'hidden',
		    'name' => 'product_id',
            'value' => $product_id,
		));		

		$count = 1;
		foreach( $prod_files as $file) {
			$file_form = array();
			$file_form = $this->_build_file_row_data($file, $form);
			$file_form['row_id'] = 'row'.$count;
			$this->view->batchAssign( $file_form );
 			$this->data['file_rows'][] = $this->view->fetch('pages/catalog/product_file_row.tpl');	
 			unset($file_form);	
 			$count++;
		}
		//empty row for new


        $this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');
        $this->view->assign('help_url', $this->gen_help_url('product_files'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/catalog/product_files.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }


	private function _build_file_row_data ($file_data, $form) {

		$file_form = array();

		$file_form['download_id'] = $form->getFieldHtml(array(
            'type' => 'hidden',
		    'name' => 'download_id',
            'value' => $file_data['download_id'],
		));		
		$file_form['name'] = $form->getFieldHtml(array(
            'type' => 'input',
		    'name' => 'name',
            'value' => $file_data['name'],
		));
        $file_form['status'] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'status',
            'value' => $file_data['status'], //??????
            'style' => 'btn_switch',
        ));
        $file_form['sort_order'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'sort_order',
            'style' => 'small-field',
            'value' => $file_data['sort_order'], //??????
        ));

		// 

		return $file_form;
	}

	private function _validateForm() {
		if (!$this->user->canModify('catalog/product_files')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}