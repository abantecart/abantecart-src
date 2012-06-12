<?php
class ControllerPagesExtensionDefaultBankTransfer extends AController {
	private $error = array();
	public $data = array();
	private $fields = array(
		'default_bank_transfer_text_instruction'

	);
	
	public function main() {

		$this->loadLanguage('default_bank_transfer/default_bank_transfer');
		$this->document->setTitle( $this->language->get('default_bank_transfer_name') );
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->_validate())) {

			$this->request->post['default_bank_transfer_text_instruction_'.$this->session->data['content_language_id']] = $this->request->post['default_bank_transfer_text_instruction'];
			unset($this->request->post['default_bank_transfer_text_instruction']);

			$this->model_setting_setting->editSetting('default_bank_transfer', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('extension/default_bank_transfer'));
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		$this->data['success'] = $this->session->data['success'];
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		if (isset($this->error['key'])) {
			$this->data['error_key'] = $this->error['key'];
		} else {
			$this->data['error_key'] = '';
		}


  		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('extension/extensions/payment'),
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('extension/default_bank_transfer'),
       		'text'      => $this->language->get('default_bank_transfer_name'),
      		'separator' => ' :: '
   		 ));
		$this->document->addScript(RDIR_TEMPLATE . 'javascript/ckeditor/ckeditor.js');
		$this->document->addScript(RDIR_TEMPLATE . 'javascript/ckeditor/adapters/jquery.js');

		$this->data['default_bank_transfer_text_instruction'] = $this->config->get('default_bank_transfer_text_instruction_'.$this->session->data['content_language_id']);

		$this->data ['action'] = $this->html->getSecureURL ( 'extension/default_bank_transfer' );
		$this->data['cancel'] = $this->html->getSecureURL('extension/extensions/shipping');
		$this->data ['heading_title'] = $this->language->get ( 'text_edit' ) . $this->language->get ( 'default_bank_transfer_name' );
		$this->data ['form_title'] = $this->language->get ( 'default_bank_transfer_name' );
		$this->data ['update'] = $this->html->getSecureURL ( 'listing_grid/extension/update', '&id=default_bank_transfer' );

		$form = new AForm ( 'HS' );
		$form->setForm ( array ('form_name' => 'editFrm', 'update' => $this->data ['update'] ) );

		$this->data['form']['form_open'] = $form->getFieldHtml ( array ('type' => 'form', 'name' => 'editFrm', 'action' => $this->data ['action'] ) );
		$this->data['form']['submit'] = $form->getFieldHtml ( array ('type' => 'button', 'name' => 'submit', 'text' => $this->language->get ( 'button_go' ), 'style' => 'button1' ) );
		$this->data['form']['cancel'] = $form->getFieldHtml ( array ('type' => 'button', 'name' => 'cancel', 'text' => $this->language->get ( 'button_cancel' ), 'style' => 'button2' ) );

		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());

		$this->data['form']['fields']['instruction'] = $form->getFieldHtml(array(
					'type' => 'textarea',
					'name' => 'default_bank_transfer_text_instruction',
					'value' => $this->data['default_bank_transfer_text_instruction'],
					'style' => 'xl-field',
			        'required' => true
				));

		$this->data['form']['fields']['instruction_note'] = $this->language->get ( 'default_bank_transfer_text_instruction' );





		$this->view->batchAssign (  $this->language->getASet () );
		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/extension/default_bank_transfer.tpl' );

	}
	
	private function _validate() {
		if (!$this->user->hasPermission('modify', 'extension/default_bank_transfer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>