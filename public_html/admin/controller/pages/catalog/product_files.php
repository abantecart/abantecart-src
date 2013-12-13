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
		if (!$this->config->get('config_download')) {
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
		$tabs_obj = $this->dispatch('pages/catalog/product_tabs', array($this->data));
		$this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$this->loadModel('catalog/download');
		$this->data['downloads'] = array();
		/*$results = $this->model_catalog_download->getDownloads();
		foreach ($results as $r) {
			$this->data['downloads'][$r['download_id']] = $r['name'];
		}
		*/

		$this->loadModel('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$prod_files = $this->model_catalog_product->getProductDownloadsDetails($product_id);

		$count = 1;
		foreach ($prod_files as $file) {
			$file_form = $this->_build_file_row_data($file);
			$file_form['row_id'] = 'row' . $count;
			$this->view->batchAssign($file_form);
			$this->data['file_rows'][] = $this->view->fetch('responses/product/product_file_row.tpl');
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


	private function _build_file_row_data($file_data) {
		$file_form = array();
		$file_form['date_added'] = dateISO2Display($file_data['date_added'], $this->language->get('date_format_short').' '.$this->language->get('time_format'));
		$file_form['date_modified'] = dateISO2Display($file_data['date_modified'], $this->language->get('date_format_short').' '.$this->language->get('time_format'));

		$file_form['action'] = $this->html->getSecureURL('catalog/product_files', '&product_id=' . $product_id);
		$file_form['form_title'] = $this->language->get('text_edit') . '&nbsp;' . $this->language->get('text_product');
		$file_form['update'] = $this->html->getSecureURL('listing_grid/product/update_files_field', '&id=' . $product_id);
		$form = new AForm('HT');
		$form->setForm(array(
					'form_name' => 'fileFrm'.$file_data['download_id'],
					'update' => $file_form['update'],
				));
		$file_form['form']['form_open'] = $form->getFieldHtml(array(
					'type' => 'form',
					'name' => 'fileFrm'.$file_data['download_id'],
					'attr' => 'confirm-exit="true"',
					'action' => $file_form['action']
				));
		$file_form[ 'form' ][ 'submit' ] = $form->getFieldHtml(array(
																	 'type' => 'button',
																	 'name' => 'submit',
																	 'text' => $this->language->get('button_save'),
																	 'style' => 'button1',
																));
		$file_form[ 'form' ][ 'cancel' ] = $form->getFieldHtml(array(
																	 'type' => 'button',
																	 'name' => 'cancel',
																	 'href' => $file_form['action'],
																	 'text' => $this->language->get('button_cancel'),
																	 'style' => 'button2',
																));








		$file_form['thumbnail'] = $this->getFileThumb(DIR_ROOT.'/'.$file_data['filename'], 150, 150);
		$file_form['file_id'] = $file_data['download_id'];

		$file_form['form']['fields']['status'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'status',
					'value' => $file_data['status'],
					'style' => 'btn_switch',
				));
		$file_form['form']['fields']['download_id'] = $form->getFieldHtml(array(
			'type' => 'hidden',
			'name' => 'download_id',
			'value' => $file_data['download_id'],
		));
		$file_form['form']['fields']['name'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'name',
			'value' => $file_data['name'],
		));
		$file_form['form']['fields']['max_downloads'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'max_downloads',
			'value' => $file_data['max_downloads'],
			'style' => 'small-field'
		));
		$file_form['form']['fields']['activate'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'activate',
			'value' => $file_data['activate'],
			'options' => array( '' => $this->language->get('text_select'),
								'before_order' => $this->language->get('text_before_order'),
								'immediately' => $this->language->get('text_immediately'),
								'order_status' => $this->language->get('text_on_order_status'),
								'manually' => $this->language->get('text_manually'), ),
			'required' => true
		));

		$options = array('' => $this->language->get('text_select'));
		foreach($this->data['order_statuses'] as $order_status){
			$options[$order_status['order_status_id']] = $order_status['name'];
		}

		$file_form['form']['fields']['order_statuses'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'order_status',
			'value' => $file_data['order_status'],
			'options' => $options,
			'required' => true
		));

		$file_form['form']['fields']['sort_order'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'sort_order',
			'style' => 'small-field',
			'value' => $file_data['sort_order'],
		));
		$file_form['form']['fields']['expired_days'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'expired_days',
			'style' => 'small-field',
			'value' => $file_data['expired_days'],
		));


		$attr_mngr = new AAttribute_Manager('download_attribute');
		$attribute_info = $attr_mngr->getAttributeTypeInfo('download_attribute');

		$attributes = $attr_mngr->getAttributes(array('attribute_type_id'=>$attribute_info['attribute_type_id']));

		$elements = HtmlElementFactory::getAvailableElements();



		$html_multivalue_elements = HtmlElementFactory::getMultivalueElements();
		$html_elements_with_options = HtmlElementFactory::getElementsWithOptions();

		foreach ($attributes as $attribute) {
			$html_type = $elements[$attribute['element_type']]['type'];
			if (!$html_type || !$attribute['status']) {
				continue;
			}
			$values = $value = array();
			$attribute['values'] = $attr_mngr->getAttributeValues($attribute['attribute_id']);
			//values that was setted
			if (in_array($attribute['element_type'], $html_elements_with_options)) {

				if (isset($this->request->post['attributes'][$attribute['attribute_id']])) {
					$value = $this->request->post['attributes'][$attribute['attribute_id']];
					$value = $html_type == 'radio' ? current($value) : $value;
				} else {
					foreach ($attribute['selected_values'] as $val) {
						$value[$val['attribute_value_id']] = $val['attribute_value'];
					}
				}
			} else {
				if (isset($this->request->post['attributes'][$attribute['attribute_id']])) {
					$value = $this->request->post['attributes'][$attribute['attribute_id']];
				} else {
					$value = $attribute['values'][0]['value'];
				}
			}
			// possible values
			foreach ($attribute['values'] as $val) {
				$values[$val['attribute_value_id']] = $val['value'];
			}

			if (!in_array($attribute['element_type'], $html_multivalue_elements)) {
				$option_name = 'attributes['.$file_form['file_id'].'][' . $attribute['attribute_id'] . ']';
			} else {
				$option_name = 'attributes['.$file_form['file_id'].'][' . $attribute['attribute_id'] . '][' . $attribute['attribute_value_id'] . ']';
			}

			$disabled = '';
			$required = $attribute['required'];

			$option_data = array(
				'type' => $html_type,
				'name' => $option_name,
				'value' => $value,
				'options' => $values,
				'required' => $required,
				'attr' => $disabled,
				'style' => 'large-field'
			);

			if ($html_type == 'checkbox') {
				$option_data['label_text'] = $value;
			} elseif ($html_type == 'checkboxgroup') {
				$option_data['scrollbox'] = true;
			}

			$file_form['entry_attribute_' .$file_form['file_id'].'_'. $attribute['attribute_id']] = $attribute['name'];
			$file_form['attributes'][$file_form['file_id'].'_' . $attribute['attribute_id']] = $form->getFieldHtml($option_data);
		}

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


	public function getFileThumb($path, $width = '', $height = '') {

		//$resource = $this->getResource($path, $language_id);

		switch ($this->type) {
			case 'image' :
				if (!$resource['default_icon']) {
					$resource['default_icon'] = 'no_image.jpg';
				}
				break;
			default :
				if (!$resource['default_icon']) {
					$resource['default_icon'] = 'no_image.jpg';
				}
				$this->load->model('tool/image');
				$this->model_tool_image->resize($resource['default_icon'], $width, $height);
				return $this->model_tool_image->resize($resource['default_icon'], $width, $height);
		}

		if (!empty($resource['resource_code'])) {
			return $resource['resource_code'];
		}

		$old_image = DIR_RESOURCE . $this->type_dir . $resource['resource_path'];
		$info = pathinfo($old_image);
		$extension = $info['extension'];

		if ($extension != 'ico') {
			if (!is_file($old_image)) {
				$this->load->model('tool/image');
				$this->model_tool_image->resize($resource['default_icon'], $width, $height);
				return $this->model_tool_image->resize($resource['default_icon'], $width, $height);
			}

			$name = preg_replace('/[^a-zA-Z0-9]/', '', $resource['name']);
//Build thumbnails path similar to resource library path
			$new_image = 'thumbnails/' . dirname($resource['resource_path']) . '/' . $name . '-' . $resource['resource_id'] . '-' . $width . 'x' . $height . '.' . $extension;

			if (!file_exists(DIR_IMAGE . $new_image) || (filemtime($old_image) > filemtime(DIR_IMAGE . $new_image))) {
				$path = '';

				$directories = explode('/', dirname(str_replace('../', '', $new_image)));

				foreach ($directories as $directory) {
					$path = $path . '/' . $directory;

					if (!file_exists(DIR_IMAGE . $path)) {
						@mkdir(DIR_IMAGE . $path, 0777);
						chmod(DIR_IMAGE . $path, 0777);
					}
				}

				$image = new AImage($old_image);
				$image->resize($width, $height);
				$image->save(DIR_IMAGE . $new_image);
				unset($image);
			}

			if (HTTPS === true) {
				return HTTPS_IMAGE . $new_image;
			} else {
				return HTTP_IMAGE . $new_image;
			}

		} else { // returns ico-file as is
			if (HTTPS === true) {
				return HTTPS_DIR_RESOURCE . $this->type_dir . $resource['resource_path'];
			} else {
				return HTTP_DIR_RESOURCE . $this->type_dir . $resource['resource_path'];
			}
		}
	}

}