<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

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

class ControllerResponsesListingGridCategory extends AController
{
    public $data = array();

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/category');
        $this->loadModel('catalog/category');
        $this->loadModel('catalog/product');
        $this->loadModel('tool/image');

        //Prepare filter config
        $grid_filter_params = array_merge(array('name'), (array)$this->data['grid_filter_params']);
        $filter = new AFilter(array('method' => 'post', 'grid_filter_params' => $grid_filter_params));
        $filter_data = $filter->getFilterData();
        //Add custom params
        //set parent to null to make search work by all category tree

        $filter_data['parent_id'] = !isset($this->request->get['parent_id']) ? 0 : $this->request->get['parent_id'];
        //NOTE: search by all categories when parent_id not set or zero (top level)

        if ($filter_data['subsql_filter']) {
            $filter_data['parent_id'] = ($filter_data['parent_id'] == 'null' || $filter_data['parent_id'] < 1) ? null : $filter_data['parent_id'];
        }
        if ($filter_data['parent_id'] === null || $filter_data['parent_id'] === 'null') {
            unset($filter_data['parent_id']);
        }
        $new_level = 0;
        //get all leave categories 
        $leaf_nodes = $this->model_catalog_category->getLeafCategories();
        if ($this->request->post['nodeid']) {
            $sort = $filter_data['sort'];
            $order = $filter_data['order'];
            //reset filter to get only parent category
            $filter_data = array();
            $filter_data['sort'] = $sort;
            $filter_data['order'] = $order;
            $filter_data['parent_id'] = (integer)$this->request->post['nodeid'];
            $new_level = (integer)$this->request->post["n_level"] + 1;
        }

        $total = $this->model_catalog_category->getTotalCategories($filter_data);
        $response = new stdClass();
        $response->page = $filter->getParam('page');
        $response->total = $filter->calcTotalPages($total);
        $response->records = $total;
        $response->userdata = new stdClass();

        $results = $this->model_catalog_category->getCategoriesData($filter_data);

        //build thumbnails list
        $category_ids = array();
        foreach ($results as $category) {
            $category_ids[] = $category['category_id'];
        }
        $resource = new AResource('image');

        $thumbnails = $resource->getMainThumbList(
            'categories',
            $category_ids,
            $this->config->get('config_image_grid_width'),
            $this->config->get('config_image_grid_height')
        );

        $i = 0;
        $language_id = $this->language->getContentLanguageID();
        $title = $this->language->get('text_view').' '.$this->language->get('tab_product');

