<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2024 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/

/**
 * Class ControllerPagesToolFormsManager
 *
 * @property ModelToolFormsManager $model_tool_forms_manager
 */
class ControllerPagesToolFormsManager extends AController
{

    public $controllers = [];
    public $error = [];

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('forms_manager/forms_manager');
        $this->loadModel('tool/forms_manager');

        if ($this->request->is_POST() && $this->_validateForm($this->request->post)) {

            if (!$this->request->get['form_id']) {
                if ($this->model_tool_forms_manager->getFormIdByName($this->request->post['form_name'])) {

                    $this->session->data['warning'] = $this->language->get('error_duplicate_form_name');
                    redirect($this->html->getSecureURL('tool/forms_manager'));
                }
                $this->request->get['form_id'] = $this->model_tool_forms_manager->addForm($this->request->post);
            } elseif (!$this->model_tool_forms_manager->addField($this->request->get['form_id'], $this->request->post)) {
                $this->session->data['warning'] = $this->language->get('error_duplicate_field_name');
            }
            redirect($this->html->getSecureURL('tool/forms_manager/update', '&form_id=' . $this->request->get['form_id']));
            exit;
        }

        $this->document->setTitle($this->language->get('forms_manager_name'));
        $this->view->assign('heading_title', $this->language->get('forms_manager_name'));

