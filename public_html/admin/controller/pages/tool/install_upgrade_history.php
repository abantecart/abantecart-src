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

class ControllerPagesToolInstallUpgradeHistory extends AController
{
    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadLanguage('extension/extensions');
        $this->document->initBreadcrumb();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('tool/install_upgrade_history'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->data['delete_button'] = $this->html->buildElement(
            [
                'type'  => 'button',
                'title' => $this->language->get('text_delete_history'),
                'href'  => $this->html->getSecureURL('tool/install_upgrade_history/delete', '&delete=1'),
            ]
        );

        $grid_settings = [
            //id of grid
            'table_id'       => 'install_upgrade_history',
            // url to load data from
            'url'            => $this->html->getSecureURL('listing_grid/install_upgrade_history'),
            // url to send data for edit / delete
            'editurl'        => $this->html->getSecureURL('listing_grid/install_upgrade_history'),
            'multiselect'    => 'false',
            // url to update one field
            'update_field'   => '',
            // default sort column
            'sortname'       => 'date_added',
            // actions
            'actions'        => '',
            'columns_search' => false,
            'sortable'       => true,
            'search_form'    => true
        ];

        $grid_settings ['colNames'] = [
            '#',
            $this->language->get('column_date_added'),
            $this->language->get('column_type'),
            $this->language->get('column_name'),
            $this->language->get('column_version'),
            $this->language->get('column_backup_date'),
            $this->language->get('column_backup_file'),
            $this->language->get('column_user'),
        ];
        $grid_settings ['colModel'] = [
            [
                'name'     => 'row_id',
                'index'    => 'row_id',
                'width'    => 10,
                'align'    => 'center',
                'sortable' => false,
                'search'   => false,
            ],
            [
                'name'     => 'date_added',
                'index'    => 'date_added',
                'width'    => 50,
                'align'    => 'center',
                'sortable' => false,
                'search'   => false,
            ],
            [
                'name'     => 'type',
                'index'    => 'type',
                'width'    => 50,
                'align'    => 'center',
                'sortable' => false,
                'search'   => false,
            ],
            [
                'name'     => 'name',
                'index'    => 'name',
                'width'    => 50,
                'align'    => 'center',
                'sortable' => false
            ],
            [
                'name'     => 'version',
                'index'    => 'version',
                'width'    => 20,
                'align'    => 'center',
                'sortable' => false,
                'search'   => false,
            ],
            [
                'name'     => 'backup_date',
                'index'    => 'backup_date',
                'width'    => 50,
                'align'    => 'center',
                'sortable' => false,
                'search'   => false,
            ],
            [
                'name'     => 'backup_file',
                'index'    => 'backup_file',
                'width'    => 70,
                'align'    => 'center',
                'sortable' => false,
                'search'   => false,
            ],
            [
                'name'     => 'user',
                'index'    => 'user',
                'width'    => 40,
                'align'    => 'center',
                'sortable' => false,
                'search'   => false,
            ],
        ];


        //prepare the filter form
        //Note: External search form needs to be named [grid_name]_search
        //		In this case it will be auto submitted to filter grid
        $form = new AForm();
        $form->setForm(['form_name' => 'install_upgrade_history_search']);
        $this->data['grid_search_form'] = [];
        $this->data['grid_search_form']['id'] = 'grid_search';
        $this->data['grid_search_form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'install_upgrade_history_search',
                'method' => 'get',
            ]
        );
        $this->data['grid_search_form']['submit'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'submit',
                'text' => $this->language->get('button_go'),
            ]
        );

        $this->data['grid_search_form']['fields']['type'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'type',
                'options' => [
                    ''          => $this->language->get('text_select'),
                    'error'     => $this->language->get('text_error', 'common/resource_library'),
                    'delete'    => $this->language->get('button_delete'),
                    'upgrade'   => $this->language->get('button_upgrade'),
                    'install'   => $this->language->get('text_install'),
                    'uninstall' => $this->language->get('text_uninstall'),
                ]
            ]
        );

        $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('help_url', $this->gen_help_url());
        $this->view->assign('search_form', $this->data['grid_search_form']);
        $this->view->batchAssign($this->data);

        if (isset($this->session->data['error'])) {
            $this->view->assign('error_warning', $this->session->data['error']);
            unset($this->session->data['error']);
        }
        if (isset($this->session->data['success'])) {
            $this->view->assign('success', $this->session->data['success']);
            unset($this->session->data['success']);
        }

        $this->processTemplate('pages/tool/install_upgrade_history.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function delete()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //prevent random click
        if (!$this->request->get['delete']) {
            redirect($this->html->getSecureURL('tool/install_upgrade_history'));
        }

        $this->loadLanguage('tool/install_upgrade_history');

        $this->loadModel('tool/install_upgrade_history');
        $this->model_tool_install_upgrade_history->deleteData();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->session->data['success'] = $this->language->get('text_delete_success');
        redirect($this->html->getSecureURL('tool/install_upgrade_history'));
    }
}