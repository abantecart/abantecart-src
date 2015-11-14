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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesCatalogAttribute extends AController {
	private $error = array();
    public $data = array();

    public function get_attribute_type() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

	    $am = new AAttribute_Manager();
	    $this->data['attribute_info'] = $am->getAttribute($this->request->get['attribute_id']);

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($this->data['attribute_info']['attribute_type_id']));
	}

	/**
	 * method that return part of attribute form
	 * @internal param array $param
	 * @param array $params
	 */
	public function getProductOptionSubform( $params=array() ){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->data = array_merge($this->data, $params['data']);

		unset($this->data['form']['fields']); // remove form fields that do not needed here

		$this->data['elements_with_options'] = HtmlElementFactory::getElementsWithOptions();

		$results = HtmlElementFactory::getAvailableElements();
		$element_types = array( '' => $this->language->get('text_select') );
		foreach ($results as $key => $type) {
			// allowed field types
			if ( in_array($key,array('I','T','S','M','R','C','G','H','U')) ) {
				$element_types[$key] = $type['type'];
			}
		}
		/**
		 * @var $form AForm
		 */
		$form = $params['aform'];
		$attribute_manager = $params['attribute_manager'];

		$this->data['form']['fields']['element_type'] = $form->getFieldHtml(array(
																				'type' => 'selectbox',
																				'name' => 'element_type',
																				'value' => $this->data['element_type'],
																				'required' => true,
																				'options' => $element_types,
		                                                                          ));
		$this->data['form']['fields']['sort_order'] = $form->getFieldHtml(array(
																				'type' => 'input',
																				'name' => 'sort_order',
																				'value' => $this->data['sort_order'],
																				'style' => 'small-field'
		                                                                        ));
		$this->data['form']['fields']['required'] = $form->getFieldHtml(array(
																				'type' => 'checkbox',
																				'name' => 'required',
																				'value' => $this->data['required'],
																				'style'  => 'btn_switch',
		                                                                      ));
		$this->data['form']['fields']['regexp_pattern'] = $form->getFieldHtml(array(
																				'type' => 'input',
																				'name' => 'regexp_pattern',
																				'value' => $this->data['regexp_pattern'],
																				'style' => 'large-field',
				                                                                  ));
		$this->data['form']['fields']['error_text'] = $form->getFieldHtml(array(
																				'type' => 'input',
																				'name' => 'error_text',
																				'value' => $this->data['error_text'],
																				'style' => 'large-field',
				                                                                  ));
		$this->data['children'] = array();
		//Build atribute values part of the form
		if ( $this->request->get['attribute_id'] ) {

			$this->data['child_count'] = $attribute_manager->totalChildren( $this->request->get['attribute_id'] );
			if ( $this->data['child_count'] > 0) {
				$children_attr = $attribute_manager->getAttributes(array(), 0, $this->request->get['attribute_id']);
				foreach ($children_attr as $attr) {
					$this->data['children'][] = array( 'name' => $attr['name'],
												 'link' => $this->html->getSecureURL('catalog/attribute/update', '&attribute_id=' . $attr['attribute_id']) );
				}
			}

			$attribute_values = $attribute_manager->getAttributeValues( $this->request->get['attribute_id'] );
			foreach ($attribute_values as $atr_val) {
				$atr_val_id = $atr_val['attribute_value_id'];
				$attributes_fields[$atr_val_id]['sort_order'] = $form->getFieldHtml(array(
															'type' => 'input',
															'name' => 'sort_orders['.$atr_val_id.']',
															'value' => $atr_val['sort_order'],
															'style' => 'small-field'
														));
				$attributes_fields[$atr_val_id]['values'] = $form->getFieldHtml(array(
															'type' => 'input',
				                                            'name' => 'values['.$atr_val_id.']',
				                                            'value' => $atr_val['value'],
				                                            'style' => 'medium-field'
				                                        ));
				$attributes_fields[$atr_val_id]['attribute_value_ids'] = $form->getFieldHtml(array(
															'type' => 'hidden',
				                                            'name' => 'attribute_value_ids['.$atr_val_id.']',
				                                            'value' => $atr_val_id,
				                                            'style' => 'medium-field'
				                                        ));
			}
		}
		if ( !$attributes_fields ) {
				$attributes_fields[0]['sort_order'] = $form->getFieldHtml(array(
															'type' => 'input',
															'name' => 'sort_orders[]',
															'value' => '',
															'style' => 'small-field no-save'
														));
				$attributes_fields[0]['values'] = $form->getFieldHtml(array(
															'type' => 'input',
				                                            'name' => 'values[]',
				                                            'value' => '',
				                                            'style' => 'medium-field no-save'
				                                        ));
				$attributes_fields[0]['attribute_value_ids'] = $form->getFieldHtml(array(
															'type' => 'hidden',
				                                            'name' => 'attribute_value_ids['.$atr_val_id.']',
				                                            'value' => 'new',
				                                            'style' => 'medium-field'
				                                        ));
		}

		$this->data['settings'] = !$this->data['settings'] ? array() : $this->data['settings'];

		$this->data['form']['settings_fields'] = array(
			'extensions' => $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'settings[extensions]',
				'value' => (has_value($this->data['settings']['extensions']) ? $this->data['settings']['extensions'] : ''),
				'style' => 'no-save'
			)),
			'min_size' => $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'settings[min_size]',
				'value' => (has_value($this->data['settings']['min_size']) ? $this->data['settings']['min_size'] :''),
				'style' => 'small-field no-save'
			)),
			'max_size' => $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'settings[max_size]',
				'value' => (has_value($this->data['settings']['max_size']) ? $this->data['settings']['max_size'] : ''),
				'style' => 'small-field no-save'
			)),
			'directory' => $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'settings[directory]',
				'value' => (has_value($this->data['settings']['directory']) ? $this->data['settings']['directory'] : ''),
				'style' => 'no-save'
			)),
		);
		$this->data['entry_upload_dir'] = sprintf($this->language->get('entry_upload_dir'),'admin/system/uploads/');

		$this->data['form']['attribute_values'] = $attributes_fields;

		$this->view->batchAssign($this->data);


		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->processTemplate('responses/catalog/global_attribute_product_option_subform.tpl');
	}


	/**
	 * method that return part of attribute form for download attribute
	 * @internal param array $param
	 * @param array $params
	 */
	public function getDownloadAttributeSubform( $params=array() ){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->data = array_merge($this->data, $params['data']);

		unset($this->data['form']['fields']); // remove form fields that do not needed here

		$this->data['elements_with_options'] = HtmlElementFactory::getElementsWithOptions();

		$results = HtmlElementFactory::getAvailableElements();
		$element_types = array( '' => $this->language->get('text_select') );
		foreach ($results as $key => $type) {
			// allowed field types
			if ( in_array($key,array('I','T','S','M','R','C')) ) {
				$element_types[$key] = $type['type'];
			}
		}

		$form = $params['aform'];
		$attribute_manager = $params['attribute_manager'];

		$this->data['form']['fields']['element_type'] = $form->getFieldHtml(
						array(
							'type' => 'selectbox',
							'name' => 'element_type',
							'value' => $this->data['element_type'],
							'required' => true,
							'options' => $element_types,
						));
		$this->data['form']['fields']['sort_order'] = $form->getFieldHtml(
						array(
						  	'type' => 'input',
						  	'name' => 'sort_order',
						  	'value' => $this->data['sort_order'],
						  	'style' => 'small-field'
						));
		$this->data['form']['fields']['show_to_customer'] = $form->getFieldHtml(
						array(
						    'type' => 'checkbox',
						    'name' => 'settings[show_to_customer]',
						    'value' => 1,
						    'checked' => ($this->data['settings'] && $this->data['settings']['show_to_customer'] ? true : false),
						  	'style'  => 'btn_switch',
						));
						
		//Build atribute values part of the form
		if ( $this->request->get['attribute_id'] ) {

			$this->data['child_count'] = $attribute_manager->totalChildren( $this->request->get['attribute_id'] );
			if ( $this->data['child_count'] > 0) {
				$children_attr = $attribute_manager->getAttributes(array(), 0, $this->request->get['attribute_id']);
				foreach ($children_attr as $attr) {
					$this->data['children'][] = array( 'name' => $attr['name'],
												 'link' => $this->html->getSecureURL('catalog/attribute/update', '&attribute_id=' . $attr['attribute_id']) );
				}
			}

			$attribute_values = $attribute_manager->getAttributeValues( $this->request->get['attribute_id'] );
			foreach ($attribute_values as $atr_val) {
				$atr_val_id = $atr_val['attribute_value_id'];
				$attributes_fields[$atr_val_id]['sort_order'] = $form->getFieldHtml(array(
															'type' => 'input',
															'name' => 'sort_orders['.$atr_val_id.']',
															'value' => $atr_val['sort_order'],
															'style' => 'small-field'
														));
				$attributes_fields[$atr_val_id]['values'] = $form->getFieldHtml(array(
															'type' => 'input',
				                                            'name' => 'values['.$atr_val_id.']',
				                                            'value' => $atr_val['value'],
				                                            'style' => 'medium-field'
				                                        ));
				$attributes_fields[$atr_val_id]['attribute_value_ids'] = $form->getFieldHtml(array(
															'type' => 'hidden',
				                                            'name' => 'attribute_value_ids['.$atr_val_id.']',
				                                            'value' => $atr_val_id,
				                                            'style' => 'medium-field'
				                                        ));
			}
		}
		if ( !$attributes_fields ) {
				$attributes_fields[0]['sort_order'] = $form->getFieldHtml(array(
															'type' => 'input',
															'name' => 'sort_orders[]',
															'value' => '',
															'style' => 'small-field no-save'
														));
				$attributes_fields[0]['values'] = $form->getFieldHtml(array(
															'type' => 'input',
				                                            'name' => 'values[]',
				                                            'value' => '',
				                                            'style' => 'medium-field no-save'
				                                        ));
				$attributes_fields[0]['attribute_value_ids'] = $form->getFieldHtml(array(
															'type' => 'hidden',
				                                            'name' => 'attribute_value_ids['.$atr_val_id.']',
				                                            'value' => 'new',
				                                            'style' => 'medium-field'
				                                        ));
		}

		$this->data['form']['attribute_values'] = $attributes_fields;

		$this->view->batchAssign($this->data);


		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->processTemplate('responses/catalog/global_attribute_product_option_subform.tpl');
	}
}