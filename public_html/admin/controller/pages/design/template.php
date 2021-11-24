<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

class ControllerPagesDesignTemplate extends AController
{
    public $error = [];
    /** @var AConfigManager */
    protected $conf_mngr;

    public function main()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->document->addStyle(
            [
                'href' => RDIR_TEMPLATE.'stylesheet/layouts-manager.css',
                'rel'  => 'stylesheet',
            ]
        );

        $this->document->setTitle($this->language->get('heading_title'));

        // breadcrumb path
        $this->document->initBreadcrumb(
            [
                'href' => $this->html->getSecureURL('index/home'),
                'text' => $this->language->get('text_home'),
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'    => $this->html->getSecureURL('design/template'),
                'text'    => $this->language->get('heading_title'),
                'current' => true,
            ]
        );

        $this->data['current_url'] = $this->html->getSecureURL('design/template');
        $this->data['form_store_switch'] = $this->html->getStoreSwitcher();
        $this->data['help_url'] = $this->gen_help_url('set_storefront_template');
        $this->loadLanguage('setting/setting');
        $this->data['manage_extensions'] = $this->html->buildElement(
            [
                'type'  => 'button',
                'name'  => 'manage_extensions',
                'href'  => $this->html->getSecureURL('extension/extensions/template'),
                'text'  => $this->language->get('button_manage_extensions'),
                'title' => $this->language->get('button_manage_extensions'),
            ]
        );

        $this->data['store_id'] = (int) ($this->request->get['store_id'] ?? $this->config->get('current_store_id'));

        //check if store is active
        $store_info = $this->model_setting_store->getStore($this->data['store_id']);
        $this->data['status_off'] = '';
        if (!($store_info['status']?:'')) {
            $this->data['status_off'] = 'status_off';
        }

        //check if we have developer tools installed
        $dev_tools = $this->extensions->getExtensionsList(
            [
                'search' => 'developer_tools'
            ])->row;

        // get templates
        $this->data['templates'] = [];

        $this->load->library('config_manager');
        $conf_mngr = new AConfigManager();

        //get all enabled templates
        $tmpls = $conf_mngr->getTemplates('storefront', 1, $this->data['store_id']);
        $settings = $this->model_setting_setting->getSetting('appearance', $this->data['store_id']);
        $this->data['default_template'] = $settings['config_storefront_template'];
        $templates = [];

        foreach ($tmpls as $tmpl) {
            $templates[$tmpl] = [
                'name'          => $tmpl,
                'edit_url'      => $this->html->getSecureURL('design/template/edit', '&tmpl_id='.$tmpl),
                // define template type by directory inside core.
                // if it does not exist - it's extension, otherwise core-template
                'template_type' => is_dir(DIR_STOREFRONT.'view/'.$tmpl) ? 'core' : 'extension',
            ];

            //button for template cloning
            $target = '';
            if (is_null($dev_tools['status'])) {
                $href = $this->gen_help_url('template_dev_tools');
                $target = '_blank';
            } elseif ($dev_tools['status'] == 1) {
                $href = $this->html->getSecureURL('tool/developer_tools/create', '&template='.$tmpl);
            } else {
                $href = $this->html->getSecureURL('extension/extensions/edit', '&extension=developer_tools');
            }
            if ($templates[$tmpl]['template_type'] == 'core') {
                $templates[$tmpl]['clone_button'] = $this->html->buildElement(
                    [
                        'type'   => 'button',
                        'name'   => 'clone_button',
                        'href'   => $href,
                        'target' => $target,
                        'text'   => $this->language->get('text_clone_template'),
                    ]
                );
            }

            //button to extension
            if (!is_dir('storefront/view/'.$tmpl) && is_dir(DIR_EXT.$tmpl)) {
                $templates[$tmpl]['extn_url'] = $this->html->getSecureURL(
                        'extension/extensions/edit',
                        '&extension='.$tmpl
                    );
            }
            //set default
            if ($this->data['default_template'] != $tmpl) {
                $templates[$tmpl]['set_default_url'] = $this->html->getSecureURL(
                    'design/template/set_default',
                    '&tmpl_id='.$tmpl
                        .'&store_id='.$this->data['store_id']
                );
            }

            $preview_file = $tmpl.'/image/preview.jpg';
            if (is_file(DIR_EXT.$preview_file)) {
                $preview_img = HTTPS_EXT.$preview_file;
            } else {
                if (is_file('storefront/view/'.$tmpl.'/image/preview.jpg')) {
                    $preview_img = AUTO_SERVER.'storefront/view/'.$tmpl.'/image/preview.jpg';
                } else {
                    $preview_img = HTTPS_IMAGE.'no_image.jpg';
                }
            }
            $templates[$tmpl]['preview'] = $preview_img;
        }

        $this->data['templates'] = $templates;