        $this->view->assign('error_warning', $this->session->data['warning']);
        if (isset($this->session->data['warning'])) {
            unset($this->session->data['warning']);
        }
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
            'href'      => $this->html->getSecureURL('tool/forms_manager'),
            'text'      => $this->language->get('forms_manager_name'),
            'separator' => ' :: ',
            'current'   => true,
        ]);

        $grid_settings = [
            'table_id'     => 'forms_grid',
            'url'          => $this->html->getSecureURL('grid/form'),
            'editurl'      => $this->html->getSecureURL('grid/form/update'),
            'update_field' => $this->html->getSecureURL('grid/form/update_field'),
            'sortname'     => 'name',
            'sortorder'    => 'asc',
            'actions'      => [
                'edit'   => [
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('tool/forms_manager/update', '&form_id=%ID%'),
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
            $this->language->get('column_description'),
            $this->language->get('column_status'),
        ];
        $grid_settings['colModel'] = [
            [
                'name'  => 'form_name',
                'index' => 'form_name',
                'align' => 'center',
                'width' => 200,
            ],
            [
                'name'  => 'description',
                'index' => 'description',
                'align' => 'center',
                'width' => 200,
            ],
            [
                'name'   => 'status',
                'index'  => 'status',
                'align'  => 'center',
                'width'  => 130,
                'search' => false,
            ],
        ];

        $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());

        $this->view->assign('insert', $this->html->getSecureURL('tool/forms_manager/update'));
        $this->view->assign('help_url', $this->gen_help_url('forms_manager'));

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/tool/forms_manager_list.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }

    public function update()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('forms_manager/forms_manager');
        $this->loadModel('tool/forms_manager');

        $this->document->setTitle($this->language->get('forms_manager_name'));

        if ($this->request->is_POST() && $this->_validateForm($this->request->post)) {
            $post = $this->request->post;

            if ($post['controller_path'] == 'forms_manager/default_email' && trim($post['success_page']) == '') {
                $post['success_page'] = 'forms_manager/default_email/success';
            }

            $form_id = $this->request->get['form_id'];
            if ($form_id) {
                $post['form_id'] = $this->request->get['form_id'];

                $this->session->data['success'] = $this->language->get('text_success_form');
                $this->model_tool_forms_manager->updateForm($post);
                $this->model_tool_forms_manager->updateFormFieldData($post);
            } else {
                $form_id = $this->model_tool_forms_manager->addForm($this->request->post);
                $this->session->data['success'] = $this->language->get('text_success_added_form');
            }
            redirect($this->html->getSecureURL('tool/forms_manager/update', '&form_id=' . $form_id));
        }

        $this->view->assign('error', $this->error);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->controllers = array_merge(
            $this->controllers,
            [
                'forms_manager/default_email' => $this->language->get('text_default_email'),
                'content/contact'             => $this->language->get('text_contactus_page'),
            ]
        );
        $this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function removeField()
    {
        $this->loadLanguage('forms_manager/forms_manager');
        $this->loadModel('tool/forms_manager');
        $this->model_tool_forms_manager->removeField($this->request->get['form_id'], $this->request->get['field_id']);
        $this->session->data['success'] = $this->language->get('text_field_removed');
        redirect($this->html->getSecureURL('tool/forms_manager/update', '&form_id=' . $this->request->get['form_id']));
    }

    private function _getForm()
    {

        //check is set sender name and email for settings
        if (!$this->config->get('forms_manager_default_sender_name') || !$this->config->get('forms_manager_default_sender_email')) {
            $this->data['error_warning'] = $this->html->convertLinks($this->language->get('forms_manager_error_empty_sender'));
        }
        $this->data['form_data'] = $this->model_tool_forms_manager->getFormById($this->request->get['form_id']);
        $this->data['form_edit_title'] = $this->data['form_data']['description'] ?? $this->language->get('entry_add_new_form');
        $this->data['cancel'] = $this->html->getSecureURL('tool/forms_manager');
        $this->data['heading_title'] = $this->language->get('forms_manager_name');
        $this->data['update'] = $this->html->getSecureURL('grid/form/update_field', '&form_id=' . $this->data['form_data']['form_id']);
        $this->data['field_id'] = (int)$this->request->get['field_id'];

        if ($this->request->get['form_id']) {
            $head_form = new AForm('HS');
            $this->data['entry_edit_form'] = $this->language->get('text_edit') . '&nbsp;' . $this->language->get('text_form');
            $this->data['form_id'] = $this->request->get['form_id'];
            $this->data['action'] = $this->html->getSecureURL('tool/forms_manager/update', '&form_id=' . $this->data['form_data']['form_id']);

        } else {
            $head_form = new AForm('HT');
            $this->data['action'] = $this->html->getSecureURL('tool/forms_manager/update');
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
                'href'      => $this->html->getSecureURL('tool/forms_manager'),
                'text'      => $this->language->get('forms_manager_name'),
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('tool/forms_manager'),
                'text'      => $this->data['entry_edit_form'] . '  ' . $this->data['form_data']['form_name'],
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $head_form->setForm(
            [
                'form_name' => 'extFrm',
                'update'    => $this->data['update'],
            ]
        );
        $this->data['head_form']['id'] = 'extFrm';
        $this->data['head_form']['form_open'] = $head_form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'extFrm',
                'action' => $this->data['action'],
            ]
        );
        $this->data['head_form']['button_save'] = $head_form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_save'),
                'style' => 'button1',
            ]
        );
        $this->data['head_form']['button_reset'] = $head_form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'reset',
                'href'  => $this->data['action'],
                'text'  => $this->language->get('button_reset'),
                'style' => 'button2',
            ]
        );

        $this->data['head_form']['fields']['form_status'] = $head_form->getFieldHtml(
            [
                'type'     => 'checkbox',
                'name'     => 'form_status',
                'value'    => $this->data['form_data']['status'] ?? '',
                'required' => true,
                'style'    => 'btn_switch status_switch',
            ]
        );
        $this->data['entry_form_status'] = $this->language->get('forms_manager_status');

        $this->data['head_form']['fields']['form_name'] = $head_form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'form_name',
                'value'    => $this->data['form_data']['form_name'] ?? '',
                'required' => true,
                'attr'     => ($this->request->get['form_id'] ? 'readonly' : ''),
                'style'    => 'large-field',
            ]
        );

        $this->data['head_form']['fields']['form_description'] = $head_form->getFieldHtml(
            [
                'type'         => 'input',
                'name'         => 'form_description',
                'value'        => $this->data['form_data']['description'] ?? '',
                'required'     => true,
                'style'        => 'large-field',
                'multilingual' => true,
            ]
        );

        $this->data['head_form']['fields']['controller_path'] = $head_form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'controller_path',
                'options'  => $this->controllers,
                'value'    => $this->data['form_data']['controller'] ?? 'forms_manager/default_email',
                'required' => true,
                'style'    => 'large-field',
            ]
        );

        $this->data['head_form']['fields']['success_page'] = $head_form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'success_page',
                'value' => $this->data['form_data']['success_page'] ?? 'forms_manager/default_email/success',
                'style' => 'large-field',
            ]
        );

        $this->data['error_required'] = $this->language->get('error_required');

        $this->data['forms_fields'] = [];

        $fields_data = $this->model_tool_forms_manager->getFields($this->data['form_data']['form_id']);

        $this->data['fields'] = [];

        if ($fields_data) {
            $this->data['fields'] =
            $fields = array_column($fields_data, 'field_name', 'field_id');
            $this->data['field_id'] = $this->data['field_id'] ?: array_key_first($fields);
        } else {
            $fields = [
                'new' => $this->language->get('text_add_new_field'),
            ];
        }

        $form = new AForm('HT');

        $form->setForm(
            [
                'form_name' => 'new_fieldFrm',
                'update'    => '',
            ]
        );

        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'new_fieldFrm',
                'action' => $this->html->getSecureURL(
                    'forms_manager/fields/addField',
                    '&form_id=' . $this->data['form_data']['form_id']
                ),
            ]
        );

        $this->data['form']['fields'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'field_id',
                'options' => $fields,
            ]
        );

        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'submit',
                'text' => $this->language->get('button_add_field'),
            ]
        );

        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'text' => $this->language->get('button_cancel'),
            ]
        );

        $results = HtmlElementFactory::getAvailableElements();
        $element_types = ['' => $this->language->get('text_select_field_type')];
        foreach ($results as $key => $type) {
            // file and multi-value element types disabled for now,
            //J = reCaptcha is not selectable, it will be used automatically if instead of captcha if enabled
            if (!in_array($key, ['P', 'L', 'J'])) {
                $element_types[$key] = $type['type'];
            }
        }

        $this->data['entry_new_field_description'] = $this->language->get('entry_field_description');
        $this->data['new_field_description'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'field_description',
                'required' => true,
            ]
        );

        $this->data['entry_new_field_name'] = $this->language->get('entry_field_name');
        $this->data['new_field_name'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'field_name',
                'required' => true,
            ]
        );

        $this->data['entry_new_field_note'] = $this->language->get('entry_field_note');
        $this->data['new_field_note'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'field_note',
                'required' => false,
            ]
        );

        $this->data['entry_status'] = $this->language->get('forms_manager_status');
        $this->data['status'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'status',
                'value' => 1,
                'style' => 'btn_switch btn-group-xs',
            ]
        );

        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['sort_order'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'sort_order',
                'style' => 'small-field',
            ]
        );

        $this->data['entry_required'] = $this->language->get('entry_required');
        $this->data['required'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'required',
                'style' => 'btn_switch btn-group-xs',
            ]
        );

        $this->data['entry_element_type'] = $this->language->get('text_field_type');
        $this->data['element_type'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'element_type',
                'required' => true,
                'options'  => $element_types,
            ]
        );

        $this->data['urls'] = [
            'get_fields_list' => $this->html->getSecureURL(
                'forms_manager/fields/get_fields_list',
                '&form_id=' . $this->request->get['form_id']
            ),
            'load_field'      => $this->html->getSecureURL(
                'forms_manager/fields/load_field',
                '&form_id=' . $this->request->get['form_id']
            ),
            'update_field'    => $this->html->getSecureURL(
                'forms_manager/fields/updateField',
                '&form_id=' . $this->request->get['form_id']
            ),
            'update_form'     => $this->html->getSecureURL(
                'forms_manager/fields/update_form',
                '&form_id=' . $this->request->get['form_id']
            )
        ];

        $this->data['text_success_field'] = $this->language->get('text_success_field');
        $this->data['help_url'] = $this->gen_help_url('forms_manager');
        $this->data['form_language_switch'] = $this->html->getContentLanguageSwitcher();

        $this->data['note'] = sprintf(
            $this->language->get('note_create_form_block'),
            $this->html->getSecureURL('design/blocks'),
            $this->html->getSecureURL('design/layout')
        );

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/tool/forms_manager_form.tpl');
    }

    protected function _validateForm($data)
    {
        if (!$this->user->hasPermission('modify', 'tool/forms_manager')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (mb_strlen($data['form_name']) == 0) {
            $this->error['form_name'] = $this->language->get('error_form_name');
        }
        if (mb_strlen($data['form_description']) == 0) {
            $this->error['form_description'] = $this->language->get('error_form_description');
        }

        $this->extensions->hk_ValidateData($this);

        return (!$this->error);
    }

    public function insert_block()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('design/blocks');
        $this->loadLanguage('forms_manager/forms_manager');
        $this->document->setTitle($this->language->get('forms_manager_name'));
        $this->data['heading_title'] = $this->language->get('custom_forms_block');

        $lm = new ALayoutManager();
        $block = $lm->getBlockByTxtId('custom_form_block');
        $this->data['block_id'] = $block['block_id'];
        $parent_instance_id = null;
        $position = 0;

        if ($this->request->is_POST() && $this->_validateBlockForm()) {
            if (isset($this->session->data['layout_params'])) {
                $layout = new ALayoutManager($this->session->data['layout_params']['tmpl_id'],
                    $this->session->data['layout_params']['page_id'],
                    $this->session->data['layout_params']['layout_id']);
                $blocks = $layout->getLayoutBlocks();
                if ($blocks) {
                    foreach ($blocks as $block) {
                        if ($block['block_id'] == $this->session->data['layout_params']['parent_block_id']) {
                            $parent_instance_id = $block['instance_id'];
                            $position = 10;
                            if ($block['children']) {
                                foreach ($block['children'] as $child) {
                                    $position = min($position, $child['position']);
                                }
                            }
                            break;
                        }
                    }
                }
                $saveData = $this->session->data['layout_params'];
                $saveData['parent_instance_id'] = $parent_instance_id;
                $saveData['position'] = $position + 10;
                $saveData['status'] = 1;
            } else {
                $layout = new ALayoutManager();
            }

            $content = isset($this->request->post['form_id'])
                ? serialize(['form_id' => $this->request->post['form_id']])
                : [];

            $custom_block_id = $layout->saveBlockDescription(
                $this->data['block_id'],
                0,
                [
                    'name'          => $this->request->post['block_name'],
                    'title'         => $this->request->post['block_title'],
                    'description'   => $this->request->post['block_description'],
                    'content'       => $content,
                    'block_wrapper' => $this->request->post['block_wrapper'],
                    'block_framed'  => ((int)$this->request->post['block_framed'] > 0) ? 1 : 0,
                    'language_id'   => $this->language->getContentLanguageID(),
                ]
            );

            $layout->editBlockStatus((int)$this->request->post['block_status'], $this->data['block_id'], $custom_block_id);

            // save custom_block in layout
            if (isset($this->session->data['layout_params'])) {
                $saveData['custom_block_id'] = $custom_block_id;
                $saveData['block_id'] = $this->data['block_id'];
                $layout->saveLayoutBlocks($saveData);
                unset($this->session->data['layout_params']);
            }
            // save list if it is custom
            $this->request->post['selected'] = json_decode(html_entity_decode($this->request->post['selected'][0]), true);
            if ($this->request->post['selected']) {
                $listing_manager = new AListingManager($custom_block_id);
                foreach ($this->request->post['selected'] as $id => $info) {
                    if ($info['status']) {
                        $listing_manager->saveCustomListItem(
                            [
                                'data_type'  => 'form_id',
                                'id'         => $id,
                                'sort_order' => (int)$info['sort_order'],
                                'store_id'   => $this->config->get('config_store_id')
                            ]
                        );
                    } else {
                        $listing_manager->deleteCustomListItem(
                            [
                                'data_type' => 'form_id',
                                'id'        => $id,
                            ]
                        );
                    }
                }

            }

            $this->session->data ['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('tool/forms_manager/edit_block', '&custom_block_id=' . $custom_block_id));
        }

        $this->data = array_merge($this->data, $this->request->post);

        $this->_init_tabs();
        $this->_getBlockForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function edit_block()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('forms_manager/forms_manager');
        $this->loadLanguage('design/blocks');
        $this->document->setTitle($this->language->get('forms_manager_name'));
        $this->data['heading_title'] = $this->language->get('custom_forms_block');

        $lm = new ALayoutManager();
        $block = $lm->getBlockByTxtId('custom_form_block');
        $this->data['block_id'] = $block['block_id'];
        $custom_block_id = (int)$this->request->get['custom_block_id'];
        if (!$custom_block_id) {
            redirect($this->html->getSecureURL('tool/forms_manager/insert_block'));
        }

        $tabs = [
            [
                'name'       => '',
                'text'       => $this->language->get('custom_forms_block'),
                'href'       => '',
                'active'     => true,
                'sort_order' => 0,
            ],
        ];
        $obj = $this->dispatch('responses/common/tabs',
            [
                //parent controller. Use customer group to use for other extensions that will add tabs via their hooks
                'tool/forms_manager/edit_block',
                ['tabs' => $tabs],
            ]
        );

        $this->data['tabs'] = $obj->dispatchGetOutput();

        if ($this->request->is_POST() && $this->_validateBlockForm()) {

            // get form html
            $content = [];
            if (isset($this->request->post['form_id'])) {
                $content['form_id'] = $this->request->post['form_id'];
            }
            $content = serialize($content);

            // saving
            $lm->saveBlockDescription($this->data['block_id'],
                $custom_block_id,
                [
                    'name'          => $this->request->post['block_name'],
                    'title'         => $this->request->post['block_title'],
                    'description'   => $this->request->post['block_description'],
                    'content'       => $content,
                    'block_wrapper' => $this->request->post['block_wrapper'],
                    'block_framed'  => $this->request->post['block_framed'],
                    'language_id'   => $this->language->getContentLanguageID(),
                ]
            );

            $lm->editBlockStatus((int)$this->request->post['block_status'], $this->data['block_id'], $custom_block_id);

            // save list if it is custom
            $this->request->post['selected'] = json_decode(
                html_entity_decode($this->request->post['selected'][0]),
                true
            );

            if ($this->request->post['selected']) {
                $listing_manager = new AListingManager($custom_block_id);

                foreach ($this->request->post['selected'] as $id => $info) {
                    if ($info['status']) {
                        $listing_manager->saveCustomListItem(
                            [
                                'data_type'  => 'form_id',
                                'id'         => $id,
                                'sort_order' => (int)$info['sort_order'],
                                'store_id'   => $this->config->get('config_store_id')
                            ]
                        );
                    } else {
                        $listing_manager->deleteCustomListItem(
                            [
                                'data_type' => 'form_id',
                                'id'        => $id,
                            ]
                        );
                    }
                }

            }

            $this->session->data ['success'] = $this->language->get('text_success');
            redirect(
                $this->html->getSecureURL(
                    'tool/forms_manager/edit_block',
                    '&custom_block_id=' . $custom_block_id
                )
            );
        }

        $this->_getBlockForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }

    protected function _getBlockForm()
    {
        if (isset ($this->session->data['warning'])) {
            $this->data ['error_warning'] = $this->session->data['warning'];
            $this->session->data['warning'] = '';
        } else {
            $this->data ['error_warning'] = '';
        }
        $this->load->library('json');
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
                'href'      => $this->html->getSecureURL('design/blocks'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );

        $this->data['cancel'] = $this->html->getSecureURL('design/blocks');
        $custom_block_id = (int)$this->request->get ['custom_block_id'];

        if ($custom_block_id) {
            $lm = new ALayoutManager();
            $block_info = $lm->getBlockDescriptions($custom_block_id);
            $language_id = $this->language->getContentLanguageID();
            if (!isset($block_info[$language_id])) {
                $language_id = key($block_info);
            }

            $this->data = array_merge($this->data, $block_info[$language_id]);
            $content = $block_info[$language_id]['content'];

            if ($content) {
                $content = unserialize($content);
            } else {
                $content = current($block_info);
                $content = unserialize($content['content']);
            }

            $this->data['form_id'] = $content['form_id'];
        }

        if (!$custom_block_id) {
            $this->data ['action'] = $this->html->getSecureURL('tool/forms_manager/insert_block');
            $this->data ['form_title'] = $this->language->get('text_create_block', 'forms_manager/forms_manager');
            $this->data ['update'] = '';
            $form = new AForm ('ST');
        } else {
            $this->data ['action'] = $this->html->getSecureURL(
                'tool/forms_manager/edit_block',
                '&custom_block_id=' . $custom_block_id
            );
            $this->data ['form_title'] = $this->language->get('text_edit') . ' ' . $this->data['name'];
            $this->data ['update'] = $this->html->getSecureURL(
                'listing_grid/blocks_grid/update_field',
                '&custom_block_id=' . $custom_block_id
            );
            $form = new AForm ('HS');
        }

        $this->document->addBreadcrumb(
            [
                'href'      => $this->data['action'],
                'text'      => $this->data ['form_title'],
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $form->setForm(['form_name' => 'CustomFormBlockFrm', 'update' => $this->data ['update']]);

        $this->data['form']['form_open'] = $form->getFieldHtml([
            'type'   => 'form',
            'name'   => 'CustomFormBlockFrm',
            'action' => $this->data ['action'],
            'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
        ]);
        $this->data['form']['submit'] = $form->getFieldHtml([
            'type'  => 'button',
            'name'  => 'submit',
            'text'  => $this->language->get('button_save'),
            'style' => 'button1',
        ]);
        $this->data['form']['cancel'] = $form->getFieldHtml([
            'type'  => 'button',
            'name'  => 'cancel',
            'text'  => $this->language->get('button_cancel'),
            'style' => 'button2',
        ]);

        if ($custom_block_id) {
            $this->data['form']['fields']['block_status'] = $form->getFieldHtml([
                'type'  => 'checkbox',
                'name'  => 'block_status',
                'value' => $this->data['status'],
                'style' => 'btn_switch',
            ]);
            $this->data['entry_block_status_note'] = $this->html->convertLinks(
                $this->language->get('entry_block_status_note')
            );
            $this->data['form']['fields']['block_status_note'] = '';
        }

        $this->data['form']['fields']['block_name'] = $form->getFieldHtml(
            [
                'type'  => 'hidden',
                'name'  => 'block_id',
                'value' => $this->data['block_id'],
            ]
        );
        $this->data['form']['fields']['block_name'] .= $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'block_name',
                'value'    => $this->data['name'],
                'required' => true,
            ]
        );
        $this->data['form']['text']['block_name'] = $this->language->get('entry_block_name');

        $this->data['form']['fields']['block_title'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'block_title',
                'required' => true,
                'value'    => $this->data ['title'],
            ]
        );
        $this->data['form']['text']['block_title'] = $this->language->get('entry_block_title');

        // list of templates for block
        $tmpl_ids = $this->extensions->getInstalled('template');
        array_unshift($tmpl_ids, (string)$this->session->data['layout_params']['tmpl_id']);
        $this->data['block_wrappers'] = [];
        foreach ($tmpl_ids as $tmpl_id) {
            $layout_manager = new ALayoutManager($tmpl_id);
            $block = $layout_manager->getBlockByTxtId('custom_form_block');
            $block_templates = (array)$layout_manager->getBlockTemplates($block['block_id']);
            foreach ($block_templates as $item) {
                $this->data['block_wrappers'][$item['template']] = $item['template'];
            }
        }
        array_unshift($this->data['block_wrappers'], 'Default');

        $this->data['form']['fields']['block_wrapper'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'block_wrapper',
                'options'  => $this->data['block_wrappers'],
                'value'    => $this->data['block_wrapper'],
                'help_url' => $this->gen_help_url('block_wrapper'),
            ]
        );
        $this->data['form']['text']['block_wrapper'] = $this->language->get('entry_block_wrapper');

        $this->data['form']['fields']['block_framed'] = $form->getFieldHtml(
            [
                'type'     => 'checkbox',
                'name'     => 'block_framed',
                'value'    => $this->data['block_framed'],
                'style'    => 'btn_switch',
                'help_url' => $this->gen_help_url('block_framed'),
            ]
        );
        $this->data['form']['text']['block_framed'] = $this->language->get('entry_block_framed');

        $this->data['form']['fields']['block_description'] = $form->getFieldHtml(
            [
                'type'  => 'textarea',
                'name'  => 'block_description',
                'value' => $this->data ['description'],
                'attr'  => ' style="height: 50px;"',
            ]
        );
        $this->data['form']['text']['block_description'] = $this->language->get('entry_block_description');

        $result = $this->model_tool_forms_manager->getForms(
            [
                'filter' => [
                    'status' => 1
                ]
            ]
        );
        $forms = [];
        if ($result) {
            $forms = array_column($result, 'form_name', 'form_id');
        }

        $this->data['form']['fields']['form'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'form_id',
                'options' => $forms,
                'value'   => $this->data['form_id'],
                'style'   => 'no-save',
                'attr'    => 'multiple="multiple"',
            ]
        );
        $this->data['form']['text']['form'] = $this->language->get('text_form');

        $this->data['note'] = sprintf(
            $this->language->get('note_edit_layout'),
            $this->html->getSecureURL('design/layout'),
            $this->html->getSecureURL('tool/forms_manager')
        );

        $this->view->batchAssign($this->language->getASet());
        $this->view->batchAssign($this->data);
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('language_code', $this->session->data['language']);
        $this->view->assign('help_url', $this->gen_help_url('form_block_edit'));

        $this->processTemplate('pages/tool/forms_manager_block_form.tpl');
    }

    private function _validateBlockForm()
    {
        if (!$this->user->canModify('tool/forms_manager')) {
            $this->session->data['warning'] = $this->error ['warning'] = $this->language->get('error_permission');
        }

        if (!$this->data['block_id']) {
            $this->error ['warning'] =
            $this->session->data['warning'] = 'Block with txt_id "custom_form_block" does not exists in your database!';
        }

        $required = [];
        if ($this->request->post) {
            $required = ['block_name', 'block_title'];

            foreach ($this->request->post as $name => $value) {
                if (in_array($name, $required) && empty($value)) {
                    $this->error ['warning'] =
                    $this->session->data['warning'] = $this->language->get('error_empty');
                    break;
                }
            }
        }

        foreach ($required as $name) {
            if (!in_array($name, array_keys($this->request->post))) {
                return false;
            }
        }

        $this->extensions->hk_ValidateData($this);

        return (!$this->error);
    }

    protected function _init_tabs()
    {

        $blocks = [];
        $lm = new ALayoutManager();
        $default_block_type = '';
        foreach (['html_block', 'listing_block'] as $txt_id) {
            $block = $lm->getBlockByTxtId($txt_id);
            if ($block['block_id']) {
                $blocks[$block['block_id']] = $this->language->get('text_' . $txt_id);
            }
            if ($txt_id == 'html_block') {
                $default_block_type = $block['block_id'];
            }
        }

        $this->data['block_id'] = !(int)$this->request->get['block_id'] ?: $default_block_type;
        $i = 0;
        $tabs = [];
        foreach ($blocks as $block_id => $block_text) {
            $tabs[] = [
                'name'       => $block_id,
                'text'       => $block_text,
                'href'       => $this->html->getSecureURL('design/blocks/insert', '&block_id=' . $block_id),
                'active'     => $block_id == $this->data['block_id'],
                'sort_order' => $i,
            ];
            $i++;
        }

        $obj = $this->dispatch('responses/common/tabs', [
                //parent controller. Use customer group to use for other extensions that will add tabs via their hooks
                'design/blocks',
                ['tabs' => $tabs],
            ]
        );
        $this->data['tabs'] = $obj->dispatchGetOutput();
    }
}