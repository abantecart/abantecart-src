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
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

class ControllerResponsesSettingSettingQuickForm extends AController
{
    public $error = [];

    public function main()
    {
        if (!$this->user->canModify('setting/setting_quick_form')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $output = ['result_text' => ''];
        /** @var ModelSettingSetting $sMdl */
        $sMdl = $this->loadModel('setting/setting');
        $this->loadLanguage('setting/setting');
        $this->loadLanguage('common/header');

        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $setting = explode('-', $this->request->get['active']);
        $this->data['group'] = $setting[0];
        $this->data['setting_key'] = $setting[1];
        $this->data['store_id'] = !isset($this->session->data['current_store_id'])
            ? $setting[2]
            : $this->session->data['current_store_id'];
        $this->request->get['active'] = $this->data['group'] . '-' . $this->data['setting_key'] . '-' . $this->data['store_id'];
        //for multilingual settings
        foreach (['config_description', 'config_title', 'config_meta_description', 'config_meta_keywords'] as $cName) {
            if (str_contains($this->data['setting_key'], $cName)) {
                $this->data['setting_key'] = substr(
                    $this->data['setting_key'],
                    0,
                    strrpos($this->data['setting_key'], '_')
                );
                $this->request->get['active'] = $this->data['group'] . '-' . $setting[1] . '-' . $this->data['store_id'];
                break;
            }
        }

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->is_POST()) {
            if ($this->validateForm($this->data['group'])) {
                $sMdl->editSetting( (string)$this->data['group'], $this->request->post, (int)$this->data['store_id'] );
                $output['result_text'] = $this->language->get('text_success');
                $this->load->library('json');
                $this->response->addJSONHeader();
                $this->response->setOutput(AJson::encode($output));
            } else {
                $error = new AError('');
                $error->toJSONResponse('NO_PERMISSIONS_406',
                    [
                        'error_text'  => $this->error,
                        'reset_value' => true,
                    ]
                );
                return;
            }
        } else {
            $this->getForm();
        }
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function getForm()
    {
        if (isset($this->error['warning'])) {
            $this->data['error_text'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->data['error'] = $this->error;
        $languages = $this->language->getAvailableLanguages();
        foreach ($languages as $lang) {
            $this->data['languages'][$lang['language_id']] = $lang;
        }

        $this->data['action'] = $this->html->getSecureURL(
            'setting/setting_quick_form',
            '&' . http_build_query(
                [
                    'target' => $this->request->get['target'],
                    'active' => $this->request->get['active']
                ]
            )
        );
        $this->data['heading_title'] = $this->language->get('text_edit') . ' ' . $this->language->get('text_setting');
        $form = new AForm('HT');
        $this->data['setting_id'] = (int)$this->request->get['setting_id'];

        $form->setForm(
            [
                'form_name' => 'qsFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'qsFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'qsFrm',
                'action' => $this->data['action'],
                'attr'   => 'class="aform form-horizontal"',
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_save'),
                'style' => 'button1',
            ]
        );
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]
        );

        $this->load->library('config_manager');
        $cManager = new AConfigManager();
        $data = $this->model_setting_setting->getSetting($this->data['group'], $this->data['store_id']);

        $this->data['form']['fields'] = $cManager->getFormField(
            $this->data['setting_key'],
            $form,
            $data,
            $this->data['store_id'],
            $this->data['group']
        );

        //replace wysiwyg text editor to textarea inside modal-mode!
        foreach ($this->data['form']['fields'] as &$field) {
            if ($field->type != 'texteditor') {
                continue;
            }

            $ext_url = $this->html->getSecureURL(
                    'setting/setting',
                    '&' . http_build_query(['active' => $this->data['group']])) . '#' . $field->element_id;
            $label_text = $this->language->getAndReplace('text_texteditor_extended_mode', replaces: $ext_url);

            $field = $form->getFieldHtml(
                [
                    'type'        => 'textarea',
                    'name'        => $field->name,
                    'value'       => $field->value,
                    'ovalue'      => $field->ovalue,
                    'style'       => $field->style,
                    'attr'        => $field->attr,
                    'required'    => $field->required,
                    'placeholder' => $field->placeholder,
                    'label_text'  => $label_text,
                ]
            );
        }

        $this->data['form_store_switch'] = $this->html->getStoreSwitcher();
        $this->data['template_image'] = $this->html->getSecureURL('setting/template_image');

        $this->data['form_title'] = $this->language->get('tab_' . $this->data['group']);
        $this->data['help_url'] = $this->gen_help_url('setting_edit');
        $this->data['active'] = $this->request->get['active'];
        $this->data['title'] = $this->data['heading_title'];
        $this->view->batchAssign($this->data);

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->processTemplate('responses/setting/setting_quick_form.tpl');
    }

    protected function validateForm($group)
    {
        if (!$this->user->canModify('setting/setting_quick_form')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->library('config_manager');
        $cManager = new AConfigManager();
        $result = $cManager->validate($group, $this->request->post);
        $this->error = $result['error'];
        $this->request->post = $result['validated']; // for changed data saving
        $this->extensions->hk_ValidateData($this);
        return !$this->error;
    }

    /* Quick Start Guide */
    public function quick_start_save_next()
    {
        $languageId = $this->language->getContentLanguageID() ?? $this->language->getLanguageID();
        $group = $this->session->data['quick_start_step'];
        if (!isset($group)) {
            //get setting's group if the session has expired
            $group = (string)$this->request->get['active'];
            $this->session->data['quick_start_step'] = $group;
        }
        $store_id = $this->session->data['current_store_id'] ?? 0;
        if (isset($this->request->get['store_id'])) {
            $store_id = (int)$this->request->get['store_id'];
            $this->session->data['current_store_id'] = $store_id;
        }

        //save settings
        if ($group && $this->request->is_POST()) {
            if ($this->validateForm($group)) {
                /** @var ModelSettingSetting $sMdl */
                $sMdl = $this->loadModel('setting/setting');
                $this->loadLanguage('setting/setting');
                $this->loadLanguage('common/header');
                $this->loadLanguage('common/quick_start');
                $post = $this->request->post;
                $section = $group;

                if ($group == 'appearance') {
                    $section = $this->request->get['tmpl_id'] == 'default'
                        ? 'appearance'
                        : preformatTextID($this->request->get['tmpl_id']);

                    foreach (['config_logo', 'config_mail_logo', 'config_icon'] as $cName) {
                        //use resource id as value
                        //also check if language specific logo presents
                        if (isset($post[$cName . '_' . $languageId . '_resource_id'])) {
                            $post[$cName . '_' . $languageId] = $post[$cName . '_' . $languageId . '_resource_id'];
                            unset($post[$cName . '_' . $languageId . '_resource_id']);
                        }
                        if (isset($post[$cName . '_resource_id'])) {
                            $post[$cName] = $post[$cName . '_resource_id'];
                            unset($post[$cName . '_resource_id']);
                        } elseif (isset($post[$cName])) {
                            $post[$cName] = html_entity_decode($post[$cName], ENT_COMPAT, 'UTF-8');
                        }
                    }
                }

                $sMdl->editSetting($section, $post, $store_id);

                $this->session->data['success'] = $this->language->get('text_success');
                $this->data['result_text'] = $this->language->get('text_success');
                //set next step
                $this->session->data['quick_start_step'] = $this->_next_step($group) ?: 'finished';
            }
        }
        $this->quick_start();
    }

    public function quick_start_back()
    {
        $this->session->data['quick_start_step'] = $this->_prior_step($this->session->data['quick_start_step']) ?: 'details';
        $this->quick_start(false);
    }

    public function quick_start(bool $forward = true)
    {
        if (!$this->user->canModify('setting/setting_quick_form')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('setting/setting');
        $this->loadLanguage('setting/setting');
        $this->loadLanguage('common/header');
        $this->loadLanguage('common/quick_start');

        //quick start guide can be for different stores
        $this->data['store_id'] = (int)($this->session->data['current_store_id'] ?? 0);
        $this->data['title'] = $this->language->get('text_quick_start');
        $this->data['heading_title'] = $this->language->get('text_quick_start');

        $this->data['qs_fields']['details'] = [
            'name',
            'title',
            'meta_description',
            'meta_keywords',
            'description',
            'owner',
            'address',
            'email',
            'telephone',
            'country',
            'country_id_zones',
        ];
        $this->data['qs_fields']['general'] = [
            'stock_display',
            'nostock_autodisable',
            'stock_status',
            'embed_status',
            'embed_click_action',
        ];
        $this->data['qs_fields']['checkout'] = [
            'tax',
            'tax_store',
            'tax_customer',
            'invoice',
            'customer_approval',
            'customer_email_activation',
            'guest_checkout',
            'stock_checkout',
            'order_status',
        ];
        $this->data['qs_fields']['appearance'] = [
            'logo',
            'icon',
            'mail_logo',
            'image_thumb_width',
            'image_thumb_height',
            'image_product_width',
            'image_product_height',
        ];
        $this->data['qs_fields']['mail'] = [
            'mail_transporting',
            'smtp_host',
            'smtp_username',
            'smtp_password',
            'smtp_port',
            'smtp_timeout',
        ];

        if (empty($this->session->data['quick_start_step'])) {
            $this->session->data['quick_start_step'] = 'details';
        }
        $stepName = (string)$this->session->data['quick_start_step'];
        //if offers - try to get data first
        $offer_response = [];
        if (str_starts_with($stepName, 'offer')) {
            $offer_response = $this->messages->getANTMessageByPlaceholder(
                'quick_start',
                'quick_start_' . $stepName
            );
            if ($offer_response['html']) {
                $this->data['title'] = $offer_response['title'];
                $this->data['html'] = $offer_response['html'];
            } else {
                //skip step if no data
                if($forward) {
                    $stepName = $this->_next_step($stepName);
                }else{
                    $stepName = $this->_prior_step($stepName);
                }
                $this->session->data['quick_start_step'] = $stepName;
            }
        }

        $template = 'responses/setting/quick_start.tpl';
        if (str_starts_with($stepName, 'offer')) {
            $this->getOfferButtons($stepName);
            $this->data['quick_start_note'] = '';
            /** @see public_html/admin/view/default/template/responses/setting/offer1.tpl */
            /** @see public_html/admin/view/default/template/responses/setting/offer2.tpl */
            $template = "responses/setting/" . $stepName . ".tpl";
            if(!$this->view->isTemplateExists($template)){
                $template = "responses/setting/offer2.tpl";
            }

            if (!empty($offer_response['html'])) {
                $this->data['title'] = $offer_response['title'];
                $this->data['html'] = $offer_response['html'];
            }
        } elseif ($stepName == 'finished') {
            $this->data['payments_selection'] = $this->html->convertLinks($this->language->get('payments_selection'));
            $this->data['shipping_selection'] = $this->html->convertLinks($this->language->get('shipping_selection'));
            //get language list from github repo
            $this->data['language_packages'] = $this->html->installLanguageModal('dataonly');
            $this->data['language_selection'] = $this->html->convertLinks($this->language->get('language_selection'));
            $this->data['more_extensions'] = $this->html->convertLinks($this->language->get('more_extensions'));
            $this->data['quick_start_note'] = $this->language->get('text_quick_start_note');
            $this->data['quick_start_last_footer'] .= $this->language->getAndReplace(
                'text_quick_start_last_footer',
                replaces: $this->html->getSecureURL('setting/setting/all')
            );
            $this->data['competed'] = true;
        } else {
            if ($stepName == 'appearance') {
                //get current template
                $template_settings = $this->model_setting_setting->getSetting('appearance', $this->data['store_id']);
                $this->data['current_tmpl_id'] = $template_settings['config_storefront_template'];
                unset($template_settings);

                //extract settings for template or default
                $data = $this->model_setting_setting->getSetting(
                    $this->data['current_tmpl_id'],
                    $this->data['store_id']
                );

                //need to set template to be edited
                $data['tmpl_id'] = $this->data['current_tmpl_id'];
            } else {
                $data = $this->model_setting_setting->getSetting($stepName, $this->data['store_id']);
            }
            $this->getQuickStartForm($stepName, (array)$data);

            if ($stepName == 'details') {
                //welcome message for the first step
                $this->data['quick_start_note'] = $this->language->get('text_quick_start_note');
            }
            $this->data['quick_start_note'] .= $this->language->getAndReplace(
                'text_quick_start_' . $stepName,
                replaces: $this->html->getSecureURL('setting/setting/' . $stepName)
            );
        }

        $back_step = $this->_prior_step($stepName);
        if ($back_step) {
            $this->data['back'] = $this->html->getSecureURL(
                'setting/setting_quick_form/quick_start_back',
                '&store_id=' . (int)$this->data['store_id']);
        }

        $this->data['error'] = $this->error;
        $this->view->batchAssign($this->data);
        $this->view->assign('help_url', $this->gen_help_url('settings_quickstart'));

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->processTemplate($template);
    }

    protected function getOfferButtons($step)
    {
        $this->data['action'] = $this->html->getSecureURL(
            'setting/setting_quick_form/quick_start_save_next',
            '&active=' . $step . '&store_id=' . $this->data['store_id']
        );
        $this->view->assign('language_code', $this->session->data['language']);
        $form = new AForm('HS');
        $form->setForm(
            [
                'form_name' => 'settingFrm',
            ]
        );

        $this->data['form']['id'] = 'settingFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'settingFrm',
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
                'action' => $this->data['action'],
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_save'),
                'style' => 'button1',
            ]
        );
        $this->data['form']['reset'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'reset',
                'text' => $this->language->get('button_reset'),
            ]
        );

    }

