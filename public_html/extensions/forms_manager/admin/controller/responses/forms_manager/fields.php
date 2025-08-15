<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerResponsesFormsManagerFields extends AController
{
    public $error = [];
    /** @var ModelToolFormsManager */
    public $mdl;
    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->loadLanguage('forms_manager/forms_manager');
        $this->mdl = $this->loadModel('tool/forms_manager');
    }

    public function get_fields_list()
    {
        $fields = $this->mdl->getFields((int)$this->request->get['form_id']);
        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($fields));
    }

    public function addField()
    {
        $post = $this->request->post;
        $post['form_id'] = (int)$this->request->get['form_id'];
        if(!$post['form_id'] || !$this->validateFieldForm($post) ) {
            $error = new AError('');
            $error->toJSONResponse(
                'VALIDATION_ERROR_406',
                ['error_text' => $this->error]);
            return;
        }

        $this->mdl->addField($post['form_id'], $post);
        $this->response->setOutput('');
    }

    public function updateField()
    {
        $formId = (int)$this->request->get['form_id'];
        $post = $this->request->post;
        $post['form_id'] = $formId;
        if (!$this->validateFieldForm($post) || !$formId) {
            $error = new AError('');
            $error->toJSONResponse(
                'VALIDATION_ERROR_406',
                ['error_text' => $this->error]);
            return;
        }

        $this->mdl->updateFormFieldData($post);
        $this->response->setOutput('');
    }

    protected function validateFieldForm($data)
    {
        if (!$this->user->hasPermission('modify', 'forms_manager/fields')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $data['field_name'] = preg_replace('/[^a-zA-Z0-9._]/', '', $data['field_name']);

        if ((!$data['element_type'] && !$data['field_id']) || !$data['field_description'] || !$data['field_name']) {
            $this->error['error_required'] = $this->language->get('error_fill_required');
        }

        if (!$this->mdl->isFieldNameUnique((int)$data['form_id'],(string)$data['field_name'],(int)$data['field_id'])) {
            $this->error['field_name'] = sprintf($this->language->get('error_field_name_exists'), $data['field_name']);
        }

        if($data['regexp_pattern'] && @preg_match($data['regexp_pattern'], '') === false) {
            $this->error['regexp_pattern'] = $this->language->get('error_regexp_pattern');
        }

        $this->extensions->hk_ValidateData($this);
        return (!$this->error);
    }

    public function remove_field()
    {
        $this->mdl->removeField((int)$this->request->get['form_id'], (int)$this->request->get['field_id']);
        $this->response->setOutput($this->language->get('text_field_removed'));
    }

    public function update_form()
    {
        $post = $this->request->post;
        if ($post['controller_path'] == 'forms_manager/default_email' && trim($post['success_page']) == '') {
            $post['success_page'] = 'forms_manager/default_email/success';
        }
        $this->mdl->updateForm($post);
        $this->mdl->updateFormFieldData($post);
        $this->response->setOutput($this->language->get('text_success_form'));

    }

    public function update_field_values()
    {
        $this->mdl->updateFieldValues(
            $this->request->get,
            (int)$this->language->getContentLanguageID()
        );
        $this->response->setOutput($this->language->get('text_success_form'));
    }

    public function load_field()
    {
        $this->data['error_warning'] = $this->session->data['warning'];
        if (isset($this->session->data['warning'])) {
            unset($this->session->data['warning']);
        }

        $this->view->assign('success', $this->session->data['success']);
        unset($this->session->data['success']);

        $formId = (int)$this->request->get['form_id'];
        $fieldId = (int)$this->request->get['field_id'];

        $this->data['language_id'] = $this->language->getContentLanguageID();
        $this->data['field_data'] = $this->mdl->getField($fieldId);
        $this->data['element_types'] = HtmlElementFactory::getAvailableElements();
        $this->data['elements_with_options'] = HtmlElementFactory::getElementsWithOptions();
        $this->data['no_set_values_elements'] = [
            'K' => 'captcha',
            'D' => 'date',
            'A' => 'IPaddress',
            'O' => 'countries',
            'Z' => 'zones',
        ];

        $elmType = $this->data['field_data']['element_type'];
        $this->data['selectable'] = in_array(
            $elmType,
            $this->data['elements_with_options'])
            ? 1
            : 0;
        $this->data['field_type'] = $this->data['element_types'][$elmType]['type'];

        if ($this->data['field_type'] == 'captcha') {
            $fieldName = $this->config->get('config_recaptcha_site_key') ? 'g-recaptcha-response' : 'captcha';
        } else {
            $fieldName = $this->data['field_data']['field_name'];
        }

        $this->data['field_name'] = $this->html->buildInput(
            [
                'name'     => 'field_name',
                'value'    => $fieldName,
                'required' => true,
                'attr'     => $this->data['field_type'] == 'captcha' ? 'readonly' : ''
            ]
        );

        $this->data['field_description'] = $this->html->buildElement(
            [
                'type'     => 'input',
                'name'     => 'field_description',
                'value'    => $this->data['field_data']['name'],
                'required' => true,
            ]
        );

        $this->data['field_note'] = $this->html->buildElement(
            [
                'type'  => 'input',
                'name'  => 'field_note',
                'value' => $this->data['field_data']['description'],
            ]
        );

        $this->data['entry_status'] = $this->language->get('forms_manager_status');
        $this->data['status'] = $this->html->buildElement(
            [
                'type'  => 'checkbox',
                'name'  => 'status',
                'value' => $this->data['field_data']['status'],
                'style' => 'btn_switch btn-group-xs',
            ]
        );
        $this->data['field_sort_order'] = $this->html->buildElement(
            [
                'type'  => 'input',
                'name'  => 'sort_order',
                'value' => $this->data['field_data']['sort_order'],
                'style' => 'small-field',
            ]
        );
        $this->data['required'] = $this->html->buildElement(
            [
                'type'  => 'checkbox',
                'name'  => 'required',
                'value' => $this->data['field_data']['required'] ? 1 : 0,
                'style' => 'btn_switch btn-group-xs',
            ]
        );

        $this->data['field_attributes'] = $this->html->buildElement(
            [
                'type'  => 'input',
                'name'  => 'attributes',
                'value' => $this->data['field_data']['attributes'],
            ]
        );

        if (!in_array($elmType, ['U', 'K'])) {
            $this->data['field_regexp_pattern'] = $this->html->buildElement(
                [
                    'type'  => 'input',
                    'name'  => 'regexp_pattern',
                    'value' => $this->data['field_data']['regexp_pattern'],
                ]
            );

            $this->data['field_error_text'] = $this->html->buildElement(
                [
                    'type'  => 'input',
                    'name'  => 'error_text',
                    'value' => $this->data['field_data']['error_text'],
                ]
            );
        }


        if ($elmType == 'U') {
            $this->data['field_settings'] = $this->_file_upload_settings_form();
        }

        $this->data['hidden_element_type'] = $this->html->buildElement(
            [
                'type'  => 'hidden',
                'name'  => 'element_type',
                'value' => $elmType,
            ]
        );
        $this->data['entry_icon'] = $this->language->get('column_icon','extension/extensions');
        $this->data['icon'] = $this->html->buildElement(
            [
                'type' => 'resource',
                'name' => 'resource_id',
                'resource_id' => $this->data['field_data']['resource_id'],
                'rl_type' => 'image',
            ]
        );

        //adds scripts for RL work
        $resources_scripts = $this->dispatch(
            'responses/common/resource_library/get_resources_scripts',
            [
                'object_name' => 'field',
                'object_id' => (int)$formId,
                'types' => ['image'],
                'onload' => true,
                'mode' => 'single',
            ]
        );
        $this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();

        $this->data['button_remove_field'] = $this->html->buildElement(
            [
                'type' => 'button',
                'href' => $this->html->getSecureURL(
                    'tool/forms_manager/removeField',
                    '&form_id=' . $formId . '&field_id=' . $fieldId
                ),
                'text' => $this->language->get('button_remove_field'),
            ]
        );
        $this->data['button_save'] = $this->html->buildElement(
            [
                'type' => 'button',
                'text' => $this->language->get('button_save'),
            ]);
        $this->data['button_reset'] = $this->html->buildElement(
            [
                'type' => 'button',
                'text' => $this->language->get('button_reset'),
            ]
        );

        $this->data['update_field_values'] = $this->html->getSecureURL(
            'forms_manager/fields/update_field_values',
            '&form_id=' . $formId . '&field_id=' . $fieldId
        );

        $this->data['remove_field_link'] = $this->html->getSecureURL(
            'forms_manager/fields/remove_field',
            '&form_id=' . $formId . '&field_id=' . $fieldId
        );

        // form of option values list
        $form = new AForm('HT');
        $form->setForm(['form_name' => 'update_field_values']);
        $this->data['form']['id'] = 'update_field_values';
        $this->data['update_field_values_form']['open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'update_field_values',
                'action' => $this->data['update_field_values'],
            ]
        );

        //form of option
        $form = new AForm('HT');
        $form->setForm(
            [
                'form_name' => 'field_value_form',
            ]
        );

        $this->data['form']['id'] = 'field_value_form';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'field_value_form',
                'action' => $this->data['update_field_values'],
            ]
        );

        //Load option values rows
        $this->data['field_values'] = [];

        if (!in_array($elmType, ['U', 'K'])) {
            if (!empty($this->data['field_data']['values'])) {
                usort($this->data['field_data']['values'], self::_sort_by_sort_order(...));

                foreach ($this->data['field_data']['values'] as $key => $item) {
                    $item['id'] = $key;
                    $this->data['field_values'][$key]['row'] = $this->_field_value_form($item, $form);
                }
            } else {
                $this->data['field_values']['new']['row'] = $this->_field_value_form([], $form);
            }
        }

        $this->data['new_field_row'] = '';
        if (in_array($elmType, $this->data['elements_with_options'])
            || $this->data['empty_values']
            && !in_array($this->data['field_type'], $this->data['no_set_values_elements'])
        ) {
            $this->data['new_value_row'] = $this->_field_value_form([], $form);
        }

        $this->data['new_value_row'] = $this->_field_value_form([], $form);

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/forms_manager/field_values.tpl');

    }

    protected function _sort_by_sort_order($a, $b)
    {
        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        }
        return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
    }

    /**
     * @param       $item
     * @param AForm $form
     *
     * @return string
     * @throws AException
     */
    protected function _field_value_form($item, $form)
    {
        $elmType = $this->data['field_data']['element_type'];

        if (in_array($elmType, ['U', 'K'])) {
            return '';
        }

        if (isset($item['id'])) {
            $field_value_id = $item['id'];
            $this->data['row_id'] = 'row' . $field_value_id;
            $this->data['attr_val_id'] = $field_value_id;
        } else {
            $field_value_id = '';
            $this->data['row_id'] = 'new_row';
        }

        $this->data['form']['fields']['field_value_id'] = $form->getFieldHtml(
            [
                'type'  => 'hidden',
                'name'  => 'field_value_id[' . $field_value_id . ']',
                'value' => $field_value_id,
            ]
        );

        $this->data['form']['fields']['field_value'] = $form->getFieldHtml(
            [
                'type'  => ($elmType == 'T') ? 'textarea' : 'input',
                'name'  => 'name[' . $field_value_id . ']',
                'value' => $item['name'],
                'style' => 'large-field',
            ]
        );

        if (in_array($elmType, $this->data['elements_with_options'])) {
            $this->data['form']['fields']['sort_order'] = $form->getFieldHtml(
                [
                    'type'  => 'input',
                    'name'  => 'sort_order[' . $field_value_id . ']',
                    'value' => (int)$item['sort_order'],
                    'style' => 'small-field',
                ]
            );
        }

        $this->view->batchAssign($this->data);
        return $this->view->fetch('responses/forms_manager/field_value_row.tpl');
    }

    /**
     * @return string
     * @throws AException
     */
    protected function _file_upload_settings_form()
    {

        $this->loadLanguage('catalog/attribute');
        $this->data['form']['settings_fields'] = [
            'extensions' => $this->html->buildElement(
                [
                    'type'  => 'input',
                    'name'  => 'settings[extensions]',
                    'value' => $this->data['field_data']['settings']['extensions'],
                    'style' => 'no-save',
                ]
            ),
            'min_size'   => $this->html->buildElement(
                [
                    'type'  => 'input',
                    'name'  => 'settings[min_size]',
                    'value' => $this->data['field_data']['settings']['min_size'],
                    'style' => 'small-field no-save',
                ]
            ),
            'max_size'   => $this->html->buildElement(
                [
                    'type'  => 'input',
                    'name'  => 'settings[max_size]',
                    'value' => $this->data['field_data']['settings']['max_size'],
                    'style' => 'small-field no-save',
                ]
            ),
            'directory'  => $this->html->buildElement(
                [
                    'type'  => 'input',
                    'name'  => 'settings[directory]',
                    'value' => trim($this->data['field_data']['settings']['directory'], '/'),
                    'style' => 'no-save',
                ]
            ),
        ];

        $this->data['entry_upload_dir'] = sprintf($this->language->get('entry_upload_dir'), 'admin/system/uploads/');
        $uploadsDir = DIR_APP_SECTION . '/system/uploads';
        $settingsDir = $uploadsDir . '/' . trim($this->data['attribute_data']['settings']['directory'], '/');
        //check or make writable dirs
        if (!make_writable_dir($uploadsDir) || !make_writable_dir($settingsDir)) {
            $this->data['form']['settings_fields']['directory'] .= '<i class="error">' . $this->language->get('error_directory_not_writable') . '</i>';
        }

        $this->view->batchAssign($this->data);
        return $this->view->fetch('responses/forms_manager/file_upload_settings.tpl');
    }
}