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

/**
 * Class ControllerPagesCatalogCategory
 */
class ControllerPagesCatalogCategory extends AController
{
    public $error = [];
    public $fields = [
        'category_description',
        'status',
        'parent_id',
        'category_store',
        'keyword',
        'sort_order',
    ];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->view->assign('help_url', $this->gen_help_url('category_listing'));

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/category'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $grid_settings = [
            'table_id'         => 'category_grid',
            'url'              => $this->html->getSecureURL('listing_grid/category'),
            'editurl'          => $this->html->getSecureURL('listing_grid/category/update'),
            'update_field'     => $this->html->getSecureURL('listing_grid/category/update_field'),
            'sortname'         => 'sort_order',
            'sortorder'        => 'asc',
            'drag_sort_column' => 'sort_order',
            'actions'          => [
                'edit'     => [
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('catalog/category/update', '&category_id=%ID%'),
                ],
                'save'     => [
                    'text' => $this->language->get('button_save'),
                ],
                'delete'   => [
                    'text' => $this->language->get('button_delete'),
                ],
                'dropdown' => [
                    'text'     => $this->language->get('text_choose_action'),
                    'href'     => $this->html->getSecureURL('catalog/category/update', '&category_id=%ID%'),
                    'children' => array_merge(
                        [
                            'quickview' => [
                                'text'  => $this->language->get('text_quick_view'),
                                'href'  => $this->html->getSecureURL('catalog/category/update', '&category_id=%ID%'),
                                //quick view port URL
                                'vhref' => $this->html->getSecureURL(
                                    'r/common/viewport/modal', '&viewport_rt=catalog/category/update&category_id=%ID%'
                                ),
                            ],
                            'general'   => [
                                'text' => $this->language->get('tab_general'),
                                'href' => $this->html->getSecureURL('catalog/category/update', '&category_id=%ID%'),
                            ],
                            'data'      => [
                                'text' => $this->language->get('tab_data'),
                                'href' => $this->html->getSecureURL(
                                        'catalog/category/update',
                                        '&category_id=%ID%') . '#data',
                            ],
                            'layout'    => [
                                'text' => $this->language->get('text_design'),
                                'href' => $this->html->getSecureURL(
                                    'catalog/category/edit_layout', '&category_id=%ID%'
                                ),
                            ],
                        ], (array)$this->data['grid_edit_expand']
                    ),
                ],
            ],
            'grid_ready'       => 'grid_ready(data);',
        ];

        $grid_settings['colNames'] = [
            '',
            $this->language->get('column_name'),
            $this->language->get('column_sort_order'),
            $this->language->get('column_status'),
            $this->language->get('column_products'),
            $this->language->get('column_subcategories'),
        ];
        $grid_settings['colModel'] = [
            [
                'name'     => 'image',
                'index'    => 'image',
                'align'    => 'center',
                'width'    => 70,
                'sortable' => false,
                'search'   => false,
            ],
            [
                'name'  => 'name',
                'index' => 'name',
                'width' => 310,
                'align' => 'left',
            ],
            [
                'name'   => 'sort_order',
                'index'  => 'sort_order',
                'width'  => 100,
                'align'  => 'center',
                'search' => false,
            ],
            [
                'name'   => 'status',
                'index'  => 'status',
                'width'  => 100,
                'align'  => 'center',
                'search' => false,
            ],
            [
                'name'     => 'products',
                'index'    => 'products',
                'width'    => 100,
                'align'    => 'center',
                'search'   => false,
                'sortable' => false,
            ],
            [
                'name'     => 'subcategories',
                'index'    => 'subcategories',
                'width'    => 140,
                'align'    => 'center',
                'search'   => false,
                'sortable' => false,
            ],
        ];
        if ($this->config->get('config_show_tree_data')) {
            $grid_settings['expand_column'] = "name";
            $grid_settings['multiaction_class'] = 'hidden';
        }

        $results = $this->model_catalog_category->getCategories(
            ROOT_CATEGORY_ID,
            $this->config->get('current_store_id')
        );
        $parents = [
            'null' => $this->language->get('text_select_all'),
            0      => $this->language->get('text_top_level'),
        ];
        foreach ($results as $c) {
            $parents[$c['category_id']] = $c['name'];
        }

        //get search filter from cookie if required
        $search_params = [];
        if ($this->request->get['saved_list']) {
            $grid_search_form = json_decode(html_entity_decode($this->request->cookie['grid_search_form']));
            if ($grid_search_form->table_id == $grid_settings['table_id']) {
                parse_str($grid_search_form->params, $search_params);
            }
            $grid_params = json_decode(html_entity_decode($this->request->cookie['grid_params']));
            if ($grid_params->postData->nodeid) {
                $search_params['parent_id'] = $grid_params->postData->nodeid;
            }
        }

        $form = new AForm();
        $form->setForm(
            [
                'form_name' => 'category_grid_search',
            ]
        );

        $grid_search_form = [];
        $grid_search_form['id'] = 'category_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'category_grid_search',
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

        $grid_search_form['fields']['parent_id'] = $form->getFieldHtml(
            [
                'type'        => 'selectbox',
                'name'        => 'parent_id',
                'options'     => $parents,
                'style'       => 'chosen',
                'value'       => $search_params['parent_id'] == null ? 0 : $search_params['parent_id'],
                'placeholder' => $this->language->get('text_select_parent'),
            ]
        );

        $grid_settings['search_form'] = true;

        $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
        if ($this->config->get('config_embed_status')) {
            $this->view->assign('embed_url', $this->html->getSecureURL('common/do_embed/categories'));
        }

        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('search_form', $grid_search_form);
        $this->view->assign('grid_url', $this->html->getSecureURL('listing_grid/category'));

        $this->document->setTitle($this->language->get('heading_title'));
        $this->view->assign('insert', $this->html->getSecureURL('catalog/category/insert'));
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

        $this->processTemplate('pages/catalog/category_list.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function insert()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        if ($this->request->is_POST() && $this->_validateForm()) {
            $languages = $this->language->getAvailableLanguages();
            $content_language_id = $this->language->getContentLanguageID();

            foreach ($languages as $l) {
                if ($l['language_id'] == $content_language_id) {
                    continue;
                }
                $this->request->post['category_description'][$l['language_id']] =
                    $this->request->post['category_description'][$content_language_id];
            }

            $category_id = $this->model_catalog_category->addCategory($this->request->post);
            $this->extensions->hk_ProcessData($this, 'insert');
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('catalog/category/update', '&category_id=' . $category_id));
        }
        $this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update(...$args)
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $cId = (int)$this->request->get['category_id'];

        $this->view->assign('help_url', $this->gen_help_url('category_edit'));

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
        $this->view->assign(
            'insert',
            $this->html->getSecureURL(
                'catalog/category/insert',
                '&parent_id=' . $cId
            )
        );

        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        if ($this->request->is_POST() && $this->_validateForm()) {
            $this->model_catalog_category->editCategory(
                $cId,
                $this->request->post
            );
            $this->session->data['success'] = $this->language->get('text_success');
            $this->extensions->hk_ProcessData($this, 'update');
            redirect(
                $this->html->getSecureURL(
                    'catalog/category/update',
                    '&category_id=' . $cId
                )
            );
        }

        /** @var ModelSettingSetting $mdl */
        $mdl = $this->loadModel('setting/setting');
        $settings = $mdl->getSetting('details', (int)$this->session->data['current_store_id']);
        $preview = $settings['config_url'] . INDEX_FILE . '?' . 'rt=product/category&category_id=' . $cId;
        $this->view->assign('preview', $preview);

        $this->_getForm($args);

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _getForm($args = [])
    {
        $viewport_mode = $args[0]['viewport_mode'] ?? '';
        $content_language_id = $this->language->getContentLanguageID();

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('error_name', $this->error['name']);
        $this->data['categories'] = $this->model_catalog_category->getCategories(
            (int)ROOT_CATEGORY_ID,
            (int)$this->session->data['current_store_id']
        );

        $categories = [0 => $this->language->get('text_none')]
            + array_column($this->data['categories'], 'name', 'category_id');

        $category_id = (int)$this->request->get['category_id'];
        $category_info = [];
        if ($category_id && $this->request->is_GET()) {
            $category_info = $this->model_catalog_category->getCategory($category_id);
        }

        if (!$category_info && $category_id) {
            redirect($this->html->getSecureURL('catalog/category'));
        }

        $history = [];
        if ($category_id) {
            $this->data['category_id'] = $category_id;
            unset($categories[$category_id]);
            $history = [
                'table'     => 'category_descriptions',
                'record_id' => $category_id,
            ];
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
                'href'      => $this->html->getSecureURL('catalog/category'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );

        $this->view->assign('cancel', $this->html->getSecureURL('catalog/category'));

        foreach ($this->fields as $f) {
            $this->data[$f] = $this->request->post[$f] ?? $category_info[$f] ?? '';
        }

        if (isset($this->request->post['category_description'])) {
            $this->data['category_description'] = $this->request->post['category_description'];
        } elseif (isset($category_info)) {
            $this->data['category_description'] = $this->model_catalog_category->getCategoryDescriptions($category_id);
        } else {
            $this->data['category_description'] = [];
        }

        if ($this->data['status'] == '') {
            $this->data['status'] = 1;
        }
        if ($this->request->is_GET() && has_value($this->request->get['parent_id'])) {
            $this->data['parent_id'] = $this->request->get['parent_id'];
        }
        if ($this->data['parent_id'] == '') {
            $this->data['parent_id'] = 0;
        }

        $this->loadModel('setting/store');
        $this->data['stores'] = $this->model_setting_store->getStores();
        if (isset($this->request->post['category_store'])) {
            $this->data['category_store'] = $this->request->post['category_store'];
        } elseif ($category_info) {
            $this->data['category_store'] = $this->model_catalog_category->getCategoryStores($category_id);
        } else {
            $this->data['category_store'] = [(int)$this->session->data['current_store_id']];
        }

        $stores = [0 => $this->language->get('text_default')]
            + array_column((array)$this->data['stores'], 'name', 'store_id');

        if (!$category_id) {
            $this->data['action'] = $this->html->getSecureURL('catalog/category/insert');
            $this->data['heading_title'] = $this->language->get('text_insert')
                . ' '
                . $this->language->get('text_category');
            $this->data['update'] = '';
            $form = new AForm('ST');
        } else {
            $this->data['action'] = $this->html->getSecureURL('catalog/category/update', '&category_id=' . $category_id);
            $this->data['heading_title'] = $this->language->get('text_edit')
                . ' '
                . $this->language->get('text_category')
                . ' - '
                . $this->data['category_description'][$content_language_id]['name'];
            $this->data['update'] = $this->html->getSecureURL(
                'listing_grid/category/update_field',
                '&id=' . $category_id
            );
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
        $this->document->setTitle($this->data['heading_title']);

        $form->setForm(
            [
                'form_name' => 'editFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'editFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'editFrm',
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
                'value' => $this->data['status'],
                'style' => 'btn_switch',
            ]
        );

        $this->data['form']['fields']['general']['parent_category'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'parent_id',
                'value'   => $this->data['parent_id'],
                'options' => $categories,
                'style'   => 'chosen',
            ]
        );
        $this->data['form']['fields']['general']['name'] = $form->getFieldHtml(
            [
                'type'         => 'input',
                'name'         => 'category_description[' . $content_language_id . '][name]',
                'value'        => $this->data['category_description'][$content_language_id]['name'],
                'required'     => true,
                'style'        => 'large-field',
                'attr'         => ' maxlength="255" ',
                'multilingual' => true,
                'history'      => $history
            ]
        );
        //no description edit for modal view
        if ($viewport_mode != 'modal') {
            $this->data['form']['fields']['general']['description'] = $form->getFieldHtml(
                [
                    'type'         => 'texteditor',
                    'name'         => 'category_description[' . $content_language_id . '][description]',
                    'value'        => $this->data['category_description'][$content_language_id]['description'],
                    'style'        => 'xl-field',
                    'multilingual' => true,
                    'history'      => $history
                ]
            );
        }
        $this->data['form']['fields']['data']['meta_keywords'] = $form->getFieldHtml(
            [
                'type'         => 'textarea',
                'name'         => 'category_description[' . $content_language_id . '][meta_keywords]',
                'value'        => $this->data['category_description'][$content_language_id]['meta_keywords'],
                'style'        => 'xl-field',
                'multilingual' => true,
                'history'      => $history
            ]
        );
        $this->data['form']['fields']['data']['meta_description'] = $form->getFieldHtml(
            [
                'type'         => 'textarea',
                'name'         => 'category_description[' . $content_language_id . '][meta_description]',
                'value'        => $this->data['category_description'][$content_language_id]['meta_description'],
                'style'        => 'xl-field',
                'multilingual' => true,
                'history'      => $history
            ]
        );

        $this->data['keyword_button'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'generate_seo_keyword',
                'text'  => $this->language->get('button_generate'),
                //set button not to submit a form
                'attr'  => 'type="button"',
                'style' => 'btn btn-info',
            ]
        );
        $this->data['generate_seo_url'] = $this->html->getSecureURL(
            'common/common/getseokeyword',
            '&object_key_name=category_id&id=' . $category_id
        );

        $this->data['form']['fields']['data']['keyword'] = $form->getFieldHtml(
            [
                'type'         => 'input',
                'name'         => 'keyword',
                'value'        => $this->data['keyword'],
                'help_url'     => $this->gen_help_url('seo_keyword'),
                'multilingual' => true,
                'attr'         => ' gen-value="' . SEOEncode($this->data['category_description']['name']) . '" ',
            ]
        );

        $this->data['form']['fields']['data']['store'] = $form->getFieldHtml(
            [
                'type'    => 'checkboxgroup',
                'name'    => 'category_store[]',
                'value'   => $this->data['category_store'],
                'options' => $stores,
                'style'   => 'chosen',
            ]
        );

        $this->data['form']['fields']['data']['sort_order'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'sort_order',
                'value' => $this->data['sort_order'],
                'style' => 'small-field',
            ]
        );

        $this->data['active'] = 'general';
        //load tabs controller
        $tabs_obj = $this->dispatch('pages/catalog/category_tabs', [$this->data]);
        $this->data['category_tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        if ($category_id && $this->config->get('config_embed_status')) {
            $this->data['embed_url'] = $this->html->getSecureURL(
                'common/do_embed/categories',
                '&category_id=' . $category_id
            );
        }

        $this->view->batchAssign($this->data);
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('language_id', $content_language_id);
        $this->view->assign('language_code', $this->session->data['language']);

        $this->addChild(
            'responses/common/resource_library/get_resources_html', 'resources_html',
            'responses/common/resource_library_scripts.tpl'
        );
        $resources_scripts = $this->dispatch(
            'responses/common/resource_library/get_resources_scripts',
            [
                'object_name' => 'categories',
                'object_id'   => (int)$category_id,
                'types'       => ['image'],
            ]
        );
        $this->view->assign('resources_scripts', $resources_scripts->dispatchGetOutput());
        $this->view->assign(
            'rl',
            $this->html->getSecureURL(
                'common/resource_library',
                '&action=list_library&object_name=&object_id&type=image&mode=single'
            )
        );

        $this->view->assign('current_url', $this->html->currentURL());
        $this->view->assign('list_url', $this->html->getSecureURL('catalog/category', '&saved_list=category_grid'));

        if ($viewport_mode == 'modal') {
            $tpl = 'responses/viewport/modal/catalog/category_form.tpl';
        } else {
            $tpl = 'pages/catalog/category_form.tpl';
        }
        $this->processTemplate($tpl);
    }

    protected function _validateForm()
    {
        if (!$this->user->canModify('catalog/category')) {
            $this->error['warning'][] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['category_description'] as $value) {
            $len = mb_strlen($value['name']);
            if ($len < 2 || $len > 255) {
                $this->error['warning'][] = $this->language->get('error_name');
            }
        }

        $error_text = $this->html->isSEOkeywordExists(
            'category_id=' . $this->request->get['category_id'],
            $this->request->post['keyword']
        );

        if ($error_text) {
            $this->error['warning'][] = $error_text;
        }

        $this->extensions->hk_ValidateData($this);

        if (!$this->error) {
            return true;
        } else {
            if (!isset($this->error['warning'])) {
                $this->error['warning'][] = $this->language->get('error_required_data');
            }
            $this->error['warning'] = implode('<br>', $this->error['warning']);
            return false;
        }
    }

    public function edit_layout()
    {
        $page_controller = 'pages/product/category';
        $page_key_param = 'path';
        $category_id = (int)$this->request->get['category_id'];
        $this->data['category_id'] = $category_id;
        $page_url = $this->html->getSecureURL(
            'catalog/category/edit_layout',
            '&category_id=' . $category_id
        );
        //note: category can not be ID of 0.
        if (!has_value($category_id)) {
            redirect($this->html->getSecureURL('catalog/category'));
        }

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('design/layout');
        $this->loadLanguage('catalog/category');
        $this->data['help_url'] = $this->gen_help_url('layout_edit');
        $this->loadModel('catalog/category');
        $this->data['category_description'] = $this->model_catalog_category->getCategoryDescriptions($category_id);
        $categoryName = $this->data['category_description'][$this->language->getContentLanguageID()]['name'];

        // Alert messages
        if (isset($this->session->data['warning'])) {
            $this->data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $this->data['heading_title'] = $this->language->get('text_edit')
            . ' '
            . $this->language->get('text_category')
            . ' - '
            . $categoryName;

        $this->document->setTitle($this->language->get('text_design') . ' - ' . $categoryName);
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/category'),
                'text'      => $this->language->get('heading_title', 'catalog/category'),
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $page_url,
                'text'      => $this->document->getTitle(),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->data['active'] = 'layout';
        //load tabs controller
        $tabs_obj = $this->dispatch('pages/catalog/category_tabs', [$this->data]);
        $this->data['category_tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $tmpl_id = $this->request->get['tmpl_id'] ?: $this->config->get('config_storefront_template');
        $layout = new ALayoutManager($tmpl_id);
        //get existing page layout or generic
        $page_layout = $layout->getPageLayoutIDs($page_controller, $page_key_param, $category_id);
        $page_id = $page_layout['page_id'];
        $layout_id = $page_layout['layout_id'];

        $params = [
            'category_id' => $category_id,
            'page_id'     => $page_id,
            'layout_id'   => $layout_id,
            'tmpl_id'     => $tmpl_id,
        ];
        $url = '&' . $this->html->buildURI($params);

        // get templates
        $this->data['templates'] = [];
        $directories = glob(DIR_STOREFRONT . 'view/*', GLOB_ONLYDIR);
        if ($directories) {
            $this->data['templates'] = array_map('basename', $directories);
        }
        $enabled_templates = $this->extensions->getExtensionsList(
            [
                'filter' => 'template',
                'status' => 1,
            ]
        );
        $this->data['templates'] = array_merge($this->data['templates'], array_column($enabled_templates->rows, 'key'));

        $action = $this->html->getSecureURL('catalog/category/save_layout');
        // Layout form data
        $form = new AForm('HT');
        $form->setForm(
            [
                'form_name' => 'layout_form',
            ]
        );

        $this->data['form_begin'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'layout_form',
                'attr'   => 'data-confirm-exit="true"',
                'action' => $action,
            ]
        );

        $this->data['hidden_fields'] = [];
        foreach ($params as $name => $value) {
            $this->data[$name] = $value;
            $this->data['hidden_fields'][] = $form->getFieldHtml(
                [
                    'type'  => 'hidden',
                    'name'  => $name,
                    'value' => $value,
                ]
            );
        }

        $this->data['page_url'] = $page_url;
        $this->data['current_url'] = $this->html->getSecureURL('catalog/category/edit_layout', $url);

        // insert external form of layout
        $layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);

        $layout_form = $this->dispatch('common/page_layout', [$layout]);
        $this->data['block_layout_form'] = $layout_form->dispatchGetOutput();

        //build pages and available layouts for cloning
        $this->data['pages'] = $layout->getAllPages();
        $avLayouts = ["0" => $this->language->get('text_select_copy_layout')]
            + array_column($this->data['pages'], 'layout_name', 'layout_id');
        unset($avLayouts[$layout_id]);

        $form = new AForm('HT');
        $form->setForm(
            [
                'form_name' => 'cp_layout_frm',
            ]
        );

        $this->data['cp_layout_select'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'source_layout_id',
                'value'   => '',
                'options' => $avLayouts,
            ]
        );

        $this->data['cp_layout_frm'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'cp_layout_frm',
                'attr'   => 'class="aform form-inline"',
                'action' => $action,
            ]
        );
        $this->view->batchAssign($this->data);

        $this->processTemplate('pages/catalog/category_layout.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function save_layout()
    {
        if ($this->request->is_GET() || !$this->request->post) {
            redirect($this->html->getSecureURL('catalog/category'));
        }

        $post = $this->request->post;
        $pageData = [
            'controller' => 'pages/product/category',
            'key_param'  => 'path',
            'key_value'  => (int)$this->request->get_or_post('category_id'),
        ];

        $this->loadLanguage('catalog/category');

        if (!$pageData['key_value']) {
            unset($this->session->data['success']);
            redirect($this->html->getSecureURL('catalog/category'));
        }

        /** @var ModelCatalogCategory $mdl */
        $mdl = $this->loadModel('catalog/category');
        $categoryInfo = $mdl->getCategoryDescriptions($pageData['key_value']);
        if ($categoryInfo) {
            $post['layout_name'] = $this->language->get('text_category')
                . ': '
                . $categoryInfo[$this->language->getContentLanguageID()]['name'];
            $pageData['page_descriptions'] = $categoryInfo;
        }

        if (saveOrCreateLayout($post['tmpl_id'], $pageData, $post)) {
            $this->session->data['success'] = $this->language->get('text_success_layout');
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        redirect(
            $this->html->getSecureURL(
                'catalog/category/edit_layout',
                '&category_id=' . $pageData['key_value'] . '&tmpl_id=' . $post['tmpl_id']
            )
        );
    }
}