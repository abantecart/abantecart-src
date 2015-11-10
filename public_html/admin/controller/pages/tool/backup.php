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

if (defined('IS_DEMO') && IS_DEMO) {
	header('Location: static_pages/demo_mode.php');
}

class ControllerPagesToolBackup extends AController {
	private $error = array();
	public $data;

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('tool/backup');
		$this->loadModel('tool/backup');

		$this->document->setTitle($this->language->get('heading_title'));
		//shedule task for backup
		if ($this->request->is_POST() && $this->_validate()) {

			if (is_uploaded_file($this->request->files['restore']['tmp_name'])) {
				if (pathinfo($this->request->files['restore']['name'], PATHINFO_EXTENSION) == 'sql') {
					$filetype = 'sql';
					$content = file_get_contents($this->request->files['restore']['tmp_name']);
				} else {
					$content = false;
				}
			} elseif (is_uploaded_file($this->request->files['import']['tmp_name'])) {
				if (pathinfo($this->request->files['import']['name'], PATHINFO_EXTENSION) == 'xml') {
					$filetype = 'xml';
					$content = file_get_contents($this->request->files['import']['tmp_name']);
				} else {
					$content = false;
				}
			} else {
				$content = false;
				//if do sheduled task for backup
				$task_details = $this->model_tool_backup->createBackupTask('sheduled_backup', $this->request->post);

				if(!$task_details){
					$this->error['warning'] = array_merge($this->error,$this->model_tool_backup->errors);
				}else{
					$this->session->data['success'] = sprintf($this->language->get('text_success_scheduled'),
															  $this->html->getSecureURL('tool/task'));
					$this->redirect($this->html->getSecureURL('tool/backup'));
				}

			}

			if ($content) {
				$this->cache->delete('*');
				if ($filetype == 'sql') {
					$this->model_tool_backup->restore($content);
					$this->session->data['success'] = $this->language->get('text_success');
					$this->redirect($this->html->getSecureURL('tool/backup'));
				} else {
					if ($this->model_tool_backup->load($content)) {
						$this->session->data['success'] = $this->language->get('text_success_xml');
						$this->redirect($this->html->getSecureURL('tool/backup'));
					} else {
						$this->error['warning'] = $this->language->get('error_xml');
					}
				}
			} elseif(!has_value($this->request->post['do_backup'])) {
				if ($this->request->files) {
					$this->error['warning'] = $this->language->get('error_empty') . ' (' . pathinfo($this->request->files['restore']['name'], PATHINFO_EXTENSION) . ')';
				} else {
					$this->error['warning'] = $this->language->get('error_upload');
					$uploaded_file = '';
					if (isset($this->request->files['restore'])) {
						$uploaded_file = $this->request->files['restore'];
					} elseif ($this->request->files['import']) {
						$uploaded_file = $this->request->files['import'];
					}
					if ($uploaded_file) {
						$this->error['warning'] .= '<br>Error: ' . getTextUploadError($uploaded_file['error']);
					}
				}
			}
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} elseif ($this->session->data['error']) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$bkp = new ABackup('manual_backup');
			$bkp->validate();
			$this->data['error_warning'] = implode("\n",$bkp->error);
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE
		));

		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('tool/backup'),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: ',
				'current' => true
		));


		$this->loadModel('tool/backup');

		$this->data['tables'] = $this->model_tool_backup->getTables();
		//if we cannot to get table list from database -show error
		if( $this->data['tables']===false ){
			$this->data['tables'] = array();
		}

		$table_sizes = $this->model_tool_backup->getTableSizes($this->data['tables']);
		$tables = array();
		$db_size = 0;
		foreach ($this->data['tables'] as $table) {
			$tables[$table] = $table .' ('.$table_sizes[$table]['text'].')';
			$db_size += $table_sizes[$table]['bytes'];
		}
		//size of data of database (sql-file will be greater)
		if($db_size>1048576){
			$this->data['entry_tables_size'] = round(($db_size/1048576),1) .'Mb';
		}else{
			$this->data['entry_tables_size'] = round($db_size/1024,1) .'Kb';
		}

		$form = new AForm('ST');

		$form->setForm(array('form_name' => 'backup'));

		$this->data['form']['id'] = 'backup';
		$this->data['form']['form_open'] = $form->getFieldHtml(
				array('type' => 'form',
						'name' => 'backup',
						'action' => $this->html->getSecureURL('tool/backup'),
						'attr' => 'class="aform form-horizontal"'
				)).
				$form->getFieldHtml(
								array(
										'type' => 'hidden',
										'name' => 'do_backup',
										'value' => 1
								));

		$this->data['form']['fields']['tables'] = $form->getFieldHtml(
				array(
						'type' => 'checkboxgroup',
						'name' => 'table_list[]',
						'value' => $this->data['tables'],
						'options' => $tables,
						'scrollbox' => true,
						'style' => 'checkboxgroup'
				));

		$c_size = $this->model_tool_backup->getCodeSize();
		if($c_size>1048576){
			$code_size = round(($c_size/1048576),1) .'Mb';
		}else{
			$code_size = round(($c_size/1024),1) .'Kb';
		}
		$this->data['entry_backup_code'] = sprintf($this->language->get('entry_backup_code'), $code_size);

		$this->data['form']['fields']['backup_code'] = $form->getFieldHtml(
				array(
						'type' => 'checkbox',
						'name' => 'backup_code',
						'value' => '1',
						'checked' => true
				));

		$c_size = $this->model_tool_backup->getContentSize();
		if($c_size>1048576){
			$content_size = round(($c_size/1048576),1) .'Mb';
		}else{
			$content_size = round(($c_size/1024),1) .'Kb';
		}
		$this->data['entry_backup_content'] = sprintf($this->language->get('entry_backup_content'), $content_size);

		$this->data['form']['fields']['backup_content'] = $form->getFieldHtml(
				array(
						'type' => 'checkbox',
						'name' => 'backup_content',
						'value' => '1',
						'checked' => true
				));

		$this->data['form']['fields']['compress_backup'] = $form->getFieldHtml(
				array(
						'type' => 'checkbox',
						'name' => 'compress_backup',
						'value' => 1
				));

		$this->data['entry_compress_backup'] = sprintf($this->language->get('entry_compress_backup'), str_replace(DIR_ROOT,'',DIR_BACKUP) ,DIR_BACKUP);

		$this->data['form']['build_task_url'] = $this->html->getSecureURL('r/tool/backup/buildTask');
		$this->data['form']['complete_task_url'] = $this->html->getSecureURL('r/tool/backup/complete');
		$this->data['form']['backup_now'] = $form->getFieldHtml(
				array('type' => 'button',
						'name' => 'backup_now',
						'text' => $this->language->get('button_backup_now'),
						'style' => 'button1',
				));
		$this->data['form']['backup_schedule'] = $form->getFieldHtml(
				array('type' => 'button',
						'name' => 'backup_schedule',
						'text' => $this->language->get('button_backup_schedule'),
						'style' => 'button1',
				));



		$form = new AForm('ST');
		$form->setForm(array('form_name' => 'restore_form'));
		$this->data['restoreform']['id'] = 'restore_form';
		$this->data['restoreform']['form_open'] = $form->getFieldHtml(
				  array('type' => 'form',
						'name' => 'restore_form',
						'action' => $this->html->getSecureURL('tool/backup'),
						'attr' => 'class="aform form-horizontal"'
				));
		$this->data['restoreform']['file'] = $form->getFieldHtml(
				array('type' => 'file',
						'name' => 'restore',
				));
		$this->data['restoreform']['submit'] = $form->getFieldHtml(
				array('type' => 'button',
						'name' => 'submit',
						'text' => $this->language->get('tab_restore'),
						'style' => 'button1',
				));


		$form = new AForm('ST');
		$form->setForm(array('form_name' => 'loadxml_form'));
		$this->data['xmlform']['id'] = 'loadxml_form';
		$this->data['xmlform']['form_open'] = $form->getFieldHtml(
				array('type' => 'form',
						'name' => 'loadxml_form',
						'action' => $this->html->getSecureURL('tool/backup'),
						'attr' => 'class="aform form-horizontal"'
				));
		$this->data['xmlform']['file'] = $form->getFieldHtml(
				array('type' => 'file',
						'name' => 'import',
				));
		$this->data['xmlform']['submit'] = $form->getFieldHtml(
				array('type' => 'button',
						'name' => 'submit',
						'text' => $this->language->get('button_load'),
						'style' => 'button1',
				));

		$this->data['text_fail_note'] = sprintf($this->language->get('text_fail_note'), DIR_APP_SECTION.'system/backup');

		$this->view->batchAssign($this->data);
		$this->view->assign('help_url', $this->gen_help_url());
		$this->view->assign('current_url', $this->html->currentURL());

		$this->processTemplate('pages/tool/backup.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

	}
	private function _validate() {
		if (!$this->user->canModify('tool/backup')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if(has_value($this->request->post['do_backup'])){ // sign of backup form
			$this->request->post['backup_code'] = $this->request->post['backup_code'] ? true : false;
			$this->request->post['backup_content'] = $this->request->post['backup_content'] ? true : false;

			if(!$this->request->post['do_backup'] &&  !$this->request->post['backup_code'] && !$this->request->post['backup_content']){
				$this->error['warning'] = $this->language->get('error_nothing_to_backup');
			}
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function download() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if ($this->user->canAccess('tool/backup')) {
			$filename = str_replace(array('../', '..\\', '\\', '/'), '', $this->request->get['filename']);
			$file = DIR_BACKUP . $filename;
			if (file_exists($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/x-gzip');
				header('Content-Disposition: attachment; filename=' . $filename);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_end_clean();
				flush();
				readfile($file);
				exit;
			} else {
				$this->session->data['error'] = 'Error: You Cannot to Download File '.$file.' Because of Absent on Hard Drive.';
				$this->redirect($this->html->getSecureURL('tool/install_upgrade_history'));
			}
		} else {
			return $this->dispatch('error/permission');
		}
	}
}
