<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

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

class ControllerResponsesToolRlManager extends AController {
	public $data = array();
	public $error = array();


	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('tool/rl_manager');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->_getForm();

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function save() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$output = array();
		$this->loadLanguage('tool/rl_manager');
		$this->document->setTitle($this->language->get('heading_title'));
		if($this->request->is_POST() && $this->_validateRLTypeForm($this->request->post)){
			$post_data = $this->request->post;
			$rm = new AResourceManager();
			if($rm->updateResourceType($post_data)){
				$output['result_text'] = $this->language->get('text_success');
				$this->load->library('json');
				$this->response->addJSONHeader();
    			$this->response->setOutput(AJson::encode($output));
			} else {
				$error = new AError('');
				$err_data = array('error_text' => 'Unable to save resource type');
				return $error->toJSONResponse('VALIDATION_ERROR_406', $err_data);		
			}
		} else{
			$error = new AError('');
			$err_data = array('error_text' => $this->error);
			return $error->toJSONResponse('VALIDATION_ERROR_406', $err_data);
		}
		
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _getForm() {

		$view = new AView($this->registry, 0);

		$view->batchAssign( $this->language->getASet('tool/rl_manager'));

		$view->assign('error_warning', $this->error['warning']);
		$view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->data = array();
		$this->data['error'] = $this->error;
			
		$rm = new AResourceManager();
		if (isset($this->request->get['rl_type'])) {
			$type_data = $rm->getResourceTypeByName($this->request->get['rl_type']);
		}

		$fields = array('type_id', 'type_name', 'default_directory', 'default_icon', 'file_types', 'access_type');
		foreach ($fields as $f) {
			if (isset($this->request->post[$f])) {
				$this->data [$f] = $this->request->post[$f];
			} elseif (isset($type_data)) {
				$this->data[$f] = $type_data[$f];
			} else {
				$this->data[$f] = '';
			}
		}

		if (!isset($this->data['type_id'])) {
			return null;
		} else {
			$this->data['action'] = $this->html->getSecureURL('r/tool/rl_manager/save', '&rl_type=' . $this->data['type_name']);
			$this->data['heading_title'] = $this->data['form_title'] = $this->language->get('text_edit').'&nbsp;'.$this->data['type_name'].'&nbsp;'.$this->language->get('text_rl_type');
			$form = new AForm('ST');
		}
		$this->document->addBreadcrumb(array(
				'href' => $this->data['action'],
				'text' => $this->data['form_title'],
				'separator' => ' :: '
		));
		$form->setForm(array(
				'form_name' => 'rl_typeFrm',
				'update' => $this->data['update'],
		));
		$this->data['form']['id'] = 'rl_typeFrm';
		$this->data['form']['form_open'] = $form->getFieldHtml(array(
				'type' => 'form',
				'name' => 'rl_typeFrm',
				'action' => $this->data['action'],
				'attr' => 'data-confirm-exit="true"  class="aform form-horizontal"',
		)).
		$this->data['form']['fields']['type_id'] = $form->getFieldHtml(array(
				'type' => 'hidden',
				'name' => 'type_id',
				'value' => $this->data['type_id'],
		));
		$this->data['form']['fields']['type_name'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'type_name',
				'value' => $this->data['type_name'],
				'style' => 'tiny-field',
				'attr' => 'readonly'
		));
		$this->data['form']['fields']['default_icon'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'default_icon',
				'value' => $this->data['default_icon'],
				'style' => 'small-field'
		));
		$this->data['form']['fields']['default_directory'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'default_directory',
				'value' => $this->data['default_directory'],
				'style' => 'small-field',
				'attr' => 'readonly'
		));
		$this->data['form']['fields']['file_types'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'file_types',
				'value' => $this->data['file_types'],
				'style' => 'small-field'
		));

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_close'] = $this->language->get('button_close');
		$this->data['button_save_and_close'] = $this->language->get('button_save_and_close');

		$view->assign('help_url', $this->gen_help_url('resource_library_types'));
		$view->batchAssign($this->data);
		$this->data['response'] = $view->fetch('responses/tool/rl_types_form.tpl');
		$this->response->setOutput($this->data['response']);
	}
	
	
	private function _validateRLTypeForm($data = array()){
		$this->error = array();
		if(!$this->user->canModify('tool/rl_manager')){
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if(empty($data['type_id'])){
			$this->error['type_id'] = $this->language->get('error_missing_type_id');
		}
		if(mb_strlen($data['type_name']) < 2 || mb_strlen($data['type_name']) > 40){
			$this->error['type_name'] = $this->language->get('error_type_name');
		}
		if(mb_strlen($data['default_directory']) < 2 || mb_strlen($data['default_directory']) > 64){
			$this->error['default_directory'] = $this->language->get('error_default_directory');
		}
		if(mb_strlen($data['default_icon']) < 2 || mb_strlen($data['default_icon']) > 64){
			$this->error['default_icon'] = $this->language->get('error_default_icon');
		}
		if(mb_strlen($data['file_types']) < 2 || mb_strlen($data['file_types']) > 40){
			$this->error['file_types'] = $this->language->get('error_file_types');
		}

		$this->extensions->hk_ValidateData($this);

		return $this->error ? false : true;
	}


}