    /**
     * @param string $section
     * @param array $settings_data
     * @return void
     * @throws AException
     */
    protected function getQuickStartForm(string $section, array $settings_data)
    {
        if ($settings_data['tmpl_id']) {
            //template settings
            $this->data['action'] = $this->html->getSecureURL(
                'setting/setting_quick_form/quick_start_save_next',
                '&active=' . $section . '&store_id=' . $this->data['store_id'] . '&tmpl_id=' . $settings_data['tmpl_id']);
            $this->data['update'] = $this->html->getSecureURL(
                'listing_grid/setting/update_field',
                '&group=' . $settings_data['tmpl_id'] . '&store_id=' . $this->data['store_id'] . '&tmpl_id=' . $settings_data['tmpl_id']);
        } else {
            $this->data['action'] = $this->html->getSecureURL(
                'setting/setting_quick_form/quick_start_save_next',
                '&active=' . $section . '&store_id=' . $this->data['store_id']);
            $this->data['update'] = $this->html->getSecureURL(
                'listing_grid/setting/update_field',
                '&group=' . $section . '&store_id=' . $this->data['store_id']);
        }

        $this->view->assign('language_code', $this->session->data['language']);
        $form = new AForm('HS');
        $form->setForm(
            [
                'form_name' => 'settingFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'settingFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'settingFrm',
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
                'action' => $this->data['action'],
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_save'),
                'style' => 'button1',
            ]
        );
        $this->data['form']['reset'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'reset',
                'text' => $this->language->get('button_reset'),
            ]
        );

