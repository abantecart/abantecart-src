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
if(!defined('DIR_CORE')){
	header('Location: static_pages/');
}

class ControllerPagesExtensionDefaultUsps extends AController{
	private $error = array();
	public $data = array();
	private $fields = array(
			'default_usps_user_id',
			'default_usps_password',
			'default_usps_postcode',
			'default_usps_domestic_0',
			'default_usps_domestic_1',
			'default_usps_domestic_2',
			'default_usps_domestic_3',
			'default_usps_domestic_4',
			'default_usps_domestic_5',
			'default_usps_domestic_6',
			'default_usps_domestic_7',
			'default_usps_domestic_12',
			'default_usps_domestic_13',
			'default_usps_domestic_16',
			'default_usps_domestic_17',
			'default_usps_domestic_18',
			'default_usps_domestic_19',
			'default_usps_domestic_22',
			'default_usps_domestic_23',
			'default_usps_domestic_25',
			'default_usps_domestic_27',
			'default_usps_domestic_28',
			'default_usps_free_domestic_method',
			'default_usps_international_1',
			'default_usps_international_2',
			'default_usps_international_4',
			'default_usps_international_5',
			'default_usps_international_6',
			'default_usps_international_7',
			'default_usps_international_8',
			'default_usps_international_9',
			'default_usps_international_10',
			'default_usps_international_11',
			'default_usps_international_12',
			'default_usps_international_13',
			'default_usps_international_14',
			'default_usps_international_15',
			'default_usps_international_16',
			'default_usps_international_21',
			'default_usps_free_international_method',
			'default_usps_size',
			'default_usps_container',
			'default_usps_machinable',
			'default_usps_length',
			'default_usps_width',
			'default_usps_height',
			'default_usps_girth',
			'default_usps_display_time',
			'default_usps_display_weight',
			'default_usps_weight_class',
			'default_usps_length_class',
			'default_usps_tax_class_id',
			'default_usps_location_id',
			'default_usps_status',
			'default_usps_sort_order',
	);

