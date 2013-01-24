<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ControllerPagesExtensionEncryptionDataManager extends AController {
	private $error = array();
	public $data = array();
	private $fields = array('key_name', 'key_length', 'private_key_type', 'encrypt_key', 'passphrase', 'enc_key', 'enc_test_mode');
	
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('encryption_data_manager/encryption_data_manager');
		$this->document->setTitle( $this->language->get('encryption_data_manager_name') );
		$this->load->model('setting/setting');
				 
		$enc = new ASSLEncryption(); 
		if ( !$enc->active || !$enc->getKeyPath() ) {
			$this->error['warning'] = $this->language->get('error_openssl_disabled');	
		}		 
		$enc_data = new ADataEncryption(); 
		if ( !$enc_data->active ) {
			$this->error['warning'] = $this->language->get('error_data_encryption_disabled');	
		}		 

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->_validate())) {
			if ( !empty($this->request->post['key_name']) ) {
				$this->request->post['key_name'] = preformatTextID($this->request->post['key_name']);
				$keys = $this->_create_key_pair($this->request->post); 
				if ( $keys['public'] || $keys['private'] ) {
					$this->session->data['success'] = sprintf($this->language->get('text_success_key_get'), $keys['public'], $keys['private']);
				} else {
					$this->error['warning'] = $this->language->get('error_generating_keys_failed');
				}
			} else if ( !empty($this->request->post['enc_key']) ) {
				$enc_result = $this->_encrypt_user_data($this->request->post);
				if ( $this->request->post['enc_test_mode'] ) {
					$this->session->data['success'] = sprintf($this->language->get('text_encryption_test'), implode('<br/>', $enc_result) );
				} else if ( count($enc_result)) {
					$this->session->data['success'] = sprintf($this->language->get('text_success_encrypting'), implode('<br/>', $enc_result) );
				} else {
					$this->error['warning'] = $this->language->get('error_encrypting');
				}
			} else {
				$this->error['warning'] = $this->language->get('error_required_data_missing');	
			}
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

  		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('eextension/extensions/extensions'),
       		'text'      => $this->language->get('text_extensions'),
      		'separator' => ' :: '
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('extension/encryption_data_manager'),
       		'text'      => $this->language->get('encryption_data_manager_name'),
      		'separator' => ' :: '
   		 ));
		
		foreach ( $this->fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post[$f];
			} else {
				$this->data [$f] = $this->config->get($f);
			}
		}

		//Build sections for display 		
		$this->data ['action'] = $this->html->getSecureURL ( 'extension/encryption_data_manager' );
		$this->data['cancel'] = $this->html->getSecureURL('extension/encryption_data_manager');
		$this->data ['heading_title'] = $this->language->get ( 'text_additional_settings' );
		$this->data ['update'] = $this->html->getSecureURL ( 'listing_grid/extension/update', '&id=encryption_data_manager' );
		$form = new AForm ( 'HT' );
		$form->setForm ( array ('form_name' => 'keyGenFrm', 'update' => $this->data ['update'] ) );

		$key_gen = array();
		$key_gen['section_id'] = 'key_gen';
		$key_gen['name'] = $this->language->get('key_gen_section_name');
		$key_gen['form_title'] = $key_gen['name'];
		$key_gen['form']['form_open'] = $form->getFieldHtml ( array ('type' => 'form', 'name' => 'keyGenFrm', 'action' => $this->data ['action'] ) );
		$key_gen['form']['submit'] = $form->getFieldHtml(array('type' => 'button', 'name' => 'submit', 'text' => $this->language->get('button_generate_keys'), 'style' => 'button1' ) );
		$key_gen['form']['cancel'] = $form->getFieldHtml( array('type' => 'button', 'name' => 'reset', 'text' => $this->language->get('button_reset'), 'style' => 'button2' ) );
				
		$key_gen['form']['fields']['key_name'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'key_name',
				'value' => $this->data['key_name'],
				'required' => true
			));

		$key_gen['form']['fields']['key_length'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'key_length',
				'value' => !$this->data['key_length'] ? 2048 : $this->data['key_length'],
			));

		$key_gen['form']['fields']['private_key_type'] = $form->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'private_key_type',
				'options' => array(
					OPENSSL_KEYTYPE_RSA => 'OPENSSL_KEYTYPE_RSA', 
					OPENSSL_KEYTYPE_DSA => 'OPENSSL_KEYTYPE_DSA', 
					OPENSSL_KEYTYPE_DH => 'OPENSSL_KEYTYPE_DH'
				),
				'value' => $this->data['private_key_type'],
			));

		$key_gen['form']['fields']['encrypt_key'] = $form->getFieldHtml(array(
				'type' => 'checkbox',
				'name' => 'encrypt_key',
				'value' => $this->data['encrypt_key'],
				'style'  => 'btn_switch',
			));

		$key_gen['form']['fields']['passphrase'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'passphrase',
				'value' => $this->data['passphrase'],
			));

		$this->data['sections'][] = $key_gen;

		
		$form2 = new AForm ( 'HT' );
		$form2->setForm ( array ('form_name' => 'dataEncFrm', 'update' => $this->data ['update'] ) );

		$data_enc = array();
		$data_enc['section_id'] = 'data_encryption';
		$data_enc['name'] = $this->language->get('data_encryption');	
	
		$data_enc['form_title'] = $data_enc['name'];
		$data_enc['form']['form_open'] = $form2->getFieldHtml ( array ('type' => 'form', 'name' => 'dataEncFrm', 'action' => $this->data ['action'] ) );
		$data_enc['form']['submit'] = $form2->getFieldHtml(array('type' => 'button', 'name' => 'submit', 'text' => $this->language->get('button_encrypt_data'), 'style' => 'button1' ) );
		$data_enc['form']['cancel'] = $form2->getFieldHtml(array('type' => 'button', 'name' => 'reset', 'text' => $this->language->get('button_reset'), 'style' => 'button2' ) );
				
		//load existing keys. 
		$files = array_filter(glob($enc->getKeyPath().'/*'), function($file) { return preg_match('/.pub$/', $file ); } );
		$pub_keys = array_map(function($file) { return basename($file, ".pub"); }, $files );
		$pub_keys_options = array();
		foreach ($pub_keys as $key_name ) {  $pub_keys_options[$key_name] = $key_name; }
				
		$data_enc['form']['fields']['enc_key'] = $form2->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'enc_key',
				'options' => $pub_keys_options,
				'value' => $this->data['enc_key'],
			));

		$enc_tables_options = array();
		foreach ($enc_data->getEcryptedTables() as $table_name) { $enc_tables_options[$table_name] = $table_name; }
		
		/*
		//Per table encryption is not suported YET
		$data_enc['form']['fields']['enc_tables'] = $form2->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'enc_tables',
				'options' => $enc_tables_options,
				'value' => $this->data['enc_tables'],
			));
		*/
		$data_enc['form']['fields']['enc_tables'] = implode(', ', $enc_data->getEcryptedTables());

		$data_enc['form']['fields']['enc_test_mode'] = $form2->getFieldHtml(array(
				'type' => 'checkbox',
				'name' => 'enc_test_mode',
				'value' => $this->data['enc_test_mode'],
				'style'  => 'btn_switch',
			));

		
		$this->data['sections'][] = $data_enc;			

		$this->view->batchAssign (  $this->language->getASet () );
		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/extension/encryption_data_manager.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
		
	private function _validate() {
		if (!$this->user->canModify('extension/encryption_data_manager')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
	
	private function _create_key_pair ( $data ) {
		if ( empty($data['key_name']) ) {
			return array();
		}
		
		$data['encrypt_key'] = $data['encrypt_key'] ? 1 : 0;
		$data['private_key_type'] = constant($data['private_key_type']);
		
		//generate keys	and save	
		$enc = new ASSLEncryption ();
		$keys = $enc->generate_ssl_key_pair($data, $data['passphrase']);
		$enc->save_ssl_key_pair($keys, $data['key_name']);
		
		return $keys;
	}
	
	private function _encrypt_user_data ( $data ) {
		if ( empty($data['enc_key']) ) {
			return array();
		}
		$result = array();
								
		//generate keys	and save	
		$enc_data = new ADataEncryption($data['enc_key']); 
		foreach ($enc_data->getEcryptedTables() as $table_name) {
			$enc_fields = $enc_data->getEcryptedFields($table_name);
			// important to use non-encripted table
			$query_read = $this->db->query("SELECT * FROM " . DB_PREFIX . $table_name );
			$count = 0;
			echo_array($query_read->rows);
			foreach($query_read->rows as $record) {					
				$enc_rec_data = $enc_data->encrypt_data($record);
				//check if this is not a test mode 
				if (!$data['enc_test_mode']) {
					
				}
				$count++;
			}			
			$result[] = "Table $table_name has encrypted $count records";
		}
		return $result;
	}	
}