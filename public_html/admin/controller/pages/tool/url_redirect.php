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

class ControllerPagesToolURLRedirect extends AController
{
    protected $error = [];

    public function main()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/url_redirect');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->initBreadcrumb();
        $this->document->addBreadcrumb([
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ]);
        $this->document->addBreadcrumb([
            'href'      => $this->html->getSecureURL('tool/url_redirect'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
            'current'   => true,
        ]);

        $gridSettings = [
            'table_id'       => 'redirect_grid',
            'url'            => $this->html->getSecureURL('listing_grid/url_redirect'),
            'editurl'        => $this->html->getSecureURL('listing_grid/url_redirect/update'),
            'update_field'   => $this->html->getSecureURL('listing_grid/url_redirect/update_field'),
            'sortname'       => 'priority',
            'sortorder'      => 'asc',
            'actions'        => [
                'edit'   => [
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('tool/url_redirect/update', '&id=%ID%'),
                ],
                'delete' => [
                    'text' => $this->language->get('button_delete'),
                ],
            ],
            'multiaction_options' => [
                'delete' => $this->language->get('text_delete_selected'),
                'save'   => $this->language->get('text_save_selected'),
            ],
            'columns_search' => true,
            'sortable'       => true,
            // show rule comments on hover
            'grid_ready'     => "$('#redirect_grid span.tooltips').tooltip({container: 'body'});",
        ];

        $gridSettings['colNames'] = [
            $this->language->get('column_id'),
            $this->language->get('column_source'),
            $this->language->get('column_action'),
            $this->language->get('column_status'),
            $this->language->get('column_hits'),
            $this->language->get('column_last_hit'),
        ];
        $gridSettings['colModel'] = [
            [
                'name'     => 'id',
                'index'    => 'id',
                'width'    => 40,
                'align'    => 'center',
                'sorttype' => 'int',
                'search'   => false,
            ],
            [
                'name'     => 'source',
                'index'    => 'source',
                'width'    => 300,
                'align'    => 'left',
                'sorttype' => 'string',
            ],
            [
                'name'     => 'url_action',
                'index'    => 'url_action',
                'width'    => 60,
                'align'    => 'center',
                'sorttype' => 'int',
            ],
            [
                'name'   => 'status',
                'index'  => 'status',
                'width'  => 70,
                'align'  => 'center',
                'search' => false,
            ],
            [
                'name'     => 'hits',
                'index'    => 'hits',
                'width'    => 60,
                'align'    => 'center',
                'sorttype' => 'int',
                'search'   => false,
            ],
            [
                'name'   => 'last_hit',
                'index'  => 'last_hit',
                'width'  => 110,
                'align'  => 'center',
                'search' => false,
            ],
        ];

        $grid = $this->dispatch('common/listing_grid', [$gridSettings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('insert', $this->html->getSecureURL('tool/url_redirect/insert'));
        $this->view->assign('help_url', $this->gen_help_url());
        $this->view->batchAssign($this->language->getASet());
        $this->processTemplate('pages/tool/url_redirect.tpl');

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function insert()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/url_redirect');
        /** @var ModelToolURLRedirect $model */
        $model = $this->loadModel('tool/url_redirect');

        if ($this->request->is_POST() && $this->_validateForm($model)) {
            // unchecked switches may be missing in the post
            $id = $model->addRedirect($this->request->post + ['status' => 0, 'keep_query' => 0]);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('tool/url_redirect/update', '&id=' . $id));
        }
        $this->_getForm($model);
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/url_redirect');
        /** @var ModelToolURLRedirect $model */
        $model = $this->loadModel('tool/url_redirect');
        $id = (int)$this->request->get['id'];

        if (!$model->getRedirect($id)) {
            $this->session->data['warning'] = $this->language->get('error_not_found');
            redirect($this->html->getSecureURL('tool/url_redirect'));
        }
        if ($this->request->is_POST() && $this->_validateForm($model, $id)) {
            $model->updateRedirect($id, $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('tool/url_redirect/update', '&id=' . $id));
        }
        $this->_getForm($model, $id);
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _getForm(ModelToolURLRedirect $model, int $id = 0): void
    {
        $redirect = $id ? $model->getRedirect($id) : [];
        $defaults = [
            'source'      => '',
            'source_host' => '',
            'source_http_headers' => '',
            'match_type'  => 'exact',
            'target'      => '',
            'action'      => 301,
            'keep_query'  => 0,
            'priority'    => 0,
            'status'      => 1,
            'comment'     => '',
        ];
        foreach ($defaults as $field => $default) {
            $this->data[$field] = $this->request->post[$field] ?? $redirect[$field] ?? $default;
        }
        $this->data['error'] = $this->error;
        $this->data['cancel'] = $this->html->getSecureURL('tool/url_redirect');
        // "saved_list" restores the grid search and paging the user had before opening the form
        $this->data['list_url'] = $this->html->getSecureURL('tool/url_redirect', '&saved_list=redirect_grid');
        $this->data['insert'] = $this->html->getSecureURL('tool/url_redirect/insert');
        $this->data['action'] = $id
            ? $this->html->getSecureURL('tool/url_redirect/update', '&id=' . $id)
            : $this->data['insert'];
        $this->data['update'] = $id
            ? $this->html->getSecureURL('listing_grid/url_redirect/update_field', '&id=' . $id)
            : '';
        $this->data['heading_title'] = $id
            ? $this->language->get('text_edit_redirect')
            : $this->language->get('text_add_redirect');

        $this->document->setTitle($this->data['heading_title']);
        $this->document->initBreadcrumb([
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false,
        ]);
        $this->document->addBreadcrumb([
            'href' => $this->html->getSecureURL('tool/url_redirect'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: ',
        ]);
        $this->document->addBreadcrumb([
            'href' => $this->data['action'],
            'text' => $this->data['heading_title'],
            'separator' => ' :: ',
            'current' => true,
        ]);

        $form = new AForm($id ? 'HS' : 'ST');
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

        $fields = &$this->data['form']['fields'];
        $fields['status'] = $form->getFieldHtml([
            'type'  => 'checkbox',
            'name'  => 'status',
            'value' => $this->data['status'],
            'style' => 'btn_switch',
        ]);
        $matchTypes = [];
        foreach (ModelToolURLRedirect::MATCH_TYPES as $type) {
            $matchTypes[$type] = $this->language->get('text_match_type_' . $type);
        }
        $fields['match_type'] = $form->getFieldHtml([
            'type'    => 'selectbox',
            'name'    => 'match_type',
            'options' => $matchTypes,
            'value'   => $this->data['match_type'],
        ]);
        $fields['source_host'] = $form->getFieldHtml([
            'type'        => 'input',
            'name'        => 'source_host',
            'value'       => $this->data['source_host'],
            'placeholder' => $this->language->get('text_any_host'),
            'style'       => 'large-field',
        ]);
        $fields['source'] = $form->getFieldHtml([
            'type'  => 'input',
            'name'  => 'source',
            'value' => $this->data['source'],
            'style' => 'large-field',
        ]);
        $fields['source_http_headers'] = $form->getFieldHtml([
            'type'        => 'textarea',
            'name'        => 'source_http_headers',
            'value'       => $this->data['source_http_headers'],
            'placeholder' => "User-Agent: Googlebot\nAccept-Language: ru",
            'style'       => 'large-field',
        ]);
        $actions = [];
        foreach (ModelToolURLRedirect::ACTIONS as $action) {
            $actions[$action] = $this->language->get('text_action_' . $action);
        }
        $fields['action'] = $form->getFieldHtml([
            'type'    => 'selectbox',
            'name'    => 'action',
            'options' => $actions,
            'value'   => $this->data['action'],
        ]);
        $fields['target'] = $form->getFieldHtml([
            'type'  => 'input',
            'name'  => 'target',
            'value' => $this->data['target'],
            'style' => 'large-field',
        ]);
        $fields['keep_query'] = $form->getFieldHtml([
            'type'  => 'checkbox',
            'name'  => 'keep_query',
            'value' => $this->data['keep_query'],
            'style' => 'btn_switch',
        ]);
        $fields['priority'] = $form->getFieldHtml([
            'type'  => 'number',
            'name'  => 'priority',
            'value' => (int)$this->data['priority'],
            'style' => 'small-field',
        ]);
        $fields['comment'] = $form->getFieldHtml([
            'type'  => 'textarea',
            'name'  => 'comment',
            'value' => $this->data['comment'],
            'style' => 'large-field',
        ]);
        if ($redirect) {
            $fields['hits'] = $form->getFieldHtml([
                'type' => 'label',
                'name' => 'hits',
                'text' => (string)(int)$redirect['hits'],
            ]);
            $fields['last_hit'] = $form->getFieldHtml([
                'type' => 'label',
                'name' => 'last_hit',
                'text' => $this->formatDateTime((string)$redirect['last_hit']) ?: '-',
            ]);
        }
        unset($fields);

        $this->view->assign('success', $this->session->data['success']);
        unset($this->session->data['success']);
        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('help_url', $this->gen_help_url('redirect'));
        $this->view->batchAssign($this->language->getASet());
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/tool/url_redirect_form.tpl');
    }

    protected function _validateForm(ModelToolURLRedirect $model, int $id = 0): bool
    {
        if (!$this->user->canModify('tool/url_redirect')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        $data = array_merge($id ? $model->getRedirect($id) : [], $this->request->post);
        $this->error = array_merge($this->error, $model->validateRedirect($data, $id));
        $this->extensions->hk_ValidateData($this);
        if ($this->error && empty($this->error['warning'])) {
            $this->error['warning'] = implode('<br>', array_values($this->error));
        }
        return !$this->error;
    }

    protected function formatDateTime(string $date): string
    {
        return dateISO2Display(
            $date,
            $this->language->get('date_format_short') . ' ' . $this->language->get('time_format')
        );
    }
}
