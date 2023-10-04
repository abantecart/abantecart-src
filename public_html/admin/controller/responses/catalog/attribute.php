<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

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

class ControllerResponsesCatalogAttribute extends AController
{

    public $data = [];

    public function get_attribute_type()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $am = new AAttribute_Manager();
        $this->data['attribute_info'] = $am->getAttribute($this->request->get['attribute_id']);

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['attribute_info']['attribute_type_id']));
    }

    /**
     * method that return part of attribute form
     *
     * @internal param array $param
     *
     * @param array $params
     */
    public function getProductOptionSubform($params = [])
    {
        $attributes_fields = [];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->data = array_merge($this->data, $params['data']);

        unset($this->data['form']['fields']); // remove form fields that do not needed here

        $this->data['elements_with_options'] = HtmlElementFactory::getElementsWithOptions();

        $results = HtmlElementFactory::getAvailableElements();
        $element_types = ['' => $this->language->get('text_select')];
        foreach ($results as $key => $type) {
            // allowed field types
            if (in_array($key, AAttribute_Manager::$allowedProductOptionFieldTypes)) {
                $element_types[$key] = $type['type'];
            }
        }
        /** @var $form AForm */
        $form = $params['aform'];
        /** @var AAttribute_Manager $attribute_manager */
        $attribute_manager = $params['attribute_manager'];

        $this->data['form']['fields']['element_type'] = $form->getFieldHtml(
            [
            'type'     => 'selectbox',
            'name'     => 'element_type',
            'value'    => $this->data['element_type'],
            'required' => true,
            'options'  => $element_types,
            ]
        );
        $this->data['form']['fields']['sort_order'] = $form->getFieldHtml(
            [
            'type'  => 'input',
            'name'  => 'sort_order',
            'value' => $this->data['sort_order'],
            'style' => 'small-field',
            ]
        );
        $this->data['form']['fields']['required'] = $form->getFieldHtml(
            [
            'type'  => 'checkbox',
            'name'  => 'required',
            'value' => $this->data['required'],
            'style' => 'btn_switch',
            ]
        );
        $this->data['form']['fields']['regexp_pattern'] = $form->getFieldHtml(
            [
            'type'  => 'input',
            'name'  => 'regexp_pattern',
            'value' => $this->data['regexp_pattern'],
            'style' => 'large-field',
            ]
        );
        $this->data['form']['fields']['placeholder'] = $form->getFieldHtml(
            [
            'type'  => 'input',
            'name'  => 'placeholder',
            'value' => $this->data['placeholder'],
            'style' => 'large-field',
            ]
        );
        $this->data['form']['fields']['error_text'] = $form->getFieldHtml(
            [
            'type'  => 'input',
            'name'  => 'error_text',
            'value' => $this->data['error_text'],
            'style' => 'large-field',
            ]
        );
        $this->data['children'] = [];

        $currency_symbol = $this->currency->getCurrency($this->config->get('config_currency'));
        $currency_symbol = $currency_symbol['symbol_left'].$currency_symbol['symbol_right'];

        //Build attribute values part of the form
        if ($this->request->get['attribute_id']) {

            $this->data['child_count'] = $attribute_manager->totalChildren($this->request->get['attribute_id']);
            if ($this->data['child_count'] > 0) {
                $children_attr = $attribute_manager->getAttributes([], 0, $this->request->get['attribute_id']);
                foreach ($children_attr as $attr) {
                    $this->data['children'][] = [
                        'name' => $attr['name'],
                        'link' => $this->html->getSecureURL('catalog/attribute/update', '&attribute_id='.$attr['attribute_id']),
                    ];
                }
            }

            $attribute_values = $attribute_manager->getAttributeValues($this->request->get['attribute_id']);
            foreach ($attribute_values as $atr_val) {
                $attrValueId = $atr_val['attribute_value_id'];
                $attributes_fields[$attrValueId]['sort_order'] = $form->getFieldHtml(
                    [
                    'type'  => 'number',
                    'name'  => 'sort_orders['.$attrValueId.']',
                    'value' => $atr_val['sort_order'],
                    ]
                );
                $attributes_fields[$attrValueId]['price_modifier'] = $form->getFieldHtml(
                    [
                    'type'  => 'input',
                    'name'  => 'price_modifiers['.$attrValueId.']',
                    'value' => number_format((float)$atr_val['price_modifier'],2)
                    ]
                );

                if (!$atr_val['prefix']) {
                    $atr_val['prefix'] = $currency_symbol;
                }
                $attributes_fields[$attrValueId]['price_prefix'] = $form->getFieldHtml(
                    [
                        'type'    => 'selectbox',
                        'name'    => 'price_prefixes['.$attrValueId.']',
                        'value'   => $atr_val['price_prefix'],
                        'options' => [
                            '$' => $currency_symbol,
                            '%' => '%',
                        ],
                    ]
                );

                $attributes_fields[$attrValueId]['values'] = $form->getFieldHtml(
                    [
                    'type'  => 'input',
                    'name'  => 'values['.$attrValueId.']',
                    'value' => $atr_val['value'],
                    'style' => 'medium-field',
                    ]
                );
                $attributes_fields[$attrValueId]['attribute_value_ids'] = $form->getFieldHtml(
                    [
                    'type'  => 'hidden',
                    'name'  => 'attribute_value_ids['.$attrValueId.']',
                    'value' => $attrValueId,
                    'style' => 'medium-field',
                    ]
                );
            }
        }
        if (!$attributes_fields) {
            $attributes_fields[0]['price_modifier'] = $form->getFieldHtml(
                [
                    'type'  => 'input',
                    'name'  => 'price_modifiers[]',
                    'value' => 0.0
                ]
            );
            $attributes_fields[0]['price_prefix'] = $form->getFieldHtml(
                [
                    'type'    => 'selectbox',
                    'name'    => 'price_prefixes[]',
                    'value'   => '$',
                    'options' => [
                        '$' => $currency_symbol,
                        '%' => '%',
                    ],
                ]
            );
            $attributes_fields[0]['sort_order'] = $form->getFieldHtml(
                [
                'type'  => 'number',
                'name'  => 'sort_orders[]',
                'value' => '1',
                'style' => 'small-field no-save',
                ]
            );
            $attributes_fields[0]['values'] = $form->getFieldHtml(
                [
                'type'  => 'input',
                'name'  => 'values[]',
                'value' => '',
                'style' => 'medium-field no-save',
                ]
            );
            $attributes_fields[0]['attribute_value_ids'] = $form->getFieldHtml(
                [
                'type'  => 'hidden',
                'name'  => 'attribute_value_ids['.$attrValueId.']',
                'value' => 'new',
                'style' => 'medium-field',
                ]
            );
        }

        $this->data['settings'] = !$this->data['settings'] ? [] : $this->data['settings'];

        $this->data['form']['settings_fields'] = [
            'extensions' => $form->getFieldHtml(
                [
                'type'  => 'input',
                'name'  => 'settings[extensions]',
                'value' => (has_value($this->data['settings']['extensions']) ? $this->data['settings']['extensions'] : ''),
                'style' => 'no-save',
                ]
            ),
            'min_size'   => $form->getFieldHtml(
                [
                'type'  => 'input',
                'name'  => 'settings[min_size]',
                'value' => (has_value($this->data['settings']['min_size']) ? $this->data['settings']['min_size'] : ''),
                'style' => 'small-field no-save',
                ]
            ),
            'max_size'   => $form->getFieldHtml(
                [
                'type'  => 'input',
                'name'  => 'settings[max_size]',
                'value' => (has_value($this->data['settings']['max_size']) ? $this->data['settings']['max_size'] : ''),
                'style' => 'small-field no-save',
                ]
            ),
            'directory'  => $form->getFieldHtml(
                [
                'type'  => 'input',
                'name'  => 'settings[directory]',
                'value' => (has_value($this->data['settings']['directory']) ? $this->data['settings']['directory'] : ''),
                'style' => 'no-save',
                ]
            ),
        ];
        $this->data['entry_upload_dir'] = sprintf($this->language->get('entry_upload_dir'), 'admin/system/uploads/');

        $this->data['form']['attribute_values'] = $attributes_fields;

        $this->view->batchAssign($this->data);

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->processTemplate('responses/catalog/global_attribute_product_option_subform.tpl');
    }

    /**
     * method that return part of attribute form for download attribute
     *
     * @param array $params
     * @throws AException
     * @internal param array $param
     *
     */
    public function getDownloadAttributeSubform($params = [])
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->data = array_merge($this->data, $params['data']);

        unset($this->data['form']['fields']); // remove form fields that do not needed here

        $this->data['elements_with_options'] = HtmlElementFactory::getElementsWithOptions();

        $results = HtmlElementFactory::getAvailableElements();
        $element_types = ['' => $this->language->get('text_select')];
        foreach ($results as $key => $type) {
            // allowed field types
            if (in_array($key, AAttribute_Manager::$allowedDownloadAttributeFieldTypes)) {
                $element_types[$key] = $type['type'];
            }
        }

        $form = $params['aform'];
        $attribute_manager = $params['attribute_manager'];

        $this->data['form']['fields']['element_type'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'element_type',
                'value'    => $this->data['element_type'],
                'required' => true,
                'options'  => $element_types,
            ]
        );
        $this->data['form']['fields']['sort_order'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'sort_order',
                'value' => $this->data['sort_order'],
                'style' => 'small-field',
            ]
        );
        $this->data['form']['fields']['show_to_customer'] = $form->getFieldHtml(
            [
                'type'    => 'checkbox',
                'name'    => 'settings[show_to_customer]',
                'value'   => 1,
                'checked' => ($this->data['settings'] && $this->data['settings']['show_to_customer']),
                'style'   => 'btn_switch',
            ]
        );

        $attributesFields = [];
        //Build attribute values part of the form
        if ($this->request->get['attribute_id']) {
            $this->data['child_count'] = $attribute_manager->totalChildren($this->request->get['attribute_id']);
            if ($this->data['child_count'] > 0) {
                $children_attr = $attribute_manager->getAttributes([], 0, $this->request->get['attribute_id']);
                foreach ($children_attr as $attr) {
                    $this->data['children'][] = [
                        'name' => $attr['name'],
                        'link' => $this->html->getSecureURL('catalog/attribute/update', '&attribute_id='.$attr['attribute_id']),
                    ];
                }
            }

            $attribute_values = $attribute_manager->getAttributeValues($this->request->get['attribute_id']);
            foreach ($attribute_values as $atr_val) {
                $atr_val_id = $atr_val['attribute_value_id'];
                $attributesFields[$atr_val_id]['sort_order'] = $form->getFieldHtml(
                    [
                    'type'  => 'input',
                    'name'  => 'sort_orders['.$atr_val_id.']',
                    'value' => $atr_val['sort_order'],
                    'style' => 'small-field',
                    ]
                );
                $attributesFields[$atr_val_id]['values'] = $form->getFieldHtml(
                    [
                    'type'  => 'input',
                    'name'  => 'values['.$atr_val_id.']',
                    'value' => $atr_val['value'],
                    'style' => 'medium-field',
                    ]
                );
                $attributesFields[$atr_val_id]['attribute_value_ids'] = $form->getFieldHtml(
                    [
                    'type'  => 'hidden',
                    'name'  => 'attribute_value_ids['.$atr_val_id.']',
                    'value' => $atr_val_id,
                    'style' => 'medium-field',
                    ]
                );
            }
        }
        if (!$attributesFields) {
            $attributesFields[0]['sort_order'] = $form->getFieldHtml(
                [
                'type'  => 'input',
                'name'  => 'sort_orders[]',
                'value' => '',
                'style' => 'small-field no-save',
                ]
            );
            $attributesFields[0]['values'] = $form->getFieldHtml(
                [
                'type'  => 'input',
                'name'  => 'values[]',
                'value' => '',
                'style' => 'medium-field no-save',
                ]
            );
            $attributesFields[0]['attribute_value_ids'] = $form->getFieldHtml(
                [
                'type'  => 'hidden',
                'name'  => 'attribute_value_ids['.$atr_val_id.']',
                'value' => 'new',
                'style' => 'medium-field',
                ]
            );
        }

        $this->data['form']['attribute_values'] = $attributesFields;
        $this->view->batchAssign($this->data);

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->processTemplate('responses/catalog/global_attribute_download_subform.tpl');
    }
}