        foreach ($results as $result) {
            $thumbnail = $thumbnails[$result['category_id']];
            $response->rows[$i]['id'] = $result['category_id'];
            $cnt = $this->model_catalog_category->getCategoriesData(array('parent_id' => $result['category_id']), 'total_only');

            if (!$result['products_count']) {
                $products_count = 0;
            } else {
                $products_count = (string)$this->html->buildElement(array(
                    'type'  => 'button',
                    'name'  => 'view products',
                    'text'  => $result['products_count'],
                    'href'  => $this->html->getSecureURL('catalog/product', '&category='.$result['category_id']),
                    'title' => $title,
                ));
            }

            //tree grid structure
            if ($this->config->get('config_show_tree_data')) {
                $name_label = '<label class="grid-parent-category" >'.$result['basename'].'</label>';
            } else {
                $name_label = '<label class="grid-parent-category">'.(str_replace($result['basename'], '', $result['name'])).'</label>'
                    .$this->html->buildInput(array(
                        'name'  => 'category_description['.$result['category_id'].']['.$language_id.'][name]',
                        'value' => $result['basename'],
                        'attr'  => ' maxlength="32" ',
                    ));
            }

            $response->rows[$i]['cell'] = array(
                $thumbnail['thumb_html'],
                $name_label,
                $this->html->buildInput(array(
                    'name'  => 'sort_order['.$result['category_id'].']',
                    'value' => $result['sort_order'],
                )),
                $this->html->buildCheckbox(array(
                    'name'  => 'status['.$result['category_id'].']',
                    'value' => $result['status'],
                    'style' => 'btn_switch',
                )),
                $products_count,
                $cnt
                .($cnt ?
                    '&nbsp;<a class="btn_action btn_grid grid_action_expand" href="#" rel="parent_id='.$result['category_id'].'" title="'.$this->language->get('text_view').'">'.
                    '<i class="fa fa-folder-open"></i></a>'
                    : ''),
                'action',
                $new_level,
                ($filter_data['parent_id'] ? $filter_data['parent_id'] : null),
                ($result['category_id'] == $leaf_nodes[$result['category_id']] ? true : false),
                false,
            );
            $i++;
        }
        $this->data['response'] = $response;
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['response']));
    }

    public function update()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('catalog/product');
        $this->loadModel('catalog/category');
        $this->loadLanguage('catalog/category');
        if (!$this->user->canModify('listing_grid/category')) {
            $error = new AError('');
            return $error->toJSONResponse('NO_PERMISSIONS_402',
                array(
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/category'),
                    'reset_value' => true,
                ));
        }

        switch ($this->request->post['oper']) {
            case 'del':
                $ids = explode(',', $this->request->post['id']);
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $this->model_catalog_category->deleteCategory($id);
                    }
                }
                break;
            case 'save':
                $allowedFields = array_merge(array('category_description', 'sort_order', 'status'), (array)$this->data['allowed_fields']);

                $ids = explode(',', $this->request->post['id']);
                if (!empty($ids)) {
                    //resort required. 
                    if ($this->request->post['resort'] == 'yes') {
                        //get only ids we need
                        $array = array();
                        foreach ($ids as $id) {
                            $array[$id] = $this->request->post['sort_order'][$id];
                        }
                        $new_sort = build_sort_order($ids, min($array), max($array), $this->request->post['sort_direction']);
                        $this->request->post['sort_order'] = $new_sort;
                    }
                    foreach ($ids as $id) {
                        foreach ($allowedFields as $field) {
                            $this->model_catalog_category->editCategory($id, array($field => $this->request->post[$field][$id]));
                        }
                    }
                }
                break;
            default:

        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * update only one field
     *
     * @return void
     */
    public function update_field()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/category');
        if (!$this->user->canModify('listing_grid/category')) {
            $error = new AError('');
            return $error->toJSONResponse('NO_PERMISSIONS_402',
                array(
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/category'),
                    'reset_value' => true,
                ));
        }

        $this->loadModel('catalog/category');
        $category_id = $this->request->get['id'];
        if (isset($category_id)) {
            //request sent from edit form. ID in url
            foreach ($this->request->post as $field => $value) {
                if ($field == 'keyword') {
                    if ($err = $this->html->isSEOkeywordExists('category_id='.$category_id, $value)) {
                        $error = new AError('');
                        return $error->toJSONResponse('VALIDATION_ERROR_406', array('error_text' => $err));
                    }
                }

                $err = $this->_validateField($category_id, $field, $value);
                if (!empty($err)) {
                    $error = new AError('');
                    return $error->toJSONResponse('VALIDATION_ERROR_406', array('error_text' => $err));
                }

                $this->model_catalog_category->editCategory($category_id, array($field => $value));
            }
            return null;
        }
        $language_id = $this->language->getContentLanguageID();
        //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $field => $value) {
            foreach ($value as $k => $v) {
                if ($field == 'category_description') {
                    if (mb_strlen($v[$language_id]['name']) < 2 || mb_strlen($v[$language_id]['name']) > 32) {
                        $err = $this->language->get('error_name');
                        $error = new AError('');
                        return $error->toJSONResponse('VALIDATION_ERROR_406', array('error_text' => $err));
                    }
                }
                $this->model_catalog_category->editCategory($k, array($field => $v));
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function _validateField($category_id, $field, $value)
    {

        $err = '';
        switch ($field) {
            case 'category_description' :
                $language_id = $this->language->getContentLanguageID();

                if (isset($value[$language_id]['name'])
                    && (mb_strlen($value[$language_id]['name']) < 1 || mb_strlen($value[$language_id]['name']) > 255)
                ) {
                    $err = $this->language->get('error_name');
                }
                break;
            case 'model' :
                if (mb_strlen($value) > 64) {
                    $err = $this->language->get('error_model');
                }
                break;
            case 'keyword' :
                $err = $this->html->isSEOkeywordExists('category_id='.$category_id, $value);
                break;
        }
        return $err;
    }

    public function categories()
    {

        $output = array();
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadModel('catalog/category');
        if (isset($this->request->post['term'])) {
            $filter = array(
                'limit'         => 20,
                'language_id'   => $this->language->getContentLanguageID(),
                'subsql_filter' => "cd.name LIKE '%".$this->db->escape($this->request->post['term'], true)."%'
												OR cd.description LIKE '%".$this->db->escape($this->request->post['term'], true)."%'
												OR cd.meta_keywords LIKE '%".$this->db->escape($this->request->post['term'], true)."%'",
            );
            $results = $this->model_catalog_category->getCategoriesData($filter);
            //build thumbnails list
            $category_ids = array();
            foreach ($results as $category) {
                $category_ids[] = $category['category_id'];
            }
            $resource = new AResource('image');
            $thumbnails = $resource->getMainThumbList(
                'categories',
                $category_ids,
                $this->config->get('config_image_grid_width'),
                $this->config->get('config_image_grid_height')
            );
            foreach ($results as $item) {
                $thumbnail = $thumbnails[$item['category_id']];
                $output[] = array(
                    'image'      => $icon = $thumbnail['thumb_html'] ? $thumbnail['thumb_html'] : '<i class="fa fa-code fa-4x"></i>&nbsp;',
                    'id'         => $item['category_id'],
                    'name'       => $item['name'],
                    'meta'       => '',
                    'sort_order' => (int)$item['sort_order'],
                );
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($output));
    }

}
