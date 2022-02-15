<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesCatalogProduct extends AController
{
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->view->assign('error_warning', $this->session->data['warning']);
        if (isset($this->session->data['warning'])) {
            unset($this->session->data['warning']);
        }
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
                'href'      => $this->html->getSecureURL('catalog/product'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->loadModel('catalog/category');
        $this->data['categories'] = ['' => $this->language->get('text_select_category')];
        $results = $this->model_catalog_category->getCategories(
            0,
            $this->session->data['current_store_id']
        );
        foreach ($results as $r) {
            $this->data['categories'][$r['category_id']] = $r['name'];
        }

        $grid_settings = [
            'table_id'     => 'product_grid',
            'url'          => $this->html->getSecureURL(
                'listing_grid/product',
                '&category='.(int) $this->request->get['category']
            ),
            'editurl'      => $this->html->getSecureURL('listing_grid/product/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/product/update_field'),
            //NOTE: set default sorting by date_modified, but column not present in the grid
            'sortname'     => 'date_modified',
            'sortorder'    => 'desc',
            'actions'      => [
                'edit'     => [
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('catalog/product/update', '&product_id=%ID%'),
                ],
                'save'     => [
                    'text' => $this->language->get('button_save'),
                ],
                'delete'   => [
                    'text' => $this->language->get('button_delete'),
                ],
                'clone'    => [
                    'text' => $this->language->get('text_clone'),
                    'href' => $this->html->getSecureURL('catalog/product/copy', '&product_id=%ID%'),
                ],
                'dropdown' => [
                    'text'     => $this->language->get('text_choose_action'),
                    'href'     => $this->html->getSecureURL('catalog/product/update', '&product_id=%ID%'),
                    'children' => array_merge(
                        [
                            'quickview'  => [
                                'text'  => $this->language->get(
                                    'text_quick_view'
                                ),
                                'href'  => $this->html->getSecureURL(
                                    'catalog/product/update',
                                    '&product_id=%ID%'
                                ),
                                //quick view port URL
                                'vhref' => $this->html->getSecureURL(
                                    'r/common/viewport/modal',
                                    '&viewport_rt=catalog/product/update&product_id=%ID%'
                                ),
                            ],
                            'general'    => [
                                'text' => $this->language->get(
                                    'tab_general'
                                ),
                                'href' => $this->html->getSecureURL(
                                    'catalog/product/update',
                                    '&product_id=%ID%'
                                ),
                            ],
                            'media'      => [
                                'text' => $this->language->get(
                                    'tab_media'
                                ),
                                'href' => $this->html->getSecureURL(
                                    'catalog/product_images',
                                    '&product_id=%ID%'
                                ),
                            ],
                            'options'    => [
                                'text' => $this->language->get(
                                    'tab_option'
                                ),
                                'href' => $this->html->getSecureURL(
                                    'catalog/product_options',
                                    '&product_id=%ID%'
                                ),
                            ],
                            'files'      => [
                                'text' => $this->language->get(
                                    'tab_files'
                                ),
                                'href' => $this->html->getSecureURL(
                                    'catalog/product_files',
                                    '&product_id=%ID%'
                                ),
                            ],
                            'relations'  => [
                                'text' => $this->language->get(
                                    'tab_relations'
                                ),
                                'href' => $this->html->getSecureURL(
                                    'catalog/product_relations',
                                    '&product_id=%ID%'
                                ),
                            ],
                            'promotions' => [
                                'text' => $this->language->get(
                                    'tab_promotions'
                                ),
                                'href' => $this->html->getSecureURL(
                                    'catalog/product_promotions',
                                    '&product_id=%ID%'
                                ),
                            ],
                            'layout'     => [
                                'text' => $this->language->get(
                                    'tab_layout'
                                ),
                                'href' => $this->html->getSecureURL(
                                    'catalog/product_layout',
                                    '&product_id=%ID%'
                                ),
                            ],

                        ],
                        (array) $this->data['grid_edit_expand']
                    ),
                ],

            ],
        ];

        $grid_settings['colNames'] = [
            '',
            $this->language->get('column_name'),
            $this->language->get('column_model'),
            $this->language->get('column_price'),
            $this->language->get('column_quantity'),
            $this->language->get('column_status'),
        ];
        $grid_settings['colModel'] = [
            [
                'name'     => 'image',
                'index'    => 'image',
                'align'    => 'center',
                'width'    => 65,
                'sortable' => false,
                'search'   => false,
            ],
            [
                'name'  => 'name',
                'index' => 'name',
                'align' => 'center',
                'width' => 200,
            ],
            [
                'name'  => 'model',
                'index' => 'model',
                'align' => 'center',
                'width' => 120,
            ],
            [
                'name'   => 'price',
                'index'  => 'price',
                'align'  => 'center',
                'width'  => 90,
                'search' => false,
            ],
            [
                'name'   => 'quantity',
                'index'  => 'quantity',
                'align'  => 'center',
                'width'  => 90,
                'search' => false,
            ],
            [
                'name'   => 'status',
                'index'  => 'status',
                'align'  => 'center',
                'width'  => 130,
                'search' => false,
            ],
        ];

        $form = new AForm();
        $form->setForm(
            [
                'form_name' => 'product_grid_search',
            ]
        );

        //get search filter from cookie if required
        $search_params = [];
        if ($this->request->get['saved_list']) {
            $grid_search_form = json_decode(html_entity_decode($this->request->cookie['grid_search_form']));
            if ($grid_search_form->table_id == $grid_settings['table_id']) {
                parse_str($grid_search_form->params, $search_params);
            }
        }

        $grid_search_form = [];
        $grid_search_form['id'] = 'product_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'product_grid_search',
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

        $grid_search_form['fields']['keyword'] = $form->getFieldHtml(
            [
                'type'        => 'input',
                'name'        => 'keyword',
                'value'       => $search_params['keyword'],
                'placeholder' => $this->language->get('filter_product'),
            ]
        );
        $grid_search_form['fields']['match'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'match',
                'value'   => $search_params['match'],
                'options' => [
                    'any'   => $this->language->get('filter_any_word'),
                    'all'   => $this->language->get('filter_all_words'),
                    'exact' => $this->language->get('filter_exact_match'),
                ],
            ]
        );
        $grid_search_form['fields']['pfrom'] = $form->getFieldHtml(
            [
                'type'        => 'input',
                'name'        => 'pfrom',
                'value'       => $search_params['pfrom'],
                'placeholder' => '0',
                'style'       => 'small-field',
            ]
        );
        $grid_search_form['fields']['pto'] = $form->getFieldHtml(
            [
                'type'        => 'input',
                'name'        => 'pto',
                'value'       => $search_params['pto'],
                'placeholder' => $this->language->get('filter_price_max'),
                'style'       => 'small-field',
            ]
        );

        if ($this->request->get['category']) {
            $search_params['category'] = $this->request->get['category'];
        }

        $grid_search_form['fields']['category'] = $form->getFieldHtml(
            [
                'type'        => 'selectbox',
                'name'        => 'category',
                'options'     => $this->data['categories'],
                'style'       => 'chosen',
                'value'       => $search_params['category'],
                'placeholder' => $this->language->get('text_select_category'),
            ]
        );
        $grid_search_form['fields']['status'] = $form->getFieldHtml(
            [
                'type'        => 'selectbox',
                'name'        => 'status',
                'value'       => $search_params['status'],
                'placeholder' => $this->language->get('text_select_status'),
                'options'     => [
                    1 => $this->language->get('text_enabled'),
                    0 => $this->language->get('text_disabled'),
                ],
            ]
        );

        $grid_settings['search_form'] = true;

        $grid_settings['multiaction_options'] = [
            'delete'            => $this->language->get('text_delete_selected'),
            'save'              => $this->language->get('text_save_selected'),
            'enable'            => $this->language->get('text_enable_selected'),
            'disable'           => $this->language->get('text_disable_selected'),
            'relate'            => $this->language->get('text_set_related'),
            'create_collection' => $this->language->get('text_create_collection'),
        ];

        $this->view->assign('relate_selected_url', $grid_settings['editurl']);
        $this->view->assign('create_collection_url', $this->html->getSecureURL('catalog/collections/insert'));
        $this->view->assign('text_success_relation_set', $this->language->get('text_success_relation_set'));

        $grid = $this->dispatch('common/listing_grid', [$grid_settings]);
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('search_form', $grid_search_form);
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

        $this->view->assign('insert', $this->html->getSecureURL('catalog/product/insert'));
        $this->view->assign('help_url', $this->gen_help_url('product_listing'));
        $this->processTemplate('pages/catalog/product_list.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function insert()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if ($this->request->is_POST() && $this->_validateForm()) {
            $product_data = $this->_prepareData($this->request->post);
            $product_id = $this->model_catalog_product->addProduct($product_data);
            $this->data['product_id'] = $product_id;
            $this->model_catalog_product->updateProductLinks($product_id, $product_data);
            $this->extensions->hk_ProcessData($this, 'product_insert');
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('catalog/product/update', '&product_id='.$product_id));
        }
        $this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update(...$args)
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->view->assign('error_warning', $this->session->data['warning']);
        if (isset($this->session->data['warning'])) {
            unset($this->session->data['warning']);
        }
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        if ($this->request->is_POST() && $this->_validateForm()) {
            $product_data = $this->_prepareData($this->request->post);
            $product_id = $this->data['product_id'] = (int) $this->request->get['product_id'];
            $this->model_catalog_product->updateProduct($product_id, $product_data);
            $this->model_catalog_product->updateProductLinks($product_id, $product_data);
            $this->extensions->hk_ProcessData($this, 'product_update');
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('catalog/product/update', '&product_id='.$product_id));
        }
        $this->_getForm($args);
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function copy()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));
        if (isset($this->request->get['product_id']) && $this->_validateCopy()) {
            $this->data['new_product'] = $this->model_catalog_product->copyProduct($this->request->get['product_id']);
            $this->extensions->hk_ProcessData($this, 'product_copy');
            if ($this->data['new_product']) {
                $this->session->data['success'] = sprintf(
                    $this->language->get('text_success_copy'),
                    $this->data['new_product']['name']
                );

                if ($this->data['new_product']['layout_clone']) {
                    $this->session->data['success'] .= ' '.$this->language->get('text_success_copy_layout');
                }
                redirect(
                    $this->html->getSecureURL(
                        'catalog/product/update',
                        '&product_id='.$this->data['new_product']['id']
                    )
                );
            } else {
                $this->session->data['success'] = $this->language->get('text_error_copy');
                redirect($this->html->getSecureURL('catalog/product'));
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function _getForm($args = [])
    {
        $product_id = null;
        $product_info = [];
        $viewport_mode = $args[0]['viewport_mode'] ?? '';
        $content_language_id = $this->language->getContentLanguageID();
        if (isset($this->request->get['product_id'])) {
            $product_id = $this->request->get['product_id'];
            $product_info = $this->model_catalog_product->getProduct($product_id);
            $product_info['featured'] = $product_info['featured'] ? 1 : 0;
            $product_info['has_track_options'] = $this->model_catalog_product->hasTrackOptions($product_id);
            $product_info['stock_locations'] = $this->model_catalog_product->getProductStockLocations($product_id);
            if ($product_info['has_track_options']) {
                $product_info['quantity'] = $this->model_catalog_product->hasAnyStock($product_id);
            }
            $this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($product_id);
            $title = $this->language->get('text_edit')
                .'&nbsp;'
                .$this->language->get('text_product')
                .' - '
                .$this->data['product_description'][$content_language_id]['name'];
        } else {
            $title = $this->language->get('text_insert');
        }
        $this->document->setTitle($title);

        $this->data['error'] = $this->error;
        $this->data['cancel'] = $this->html->getSecureURL('catalog/product');

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/product'),
                'text'      => $this->language->get('heading_title'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/product'),
                'text'      => $title,
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->loadModel('setting/store');
        $this->loadModel('catalog/category');
        $this->data['categories'] = [];

        $product_stores = array_column($this->model_setting_store->getStores(), 'store_id');
        $results = $this->model_catalog_category->getCategories(ROOT_CATEGORY_ID, $product_stores);
        foreach ($results as $r) {
            $name = $r['name'].(count($product_stores) > 1 ? ' ('.$r['store_name'].')' : '');
            $this->data['categories'][$r['category_id']] = $name;
        }

        $this->loadModel('setting/store');
        $this->data['stores'] = [0 => $this->language->get('text_default')];
        $results = $this->model_setting_store->getStores();
        foreach ($results as $r) {
            $this->data['stores'][$r['store_id']] = $r['name'];
        }

        $this->loadModel('catalog/manufacturer');
        $this->data['manufacturers'] = [0 => $this->language->get('text_none')];
        $results = $this->model_catalog_manufacturer->getManufacturers();
        foreach ($results as $r) {
            $this->data['manufacturers'][$r['manufacturer_id']] = $r['name'];
        }

        $this->loadModel('localisation/stock_status');
        $this->data['stock_statuses'] = ['0' => $this->language->get('text_none')];
        $results = $this->model_localisation_stock_status->getStockStatuses();
        foreach ($results as $r) {
            $this->data['stock_statuses'][$r['stock_status_id']] = $r['name'];
        }

        $this->loadModel('localisation/tax_class');
        $this->data['tax_classes'] = [0 => $this->language->get('text_none')];
        $results = $this->model_localisation_tax_class->getTaxClasses();
        foreach ($results as $r) {
            $this->data['tax_classes'][$r['tax_class_id']] = $r['title'];
        }

        $this->loadModel('localisation/weight_class');
        $this->data['weight_classes'] = [];
        $results = $this->model_localisation_weight_class->getWeightClasses();
        foreach ($results as $r) {
            $this->data['weight_classes'][$r['weight_class_id']] = $r['title'];
        }

        $this->loadModel('localisation/length_class');
        $this->data['length_classes'] = [];
        $results = $this->model_localisation_length_class->getLengthClasses();
        foreach ($results as $r) {
            $this->data['length_classes'][$r['length_class_id']] = $r['title'];
        }

        $fields = [
            'product_category',
            'featured',
            'product_store',
            'model',
            'call_to_order',
            'sku',
            'location',
            'keyword',
            'image',
            'manufacturer_id',
            'shipping',
            'ship_individually',
            'shipping_price',
            'free_shipping',
            'quantity',
            'minimum',
            'maximum',
            'subtract',
            'sort_order',
            'stock_status_id',
            'stock_checkout',
            'price',
            'cost',
            'status',
            'tax_class_id',
            'weight',
            'weight_class_id',
            'length',
            'width',
            'height',
        ];

        foreach ($fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data[$f] = $this->request->post [$f];
            } elseif (isset($product_info)) {
                $this->data[$f] = $product_info[$f];
            }
        }

        if (isset($this->request->post['product_category'])) {
            $this->data['product_category'] = $this->request->post['product_category'];
        } elseif (isset($product_info)) {
            $this->data['product_category'] = $this->model_catalog_product->getProductCategories($product_id);
        } else {
            $this->data['product_category'] = [];
        }

        if (isset($this->request->post['product_store'])) {
            $this->data['product_store'] = $this->request->post['product_store'];
        } elseif (isset($product_info)) {
            $this->data['product_store'] = $this->model_catalog_product->getProductStores($product_id);
        } else {
            $this->data['product_store'] = [0];
        }

        if (isset($this->request->post['product_description'])) {
            $this->data['product_description'] = $this->request->post['product_description'];
        } elseif (isset($product_info)) {
            $this->data['product_description'] = $this->model_catalog_product->getProductDescriptions(
                $product_id,
                $content_language_id
            );
        } else {
            $this->data['product_description'] = [];
        }

        if (isset($this->request->post['featured'])) {
            $this->data['featured'] = $this->request->post['featured'];
        } elseif (isset($product_info)) {
            $this->data['featured'] = $product_info['featured'];
        } else {
            $this->data['featured'] = 0;
        }

        if (isset($this->request->post['product_tags'])) {
            $this->data['product_tags'] = $this->request->post['product_tags'];
        } elseif (isset($product_info)) {
            $this->data['product_tags'] = $this->model_catalog_product->getProductTags(
                $product_id,
                $content_language_id
            );
        } else {
            $this->data['product_tags'] = '';
        }
        $this->loadModel('tool/image');
        if (isset($product_info) && $product_info['image'] && file_exists(DIR_IMAGE.$product_info['image'])) {
            $this->data['preview'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
        } else {
            $this->data['preview'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        }

        if (!isset($this->data['stock_status_id'])) {
            $this->data['stock_status_id'] = $this->config->get('config_stock_status_id');
        }
        if (isset($this->request->post['date_available'])) {
            $this->data['date_available'] = $this->request->post['date_available'];
        } elseif (isset($product_info)) {
            $this->data['date_available'] = $product_info['date_available'];
        } else {
            $this->data['date_available'] = dateInt2ISO(time() - 86400);
        }

        $weight_info = $this->model_localisation_weight_class->getWeightClassDescriptionByUnit(
            $this->config->get('config_weight_class')
        );
        if (isset($this->request->post['weight_class_id'])) {
            $this->data['weight_class_id'] = (int)$this->request->post['weight_class_id'];
        } elseif ($product_info) {
            $this->data['weight_class_id'] = (int)$product_info['weight_class_id'];
        } elseif (isset($weight_info)) {
            $this->data['weight_class_id'] = (int)$weight_info['weight_class_id'];
        } else {
            $this->data['weight_class_id'] = '';
        }

        $length_info = $this->model_localisation_length_class->getLengthClassDescriptionByUnit(
            $this->config->get('config_length_class')
        );

        if (isset($this->request->post['length_class_id'])) {
            $this->data['length_class_id'] = (int)$this->request->post['length_class_id'];
        } elseif ($product_info) {
            $this->data['length_class_id'] = (int)$product_info['length_class_id'];
        } elseif (isset($length_info)) {
            $this->data['length_class_id'] = (int)$length_info['length_class_id'];
        } else {
            $this->data['length_class_id'] = '';
        }

        if ($this->data['status'] === '') {
            $this->data['status'] = 1;
        }
        if ($this->data['quantity'] === '') {
            $this->data['quantity'] = 1;
        }
        if ($this->data['minimum'] === '') {
            $this->data['minimum'] = 1;
        }
        if ($this->data['sort_order'] === '') {
            $this->data['sort_order'] = 1;
        }

        $this->data['active'] = 'details';
        if (!isset($product_id)) {
            $this->data['action'] = $this->html->getSecureURL('catalog/product/insert');
            $this->data['form_title'] = $this->language->get('text_insert').$this->language->get('text_product');
            $this->data['update'] = '';
            $form = new AForm('ST');
            $this->data['summary_form'] = '';
        } else {
            $this->data['action'] = $this->html->getSecureURL('catalog/product/update', '&product_id='.$product_id);
            $this->data['form_title'] = $this->language->get('text_edit').'&nbsp;'.$this->language->get('text_product');
            $this->data['update'] = $this->html->getSecureURL('listing_grid/product/update_field', '&id='.$product_id);
            $form = new AForm('HS');

            $this->data['active'] = 'general';
            //load tabs controller
            $tabs_obj = $this->dispatch('pages/catalog/product_tabs', [$this->data]);
            $this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
            unset($tabs_obj);
            $this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');
        }

        $form->setForm(
            [
                'form_name' => 'productFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'productFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'productFrm',
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
                'value' => $this->data['status'],
                'style' => 'btn_switch btn-group-sm',
            ]
        );

        $this->data['form']['fields']['general']['featured'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'featured',
                'value' => $this->data['featured'],
                'style' => 'btn_switch btn-group-sm',
            ]
        );

        $this->data['form']['fields']['general']['name'] = $form->getFieldHtml(
            [
                'type'         => 'input',
                'name'         => 'product_description[name]',
                'value'        => $this->data['product_description']['name'],
                'required'     => true,
                'multilingual' => true,
            ]
        );

        $this->data['form']['fields']['general']['blurb'] = $form->getFieldHtml(
            [
                'type'         => 'textarea',
                'name'         => 'product_description[blurb]',
                'value'        => $this->data['product_description']['blurb'],
                'multilingual' => true,
            ]
        );

        if ($viewport_mode != 'modal') {
            $this->data['form']['fields']['general']['description'] = $form->getFieldHtml(
                [
                    'type'         => 'texteditor',
                    'name'         => 'product_description[description]',
                    'value'        => $this->data['product_description']['description'],
                    'multilingual' => true,
                ]
            );
        }

        $this->data['form']['fields']['general']['meta_keywords'] = $form->getFieldHtml(
            [
                'type'         => 'textarea',
                'name'         => 'product_description[meta_keywords]',
                'value'        => $this->data['product_description']['meta_keywords'],
                'multilingual' => true,
            ]
        );

        $this->data['form']['fields']['general']['meta_description'] = $form->getFieldHtml(
            [
                'type'         => 'textarea',
                'name'         => 'product_description[meta_description]',
                'value'        => $this->data['product_description']['meta_description'],
                'multilingual' => true,
            ]
        );

        $this->data['form']['fields']['general']['tags'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'product_tags',
                'value' => $this->data['product_tags'],
            ]
        );

        $this->data['form']['fields']['general']['category'] = $form->getFieldHtml(
            [
                'type'        => 'checkboxgroup',
                'name'        => 'product_category[]',
                'value'       => $this->data['product_category'],
                'options'     => $this->data['categories'],
                'style'       => 'chosen',
                'placeholder' => $this->language->get('text_select_category'),
            ]
        );

        $this->data['form']['fields']['general']['store'] = $form->getFieldHtml(
            [
                'type'        => 'checkboxgroup',
                'name'        => 'product_store[]',
                // if new product - take selected store from storeSwitcher
                //otherwise - take product data
                'value'       => $product_id
                    ? $this->data['product_store']
                    : ($this->data['product_store'] ? : [$this->config->get('current_store_id')]),
                'options'     => $this->data['stores'],
                'style'       => 'chosen',
                'placeholder' => $this->language->get('entry_store'),
            ]
        );

        $this->data['form']['fields']['data']['manufacturer'] = $form->getFieldHtml(
            [
                'type'        => 'selectbox',
                'name'        => 'manufacturer_id',
                'value'       => $this->data['manufacturer_id'],
                'options'     => $this->data['manufacturers'],
                'style'       => 'chosen',
                'placeholder' => $this->language->get('entry_manufacturer'),
            ]
        );

        $this->data['form']['fields']['data']['model'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'model',
                'value'    => $this->data['model'],
                'required' => false,
            ]
        );

        $this->data['form']['fields']['data']['call_to_order'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'call_to_order',
                'value' => $this->data['call_to_order'],
                'style' => 'btn_switch btn-group-sm',
            ]
        );

        $this->data['form']['fields']['data']['price'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'price',
                'value' => moneyDisplayFormat($this->data['price']),
                'style' => 'small-field',
            ]
        );
        $this->data['form']['fields']['data']['cost'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'cost',
                'value' => moneyDisplayFormat($this->data['cost']),
                'style' => 'small-field',
            ]
        );

        $this->data['form']['fields']['data']['tax_class'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'tax_class_id',
                'value'    => $this->data['tax_class_id'] ?? $this->config->get('config_tax_class_id'),
                'options'  => $this->data['tax_classes'],
                'help_url' => $this->gen_help_url('tax_class'),
                'style'    => 'medium-field',
            ]
        );
        $this->data['form']['fields']['data']['subtract'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'subtract',
                'value'    => $this->data['subtract'],
                'options'  => [
                    1 => $this->language->get('text_yes'),
                    0 => $this->language->get('text_no'),
                ],
                'help_url' => $this->gen_help_url('product_inventory'),
                'style'    => 'medium-field',
                'attr'     => 'reload_on_save="true" ',
                'disabled' => $product_info['has_track_options'],
            ]
        );

        $this->data['form']['fields']['data']['quantity'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'quantity',
                'value'    => (int) $this->data['quantity'],
                'style'    => 'col-xs-1 small-field',
                'help_url' => $this->gen_help_url('product_inventory'),
                'attr'     => ($product_info['has_track_options'] || $product_info['stock_locations']
                    ? 'disabled'
                    : ''),
            ]
        );

        if ($this->data['subtract'] && !$product_info['has_track_options']) {
            $dd = new ADispatcher('responses/product/product/stockLocations', [$product_info['product_id']]);
            $this->data['form']['fields']['data']['stock_locations'] = $dd->dispatchGetOutput(
                'responses/product/product/stockLocations'
            );
        }

        $this->data['form']['fields']['data']['minimum'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'minimum',
                'value' => (int) $this->data['minimum'],
                'style' => 'small-field',
            ]
        );

        $this->data['form']['fields']['data']['maximum'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'maximum',
                'value' => (int) $this->data['maximum'],
                'style' => 'small-field',
            ]
        );

        $this->data['form']['fields']['data']['stock_checkout'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'stock_checkout',
                'value'   => $this->data['stock_checkout'] ? : '',
                'options' => [
                    '' => $this->language->get('text_default'),
                    0  => $this->language->get('text_no'),
                    1  => $this->language->get('text_yes'),
                ],
                'style'   => 'small-field',
            ]
        );

        $this->data['form']['fields']['data']['stock_status'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'stock_status_id',
                'value'    => (int) ($this->data['stock_status_id'] ? : $this->config->get('config_stock_status_id')),
                'options'  => $this->data['stock_statuses'],
                'help_url' => $this->gen_help_url('product_inventory'),
                'style'    => 'small-field',
            ]
        );

        $this->data['form']['fields']['data']['sku'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'sku',
                'value' => $this->data['sku'],
            ]
        );

        $this->data['form']['fields']['data']['location'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'location',
                'value' => $this->data['location'],
            ]
        );
        //prepend button to generate keyword
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
            '&object_key_name=product_id&id='.$product_id
        );

        $this->data['form']['fields']['data']['keyword'] = $form->getFieldHtml(
            [
                'type'         => 'input',
                'name'         => 'keyword',
                'value'        => $this->data['keyword'],
                'help_url'     => $this->gen_help_url('seo_keyword'),
                'attr'         => ' gen-value="'.SEOEncode($this->data['product_description']['name']).'" ',
                'multilingual' => true,
            ]
        );
        $this->data['form']['fields']['data']['date_available'] = $form->getFieldHtml(
            [
                'type'       => 'date',
                'name'       => 'date_available',
                'value'      => dateISO2Display($this->data['date_available']),
                'default'    => dateNowDisplay(),
                'dateformat' => format4Datepicker($this->language->get('date_format_short')),
                'highlight'  => 'future',
                'style'      => 'small-field',
            ]
        );

        $this->data['form']['fields']['data']['sort_order'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'sort_order',
                'value' => $this->data['sort_order'],
                'style' => 'tiny-field',
            ]
        );

        $this->data['form']['fields']['data']['shipping'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'shipping',
                'style' => 'btn_switch btn-group-sm',
                'value' => $this->data['shipping'] ?? 1,
            ]
        );

        $this->data['form']['fields']['data']['free_shipping'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'free_shipping',
                'style' => 'btn_switch btn-group-sm',
                'value' => $this->data['free_shipping'] ?? 0,
            ]
        );

        $this->data['form']['fields']['data']['ship_individually'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'ship_individually',
                'style' => 'btn_switch btn-group-sm',
                'value' => $this->data['ship_individually'] ?? 0,
            ]
        );

        $this->data['form']['fields']['data']['shipping_price'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'shipping_price',
                'value' => moneyDisplayFormat($this->data['shipping_price']),
                'style' => 'tiny-field',
            ]
        );

        $this->data['form']['fields']['data']['length'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'length',
                'value' => $this->data['length'],
                'style' => 'tiny-field',
            ]
        );
        $this->data['form']['fields']['data']['width'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'width',
                'value' => $this->data['width'],
                'attr'  => ' autocomplete="false"',
                'style' => 'tiny-field',
            ]
        );
        $this->data['form']['fields']['data']['height'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'height',
                'value' => $this->data['height'],
                'attr'  => ' autocomplete="false"',
                'style' => 'tiny-field',
            ]
        );

        if ($product_id && !$this->data['length_class_id']) {
            $this->data['length_classes'][0] = $this->language->get('text_none');
        }

        $this->data['form']['fields']['data']['length_class'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'length_class_id',
                'value'   => $this->data['length_class_id'],
                'options' => $this->data['length_classes'],
                'style'   => 'small-field',
            ]
        );

        if ($product_id && $this->data['shipping']
            && (!(float) $this->data['weight']
                || !$this->data['weight_class_id'])
            && !(float) $this->data['shipping_price']
        ) {
            if (!$this->data['weight_class_id']) {
                $this->data['error']['weight_class'] = $this->language->get('error_weight_class');
            }
            if (!(float) $this->data['weight']) {
                $this->data['error']['weight'] = $this->language->get('error_weight_value');
            }
        }

        if ($product_id
            && (!$this->data['weight_class_id']
                || !$this->data['weight_classes'][$this->data['weight_class_id']])
        ) {
            $this->data['weight_classes'][0] = $this->language->get('text_none');
            $this->data['weight_class_id'] = 0;
        }

        $this->data['form']['fields']['data']['weight'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'weight',
                'value' => $this->data['weight'],
                'attr'  => ' autocomplete="false"',
                'style' => 'tiny-field',
            ]
        );

        $this->data['form']['fields']['data']['weight_class'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'weight_class_id',
                'value'   => $this->data['weight_class_id'],
                'options' => $this->data['weight_classes'],
                'style'   => 'small-field',
            ]
        );

        $this->data['product_id'] = $product_id;
        if ($product_id && $this->config->get('config_embed_status')) {
            $this->data['embed_url'] = $this->html->getSecureURL(
                'common/do_embed/product',
                '&product_id='.$product_id
            );
        }

        $this->data['text_clone'] = $this->language->get('text_clone');
        $this->data['clone_url'] = $this->html->getSecureURL(
            'catalog/product/copy',
            '&product_id='.$this->request->get['product_id']
        );
        $this->data['form_language_switch'] = $this->html->getContentLanguageSwitcher();
        $this->data['language_id'] = $content_language_id;
        $this->data['language_code'] = $this->session->data['language'];
        $this->data['help_url'] = $this->gen_help_url('product_edit');
        $saved_list_data = json_decode(html_entity_decode($this->request->cookie['grid_params']));
        if ($saved_list_data->table_id == 'product_grid') {
            $this->data['list_url'] = $this->html->getSecureURL('catalog/product', '&saved_list=product_grid');
        }

        if ($viewport_mode == 'modal') {
            $tpl = 'responses/viewport/modal/catalog/product_form.tpl';
        } else {
            $this->addChild(
                'responses/common/resource_library/get_resources_html', 'resources_html',
                'responses/common/resource_library_scripts.tpl'
            );
            $resources_scripts = $this->dispatch(
                'responses/common/resource_library/get_resources_scripts',
                [
                    'object_name' => '',
                    'object_id'   => '',
                    'types'       => ['image'],
                ]
            );
            $this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();
            $this->data['rl'] = $this->html->getSecureURL(
                'common/resource_library',
                '&action=list_library&object_name=&object_id&type=image&mode=single'
            );

            $tpl = 'pages/catalog/product_form.tpl';
        }

        $this->view->batchAssign($this->data);
        $this->processTemplate($tpl);
    }

    protected function _validateForm()
    {
        if (!$this->user->canModify('catalog/product')) {
            $this->error['warning'] = $this->language->get_error('error_permission');
        }
        $post = $this->request->post;
        $productId = $this->request->get['product_id'];
        $len = mb_strlen($post['product_description']['name']);
        if ($len < 1 || $len > 255) {
            $this->error['name'] = $this->language->get_error('error_name');
        }

        if (mb_strlen($post['model']) > 64) {
            $this->error['model'] = $this->language->get_error('error_model');
        }

        if (($error_text = $this->html->isSEOkeywordExists(
            'product_id='.$productId,
            $post['keyword']
        ))
        ) {
            $this->error['keyword'] = $error_text;
        }

        foreach (['length', 'width', 'height', 'weight'] as $name) {
            $v = abs(preformatFloat($post[$name], $this->language->get('decimal_point')));
            if ($v >= 1000) {
                $this->error[$name] = $this->language->get('error_measure_value');
            } elseif (
                $post['shipping']
                && !$v
                && ((float) $post['length'] + (float) $post['width'] + (float) $post['height'])
            ) {
                $this->error[$name] = $this->language->get('error_dimension_value');
            }
        }

        if (!$post['length_class_id'] && $post['shipping']) {
            $this->error['length_class'] = $this->language->get('error_length_class');
        }
        if (!$post['weight_class_id'] && $post['shipping']) {
            $this->error['weight_class'] = $this->language->get('error_weight_class');
        }

        $this->extensions->hk_ValidateData($this, [__FUNCTION__]);

        if (!$this->error) {
            return true;
        } else {
            if (!isset($this->error['warning'])) {
                $this->error['warning'] = $this->language->get_error('error_required_data');
            }
            return false;
        }
    }

    protected function _validateCopy()
    {
        if (!$this->user->canModify('catalog/product')) {
            $this->error['warning'] = $this->language->get_error('error_permission');
        }

        $this->extensions->hk_ValidateData($this, [__FUNCTION__]);

        return (!$this->error);
    }

    protected function _prepareData($data = [])
    {
        if (isset($data['date_available'])) {
            $data['date_available'] = dateDisplay2ISO($data['date_available']);
        }
        //set default store if not set
        $data['product_store'] = $data['product_store'] ?? [0];
        return $data;
    }
}
