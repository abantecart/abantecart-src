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

class ControllerResponsesListingGridBlocksGrid extends AController
{

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('design/blocks');

        $page = $this->request->post['page']; // get the requested page
        if ((int) $page < 0) {
            $page = 0;
        }
        $limit = $this->request->post['rows']; // get how many rows we want to have into the grid

        //process custom search form
        $grid_filter_params = array_merge(['block_txt_id', 'name'], (array) $this->data['grid_filter_params']);
        $custom_block_types = array_merge(['html_block', 'listing_block'], (array) $this->data['custom_block_types']);

        $filter_grid = new AFilter(['method' => 'post', 'grid_filter_params' => $grid_filter_params]);

        $layout = new ALayoutManager();
        $total = $layout->getBlocksList($filter_grid->getFilterData(), 'total_only');
        $blocks = $layout->getBlocksList($filter_grid->getFilterData());

        $tmp = [];
        // prepare block list (delete template duplicates)
        foreach ($blocks as $block) {
            // skip base custom blocks
            if (!$block['custom_block_id'] && in_array($block['block_txt_id'], $custom_block_types)) {
                continue;
            }
            $tmp[$block['block_id'].'_'.$block['custom_block_id']] = $block;
        }
        $blocks = $tmp;

        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $total;
        $response->userdata = new stdClass();

