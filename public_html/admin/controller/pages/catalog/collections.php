<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

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

/**
 * Class ControllerPagesCatalogCategory
 */
class ControllerPagesCatalogCollections extends AController
{
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('catalog/collections');
        $this->buildHeader();
        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

        $grid_settings = [
            'table_id'       => 'collections_grid',
            'url'            => $this->html->getSecureURL('listing_grid/collections'),
            'editurl'        => $this->html->getSecureURL('listing_grid/collections/update'),
            'update_field'   => $this->html->getSecureURL('listing_grid/collections/update_field'),
            'sortname'       => 'text_id',
            'sortorder'      => 'asc',
            'columns_search' => true,
            'actions'        => [
                'edit'   => [
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('catalog/collections/update', '&id=%ID%'),
                ],
                'delete' => [
                    'text' => $this->language->get('button_delete'),
                ],
            ],
        ];

        $grid_settings['colNames'] = [
            $this->language->get('collection_column_name'),
            $this->language->get('collection_column_status'),
            $this->language->get('collection_column_date_added'),
        ];

        $grid_settings['colModel'] = [
            [
                'name'  => 'name',
                'index' => 'name',
                'width' => 200,
                'align' => 'left',
            ],
            [
                'name'   => 'status',
                'index'  => 'status',
                'width'  => 50,
                'align'  => 'center',
                'search' => false,
            ],
            [
                'name'   => 'date_added',
                'index'  => 'date_added',
                'width'  => 50,
                'align'  => 'center',
                'search' => false,
            ],
        ];

        $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('insert', $this->html->getSecureURL('catalog/collections/insert'));
        $this->view->assign('help_url', $this->gen_help_url('data_collections'));

        $this->processTemplate('pages/catalog/collections_list.tpl');

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function buildHeader()
    {
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/collections'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->document->setTitle($this->language->get('heading_title'));
    }

    public function insert()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->buildHeader();

        /** @var ModelCatalogCollection $mdl */
        $mdl = $this->loadModel('catalog/collection');

        if ($this->request->is_POST() && $this->validate($this->request->post)) {
            $data = $this->request->post;
            $collection = $mdl->insert($data);
            $this->extensions->hk_ProcessData($this, __FUNCTION__, ['inData' => $data, 'collection' => $collection]);
            $this->session->data['success'] = $this->language->get('save_complete');
            redirect($this->html->getSecureURL('catalog/collections/update', '&id=' . $collection['id']));
        }