	public function main(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->request->get['extension'] = 'default_usps';
		$this->loadLanguage('extension/extensions');
		$this->loadLanguage('default_usps/default_usps');
		$this->document->setTitle($this->language->get('text_additional_settings'));
		$this->load->model('setting/setting');

		if($this->request->is_POST() && $this->_validate()){
			$this->model_setting_setting->editSetting('default_usps', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('extension/default_usps'));
		}

		if(isset($this->error['warning'])){
			$this->data['error_warning'] = $this->error['warning'];
		} else{
			$this->data['error_warning'] = '';
		}
		if(isset($this->error['user_id'])){
			$this->data['error']['user_id'] = $this->error['user_id'];
		}
		if(isset($this->error['postcode'])){
			$this->data['error']['postcode'] = $this->error['postcode'];
		}
		$this->data['success'] = $this->session->data['success'];
		if(isset($this->session->data['success'])){
			unset($this->session->data['success']);
		}

		$this->document->initBreadcrumb(array(
				'href'      => $this->html->getSecureURL('index/home'),
				'text'      => $this->language->get('text_home'),
				'separator' => false
		));
		$this->document->addBreadcrumb(array(
				'href'      => $this->html->getSecureURL('extension/extensions/shipping'),
				'text'      => $this->language->get('text_shipping'),
				'separator' => ' :: '
		));
		$this->document->addBreadcrumb(array(
				'href'      => $this->html->getSecureURL('extension/default_usps'),
				'text'      => $this->language->get('default_usps_name'),
				'separator' => ' :: ',
				'current'   => true
		));

		$sizes = array(
				'REGULAR'  => $this->language->get('text_regular'),
				'LARGE'    => $this->language->get('text_large'),
				'OVERSIZE' => $this->language->get('text_oversize'),
		);

		$containers = array(
				'RECTANGULAR'    => $this->language->get('text_rectangular'),
				'NONRECTANGULAR' => $this->language->get('text_non_rectangular'),
				'VARIABLE'       => $this->language->get('text_variable'),
		);


		$this->load->model('localisation/weight_class');
		$results = $this->model_localisation_weight_class->getWeightClasses();
		$weight_classes = array();
		foreach($results as $k => $v){
			$weight_classes[$v['unit']] = $v['title'];
		}

		$this->load->model('localisation/tax_class');
		$results = $this->model_localisation_tax_class->getTaxClasses();
		$tax_classes = array(0 => $this->language->get('text_none'));
		foreach($results as $k => $v){
			$tax_classes[$v['tax_class_id']] = $v['title'];
		}

		$this->load->model('localisation/location');
		$results = $this->model_localisation_location->getLocations();
		$locations = array(0 => $this->language->get('text_all_zones'));
		foreach($results as $k => $v){
			$locations[$v['location_id']] = $v['name'];
		}

		foreach($this->fields as $f){
			if(isset ($this->request->post [$f])){
				$this->data [$f] = $this->request->post [$f];
			} else{
				$this->data [$f] = $this->config->get($f);
			}
		}

		$this->data ['action'] = $this->html->getSecureURL('extension/default_usps', '&extension=default_usps');
		$this->data['cancel'] = $this->html->getSecureURL('extension/shipping');
		$this->data ['heading_title'] = $this->language->get('text_additional_settings');
		$this->data ['form_title'] = $this->language->get('default_usps_name');
		$this->data ['update'] = $this->html->getSecureURL('r/extension/default_usps_save/update');

		$form = new AForm ('HS');
		$form->setForm(array('form_name' => 'editFrm', 'update' => $this->data ['update']));

		$this->data['form']['form_open'] = $form->getFieldHtml(array(
				'type'   => 'form',
				'name'   => 'editFrm',
				'action' => $this->data ['action'],
				'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"'
		));
		$this->data['form']['submit'] = $form->getFieldHtml(array(
				'type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get('button_save')
		));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
				'type' => 'button',
				'name' => 'cancel',
				'text' => $this->language->get('button_cancel')
		));

		$this->data['form']['fields']['user_id'] = $form->getFieldHtml(array(
				'type'     => 'input',
				'name'     => 'default_usps_user_id',
				'value'    => $this->data['default_usps_user_id'],
				'required' => true,
		));
		$this->data['form']['fields']['password'] = $form->getFieldHtml(array(
				'type'  => 'input',
				'name'  => 'default_usps_password',
				'value' => $this->data['default_usps_password'],
		));
		$this->data['form']['fields']['postcode'] = $form->getFieldHtml(array(
				'type'     => 'input',
				'name'     => 'default_usps_postcode',
				'value'    => $this->data['default_usps_postcode'],
				'required' => true,
		));

		$domestic = array(0, 1, 2, 3, 4, 5, 6, 7, 12, 13, 16, 17, 18, 19, 22, 23, 25, 27, 28);
		$this->data['form']['fields']['domestic'] = array();

		$options = array();
		foreach($domestic as $i){
			$title = 'domestic_' . $i;
			$name = 'default_usps_domestic_' . $i;
			$this->data['form']['fields']['domestic'][$title] = $form->getFieldHtml(array(
					'type'  => 'checkbox',
					'name'  => $name,
					'style' => 'btn_switch',
					'value' => $this->data[$name],
			));
			$options[$title] = $this->language->get('text_' . $title);
		}


		$this->data['form']['fields']['free_domestic_method'] = $form->getFieldHtml(array(
				'type'    => 'selectbox',
				'name'    => 'default_usps_free_domestic_method',
				'options' => $options,
				'value'   => $this->data['default_usps_free_domestic_method'],
		));

		$international = array(1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 21);
		$this->data['form']['fields']['international'] = array();
		$options = array();
		foreach($international as $i){
			$title = 'international_' . $i;
			$name = 'default_usps_international_' . $i;
			$this->data['form']['fields']['international'][$title] = $form->getFieldHtml(array(
					'type'  => 'checkbox',
					'name'  => $name,
					'style' => 'btn_switch',
					'value' => $this->data[$name],
			));
			$options[$title] = $this->language->get('text_' . $title);
		}
