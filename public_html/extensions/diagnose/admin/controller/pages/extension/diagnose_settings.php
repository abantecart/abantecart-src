<?php
class ControllerPagesExtensionDefaultStripeSettings extends AController {
	private $error = array();
	public $data = array();
	private $errors = array('diagnose_sk_live','diagnose_sk_test');

	private $fields = array(
		'diagnose_access_token',
		'diagnose_test_mode',
		'diagnose_sk_live',
		'diagnose_sk_test',
		'diagnose_settlement',
	);
	
	public function main() {

		$this->request->get['extension'] = 'diagnose';
		$this->loadLanguage('diagnose/diagnose');
		$this->document->setTitle( $this->language->get('heading_title') );
		$this->load->model('setting/setting');

		$this->document->addStyle(
			array(
			'href' => $this->view->templateResource('/stylesheet/stripe.css'),
                                         'rel' => 'stylesheet',
                                         'media' => 'screen',
                                )
        );

		//did we get code from strype connect 
		if( $this->request->get['access_token'] ) {
			//need to save stripe access_token and set live mode
			$settings = array(
				'diagnose_access_token' => $this->request->get['access_token'],
				'diagnose_test_mode' => 1
			);
			if( $this->request->get['livemode'] ) {
				$settings['diagnose_test_mode'] = 0;
			}
			
			$this->model_setting_setting->editSetting('diagnose', $settings);
			$this->session->data['success'] = $this->language->get('text_connect_success');
			$this->redirect($this->html->getSecureURL('extension/diagnose_settings'));
		} else if($this->request->get['disconnect']) {
			$this->model_setting_setting->editSetting('diagnose', array('diagnose_access_token' => '' ));		
			$this->session->data['success'] = $this->language->get('text_disconnect_success');
			$this->redirect($this->html->getSecureURL('extension/diagnose_settings'));
		}

		if ( $this->request->is_POST() && $this->_validate() ) {
			$this->model_setting_setting->editSetting('diagnose', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('extension/diagnose_settings'));
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

		$this->data['error'] = array();
		foreach ( $this->errors as $f ) {
			if (isset ( $this->error[$f] )) {
				$this->data['error'][$f] = $this->error[$f];
			}
		}
		//error with strype connect?
		if($this->request->get['error']) {
			$this->data['error'][$this->request->get['error']] = $this->request->get['error_dec'];
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
       		'href'      => $this->html->getSecureURL('payment/diagnose'),
       		'text'      => $this->language->get('diagnose_name'),
      		'separator' => ' :: ',
		    'current' => true
   		 ));

		foreach ( $this->fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data[$f] = $this->request->post[$f];
			} else {
				$this->data[$f] = $this->config->get($f);
			}
		}

		//if skip connect is selected or API keys are set up
		if( $this->request->get['skip_connect'] || 
			( !$this->data['diagnose_access_token'] && ( $this->data['diagnose_sk_test'] || $this->data['diagnose_sk_live'] ))
			) {
			$this->data['skip_connect'] = true;
		}
 
		$this->data['action'] = $this->html->getSecureURL ( 'extension/diagnose_settings', '&extension=diagnose' );
		$this->data['disconnect'] = $this->html->getSecureURL('extension/diagnose_settings', '&extension=diagnose&disconnect=true' );
		$this->data['heading_title'] = $this->language->get ( 'text_edit' ) . $this->language->get('diagnose_name');
		$this->data['form_title'] = $this->language->get ( 'heading_title' );
		$this->data['update'] = $this->html->getSecureURL ( 'r/extension/diagnose/update' );
		$url = base64_encode($this->html->getSecureURL('extension/diagnose_settings', '&extension=diagnose'));
		$this->data['connect_url'] = base64_decode('aHR0cHM6Ly9tYXJrZXRwbGFjZS5hYmFudGVjYXJ0LmNvbS9zdHJpcGVfY29ubmVjdC5waHA=');
		$this->data['connect_url'] .= '?clid=ca_5XtCjhqt1xB4wy8bMvr3QVlbtJg2coIs'; 
		$this->data['connect_url'] .= '&ret='.$url;

		//see if we are connected yet to strype
		$stripe_code = $this->config->get('diagnose_access_token');
		if ($stripe_code) {
			//validate the tocken
			$this->data['connected'] = true;
		} else {
		    $this->data['skip_url'] = $this->html->getSecureURL ( 'extension/diagnose_settings', '&extension=diagnose&skip_connect=true' );
		}
		$form = new AForm('HT');
		$form->setForm ( array (
				'form_name' => 'editFrm',
				'update' => $this->data ['update'] ) );

		$this->data['form']['form_open'] = $form->getFieldHtml ( array (
				'type' => 'form',
				'name' => 'editFrm',
				'action' => $this->data ['action'],
				'attr' => 'data-confirm-exit="true" class="aform form-horizontal"'
		) );
		$this->data['form']['submit'] = $form->getFieldHtml ( array (
				'type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get ( 'button_save' )
				 ) );

		//stripe related settings
		$this->data['test_mode'] = $this->data['diagnose_test_mode'];
		$this->data['form']['fields']['diagnose_test_mode'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'diagnose_test_mode',
		    'value' => $this->data['diagnose_test_mode'],
			'style'  => 'btn_switch',
	    ));

		$this->data['form']['fields']['diagnose_sk_test'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'diagnose_sk_test',
		    'value' => $this->data['diagnose_sk_test'],
			'required' => true,
	    ));
		$this->data['form']['fields']['diagnose_sk_live'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'diagnose_sk_live',
		    'value' => $this->data['diagnose_sk_live'],
			'required' => true,
	    ));

		$settlement = array(
			'auto' => $this->language->get('diagnose_settlement_auto'),
			'delayed' => $this->language->get('diagnose_settlement_delayed'),
		);
		$this->data['form']['fields']['diagnose_settlement'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'diagnose_settlement',
			'options' => $settlement,
		    'value' => $this->data['diagnose_settlement'],
	    ));

		//load tabs controller
		$this->data['groups'][] = 'additional_settings';
		$this->data['link_additional_settings'] = $this->data['action'];
		$this->data['active_group'] = 'additional_settings';

		$tabs_obj = $this->dispatch('pages/extension/extension_tabs', array( $this->data ) );
		$this->data['tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$obj = $this->dispatch('pages/extension/extension_summary', array( $this->data ) );
		$this->data['extension_summary'] = $obj->dispatchGetOutput();
		unset($obj);

		$this->view->batchAssign( $this->data );
		$this->view->batchAssign( $this->language->getASet() );
		$this->processTemplate('pages/extension/diagnose_settings.tpl');

	}
	
	private function _validate() {
		if (!$this->user->canModify('diagnose/diagnose')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if($this->request->get['skip_connect']){
			if (!$this->request->post['diagnose_sk_live']) {
				$this->error['diagnose_sk_live'] = $this->language->get('error_diagnose_sk_live');
			}
			if (!$this->request->post['diagnose_sk_test']) {
				$this->error['diagnose_sk_test'] = $this->language->get('error_diagnose_sk_test');
			}
		}
				
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
