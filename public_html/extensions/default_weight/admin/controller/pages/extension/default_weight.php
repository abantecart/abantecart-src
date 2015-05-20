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
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ControllerPagesExtensionDefaultWeight extends AController {
	private $error = array();
	public $data = array();
	private $fields = array('default_weight_tax_class_id', 'default_weight_sort_order');
	
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$this->request->get['extension'] = 'default_weight';

		$this->loadLanguage('default_weight/default_weight');
		$this->document->setTitle( $this->language->get('default_weight_name') );
		$this->load->model('setting/setting');

		//set store id based on param or session.
		$store_id = (int)$this->config->get('config_store_id');
		if ($this->session->data['current_store_id']) {
			$store_id = (int)$this->session->data['current_store_id'];
		}

		if ($this->request->is_POST() && ($this->_validate())) {
			$this->model_setting_setting->editSetting('default_weight', $this->request->post, $store_id );
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('extension/default_weight'));
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
       		'href'      => $this->html->getSecureURL('extension/extensions/shipping'),
       		'text'      => $this->language->get('text_shipping'),
      		'separator' => ' :: '
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('extension/default_weight'),
       		'text'      => $this->language->get('default_weight_name'),
      		'separator' => ' :: ',
		    'current'   => true
   		 ));

		$this->data['form_store_switch'] = $this->html->getStoreSwitcher();

		$this->load->model('localisation/tax_class');
		$results = $this->model_localisation_tax_class->getTaxClasses();
		$tax_classes = array( 0 => $this->language->get ( 'text_none' ));
		foreach ( $results as $k => $v ) {
			$tax_classes[ $v['tax_class_id'] ] = $v['title'];
		}

		$this->load->model('localisation/location');
		$this->data['locations'] = $this->model_localisation_location->getLocations();
		$locations = array( 0 => $this->language->get ( 'text_all_zones' ));
		foreach ( $this->data['locations'] as $k => $v ) {
			$locations[ $v['location_id'] ] = $v['name'];
		}

		$settings = $this->model_setting_setting->getSetting('default_weight',$store_id);

		foreach ( $this->fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} else {
				$this->data [$f] = $settings[$f];
			}
		}
		
		foreach ($this->data['locations'] as $location) {
			if (isset($this->request->post['default_weight_' . $location['location_id'] . '_rate'])) {
				$this->data['default_weight_' . $location['location_id'] . '_rate'] = $this->request->post['default_weight_' . $location['location_id'] . '_rate'];
			} else {
				$this->data['default_weight_' . $location['location_id'] . '_rate'] = $settings['default_weight_' . $location['location_id'] . '_rate'];
			}		
			
			if (isset($this->request->post['default_weight_' . $location['location_id'] . '_status'])) {
				$this->data['default_weight_' . $location['location_id'] . '_status'] = $this->request->post['default_weight_' . $location['location_id'] . '_status'];
			} else {
				$this->data['default_weight_' . $location['location_id'] . '_status'] = $settings['default_weight_' . $location['location_id'] . '_status'];
			}		
		}


		$this->data ['action'] = $this->html->getSecureURL ( 'extension/default_weight' );
		$this->data['cancel'] = $this->html->getSecureURL('extension/shipping');
		$this->data ['heading_title'] = $this->language->get ( 'text_additional_settings' );
		$this->data ['form_title'] = $this->language->get ( 'default_weight_name' );
		$this->data ['update'] = $this->html->getSecureURL ( 'listing_grid/extension/update', '&id=default_weight' );

		$form = new AForm ( 'HS' );
		$form->setForm ( array (
				'form_name' => 'editFrm',
				'update' => $this->data ['update'] ) );

		$this->data['form']['form_open'] = $form->getFieldHtml ( array (
				'type' => 'form',
				'name' => 'editFrm',
				'action' => $this->data ['action'],
				'attr' => 'data-confirm-exit="true" class="aform form-horizontal"',
		) );
		$this->data['form']['submit'] = $form->getFieldHtml ( array (
				'type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get ( 'button_save' )
		) );
		$this->data['form']['cancel'] = $form->getFieldHtml ( array (
				'type' => 'button',
				'name' => 'cancel',
				'text' => $this->language->get ( 'button_cancel' )
		) );


		foreach ($this->data['locations'] as $location) {

			$rate = 'default_weight_' . $location['location_id'] . '_rate';
			$status = 'default_weight_' . $location['location_id'] . '_status';

			$this->data['form']['fields']['rates'][$status] = $form->getFieldHtml(array(
				'type' => 'checkbox',
				'name' => $status,
				'value' => $this->data[$status],
				'style'  => 'btn_switch',
			));
			$this->data['form']['fields']['rates'][$rate] = $form->getFieldHtml(array(
				'type' => 'textarea',
				'name' => $rate,
				'value' => $this->data[$rate],
				'style' => 'xl-field',
			));
		}

		$this->data['form']['fields']['tax'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'default_weight_tax_class_id',
			'options' => $tax_classes,
		    'value' => $this->data['default_weight_tax_class_id'],
	    ));
		$this->data['form']['fields']['sort_order'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'default_weight_sort_order',
		    'value' => $this->data['default_weight_sort_order'],
	    ));

		$this->view->batchAssign (  $this->language->getASet () );

		//load tabs controller

		$this->data['groups'][] = 'additional_settings';
		$this->data['link_additional_settings'] = $this->data['add_sett']->href;
		$this->data['active_group'] = 'additional_settings';

		$tabs_obj = $this->dispatch('pages/extension/extension_tabs', array( $this->data ) );
		$this->data['tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$obj = $this->dispatch('pages/extension/extension_summary', array( $this->data ) );
		$this->data['extension_summary'] = $obj->dispatchGetOutput();
		unset($obj);

		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/extension/default_weight.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
		
	private function _validate() {
		if (!$this->user->canModify('extension/default_weight')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}