//method of usps for products with free shipping
		$this->data['form']['fields']['free_international_method'] = $form->getFieldHtml(array(
				'type'    => 'selectbox',
				'name'    => 'default_usps_free_international_method',
				'options' => $options,
				'value'   => $this->data['default_usps_free_international_method'],
		));

		$this->data['form']['fields']['size'] = $form->getFieldHtml(array(
				'type'    => 'selectbox',
				'name'    => 'default_usps_size',
				'options' => $sizes,
				'value'   => $this->data['default_usps_size'],
		));
		$this->data['form']['fields']['container'] = $form->getFieldHtml(array(
				'type'    => 'selectbox',
				'name'    => 'default_usps_container',
				'options' => $containers,
				'value'   => $this->data['default_usps_container'],
		));
		$this->data['form']['fields']['machinable'] = $form->getFieldHtml(array(
				'type'    => 'selectbox',
				'name'    => 'default_usps_machinable',
				'options' => array(
						1 => $this->language->get('text_yes'),
						0 => $this->language->get('text_no'),
				),
				'value'   => $this->data['default_usps_machinable'],
		));
		$this->data['form']['fields']['length'] = $form->getFieldHtml(array(
				'type'  => 'input',
				'name'  => 'default_usps_length',
				'value' => $this->data['default_usps_length'],
		));
		$this->data['form']['fields']['width'] = $form->getFieldHtml(array(
				'type'  => 'input',
				'name'  => 'default_usps_width',
				'value' => $this->data['default_usps_width'],
		));
		$this->data['form']['fields']['height'] = $form->getFieldHtml(array(
				'type'  => 'input',
				'name'  => 'default_usps_height',
				'value' => $this->data['default_usps_height'],
		));
		$this->data['form']['fields']['girth'] = $form->getFieldHtml(array(
				'type'  => 'input',
				'name'  => 'default_usps_girth',
				'value' => $this->data['default_usps_girth'],
		));

		$this->data['form']['fields']['display_time'] = $form->getFieldHtml(array(
				'value'   => $this->data['default_usps_display_time'],
				'type'    => 'selectbox',
				'name'    => 'default_usps_display_time',
				'options' => array(
						1 => $this->language->get('text_yes'),
						0 => $this->language->get('text_no'),
				),
		));
		$this->data['form']['fields']['display_weight'] = $form->getFieldHtml(array(
				'type'    => 'selectbox',
				'name'    => 'default_usps_display_weight',
				'value'   => $this->data['default_usps_display_weight'],
				'options' => array(
						1 => $this->language->get('text_yes'),
						0 => $this->language->get('text_no'),
				),
		));
		$this->data['form']['fields']['weight_class'] = $form->getFieldHtml(array(
				'type'    => 'selectbox',
				'name'    => 'default_usps_weight_class',
				'options' => $weight_classes,
				'value'   => $this->data['default_usps_weight_class'],
		));

		$this->load->model('localisation/length_class');
		$results = $this->model_localisation_length_class->getLengthClasses();
		$length_classes = array();
		foreach($results as $k => $v){
			$length_classes[$v['unit']] = $v['title'];
		}
		$this->data['form']['fields']['length_class'] = $form->getFieldHtml(array(
				'type'    => 'selectbox',
				'name'    => 'default_usps_length_class',
				'options' => $length_classes,
				'value'   => $this->data['default_usps_length_class'],
		));
		$this->data['form']['fields']['tax'] = $form->getFieldHtml(array(
				'type'    => 'selectbox',
				'name'    => 'default_usps_tax_class_id',
				'options' => $tax_classes,
				'value'   => $this->data['default_usps_tax_class_id'],
		));
		$this->data['form']['fields']['location'] = $form->getFieldHtml(array(
				'type'    => 'selectbox',
				'name'    => 'default_usps_location_id',
				'options' => $locations,
				'value'   => $this->data['default_usps_location_id'],
		));
		$this->data['form']['fields']['sort_order'] = $form->getFieldHtml(array(
				'type'  => 'input',
				'name'  => 'default_usps_sort_order',
				'value' => $this->data['default_usps_sort_order'],
		));


		//load tabs controller

		$this->data['groups'][] = 'additional_settings';
		$this->data['link_additional_settings'] = '';
		$this->data['active_group'] = 'additional_settings';

		$tabs_obj = $this->dispatch('pages/extension/extension_tabs', array($this->data));
		$this->data['tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$obj = $this->dispatch('pages/extension/extension_summary', array($this->data));
		$this->data['extension_summary'] = $obj->dispatchGetOutput();
		unset($obj);

		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/extension/default_usps.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _validate(){
		if(!$this->user->canModify('extension/default_usps')){
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if(!$this->request->post['default_usps_user_id']){
			$this->error['user_id'] = $this->language->get('error_user_id');
		}

		if(!$this->request->post['default_usps_postcode']){
			$this->error['postcode'] = $this->language->get('error_postcode');
		}

		if(!$this->error){
			return true;
		} else{
			return false;
		}
	}
}
