<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2017 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if(!defined('DIR_CORE')){
	header('Location: static_pages/');
}
class ControllerPagesToolImportExport extends AController{

	public $tabs = array('import', 'export');
	public $error;
	public $success = '';
	public $data = array();
	/**
	 * @var AData
	 */
	private $handler;
	/**
	 * @var array()
	 */
	private $tables;

	public function __construct($registry, $instance_id, $controller, $parent_controller = '') {
		parent::__construct($registry, $instance_id, $controller, $parent_controller);
		if ($this->session->data['import']) {
			$this->tabs = array_merge(array('import_wizard'), $this->tabs);
		}
		$this->handler = new AData();
		$this->tables = $this->_get_tables_cols();
	}

	public function main(){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if ($this->request->get['active'] == 'import_wizard' && $this->session->data['import']) {
			redirect($this->html->getSecureURL('tool/import_export/import_wizard'));
		}

		$this->loadLanguage('tool/import_export');
		$this->document->setTitle($this->language->get('import_export_title'));

		$this->data['title'] = $this->language->get('import_export_title');
		$this->data['text_tables'] = $this->language->get('text_tables');
		$this->data['text_options'] = $this->language->get('text_options');

		$this->data['tabs'] = $this->tabs;

		if (isset($this->request->get['active']) && strpos($this->request->get['active'], '-') !== false) {
			$this->request->get['active'] = substr($this->request->get['active'], 0, strpos($this->request->get['active'], '-'));
		}
		$this->data['active'] = isset($this->request->get['active']) && in_array($this->request->get['active'], $this->data['tabs']) ?
				$this->request->get['active'] : $this->data['tabs'][0];
		if (!$this->data['active']) {
			$this->data['active'] = 'import';
		}

		foreach($this->data['tabs'] as $tab){
			$this->data['tab_' . $tab] = $this->language->get('tab_' . $tab);
			$this->data['link_' . $tab] = $this->html->getSecureURL('p/tool/import_export', '&active=' . $tab);
		}

		$this->document->initBreadcrumb(array(
				'href'      => $this->html->getSecureURL('index/home'),
				'text'      => $this->language->get('text_home'),
				'separator' => false
		));
		$this->document->addBreadcrumb(array(
				'href'    => $this->html->getSecureURL('tool/import_export'),
				'text'    => $this->language->get('import_export_title'),
				'current' => true
		));

		$this->view->assign('help_url', $this->gen_help_url($this->data['active']));

		$this->getForm();

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else{
			$this->data['error_warning'] = $this->error;
		}
		$this->data['success'] = $this->success;

		$this->view->batchAssign($this->data);

		$this->processTemplate("pages/tool/{$this->data['active']}.tpl");

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function getForm(){
		$form = new AForm('ST');

		$form->setForm(array(
				'form_name' => $this->data['active'] . 'Frm'
		));

		$this->data['form']['id'] = $this->data['active'] . 'Frm';

		$this->data['form']['submit'] = $form->getFieldHtml(array(
				'type'  => 'button',
				'name'  => 'submit',
				'text'  => $this->language->get('tab_' . $this->data['active']),
				'style' => 'button1',
		));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
				'type'  => 'button',
				'name'  => 'cancel',
				'text'  => $this->language->get('button_cancel'),
				'style' => 'button2',
		));

