<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
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
/** @noinspection PhpUndefinedClassInspection */

/**
 * Class ControllerResponsesFormsManagerFields
 * @property ModelToolFormsManager $model_tool_forms_manager
 */
class ControllerResponsesFormsManagerFields extends AController {

	public $data = array();
	public $error = array();

	public function get_fields_list() {

		$this->loadModel('tool/forms_manager');

		$fields = $this->model_tool_forms_manager->getFields($this->request->get['form_id']);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($fields));
	}


	public function addField() {
		$this->loadLanguage('forms_manager/forms_manager');
		$this->loadModel('tool/forms_manager');

		if (!$this->_validateFieldForm($this->request->post) || !$this->request->get['form_id']) {
			$error = new AError('');
			return $error->toJSONResponse('VALIDATION_ERROR_406',
					array('error_text' => $this->error));
		}

		$this->model_tool_forms_manager->addField($this->request->get['form_id'], $this->request->post);
		$this->response->setOutput('');
	}

	public function updateField() {
		$this->loadLanguage('forms_manager/forms_manager');
		$this->loadModel('tool/forms_manager');

		if (!$this->_validateFieldForm($this->request->post) || !$this->request->get['form_id']) {
			$error = new AError('');
			return $error->toJSONResponse('VALIDATION_ERROR_406',
					array('error_text' => $this->error));
		}

		$data = $this->request->post;
		$data['form_id'] = $this->request->get['form_id'];

		$this->model_tool_forms_manager->updateFormFieldData($data);
		$this->response->setOutput('');
	}

	private function _validateFieldForm($data) {
		if (!$this->user->hasPermission('modify', 'forms_manager/fields')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$data['field_name'] = preg_replace('/[^a-zA-Z0-9\._]/', '', $data['field_name']);

		if ((!$data['element_type'] && !$data['field_id']) || !$data['field_description'] || !$data['field_name']) {
			$this->error['error_required'] = $this->language->get('error_fill_required');
		}

		if(!($rr = $this->model_tool_forms_manager->isFieldNameUnique(
				$this->request->get['form_id'],
				$data['field_name'],
				$data['field_id']
				))
		){
			$this->error['field_name'] = sprintf($this->language->get('error_field_name_exists'),$data['field_name']) ;
		}


		$this->extensions->hk_ValidateData($this);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function remove_field() {
		$this->loadLanguage('forms_manager/forms_manager');
		$this->loadModel('tool/forms_manager');
		$this->model_tool_forms_manager->removeField($this->request->get['form_id'], $this->request->get['field_id']);
		$this->response->setOutput($this->language->get('text_field_removed'));
	}

	public function update_form() {

		$this->loadLanguage('forms_manager/forms_manager');
		$this->loadModel('tool/forms_manager');
		$post = $this->request->post;
		if ($post['controller_path'] == 'forms_manager/default_email' && trim($post['success_page']) == '') {
			$post['success_page'] = 'forms_manager/default_email/success';
		}
		$this->model_tool_forms_manager->updateForm($post);
		$this->model_tool_forms_manager->updateFormFieldData($post);
		$this->response->setOutput($this->language->get('text_success_form'));

	}

	public function update_field_values() {
		$this->loadLanguage('forms_manager/forms_manager');
		$this->loadModel('tool/forms_manager');
		$this->model_tool_forms_manager->updateFieldValues($this->request->get, (int)$this->language->getContentLanguageID());
		$this->response->setOutput($this->language->get('text_success_form'));
	}

	public function load_field() {

		$this->loadLanguage('forms_manager/forms_manager');

		$this->loadModel('tool/forms_manager');

		$this->data['error_warning'] = $this->session->data['warning'];
		if (isset($this->session->data['warning'])) {
			unset($this->session->data['warning']);
		}

		$this->view->assign('success', $this->session->data['success']);
		unset($this->session->data['success']);

		$this->data['language_id'] = $this->session->data['content_language_id'];

		$this->data['field_data'] = $this->model_tool_forms_manager->getField($this->request->get['field_id']);

		$this->data['element_types'] = HtmlElementFactory::getAvailableElements();
		$this->data['elements_with_options'] = HtmlElementFactory::getElementsWithOptions();
		$this->data['no_set_values_elements'] = array(
				'K' => 'captcha',
				'D' => 'date',
				'A' => 'IPaddress',
				'O' => 'countries',
				'Z' => 'zones'
		);

		$this->data['selectable'] = in_array($this->data['field_data']['element_type'], $this->data['elements_with_options']) ? 1 : 0;
		$this->data['field_type'] = $this->data['element_types'][$this->data['field_data']['element_type']]['type'];

		$this->data['field_name'] = $this->html->buildInput(array(
				'name' => 'field_name',
				'value' => $this->data['field_data']['field_name'],
				'required' => true
		));

		$this->data['field_description'] = $this->html->buildElement(array(
				'type' => 'input',
				'name' => 'field_description',
				'value' => $this->data['field_data']['name'],
				'required' => true
		));

		$this->data['field_note'] = $this->html->buildElement(array(
				'type' => 'input',
				'name' => 'field_note',
				'value' => $this->data['field_data']['description'],
		));

		$this->data['entry_status'] = $this->language->get('forms_manager_status');
		$this->data['status'] = $this->html->buildElement(array(
				'type' => 'checkbox',
				'name' => 'status',
				'value' => $this->data['field_data']['status'],
				'style' => 'btn_switch btn-group-xs',
		));
		$this->data['field_sort_order'] = $this->html->buildElement(array(
				'type' => 'input',
				'name' => 'sort_order',
				'value' => $this->data['field_data']['sort_order'],
				'style' => 'small-field'
		));
		$this->data['required'] = $this->html->buildElement(array(
				'type' => 'checkbox',
				'name' => 'required',
				'value' => ($this->data['field_data']['required'] == 'Y') ? 1 : 0,
				'style' => 'btn_switch btn-group-xs'
		));

		if (!in_array($this->data['field_data']['element_type'], array('U', 'K'))) {
			$this->data['field_regexp_pattern'] = $this->html->buildElement(array(
					'type' => 'input',
					'name' => 'regexp_pattern',
					'value' => $this->data['field_data']['regexp_pattern'],
					'style' => 'large-field'
			));

			$this->data['field_error_text'] = $this->html->buildElement(array(
					'type' => 'input',
					'name' => 'error_text',
					'value' => $this->data['field_data']['error_text'],
					'style' => 'large-field'
			));
		}
		if ($this->data['field_data']['element_type'] == 'U') {
			$this->data['field_settings'] = $this->_file_upload_settings_form();
		}

		$this->data['hidden_element_type'] = $this->html->buildElement(array(
				'type' => 'hidden',
				'name' => 'element_type',
				'value' => $this->data['field_data']['element_type'],
		));

		$this->data['button_remove_field'] = $this->html->buildElement(array(
				'type' => 'button',
				'href' => $this->html->getSecureURL('tool/forms_manager/removeField', '&form_id=' . $this->request->get['form_id'] . '&field_id=' . $this->request->get['field_id']),
				'text' => $this->language->get('button_remove_field')
		));
		$this->data['button_save'] = $this->html->buildElement(array(
				'type' => 'button',
				'text' => $this->language->get('button_save')
		));
		$this->data['button_reset'] = $this->html->buildElement(array(
				'type' => 'button',
				'text' => $this->language->get('button_reset')
		));

		$this->data['update_field_values'] = $this->html->getSecureURL(
				'forms_manager/fields/update_field_values',
				'&form_id=' . $this->request->get['form_id'] .
				'&field_id=' . $this->request->get['field_id']
		);

		$this->data['remove_field_link'] = $this->html->getSecureURL(
				'forms_manager/fields/remove_field',
				'&form_id=' . $this->request->get['form_id'] .
				'&field_id=' . $this->request->get['field_id']
		);

		// form of option values list
		$form = new AForm('HT');
		$form->setForm(array('form_name' => 'update_field_values'));
		$this->data['form']['id'] = 'update_field_values';
		$this->data['update_field_values_form']['open'] = $form->getFieldHtml(array(
				'type' => 'form',
				'name' => 'update_field_values',
				'action' => $this->data['update_field_values']
		));

		//form of option
		$form = new AForm('HT');
		$form->setForm(array(
				'form_name' => 'field_value_form',
		));

		$this->data['form']['id'] = 'field_value_form';
		$this->data['form']['form_open'] = $form->getFieldHtml(array(
				'type' => 'form',
				'name' => 'field_value_form',
				'action' => $this->data['update_field_values']
		));

		//Load option values rows
		$this->data['field_values'] = array();

		if (!in_array($this->data['field_data']['element_type'], array('U', 'K'))) {
			if (!empty($this->data['field_data']['values'])) {
				usort($this->data['field_data']['values'], array('self', '_sort_by_sort_order'));

				foreach ($this->data['field_data']['values'] as $key => $item) {
					$item['id'] = $key;
					$this->data['field_values'][$key]['row'] = $this->_field_value_form($item, $form);
				}
			} else {
				$this->data['field_values']['new']['row'] = $this->_field_value_form(array(), $form);
			}
		}

		$this->data['new_field_row'] = '';
		if (in_array($this->data['field_data']['element_type'], $this->data['elements_with_options'])
				|| $this->data['empty_values']
				&& !in_array($this->data['field_type'], $this->data['no_set_values_elements'])
		) {

			$this->data['new_value_row'] = $this->_field_value_form(array(), $form);
		}

		$this->data['new_value_row'] = $this->_field_value_form(array(), $form);

		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/forms_manager/field_values.tpl');

	}

	private function _sort_by_sort_order($a, $b) {

		if ($a['sort_order'] == $b['sort_order']) {
			return 0;
		}
		return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
	}

	/**
	 * @param $item
	 * @param AForm $form
	 * @return string
	 */
	private function _field_value_form($item, $form) {

		if(in_array($this->data['field_data']['element_type'], array('U','K'))){ return array();}

		$field_value_id = '';
		if (isset($item['id'])) {
			$field_value_id = $item['id'];
			$this->data['row_id'] = 'row' . $field_value_id;
			$this->data['attr_val_id'] = $field_value_id;
		} else {
			$field_value_id = '';
			$this->data['row_id'] = 'new_row';
		}

		$this->data['form']['fields']['field_value_id'] = $form->getFieldHtml(array(
				'type' => 'hidden',
				'name' => 'field_value_id[' . $field_value_id . ']',
				'value' => $field_value_id,
		));

		$this->data['form']['fields']['field_value'] = $form->getFieldHtml(array(
				'type' => ($this->data['field_data']['element_type'] == 'T') ? 'textarea' : 'input',
				'name' => 'name[' . $field_value_id . ']',
				'value' => $item['name'],
				'style' => 'large-field'
		));

		if (in_array($this->data['field_data']['element_type'], $this->data['elements_with_options'])) {
			$this->data['form']['fields']['sort_order'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'sort_order[' . $field_value_id . ']',
					'value' => (int)$item['sort_order'],
					'style' => 'small-field'
			));
		}

		$this->view->batchAssign($this->data);
		return $this->view->fetch('responses/forms_manager/field_value_row.tpl');
	}

	/**
	 * @return string
	 */
	private function _file_upload_settings_form() {

		$this->loadLanguage('catalog/attribute');
		$this->data['form']['settings_fields'] = array(
				'extensions' => $this->html->buildElement(array(
						'type' => 'input',
						'name' => 'settings[extensions]',
						'value' => $this->data['field_data']['settings']['extensions'],
						'style' => 'no-save'
				)),
				'min_size' => $this->html->buildElement(array(
						'type' => 'input',
						'name' => 'settings[min_size]',
						'value' => $this->data['field_data']['settings']['min_size'],
						'style' => 'small-field no-save'
				)),
				'max_size' => $this->html->buildElement(array(
						'type' => 'input',
						'name' => 'settings[max_size]',
						'value' => $this->data['field_data']['settings']['max_size'],
						'style' => 'small-field no-save'
				)),
				'directory' => $this->html->buildElement(array(
						'type' => 'input',
						'name' => 'settings[directory]',
						'value' => trim($this->data['field_data']['settings']['directory'], '/'),
						'style' => 'no-save'
				)),
		);

		$this->data['entry_upload_dir'] = sprintf($this->language->get('entry_upload_dir'), 'admin/system/uploads/');
		$uplds_dir = DIR_APP_SECTION . '/system/uploads';
		$settgs_dir = $uplds_dir.'/'.trim($this->data['attribute_data']['settings']['directory'], '/');
		//check or make writable dirs
		if( !make_writable_dir($uplds_dir) || !make_writable_dir($settgs_dir) ){
			$this->data['form']['settings_fields']['directory'] .= '<i class="error">' . $this->language->get('error_directory_not_writable') . '</i>';
		}

		$this->view->batchAssign($this->data);
		return $this->view->fetch('responses/forms_manager/file_upload_settings.tpl');
	}
}