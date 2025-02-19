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

class ControllerResponsesListingGridTaxClass extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('localisation/tax_class');
        $this->loadModel('localisation/tax_class');

        //Prepare filter config
        $grid_filter_params = array_merge(['title'], (array)$this->data['grid_filter_params']);
        $filter = new AFilter(['method' => 'post', 'grid_filter_params' => $grid_filter_params]);
        $filter_data = $filter->getFilterData();

        $total = $this->model_localisation_tax_class->getTotalTaxClasses($filter_data);
        $response = new stdClass();
        $response->page = $filter->getParam('page');
        $response->total = $filter->calcTotalPages($total);
        $response->records = $total;
        $response->userdata = (object)[''];
        $results = $this->model_localisation_tax_class->getTaxClasses($filter_data);

        $i = 0;
        foreach ($results as $result) {

            $response->rows[$i]['id'] = $result['tax_class_id'];
            $response->rows[$i]['cell'] = [
                $this->html->buildInput([
                    'name'  => 'tax_class['.$result['tax_class_id'].']['.$this->session->data['content_language_id'].'][title]',
                    'value' => $result['title'],
                ]),
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

        $this->loadModel('localisation/tax_class');
        $this->loadLanguage('localisation/tax_class');
        if (!$this->user->canModify('listing_grid/tax_class')) {
            $error = new AError('');
            $error->toJSONResponse('NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/tax_class'),
                    'reset_value' => true,
                ]
            );
            return;
        }
        $ids = array_unique(
            array_map(
                'intval',
                explode(',', $this->request->post['id'])
            )
        );
        if ($ids) {
            switch ($this->request->post['oper']) {
                case 'del':
                    foreach ($ids as $id) {
                        $errorText = $this->_validateDelete($id);
                        $this->extensions->hk_ProcessData(
                            $this,
                            __FUNCTION__,
                            ['tax_class_id' => $id, 'error_text' => $errorText]
                        );
                        if ($errorText) {
                            $error = new AError($errorText);
                            $error->toJSONResponse(
                                'VALIDATION_ERROR_406',
                                [
                                    'error_text' => $errorText,
                                ]
                            );
                            return;
                        }

                        $this->model_localisation_tax_class->deleteTaxClass($id);
                    }
                    break;
                case 'save':
                    foreach ($ids as $id) {
                        if (isset($this->request->post['tax_class'][$id])) {
                            foreach ($this->request->post['tax_class'][$id] as $value) {
                                if (isset($value['title'])) {
                                    $errorText = $this->_validateField('title', $value['title']);
                                    if ($errorText) {
                                        $error = new AError($errorText);
                                        $error->toJSONResponse(
                                            'VALIDATION_ERROR_406',
                                            [
                                                'error_text' => $errorText,
                                            ]
                                        );
                                        return;
                                    }
                                }
                            }
                            $this->model_localisation_tax_class->editTaxClass(
                                $id,
                                ['tax_class' => $this->request->post['tax_class'][$id]]
                            );
                        }
                    }
                    break;
                default:
            }
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

        $this->loadLanguage('localisation/tax_class');
        if (!$this->user->canModify('listing_grid/tax_class')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/tax_class'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }
        $this->loadModel('localisation/tax_class');
        if (isset($this->request->get['id'])) {
            //request sent from edit form. ID in url
            foreach ($this->request->post as $key => $value) {
                $errorText = '';
                if ($key == 'tax_class') {
                    foreach ($value as $val) {
                        if (isset($val['title'])) {
                            $errorText .= $this->_validateField('title', $val['title']);
                        }
                    }
                } else {
                    $errorText = $this->_validateField($key, $value);
                }
                if ($errorText) {
                    $error = new AError($errorText);
                    $error->toJSONResponse(
                        'VALIDATION_ERROR_406',
                        [
                            'error_text' => $errorText,
                        ]
                    );
                    return;
                }
                $data = [$key => $value];
                $this->model_localisation_tax_class->editTaxClass($this->request->get['id'], $data);
            }
            return null;
        }

        //request sent from jGrid. ID is key of array
        if (isset($this->request->post['tax_class'])) {
            foreach ($this->request->post['tax_class'] as $id => $v) {
                foreach ($v as $value) {
                    $errorText = $this->_validateField('title', $value['title']);
                    if ($errorText) {
                        $error = new AError($errorText);
                        $error->toJSONResponse(
                            'VALIDATION_ERROR_406',
                            [
                                'error_text' => $errorText,
                            ]
                        );
                        return;
                    }
                }
                $this->model_localisation_tax_class->editTaxClass($id, ['tax_class' => $v]);
            }
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
    public function update_rate_field()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('localisation/tax_class');
        if (!$this->user->canModify('listing_grid/tax_class')) {
            $error = new AError('');
            $error->toJSONResponse('NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/tax_class'),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadModel('localisation/tax_class');
        if (isset($this->request->get['id'])) {
            //request sent from edit form. ID in url
            foreach ($this->request->post as $key => $value) {
                $errorText = $this->_validateField($key, $value);
                if ($errorText) {
                    $error = new AError($errorText);
                    $error->toJSONResponse(
                        'VALIDATION_ERROR_406',
                        [
                            'error_text' => $errorText,
                        ]
                    );
                    return;
                }
                $data = [$key => $value];
                $this->model_localisation_tax_class->editTaxRate($this->request->get['id'], $data);
            }
            return;
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _validateField($field, $value)
    {
        $this->data['error'] = '';
        switch ($field) {
            case 'title' :
                if (mb_strlen($value) < 2 || mb_strlen($value) > 128) {
                    $this->data['error'] = $this->language->get('error_tax_title');
                }
                break;
            case 'rate' :
                if (!$value) {
                    $this->data['error'] = $this->language->get('error_rate');
                }
                break;
        }

        $this->extensions->hk_ValidateData($this, [__FUNCTION__, $field, $value]);
        return $this->data['error'];
    }

    protected function _validateDelete($tax_class_id)
    {
        $this->data['error'] = '';
        $this->loadModel('catalog/product');

        $product_total = $this->model_catalog_product->getTotalProductsByTaxClassId($tax_class_id);
        if ($product_total) {
            $this->data['error'] = sprintf($this->language->get('error_product'), $product_total);
        }

        $this->extensions->hk_ValidateData($this, [__FUNCTION__, $tax_class_id]);
        return $this->data['error'];
    }

    public function tax_rates()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('localisation/tax_class');
        $this->loadModel('localisation/tax_class');

        //Prepare filter config
        $grid_filter_params = array_merge(['title'], (array)$this->data['grid_filter_params']);
        $filter = new AFilter(['method' => 'post', 'grid_filter_params' => $grid_filter_params]);

        $this->loadModel('localisation/location');
        $this->loadModel('localisation/zone');
        $results = $this->model_localisation_location->getLocations();

        $zones = $locations = [];
        $zones[0] = $this->language->get('text_tax_all_zones');

        $tax_rates = $this->model_localisation_tax_class->getTaxRates($this->request->get['tax_class_id']);
        $total = sizeof($tax_rates);
        $rates = array_column($tax_rates, 'location_id');
        foreach ($results as $c) {
            if (in_array($c['location_id'], $rates)) {
                $locations[$c['location_id']] = $c['name'];
                $tmp = $this->model_localisation_zone->getZonesByLocationId($c['location_id']);
                $zones += array_column($tmp, 'name', 'zone_id');
            }
        }
        unset($results, $tmp);

        $response = new stdClass();
        $response->page = $filter->getParam('page');
        $response->total = $filter->calcTotalPages($total);
        $response->records = $total;
        $response->userdata = new stdClass();

        foreach ($tax_rates as $i => $tax_rate) {
            $response->rows[$i]['id'] = $tax_rate['tax_rate_id'];
            $response->rows[$i]['cell'] = [
                $locations[$tax_rate['location_id']],
                $zones[$tax_rate['zone_id']],
                $tax_rate['description'],
                $tax_rate['rate_prefix'].$tax_rate['rate'],
                $tax_rate['priority'],
            ];

        }
        $this->data['response'] = $response;

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['response']));
    }
}