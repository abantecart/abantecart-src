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

class ControllerResponsesListingGridLanguage extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('localisation/language');
        $this->loadModel('localisation/language');

        //Prepare filter config
        $filter_params = array_merge(['name', 'status'], (array)$this->data['filter_params']);
        $grid_filter_params = array_merge(['name', 'code', 'sort_order'], (array)$this->data['grid_filter_params']);

        $filter_form = new AFilter(['method' => 'get', 'filter_params' => $filter_params]);
        $filter_grid = new AFilter(['method' => 'post', 'grid_filter_params' => $grid_filter_params]);

        $total = $this->model_localisation_language->getTotalLanguages(
            array_merge(
                $filter_form->getFilterData(),
                $filter_grid->getFilterData()
            )
        );
        $response = new stdClass();
        $response->page = $filter_grid->getParam('page');
        $response->total = $filter_grid->calcTotalPages($total);
        $response->records = $total;
        $results = $this->model_localisation_language->getLanguages(
            array_merge(
                $filter_form->getFilterData(),
                $filter_grid->getFilterData()
            )
        );
        $i = 0;
        foreach ($results as $result) {
            $response->rows[$i]['id'] = $result['language_id'];
            $response->rows[$i]['cell'] = [
                $this->html->buildInput(
                    [
                        'name'  => 'name[' . $result['language_id'] . ']',
                        'value' => $result['name'],
                    ]
                ),
                $this->html->buildInput(
                    [
                        'name'  => 'code[' . $result['language_id'] . ']',
                        'value' => $result['code'],
                    ]
                ),
                $this->html->buildInput(
                    [
                        'name'  => 'sort_order[' . $result['language_id'] . ']',
                        'value' => $result['sort_order'],
                    ]
                ),
                $this->html->buildCheckbox(
                    [
                        'name'  => 'status[' . $result['language_id'] . ']',
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
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($this->data['response']));
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->user->canModify('listing_grid/language')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/language'),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('localisation/language');
        $this->loadModel('localisation/language');
        $this->loadModel('setting/store');
        $this->loadModel('sale/order');
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
                        $errorText = '';
                        $language_info = $this->model_localisation_language->getLanguage($id);

                        if ($language_info) {
                            if ($this->config->get('config_storefront_language') == $language_info['code']) {
                                $errorText = $this->language->get('error_default');
                            }
                            if ($this->config->get('admin_language') == $language_info['code']) {
                                $errorText .= $this->language->get('error_admin');
                            }
                            $store_total = $this->model_setting_store->getTotalStoresByLanguage($language_info['code']);
                            if ($store_total) {
                                $errorText .= sprintf($this->language->get('error_store'), $store_total);
                            }
                        }

                        $order_total = $this->model_sale_order->getTotalOrdersByLanguageId($id);
                        if ($order_total) {
                            $errorText .= sprintf($this->language->get('error_order'), $order_total);
                        }
                        $this->extensions->hk_ProcessData(
                            $this,
                            __FUNCTION__,
                            ['language_id' => $id, 'error_text' => $errorText]
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
                        $this->model_localisation_language->deleteLanguage($id);
                    }
                    break;
                case 'save':
                    if ($this->request->post['resort'] == 'yes') {
                        $array = [];
                        //get only ids we need
                        foreach ($ids as $id) {
                            $array[$id] = $this->request->post['sort_order'][$id];
                        }
                        $new_sort = build_sort_order(
                            $ids,
                            min($array),
                            max($array),
                            $this->request->post['sort_direction']
                        );
                        $this->request->post['sort_order'] = $new_sort;
                    }

                    foreach ($ids as $id) {
                        $data = [
                            'name'       => $this->request->post['name'][$id],
                            'code'       => $this->request->post['code'][$id],
                            'sort_order' => $this->request->post['sort_order'][$id],
                            'status'     => $this->request->post['status'][$id],
                        ];
                        $this->model_localisation_language->editLanguage($id, $data);
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

        if (!$this->user->canModify('listing_grid/language')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/language'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('localisation/language');
        $this->loadModel('localisation/language');
        $allowedFields = array_merge(
            [
                'name',
                'code',
                'sort_order',
                'status',
                'locale',
                'directory'
            ],
            (array)$this->data['allowed_fields']
        );

        if (isset($this->request->get['id'])) {
            $upd = [];
            foreach ($this->request->post as $key => $value) {
                if (!in_array($key, $allowedFields)) {
                    continue;
                }
                $upd[$key] = $value;
            }
            if ($upd) {
                $this->model_localisation_language->editLanguage($this->request->get['id'], $upd);
            }
            return;
        }

        //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $key => $value) {
            if (!in_array($key, $allowedFields)) {
                continue;
            }
            foreach ($value as $k => $v) {
                $data = [$key => $v];
                $this->model_localisation_language->editLanguage($k, $data);
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}