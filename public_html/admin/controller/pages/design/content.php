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

class ControllerPagesDesignContent extends AController
{
    public $error = [];
    /** @var AContentManager */
    protected $acm;

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

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
                'href'      => $this->html->getSecureURL('design/content'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $grid_settings = [
            'table_id'         => 'content_grid',
            'url'              => $this->html->getSecureURL('listing_grid/content'),
            'editurl'          => $this->html->getSecureURL('listing_grid/content/update'),
            'update_field'     => $this->html->getSecureURL('listing_grid/content/update_field'),
            'sortname'         => 'sort_order',
            'sortorder'        => 'asc',
            'drag_sort_column' => 'sort_order',
            'columns_search'   => true,
            'actions'          => [
                'edit'   => [
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('design/content/update', '&content_id=%ID%'),
                ],
                'delete' => [
                    'text' => $this->language->get('button_delete'),
                ],
                'save'   => [
                    'text' => $this->language->get('button_save'),
                ],
                'clone'  => [
                    'text' => $this->language->get('text_clone'),
                    'href' => $this->html->getSecureURL('design/content/clone', '&content_id=%ID%'),
                ],
            ],
        ];

        $grid_settings['colNames'] = [
            $this->language->get('column_title'),
            $this->language->get('column_parent'),
            $this->language->get('column_status'),
            $this->language->get('column_publish_date'),
            $this->language->get('column_sort_order'),
        ];
        $grid_settings['colModel'] = [
            [
                'name'  => 'title',
                'index' => 'id.title',
                'width' => 250,
                'align' => 'left',
            ],
            [
                'name'   => 'parent_name',
                'index'  => 'parent_name',
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
                'name'   => 'publish_date',
                'index'  => 'publish_date',
                'width'  => 100,
                'align'  => 'center',
                'search' => false,
            ],
            [
                'name'   => 'sort_order',
                'index'  => 'sort_order',
                'width'  => 100,
                'align'  => 'center',
                'search' => false,
            ],
        ];
        if ($this->config->get('config_show_tree_data')) {
            $grid_settings['expand_column'] = 'title';
            $grid_settings['multiaction_class'] = 'hidden';
        }
        $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());

        $this->document->setTitle($this->language->get('heading_title'));
        $this->view->assign('insert', $this->html->getSecureURL('design/content/insert'));
        $this->view->assign('help_url', $this->gen_help_url('content_listing'));

        $this->processTemplate('pages/design/content_list.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function insert()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));
        $this->acm = new AContentManager();
        $contentId = 0;
        if ($this->request->is_POST() && $this->validateForm()) {
            $post = $this->request->post;
            foreach(['publish_date', 'expire_date'] as $datetime) {
                if ($post[$datetime]) {
                    $post[$datetime] = dateDisplay2ISO(
                        $post[$datetime],
                        $this->language->get('date_format_short') . ' ' . $this->language->get('time_format_short')
                    );
                }else{
                    $post[$datetime] = '';
                }
            }
            $contentId = $this->acm->addContent($post);
            $this->extensions->hk_ProcessData($this, __FUNCTION__, ['content_id' => $contentId]);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('design/content/update', '&content_id=' . $contentId));
        }