		switch($this->data['active']){
			case 'import':
				$this->data['action'] = $this->html->getSecureURL('tool/import_upload');

				$this->data['text_load_file'] = $this->language->get('text_load_file');
				$this->data['text_file_field'] = $this->language->get('text_file_field');

				$fileForm = new AForm('ST');

				$fileForm->setForm(array(
						'form_name' => 'file_import_form'
				));

				$this->data['file_form_open'] = $fileForm->getFieldHtml(array(
						'type'   => 'form',
						'name'   => 'file_import_form',
						'action' => $this->data['action'],
						'attr'   => 'class="aform form-horizontal"',
				));

				$this->data['file_field'] = $fileForm->getFieldHtml(array(
						'type' => 'file',
						'name' => 'imported_file'
				));

				$this->data['file_submit'] = $fileForm->getFieldHtml(array(
						'type'  => 'button',
						'name'  => 'file_submit',
						'text'  => $this->language->get('text_load'),
						'style' => 'button1',
				));

				$options['text']['delimiter'] = $this->language->get('text_csv_delimiter') . '<br/>' . $this->language->get('text_export_note_csv_delimiter');
				$options['item']['delimiter'] = $fileForm->getFieldHtml(array(
						'type'    => 'selectbox',
						'name'    => 'options[delimiter]',
						'value'   => 0,
						'options' => array(',', ';', 'TAB', '|')
				));

				$options['text']['test_mode'] = $this->language->get('text_test_mode');
				$options['item']['test_mode'] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'test_mode',
						'value' => 'test',
						'style' => '',
				));

				$this->data['options'] = $options;

				break;

			case 'export':
				$sections = $this->handler->getSections();

				$this->data['action'] = $this->html->getSecureURL('p/tool/export_upload');

				$this->data['form']['fields'] = $this->_build_table_fields($form, (array)$sections);

				$this->data['text_range_from'] = $this->language->get('text_id_range_from');
				$this->data['text_to'] = $this->language->get('text_to');
				break;

		}

		$options = array();

		$options['text']['file_format'] = $this->language->get('text_file_format');
		$options['item']['file_format'] = $form->getFieldHtml(array(
				'type'    => 'selectbox',
				'name'    => 'options[file_format]',
				'value'   => 0,
				'options' => array(
						'csv' => '&nbsp;&nbsp;CSV&nbsp;&nbsp;',
						'txt' => 'TXT (delimited)',
						'xml' => '&nbsp;&nbsp;XML&nbsp;&nbsp;'
				)
		));

		$options['text']['file_name'] = $this->language->get('text_file_name');
		$options['item']['file_name'] = $form->getFieldHtml(array(
				'type'  => 'input',
				'name'  => 'options[file_name]',
				'value' => 'export_' . date('dmY_His'),
				'style' => 'large-field',
		));

		$options['text']['delimiter'] = $this->language->get('text_csv_delimiter') . '<br/>' . $this->language->get('text_export_note_csv_delimiter');
		$options['item']['delimiter'] = $form->getFieldHtml(array(
				'type'    => 'selectbox',
				'name'    => 'options[delimiter]',
				'value'   => 0,
				'options' => array(',', ';', 'TAB')
		));

		$this->data['form']['options'] = $options;

		$this->data['form']['form_open'] = $form->getFieldHtml(array(
				'type'   => 'form',
				'name'   => $this->data['active'] . 'Frm',
				'action' => $this->data['action'],
				'attr'   => 'class="aform form-horizontal"',
		));

	}

	private function _build_table_fields($form, $data){
		$result = array();

		foreach($data as $table_name => $val){

			$result[$table_name]['main'] = $form->getFieldHtml(array(
					'type'  => 'checkbox',
					'name'  => 'section_' . $table_name,
					'value' => $table_name,
					'style' => 'section_input',
			));

			$columns_data = $this->handler->getTableColumns($table_name);
			$columns = array();
			foreach($columns_data as $column){
				$columns[$column['Field']] = $column['Field'];
			}

			$result[$table_name]['filter']['columns'] = $form->getFieldHtml(array(
					'type'    => 'selectbox',
					'name'    => 'data[' . $table_name . '][filter][columns]',
					'value'   => 0,
					'options' => $columns
			));

			$name = 'data[' . $table_name . ']';

			if(isset($val['children'])){
				$name .= '[tables]';
				$children = $this->_get_table_children($form, $val['children'], $name);
				$result[$table_name]['children'] = $children;
			}
		}

		$this->data['text_no_children'] = $this->language->get('text_no_children');
		ksort($result);

		return $result;
	}

	private function _get_table_children($form, $data, $name){

		$children = array();

		foreach($data as $key => $val){
			$new_name = $name . '[' . $key . ']';

			$children[$key]['name'] = $new_name;
			$children[$key]['field'] = $form->getFieldHtml(array(
					'type'  => 'checkbox',
					'name'  => $new_name,
					'value' => $key,
					'style' => '',
			));

			if(isset($val['children'])){
				$new_name .= '[tables]';
				$children = array_merge($children, $this->_get_table_children($form, $val['children'], $new_name));
			}

		}
		return $children;
	}

	public function import_wizard(){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$import_data = $this->session->data['import'];
		if(empty($import_data)) {
			$this->session->data['error'] = $this->language->get('error_data_corrupted');
			return $this->main();
		}

		$this->handler = new AData();

		$this->data['post'] = $this->request->post;
		if($this->request->is_POST() && $this->validateWizardRequest($this->data['post'])){
			//all good to load import task




//exit;
		}


		$this->loadLanguage('tool/import_export');
		$this->document->setTitle($this->language->get('import_wizard_title'));
		$this->data['title'] = $this->language->get('import_wizard_title');

		$this->data['tabs'] = $this->tabs;
		$this->data['active'] = 'import_wizard';
		foreach($this->data['tabs'] as $tab){
			$this->data['tab_' . $tab] = $this->language->get('tab_' . $tab);
			$this->data['link_' . $tab] = $this->html->getSecureURL('p/tool/import_export', '&active=' . $tab);
		}

		$this->document->initBreadcrumb(array(
			'href'      => $this->html->getSecureURL('index/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => false
		));
		$this->document->addBreadcrumb(array(
			'href'    => $this->html->getSecureURL('tool/import_export'),
			'text'    => $this->language->get('import_export_title'),
			'current' => true
		));

		$this->getForm();
		$form = new AForm('ST');

		$form->setForm(array(
			'form_name' => 'importWizardFrm'
		));
		$this->data['form']['id'] = 'importWizardFrmFrm';

		$this->data['form_open'] = $form->getFieldHtml(array(
			'type'   => 'form',
			'name'   => 'importWizardFrmFrm',
			'action' => $this->html->getSecureURL('tool/import_export/import_wizard'),
			'attr'   => 'class="aform form-horizontal"',
		));
		$this->data['form']['submit'] = $form->getFieldHtml(array(
			'type'  => 'button',
			'name'  => 'submit',
			'text'  => $this->language->get('button_continue'),
			'style' => 'button1',
		));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
			'type'  => 'button',
			'name'  => 'cancel',
			'text'  => $this->language->get('button_cancel'),
			'style' => 'button2',
		));
		//urls for creating and running task
		$this->data['form']['build_task_url'] = $this->html->getSecureURL('r/tool/import_process/buildTask');
		$this->data['form']['complete_task_url'] = $this->html->getSecureURL('r/tool/import_process/complete');
		$this->data['form']['abort_task_url'] = $this->html->getSecureURL('r/tool/import_process/abort');

		$this->view->assign('help_url', $this->gen_help_url($this->data['active']));

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else{
			$this->data['error_warning'] = $this->error;
		}
		$this->data['success'] = $this->success;

		//get sample row
		if($import_data['file_type'] == 'csv') {
			ini_set('auto_detect_line_endings', true);
			if ($fh = fopen($import_data['file'], 'r')) {
				$this->data['cols'] = fgetcsv($fh, 0, $import_data['delimiter']);
				$this->data['data'] = fgetcsv($fh, 0, $import_data['delimiter']);
			}
		} elseif ($import_data['file_type'] == 'xml') {
			//need to develop



		}

		$this->data['tables'] = $this->tables;

		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

		$this->view->batchAssign($this->data);

		$this->processTemplate("pages/tool/import_wizard.tpl");

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function import(){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$import_data = $this->session->data['import'];
		if(empty($import_data)) {
			$this->session->data['error'] = $this->language->get('error_data_corrupted');
			return $this->main();
		}

		if($import_data['file_type'] == 'csv'){
			#TODO: Need to add test for the file in case of 'application/octet-stream'
			$csv_array = $this->handler->CSV2ArrayFromFile($import_data['file'], $import_data['delimiter_id']);
			$this->data['results'] = $this->handler->importData($csv_array, $import_data['run_mode']);
			$this->cache->remove('*');
		} elseif($import_data['file_type'] == 'xml'){
			$xml_array = $this->handler->XML2ArrayFromFile($import_data['file']);
			$this->data['results'] = $this->handler->importData($xml_array, $import_data['run_mode']);
			$this->cache->remove('*');
		} else{
			$this->session->data['error'] = $this->language->get('error_file_format');
		}

		if (!$this->data['results']) {
			$this->session->data['error'] = $this->language->get('error_data_corrupted');
		} else {
			$this->data['text_updated'] = $this->language->get('text_updated');
			$this->data['count_updated'] = isset($this->data['results']['update']) ? count($this->data['results']['update']) : 0;
			$this->data['text_created'] = $this->language->get('text_created');
			$this->data['count_created'] = isset($this->data['results']['insert']) ? count($this->data['results']['insert']) : 0;
			$this->data['text_errors'] = $this->language->get('text_errors');
			$this->data['count_errors'] = isset($this->data['results']['error']) ? count($this->data['results']['error']) : 0;
			$this->data['text_some_errors'] = $this->language->get('text_some_errors');
			$this->data['text_loaded'] = $this->language->get('text_import_loaded');
			$this->data['count_loaded'] = $this->data['count_updated'] + $this->data['count_created'] + $this->data['count_errors'];
			$this->data['text_show_details'] = $this->language->get('text_show_details');

			if(isset($this->data['results']['sql'])){
				$this->data['text_test_completed'] = $this->language->get('text_test_completed');
				$this->data['count_test_sqls'] = count($this->data['results']['sql']);
			}
			$this->success = $this->language->get('text_import_loaded') . '0';
		}

		//cleanup
		@unlink($import_data['file']);
		unset($this->session->data['import']);

		$this->main();
	}

	private function _get_tables_cols() {
		return array(
			'products' => array(
				'columns' => array(
					'products.status' => array(
						'title' => 'Product Status (1 or 0)',
						'default' => 1,
					),
					'products.sku' => array(
						'title' => 'Product SKU (up to 64 chars)',
						'update' => true,
					),
					'products.model' => array(
						'title' => 'Product Model (up to 64 chars)',
						'update' => true,
					),
					'product_descriptions.name' => array(
						'title' => 'Product Name (up to 255 chars)',
						'required' => true,
						'update' => true,
					),
					'product_descriptions.blurb' => array(
						'title' => 'Product Short Description',
						'default' => ''
					),
					'product_descriptions.description' => array(
						'title' => 'Product Long Description',
						'default' => ''
					),
					'product_descriptions.meta_keywords' => array(
						'title' => 'Product Long Description',
						'default' => ''
					),
					'product_descriptions.meta_description' => array(
						'title' => 'Product Long Description',
						'default' => ''
					),

				)
			),
			'categories' => array(
				'columns' => array(
					'categories.status' => array(
						'title' => 'Category Status',
						'default' => 1,
					),
					'categories.sort_order' => array(
						'title' => 'Category Sorting (Number)',
						'default' => 0,
					),
					'category_descriptions.parent' => array(
						'title' => 'Category Parent Name',
						'default' => '',
					),
					'category_descriptions.name' => array(
						'title' => 'Category Name',
						'update' => true,
						'required' => true,
					),
					'category_descriptions.description' => array(
						'title' => 'Category Description',
						'default' => ''
					),
					'category_descriptions.meta_keywords' => array(
						'title' => 'Category Meta Keywords',
						'default' => ''
					),
					'category_descriptions.meta_description' => array(
						'title' => 'Category Medta Description',
						'default' => ''
					),
				),
			),
			'manufacturers' => array(
				'columns' => array(
					'manufacturers.sort_order' => array(
						'title' => 'Manufacturers Sorting (Number)',
						'default' => 0
					),
					'manufacturers.name' => array(
						'title' => 'Manufacturers Name (up to 64 chars)',
						'update' => true,
						'required' => true,
					),
				),
			),
		);
	}


}