        $i = 0;
        foreach ($blocks as $result) {
            $response->rows[$i]['id'] = $result['custom_block_id']
                ? $result['block_id'].'_'.$result['custom_block_id']
                : $result['block_id'];
            $id = $response->rows[$i]['id'];

            if (!$result['custom_block_id']) {
                $response->userdata->classes[$id] = 'disable-edit disable-delete';
            }

            $response->rows[$i]['cell'] = [
                $result['custom_block_id'] ? $result['block_id'].'_'.$result['custom_block_id'] : $result['block_id'],
                $result['block_txt_id'],
                $result['block_name'],
                (isset($result['status']) ?
                    $this->html->buildCheckbox(
                        [
                            'name'  => 'status['.$id.']',
                            'value' => $result['status'],
                            'style' => 'btn_switch',
                            'attr'  => 'readonly="true"',
                        ]
                    )
                    : ''),
                $result['block_date_added'],
            ];
            $i++;
        }
        $this->data['response'] = $response;
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($this->data['response']));
    }

    public function update_field()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->user->canModify('listing_grid/blocks_grid')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/blocks_grid'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('design/blocks');

        $custom_block_id = (int) $this->request->get['custom_block_id'];
        $layout = new ALayoutManager();
        if ($this->request->is_POST()) {
            $tmp = [];
            if (isset($this->request->post['block_status'])) {
                $tmp['status'] = (int) $this->request->post['block_status'];
            }
            if (isset($this->request->post['block_name'])) {
                $tmp['name'] = $this->request->post['block_name'];
            }
            if (isset($this->request->post['block_title'])) {
                $tmp['title'] = $this->request->post['block_title'];
            }
            if (isset($this->request->post['block_description'])) {
                $tmp['description'] = $this->request->post['block_description'];
            }
            if (isset($this->request->post['block_content'])) {
                $tmp['content'] = $this->request->post['block_content'];
            }
            if (isset($this->request->post['block_wrapper'])) {
                $tmp['block_wrapper'] = $this->request->post['block_wrapper'];
            }
            if (isset($this->request->post['block_framed'])) {
                $tmp['block_framed'] = (int) $this->request->post['block_framed'];
            }
            if (isset($this->request->post['selected']) && is_array($this->request->post['selected'])) {
                //updating custom list of selected items
                $listing_manager = new AListingManager($custom_block_id);
                $listing_manager->deleteCustomListing($this->config->get('config_store_id'));
                $k = 0;
                foreach ($this->request->post['selected'] as $id) {
                    $listing_manager->saveCustomListItem(
                        [
                            'id'         => $id,
                            'sort_order' => (int) $k,
                            'store_id'   => $this->config->get('config_store_id'),
                        ]
                    );
                    $k++;
                }
            }

            $tmp['language_id'] = $this->language->getContentLanguageID();

            $layout->saveBlockDescription(
                (int) $this->request->post['block_id'],
                $custom_block_id,
                $tmp
            );

            if (isset($tmp['status'])) {
                $layout->editBlockStatus($tmp['status'], (int) $this->request->post['block_id'], $custom_block_id);
                $info = $layout->getBlockDescriptions($custom_block_id);
                if ($info[$tmp['language_id']]['status'] != $tmp['status']) {
                    $error = new AError('');
                    $error->toJSONResponse(
                        'NO_PERMISSIONS_406',
                        [
                            'error_text'  => $this->language->get('error_text_status'),
                            'reset_value' => true,
                        ]
                    );
                    return;
                }
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function getSubForm()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('design/blocks');
        $listing_datasource = $this->request->post_or_get('listing_datasource');
        //need to reset get variable for switch case
        $this->request->get['listing_datasource'] = $listing_datasource;

        $listing_manager = new AListingManager((int) $this->request->get ['custom_block_id']);
        $this->data['data_sources'] = $listing_manager->getListingDataSources();
        // if request for non-existent datasource
        if (!in_array($listing_datasource, array_keys($this->data['data_sources']))) {
            return null;
        }

        if (strpos($listing_datasource, 'custom_') !== false) {
            $this->getCustomListingSubForm();
        } elseif ($listing_datasource == 'media') {
            $this->getMediaListingSubForm();
        } elseif ($listing_datasource == 'collection') {
            $this->getCollectionListingSubForm();
        } elseif ($listing_datasource == '') {
            return null;
        } else {
            $this->getAutoListingSubForm();
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function getAutoListingSubForm()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('design/blocks');

        $custom_block_id = (int) $this->request->get['custom_block_id'];
        $lm = new ALayoutManager();
        $content = [];

        if (!$custom_block_id) {
            $form = new AForm ('ST');
        } else {
            $form = new AForm ('HS');
            $content = $lm->getBlockDescriptions($custom_block_id);
            $content = $content[$this->language->getContentLanguageID()]['content'];
            $content = unserialize($content);
        }
        $form->setForm(['form_name' => 'BlockFrm']);

        $view = new AView($this->registry, 0);
        $view->assign('entry_limit', $this->language->get('entry_limit'));
        $view->assign(
            'field_limit', $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'limit',
                'value'    => $content['limit'],
                'style'    => 'no-save',
                'help_url' => $this->gen_help_url('block_limit'),
            ]
        )
        );

        $this->data['response'] = $view->fetch('responses/design/block_auto_listing_subform.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->response->setOutput($this->data['response']);
    }

    public function getCollectionListingSubForm()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('design/blocks');
        $custom_block_id = (int) $this->request->get['custom_block_id'];
        $content = [];

        $lm = new ALayoutManager();
        if (!$custom_block_id) {
            $form = new AForm ('ST');
        } else {
            $form = new AForm ('HS');
            $content = $lm->getBlockDescriptions($custom_block_id);
            $content = $content[$this->language->getContentLanguageID()]['content'];
            $content = unserialize($content);
        }
        $form->setForm(['form_name' => 'BlockFrm']);

        $this->loadModel('catalog/collection');

        $collections = $this->model_catalog_collection->getCollections(
            [
                'store_id' => $this->config->get('store_id'),
                'status'   => 1,
            ]
        );

        $arCollections[''] = $this->language->get('text_select');
        foreach ($collections['items'] as $item) {
            $arCollections[$item['id']] = $item['name'];
        }
        $view = new AView($this->registry, 0);
        $view->batchAssign(
            [
                'entry_collection_resource_type'  => $this->language->get('entry_collection_resource_type'),
                'collection_resource_type'        => $form->getFieldHtml(
                    [
                        'type'    => 'selectbox',
                        'name'    => 'collection_id',
                        'value'   => $content['collection_id'],
                        'options' => $arCollections,
                        'style'   => 'no-save',
                    ]
                ),
                'entry_collection_resource_limit' => $this->language->get('entry_limit'),
                'collection_resource_limit'       => $form->getFieldHtml(
                    [
                        'type'     => 'input',
                        'name'     => 'limit',
                        'value'    => $content['limit'],
                        'style'    => 'no-save',
                        'help_url' => $this->gen_help_url('block_limit'),
                    ]
                ),

            ]
        );
        $this->data['response'] = $view->fetch('responses/design/block_collection_listing_subform.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->response->setOutput($this->data['response']);
    }

    public function getMediaListingSubForm()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('design/blocks');
        $custom_block_id = (int) $this->request->get['custom_block_id'];
        $content = [];

        $lm = new ALayoutManager();
        if (!$custom_block_id) {
            $form = new AForm ('ST');
        } else {
            $form = new AForm ('HS');
            $content = $lm->getBlockDescriptions($custom_block_id);
            $content = $content[$this->language->getContentLanguageID()]['content'];
            $content = unserialize($content);
        }
        $form->setForm(['form_name' => 'BlockFrm']);

        $rl = new AResourceManager();
        $types = $rl->getResourceTypes();
        $resource_types[''] = $this->language->get('text_select');
        foreach ($types as $type) {
            if ($type['type_name'] == 'download') {
                continue;
            }
            $resource_types[$type['type_name']] = $type['type_name'];
        }
        $view = new AView($this->registry, 0);
        $view->batchAssign(
            [
                'entry_media_resource_type'  => $this->language->get('entry_resource_type'),
                'media_resource_type'        => $form->getFieldHtml(
                    [
                        'type'     => 'selectbox',
                        'name'     => 'resource_type',
                        'value'    => (string) $content['resource_type'],
                        'options'  => $resource_types,
                        'style'    => 'no-save',
                        'help_url' => $this->gen_help_url('block_resource_type'),
                    ]
                ),
                'entry_media_resource_limit' => $this->language->get('entry_limit'),
                'media_resource_limit'       => $form->getFieldHtml(
                    [
                        'type'     => 'input',
                        'name'     => 'limit',
                        'value'    => $content['limit'],
                        'style'    => 'no-save',
                        'help_url' => $this->gen_help_url('block_limit'),
                    ]
                ),

            ]
        );
        $this->data['response'] = $view->fetch('responses/design/block_media_listing_subform.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->response->setOutput($this->data['response']);
    }

    public function getCustomListingSubForm()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->load->library('json');
        $lm = new ALayoutManager();
        $form_name = $this->request->get['form_name'] ?? 'BlockFrm';
        $custom_block_id = (int) $this->request->get ['custom_block_id'];
        $listing_datasource = $this->request->post_or_get('listing_datasource');
        $rl_object_name = $id_name = $ajax_url = '';
        $results = $ids = $options_list = [];

        // need to get data of custom listing
        if ($custom_block_id) {
            $content = $lm->getBlockDescriptions($custom_block_id);
            $content = $content[$this->language->getContentLanguageID()]['content'];
            $content = unserialize($content);

            if ($content['listing_datasource'] == $listing_datasource) {
                $lm = new AListingManager($custom_block_id);
                $list = $lm->getCustomList($this->config->get('config_store_id'));

                if ($list) {
                    foreach ($list as $row) {
                        $options_list[(int) $row['id']] = [];
                    }
                    $ids = array_keys($options_list);
                    switch ($listing_datasource) {
                        case 'custom_products':
                            $this->loadModel('catalog/product');
                            if ($ids) {
                                $filter = ['subsql_filter' => 'p.product_id in ('.implode(',', $ids).')'];
                                $results = $this->model_catalog_product->getProducts($filter);
                            } else {
                                $results = [];
                            }
                            $id_name = 'product_id';
                            $rl_object_name = 'products';
                            break;
                        case 'custom_categories':
                            $this->loadModel('catalog/category');
                            $filter = ['subsql_filter' => 'c.category_id in ('.implode(',', $ids).')'];
                            $results = $this->model_catalog_category->getCategoriesData($filter);

                            $id_name = 'category_id';
                            $rl_object_name = 'categories';
                            break;
                        case 'custom_manufacturers':
                            $this->loadModel('catalog/manufacturer');
                            $filter = ['subsql_filter' => 'm.manufacturer_id in ('.implode(',', $ids).')'];
                            $results = $this->model_catalog_manufacturer->getManufacturers($filter);

                            $id_name = 'manufacturer_id';
                            $rl_object_name = 'manufacturers';
                            break;
                    }

                    //get thumbnails by one pass
                    $resource = new AResource('image');
                    $thumbnails = $ids
                        ? $resource->getMainThumbList(
                                $rl_object_name,
                                $ids,
                                $this->config->get('config_image_grid_width'),
                                $this->config->get('config_image_grid_height'),
                                false
                            )
                        : [];

                    foreach ($results as $item) {
                        $id = $item[$id_name];
                        if (in_array($id, $ids)) {
                            $thumbnail = $thumbnails[$id];
                            $icon = $thumbnail['thumb_html'] ? : '<i class="fa fa-code fa-4x"></i>&nbsp;';
                            $options_list[$id] = [
                                'image'      => $icon,
                                'id'         => $id,
                                'name'       => $item['name'],
                                'meta'       => $item['model'],
                                'sort_order' => (int) $item['sort_order'],
                            ];
                        }
                    }
                }
            }
        }

        switch ($listing_datasource) {
            case 'custom_products':
                $ajax_url = $this->html->getSecureURL('r/product/product/products');
                break;
            case 'custom_categories':
                $ajax_url = $this->html->getSecureURL('r/listing_grid/category/categories');
                break;
            case 'custom_manufacturers':
                $ajax_url = $this->html->getSecureURL('r/listing_grid/manufacturer/manufacturers');
                break;
        }

        $form = new AForm ('ST');
        $form->setForm(['form_name' => $form_name]);

        $multivalue_html = $form->getFieldHtml(
            [
                'type'        => 'multiselectbox',
                'name'        => 'selected[]',
                'value'       => $ids,
                'options'     => $options_list,
                'style'       => 'chosen',
                'ajax_url'    => $ajax_url,
                'placeholder' => $this->language->get('text_select_from_lookup'),
            ]
        );

        $this->view->assign('multivalue_html', $multivalue_html);
        $this->view->assign('form_name', $form_name);

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->processTemplate('responses/design/block_custom_listing_subform.tpl');
    }
}
