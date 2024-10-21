<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
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

class ControllerPagesLocalisationLanguage extends AController
{
    public $error = [];
    public $fields = ['name', 'code', 'locale', 'image', 'directory', 'sort_order', 'status'];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('localisation/language'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $grid_settings = [
            //id of grid
            'table_id'         => 'languages_grid',
            // url to load data from
            'url'              => $this->html->getSecureURL('listing_grid/language'),
            // url to send data for edit / delete
            'editurl'          => $this->html->getSecureURL('listing_grid/language/update'),
            // url to update one field
            'update_field'     => $this->html->getSecureURL('listing_grid/language/update_field'),
            // default sort column
            'sortname'         => 'sort_order',
            // columns for drag sort
            'drag_sort_column' => 'sort_order',
            // actions
            'actions'          => [
                'edit'   => [
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('localisation/language/update', '&language_id=%ID%'),
                ],
                'delete' => [
                    'text' => $this->language->get('button_delete'),
                ],
                'save'   => [
                    'text' => $this->language->get('button_save'),
                ],
            ],
        ];

        $form = new AForm();
        $form->setForm(
            [
                'form_name' => 'languages_grid_search',
            ]
        );

        $grid_search_form = [];
        $grid_search_form['id'] = 'languages_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'languages_grid_search',
                'action' => '',
            ]
        );
        $grid_search_form['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_go'),
                'style' => 'button1',
            ]
        );
        $grid_search_form['reset'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'reset',
                'text'  => $this->language->get('button_reset'),
                'style' => 'button2',
            ]
        );

        $grid_search_form['fields']['status'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'status',
                'options' => [
                    1  => $this->language->get('text_enabled'),
                    0  => $this->language->get('text_disabled'),
                    '' => $this->language->get('text_select_status'),
                ],
            ]
        );

        $grid_settings['search_form'] = true;

        $grid_settings['colNames'] = [
            $this->language->get('column_name'),
            $this->language->get('column_code'),
            $this->language->get('column_sort_order'),
            $this->language->get('entry_status'),
        ];
        $grid_settings['colModel'] = [
            [
                'name'     => 'name',
                'index'    => 'name',
                'width'    => 270,
                'align'    => 'center',
                'sorttype' => 'string',
            ],
            [
                'name'     => 'code',
                'index'    => 'code',
                'width'    => 70,
                'align'    => 'center',
                'sorttype' => 'string',
            ],
            [
                'name'     => 'sort_order',
                'index'    => 'sort_order',
                'width'    => 90,
                'align'    => 'center',
                'sorttype' => 'string',
                'search'   => false,
            ],
            [
                'name'     => 'status',
                'index'    => 'status',
                'width'    => 110,
                'align'    => 'center',
                'sortable' => false,
                'search'   => false,
            ],
        ];

        $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('search_form', $grid_search_form);

        $this->document->setTitle($this->language->get('heading_title'));
        $this->view->assign('insert', $this->html->getSecureURL('localisation/language/insert'));
        $this->view->assign('help_url', $this->gen_help_url('language_listing'));

        $this->view->assign(
            'manage_extensions',
            $this->html->buildElement(
                [
                    'type'  => 'button',
                    'name'  => 'manage_extensions',
                    'href'  => $this->html->getSecureURL('extension/extensions/language'),
                    'text'  => $this->language->get('button_manage_extensions'),
                    'title' => $this->language->get('button_manage_extensions'),
                ]
            )
        );

        $this->processTemplate('pages/localisation/language_list.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function insert()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));
        if ($this->request->is_POST() && $this->_validateForm()) {
            $language_id = $this->model_localisation_language->addLanguage($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('localisation/language/update', '&language_id=' . $language_id));
        }

        $this->_getForm();
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $language_id = (int)$this->request->get['language_id'];
        if (!$language_id) {
            redirect($this->html->getSecureURL('localisation/language'));
        }

        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->setTitle($this->language->get('heading_title'));
        if ($this->request->is_POST() && $this->_validateForm()) {
            $this->model_localisation_language->editLanguage($language_id, $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('localisation/language/update', '&language_id=' . $language_id));
        }
        $this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _getForm()
    {
        $this->data['error_warning'] = $this->error ? $this->error['warning'] : '';
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
                'href'      => $this->html->getSecureURL('localisation/language'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );

        $this->data['cancel'] = $this->html->getSecureURL('localisation/language');

        if (isset($this->request->get['language_id']) && $this->request->is_GET()) {
            $language_info = $this->model_localisation_language->getLanguage($this->request->get['language_id']);
        }

        foreach ($this->fields as $field) {
            $this->data[$field] = $this->request->post[$field] ?? $language_info[$field] ?? '';
        }

        if (!isset($this->request->get['language_id'])) {
            $this->data['action'] = $this->html->getSecureURL('localisation/language/insert');
            $this->data['heading_title'] = $this->language->get('text_insert') . '&nbsp;' . $this->language->get('text_language');
            $this->data['update'] = '';
            $form = new AForm('ST');
        } else {
            $this->data['action'] = $this->html->getSecureURL('localisation/language/update', '&language_id=' . $this->request->get['language_id']);
            $this->data['heading_title'] = $this->language->get('text_edit') . '&nbsp;' . $this->language->get('text_language') . ' - ' . $this->data['name'];
            $this->data['update'] = $this->html->getSecureURL('listing_grid/language/update_field', '&id=' . $this->request->get['language_id']);
            $form = new AForm('HS');
        }

        $this->document->addBreadcrumb(
            [
                'href'      => $this->data['action'],
                'text'      => $this->data['heading_title'],
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $form->setForm(
            [
                'form_name' => 'languageFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'languageFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'languageFrm',
                'action' => $this->data['action'],
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'submit',
                'text' => $this->language->get('button_save'),
            ]
        );
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'cancel',
                'text' => $this->language->get('button_cancel'),
            ]
        );

        $this->data['form']['fields']['status'] = $form->getFieldHtml(
            [
                'type'     => 'checkbox',
                'name'     => 'status',
                'style'    => 'btn_switch',
                'value'    => $this->data['status'],
                'help_url' => $this->gen_help_url('status'),
            ]
        );
        $this->data['form']['fields']['name'] = $form->getFieldHtml([
            'type'     => 'input',
            'name'     => 'name',
            'value'    => $this->data['name'],
            'required' => true,
            'help_url' => $this->gen_help_url('name'),
        ]);

        $this->data['form']['fields']['code'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'code',
                'value'    => $this->data['code'],
                'required' => true,
                'help_url' => $this->gen_help_url('code'),
            ]
        );

        $this->data['form']['fields']['locale'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'locale',
                'value'    => $this->data['locale'],
                'required' => true,
                'help_url' => $this->gen_help_url('locale'),
            ]
        );

        $this->data['form']['fields']['directory'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'directory',
                'value'    => $this->data['directory'],
                'required' => true,
                'help_url' => $this->gen_help_url('directory'),
            ]
        );

        $this->data['form']['fields']['sort_order'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'sort_order',
                'value'    => $this->data['sort_order'],
                'required' => true,
            ]
        );

        if (isset($this->request->get['language_id'])
            && sizeof($this->language->getAvailableLanguages()) > 1
        ) {
            if ($this->config->get('translate_override_existing')) {
                $this->data['override_text_note'] = sprintf(
                    $this->language->get('text_translate_override_existing'),
                    $this->html->getSecureURL('setting/setting/details')
                );
            }

            $form2 = new AForm();
            $form2->setForm(
                [
                    'form_name' => 'languageLoadFrm',
                ]
            );

            $this->data['form2']['id'] = 'languageFrm';
            $this->data['form2']['form_open'] = $form2->getFieldHtml(
                [
                    'type' => 'form',
                    'name' => 'languageLoadFrm',
                    'attr' => 'class="aform form-horizontal"',
                ]
            );
            $this->data['form2']['load_data'] = $form2->getFieldHtml(
                [
                    'type'  => 'button',
                    'name'  => 'load_data',
                    'text'  => $this->language->get('button_load_language'),
                    'style' => 'button3',
                ]
            );

            $language_id = (int)$this->request->get['language_id'];
            $langList = array_column($this->language->getAvailableLanguages(), 'name', 'language_id');
            unset($langList[$language_id]);
            if (count($langList) > 1) {
                $langList = ['' => $this->language->get('text_select')] + $langList;
            }

            $this->data['form2']['fields']['language_selector'] = $form2->getFieldHtml(
                [
                    'type'    => 'selectbox',
                    'name'    => 'source_language',
                    'value'   => '',
                    'options' => $langList,
                ]
            );
            $this->data['form2']['fields']['language_id'] = $form2->getFieldHtml(
                [
                    'type'  => 'hidden',
                    'name'  => 'language_id',
                    'value' => $language_id,
                ]
            );

            $translate_methods = $this->language->getTranslationMethods();
            $this->data['form2']['fields']['translate_method_selector'] = $form2->getFieldHtml(
                [
                    'type'    => 'selectbox',
                    'name'    => 'translate_method',
                    'value'   => '',
                    'options' => $translate_methods,
                ]
            );
            $this->data['form2']['build_task_url'] = $this->html->getSecureURL('r/localisation/language_description/buildTask');
            $this->data['form2']['complete_task_url'] = $this->html->getSecureURL('r/localisation/language_description/complete');
            $this->data['form']['abort_task_url'] = $this->html->getSecureURL('r/localisation/language_description/abort');

            //check for incomplete tasks
            $task_name = 'description_translation';
            $tm = new ATaskManager();
            $incomplete = $tm->getTasks(
                [
                    'filter' => [
                        'name' => $task_name,
                    ],
                ]
            );

            foreach ($incomplete as $incm_task) {
                //show all incomplete tasks for Top Administrator user group
                if ($this->user->getUserGroupId() != 1) {
                    if ($incm_task['starter'] != $this->user->getId()) {
                        continue;
                    }
                    //rename task to prevent collision with new
                    if ($incm_task['name'] == $task_name) {
                        $tm->updateTask(
                            $incm_task['task_id'],
                            ['name' => $task_name . '_' . date('YmdHis')]
                        );
                    }
                }
                //define incomplete tasks by last time run
                $max_exec_time = (int)$incm_task['max_execution_time'];
                if (!$max_exec_time) {
                    //if no limitations for execution time for task - think it's 2 hours
                    $max_exec_time = 7200;
                }
                if (time() - dateISO2Int($incm_task['last_time_run']) > $max_exec_time) {
                    $this->data['incomplete_tasks_url'] = $this->html->getSecureURL('r/localisation/language_description/incomplete');
                    break;
                }
            }
        } else {
            $this->data['entry_create_language_note'] = $this->language->get('create_language_note');
        }

        $this->view->assign('help_url', $this->gen_help_url('language_edit'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/localisation/language_form.tpl');
    }

    protected function _validateForm()
    {
        if (!$this->user->canModify('localisation/language')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        $len = mb_strlen($this->request->post['name']);
        if ($len < 2 || $len > 32) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (mb_strlen($this->request->post['code']) < 2) {
            $this->error['code'] = $this->language->get('error_code');
        }

        if (!$this->request->post['locale']) {
            $this->error['locale'] = $this->language->get('error_locale');
        }

        if (!$this->request->post['directory']) {
            $this->error['directory'] = $this->language->get('error_directory');
        }

        $this->extensions->hk_ValidateData($this);
        return (!$this->error);
    }
}