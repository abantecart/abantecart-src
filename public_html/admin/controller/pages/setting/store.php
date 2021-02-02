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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesSettingStore extends AController
{
    public $error = [];
    public $data = [];

    public function insert()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('setting/store');

        $this->document->setTitle($this->language->get('heading_title'));
        if ($this->request->is_POST() && $this->_validateForm()) {
            $store_id = $this->model_setting_store->addStore($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('setting/setting', '&active=details&store_id='.$store_id));
        }
        $this->getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('setting/store');

        //nothing to do here for default store
        if ($this->request->get['store_id'] == 0) {
            redirect($this->html->getSecureURL('setting/setting', '&active=details&store_id='.$this->request->get['store_id']));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->is_POST() && $this->_validateForm()) {
            $this->model_setting_store->editStore($this->request->get['store_id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('setting/store/update', '&store_id='.$this->request->get['store_id']));
        }
        $this->getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function delete()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('setting/store');

        //nothing to do here for default store
        if ($this->request->get['store_id'] == 0) {
            redirect($this->html->getSecureURL('setting/setting', '&active=details&store_id='.$this->request->get['store_id']));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->get['store_id']) && $this->_validateDelete()) {
            $this->model_setting_store->deleteStore($this->request->get['store_id']);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('setting/setting', '&active=details&store_id=0'));
        }
        $this->getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function getForm()
    {

        $this->data = [];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->data['error'] = $this->error;
        $this->data['token'] = $this->session->data['token'];
        $this->data['content_language_id'] = $this->session->data['content_language_id'];
        $this->data['form_language_switch'] = $this->html->getContentLanguageSwitcher();

        $this->document->initBreadcrumb(
            [
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
            'href'      => $this->html->getSecureURL('setting/setting'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
            'current'   => true,
            ]
        );

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        if (isset($this->request->get['store_id'])) {
            $this->data['delete'] = $this->html->getSecureURL('setting/store/delete', '&store_id='.$this->request->get['store_id']);
            $this->data['edit_settings'] = $this->html->getSecureURL('setting/setting', '&store_id='.$this->request->get['store_id']);
        } else {
            $this->data['delete'] = '';
            $this->data['edit_settings'] = '';
        }

        if (!isset($this->request->get['store_id'])) {
            $this->data['cancel'] = $this->html->getSecureURL('setting/setting');
        } else {
            $this->data['cancel'] = $this->html->getSecureURL('setting/store/update', '&store_id='.$this->request->get['store_id']);
        }

        if (isset($this->request->get['store_id'])) {
            $this->data['store_id'] = $this->request->get['store_id'];
        } else {
            $this->data['store_id'] = 0;
        }

        $stores = $store_options = [];
        $stores[0] = ['name' => $this->language->get('text_default')];
        $this->loadModel('setting/store');
        $results = $this->model_setting_store->getStores();
        foreach ($results as $result) {
            $stores[$result['store_id']] = [
                'name' => $result['alias'],
                'href' => $this->html->getSecureURL('setting/setting', '&active='.$this->data['active'].'&store_id='.$result['store_id']),
            ];
            $store_options[$result['store_id']] = $result['alias'];
        }

        if ($this->data['delete']) {
            $this->data['delete_store_button'] = $this->html->buildElement(
                [
                'type'  => 'button',
                'title' => $this->language->get('button_delete_store'),
                'text'  => '&nbsp;',
                'style' => 'icon_delete',
                'href'  => $this->html->getSecureURL('setting/store/delete', '&store_id='.$this->request->get['store_id']),
                ]
            );
        }

        if ($this->data['edit_settings']) {
            $this->data['edit_settings_button'] = $this->html->buildElement(
                [
                'type'  => 'button',
                'title' => $this->language->get('button_edit_settings'),
                'text'  => $this->language->get('button_edit_settings'),
                'href'  => $this->data['edit_settings'],
                'style' => 'button2',
                ]
            );
        }

        $this->data['cancel_store_button'] = $this->html->buildElement(
            [
            'type'  => 'button',
            'text'  => $this->language->get('button_cancel'),
            'href'  => $this->data['cancel'],
            'style' => 'button2',
            ]
        );

        $this->data['new_store_button'] = $this->html->buildElement(
            [
            'type'  => 'button',
            'title' => $this->language->get('button_add_store'),
            'text'  => '&nbsp;',
            'href'  => $this->html->getSecureURL('setting/store/insert'),
            ]
        );
        $store_info = [];
        if (isset($this->request->get['store_id']) && $this->request->is_GET()) {
            $store_info = $this->model_setting_store->getStore($this->request->get['store_id']);
        } else {
            if ($this->request->is_POST()) {
                $store_info = $this->request->post;
            } else {
                //set status to ON for newly created stores
                $store_info['status'] = 1;
            }
        }

        if (!isset($this->request->get['store_id'])) {
            $this->data['action'] = $this->html->getSecureURL('setting/store/insert');
            $this->data['form_title'] = $this->language->get('button_add_store');
            $this->data['update'] = '';
            $form = new AForm('ST');
        } else {
            $this->data['action'] = $this->html->getSecureURL('setting/store/update', '&store_id='.$this->request->get['store_id']);
            $this->data['form_title'] = $this->language->get('text_edit_store');
            $this->data['update'] = $this->html->getSecureURL('listing_grid/store/update_field', '&id='.$this->request->get['store_id']);
            $form = new AForm('HS');
        }

        $form->setForm(
            [
            'form_name' => 'storeFrm',
            'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'storeFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
            'type'   => 'form',
            'name'   => 'storeFrm',
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
        $this->data['form']['fields']['general']['status'] = $form->getFieldHtml(
            [
            'type'  => 'checkbox',
            'name'  => 'status',
            'value' => $store_info['status'],
            'style' => 'btn_switch',
            ]
        );
        $this->data['form']['fields']['general']['name'] = $form->getFieldHtml(
            [
            'type'     => 'input',
            'name'     => 'name',
            'value'    => $store_info['name'],
            'required' => true,
            ]
        );
        $this->data['form']['fields']['general']['alias'] = $form->getFieldHtml(
            [
            'type'  => 'input',
            'name'  => 'alias',
            'value' => $store_info['alias'],
            ]
        );
        if (empty($store_info['store_description'][$this->session->data['content_language_id']]['description'])) {
            $store_info['store_description'] = $this->model_setting_store->getStoreDescriptions($this->request->get['store_id']);
        }
        $this->data['form']['fields']['general']['description'] = $form->getFieldHtml(
            [
            'type'  => 'texteditor',
            'name'  => 'store_description['.$this->session->data['content_language_id'].'][description]',
            'value' => $store_info['store_description'][$this->session->data['content_language_id']]['description'],
            'style' => 'xl-field',
            ]
        );
        $this->data['form']['fields']['general']['url'] = $form->getFieldHtml(
            [
            'type'     => 'input',
            'name'     => 'config_url',
            'value'    => $store_info['config_url'],
            'required' => true,
            'style'    => 'large-field',
            ]
        );
        $this->data['form']['fields']['general']['ssl_url'] = $form->getFieldHtml(
            [
            'type'     => 'input',
            'name'     => 'config_ssl_url',
            'value'    => $store_info['config_ssl_url'],
            'required' => true,
            'style'    => 'large-field',
            ]
        );

        if (!isset($this->request->get['store_id'])) {
            $store_options = array_merge(['' => ' --- '], $store_options);
            $this->data['form']['fields']['general']['clone_store'] = $form->getFieldHtml(
                [
                'type'    => 'selectbox',
                'name'    => 'clone_store',
                'options' => $store_options,
                'value'   => 0,
                'style'   => "no-save",
                ]
            );
        }

        $resources_scripts = $this->dispatch(
            'responses/common/resource_library/get_resources_scripts',
            [
                'object_name' => '',
                'object_id'   => '',
                'types'       => ['image'],
            ]
        );
        $this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();

        $this->view->batchAssign($this->data);
        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

        $this->processTemplate('pages/setting/store.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _validateForm()
    {
        if (!$this->user->canModify('setting/store')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (!$this->request->post['name']) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (!$this->request->post['config_url']) {
            $this->error['url'] = $this->language->get('error_url');
        }

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

    protected function _validateDelete()
    {
        if (!$this->user->canModify('setting/store')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->loadModel('sale/order');
        $store_total = $this->model_sale_order->getTotalOrdersByStoreId($this->request->get['store_id']);
        if ($store_total) {
            $this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
        }

        $this->extensions->hk_ValidateData($this);

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
