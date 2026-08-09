<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
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

class ControllerPagesToolSeoUrl extends AController
{
    protected $error = [];

    public function main()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/seo_url');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->initBreadcrumb();
        $this->document->addBreadcrumb([
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ]);
        $this->document->addBreadcrumb([
            'href'      => $this->html->getSecureURL('tool/seo_url'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
            'current'   => true,
        ]);

        $gridSettings = [
            'table_id'       => 'seo_url_grid',
            'url'            => $this->html->getSecureURL('listing_grid/seo_url'),
            'editurl'        => $this->html->getSecureURL('listing_grid/seo_url/delete'),
            'update_field'   => $this->html->getSecureURL('listing_grid/seo_url/update_field'),
            'sortname'       => 'seo_keyword',
            'sortorder'      => 'asc',
            'actions'        => [
                'edit'   => [
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('tool/seo_url/update', '&url_alias_id=%ID%'),
                ],
                'delete' => [
                    'text' => $this->language->get('button_delete'),
                ],
            ],
            'multiaction_options' => [
                'delete' => $this->language->get('text_delete_selected'),
            ],
            'columns_search' => true,
            'sortable'       => true,
        ];

        $gridSettings['colNames'] = [
            $this->language->get('column_seo_keyword'),
            $this->language->get('column_route'),
            $this->language->get('column_query'),
        ];
        $gridSettings['colModel'] = [
            [
                'name'     => 'seo_keyword',
                'index'    => 'seo_keyword',
                'width'    => 180,
                'align'    => 'left',
                'sorttype' => 'string',
            ],
            [
                'name'     => 'route',
                'index'    => 'route',
                'width'    => 130,
                'align'    => 'left',
                'sorttype' => 'string',
            ],
            [
                'name'     => 'query',
                'index'    => 'query',
                'width'    => 220,
                'align'    => 'left',
                'sorttype' => 'string',
            ],
        ];

        $grid = $this->dispatch('common/listing_grid', [$gridSettings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('insert', $this->html->getSecureURL('tool/seo_url/insert'));
        $this->view->assign('help_url', $this->gen_help_url());
        $this->view->batchAssign($this->language->getASet());
        $this->processTemplate('pages/tool/seo_url.tpl');

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function insert()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/seo_url');
        /** @var ModelToolSeoUrl $model */
        $model = $this->loadModel('tool/seo_url');

        if ($this->request->is_POST() && $this->_validateForm($model)) {
            $urlAliasId = $model->addSeoUrl($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('tool/seo_url/update', '&url_alias_id=' . $urlAliasId));
        }
        $this->_getForm($model);
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/seo_url');
        /** @var ModelToolSeoUrl $model */
        $model = $this->loadModel('tool/seo_url');
        $urlAliasId = (int)$this->request->get['url_alias_id'];

        if (!$model->getSeoUrl($urlAliasId)) {
            $this->session->data['warning'] = $this->language->get('error_not_found');
            redirect($this->html->getSecureURL('tool/seo_url'));
        }
        if ($this->request->is_POST() && $this->_validateForm($model, $urlAliasId)) {
            $model->updateSeoUrl($urlAliasId, $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('tool/seo_url/update', '&url_alias_id=' . $urlAliasId));
        }
        $this->_getForm($model, $urlAliasId);
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _getForm(ModelToolSeoUrl $model, int $urlAliasId = 0): void
    {
        $seoUrl = $urlAliasId ? $model->getSeoUrl($urlAliasId) : [];
        foreach (['seo_keyword', 'route', 'query'] as $field) {
            $this->data[$field] = $this->request->post[$field] ?? $seoUrl[$field] ?? '';
        }
        $this->data['language_id'] = (int)($this->request->post['language_id']
            ?? $seoUrl['language_id']
            ?? $this->language->getContentLanguageID());
        $this->data['error'] = $this->error;
        $this->data['cancel'] = $this->html->getSecureURL('tool/seo_url');
        $this->data['insert'] = $this->html->getSecureURL('tool/seo_url/insert');
        $this->data['action'] = $urlAliasId
            ? $this->html->getSecureURL('tool/seo_url/update', '&url_alias_id=' . $urlAliasId)
            : $this->data['insert'];
        $this->data['update'] = $urlAliasId
            ? $this->html->getSecureURL('listing_grid/seo_url/update_field', '&id=' . $urlAliasId)
            : '';
        $this->data['validate_url'] = $this->html->getSecureURL(
            'listing_grid/seo_url/validate',
            $urlAliasId ? '&id=' . $urlAliasId : ''
        );
        $this->data['heading_title'] = $urlAliasId
            ? $this->language->get('text_edit_seo_url')
            : $this->language->get('text_add_seo_url');

        $this->document->setTitle($this->data['heading_title']);
        $this->document->initBreadcrumb([
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false,
        ]);
        $this->document->addBreadcrumb([
            'href' => $this->html->getSecureURL('tool/seo_url'),
            'text' => $this->language->get('text_seo_urls'),
            'separator' => ' :: ',
        ]);
        $this->document->addBreadcrumb([
            'href' => $this->data['action'],
            'text' => $this->data['heading_title'],
            'separator' => ' :: ',
            'current' => true,
        ]);

        $form = new AForm($urlAliasId ? 'HS' : 'ST');
        $form->setForm(['form_name' => 'editFrm', 'update' => $this->data['update']]);
        $this->data['form']['form_open'] = $form->getFieldHtml([
            'type' => 'form',
            'name' => 'editFrm',
            'attr' => 'data-confirm-exit="true" class="aform form-horizontal"',
            'action' => $this->data['action'],
        ]);
        $this->data['form']['submit'] = $form->getFieldHtml([
            'type' => 'button', 'name' => 'submit', 'text' => $this->language->get('button_save'),
        ]);
        foreach (['seo_keyword', 'route', 'query'] as $field) {
            $this->data['form']['fields'][$field] = $form->getFieldHtml([
                'type' => 'input',
                'name' => $field,
                'value' => $this->data[$field],
                'required' => $field !== 'query',
                'style' => 'large-field',
            ]);
        }
        $this->data['form']['fields']['language_id'] = $form->getFieldHtml([
            'type' => 'hidden', 'name' => 'language_id', 'value' => $this->data['language_id'],
        ]);

        $this->view->assign('success', $this->session->data['success']);
        unset($this->session->data['success']);
        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('help_url', $this->gen_help_url('seo_url'));
        $this->view->batchAssign($this->language->getASet());
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/tool/seo_url_form.tpl');
    }

    protected function _validateForm(ModelToolSeoUrl $model, int $urlAliasId = 0): bool
    {
        if (!$this->user->canModify('tool/seo_url')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        $this->error = array_merge($this->error, $model->validateSeoUrl($this->request->post, $urlAliasId));
        $this->extensions->hk_ValidateData($this);
        if ($this->error && empty($this->error['warning'])) {
            $this->error['warning'] = implode('<br>', array_values($this->error));
        }
        return !$this->error;
    }
}