        // Alert messages
        if (isset($this->session->data['warning'])) {
            $this->data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/design/template.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function set_default()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->user->canModify('design/template')) {
            $this->session->data['warning'] = $this->language->get('error_permission');
            redirect($this->html->getSecureURL('design/template'));
        }

        $this->loadModel('setting/setting');

        if ($this->request->get['store_id']) {
            $store_id = (int) $this->request->get['store_id'];
        } else {
            $store_id = (int) $this->config->get('current_store_id');
        }

        if ($this->request->get['tmpl_id']) {
            $this->model_setting_setting->editSetting(
                'appearance',
                [
                    'config_storefront_template' => $this->request->get['tmpl_id']
                ],
                $store_id
            );
            $this->config->set('config_storefront_template', $this->request->get['tmpl_id']);
            $this->session->data['success'] = $this->language->get('text_success');
        } else {
            $this->session->data['warning'] = $this->language->get('text_error');
        }

        $extWithLayouts = findExtensionsLayouts($this->request->get['tmpl_id']);
        if($extWithLayouts){
            $list = [];
            foreach($extWithLayouts as $key => $files){
                $list[] = '<a target="_blank" href="'.$this->html->getSecureURL('extension/extensions/edit','&extension='.$key).'">'
                        .$key.' ('.implode(', ',$files).')</a>';
            }

            $this->session->data['warning'] .= sprintf(
                $this->language->get('warning_extensions_with_layouts'),
                implode('<br>', $list)
            );
        }

        redirect($this->html->getSecureURL('design/template'));

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function edit()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('setting/setting');
        $this->document->setTitle($this->language->get('heading_title'));
        $tmpl_id = $this->request->get['tmpl_id'] ?? '';
        $this->data['tmpl_id'] = $tmpl_id;
        $languageId = $this->language->getContentLanguageID();
        $this->loadModel('setting/setting');
        $this->loadModel('setting/store');

        if (!$tmpl_id) {
            $this->session->data['warning'] = $this->language->get('text_error');
            redirect($this->html->getSecureURL('design/template'));
        }

        $this->data['group'] = $this->data['tmpl_id'] == 'default'
            ? 'appearance'
            : $this->data['tmpl_id'];

        if ($this->request->is_POST() && $this->_validate('appearance')) {
            $post = $this->request->post;
            foreach (['config_logo', 'config_mail_logo', 'config_icon'] as $n) {
                //use resource id as value
                //also check if language specific logo presents
                if (isset($post[$n.'_'.$languageId.'_resource_id'])) {
                    $post[$n.'_'.$languageId] = $post[$n.'_'.$languageId.'_resource_id'];
                    unset($post[$n.'_'.$languageId.'_resource_id']);
                }
                if (isset($post[$n.'_resource_id'])) {
                    $post[$n] = $post[$n.'_resource_id'];
                    unset($post[$n.'_resource_id']);
                } elseif (isset($post[$n])) {
                    $post[$n] = html_entity_decode($post[$n], ENT_COMPAT, 'UTF-8');
                }
            }

            $this->model_setting_setting->editSetting($this->data['group'], $post, $this->request->get['store_id']);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->data['redirect_url'] = $this->html->getSecureURL('design/template/edit', '&tmpl_id='.$tmpl_id);
            $this->extensions->hk_ProcessData($this, __METHOD__);
            redirect($this->data['redirect_url']);
        }

        $this->data['store_id'] = (int) ($this->request->get['store_id'] ?? $this->config->get('current_store_id'));