        $this->data['form']['fields'] = [];
        $this->load->library('config_manager');
        $configManager = new AConfigManager();
        $set_fields = $configManager->getFormFields($section, $form, $settings_data);
        foreach ($this->data['qs_fields'][$section] as $field_name) {
            $field = $set_fields[$field_name];
            //replace wysiwyg text editor to textarea inside modal-mode!
            if ($field->type == 'texteditor') {
                $ext_url = $this->html->getSecureURL('setting/setting', '&active=' . $section) . '#' . $field->element_id;
                $label_text = $this->language->getAndReplace('text_texteditor_extended_mode', replaces: $ext_url);
                $field = $form->getFieldHtml(
                    [
                        'type'        => 'textarea',
                        'name'        => $field->name,
                        'value'       => $field->value,
                        'ovalue'      => $field->ovalue,
                        'style'       => $field->style,
                        'attr'        => $field->attr,
                        'required'    => $field->required,
                        'placeholder' => $field->placeholder,
                        'label_text'  => $label_text,
                    ]
                );
            }
            $this->data['form']['fields'][$field_name] = $field;
        }
        unset($set_fields);

    }

    /**
     * @param string $current_step
     * @return string
     */
    protected function _next_step(string $current_step)
    {
        $steps = [
            'details'    => 'offer1',
            'offer1'     => 'general',
            'general'    => 'offer2',
            'offer2'     => 'checkout',
            'checkout'   => 'offer3',
            'offer3'     => 'appearance',
            'appearance' => 'offer4',
            'offer4'     => 'mail',
            'mail'       => 'offer5',
            'offer5'     => 'finished',
        ];
        return $steps[$current_step];
    }

    /**
     * @param string $current_step
     * @return string
     */
    protected function _prior_step(string $current_step)
    {
        $steps = [
            'details'    => '',
            'offer1'     => 'details',
            'general'    => 'offer1',
            'offer2'     => 'general',
            'checkout'   => 'offer2',
            'offer3'     => 'checkout',
            'appearance' => 'offer3',
            'offer4'     => 'appearance',
            'mail'       => 'offer4',
            'offer5'     => 'mail',
            'finished'   => 'offer5',
        ];
        return $steps[$current_step];
    }
}
