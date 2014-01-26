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

if (defined('IS_DEMO') && IS_DEMO) {
	header('Location: static_pages/demo_mode.php');
}

class ControllerPagesToolBackup extends AController {
	private $error = array();
	public $data;

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->document->setTitle($this->language->get('heading_title'));
		if ($this->request->server[ 'REQUEST_METHOD' ] == 'POST' && $this->_validate()) {

			if (is_uploaded_file($this->request->files[ 'restore' ][ 'tmp_name' ])) {
				if (pathinfo($this->request->files[ 'restore' ][ 'name' ], PATHINFO_EXTENSION) == 'sql') {
					$filetype = 'sql';
					$content = file_get_contents($this->request->files[ 'restore' ][ 'tmp_name' ]);
				} else {
					$content = false;
				}
			} elseif (is_uploaded_file($this->request->files[ 'import' ][ 'tmp_name' ])) {
				if (pathinfo($this->request->files[ 'import' ][ 'name' ], PATHINFO_EXTENSION) == 'xml') {
					$filetype = 'xml';
					$content = file_get_contents($this->request->files[ 'import' ][ 'tmp_name' ]);
				} else {
					$content = false;
				}
			} else {
				$content = false;
			}

			if ($content) {
				$this->cache->delete('*');
				if ($filetype == 'sql') {
					$this->model_tool_backup->restore($content);
					$this->session->data[ 'success' ] = $this->language->get('text_success');
					$this->redirect($this->html->getSecureURL('tool/backup'));
				} else {
					if ($this->model_tool_backup->load($content)) {
						$this->session->data[ 'success' ] = $this->language->get('text_success_xml');
						$this->redirect($this->html->getSecureURL('tool/backup'));
					} else {
						$this->error[ 'warning' ] = $this->language->get('error_xml');
					}
				}
			} else {
				if ($this->request->files) {
					$this->error[ 'warning' ] = $this->language->get('error_empty') . ' (' . pathinfo($this->request->files[ 'restore' ][ 'name' ], PATHINFO_EXTENSION) . ')';
				} else {
					$this->error[ 'warning' ] = $this->language->get('error_upload');
					$uploaded_file = '';
					if(isset($this->request->files[ 'restore' ])){
						$uploaded_file = $this->request->files[ 'restore' ];
					}elseif($this->request->files[ 'import' ]){
						$uploaded_file = $this->request->files[ 'import' ];
					}
					if($uploaded_file){
						$this->error[ 'warning' ] .= '<br>Error: ' . getTextUploadError($uploaded_file['error']);
					}
				}
			}
		}

		$this->data[ 'heading_title' ] = $this->language->get('heading_title');

		$this->data[ 'text_select_all' ] = $this->language->get('text_select_all');
		$this->data[ 'text_unselect_all' ] = $this->language->get('text_unselect_all');

		$this->data[ 'entry_restore' ] = $this->language->get('entry_restore');
		$this->data[ 'entry_backup' ] = $this->language->get('entry_backup');
		$this->data[ 'entry_loadxml' ] = $this->language->get('entry_loadxml');

		$this->data[ 'button_backup' ] = $this->language->get('button_backup');
		$this->data[ 'button_restore' ] = $this->language->get('button_restore');

		$this->data[ 'tab_backup' ] = $this->language->get('tab_backup');
		$this->data[ 'tab_restore' ] = $this->language->get('tab_restore');
		$this->data[ 'tab_loadxml' ] = $this->language->get('tab_loadxml');

		if (isset($this->error[ 'warning' ])) {
			$this->data[ 'error_warning' ] = $this->error[ 'warning' ];
		} elseif ($this->session->data[ 'error' ]) {
			$this->data[ 'error_warning' ] = $this->session->data[ 'error' ];
			unset($this->session->data[ 'error' ]);
		} else {
			$this->data[ 'error_warning' ] = '';
		}

