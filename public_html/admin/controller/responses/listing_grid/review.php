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

class ControllerResponsesListingGridReview extends AController
{
    public $data = array();

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/review');
        $this->loadModel('catalog/review');
        $this->loadModel('tool/image');

        if (!isset($this->request->get['store_id'])) {
            $this->request->get['store_id'] = (int)$this->session->data['current_store_id'];
        }

        //Prepare filter config
        $filter_params = array_merge(array('product_id', 'status', 'store_id'), (array)$this->data['filter_params']);
        $grid_filter_params = array_merge(array('name', 'author'), (array)$this->data['grid_filter_params']);

        $filter_form = new AFilter(array('method' => 'get', 'filter_params' => $filter_params));
        $filter_grid = new AFilter(array('method' => 'post', 'grid_filter_params' => $grid_filter_params));

        $total = $this->model_catalog_review->getTotalReviews(
            array_merge($filter_form->getFilterData(), $filter_grid->getFilterData())
        );

        $response = new stdClass();
        $response->page = $filter_grid->getParam('page');
        $response->total = $filter_grid->calcTotalPages($total);
        $response->records = $total;

        $results = $this->model_catalog_review->getReviews(
            array_merge(
                $filter_form->getFilterData(),
                $filter_grid->getFilterData()
            )
        );

        $product_ids = array_column($results, 'product_id');
        $resource = new AResource('image');
        $thumbnails = $product_ids
            ? $resource->getMainThumbList(
                'products',
                $product_ids,
                $this->config->get('config_image_grid_width'),
                $this->config->get('config_image_grid_height')
            )
            : [];

        $i = 0;
        foreach ($results as $result) {
            $thumbnail = $thumbnails[$result['product_id']];
            $response->rows[$i]['id'] = $result['review_id'];
            $response->rows[$i]['cell'] = array(
                $thumbnail['thumb_html'],
                $result['name'],
                $result['author'],
                $result['rating'],
                $result['verified_purchase'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
                $this->html->buildCheckbox(array(
                    'name'  => 'status['.$result['review_id'].']',
                    'value' => $result['status'],
                    'style' => 'btn_switch',
                )),
                dateISO2Display($result['date_added'], $this->language->get('date_format_short')),
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

        if (!$this->user->canModify('listing_grid/review')) {
            $error = new AError('');
            return $error->toJSONResponse('NO_PERMISSIONS_402',
                array(
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/review'),
                    'reset_value' => true,
                ));
        }

        $this->loadModel('catalog/review');
        $this->loadLanguage('catalog/review');

        switch ($this->request->post['oper']) {
            case 'del':
                $ids = explode(',', $this->request->post['id']);
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $this->model_catalog_review->deleteReview($id);
                    }
                }
                break;
            case 'save':
                $ids = explode(',', $this->request->post['id']);
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $data = array('status' => $this->request->post['status'][$id],);
                        $this->model_catalog_review->editReview($id, $data);
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

        if (!$this->user->canModify('listing_grid/review')) {
            $error = new AError('');
            return $error->toJSONResponse('NO_PERMISSIONS_402',
                array(
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/review'),
                    'reset_value' => true,
                ));
        }

        $this->loadLanguage('catalog/review');
        $this->loadModel('catalog/review');
        $allowedFields = array_merge(array('status', 'author', 'product_id', 'text', 'rating', 'verified_purchase'), (array)$this->data['allowed_fields']);

        if (isset($this->request->get['id'])) {
            //request sent from edit form. ID in url
            foreach ($this->request->post as $key => $value) {
                if (!in_array($key, $allowedFields)) {
                    continue;
                }
                $data = array($key => $value);
                $this->model_catalog_review->editReview($this->request->get['id'], $data);
            }
            return null;
        }

        //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $key => $value) {
            if (!in_array($key, $allowedFields)) {
                continue;
            }
            foreach ($value as $k => $v) {
                $data = array($key => $v);
                $this->model_catalog_review->editReview($k, $data);
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

}