        if ($this->session->data['success']) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if ($this->session->data['warning']) {
            $this->error['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }

        if (!empty($this->error)) {
            $this->view->assign('error', $this->error);
        }

        $this->data['form_title'] = $this->language->get('collection_new');
        $this->data['conditions_title'] = $this->language->get('conditions_title');

        $this->getForm();

        $this->view->batchAssign($this->data);
        $this->view->assign('help_url', $this->gen_help_url('data_collections'));
        $this->view->assign('list_url', $this->html->getSecureURL('catalog/collections'));
        /** @see public_html/admin/view/default/template/pages/catalog/collection_form.tpl */
        $this->processTemplate('pages/catalog/collection_form.tpl');

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $collectionId = (int) $this->request->get['id'];
        $this->buildHeader();

        /** @var ModelCatalogCollection $mdl */
        $mdl = $this->loadModel('catalog/collection');
        $this->loadModel('localisation/language');

        if ($this->request->is_POST()) {
            $collection = $mdl->getById($collectionId);

            if ($collection && $this->validate($this->request->post)) {
                try {
                    $mdl->update($collectionId, $this->request->post);
                    $this->session->data['success'] = $this->language->get('save_complete');
                    $this->extensions->hk_ProcessData($this, 'update', ['inData' => $this->request->post]);
                    redirect(
                        $this->html->getSecureURL(
                            'catalog/collections/update',
                            '&id=' . $collectionId
                        )
                    );
                } catch (Exception $e) {
                    $this->log->write($e->getMessage());
                    $this->session->data['warning'] = $this->language->get('save_error');
                }
            }

            if (!$collection || !$this->validate($this->request->post)) {
                $this->session->data['warning'] = $this->language->get('save_error');
            }
        }

        if (!$collectionId) {
            redirect($this->html->getSecureURL('catalog/collections'));
        }

        if ($this->session->data['success']) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if ($this->session->data['warning']) {
            $this->error['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }

        if (!empty($this->error)) {
            $this->view->assign('error', $this->error);
        }

        $this->data['form_title'] = $this->language->get('collection_update');
        $this->data['conditions_title'] = $this->language->get('conditions_title');

        $this->getForm();

        if ($this->config->get('config_embed_status')) {
            $btnData = getEmbedButtonsData(
                'common/do_embed/collections',
                ['collection_id' => $collectionId]
            );
            $this->data['embed_url'] = $btnData['embed_url'];
            $this->data['embed_stores'] = $btnData['embed_stores'];
        }

        $this->view->batchAssign($this->data);
        $this->view->assign('help_url', $this->gen_help_url('data_collections'));
        $this->view->assign('list_url', $this->html->getSecureURL('catalog/collections'));
        $this->processTemplate('pages/catalog/collection_form.tpl');
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function getForm()
    {
        /** @var ModelCatalogCollection $mdl */
        $mdl = $this->loadModel('catalog/collection');
        $collection = null;
        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('error_name', $this->error['name']);
        $this->view->assign('cancel', $this->html->getSecureURL('catalog/collections'));

        $collectionId = (int) $this->request->get['id'];
        if ($collectionId) {
            $collection = $mdl->getById($collectionId);
            if (!$collection) {
                redirect($this->html->getSecureURL('catalog/collections'));
            }

            $this->data = array_merge($this->data, $collection);

            /** @var ModelSettingSetting $sMdl */
            $sMdl = $this->loadModel('setting/setting');
            $storeSettings = $sMdl->getSetting('details', (int) $this->session->data['current_store_id']);
            $this->data['preview'] = $storeSettings['config_ssl_url'] ? : $storeSettings['config_url'];
            if ($this->data['keyword']
                && $sMdl->getSettingByKey('enable_seo_url', (int) $this->session->data['current_store_id'])
            ) {
                $this->data['preview'] .= $this->data['keyword'];
            } else {
                $this->data['preview'] .= '?rt=product/collection&collection_id=' . $collectionId;
            }
            $this->data['text_view'] = $this->language->get('text_storefront');
        }

        if ($this->request->get['products']) {
            $productIds = filterIntegerIdList((array) $this->request->get['products']);
            if (is_array($productIds) && !empty($productIds)) {
                $this->data['conditions']['conditions'][] = [
                    'object'   => 'products',
                    'value'    => $productIds,
                    'operator' => 'in',
                ];
            }
        }

        if ($this->request->post) {
            $this->data = array_merge($this->data, $this->request->post);
        }

        if ($collection) {
            $this->data['action'] = $this->html->getSecureURL('catalog/collections/update', '&id=' . $collectionId);
            $this->data['update'] = $this->html->getSecureURL(
                'listing_grid/collections/update_field',
                '&id=' . $collectionId
            );
            $form = new AForm ('HS');
        } else {
            $this->data['action'] = $this->html->getSecureURL('catalog/collections/insert');
            $form = new AForm ('ST');
        }

        $form->setForm(
            [
                'form_name' => 'collectionsFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'collectionsFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'collectionsFrm',
                'action' => $this->data['action'],
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
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
                'value' => $this->data['status'] ?? 1,
                'style' => 'btn_switch',
            ]
        );

        $this->data['entry_store'] = $this->language->get('entry_store', 'catalog/product');
        /** @var ModelSettingStore $mdl */
        $mdl = $this->loadModel('setting/store');

        $stores = [0 => $this->language->get('text_default')]
            + array_column($mdl->getStores(), 'name', 'store_id');

        $this->data['form']['fields']['general']['store'] = $form->getFieldHtml(
            [
                'type'    => 'checkboxgroup',
                'name'    => 'stores[]',
                // if new collection, take selected store from storeSwitcher
                //otherwise - take from collection data
                'value'   => $this->data['stores']
                    ? : $collection['stores']
                        ? : [
                            $this->config->get(
                                'current_store_id'
                            ),
                        ],
                'options' => $stores,
                'style'   => 'chosen',
            ]
        );

        $history = [
            'table'     => 'collection_descriptions',
            'record_id' => $collectionId,
        ];

        $this->data['form']['fields']['general']['name'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'name',
                'value'    => $this->data['name'],
                'required' => true,
            ]
        );

        $this->data['form']['fields']['general']['description'] = $form->getFieldHtml(
            [
                'type'    => 'textarea',
                'name'    => 'description',
                'value'   => $this->data['description'],
                'history' => $history,
            ]
        );

        $this->data['form']['fields']['general']['title'] = $form->getFieldHtml(
            [
                'type'         => 'input',
                'name'         => 'title',
                'value'        => $this->data['title'],
                'multilingual' => true,
                'history'      => $history,
            ]
        );
        $this->data['form']['fields']['general']['meta_keywords'] = $form->getFieldHtml(
            [
                'type'         => 'textarea',
                'name'         => 'meta_keywords',
                'value'        => $this->data['meta_keywords'],
                'style'        => 'xl-field',
                'multilingual' => true,
                'history'      => $history,
            ]
        );
        $this->data['form']['fields']['general']['meta_description'] = $form->getFieldHtml(
            [
                'type'         => 'textarea',
                'name'         => 'meta_description',
                'value'        => $this->data['meta_description'],
                'style'        => 'xl-field',
                'multilingual' => true,
                'history'      => $history,
            ]
        );
        $this->data['form']['fields']['general']['content'] = $form->getFieldHtml(
            [
                'type'         => 'texteditor',
                'name'         => 'content',
                'value'        => $this->data['content'],
                'multilingual' => true,
                'history'      => $history,
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
            '&object_key_name=collection_id&id=' . $collectionId
        );

        $this->data['form']['fields']['general']['keyword'] = $form->getFieldHtml(
            [
                'type'         => 'input',
                'name'         => 'keyword',
                'value'        => $this->data['keyword'],
                'help_url'     => $this->gen_help_url('seo_keyword'),
                'multilingual' => true,
                'attr'         => ' gen-value="' . SEOEncode((string) $this->data['name']) . '" ',
            ]
        );

        // relations between conditions
        $this->data['conditions_relation']['fields']['if'] = [
            'text'  => $this->language->get('text_if_1'),
            'field' => $form->getFieldHtml(
                [
                    'type'    => 'selectbox',
                    'name'    => 'conditions[relation][if]',
                    'style'   => 'no-save',
                    'options' => [
                        'all' => $this->language->get('text_all'),
                        'any' => $this->language->get('text_any'),
                    ],
                    'value'   => ($this->data['conditions']['relation']['if'] ?? ''),
                ]
            ),
        ];

        $this->data['conditions_relation']['fields']['value'] = [
            'text'  => $this->language->get('text_if_2'),
            'field' => $form->getFieldHtml(
                [
                    'type'    => 'selectbox',
                    'name'    => 'conditions[relation][value]',
                    'style'   => 'no-save',
                    'options' => [
                        'true'  => $this->language->get('text_true'),
                        'false' => $this->language->get('text_false'),
                    ],
                    'value'   => ($this->data['conditions']['relation']['value'] ?? ''),
                ]
            ),
        ];

        // conditions
        if (isset($this->data['conditions']['conditions'])) {
            $i = 0;
            $this->load->library('json');
            foreach ($this->data['conditions']['conditions'] as $rule) {
                $this->request->post['idx'] = $i;
                $this->request->post['condition_object'] = $rule['object'];
                $args = [
                    0, //instance_id. @see core/engine/dispatcher
                    [
                        'operator'  => $rule['operator'],
                        'value'     => $rule['value'],
                        'store_ids' => $this->data['form']['fields']['general']['store']->value,
                    ],
                ];
                /** @see ControllerResponsesListingGridCollections::getFieldsByConditionObject() */
                $fields = $this->dispatch('responses/listing_grid/collections/getFieldsByConditionObject', $args);
                $fields = AJson::decode($fields->dispatchGetOutput(), true);
                $this->data['form']['fields']['conditions'][$i]['id'] = $rule['object'];
                $this->data['form']['fields']['conditions'][$i]['text'] = $fields['text'];
                $this->data['form']['fields']['conditions'][$i]['field'] = $fields['fields'];
                $i++;
            }
        }

        $cond_objects = [
            'product_price',
            'categories',
            'brands',
            'products',
            'tags',
        ];

        foreach ($cond_objects as $obj) {
            $this->data['condition_objects'][$obj] = $this->language->get('text_' . $obj);
        }
        array_unshift($this->data['condition_objects'], $this->language->get('text_select'));
        $this->data['condition_object'] = [];
        $this->data['condition_object']['field'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'condition_object',
                'options' => $this->data['condition_objects'],
                'value'   => $this->data['promotion_type'],
                'style'   => 'no-save',
            ]
        );
        $this->data['condition_object']['text'] = $this->language->get('entry_condition_object');

        $this->data['condition_url'] = $this->html->getSecureURL(
            'listing_grid/collections/getFieldsByConditionObject',
            '&id=' . $this->data['id']
        );

        $tabs['general'] = [
            'name'       => 'general',
            'text'       => $this->language->get('tab_general'),
            'href'       => $this->data['action'],
            'active'     => true,
            'sort_order' => 0,
        ];
        if ($collectionId) {
            $tabs['design'] = [
                'name'       => 'design',
                'text'       => $this->language->get('tab_design'),
                'href'       => $this->html->getSecureURL('catalog/collections/edit_layout', '&id=' . $collectionId),
                'active'     => false,
                'sort_order' => 1,
            ];
        }
        $this->buildTabs($tabs);
    }

    protected function buildTabs(array $tabs)
    {
        $obj = $this->dispatch(
            'responses/common/tabs',
            [
                'catalog/collections',
                ['tabs' => $tabs],
            ]
        );
        $this->data['tabs'] = $obj->dispatchGetOutput();
    }

    protected function validate(array $data)
    {
        $this->loadModel('catalog/collection');

        if (isset($data['name'])) {
            if (strlen(trim($data['name'])) === 0 || strlen(trim($data['name'])) > 254) {
                $this->error['name'] = $this->language->get('save_error_name');
            }
        }

        $error_text = $this->html->isSEOkeywordExists(
            'collection_id=' . (int) $this->request->get['id'],
            $this->request->post['keyword']
        );
        if ($error_text) {
            $this->error['warning'] = $error_text;
            $this->error['keyword'] = $this->language->get('save_error_unique_keyword');
        }

        $this->extensions->hk_ValidateData($this, $data);
        return (!$this->error);
    }

    public function edit_layout()
    {
        $collection = [];
        $page_controller = 'pages/product/collection';
        $page_key_param = 'collection_id';
        $collectionId = (int) $this->request->get['id'];
        $this->data['collection_id'] = $collectionId;
        $this->data['id'] = $collectionId;
        $page_url = $this->html->getSecureURL('catalog/collections/edit_layout', '&id=' . $collectionId);
        if (!$collectionId) {
            redirect($this->html->getSecureURL('catalog/collections'));
        }

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/collections');
        $this->loadLanguage('design/layout');
        $this->data['help_url'] = $this->gen_help_url('layout_edit');

        if (has_value($collectionId) && $this->request->is_GET()) {
            /** @var ModelCatalogCollection $mdl */
            $mdl = $this->loadModel('catalog/collection');
            $collection = $mdl->getById($collectionId);
        }

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
            . $this->language->get('text_collection')
            . ' - '
            . $collection['name'];

        $this->document->setTitle($this->data['heading_title']);
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
                'href'      => $this->html->getSecureURL('catalog/collections'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/collections/update', '&id=' . $collectionId),
                'text'      => $this->data['heading_title'],
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $page_url,
                'text'      => $this->language->get('tab_layout'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->data['active'] = 'layout';

        $tmpl_id = $this->request->get['tmpl_id'] ? : $this->config->get('config_storefront_template');
        $layout = new ALayoutManager($tmpl_id);
        //get existing page layout or generic
        $page_layout = $layout->getPageLayoutIDs($page_controller, $page_key_param, $collectionId);
        $page_id = $page_layout['page_id'];
        $layout_id = $page_layout['layout_id'];
        $params = [
            'id'        => $collectionId,
            'page_id'   => $page_id,
            'layout_id' => $layout_id,
            'tmpl_id'   => $tmpl_id,
        ];
        $url = '&' . $this->html->buildURI($params);

        // get templates
        $this->data['templates'] = [];
        $directories = glob(DIR_STOREFRONT . 'view' . DS . '*', GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $this->data['templates'][] = basename($directory);
        }
        $enabled_templates = $this->extensions->getExtensionsList(
            [
                'filter' => 'template',
                'status' => 1,
            ]
        );
        foreach ($enabled_templates->rows as $template) {
            $this->data['templates'][] = $template['key'];
        }

        $action = $this->html->getSecureURL('catalog/collections/save_layout');
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
        $this->data['current_url'] = $this->html->getSecureURL('catalog/collection/edit_layout', $url);

        // insert external form of layout
        $layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);

        $layout_form = $this->dispatch('common/page_layout', [$layout]);
        $this->data['block_layout_form'] = $layout_form->dispatchGetOutput();

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

        $tabs['general'] = [
            'name'       => 'general',
            'text'       => $this->language->get('tab_general'),
            'href'       => $this->html->getSecureURL('catalog/collections/update', '&id=' . $collectionId),
            'active'     => false,
            'sort_order' => 0,
        ];
        if ($collectionId) {
            $tabs['design'] = [
                'name'       => 'design',
                'text'       => $this->language->get('tab_design'),
                'href'       => $this->html->getSecureURL('catalog/collections/edit_layout', '&id=' . $collectionId),
                'active'     => true,
                'sort_order' => 1,
            ];
        }
        $this->buildTabs($tabs);

        $this->view->batchAssign($this->data);

        $this->processTemplate('pages/catalog/collection_layout.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function save_layout()
    {
        if ($this->request->is_GET() || !$this->request->post) {
            redirect($this->html->getSecureURL('catalog/collections'));
        }
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $post = $this->request->post;
        $post['tmpl_id'] = preformatTextID($post['tmpl_id']);
        $pageData = [
            'controller' => 'pages/product/collection',
            'key_param'  => 'collection_id',
            'key_value'  => (int) $post['id'],
        ];

        $this->loadLanguage('catalog/collections');

        if (!$pageData['key_value']) {
            unset($this->session->data['success']);
            redirect($this->html->getSecureURL('catalog/product/update'));
        }

        /** @var ModelCatalogCollection $mdl */
        $mdl = $this->loadModel('catalog/collection');
        $collectionInfo = $mdl->getById((int) $pageData['key_value']);
        if ($collectionInfo) {
            $post['layout_name'] = $this->language->get('text_collection') . ': ' . $collectionInfo['name'];
            $pageData['page_descriptions'] = [$this->language->getContentLanguageID() => $collectionInfo];
        }

        if (saveOrCreateLayout($post['tmpl_id'], $pageData, $post)) {
            $this->session->data['success'] = $this->language->get('text_success_layout');
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        redirect(
            $this->html->getSecureURL(
                'catalog/collections/edit_layout',
                '&id=' . $pageData['key_value'] . '&tmpl_id=' . $post['tmpl_id']
            )
        );
    }
}
