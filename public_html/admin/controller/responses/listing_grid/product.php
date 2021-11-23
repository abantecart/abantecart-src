<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

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

class ControllerResponsesListingGridProduct extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('catalog/product');
        $this->loadModel('catalog/product');
        $this->loadModel('tool/image');

        //Clean up parameters if needed
        if (isset($this->request->get['keyword'])
            && $this->request->get['keyword'] == $this->language->get('filter_product')
        ) {
            unset($this->request->get['keyword']);
        }
        if (isset($this->request->get['pfrom'])
            && $this->request->get['pfrom'] == 0
        ) {
            unset($this->request->get['pfrom']);
        }
        if (isset($this->request->get['pto'])
            && $this->request->get['pto'] == $this->language->get('filter_price_max')
        ) {
            unset($this->request->get['pto']);
        }

        //Prepare filter config
        $filter_params = [
            'category',
            'status',
            'keyword',
            'match',
            'pfrom',
            'pto',
        ];

        $grid_filter_params = ['name', 'sort_order', 'model'];

        $filter_form = new AFilter(['method' => 'get', 'filter_params' => $filter_params]);
        $filter_grid = new AFilter(['method' => 'post', 'grid_filter_params' => $grid_filter_params]);
        $data = array_merge($filter_form->getFilterData(), $filter_grid->getFilterData());
        $total = $this->model_catalog_product->getTotalProducts($data);
        $response = new stdClass();
        $response->page = $filter_grid->getParam('page');
        $response->total = $filter_grid->calcTotalPages($total);
        $response->records = $total;
        $response->userdata = new stdClass();
        $response->userdata->classes = [];
        $results = $this->model_catalog_product->getProducts($data);

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

            $response->rows[$i]['id'] = $result['product_id'];
            if (dateISO2Int($result['date_available']) > time()) {
                $response->userdata->classes[$result['product_id']] = 'warning';
            }

            if ($result['call_to_order'] > 0) {
                $price = $this->language->get('text_call_to_order');
            } else {
                $price = $this->html->buildInput(
                    [
                        'name'  => 'price['.$result['product_id'].']',
                        'value' => moneyDisplayFormat($result['price']),
                    ]
                );
            }

            $response->rows[$i]['cell'] = [
                $thumbnail['thumb_html'],
                $this->html->buildInput(
                    [
                        'name'  => 'product_description['.$result['product_id'].'][name]',
                        'value' => $result['name'],
                    ]
                ),
                $this->html->buildInput(
                    [
                        'name'  => 'model['.$result['product_id'].']',
                        'value' => $result['model'],
                    ]
                ),
                $price,
                (int) $result['quantity'],
                $this->html->buildCheckbox(
                    [
                        'name'  => 'status['.$result['product_id'].']',
                        'value' => $result['status'],
                        'style' => 'btn_switch',
                    ]
                ),
            ];
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
        $post = $this->request->post;

        if (!$this->user->canModify('listing_grid/product')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/product'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }

        /** @var ModelCatalogProduct $mdl */
        $mdl = $this->loadModel('catalog/product');
        $this->loadLanguage('catalog/product');

        switch ($post['oper']) {
            case 'del':
                $ids = explode(',', $post['id']);
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $err = $this->_validateDelete($id);
                        if (!empty($err)) {
                            $error = new AError('');
                            $error->toJSONResponse(
                                'VALIDATION_ERROR_406',
                                [
                                    'error_text' => $err,
                                ]
                            );
                            return;
                        }
                        $mdl->deleteProduct($id);
                    }
                    $this->extensions->hk_ProcessData($this, 'product_delete');
                }
                break;
            case 'save':
            case 'enable':
            case 'disable':
                $allowedFields = array_merge(
                    [
                        'product_description',
                        'model',
                        'call_to_order',
                        'price',
                        'quantity',
                        'status',
                    ],
                    (array) $this->data['allowed_fields']
                );

                $ids = explode(',', $post['id']);
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        foreach ($allowedFields as $f) {
                            if ($f == 'status' && !isset($post['status'][$id])) {
                                $post['status'][$id] = 0;
                            }

                            if ($post['oper'] == 'enable') {
                                $post['status'][$id] = 1;
                            } elseif ($post['oper'] == 'disable') {
                                $post['status'][$id] = 0;
                            }

                            if (isset($post[$f][$id])) {
                                $err = $this->_validateField(
                                    $f,
                                    $post[$f][$id]
                                );

                                if (!empty($err)) {
                                    $error = new AError('');
                                    $error->toJSONResponse(
                                        'VALIDATION_ERROR_406',
                                        [
                                            'error_text' => $err,
                                        ]
                                    );
                                    return;
                                }
                                $mdl->updateProduct(
                                    $id,
                                    [
                                        $f => $post[$f][$id],
                                    ]
                                );
                            }
                        }
                    }
                    $this->extensions->hk_ProcessData($this, 'product_update');
                }
                break;
            case 'relate':
                $ids = explode(',', $post['id']);
                if (!empty($ids)) {
                    $mdl->relateProducts($ids);
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
     * @throws AException
     */
    public function update_field()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $post = $this->request->post;

        if (!$this->user->canModify('listing_grid/product')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/product'),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('catalog/product');
        /** @var ModelCatalogProduct $mdl */
        $mdl = $this->loadModel('catalog/product');

        $product_id = (int) $this->request->get['id'];
        $productInfo = $mdl->getProduct($product_id);
        if ($product_id && $productInfo) {
            //request sent from edit form. ID in url
            foreach ($post as $key => $value) {
                $err = $this->_validateField($key, $value, $productInfo);
                if (!empty($err)) {
                    $error = new AError('');
                    $error->toJSONResponse('VALIDATION_ERROR_406', ['error_text' => $err]);
                    return;
                }
                if ($key == 'date_available') {
                    $value = dateDisplay2ISO($value);
                }
                $data = [$key => $value];
                $mdl->updateProduct($product_id, $data);
                $mdl->updateProductLinks($product_id, $data);
            }
            $this->extensions->hk_ProcessData($this, 'product_update', ['product_id' => $product_id]);
            return;
        }

        //request sent from jGrid. ID is key of array
        $allowedFields = array_merge(
            [
                'product_description',
                'model',
                'price',
                'call_to_order',
                'quantity',
                'status',
            ],
            (array) $this->data['allowed_fields']
        );

        foreach ($allowedFields as $f) {
            if (isset($post[$f])) {
                foreach ($post[$f] as $k => $v) {
                    $err = $this->_validateField($f, $v);
                    if (!empty($err)) {
                        $error = new AError('');
                        $error->toJSONResponse('VALIDATION_ERROR_406', ['error_text' => $err]);
                        return;
                    }
                    $this->model_catalog_product->updateProduct($k, [$f => $v]);
                    $this->extensions->hk_ProcessData($this, 'product_update', ['product_id' => $k]);
                }
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update_discount_field()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $post = $this->request->post;

        if (!$this->user->canModify('listing_grid/product')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/product'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('catalog/product');
        $this->loadModel('catalog/product');
        if (isset($this->request->get['id'])) {
            //request sent from edit form. ID in url
            foreach ($post as $key => $value) {
                $data = [$key => $value];
                $this->model_catalog_product->updateProductDiscount(
                    $this->request->get['id'],
                    $data
                );
            }
            return;
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update_special_field()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $post = $this->request->post;

        if (!$this->user->canModify('listing_grid/product')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/product'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('catalog/product');
        $this->loadModel('catalog/product');
        if (isset($this->request->get['id'])) {
            //request sent from edit form. ID in url
            foreach ($post as $key => $value) {
                $data = [$key => $value];
                $this->model_catalog_product->updateProductSpecial(
                    $this->request->get['id'],
                    $data
                );
            }
            return;
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update_relations_field()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->user->canModify('listing_grid/product')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/product'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('catalog/product');
        $this->loadModel('catalog/product');
        if (isset($this->request->get['id'])) {
            //request sent from edit form. ID in url
            foreach ($this->request->post as $key => $value) {
                $data = [$key => $value];
                $this->model_catalog_product->updateProductLinks($this->request->get['id'], $data);
            }
            return;
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * @param string $field
     * @param string|array $value
     * @param array $productInfo
     *
     * @return string|null
     * @throws AException
     */
    protected function _validateField($field, $value, $productInfo = [])
    {
        $this->data['error'] = '';
        switch ($field) {
            case 'product_description' :
                if (isset($value['name']) && ((mb_strlen($value['name']) < 1) || (mb_strlen($value['name']) > 255))) {
                    $this->data['error'] = $this->language->get('error_name');
                }
                break;
            case 'model' :
                if (mb_strlen($value) > 64) {
                    $this->data['error'] = $this->language->get('error_model');
                }
                break;
            case 'keyword' :
                $this->data['error'] = $this->html->isSEOkeywordExists('product_id='.$this->request->get['id'], $value);
                break;
            case 'length' :
            case 'width'  :
            case 'height' :
                $v = abs(preformatFloat($value, $this->language->get('decimal_point')));
                if ($v >= 1000) {
                    $this->data['error'] = $this->language->get('error_measure_value');
                }
                $dimensions = [
                    'length' => $productInfo['length'],
                    'width'  => $productInfo['width'],
                    'height' => $productInfo['height'],
                ];
                $dimensions[$field] = $v;
                //if at least one dimension presents - show error
                if (array_sum($dimensions) && !$v && $productInfo['shipping']) {
                    $this->data['error'] = $this->language->get('error_dimension_value');
                }
                break;
            case 'length_class_id':
                if (!$value && $productInfo['shipping']) {
                    $this->data['error'] = $this->language->get('error_length_class');
                }
                break;
            case 'weight' :
                $v = abs(preformatFloat($value, $this->language->get('decimal_point')));
                if ($v >= 1000) {
                    $this->data['error'] = $this->language->get('error_measure_value');
                }
                if (!$v && $productInfo['shipping']) {
                    $this->data['error'] = $this->language->get('error_weight_value');
                }
                break;
            case 'weight_class_id' :
                if (!$value && $productInfo['shipping']) {
                    $this->data['error'] = $this->language->get('error_weight_class');
                }
                break;
        }
        $this->extensions->hk_ValidateData($this, [__FUNCTION__, $field, $value]);
        return $this->data['error'];
    }

    protected function _validateDelete($id)
    {
        $this->data['error'] = '';
        $this->extensions->hk_ValidateData($this, [__FUNCTION__, $id]);
        return $this->data['error'];
    }

}