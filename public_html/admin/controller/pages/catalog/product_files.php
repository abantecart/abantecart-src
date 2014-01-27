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
		$this->loadModel('catalog/download');
		$product_id = $this->request->get['product_id'];

		if (has_value($product_id) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			if (!$product_info) {
				$this->session->data['warning'] = $this->language->get('error_product_not_found');
				$this->redirect($this->html->getSecureURL('catalog/product'));
			}
			// remove
			if(has_value($this->request->get['act'])){
				if($this->request->get['act']=='unmap'){
					$this->model_catalog_download->unmapDownload($this->request->get['download_id'], $product_id);
				}elseif($this->request->get['act']=='delete'){
					$this->model_catalog_download->deleteDownload($this->request->get['download_id']);
				}
				$this->session->data['success'] = $this->language->get('text_success_remove');
				$this->redirect($this->html->getSecureURL('catalog/product_files', '&product_id='.$product_id));
			}

		}

		//Downloads disabled. Warn user
		if (!$this->config->get('config_download')) {
			$this->error['warning'] = $this->html->convertLinks($this->language->get('error_downloads_disabled'));
		}

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->_validateForm()) {
			foreach($this->request->post['selected'] as $id=>$value){
				if($value['status']){
					$this->model_catalog_download->mapDownload($id,$product_id);
				}
			}

			$this->session->data['success'] = $this->language->get('text_map_success');
			$this->redirect($this->html->getSecureURL('catalog/product_files', '&product_id=' . $product_id));
		}


		$this->view->assign('download_id', $this->request->get['download_id']);

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
		$tabs_obj = $this->dispatch('pages/catalog/product_tabs', array($this->data));
		$this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$this->loadModel('catalog/download');
		$this->data['downloads'] = array();

		$prod_files = $this->model_catalog_download->getProductDownloadsDetails($product_id);
		$prod_files[] = array( 'download_id' => 0 );

		$this->session->data['multivalue_excludes'] = array(); // array for excluding assigned downloads from multivalue list of create form

		foreach ($prod_files as $download_info) {
			$download_info['product_id'] = $product_id;
			if($download_info['download_id']){
				$this->session->data['multivalue_excludes'][] = $download_info['download_id'];
				$download_info['map_list'] = $this->model_catalog_download->getDownloadMapList($download_info['download_id']);
			}

			$row = $this->dispatch('responses/product/product/buildDownloadForm',array($download_info,'responses/product/product_file_row.tpl'));
			$this->data['file_rows'][] = $row->dispatchGetOutput();
			unset($row);
		}


		$this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');
		$this->view->assign('help_url', $this->gen_help_url('product_files'));
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/catalog/product_files.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _validateForm() {
		if (!$this->user->canModify('catalog/product_files')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		$this->load->library('json');
		$this->request->post[ 'selected' ] = AJson::decode(html_entity_decode(current($this->request->post[ 'selected' ])), true);

		if(!$this->request->post['selected']){
			$this->error['warning'] = $this->language->get('error_selected_downloads');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}