		if (isset($this->session->data[ 'success' ])) {
			$this->data[ 'success' ] = $this->session->data[ 'success' ];

			unset($this->session->data[ 'success' ]);
		} else {
			$this->data[ 'success' ] = '';
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
			'separator' => ' :: '
		));


		$this->data[ 'backup' ] = $this->html->getSecureURL('tool/backup_file');

		$this->loadModel('tool/backup');

		$this->data[ 'tables' ] = $this->model_tool_backup->getTables();
		$tables = array();
		foreach ($this->data[ 'tables' ] as $table) {
			$tables[ $table ] = $table;
		}

		$this->data[ 'note_rl' ] = $this->language->get('note_rl');
		$this->data[ 'note_config' ] = $this->language->get('note_config');

		$form = new AForm('ST');

		$form->setForm(array( 'form_name' => 'backup' ));

		$this->data[ 'form' ][ 'id' ] = 'backup';
		$this->data[ 'form' ][ 'form_open' ] = $form->getFieldHtml(
			array( 'type' => 'form',
				'name' => 'backup',
				'action' => $this->data[ 'backup' ],
			));

		$this->data[ 'form' ][ 'fields' ][ 'tables' ] = $form->getFieldHtml(
			array(
				'type' => 'checkboxgroup',
				'name' => 'backup[]',
				'value' => $this->data[ 'tables' ],
				'options' => $tables,
				'scrollbox' => true,
				'style' => 'omg'
			));

		$this->data[ 'form' ][ 'backup_rl' ] = $form->getFieldHtml(
			array(
				'type' => 'checkbox',
				'name' => 'backup_rl',
				'value' => 'rl',
				'label_text' => $this->data[ 'note_rl' ],
				'checked' => true
			));

		$this->data[ 'form' ][ 'backup_config' ] = $form->getFieldHtml(
			array(
				'type' => 'checkbox',
				'name' => 'backup_config',
				'label_text' => $this->data[ 'note_config' ],
				'value' => 'config'
			));

		$this->data[ 'form' ][ 'submit' ] = $form->getFieldHtml(
			array( 'type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get('button_backup'),
				'style' => 'button1',
			));
		$this->data[ 'form' ][ 'cancel' ] = $form->getFieldHtml(
			array( 'type' => 'button',
				'name' => 'cancel',
				'text' => $this->language->get('button_cancel'),
				'style' => 'button2',
			));

		$this->data[ 'restore' ] = $this->html->getSecureURL('tool/backup');
		$form = new AForm('ST');
		$form->setForm(array( 'form_name' => 'restore_form' ));
		$this->data[ 'restoreform' ][ 'id' ] = 'restore_form';
		$this->data[ 'restoreform' ][ 'form_open' ] = $form->getFieldHtml(
			array( 'type' => 'form',
				'name' => 'restore_form',
				'action' => $this->data[ 'restore' ],
			));
		$this->data[ 'restoreform' ][ 'file' ] = $form->getFieldHtml(
			array( 'type' => 'file',
				'name' => 'restore',
			));
		$this->data[ 'restoreform' ][ 'submit' ] = $form->getFieldHtml(
			array( 'type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get('button_go'),
				'style' => 'button1',
			));


		$form = new AForm('ST');
		$form->setForm(array( 'form_name' => 'loadxml_form' ));
		$this->data[ 'xmlform' ][ 'id' ] = 'loadxml_form';
		$this->data[ 'xmlform' ][ 'form_open' ] = $form->getFieldHtml(
			array( 'type' => 'form',
				'name' => 'loadxml_form',
				'action' => $this->data[ 'restore' ],
			));
		$this->data[ 'xmlform' ][ 'file' ] = $form->getFieldHtml(
			array( 'type' => 'file',
				'name' => 'import',
			));
		$this->data[ 'xmlform' ][ 'submit' ] = $form->getFieldHtml(
			array( 'type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get('button_go'),
				'style' => 'button1',
			));

		$this->view->batchAssign($this->data);
		$this->view->assign('help_url', $this->gen_help_url());
		$this->view->assign('current_url', $this->html->currentURL() );

		$this->processTemplate('pages/tool/backup.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function backup() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if ($this->request->server[ 'REQUEST_METHOD' ] == 'POST' && $this->_validate()) {
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename=backup.sql');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			$this->loadModel('tool/backup');

			$this->response->setOutput($this->model_tool_backup->backup($this->request->post[ 'backup' ]));
		} else {
			return $this->dispach('error/permission');
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _validate() {
		if (!$this->user->canModify('tool/backup')) {
			$this->error[ 'warning' ] = $this->language->get('error_permission');
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

		if ($this->user->canAccess('tool/backup_file')) {
			$filename = str_replace(array( '../', '..\\', '\\', '/' ), '', $this->request->get[ 'filename' ]);
			$file = DIR_APP_SECTION . 'system/backup/' . $filename;
			if (file_exists($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/x-gzip');
				header('Content-Disposition: attachment; filename=' . $filename);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_clean();
				flush();
				readfile($file);
				exit;
			} else {
				echo 'file does not exists!';
			}
		} else {
			return $this->dispach('error/permission');
		}
	}
}

?>