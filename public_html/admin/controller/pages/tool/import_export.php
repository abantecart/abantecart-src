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
if(!defined('DIR_CORE')){
	header('Location: static_pages/');
}
class ControllerPagesToolImportExport extends AController{

	public $tabs = array('export', 'import');
	public $error;
	public $success = '';
	private $data = array();
	private $handler;

	public function main(){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->handler = new AData();

		$this->loadLanguage('tool/import_export');
		$this->document->setTitle($this->language->get('import_export_title'));

		$this->data['title'] = $this->language->get('import_export_title');
		$this->data['text_tables'] = $this->language->get('text_tables');
		$this->data['text_options'] = $this->language->get('text_options');

		$this->data['tabs'] = $this->tabs;

		if(isset($this->request->get['active']) && strpos($this->request->get['active'], '-') !== false){
			$this->request->get['active'] = substr($this->request->get['active'], 0, strpos($this->request->get['active'], '-'));
		}
		$this->data['active'] = isset($this->request->get['active']) && in_array($this->request->get['active'], $this->data['tabs']) ?
				$this->request->get['active'] : $this->data['tabs'][0];

		foreach($this->data['tabs'] as $tab){
			$this->data['tab_' . $tab] = $this->language->get('tab_' . $tab);
			$this->data['link_' . $tab] = $this->html->getSecureURL('p/tool/import_export', '&active=' . $tab);
		}

		$this->data['token'] = $this->session->data['token'];

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

		$this->view->assign('help_url', $this->gen_help_url($this->data['active']));


		if(isset($this->session->data['error'])){
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else{
			$this->data['error_warning'] = $this->errors;
		}
		$this->data['success'] = $this->success;

		$this->view->batchAssign($this->data);

		if($this->data['active'] == 'import'){
			$this->processTemplate('pages/tool/import.tpl');
		} else{
			$this->processTemplate('pages/tool/export.tpl');
		}

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
				$this->data['action'] = $this->html->getSecureURL('tool/import_export', '&active=' . $this->data['active']);

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
						'options' => array(',', ';', 'TAB')
				));

				$options['text']['test_mode'] = $this->language->get('text_test_mode');
				$options['item']['test_mode'] = $form->getFieldHtml(array(
						'type'  => 'checkbox',
						'name'  => 'test_mode',
						'value' => 'test',
						'style' => '',
				));

				$this->data['options'] = $options;

				if(!empty($this->request->files)){
					if(file_exists($this->request->files['imported_file']['tmp_name'])){

						$this->data['results'] = $this->import($this->request->files['imported_file']);

						if(!$this->data['results']){
							$this->success = $this->language->get('text_import_loaded') . '0';
							$this->session->data['error'] = $this->language->get('error_data_corrupted');
						} else{
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
						}
					} elseif($this->request->files['imported_file']['error'] != 0){
						$this->success = $this->language->get('text_import_loaded') . '0';
						$this->session->data['error'] = $this->language->get('error_upload_' . $this->request->files['imported_file']['error']);
					}

				}
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

	private function getImportedData($file){
		if(in_array($file['type'], array('text/csv', 'application/vnd.ms-excel'))){
			return $this->handler->csvToArray($file);
		} elseif(in_array($file['type'], array('text/xml'))){
			if($xml = simplexml_load_file($file['tmp_name'])){
				return $this->handler->xmlToArray($xml);
			}
		}
		return false;
	}

	private function import($file, $action = 'update_or_insert'){

		$results = array();
		$run_mode = isset($this->request->post['test_mode']) ? $this->request->post['test_mode'] : 'commit';

		if(in_array($file['type'], array('text/csv', 'application/vnd.ms-excel', 'text/plain', 'application/octet-stream'))){
			#NOTE: 'application/octet-stream' is a solution for Windows OS sending unknown file type
			#TODO: Need to add test for the file in case of 'application/octet-stream'
			$csv_array = $this->handler->CSV2ArrayFromFile($file['tmp_name'], $this->request->post['options']['delimiter']);
			$results = $this->handler->importData($csv_array, $run_mode);
			$this->cache->delete('*');
		} elseif($file['type'] == 'text/xml'){
			$xml_array = $this->handler->XML2ArrayFromFile($file['tmp_name']);
			$results = $this->handler->importData($xml_array, $run_mode);
			$this->cache->delete('*');
		} else{
			$this->session->data['error'] = $this->language->get('error_file_format');
		}
		return $results;
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

}