        $this->data['error'] = $this->error;

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('design/template'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if (isset($this->session->data['error'])) {
            $this->error['warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        }

        $this->data['cancel'] = $this->html->getSecureURL('design/template/edit', '&tmpl_id='.$tmpl_id);
        $this->data['back'] = $this->html->getSecureURL('design/template');

        $this->load->library('config_manager');
        $this->conf_mngr = new AConfigManager();

        //set control buttons
        $tmpls = $this->conf_mngr->getTemplates('storefront');
        $templates = [];
        foreach ($tmpls as $tmpl) {
            //skip current template
            if ($tmpl != $tmpl_id) {
                $templates[$tmpl] = [
                    'name' => $tmpl,
                    'href' => $this->html->getSecureURL('design/template/edit', '&tmpl_id='.$tmpl),
                ];
            }
        }

        $this->data['templates'] = $templates;
        $this->data['current_template'] = $tmpl_id;

        //button for template cloning
        $dev_tools = $this->extensions->getExtensionsList(
            [
                'search' => 'developer_tools'
            ])->row;
        $target = '';
        if (is_null($dev_tools['status'])) {
            $href = $this->gen_help_url('template_dev_tools');
            $target = '_blank';
        } elseif ($dev_tools['status'] == 1) {
            $href = $this->html->getSecureURL('tool/developer_tools/create');
        } else {
            $href = $this->html->getSecureURL('extension/extensions/edit', '&extension=developer_tools');
        }
        //NOTE: need to show different icon and message if dev tools extension is not installed
        $this->data['clone_button'] = $this->html->buildElement(
            [
                'type'   => 'button',
                'name'   => 'clone_button',
                'href'   => $href,
                'target' => $target,
                'text'   => $this->language->get('text_clone_template'),
            ]
        );

        $this->data['settings'] = $this->model_setting_setting->getSetting(
            $this->data['group'],
            $this->data['store_id']
        );

        unset($this->data['settings']['one_field']); //remove sign of single form field
        foreach ($this->data['settings'] as $key => $value) {
            if (isset($this->request->post[$key])) {
                $this->data['settings'][$key] = $this->request->post[$key];
            }
        }
        //check if store is active
        $store_info = $this->model_setting_store->getStore($this->data['store_id']);
        $this->data['status_off'] = '';
        if (!$store_info['status']) {
            $this->data['status_off'] = 'status_off';
        }
        //check if template is used
        $settings = $this->model_setting_setting->getSetting(
            'appearance',
            $this->data['store_id']
        );
        $this->data['default_template'] = $settings['config_storefront_template'];
        if ($this->data['default_template'] != $tmpl_id) {
            $this->data['status_off'] = 'status_off';
        }

        $this->_getForm();

        $preview_file = $tmpl_id.'/image/preview.jpg';
        if (is_file(DIR_EXT.$preview_file)) {
            $preview_img = HTTPS_EXT.$preview_file;
        } else {
            if (is_file('storefront/view/'.$tmpl_id.'/image/preview.jpg')) {
                $preview_img = AUTO_SERVER.'storefront/view/'.$tmpl_id.'/image/preview.jpg';
            } else {
                $preview_img = HTTPS_IMAGE.'no_image.jpg';
            }
        }
        $this->data['preview_img'] = $preview_img;
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());
        $this->view->assign('help_url', $this->gen_help_url('edit_storefront_template'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/design/template_edit.tpl');

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _getForm()
    {
        $this->data['action'] = $this->html->getSecureURL(
            'design/template/edit',
            '&tmpl_id='.$this->data['tmpl_id'].'&store_id='.$this->data['store_id']
        );
        $this->data['form_title'] = $this->language->get('text_edit').' '.$this->language->get('heading_title');
        $this->data['update'] = $this->html->getSecureURL(
            'listing_grid/setting/update_field',
            '&group='.$this->data['group'].'&store_id='.$this->data['store_id'].'&tmpl_id='.$this->data['tmpl_id']
        );

        $form = new AForm('HS');
        $form->setForm(
            [
                'form_name' => 'templateFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'templateFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'templateFrm',
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
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]
        );

        $this->data['settings']['tmpl_id'] = $this->data['tmpl_id'];
        if ($this->data['settings']['tmpl_id'] == 'default') {
            //get default setting fields for template
            $this->data['settings']['tmpl_id'] = 'appearance';
        }
        $this->data['form']['fields'] = $this->conf_mngr->getFormFields(
            'appearance',
            $form,
            $this->data['settings']
        );
        $this->data['entry_logo'] = $this->language->get('entry_default_logo');
        $this->data['entry_mail_logo'] = $this->language->get('entry_default_mail_logo');

        $languages = $this->language->getActiveLanguages();
        $contentLanguage = $languages[ $this->language->getContentLanguageCode() ?? $this->language->getLanguageCode() ];
        if(count($languages) > 1){
            //language related logos
            $langName = mb_strtolower($contentLanguage['name']);
            $this->data['entry_logo_'.$langName] = ucfirst($langName).' '.$this->language->get('entry_logo');
            $this->data['entry_mail_logo_'.$langName] = ucfirst($langName).' '.$this->language->get('entry_mail_logo');
        }

        $resources_scripts = $this->dispatch(
            'responses/common/resource_library/get_resources_scripts',
            [
                'object_name' => 'store',
                'object_id'   => (int) $this->data['store_id'],
                'types'       => ['image'],
                'onload'      => true,
                'mode'        => 'single',
            ]
        );
        $this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();
    }

    /**
     * @param string $group
     *
     * @return bool
     * @throws AException
     */
    protected function _validate($group)
    {
        if (!$this->user->canModify('design/template')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->library('config_manager');
        $config_mngr = new AConfigManager();
        $result = $config_mngr->validate($group, $this->request->post);
        $this->error = $result['error'];
        $this->request->post = $result['validated']; // for changed data saving

        $this->extensions->hk_ValidateData($this);

        if (!$this->error) {
            return true;
        } else {
            if (!isset($this->error['warning'])) {
                $this->error['warning'] = $this->language->get('error_required_data');
            }
            return false;
        }
    }
}