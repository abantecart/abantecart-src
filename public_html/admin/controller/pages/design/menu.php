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

class ControllerPagesDesignMenu extends AController
{
    public $error = [];
    protected $columns = [
        'item_id',
        'item_icon',
        'item_text',
        'item_url',
        'target',
        'parent_id',
        'sort_order',
    ];
    /** @var AMenu_Storefront */
    protected $menu;
    protected $menu_items;
    protected $menu_tree;

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('design/menu'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->menu = new AMenu_Storefront();
        $menu_parents = $this->menu->getItemIds();

        $parentIds = ['' => $this->language->get('text_select_parent_id')];
        foreach ($menu_parents as $item) {
            if ($item != '') {
                $parentIds[$item] = $item;
            }
        }

        $grid_settings = [
            'table_id'         => 'menu_grid',
            'url'              => $this->html->getSecureURL(
                'listing_grid/menu',
                '&parent_id='.$this->request->get['parent_id']
            ),
            'editurl'          => $this->html->getSecureURL('listing_grid/menu/update'),
            'update_field'     => $this->html->getSecureURL('listing_grid/menu/update_field'),
            'sortname'         => 'sort_order',
            'sortorder'        => 'asc',
            'drag_sort_column' => 'sort_order',
            'columns_search'   => false,
            'actions'          => [
                'edit'   => [
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('design/menu/update', '&item_id=%ID%'),
                ],
                'delete' => ['text' => $this->language->get('button_delete')],
                'save'   => ['text' => $this->language->get('button_save')],
            ],
        ];

        $form = new AForm ();
        $form->setForm(['form_name' => 'menu_grid_search']);

        $grid_search_form = [];
        $grid_search_form['id'] = 'menu_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'menu_grid_search',
                'action' => '',
            ]
        );
        $grid_search_form['submit'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'submit',
                'text' => $this->language->get('button_go'),
            ]
        );

        $grid_search_form['reset'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'reset',
                'text' => $this->language->get('button_reset'),
            ]
        );
        $grid_search_form['fields']['parent_id'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'parent_id',
                'options' => $parentIds,
                'value'   => $this->request->get['parent_id'],
            ]
        );

        $grid_settings['search_form'] = true;

        $grid_settings['colNames'] = [
            '',
            $this->language->get('entry_item_id'),
            $this->language->get('entry_item_text'),
            $this->language->get('entry_status'),
            $this->language->get('entry_sort_order'),
        ];
        $grid_settings['colModel'] = [
            [
                'name'     => 'item_icon',
                'index'    => 'item_icon',
                'width'    => 80,
                'align'    => 'center',
                'sortable' => false,
                'search'   => false,
            ],
            [
                'name'   => 'item_id',
                'index'  => 'item_id',
                'width'  => 120,
                'align'  => 'left',
                'search' => false,
            ],
            [
                'name'   => 'item_text',
                'index'  => 'item_text',
                'width'  => 360,
                'align'  => 'center',
                'search' => false,
            ],
            [
                'name'   => 'status',
                'index'  => 'settings[status]',
                'align'  => 'center',
                'search' => false,
            ],
            [
                'name'   => 'sort_order',
                'index'  => 'sort_order',
                'align'  => 'center',
                'search' => false,
            ],
        ];

        if ($this->config->get('config_show_tree_data')) {
            $grid_settings['expand_column'] = "item_id";
            $grid_settings['multiaction_class'] = 'hidden';
        }

        $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('search_form', $grid_search_form);

        $this->view->batchAssign($this->language->getASet());
        $this->view->assign('insert', $this->html->getSecureURL('design/menu/insert'));
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('help_url', $this->gen_help_url('menu_listing'));

        $this->processTemplate('pages/design/menu.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function insert()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->document->setTitle($this->language->get('heading_title'));

        $this->menu = new AMenu_Storefront();
        $language_id = $this->language->getContentLanguageID();

        if ($this->request->is_POST() && $this->_validateForm($this->request->post)) {
            $post = $this->request->post;
            $languages = $this->language->getAvailableLanguages();
            foreach ($languages as $l) {
                if ($l['language_id'] == $language_id) {
                    continue;
                }
                $post['item_text'][$l['language_id']] = $post['item_text'][$language_id];
            }

            $post['item_icon'] = html_entity_decode($post['item_icon'], ENT_COMPAT, 'UTF-8');
            $text_id = preformatTextID($post['item_id']);
            $result = $this->menu->insertMenuItem(
                [
                    'item_id'         => $text_id,
                    'item_icon'       => $post['item_icon'],
                    'item_text'       => $post['item_text'],
                    'parent_id'       => $post['parent_id'],
                    'item_url'        => $post['item_url'],
                    'sort_order'      => $post['sort_order'],
                    'item_type'       => 'core',
                    'settings'        => serialize($post['settings'])
                ]
            );

            if ($result !== true) {
                $this->error['warning'] = $result;
            } else {
                $this->session->data['success'] = $this->language->get('text_success');
                redirect($this->html->getSecureURL('design/menu/update', '&item_id='.$text_id));
            }
        }

        $this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $item_id = $this->request->get['item_id'];
        $this->document->setTitle($this->language->get('heading_title'));

        $this->menu = new AMenu_Storefront();

        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        if (($this->request->is_POST()) && $this->_validateForm($this->request->post)) {
            $post = $this->request->post;
            if (isset ($post['item_icon'])) {
                $post['item_icon'] = (int)$post['item_icon'];
            }
            $post['settings'] = serialize( $post['settings'] );

            $item_keys = [
                'item_icon',
                'item_text',
                'item_url',
                'parent_id',
                'sort_order',
                'settings',
            ];

            $update_item = [];
            if ($item_id) {
                foreach ($item_keys as $item_key) {
                    if (isset ($post[$item_key])) {
                        $update_item[$item_key] = $post[$item_key];
                    }
                }
                // set condition for updating row
                $this->menu->updateMenuItem($item_id, $update_item);
            }

            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('design/menu/update', '&item_id='.$item_id));
        }

        $this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _getForm()
    {
        if (isset ($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

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
                'href'      => $this->html->getSecureURL('design/menu'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );

        $this->data['cancel'] = $this->html->getSecureURL('design/menu');

        $language_id = $this->language->getContentLanguageID();
        $item_id = $this->request->get['item_id'];

        $menu_item = null;
        $this->menu_items = $this->menu->getItemIds();
        $parentIds = ['' => $this->language->get('text_none')];

        foreach ($this->menu_items as $item) {
            if ($item != '') {
                $parentIds[$item] = $item;
            }
        }

        foreach ($this->columns as $column) {
            if (isset ($this->request->post[$column])) {
                $this->data[$column] = $this->request->post[$column];
            } elseif (!empty($menu_item)) {
                $this->data[$column] = $menu_item[$column];
            } else {
                $this->data[$column] = '';
            }
        }

        if (!$item_id) {
            $this->data['action'] = $this->html->getSecureURL('design/menu/insert');
            $this->data['heading_title'] = $this->language->get('text_insert')
                .'&nbsp;'
                .$this->language->get('heading_title');
            $this->data['update'] = '';
            $form = new AForm ('HT');
        } else {
            //get menu item details
            $this->data = array_merge($this->data, $this->menu->getMenuItem($item_id));

            $this->data['action'] = $this->html->getSecureURL('design/menu/update', '&item_id='.$item_id);
            $this->data['heading_title'] = $this->language->get('text_edit').$this->language->get('heading_title');
            $this->data['update'] = $this->html->getSecureURL('listing_grid/menu/update_field', '&id='.$item_id);
            $form = new AForm ('HS');
        }

        $this->document->addBreadcrumb(
            [
                'href'      => $this->data['action'],
                'text'      => $this->data['heading_title'],
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $form->setForm(['form_name' => 'menuFrm', 'update' => $this->data['update']]);

        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'menuFrm',
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
                'action' => $this->data['action'],
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
                'name'     => 'settings[status]',
                'value'    => $this->data['settings']['status'] ?? 1,
                'required' => true,
                'style'    => 'btn_switch',
            ]
        );
        $this->data['form']['fields']['item_id'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'item_id',
                'value'    => $this->data['item_id'],
                'required' => true,
                'attr'     => $item_id ? 'disabled' : '',
            ]
        );

        $this->data['form']['fields']['item_text'] = $form->getFieldHtml(
            [
                'type'         => 'input',
                'name'         => 'item_text['.$language_id.']',
                'value'        => $this->data['item_text'][$language_id],
                'required'     => true,
                'style'        => 'large-field',
                'multilingual' => true,
            ]
        );

        $this->data['link_types'] = array_merge(
            [
                'category' => $this->language->get('text_category_link_type'),
                'content'  => $this->language->get('text_content_link_type'),
                'custom'   => $this->language->get('text_custom_link_type'),
            ],
            (array)$this->data['link_types']
        );

        $this->data['form']['fields']['link_type'] = $this->html->buildElement(
            [
                'type'    => 'selectbox',
                'name'    => 'link_type',
                'options' => $this->data['link_types'],
                'value'   => '',
                'style'   => 'no-save short-field',
            ]
        );

        $this->data['form']['fields']['item_url'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'item_url',
                'value'    => $this->data['item_url'],
                'style'    => 'large-field',
                'required' => true,
                'help_url' => $this->gen_help_url('item_url'),
            ]
        );
        $this->data['form']['fields']['item_target'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'settings[target]',
                'value'    => $this->data['settings']['target'],
                'options'  => [
                    '_self' => '_self',
                    '_blank' => '_blank'
                ],
                'style'    => 'small-field',
                'required' => true
            ]
        );

        $this->loadModel('catalog/category');
        $categories = $this->model_catalog_category->getCategories(ROOT_CATEGORY_ID);
        $options = ['' => $this->language->get('text_select')];
        foreach ($categories as $c) {
            if (!$c['status']) {
                continue;
            }
            $options[$c['category_id']] = $c['name'];
        }
        $this->data['link_category'] = $this->html->buildElement(
            [
                'type'    => 'selectbox',
                'name'    => 'menu_categories',
                'options' => $options,
                'style'   => 'no-save short-field',
            ]
        );

        $this->data['link_category_include_children'] = $this->html->buildElement(
            [
                'type'    => 'checkbox',
                'id'      => 'category_children',
                'name'    => 'settings[include_children]',
                'value'   => 1,
                'checked'   => $this->request->post['settings']['include_children'] ?? (bool)$this->data['settings']['include_children'],
                'style'   => 'no-save btn_switch',
            ]
        );

        $acm = new AContentManager();
        $results = $acm->getContentsForSelect(false,false,0,true);

        $options = ['' => $this->language->get('text_select')];
        foreach ($results as $k=>$c) {
            if ($k == '0_0') {
                continue;
            }
            $options[explode('_',$k)[1]] = $c;
        }

        $this->data['link_content'] = $this->html->buildElement(
            [
                'type'    => 'selectbox',
                'name'    => 'menu_information',
                'options' => $options,
                'style'   => 'no-save short-field',
            ]
        );
        $this->data['link_content_include_children'] = $this->html->buildElement(
            [
                'type'    => 'checkbox',
                'id'      => 'content_children',
                'name'    => 'settings[include_children]',
                'value'   => 1,
                'checked' => $this->request->post['settings']['include_children'] ?? (bool)$this->data['settings']['include_children'],
                'style'   => 'no-save short-field btn_switch',
            ]
        );

        $this->data['form']['fields']['parent_id'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'parent_id',
                'options' => $parentIds,
                'value'   => $this->data['parent_id'],
                'style'   => 'medium-field',
            ]
        );
        $this->data['form']['fields']['sort_order'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'sort_order',
                'value' => $this->data['sort_order'],
                'style' => 'small-field',
            ]
        );

        $this->data['form']['fields']['item_icon'] = $form->getFieldHtml(
            [
                'type'        => 'resource',
                'name'        => 'item_icon',
                'resource_id' => $this->data['item_icon'] ? : $this->data['item_icon_rl_id'],
                'rl_type'     => 'image',
            ]
        );
        //adds scripts for RL work
        $resources_scripts = $this->dispatch(
            'responses/common/resource_library/get_resources_scripts',
            [
                'object_name' => 'storefront_menu_item',
                'object_id'   => (int) $this->request->get['item_id'],
                'types'       => ['image'],
                'onload'      => true,
                'mode'        => 'single',
            ]
        );
        $this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();

        $this->view->batchAssign($this->language->getASet());
        $this->view->batchAssign($this->data);
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('help_url', $this->gen_help_url('menu_edit'));
        $this->processTemplate('pages/design/menu_form.tpl');
    }

    protected function _buildMenuTree($parent = '', $level = 0)
    {
        if (empty($this->menu_items[$parent])) {
            return [];
        }
        $lang_id = (int) $this->language->getContentLanguageID();
        foreach ($this->menu_items[$parent] as $item) {
            $this->menu_tree[$item['item_id']] = [
                'item_id' => $item['item_id'],
                'text'    => str_repeat('&nbsp;&nbsp;&nbsp;', $level).$item['item_text'][$lang_id],
                'level'   => $level,
            ];
            $this->_buildMenuTree($item['item_id'], $level + 1);
        }
        return true;
    }

    protected function _validateForm($post)
    {
        if (!$this->user->canModify('design/menu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!empty($post['item_id'])) {
            $ids = $this->menu->getItemIds();
            if (!preg_match("/^[A-Za-z0-9]*$/",$post['item_id'])) {
                $this->error['item_id'] = $this->language->get('error_non_ascii');
            } else if (in_array($post['item_id'], $ids)) {
                $this->error['item_id'] = $this->language->get('error_non_unique');
            }
        }
        if (empty ($post['item_id']) && empty ($this->request->get['item_id'])) {
            $this->error['item_id'] = $this->language->get('error_empty');
        }

        if (empty ($post['item_text'][$this->language->getContentLanguageID()])) {
            $this->error['item_text'] = $this->language->get('error_empty');
        }
        if (empty ($post['item_url'])) {
            $this->error['item_url'] = $this->language->get('error_empty');
        }

        $this->extensions->hk_ValidateData($this);

        return (!$this->error);
    }
}