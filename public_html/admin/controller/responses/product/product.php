<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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
class ControllerResponsesProductProduct extends AController {
	private $error = array();
	public $data = array();

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
					'model' => $product_info[ 'model' ]
				);
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
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

		if (($this->request->server[ 'REQUEST_METHOD' ] == 'POST')) {
			$this->model_catalog_product->updateProduct($this->request->get[ 'product_id' ], $this->request->post);
			$result = 'Saved!';
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->response->setOutput($result);
	}

	private function _validateForm() {
		if (!$this->user->canModify('catalog/product')) {
			$this->error[ 'warning' ] = $this->language->get('error_permission');
		}

		foreach ($this->request->post[ 'product_description' ] as $language_id => $value) {
			if ((strlen(utf8_decode($value[ 'name' ])) < 1) || (strlen(utf8_decode($value[ 'name' ])) > 255)) {
				$this->error[ 'name' ][ $language_id ] = $this->language->get('error_name');
			}
		}

		if ((strlen(utf8_decode($this->request->post[ 'model' ])) < 1) || (strlen(utf8_decode($this->request->post[ 'model' ])) > 64)) {
			$this->error[ 'model' ] = $this->language->get('error_model');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			if (!isset($this->error[ 'warning' ])) {
				$this->error[ 'warning' ] = $this->language->get('error_required_data');
			}
			return FALSE;
		}
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
				$price = $this->currency->format($price, $this->request->get[ 'currency' ], $this->request->get[ 'value' ]);
			} else {
				$price = $this->currency->format($price);
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
				'name' => $this->model_catalog_category->getPath($category_id)
			);

		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
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
					'model' => $product_info[ 'model' ]
				);
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($product_data));
	}

	public function get_options_list() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('catalog/product');
		$product_options = $this->model_catalog_product->getProductOptions($this->request->get[ 'product_id' ]);

		$result = array();
		foreach ($product_options as $option) {
			$option_name = trim($option[ 'language' ][ $this->session->data[ 'content_language_id' ] ][ 'name' ]);
			$result[ $option[ 'product_option_id' ] ] = $option_name ? $option_name : 'n/a';
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
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

		$this->loadModel('catalog/product');
		$this->model_catalog_product->updateProductOption($this->request->get[ 'option_id' ], $this->request->get);

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

		$this->data[ 'language_id' ] = $this->session->data[ 'content_language_id' ];
		$this->data[ 'element_types' ] = HtmlElementFactory::getAvailableElements();
		$this->data[ 'elements_with_options' ] = HtmlElementFactory::getElementsWithOptions();
		$this->data[ 'selectable' ] = in_array($this->data[ 'option_data' ][ 'element_type' ], $this->data[ 'elements_with_options' ]) ? 1 : 0;
		$this->data[ 'option_type' ] = $this->data[ 'element_types' ][ $this->data[ 'option_data' ][ 'element_type' ] ][ 'type' ];

		$this->attribute_manager = new AAttribute_Manager();

		$this->data[ 'action' ] = $this->html->getSecureURL('product/product/update_option_values', '&product_id=' . $this->request->get[ 'product_id' ] . '&option_id=' . $this->request->get[ 'option_id' ]);
		$this->data[ 'language_id' ] = $this->session->data[ 'content_language_id' ];

		$this->data[ 'option_values' ] = $this->model_catalog_product->getProductOptionValues(
			$this->request->get[ 'product_id' ],
			$this->request->get[ 'option_id' ]
		);

		$this->data[ 'option_name' ] = $this->html->buildInput(array(
			'name' => 'name',
			'value' => $this->data[ 'option_data' ][ 'language' ][ $this->data[ 'language_id' ] ][ 'name' ],
		));
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
					$values = $this->attribute_manager->getAttributeValues($attribute[ 'attribute_id' ], $this->session->data[ 'content_language_id' ]);

					foreach ($values as $v) {
						$this->data[ 'option_attribute' ][ 'group' ][ $option_id ][ 'values' ][ $v[ 'attribute_value_id' ] ] = addslashes(html_entity_decode($v[ 'value' ], ENT_COMPAT, 'UTF-8'));
					}
				}
			}

		} else {
			if (in_array($this->data[ 'option_attribute' ][ 'element_type' ], $this->data[ 'elements_with_options' ])) {
				$this->data[ 'option_attribute' ][ 'type' ] = 'selectbox';
				$values = $this->attribute_manager->getAttributeValues(
					$this->data[ 'option_attribute' ][ 'attribute_id' ],
					$this->session->data[ 'content_language_id' ]
				);
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

		$fields = array( 'name', 'sku', 'quantity', 'subtract', 'price', 'prefix', 'sort_order', 'weight', 'weight_type', 'attribute_value_id', 'children_options' );
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
			$this->data[ 'name' ] = $item_info[ 'language' ][ $this->session->data[ 'content_language_id' ] ][ 'name' ];
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
			'value' => $this->data[ 'price' ],
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

	private function _validateOptionValueForm() {
		if (!$this->user->canModify('product/product')) {
			$this->error[ 'warning' ] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}