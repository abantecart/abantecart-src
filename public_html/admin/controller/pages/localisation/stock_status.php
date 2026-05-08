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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesLocalisationStockStatus extends AController
{
    public $error = [];

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb([
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ]);
        $this->document->addBreadcrumb([
            'href'      => $this->html->getSecureURL('localisation/stock_status'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
            'current'   => true,
        ]);

        $grid_settings = [
            'table_id'       => 'stock_grid',
            'url'            => $this->html->getSecureURL('listing_grid/stock_status'),
            'editurl'        => $this->html->getSecureURL('listing_grid/stock_status/update'),
            'update_field'   => $this->html->getSecureURL('listing_grid/stock_status/update_field'),
            'sortname'       => 'name',
            'sortorder'      => 'asc',
            'columns_search' => false,
            'actions'        => [
                'edit'   => [
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('localisation/stock_status/update', '&stock_status_id=%ID%'),
                ],
                'save'   => [
                    'text' => $this->language->get('button_save'),
                ],
                'delete' => [
                    'text' => $this->language->get('button_delete'),
                ],
            ],
        ];

        $grid_settings['colNames'] = [
            $this->language->get('column_name'),
        ];
        $grid_settings['colModel'] = [
            [
                'name'  => 'name',
                'index' => 'name',
                'width' => 600,
                'align' => 'left',
            ],
        ];

        $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());

        $this->view->assign('insert', $this->html->getSecureURL('localisation/stock_status/insert'));
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());

        $this->view->assign('help_url', $this->gen_help_url('stock_status_listing'));
        $this->processTemplate('pages/localisation/stock_status_list.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function insert()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->document->setTitle($this->language->get('heading_title'));
        $post = $this->request->post;
        if ($this->request->is_POST() && $this->_validateForm($post)) {
            $post['language_id'] = $this->language->getContentLanguageID();
            $this->data['stock_status_id'] = $this->model_localisation_stock_status->addStockStatus($post);
            $this->extensions->hk_ProcessData($this, __FUNCTION__, ['stock_status_id' => $this->data['stock_status_id']]);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect(
                $this->html->getSecureURL(
                    'localisation/stock_status/update',
                    '&stock_status_id=' . $this->data['stock_status_id']
                )
            );
        }
        $this->_getForm();
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->setTitle($this->language->get('heading_title'));
        $stockStatusId = (int)$this->request->get['stock_status_id'];
        if ($this->request->is_POST() && $this->_validateForm($this->request->post)) {
            $this->model_localisation_stock_status->editStockStatus($stockStatusId, $this->request->post);
            $this->extensions->hk_ProcessData($this, __FUNCTION__, ['stock_status_id' => $stockStatusId]);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('localisation/stock_status/update', '&stock_status_id=' . $stockStatusId));
        }
        $this->_getForm();
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _getForm()
    {
        $this->data = [];
        $this->data['error'] = $this->error;
        $this->data['cancel'] = $this->html->getSecureURL('localisation/stock_status');

        $this->document->initBreadcrumb([
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ]);
        $this->document->addBreadcrumb([
            'href'      => $this->html->getSecureURL('localisation/stock_status'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
        ]);

        $stockStatusId = (int)$this->request->get['stock_status_id'];

        if (isset($this->request->post['stock_status'])) {
            $this->data['stock_status'] = $this->request->post['stock_status'];
        } elseif ($stockStatusId) {
            $this->data['stock_status'] = $this->model_localisation_stock_status->getStockStatusDescriptions(
                $stockStatusId,
                $this->language->getContentLanguageID()
            );
        } else {
            $this->data['stock_status'] = [];
        }

        if (!$stockStatusId) {
            $this->data['action'] = $this->html->getSecureURL('localisation/stock_status/insert');
            $this->data['heading_title'] = $this->language->get('text_insert') . ' ' . $this->language->get('text_status');
            $this->data['update'] = '';
            $form = new AForm('ST');
        } else {
            $this->data['action'] = $this->html->getSecureURL(
                'localisation/stock_status/update',
                '&stock_status_id=' . $stockStatusId
            );
            $this->data['heading_title'] = $this->language->get('text_edit') . ' ' . $this->language->get('text_status');
            $this->data['update'] = $this->html->getSecureURL(
                'listing_grid/stock_status/update_field', '&id=' . $stockStatusId
            );
            $form = new AForm('HS');
        }

        $this->document->addBreadcrumb([
            'href'      => $this->data['action'],
            'text'      => $this->data['heading_title'],
            'separator' => ' :: ',
            'current'   => true,
        ]);

        $form->setForm([
            'form_name' => 'editFrm',
            'update'    => $this->data['update'],
        ]);

        $this->data['form']['id'] = 'editFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml([
            'type'   => 'form',
            'name'   => 'editFrm',
            'action' => $this->data['action'],
            'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
        ]);
        $this->data['form']['submit'] = $form->getFieldHtml([
            'type' => 'button',
            'name' => 'submit',
            'text' => $this->language->get('button_save'),
        ]);
        $this->data['form']['cancel'] = $form->getFieldHtml([
            'type' => 'button',
            'name' => 'cancel',
            'text' => $this->language->get('button_cancel'),
        ]);
        $languageId = $this->language->getContentLanguageId();
        $this->data['form']['fields']['name'] = $form->getFieldHtml([
            'type'         => 'input',
            'name'         => 'stock_status[name]',
            'value'        => $this->data['stock_status']['name'],
            'required'     => true,
            'multilingual' => true,
        ]);

        $this->view->batchAssign($this->data);
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('language_id', $languageId);
        $this->view->assign('help_url', $this->gen_help_url('stock_status_edit'));
        $this->processTemplate('pages/localisation/stock_status_form.tpl');
    }

    protected function _validateForm($post)
    {
        if (!$this->user->canModify('localisation/stock_status')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (mb_strlen($post['stock_status']['name']) < 2 || mb_strlen($post['stock_status']['name']) > 128) {
            $this->error['name'] = $this->language->get('error_name');
        }

        $this->extensions->hk_ValidateData($this, ['post' => $this->request->post]);
        return (!$this->error);
    }
}