<?php
/*
 * $Id$
 *
 * AbanteCart, Ideal OpenSource Ecommerce Solution
 * http://www.AbanteCart.com
 *
 * Copyright © 2011-2024 Belavier Commerce LLC
 *
 * This source file is subject to Open Software License (OSL 3.0)
 * License details is bundled with this package in the file LICENSE.txt.
 * It is also available at this URL:
 * <http://www.opensource.org/licenses/OSL-3.0>
 *
 * UPGRADE NOTE:
 * Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 * versions in the future. If you wish to customize AbanteCart for your
 * needs please refer to http://www.AbanteCart.com for more information.
 */

class ControllerPagesCatalogAttribute extends AController
{
    public $error = [];
    /** @var AAttribute_Manager */
    private $attribute_manager;

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->attribute_manager = new AAttribute_Manager();
    }

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

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/attribute'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $grid_settings = [
            'table_id'         => 'attribute_grid',
            'url'              => $this->html->getSecureURL('listing_grid/attribute'),
            'editurl'          => $this->html->getSecureURL('listing_grid/attribute/update'),
            'update_field'     => $this->html->getSecureURL('listing_grid/attribute/update_field'),
            'sortname'         => 'sort_order',
            'drag_sort_column' => 'sort_order',
            'sortorder'        => 'asc',
            'columns_search'   => true,
            'actions'          => [
                'edit'   => [
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('catalog/attribute/update', '&attribute_id=%ID%'),
                ],
                'save'   => [
                    'text' => $this->language->get('button_save'),
                ],
                'delete' => [
                    'text' => $this->language->get('button_delete'),
                ],
            ],
        ];

        $grid_settings['search_form'] = false;

        $grid_settings['colNames'] = [
            $this->language->get('column_name'),
            $this->language->get('column_type'),
            $this->language->get('column_sort_order'),
            $this->language->get('column_status'),
        ];
        $grid_settings['colModel'] = [
            [
                'name'  => 'name',
                'index' => 'name',
                'align' => 'left',
            ],
            [
                'name'  => 'attribute_type',
                'index' => 'type_name',
                'align' => 'left',
                'width' => 90,
            ],
            [
                'name'   => 'sort_order',
                'index'  => 'sort_order',
                'align'  => 'center',
                'width'  => 60,
                'search' => false,
            ],
            [
                'name'   => 'status',
                'index'  => 'status',
                'align'  => 'center',
                'width'  => 80,
                'search' => false,
            ],
        ];

        if ($this->config->get('config_show_tree_data')) {
            $grid_settings['expand_column'] = "name";
            $grid_settings['multiaction_class'] = 'hidden';
        }

        $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('insert', $this->html->getSecureURL('catalog/attribute/insert'));

        $results = $this->attribute_manager->getAttributeTypes();
        foreach ($results as $type) {
            $this->data['attribute_types'][$type['attribute_type_id']] = $type;
        }
        $this->_initTabs();
        $this->view->assign('inserts', $this->data['tabs']);

        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('help_url', $this->gen_help_url('global_attributes_listing'));

        $this->processTemplate('pages/catalog/attribute_list.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function insert()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->is_POST() && $this->validateAttributeForm()) {
            $attribute_id = $this->attribute_manager->addAttribute($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('catalog/attribute/update', '&attribute_id=' . $attribute_id));
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

        if ($this->request->is_POST() && $this->validateAttributeForm()) {
            $this->data['inserted'] = $this->attribute_manager->updateAttribute(
                $this->request->get['attribute_id'],
                $this->request->post
            );
            $this->extensions->hk_ProcessData($this, __FUNCTION__);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL(
                'catalog/attribute/update',
                '&attribute_id=' . $this->request->get['attribute_id'])
            );
        }

        $this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _getForm()
    {
        $this->data = $attribute_type_info = [];
        $this->data['error_warning'] = implode("\n", $this->error);

        $this->data['cancel'] = $this->html->getSecureURL('catalog/attribute');
        $this->data['get_attribute_type'] = $this->html->getSecureURL('r/catalog/attribute/get_attribute_type');

        $this->document->initBreadcrumb([
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ]);
        $this->document->addBreadcrumb([
            'href'      => $this->html->getSecureURL('catalog/attribute'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
        ]);

        $attribute_id = (int)$this->request->get['attribute_id'];

        if ($attribute_id && $this->request->is_GET()) {
            $attribute_info = $this->attribute_manager->getAttribute(
                $this->request->get['attribute_id'],
                $this->language->getContentLanguageID()
            );

            $attribute_type_info = $this->attribute_manager->getAttributeTypeInfoById(
                (int)$attribute_info['attribute_type_id']
            );

            //load values for attributes with options

            $this->data['elements_with_options'] = HtmlElementFactory::getElementsWithOptions();

            if (in_array($attribute_info['element_type'], $this->data['elements_with_options'])) {
                $values = $this->attribute_manager->getAttributeValues(
                    $attribute_id,
                    $this->language->getContentLanguageID()
                );
                $attribute_info['values'] = [];
                foreach ($values as $v) {
                    $attribute_info['values'][] = addslashes(html_entity_decode($v['value'], ENT_COMPAT, 'UTF-8'));
                }
            }

            if (has_value($attribute_info['settings'])) {
                $attribute_info['settings'] = unserialize($attribute_info['settings']);
            }
        }

        if (has_value($this->request->get['attribute_type_id'])) {
            $attribute_type_info = $this->attribute_manager->getAttributeTypeInfoById(
                (int)$this->request->get['attribute_type_id']
            );
        }

        $fields = [
            'name',
            'attribute_group_id',
            'attribute_type_id',
            'element_type',
            'sort_order',
            'required',
            'regexp_pattern',
            'error_text',
            'placeholder',
            'settings',
            'status',
            'values',
        ];

        if ($attribute_type_info['type_key'] != 'download_attribute') {
            $fields[] = 'attribute_parent_id';
        }

        foreach ($fields as $f) {
            if (isset($this->request->post[$f])) {
                $this->data[$f] = $this->request->post[$f];
            } elseif (isset($attribute_info[$f])) {
                $this->data[$f] = $attribute_info[$f];
            } else {
                $this->data[$f] = '';
                if ($f == 'status') {
                    $this->data[$f] = 1;
                }
            }
        }

        // build tabs on page
        $results = $this->attribute_manager->getAttributeTypes();
        foreach ($results as $type) {
            $this->data['attribute_types'][$type['attribute_type_id']] = $type;
        }

        if (isset($attribute_info['attribute_type_id'])) {
            $attribute_type_id = (int)$attribute_info['attribute_type_id'];
        } else {
            $attribute_type_id = (int)$this->request->get_or_post('attribute_type_id');
        }
        if (!$attribute_type_id) {
            $attribute_type_id = key($this->data['attribute_types']);
        }

        $this->_initTabs($attribute_type_id);

        if (!$attribute_id) {
            $this->data['action'] = $this->html->getSecureURL('catalog/attribute/insert', '&attribute_type_id=' . $attribute_type_id);
            $this->data['heading_title'] = $this->language->get('text_insert') . $this->language->get('text_attribute');
            $this->data['update'] = '';
            $form = new AForm('ST');
        } else {
            $this->data['action'] = $this->html->getSecureURL('catalog/attribute/update', '&attribute_id=' . $attribute_id . '&attribute_type_id=' . $attribute_type_id);
            $this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_attribute');
            $this->data['update'] = $this->html->getSecureURL('listing_grid/attribute/update_field', '&id=' . $attribute_id);
            $form = new AForm('HT');
            $this->data['attribute_id'] = $attribute_id;
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
            'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
            'action' => $this->data['action'],
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

        $this->data['form']['fields']['status'] = $form->getFieldHtml([
            'type'  => 'checkbox',
            'name'  => 'status',
            'value' => $this->data['status'],
            'style' => 'btn_switch',
        ]);
        $this->data['form']['fields']['name'] = $form->getFieldHtml([
            'type'         => 'input',
            'name'         => 'name',
            'value'        => $this->data['name'],
            'required'     => true,
            'style'        => 'large-field',
            'multilingual' => true,
        ]);

        if ($attribute_type_info['type_key'] != 'download_attribute') {
            $parent_attributes = ['' => $this->language->get('text_select')];
            $results = $this->attribute_manager->getAttributes(['attribute_type_id' => $attribute_type_id, 'limit' => null], 0, 0);
            foreach ($results as $type) {
                if ($attribute_id && $attribute_id == $type['attribute_id']) {
                    continue;
                }
                $parent_attributes[$type['attribute_id']] = $type['name'];
            }
            $this->data['form']['fields']['attribute_parent'] = $form->getFieldHtml([
                'type'    => 'selectbox',
                'name'    => 'attribute_parent_id',
                'value'   => $this->data['attribute_parent_id'],
                'options' => $parent_attributes,
            ]);
        }

        if ($this->data['attribute_types'][$attribute_type_id]['controller']) {
            $subform = $this->dispatch($this->data['attribute_types'][$attribute_type_id]['controller'],
                [
                    [
                        'data'              => $this->data,
                        'aform'             => $form,
                        'attribute_manager' => $this->attribute_manager,
                    ],
                ]
            );
            $this->data['subform'] = $subform->dispatchGetOutput();
        }

        $this->data['insert'] = $this->html->getSecureURL('catalog/attribute/insert');
        $this->data['form_language_switch'] = $this->html->getContentLanguageSwitcher();

        $this->data['text_parent_note'] = $this->language->get('text_parent_note');
        $this->data['help_url'] = $this->gen_help_url('global_attributes_edit');

        $this->data['list_url'] = $this->html->getSecureURL('catalog/attribute', '&saved_list=attribute_grid');

        $this->view->batchAssign($this->data);
        /** @see public_html/admin/view/default/template/pages/catalog/attribute_form.tpl */
        $this->processTemplate('pages/catalog/attribute_form.tpl');
    }

    public function validateAttributeForm()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->user->canModify('catalog/attribute')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!has_value($this->request->get_or_post('attribute_type_id'))) {
            $this->error['attribute_type'] = $this->language->get('error_required');
        } else {
            $this->request->post['attribute_type_id'] = $this->request->get_or_post('attribute_type_id');
        }

        if (!isset($this->request->post['required'])) {
            $this->request->post['required'] = 0;
        }

        if (has_value($this->request->post['regexp_pattern'])) {
            $this->request->post['regexp_pattern'] = trim($this->request->post['regexp_pattern']);
        }
        if (has_value($this->request->post['placeholder'])) {
            $this->request->post['placeholder'] = trim($this->request->post['placeholder']);
        }

        $this->error = array_merge(
            $this->error,
            $this->attribute_manager->validateAttributeCommonData($this->request->post),
            $this->attribute_manager->validateAttributeValues(
                (int)$this->request->get_or_post('attribute_id'),
                (array)$this->request->post['values']
            )
        );

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        return (!$this->error);
    }

    protected function _initTabs($active = null)
    {
        $method = (has_value($this->request->get['attribute_id']) ? 'update' : 'insert');

        foreach ($this->data['attribute_types'] as $type_id => $type) {
            if (!$type_id) {
                continue;
            }
            // check is extension enabled
            if (!in_array($type['controller'], $this->attribute_manager->getCoreAttributeTypesControllers())) {
                $controller = explode('/', $type['controller']);
                array_pop($controller);
                $controller = implode('/', $controller);
                if (!$this->extensions->isExtensionController($controller)) {
                    continue;
                }
            }

            $this->data['tabs'][$type_id] = [
                'text' => $type['type_name'],
                'href' => $method == 'insert'
                    ? $this->html->getSecureURL('catalog/attribute/' . $method, '&attribute_type_id=' . $type_id)
                    : '',
            ];
        }

        $active = in_array($active, array_keys($this->data['tabs']))
            ? $active
            : key($this->data['tabs']);
        $this->data['tabs'][$active]['active'] = 1;
    }
}

