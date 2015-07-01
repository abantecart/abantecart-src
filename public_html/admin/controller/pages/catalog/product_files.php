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

		if(!$product_id){
			$this->redirect($this->html->getSecureURL('catalog/product'));
		}

		if (has_value($product_id) && $this->request->is_GET()) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			if (!$product_info) {
				$this->session->data['warning'] = $this->language->get('error_product_not_found');
				$this->redirect($this->html->getSecureURL('catalog/product'));
			}
			// remove
			if(has_value($this->request->get['act']) && $this->request->get['act']=='delete'){
				$download_info = $this->model_catalog_download->getDownload( $this->request->get['download_id'] );
				$map_list = $this->model_catalog_download->getDownloadMapList($this->request->get['download_id']);

				if( (sizeof($map_list)==1 && key($map_list) == $product_id) || $download_info['shared']!=1 ){
					$this->model_catalog_download->deleteDownload($this->request->get['download_id']);
				}else{
					$this->model_catalog_download->unmapDownload($this->request->get['download_id'], $product_id);
				}
				$this->session->data['success'] = $this->language->get('text_success_remove');
				$this->redirect($this->html->getSecureURL('catalog/product_files', '&product_id='.$product_id));
			}

		}

		//Downloads disabled. Warn user
		if (!$this->config->get('config_download')) {
			$this->error['warning'] = $this->html->convertLinks($this->language->get('error_downloads_disabled'));
		}

		if ($this->request->is_POST() && $this->_validateForm()) {

			foreach($this->request->post['selected'] as $id){
					$this->model_catalog_download->mapDownload($id,$product_id);
			}

			$this->session->data['success'] = $this->language->get('text_map_success');
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
			'separator' => ' :: ',
			'current'	=> true
		));

		$this->data['active'] = 'files';
		//load tabs controller
		$tabs_obj = $this->dispatch('pages/catalog/product_tabs', array($this->data));
		$this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$this->loadModel('catalog/download');
		$this->data['downloads'] = array();

		$this->data['product_files'] = $this->model_catalog_download->getProductDownloadsDetails($product_id);

		$rl = new AResource('download');
		$rl_dir = $rl->getTypeDir();
		foreach($this->data['product_files'] as &$file){

			$resource_id = $rl->getIdFromHexPath(str_replace($rl_dir,'',$file['filename']));
			$resource_info = $rl->getResource($resource_id);
			$thumbnail = $rl->getResourceThumb($resource_id, $this->config->get('config_image_grid_width'), $this->config->get('config_image_grid_height'));
			if($resource_info['resource_path']){
				$file[ 'icon' ] = $this->html->buildResourceImage(
															array('url' => $thumbnail,
																'width' => $this->config->get('config_image_grid_width'),
																'height' => $this->config->get('config_image_grid_height'),
																'attr' => 'alt="' . $resource_info['title'] . '"') );
			}else{
				$file[ 'icon' ] = $resource_info['resource_code'];
			}

			$file['status'] = $file['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled');

			$file['button_edit'] = $this->html->buildElement(
					array(
							'type' => 'button',
							'text' => $this->language->get('button_edit'),
							'href' => $this->html->getSecureURL('r/product/product/buildDownloadForm','&product_id=' . $product_id.'&download_id='.$file['download_id'])
			));

			$map_list = $this->model_catalog_download->getDownloadMapList($file['download_id']);
			if( (sizeof($map_list)==1 && key($map_list) == $product_id) || $file['shared']!=1 ){
				$text = $this->language->get('button_delete');
				$icon = 'fa-trash-o';
			}else{
				$text = $this->language->get('button_unmap');
				$icon = 'fa-chain-broken';
			}

			$file['button_delete'] = $this->html->buildElement(
					array(
							'type' => 'button',
							'text' => $text,
							'href' => $this->html->getSecureURL('catalog/product_files','&act=delete&product_id=' . $product_id.'&download_id='.$file['download_id']),
						    'icon' => $icon
			));

			$orders_count = $this->model_catalog_download->getTotalOrdersWithProduct($product_id);
			if($orders_count){
				$file['push_to_customers'] = $this->html->buildElement(
						array(
							'type' => 'button',
							'name' => 'push_to_customers',
							'text' => sprintf($this->language->get('text_push_to_orders'),	$orders_count),
							'title' => $this->language->get('text_push'),
							'icon' => 'fa-share-alt-square',
							'href' => $this->html->getSecureURL('catalog/product_files/pushToCustomers',
																'&product_id='.$product_id.'&download_id='.$file['download_id']),
							'attr' => 'data-orders-count="'.$orders_count.'"'));
			}


		} unset($file);

		$this->data['button_add_file'] = $this->html->buildElement(
			array(
				'type' => 'button',
				'text' => $this->language->get('text_add_file'),
				'href' => $this->html->getSecureURL('r/product/product/buildDownloadForm','&product_id=' . $product_id)

			)
		);
		if($this->config->get('config_embed_status')){
			$this->data['embed_url'] = $this->html->getSecureURL('common/do_embed/product', '&product_id=' . $product_id);
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

		if(!$this->request->post['selected']){
			$this->error['warning'] = $this->language->get('error_selected_downloads');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	public function pushToCustomers(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$download_id = (int)$this->request->get['download_id'];
		$product_id = (int)$this->request->get['product_id'];

		$download_info = $this->download->getDownloadInfo($download_id);

		if(!$download_info || !$product_id){
			$this->redirect($this->html->getSecureURL('catalog/product_files', '&product_id=' . $product_id));
		}

		$download_info['attributes_data'] = serialize($this->download->getDownloadAttributesValues($download_id));
		$this->loadModel('catalog/download');
		$orders_for_push = $this->model_catalog_download->getOrdersWithProduct($product_id);
		$updated_array = array();
		if($orders_for_push){
			foreach($orders_for_push as $row){
				$updated_array = array_merge(
						$updated_array,
						$this->download->addUpdateOrderDownload($row['order_product_id'],$row['order_id'], $download_info)
						);
			}

			$this->loadLanguage('catalog/files');
			$this->session->data['success'] = sprintf($this->language->get('success_push_to_orders'), count($updated_array));
		}
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->redirect( $this->html->getSecureURL('catalog/product_files','&product_id='.$product_id) );

	}

}