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
/** @noinspection PhpUndefinedClassInspection */
class ControllerResponsesProductProduct extends AController {
	private $error = array();
	public $data = array();
	/**
	 * @var AAttribute_Manager
	 */
	private $attribute_manager;

	public function products() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->loadModel('catalog/product');
		if (isset($this->request->post[ 'coupon_product' ])) {
			$products = $this->request->post[ 'coupon_product' ];
		} else {
			$products = array();
		}

		$product_data = array();
		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			if ($product_info) {
				$product_data[ ] = array(
					'product_id' => $product_info[ 'product_id' ],
					'name' => $product_info[ 'name' ],
					'model' => $product_info[ 'model' ],
					'sort_order' => (int)$product_info[ 'sort_order' ]
				);
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($product_data));
	}

	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('product/product')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'product/product'),
					'reset_value' => true
				));
		}

		$this->loadLanguage('catalog/product');
		$this->loadModel('catalog/product');

		if ($this->request->is_POST()) {
			$this->model_catalog_product->updateProduct($this->request->get[ 'product_id' ], $this->request->post);
			$result = 'Saved!';
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->response->setOutput($result);
	}

	public function category() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('catalog/product');
		$promoton = new APromotion($this->request->get[ 'customer_group_id' ]);

		if (isset($this->request->get[ 'category_id' ])) {
			$category_id = $this->request->get[ 'category_id' ];
		} else {
			$category_id = 0;
		}

		$product_data = array();
		$results = $this->model_catalog_product->getProductsByCategoryId($category_id);
		foreach ($results as $result) {

			$discount = $promoton->getProductDiscount($result[ 'product_id' ]);
			if ($discount) {
				$price = $discount;
			} else {
				$price = $result[ 'price' ];
				$special = $promoton->getProductSpecial($result[ 'product_id' ]);
				if ($special) {
					$price = $special;
				}
			}

			if (!empty($this->request->get[ 'currency' ]) && !empty($this->request->get[ 'value' ])) {
				$price = $this->currency->format((float)$price, $this->request->get[ 'currency' ], $this->request->get[ 'value' ]);
			} else {
				$price = $this->currency->format((float)$price);
			}

			$product_data[ ] = array(
				'product_id' => $result[ 'product_id' ],
				'name' => $result[ 'name' ],
				'model' => $result[ 'model' ],
				'price' => $price,
			);
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($product_data));
	}

	public function product_categories() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('catalog/category');

		if (isset($this->request->post[ 'id' ])) { // variant for popup listing
			$categories = $this->request->post[ 'id' ];
		} else {
			$categories = array();
		}
		$category_data = array();

		foreach ($categories as $category_id) {
			$category_data[ ] = array(
				'id' => $category_id,
				'name' => $this->model_catalog_category->getPath($category_id),
				'sort_order' => 0
			);

		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($category_data));
	}

	public function related() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('catalog/product');

		if (isset($this->request->post[ 'product_related' ])) {
			$products = $this->request->post[ 'product_related' ];
		} elseif (isset($this->request->post[ 'id' ])) { // variant for popup listing
			$products = $this->request->post[ 'id' ];
		} else {
			$products = array();
		}
		$product_data = array();

		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				$product_data[ ] = array(
					'id' => $product_info[ 'product_id' ],
					'product_id' => $product_info[ 'product_id' ],
					'name' => $product_info[ 'name' ],
					'model' => $product_info[ 'model' ],
					'sort_order' => 0
				);
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($product_data));
	}

	public function get_options_list() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('catalog/product');
		$product_options = $this->model_catalog_product->getProductOptions($this->request->get[ 'product_id' ]);

		$result = array();
		foreach ($product_options as $option) {
			$option_name = trim($option[ 'language' ][ $this->language->getContentLanguageID() ][ 'name' ]);
			$result[ $option[ 'product_option_id' ] ] = $option_name ? $option_name : 'n/a';
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($result));
	}

	public function update_option() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('product/product')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'product/product'),
					'reset_value' => true
				));
		}
		//needs to validate attribute properties
		// first - prepare data for validation
		if (!isset($this->request->get[ 'required' ])) {
			$this->request->get[ 'required' ] = 0;
		}

		if (has_value($this->request->get['regexp_pattern'])) {
			$this->request->get['regexp_pattern'] = trim($this->request->get['regexp_pattern']);
		}

		$this->loadModel('catalog/product');

		$data = $this->request->get;
		$attribute_manager = new AAttribute_Manager('product_option');
		$option_info = $this->model_catalog_product->getProductOption($this->request->get[ 'product_id' ],$this->request->get[ 'option_id' ]);
		$data['element_type'] = $option_info['element_type'];
		$data['attribute_type_id'] = $attribute_manager->getAttributeTypeID('product_option');

		$errors = $attribute_manager->validateAttributeCommonData($data);
		if(!$errors){
			$this->model_catalog_product->updateProductOption($this->request->get[ 'option_id' ], $this->request->get);
		}else{
			$error = new AError('');
			return $error-> toJSONResponse('',
				array( 'error_title' => implode('<br>',$errors)		));
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function load_option() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('catalog/product');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->loadModel('catalog/product');

		$this->view->assign('success', $this->session->data[ 'success' ]);
		unset($this->session->data[ 'success' ]);

		$this->data[ 'option_data' ] = $this->model_catalog_product->getProductOption(
			$this->request->get[ 'product_id' ],
			$this->request->get[ 'option_id' ]
		);

		$this->data[ 'language_id' ] = $this->language->getContentLanguageID();
		$this->data[ 'element_types' ] = HtmlElementFactory::getAvailableElements();
		$this->data[ 'elements_with_options' ] = HtmlElementFactory::getElementsWithOptions();
		$this->data[ 'selectable' ] = in_array($this->data[ 'option_data' ][ 'element_type' ], $this->data[ 'elements_with_options' ]) ? 1 : 0;
		$this->data[ 'option_type' ] = $this->data[ 'element_types' ][ $this->data[ 'option_data' ][ 'element_type' ] ][ 'type' ];

		$this->attribute_manager = new AAttribute_Manager('product_option');

		$this->data[ 'action' ] = $this->html->getSecureURL('product/product/update_option_values', '&product_id=' . $this->request->get[ 'product_id' ] . '&option_id=' . $this->request->get[ 'option_id' ]);

		$this->data[ 'option_values' ] = $this->model_catalog_product->getProductOptionValues(
			$this->request->get[ 'product_id' ],
			$this->request->get[ 'option_id' ]
		);

		$this->data[ 'option_name' ] = $this->html->buildInput(array(
			'name' => 'name',
			'value' => $this->data[ 'option_data' ][ 'language' ][ $this->data[ 'language_id' ] ][ 'name' ],
		));

		if(in_array($this->data[ 'option_data' ][ 'element_type' ],HtmlElementFactory::getElementsWithPlaceholder())){
			$this->data[ 'option_placeholder' ] = $this->html->buildInput(array(
				'name' => 'option_placeholder',
				'value' => $this->data[ 'option_data' ][ 'language' ][ $this->data[ 'language_id' ] ][ 'option_placeholder' ],
			));
		}

		$this->data[ 'status' ] = $this->html->buildCheckbox(array(
			'type' => 'checkbox',
			'name' => 'status',
			'value' => $this->data[ 'option_data' ][ 'status' ],
			'style' => 'btn_switch',
		));
		$this->data[ 'option_sort_order' ] = $this->html->buildInput(array(
			'type' => 'input',
			'name' => 'sort_order',
			'value' => $this->data[ 'option_data' ][ 'sort_order' ],
			'style' => 'small-field'
		));
		$this->data[ 'required' ] = $this->html->buildCheckbox(array(
			'type' => 'checkbox',
			'name' => 'required',
			'value' => $this->data[ 'option_data' ][ 'required' ],
		));

		$this->data[ 'option_regexp_pattern' ] = $this->html->buildInput(array(
			'type' => 'input',
			'name' => 'regexp_pattern',
			'value' => $this->data[ 'option_data' ][ 'regexp_pattern' ],
			'style' => 'medium-field'
		));

		$this->data[ 'option_error_text' ] = $this->html->buildInput(array(
			'type' => 'input',
			'name' => 'error_text',
			'value' => $this->data[ 'option_data' ][ 'language' ][ $this->data[ 'language_id' ] ][ 'error_text' ],
			'style' => 'large-field'
		));

		$this->data[ 'button_remove_option' ] = $this->html->buildButton(array(
			'text' => $this->language->get('button_remove_option'),
			'style' => 'button3',
		));
		$this->data[ 'button_save' ] = $this->html->buildButton(array(
			'text' => $this->language->get('button_save'),
			'style' => 'button1',
		));
		$this->data[ 'button_reset' ] = $this->html->buildButton(array(
			'text' => $this->language->get('button_reset'),
			'style' => 'button2',
		));
		$this->data[ 'button_remove' ] = $this->html->buildButton(array(
			'text' => $this->language->get('button_remove'),
			'style' => 'button3',
		));

		$this->data[ 'update_option_values' ] = $this->html->getSecureURL('product/product/update_option_values', '&product_id=' . $this->request->get[ 'product_id' ] . '&option_id=' . $this->request->get[ 'option_id' ]);
		$this->data[ 'remove_option' ] = $this->html->getSecureURL('product/product/del_option', '&product_id=' . $this->request->get[ 'product_id' ] . '&option_id=' . $this->request->get[ 'option_id' ]);
		// form of option values list
		$form = new AForm('HT');
		$form->setForm(array( 'form_name' => 'update_option_values' ));
		$this->data[ 'form' ][ 'id' ] = 'update_option_values';
		$this->data[ 'update_option_values_form' ][ 'open' ] = $form->getFieldHtml(array(
			'type' => 'form',
			'name' => 'update_option_values',
			'attr' => 'confirm-exit="true"',
			'action' => $this->data[ 'update_option_values' ] ));

		//form of option
		$form = new AForm('HT');
		$form->setForm(array(
			'form_name' => 'option_value_form',
		));

		$this->data[ 'form' ][ 'id' ] = 'option_value_form';
		$this->data[ 'form' ][ 'form_open' ] = $form->getFieldHtml(array(
			'type' => 'form',
			'name' => 'option_value_form',
			'attr' => 'confirm-exit="true"',
			'action' => $this->data[ 'update_option_values' ]
		));

		//Load option values rows
		foreach ($this->data[ 'option_values' ] as $key => $item) {
			$this->request->get[ 'product_option_value_id' ] = $item[ 'product_option_value_id' ];
			$this->data[ 'option_values' ][ $key ][ 'row' ] = $this->_option_value_form($form);
		}

		$this->data[ 'new_option_row' ] = '';
		if (in_array($this->data[ 'option_data' ][ 'element_type' ], $this->data[ 'elements_with_options' ])) {
			$this->request->get[ 'product_option_value_id' ] = null;
			$this->data[ 'new_option_row' ] = $this->_option_value_form($form);
		}


		$this->view->batchAssign($this->data);

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->processTemplate('responses/product/option_values.tpl');
	}

	public function del_option() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('product/product')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'product/product'),
					'reset_value' => true
				));
		}

		$this->loadLanguage('catalog/product');
		$this->loadModel('catalog/product');
		$this->model_catalog_product->deleteProductOption($this->request->get[ 'product_id' ], $this->request->get[ 'option_id' ]);
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		$this->response->setOutput($this->language->get('text_option_removed'));
	}

	public function update_option_values() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('product/product')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'product/product'),
					'reset_value' => true
				));
		}

		$this->loadLanguage('catalog/product');
		$this->loadModel('catalog/product');
		$this->model_catalog_product->updateProductOptionValues($this->request->get[ 'product_id' ], $this->request->get[ 'option_id' ], $this->request->post);
		$this->session->data[ 'success' ] = $this->language->get('text_success_option');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->redirect($this->html->getSecureURL('product/product/load_option', '&product_id=' . $this->request->get[ 'product_id' ] . '&option_id=' . $this->request->get[ 'option_id' ]));
	}

	/**
	 * @param $form AForm
	 * @return string
	 */
	private function _option_value_form($form) {
		$this->data[ 'option_attribute' ] = $this->attribute_manager->getAttributeByProductOptionId($this->request->get[ 'option_id' ]);
		$this->data[ 'option_attribute' ][ 'values' ] = '';
		$this->data[ 'option_attribute' ][ 'type' ] = 'input';
		$product_option_value_id = $this->request->get[ 'product_option_value_id' ];
		$group_attribute = array();
		if ($this->data[ 'option_attribute' ][ 'attribute_id' ]) {
			$group_attribute = $this->attribute_manager->getAttributes(array(), $this->data[ 'language_id' ], $this->data[ 'option_attribute' ][ 'attribute_id' ]);
		}

		$this->data[ 'elements_with_options' ] = HtmlElementFactory::getElementsWithOptions();
		//load values for attributes with options
		if (count($group_attribute)) {
			$this->data[ 'option_attribute' ][ 'group' ] = array();
			foreach ($group_attribute as $attribute) {
				$option_id = $attribute[ 'attribute_id' ];

				$this->data[ 'option_attribute' ][ 'group' ][ $option_id ][ 'name' ] = $attribute[ 'name' ];
				$this->data[ 'option_attribute' ][ 'group' ][ $option_id ][ 'type' ] = 'hidden';
				if (in_array($attribute[ 'element_type' ], $this->data[ 'elements_with_options' ])) {
					$this->data[ 'option_attribute' ][ 'group' ][ $option_id ][ 'type' ] = 'selectbox';
					$values = $this->attribute_manager->getAttributeValues($attribute[ 'attribute_id' ], $this->language->getContentLanguageID());

					foreach ($values as $v) {
						$this->data[ 'option_attribute' ][ 'group' ][ $option_id ][ 'values' ][ $v[ 'attribute_value_id' ] ] = addslashes(html_entity_decode($v[ 'value' ], ENT_COMPAT, 'UTF-8'));
					}
				}
			}

		} else {
			if (in_array($this->data[ 'option_attribute' ][ 'element_type' ], $this->data[ 'elements_with_options' ])) {
				$this->data[ 'option_attribute' ][ 'type' ] = 'selectbox';
				if(is_null($product_option_value_id)){ // for new row values
					$values = $this->attribute_manager->getAttributeValues(
						$this->data[ 'option_attribute' ][ 'attribute_id' ],
						$this->language->getContentLanguageID()
					);
				}else{
					$values = $this->getProductOptionValues(
						$this->data[ 'option_attribute' ][ 'attribute_id' ],
						$this->language->getContentLanguageID()
					);
				}

				foreach ($values as $v) {
					$this->data[ 'option_attribute' ][ 'values' ][ $v[ 'attribute_value_id' ] ] = addslashes(html_entity_decode($v[ 'value' ], ENT_COMPAT, 'UTF-8'));
				}
			}

		}

		$this->data[ 'cancel' ] = $this->html->getSecureURL('product/product/load_option', '&product_id=' . $this->request->get[ 'product_id' ] . '&option_id=' . $this->request->get[ 'option_id' ]);

		if (isset($this->request->get[ 'product_option_value_id' ])) {
			$this->data[ 'row_id' ] = 'row' . $product_option_value_id;
			$this->data[ 'attr_val_id' ] = $product_option_value_id;
			$item_info = $this->model_catalog_product->getProductOptionValue($this->request->get[ 'product_id' ], $product_option_value_id);
		} else {
			$this->data[ 'row_id' ] = 'new_row';
		}

		$fields = array( 'default','name', 'sku', 'quantity', 'subtract', 'price', 'prefix', 'sort_order', 'weight', 'weight_type', 'attribute_value_id', 'children_options' );
		foreach ($fields as $f) {
			if (isset($this->request->post[ $f ])) {
				$this->data[ $f ] = $this->request->post[ $f ];
			} elseif (isset($item_info)) {
				$this->data[ $f ] = $item_info[ $f ];
			} else {
				$this->data[ $f ] = '';
			}
		}


		if (isset($this->request->post[ 'name' ])) {
			$this->data[ 'name' ] = $this->request->post[ 'name' ];
		} elseif (isset($item_info)) {
			$this->data[ 'name' ] = $item_info[ 'language' ][ $this->language->getContentLanguageID() ][ 'name' ];
		}


		if (isset($this->data[ 'option_attribute' ][ 'group' ])) {
			//process grouped (parent/chiled) options
			$this->data[ 'form' ][ 'fields' ][ 'option_value' ] = '';
			foreach ($this->data[ 'option_attribute' ][ 'group' ] as $attribute_id => $data) {
				$this->data[ 'form' ][ 'fields' ][ 'option_value' ] .= '<span style="white-space: nowrap;">' . $data[ 'name' ] . '' . $form->getFieldHtml(array(
					'type' => $data[ 'type' ],
					'name' => 'attribute_value_id[' . $product_option_value_id . '][' . $attribute_id . ']',
					'value' => $this->data[ 'children_options' ][ $attribute_id ],
					'options' => $data[ 'values' ],
					'attr' => ''
				)).'<span><br class="clr_both">';

			}
		} else {
			if (in_array($this->data[ 'option_attribute' ][ 'element_type' ], $this->data[ 'elements_with_options' ])) {
				$this->data[ 'form' ][ 'fields' ][ 'option_value' ] = $form->getFieldHtml(array(
					'type' => $this->data[ 'option_attribute' ][ 'type' ],
					'name' => 'attribute_value_id[' . $product_option_value_id . ']',
					'value' => $this->data[ 'attribute_value_id' ],
					'options' => $this->data[ 'option_attribute' ][ 'values' ],
				));
			} else if ( $this->data[ 'option_attribute' ][ 'element_type' ] == 'U') {
				//for file there is no option value 	
				$attribute_id = $this->data['option_attribute']['attribute_id'];
				$edit_url = $this->html->getSecureURL('catalog/attribute/update', '&attribute_id=' . $attribute_id);
				$this->data[ 'form' ][ 'fields' ][ 'option_value' ] = '<span link="'.$edit_url.'" class="open_newtab pointer">'.$this->language->get('text_edit').'</span>';
			} else {
				$this->data[ 'form' ][ 'fields' ][ 'option_value' ] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'name[' . $product_option_value_id . ']',
					'value' => $this->data[ 'name' ],
				));
			}
		}

		$this->data[ 'form' ][ 'fields' ][ 'product_option_value_id' ] = $form->getFieldHtml(array(
			'type' => 'hidden',
			'name' => 'product_option_value_id[' . $product_option_value_id . ']',
			'value' => $product_option_value_id,
		));

		if (in_array($this->data[ 'option_data' ][ 'element_type' ], $this->data[ 'elements_with_options' ])) {
			$this->data[ 'form' ][ 'fields' ][ 'default' ] = $form->getFieldHtml(array(
				'type' => 'radio',
				'name' => 'default',
				'id' => 'default_'.$product_option_value_id,
				'value' => ($this->data[ 'default' ] ? $product_option_value_id : ''),
				'options' => array($product_option_value_id=>''),
			));
			$this->data[ 'with_default' ] = 1;
		}

		$this->data[ 'form' ][ 'fields' ][ 'sku' ] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'sku[' . $product_option_value_id . ']',
			'value' => $this->data[ 'sku' ],
		));
		$this->data[ 'form' ][ 'fields' ][ 'quantity' ] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'quantity[' . $product_option_value_id . ']',
			'value' => $this->data[ 'quantity' ],
			'style' => 'small-field',
		));
		$this->data[ 'form' ][ 'fields' ][ 'subtract' ] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'subtract[' . $product_option_value_id . ']',
			'value' => $this->data[ 'subtract' ],
			'options' => array(
				1 => $this->language->get('text_yes'),
				0 => $this->language->get('text_no'),
			),
		));
		$this->data[ 'form' ][ 'fields' ][ 'price' ] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'price[' . $product_option_value_id . ']',
			'value' => moneyDisplayFormat($this->data[ 'price' ]),
			'style' => 'small-field'
		));

		$this->data[ 'prefix' ] = trim($this->data[ 'prefix' ]);
		$currency_symbol = $this->currency->getCurrency($this->config->get('config_currency'));
		$currency_symbol = $currency_symbol[ 'symbol_left' ] . $currency_symbol[ 'symbol_right' ];
		if (!$this->data[ 'prefix' ]) {
			$this->data[ 'prefix' ] = $currency_symbol;
		}

		$this->data[ 'form' ][ 'fields' ][ 'prefix' ] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'prefix[' . $product_option_value_id . ']',
			'value' => $this->data[ 'prefix' ],
			'options' => array(
				'$' => $currency_symbol,
				'%' => '%',
			),
		));
		$this->data[ 'form' ][ 'fields' ][ 'sort_order' ] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'sort_order[' . $product_option_value_id . ']',
			'value' => $this->data[ 'sort_order' ],
			'style' => 'small-field'
		));
		$this->data[ 'form' ][ 'fields' ][ 'weight' ] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'weight[' . $product_option_value_id . ']',
			'value' => $this->data[ 'weight' ],
			'style' => 'small-field'
		));

		//build available weight units for options
		$wht_options = array( '%' => '%');
		$this->loadModel('localisation/weight_class');
		$selected_unit = trim($this->data[ 'weight_type' ]);
		$prd_info = $this->model_catalog_product->getProduct( $this->request->get[ 'product_id' ] );
		$prd_weight_info = $this->model_localisation_weight_class->getWeightClass($prd_info['weight_class_id']);
		$wht_options[ $prd_weight_info['unit'] ] = $prd_weight_info[ 'title' ];
				
		if (empty($selected_unit)) {
			//no weight yet, use product weight unit as default 
			$selected_unit = trim($prd_weight_info['unit']);
		} else if ( $selected_unit != trim($prd_weight_info['unit']) && $selected_unit != '%' ) {
			//main product type has changed. Show what weight unit we have in option
			$weight_info = $this->model_localisation_weight_class->getWeightClassDescriptionByUnit( $selected_unit );	
			$wht_options[ $selected_unit ] = $weight_info[ 'title' ];
		}
		$this->data[ 'form' ][ 'fields' ][ 'weight_type' ] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'weight_type[' . $product_option_value_id . ']',
			'value' => $selected_unit,
			'options' => $wht_options
		));

		$this->view->batchAssign($this->data);
		return $this->view->fetch('responses/product/option_value_row.tpl');
	}


	/**
	 * @param int $attribute_id
	 * @param int $language_id
	 * @return array
	 */
	public function getProductOptionValues($attribute_id, $language_id = 0) {
        if ( !$language_id ) {
            $language_id = $this->language->getContentLanguageID();
        }
        $query = $this->db->query( "SELECT pov.*, povd.name as value
									FROM ".$this->db->table('product_options')." po
									LEFT JOIN ".$this->db->table('product_option_values')." pov ON po.product_option_id = pov.product_option_id
									LEFT JOIN ".$this->db->table('product_option_value_descriptions')." povd
										ON ( pov.product_option_value_id = povd.product_option_value_id AND povd.language_id = '" . (int)$language_id . "' )
									WHERE po.attribute_id = '" . $this->db->escape( $attribute_id ) . "'
									ORDER BY pov.sort_order" );
	    return $query->rows;
	}


	public function processDownloadForm(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('product/product')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'product/product'),
					'reset_value' => true
				));
		}

		if(!$this->request->is_POST()){ return null;}

		$this->loadModel('catalog/download');
		if($this->_validateDownloadForm($this->request->post)){
			$post_data = $this->request->post;
			$post_data['filename'] = (string)$this->request->post['download_rl_path_'.$this->request->post['download_id']];
			// for shared downloads
			if(!isset($post_data['shared']) && !$this->request->get['product_id']){
				$post_data['shared'] = 1;
			}

			if((int)$this->request->post['download_id']){
				$this->model_catalog_download->editDownload($this->request->post['download_id'],$post_data);
				$download_id = (int)$this->request->post['download_id'];
			}else{
				unset($post_data['download_id']);
				$post_data['product_id'] = (int)$this->request->get['product_id'];
				$download_id = $this->model_catalog_download->addDownload($post_data);
			}
			$this->session->data['success'] = $this->language->get('text_success_download_save');
			$this->data['output'] = array('download_id' => $download_id,
										  'success'=>true,
											'text'=> $this->language->get('text_success'));

		}else{
			$error = new AError( '' );
			$err_data = array(
				'error_title' => implode( '<br>', $this->error )
			);
			return $error->toJSONResponse('VALIDATION_ERROR_406', $err_data );
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->response->addJSONHeader();
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($this->data['output']));
	}

	/**
	 * @param array $file_data
	 * @param string $tpl
	 */
	public function buildDownloadForm($file_data, $tpl) {
		$this->data = array();
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('catalog/files');
		$this->loadModel('localisation/order_status');
		$this->loadModel('catalog/download');

		$product_id = $file_data['product_id'];

		$order_statuses = $this->model_localisation_order_status->getOrderStatuses();

		$this->data['date_added'] = dateISO2Display($file_data['date_added'], $this->language->get('date_format_short').' '.$this->language->get('time_format'));
		$this->data['date_modified'] = dateISO2Display($file_data['date_modified'], $this->language->get('date_format_short').' '.$this->language->get('time_format'));

		$this->data['action'] = $this->html->getSecureURL('r/product/product/processDownloadForm', '&product_id=' . $product_id);
		$this->data['form_title'] = $this->language->get('text_edit') . '&nbsp;' . $this->language->get('text_product');

		$this->data['download_id'] = (int)$file_data['download_id'];

		if($this->data['download_id']){
			$form = new AForm('HS');
			$this->data[ 'update' ] = $this->html->getSecureURL('listing_grid/download/update_field', '&id=' . $this->data['download_id']);
		}else{
			$form = new AForm('HT');
		}

		$form->setForm(array(
					'form_name' => 'downloadFrm'.$file_data['download_id'],
					'update' => $this->data['update'],
				));
		$this->data['form']['form_open'] = $form->getFieldHtml(array(
					'type' => 'form',
					'name' => 'downloadFrm'.$file_data['download_id'],
					'attr' => 'confirm-exit="true"',
					'action' => $this->data['action']
				));
		$this->data[ 'form' ][ 'submit' ] = $form->getFieldHtml(array(
																	 'type' => 'button',
																	 'name' => 'submit',
																	 'text' => ((int)$this->data['download_id'] ? $this->language->get('button_save'): $this->language->get('text_add')),
																	 'style' => 'button1',
																));

		$this->data[ 'form' ][ 'cancel' ] = $form->getFieldHtml(array(
																	 'type' => 'button',
																	 'name' => 'cancel',
																	 'href' => $this->html->getSecureURL('catalog/product_files','&product_id='.$product_id),
																	 'text' => $this->language->get('button_cancel'),
																	 'style' => 'button2',
																));
		$rl = new AResource('download');
		$rl_dir = $rl->getTypeDir();
		$resource_id = $rl->getIdFromHexPath(str_replace($rl_dir,'',$file_data['filename']));

		$resource_info = $rl->getResource($resource_id);
		$thumbnail = $rl->getResourceThumb($resource_id,30, 30);
		if($resource_info['resource_path']){
			$this->data[ 'icon' ] = $this->html->buildResourceImage(
														array('url' => $thumbnail,
															'width' => 30,
															'height' => 30,
															'attr' => 'alt="' . $resource_info['title'] . '"') );
		}else{
			$this->data[ 'icon' ] = $resource_info['resource_code'];
		}
		if($resource_id){
			$this->data['preview']['href'] = $this->html->getSecureURL('common/resource_library/get_resource_preview','&resource_id='.$resource_id, true);
			$this->data['preview']['path'] = 'resources/'.$file_data['filename'];
		}

		$r = $this->dispatch(
					'responses/common/resource_library/get_resource_html_single',
				array('type'=>'download',
					  'wrapper_id' => 'download_'.(int)$this->data['download_id'],
					  'resource_id'=> $resource_id,
					  'field' => 'download_rl_path_'.$this->data['download_id'] ));
		$this->data['resource'] = $r->dispatchGetOutput();

		$resources_scripts = $this->dispatch(
				   'responses/common/resource_library/get_resources_scripts',
					array(
						'object_name' => 'downloads',
						'object_id' => (int)$this->data['download_id'],
						'types' => 'download',
						'mode' => 'url'
					)
				);
		$this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();

		$this->data['form']['fields']['download_rl_path'] = $form->getFieldHtml(
		            array(
		                'type' => 'hidden',
		                'name' => 'download_rl_path_'.$this->data['download_id'],
		                'value' => htmlspecialchars($file_data['filename'], ENT_COMPAT, 'UTF-8'),
		));

		$this->data['form']['fields']['status'] = $form->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'status',
					'value' => 1,
					'checked' => $file_data['status'] ? true : false,
					'style' => 'btn_switch',
		));
		$orders_count = $this->model_catalog_download->getTotalOrdersWithProduct($product_id);
		if($orders_count){
			$this->data['push_to_customers'] = $this->html->buildElement(
					array(
						'type' => 'button',
						'name' => 'push_to_customers',
						'title' => sprintf($this->language->get('text_push_to_orders'),
											$orders_count),
						'text'=> $this->language->get('text_push'),
						'href' => $this->html->getSecureURL('r/product/product/pushToCustomers',
															'&product_id='.$product_id.'&download_id='.$this->data['download_id']),
						'style' => 'button2',
						'attr' => 'data-orders-count="'.$orders_count.'"'));
		}


		$this->data['maplist'] = array();
		$file_data['map_list'] = (array)$file_data['map_list'];
		foreach($file_data['map_list'] as $map_id => $map_name){
			if($map_id==$product_id){ continue;}
			$this->data['maplist'][] = array('href'=> $this->html->getSecureURL('catalog/product_files','&product_id='.$map_id.'&download_id='.$this->data['download_id'], true),
											 'text' => $map_name);
		}
		if(!sizeof($this->data['maplist'])){
			$this->data['already_shared'] = false;
		}else{
			$this->data['already_shared'] = true;
		}

		$this->data['delete_unmap_href'] = $this->html->getSecureURL('catalog/product_files','&act='.($file_data['shared']? 'unmap' : 'delete').'&product_id='.$product_id.'&download_id='.$this->data['download_id'], true);

		$this->data['form']['fields']['shared'] = $form->getFieldHtml(array(
							'type' => 'checkbox',
							'name' => 'shared',
							'value' => $file_data['shared'],
							'attr'=> ($this->data['already_shared'] ? ' disabled=disabled':'')
		));

		if($file_data['shared']){
			$this->data['text_attention_shared'] = $this->language->get('attention_shared');
		}

		$this->data['form']['fields']['download_id'] = $form->getFieldHtml(array(
			'type' => 'hidden',
			'name' => 'download_id',
			'value' => $this->data['download_id'],
		));
		$this->data['form']['fields']['name'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'name',
			'value' => $file_data['name'],
			'attr' => ' maxlength="64" '
		));
		$this->data['form']['fields']['mask'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'mask',
			'value' => $file_data['mask'],
		));
		$this->data['form']['fields']['max_downloads'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'max_downloads',
			'value' => $file_data['max_downloads'],
			'style' => 'small-field'
		));
		$this->data['form']['fields']['activate'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'activate',
			'value' => $file_data['activate'],
			'options' => array( '' => $this->language->get('text_select'),
								'before_order' => $this->language->get('text_before_order'),
								'immediately' => $this->language->get('text_immediately'),
								'order_status' => $this->language->get('text_on_order_status'),
								'manually' => $this->language->get('text_manually'), ),
			'required' => true,
			'style' => 'download_activate no-save'
		));

		$options = array('' => $this->language->get('text_select'));
		foreach($order_statuses as $order_status){
			$options[$order_status['order_status_id']] = $order_status['name'];
		}

		$this->data['form']['fields']['order_statuses'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'activate_order_status_id',
			'value' => $file_data['activate_order_status_id'],
			'options' => $options,
			'required' => true,
			'style' => 'no-save'
		));

		$this->data['form']['fields']['sort_order'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'sort_order',
			'style' => 'small-field',
			'value' => $file_data['sort_order'],
		));
		$this->data['form']['fields']['expire_days'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'expire_days',
			'style' => 'small-field',
			'value' => $file_data['expire_days'],
		));

		/*
		 * DOWNLOAD ATTRIBUTES PIECE OF FORM
		 * */
		$attributes = $this->model_catalog_download->getDownloadAttributes($this->data['download_id']);
		$elements = HtmlElementFactory::getAvailableElements();


		$html_multivalue_elements = HtmlElementFactory::getMultivalueElements();
		$html_elements_with_options = HtmlElementFactory::getElementsWithOptions();
		if(!$attributes){
			$attr_mng = new AAttribute_Manager('download_attribute');
			$attr_type_id = $attr_mng->getAttributeTypeID('download_attribute');
			$this->data['text_no_download_attributes_yet'] = sprintf($this->language->get('text_no_download_attributes_yet'),
																	 $this->html->getSecureURL('catalog/attribute/insert',
																		                        '&attribute_type_id='.$attr_type_id));
		}else{
			foreach ($attributes as $attribute) {
				$html_type = $elements[$attribute['element_type']]['type'];
				if (!$html_type || !$attribute['status']) {
					continue;
				}
				$values = $value = array();
				//values that was setted
				if (in_array($attribute['element_type'], $html_elements_with_options) && $attribute['element_type']!='R') {

					if(is_array($attribute['selected_values'])){
						foreach ($attribute['selected_values'] as $val) {
							$value[$val] = $val;
						}
					}else{
						$value = $attribute['selected_values'];
					}
				} else {
					if (isset($attribute['selected_values'])) {
						$value = $attribute['selected_values'];
						if($attribute['element_type']=='R' && is_array($value)){
							$value = current($value);
						}

					} else {
						$value = $attribute['values'][0]['value'];
					}
				}
				// possible values
				foreach ($attribute['values'] as $val) {
					$values[$val['attribute_value_id']] = $val['value'];
				}

				if (!in_array($attribute['element_type'], $html_multivalue_elements)) {
					$option_name = 'attributes['.$this->data['download_id'].'][' . $attribute['attribute_id'] . ']';
				} else {
					$option_name = 'attributes['.$this->data['download_id'].'][' . $attribute['attribute_id'] . '][' . $attribute['attribute_value_id'] . ']';
				}

				$disabled = '';
				$required = $attribute['required'];

				$option_data = array(
					'type' => $html_type,
					'name' => $option_name,
					'value' => $value,
					'options' => $values,
					'required' => $required,
					'attr' => $disabled,
					'style' => 'large-field'
				);

				if ($html_type == 'checkboxgroup') {
					$option_data['scrollbox'] = true;
				}

				$this->data['entry_attribute_' .$this->data['download_id'].'_'. $attribute['attribute_id']] = $attribute['name'];
				$this->data['attributes'][$this->data['download_id'].'_' . $attribute['attribute_id']] = $form->getFieldHtml($option_data);
			}
		}
		// for new download - create form for mapping shared downloads to product
		if(!$file_data['download_id']){

			if (!$this->registry->has('jqgrid_script')) {
				$locale = $this->session->data['language'];
				if (!file_exists(DIR_ROOT . '/' . RDIR_TEMPLATE . 'javascript/jqgrid/js/i18n/grid.locale-' . $locale . '.js')) {
					$locale = 'en';
				}
				$this->document->addScript(RDIR_TEMPLATE . 'javascript/jqgrid/js/i18n/grid.locale-' . $locale . '.js');
				$this->document->addScript(RDIR_TEMPLATE . 'javascript/jqgrid/js/jquery.jqGrid.min.js');
				$this->document->addScript(RDIR_TEMPLATE . 'javascript/jqgrid/plugins/jquery.grid.fluid.js');
				$this->document->addScript(RDIR_TEMPLATE . 'javascript/jqgrid/js/jquery.ba-bbq.min.js');
				$this->document->addScript(RDIR_TEMPLATE . 'javascript/jqgrid/js/grid.history.js');

				//set flag to not include scripts/css twice
				$this->registry->set('jqgrid_script', true);
			}


			$form0 = new AForm('ST');
			$form0->setForm(array(
						'form_name' => 'SharedFrm'.$file_data['download_id'],
						'update' => $this->data['update'],
					));
			$this->data['form0']['form_open'] = $form0->getFieldHtml(array(
						'type' => 'form',
						'name' => 'SharedFrm'.$file_data['download_id'],
						'attr' => 'confirm-exit="true"',
						'action' => $this->html->getSecureURL('catalog/product_files','&product_id='.$product_id)
					));

			// exclude this product from multivalue list. why we need relate recursion?
			$this->session->data['multivalue_excludes']['product_id'] = $this->request->get['product_id'];

			$this->data['form0']['fields']['list_hidden'] = $form0->getFieldHtml(
				array('id' => 'popup',
					'type' => 'multivalue',
					'name' => 'popup',
					'title' => $this->language->get('text_select_from_list'),
					'selected' => ($listing_data ? AJson::encode($listing_data) : "{}"),
					'content_url' => $this->html->getSecureUrl('catalog/download_listing', '&shared_only=1&form_name=SharedFrm'.$file_data['download_id'].'&multivalue_hidden_id=popup'),
					'postvars' => '',
					'return_to' => '', // placeholder's id of listing items count.
					'popup_height' => 708,
					'text' => array(
						'selected' => $this->language->get('text_count_selected'),
						'edit' => $this->language->get('text_save_edit'),
						'apply' => $this->language->get('text_apply'),
						'save' => $this->language->get('button_save'),
						'reset' => $this->language->get('button_reset')),
				));

		}


		$this->view->batchAssign($this->data);
		$this->processTemplate($tpl);
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
	/**
	 * @param array $data
	 * @return bool
	 */
	private function _validateDownloadForm($data = array()){
		$this->error = array();
		$this->load->language('catalog/files');
		if (!empty($data[ 'download_id' ]) && !$this->model_catalog_download->getDownload($data[ 'download_id' ])) {
			$this->error[ 'download_id' ] = $this->language->get('error_download_exists');
		}
		if ( mb_strlen($data[ 'name' ]) < 2 || mb_strlen($data[ 'name' ]) > 64 ) {
			$this->error[ 'name' ] = $this->language->get('error_download_name');
		}
		if ( !in_array($data[ 'activate' ],array('before_order','immediately','order_status','manually')) ) {
			$this->error[ 'activate' ] = $this->language->get('error_activate');
		}else{
			if($data[ 'activate' ]=='order_status' && !(int)$data['activate_order_status_id']){
				$this->error[ 'order_status' ] = $this->language->get('error_order_status');
			}
		}
		$attr_mngr = new AAttribute_Manager('download_attribute');
		$attr_errors = $attr_mngr->validateAttributeData($data['attributes'][$data[ 'download_id' ]]);
		if($attr_errors){
			$this->error['atributes'] = $this->language->get('error_download_attributes').'<br>&nbsp;&nbsp;&nbsp;'. implode('<br>&nbsp;&nbsp;&nbsp;',$attr_errors);
		}
		return $this->error ? false : true;
	}

	public function downloads() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$downloads = array();
		$this->loadModel('catalog/download');
		if($this->request->post['id']){
			$downloads = $this->model_catalog_download->getDownloads(array('subsql_filter'=>' shared = 1 AND d.download_id IN ('.implode(',',$this->request->post['id']).')'));
		}

		$download_data = array();
		foreach ($downloads as $download) {
			$download_data[ ] = array(
				'id' => $download[ 'download_id' ],
				'name' => $download[ 'name' ],
				'sort_order' => (int)$download[ 'sort_order' ]
			);
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($download_data));
	}

	public function pushToCustomers(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$download_id = (int)$this->request->get['download_id'];
		$product_id = (int)$this->request->get['product_id'];

		$download_info = $this->download->getDownloadInfo($download_id);

		if(!$download_info || !$product_id){
			$this->redirect($this->html->getSecureURL('catalog/product_files', '&product_id=' . $product_id));
		}

		$download_info['attributes_data'] = serialize($this->download->getDownloadAttributesValues($download_id));
		$this->loadModel('catalog/download');
		$orders_for_push = $this->model_catalog_download->getOrdersWithProduct($product_id);
		$updated_array = array();
		if($orders_for_push){
			foreach($orders_for_push as $row){
				$updated_array = array_merge(
						$updated_array, 
						$this->download->addUpdateOrderDownload($row['order_product_id'],$row['order_id'], $download_info)
						);
			}
		}
		
		$this->load->language('catalog/files');
		$output = array('progress'=> 100, 'text'=> sprintf($this->language->get('text_push_to_orders'), count($updated_array)) );

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($output));
	}
}