        // content language switcher
        $languages = $this->language->getActiveLanguages();
        if (sizeof($languages) > 1) {
            $this->view->assign('languages', $languages);
            //selected in selectbox
            $this->view->assign('language_code', $this->session->data['content_language']);
            $get = $this->request->get;
            $hiddens = [];
            foreach ($get as $name => $value) {
                if ($name == 'content_language_code') {
                    continue;
                }
                $hiddens[$name] = $value;
            }
            $this->view->assign('lang_action', $this->html->getSecureURL('design/content/update'));
            $this->view->assign('hiddens', $hiddens);
        }
        $this->_initTabs('form');
        $this->_getForm($contentId);

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        if (!$this->request->get['content_id']) {
            redirect($this->html->getSecureURL('design/content/insert'));
        }
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->document->setTitle($this->language->get('update_title'));
        $this->acm = new AContentManager();
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $contentId = (int)$this->request->get['content_id'];
        if ($this->request->is_POST() && $this->validateForm()) {
            $post = $this->request->post;
            foreach(['publish_date', 'expire_date'] as $datetime) {
                if ($post[$datetime]) {
                    $post[$datetime] = dateDisplay2ISO(
                        $post[$datetime],
                        $this->language->get('date_format_short') . ' ' . $this->language->get('time_format_short')
                    );
                }else{
                    $post[$datetime] = '';
                }
            }
            $this->acm->editContent($contentId, $post);
            $this->extensions->hk_ProcessData($this, __FUNCTION__, ['content_id' => $contentId]);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('design/content/update', '&content_id=' . $contentId));
        }
        $this->_initTabs('form');
        $this->view->assign('content_id', $contentId);
        $this->view->assign(
            'insert',
            $this->html->getSecureURL(
                'design/content/insert',
                '&parent_content_id=' . $contentId
            )
        );
        $this->view->assign('clone_url',
            $this->html->getSecureURL(
                'design/content/clone',
                '&content_id=' . $contentId)
        );
        /** @var ModelSettingSetting $mdl */
        $mdl = $this->loadModel('setting/setting');
        $settings = $mdl->getSetting('details', (int)$this->session->data['current_store_id']);
        $preview = $settings['config_url'] . INDEX_FILE . '?' . 'rt=content/content&content_id=' . $contentId;
        $this->view->assign('preview', $preview);

        $this->_getForm($contentId);
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _initTabs($active = null)
    {
        $content_id = (int)$this->request->get['content_id'];
        //no need tabs for new content
        if (!$content_id) {
            return null;
        }

        $this->data['tabs'] = [
            'form' => [
                'href' => $this->html->getSecureURL(
                    'design/content/update',
                    '&content_id=' . $content_id
                ),
                'text' => $this->language->get('tab_form'),
            ],
        ];

        $this->data['tabs']['layout'] = [
            'href' => $this->html->getSecureURL('design/content/edit_layout', '&content_id=' . $content_id),
            'text' => $this->language->get('tab_layout'),
        ];

        if (in_array($active, array_keys($this->data['tabs']))) {
            $this->data['tabs'][$active]['active'] = 1;
        } else {
            $this->data['tabs']['form']['active'] = 1;
        }
    }

    protected function _getForm($contentId)
    {
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->data['error'] = $this->error;
        $this->data['language_id'] = $this->language->getContentLanguageID();
        $content_info = [];
        if ($contentId && $this->request->is_GET()) {
            $content_info = $this->acm->getContent($contentId);
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
                'href'      => $this->html->getSecureURL('design/content'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );

        if ($contentId) {
            $this->document->addBreadcrumb(
                [
                    'href'      => $this->html->getSecureURL(
                        'design/content/update',
                        '&content_id=' . $contentId
                    ),
                    'text'      => $this->language->get('update_title') . ' - ' . $content_info['title'],
                    'separator' => ' :: ',
                    'current'   => true,
                ]
            );
        } else {
            $this->document->addBreadcrumb(
                [
                    'href'      => $this->html->getSecureURL('design/content/insert'),
                    'text'      => $this->language->get('insert_title'),
                    'separator' => ' :: ',
                    'current'   => true,
                ]
            );
        }

        $this->data['cancel'] = $this->html->getSecureURL('design/content');
        $allowedFields = [
            'status',
            'store_id',
            'author',
            'publish_date',
            'expire_date',
            'icon_rl_id',
            'tags',
            'description',
            'meta_keywords',
            'meta_description',
            'title',
            'content',
            'parent_content_id',
            'sort_order',
            'keyword',
        ];
        foreach ($allowedFields as $field) {
            $this->data[$field] = $this->request->post[$field] ?? $content_info[$field] ?? '';
        }
        //if got parent_id - create new content for parent
        if ($this->request->get['parent_content_id']) {
            $this->data['parent_content_id'] = $this->request->get['parent_content_id'];
        }

        if (!$contentId) {
            $this->data['action'] = $this->html->getSecureURL('design/content/insert');
            $this->data['form_title'] = $this->language->get('insert_title');
            $this->data['update'] = '';
            $form = new AForm('ST');
        } else {
            $this->data['action'] = $this->html->getSecureURL('design/content/update', '&content_id=' . $contentId);
            $this->data['form_title'] = $this->language->get('update_title');
            $this->data['update'] = $this->html->getSecureURL('listing_grid/content/update_field', '&id=' . $contentId);
            $form = new AForm('HS');
        }

        $form->setForm(
            [
                'form_name' => 'contentFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'contentFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'contentFrm',
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

        $this->data['form']['fields']['status'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'status',
                'value' => $this->data['status'],
                'style' => 'btn_switch',
            ]
        );

        // get array with stores looks like array (store_id=>array(content_id=>store_name))
        $store_values = $store_selected = [];
        $store_values[0] = $this->language->get('text_default');
        $stores = $this->acm->getContentStores();
        if (count($stores) > 1) {
            foreach ($stores as $store_id => $store) {
                $store_values[$store_id] = trim(current($store));
                if (isset($store[$contentId])) {
                    $store_selected[$store_id] = $store_id;
                }
            }
            if (!$store_selected) {
                $store_selected[0] = 0;
            }
            $this->data['form']['fields']['store'] = $form->getFieldHtml(
                [
                    'type'      => 'checkboxgroup',
                    'name'      => 'store_id[]',
                    'value'     => $store_selected,
                    'options'   => $store_values,
                    'scrollbox' => true,
                    'style'     => 'chosen'
                ]
            );
        } else {
            //only one store
            $this->data['form']['fields']['store'] = $form->getFieldHtml(
                [
                    'type'  => 'hidden',
                    'name'  => 'store_id[]',
                    'value' => 0,
                ]
            );
        }

        // we need get contents list for multiselect
        $selected_parent = $disabled_parent = [];
        $selectTree = $this->acm->getContentsForSelect();
        foreach ($selectTree as $node) {
            $id = $node['content_id'];
            if ($id == $content_info['parent_content_id']) {
                $selected_parent[$id] = (string)$id;
            }
            if ($id == $contentId) {
                $disabled_parent[$id] = $id;
                $disabled_parent += array_combine($node['children'], $node['children']);
            }
        }
        $this->data['form']['fields']['parent'] = $form->getFieldHtml(
            [
                'type'             => 'selectbox',
                'name'             => 'parent_content_id',
                'options'          => array_column($selectTree, 'title', 'content_id'),
                'value'            => $selected_parent,
                'disabled_options' => $disabled_parent,
                'attr'             => 'size = "' . min(sizeof($selectTree), 10) . '"',
            ]
        );

        $this->data['form']['fields']['sort_order'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'sort_order',
                'value' => $this->data['sort_order'],
                'style' => 'tiny-field',
            ]
        );

        $this->data['form']['fields']['icon'] = $form->getFieldHtml(
            [
                'type'        => 'resource',
                'name'        => 'icon_rl_id',
                'resource_id' => $this->data['icon_rl_id'] ?: '',
                'rl_type'     => 'image',
            ]
        );
        //adds scripts for RL
        $resources_scripts = $this->dispatch(
            'responses/common/resource_library/get_resources_scripts',
            [
                'object_name' => 'contents',
                'object_id'   => $contentId,
                'types'       => ['image'],
                'onload'      => true,
                'mode'        => 'single',
            ]
        );
        $this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();
        $this->data['rl'] = $this->html->getSecureURL(
            'common/resource_library',
            '&action=list_library&object_name=&object_id&type=image&mode=single'
        );

        $this->data['form']['fields']['author'] = $form->getFieldHtml(
            [
                'type'         => 'input',
                'name'         => 'author',
                'value'        => $this->data['author'],
                'required'     => false,
                'multilingual' => false,
            ]
        );

        $this->data['form']['fields']['publish_date'] = $form->getFieldHtml(
            [
                'type'       => 'date',
                'name'       => 'publish_date',
                'value'      => dateISO2Display(
                    $this->data['publish_date'],
                    $this->language->get('date_format_short').' '.$this->language->get('time_format_short')
                ),
                'default'    => '',
                'dateformat' => format4Datepicker(
                    $this->language->get('date_format_short').' '.$this->language->get('time_format_short')
                ),
            ]
        );

        $this->data['form']['fields']['expire_date'] = $form->getFieldHtml(
            [
                'type'       => 'date',
                'name'       => 'expire_date',
                'value'      => dateISO2Display(
                    $this->data['expire_date'],
                    $this->language->get('date_format_short').' '.$this->language->get('time_format_short')
                ),
                'default'    => '',
                'dateformat' => format4Datepicker(
                    $this->language->get('date_format_short').' '.$this->language->get('time_format_short')
                ),
            ]
        );

        $this->data['form']['fields']['title'] = $form->getFieldHtml(
            [
                'type'         => 'input',
                'name'         => 'title',
                'value'        => $this->data['title'],
                'required'     => true,
                'multilingual' => true,
            ]
        );
        $this->data['form']['fields']['description'] = $form->getFieldHtml(
            [
                'type'         => 'textarea',
                'name'         => 'description',
                'value'        => $this->data['description'],
                'multilingual' => true,
            ]
        );

        $this->data['form']['fields']['meta_keywords'] = $form->getFieldHtml(
            [
                'type'         => 'textarea',
                'name'         => 'meta_keywords',
                'value'        => $this->data['meta_keywords'],
                'multilingual' => true,
            ]
        );

        $this->data['form']['fields']['meta_description'] = $form->getFieldHtml(
            [
                'type'         => 'textarea',
                'name'         => 'meta_description',
                'value'        => $this->data['meta_description'],
                'multilingual' => true,
            ]
        );

        $this->data['form']['fields']['content'] = $form->getFieldHtml(
            [
                'type'         => 'texteditor',
                'name'         => 'content',
                'value'        => $this->data['content'],
                'required'     => true,
                'multilingual' => true,
            ]
        );
        $this->data['keyword_button'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'generate_seo_keyword',
                'text'  => $this->language->get('button_generate'),
                'style' => 'btn btn-info',
            ]
        );

        $this->data['generate_seo_url'] = $this->html->getSecureURL(
            'common/common/getseokeyword',
            '&object_key_name=content_id&id=' . $contentId
        );

        $this->data['form']['fields']['tags'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'tags',
                'value' => $this->data['tags'],
            ]
        );

        $this->data['form']['fields']['keyword'] = $form->getFieldHtml(
            [
                'type'         => 'input',
                'name'         => 'keyword',
                'value'        => $this->data['keyword'],
                'style'        => 'large-field',
                'multilingual' => true,
                'help_url'     => $this->gen_help_url('seo_keyword'),
            ]
        );
        $this->data['list_url'] = $this->html->getSecureURL('design/content', '&saved_list=content_grid');
        $this->view->assign('help_url', $this->gen_help_url('content_edit'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/design/content_form.tpl');
    }

    protected function validateForm()
    {
        if (!$this->user->canModify('design/content')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        $len = mb_strlen($this->request->post['title']);
        if ($len < 2 || $len > 255) {
            $this->error['title'] = $this->language->get('error_title');
        }

        if (isHtml(html_entity_decode($this->request->post['title']))) {
            $this->error['title'] = $this->language->get('error_title_html');
        }

        if (mb_strlen($this->request->post['content']) < 2) {
            $this->error['content'] = $this->language->get('error_content');
        }

        if (($error_text = $this->html->isSEOkeywordExists(
            'content_id=' . (int)$this->request->get['content_id'],
            $this->request->post['keyword']
        ))
        ) {
            $this->error['keyword'] = $error_text;
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

    public function edit_layout()
    {
        $page_controller = 'pages/content/content';
        $page_key_param = 'content_id';

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('design/layout');
        $this->document->setTitle($this->language->get('update_title'));
        $this->acm = new AContentManager();

        $content_id = (int)$this->request->get['content_id'];
        if (!$content_id) {
            redirect($this->html->getSecureURL('design/content'));
        }

        $page_url = $this->html->getSecureURL('design/content/edit_layout', '&content_id=' . $content_id);

        // Alert messages
        if (isset($this->session->data['warning'])) {
            $this->data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $this->data['help_url'] = $this->gen_help_url('content_layout');

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
                'href'      => $this->html->getSecureURL('design/content'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('design/content/update', '&content_id=' . $content_id),
                'text'      => $this->language->get('update_title'),
                'separator' => ' :: ',
            ]
        );
        $content_info = $this->acm->getContent($content_id);
        $this->document->addBreadcrumb(
            [
                'href'    => $page_url,
                'text'    => $this->language->get('tab_layout') . ' - ' . $content_info['title'],
                'current' => true,
            ]
        );

        $this->_initTabs('layout');

        $tmpl_id = $this->request->get['tmpl_id'] ?: $this->config->get('config_storefront_template');
        $layout = new ALayoutManager($tmpl_id);
        //get existing page layout or generic
        $page_layout = $layout->getPageLayoutIDs($page_controller, $page_key_param, $content_id);
        $page_id = $page_layout['page_id'];
        $layout_id = $page_layout['layout_id'];
        $params = [
            'content_id' => $content_id,
            'page_id'    => $page_id,
            'layout_id'  => $layout_id,
            'tmpl_id'    => $tmpl_id,
        ];
        $url = '&' . http_build_query($params);

        // get templates
        $this->data['templates'] = [];
        $directories = glob(DIR_STOREFRONT . 'view/*', GLOB_ONLYDIR);
        if ($directories) {
            $this->data['templates'] = array_map('basename', $directories);
        }
        $enabled_templates = $this->extensions->getExtensionsList(['filter' => 'template', 'status' => 1]);
        $this->data['templates'] += array_column($enabled_templates->rows, 'key');

        $action = $this->html->getSecureURL('design/content/save_layout');
        // Layout form data
        $form = new AForm('HT');
        $form->setForm(['form_name' => 'layout_form']);
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
        $this->data['current_url'] = $this->html->getSecureURL('design/content/edit_layout', $url);

        // insert external form of layout
        $layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);
        $layout_form = $this->dispatch('common/page_layout', [$layout]);
        $this->data['layoutform'] = $layout_form->dispatchGetOutput();

        //build pages and available layouts for cloning
        $this->data['pages'] = $layout->getAllPages();
        $av_layouts = ["0" => $this->language->get('text_select_copy_layout')];
        foreach ($this->data['pages'] as $page) {
            if ($page['layout_id'] != $layout_id) {
                $av_layouts[$page['layout_id']] = $page['layout_name'];
            }
        }

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
                'options' => $av_layouts,
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

        $this->view->assign('heading_title', $this->language->get('heading_title'));

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/design/content_layout.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function save_layout()
    {
        if ($this->request->is_GET() || !$this->request->post) {
            redirect($this->html->getSecureURL('design/content'));
        }
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $post = $this->request->post;
        $this->acm = new AContentManager();
        $content_id = (int)$post['content_id'];
        $pageData = [
            'controller' => 'pages/content/content',
            'key_param'  => 'content_id',
            'key_value'  => $content_id,
        ];

        $this->loadLanguage('catalog/product');
        if (!$pageData['key_value']) {
            unset($this->session->data['success']);
            redirect($this->html->getSecureURL('design/content'));
        }

        $this->acm = new AContentManager();
        $languageId = $this->language->getDefaultLanguageID();
        $content_info = $this->acm->getContent($content_id, $languageId);
        if ($content_info) {
            $title = $content_info['title'] ?: 'Unnamed content page';
            $pageData['page_descriptions'][$languageId]['name'] = $title;
            $post['layout_name'] = $this->language->get('text_content', 'common/header') . ': ' . $title;
        }

        if (saveOrCreateLayout($post['tmpl_id'], $pageData, $post)) {
            $this->session->data['success'] = $this->language->get('text_success_layout');
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        redirect(
            $this->html->getSecureURL(
                'design/content/edit_layout',
                '&content_id=' . $content_id . '&tmpl_id=' . $post['tmpl_id']
            )
        );
    }

    public function clone()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->acm = new AContentManager();
        $content_id = (int)$this->request->get['content_id'];
        $this->document->setTitle($this->language->get('heading_title'));
        if ($content_id && $this->validateCopy()) {
            $this->data['new_content'] = $this->acm->cloneContent($content_id);
            $this->extensions->hk_ProcessData($this, 'content_copy');
            if ($this->data['new_content']) {
                $this->session->data['success'] = sprintf(
                    $this->language->get('text_success_copy'),
                    $this->data['new_content']['title']
                );

                if ($this->data['new_content']['layout_clone']) {
                    $this->session->data['success'] .= ' ' . $this->language->get('text_success_copy_layout');
                }
                redirect(
                    $this->html->getSecureURL(
                        'design/content/update',
                        '&content_id=' . $this->data['new_content']['id']
                    )
                );
            } else {
                $this->session->data['success'] = $this->language->get('text_error_copy');
                redirect($this->html->getSecureURL('design/content/update', '&content_id=' . $content_id));
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function validateCopy()
    {
        if (!$this->user->canModify('design/content')) {
            $this->error['warning'] = $this->language->get_error('error_permission');
        }
        $this->extensions->hk_ValidateData($this, [__FUNCTION__]);
        return (!$this->error);
    }
}
