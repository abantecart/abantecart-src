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

class ControllerResponsesListingGridZone extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('localisation/zone');
        $this->loadModel('localisation/zone');

        $page = $this->request->post['page']; // get the requested page
        $limit = (int)$this->request->post['rows']; // get how many rows we want to have into the grid
        $sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
        $sord = $this->request->post['sord']; // get the direction

        $this->loadModel('localisation/country');

        $search_str = '';
        //process custom search form
        $allowedSearchFilter = ['country_id'];
        $search_param = [];
        foreach ($allowedSearchFilter as $filter) {
            if (isset($this->request->get[$filter]) && $this->request->get[$filter] != '') {
                $search_param[] = " z.`" . $filter . "` = '" . $this->db->escape(trim($this->request->get[$filter])) . "' ";
            }
        }
        if (!empty($search_param)) {
            $search_str = implode(" AND ", $search_param);
        }

        $data = [
            'sort'   => $sidx,
            'order'  => strtoupper($sord),
            'start'  => ($page - 1) * $limit,
            'limit'  => $limit,
            'search' => $search_str,
        ];

        $total = $this->model_localisation_zone->getTotalZones($data);
        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages) {
            $page = $total_pages;
            $data['start'] = ($page - 1) * $limit;
        }

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $total;

        $languageId = $this->language->getContentLanguageID();
        $results = $this->model_localisation_zone->getZones($data);
        $i = 0;
        foreach ($results as $result) {
            $response->rows[$i]['id'] = $result['zone_id'];
            $response->rows[$i]['cell'] = [
                $result['country'],
                $this->html->buildInput(
                    [
                        'name'  => 'zone_name[' . $result['zone_id'] . '][' . $languageId . '][name]',
                        'value' => $result['name'],
                    ]
                ),
                $this->html->buildInput(
                    [
                        'name'  => 'code[' . $result['zone_id'] . ']',
                        'value' => $result['code'],
                    ]
                ),
                $this->html->buildCheckbox(
                    [
                        'name'  => 'status[' . $result['zone_id'] . ']',
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

        if (!$this->user->canModify('listing_grid/zone')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/zone'),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadModel('localisation/zone');
        $this->loadLanguage('localisation/zone');

        $ids = array_unique(
            array_map(
                'intval',
                explode(',', $this->request->post['id'])
            )
        );
        if ($ids) {
            switch ($this->request->post['oper']) {
                case 'del':
                    $this->loadModel('setting/store');
                    $this->loadModel('sale/customer');
                    $this->loadModel('localisation/location');
                    foreach ($ids as $id) {
                        $errorText = $this->_validateDelete($id);
                        $this->extensions->hk_ProcessData(
                            $this,
                            __FUNCTION__,
                            ['zone_id' => $id, 'error_text' => $errorText]
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
                        $this->model_localisation_zone->deleteZone($id);
                    }
                    break;
                case 'save':
                    $allowedFields = array_merge(['status', 'code'], (array)$this->data['allowed_fields']);
                    foreach ($ids as $id) {
                        foreach ($allowedFields as $f) {
                            if ($f == 'status' && !isset($this->request->post['status'][$id])) {
                                $this->request->post['status'][$id] = 0;
                            }
                            if (isset($this->request->post[$f][$id])) {
                                $errorText = $this->_validateField($f, $this->request->post[$f][$id]);
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
                                $this->model_localisation_zone->editZone($id, [$f => $this->request->post[$f][$id]]);
                            }
                        }

                        if (isset($this->request->post['zone_name'][$id])) {
                            foreach ($this->request->post['zone_name'][$id] as $value) {
                                $errorText = $this->_validateField('name', $value['name']);
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
                            $this->model_localisation_zone->editZone(
                                $id,
                                [
                                    'zone_name' => $this->request->post['zone_name'][$id],
                                ]
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
        if (!$this->user->canModify('listing_grid/zone')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/zone'),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('localisation/zone');
        $this->loadModel('localisation/zone');
        if (isset($this->request->get['id'])) {
            $upd = [];
            //request sent from edit form. ID in url
            foreach ($this->request->post as $key => $value) {
                $errorText = '';
                if ($key == 'zone_name') {
                    foreach ($value as $val) {
                        $errorText .= $this->_validateField('name', $val['name']);
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
                $upd[$key] = $value;
            }
            if ($upd) {
                $this->model_localisation_zone->editZone($this->request->get['id'], $upd);
            }
            return null;
        }

        //request sent from jGrid. ID is key of array
        $fields = ['status', 'code'];
        foreach ($fields as $f) {
            if (isset($this->request->post[$f])) {
                foreach ($this->request->post[$f] as $k => $v) {
                    $errorText = $this->_validateField($f, $v);
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
                    $this->model_localisation_zone->editZone($k, [$f => $v]);
                }
            }
        }

        if (isset($this->request->post['zone_name'])) {
            foreach ($this->request->post['zone_name'] as $id => $v) {
                foreach ($v as $value) {
                    $errorText = $this->_validateField('name', $value['name']);
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
                $this->model_localisation_zone->editZone($id, ['zone_name' => $this->request->post['zone_name'][$id]]);
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _validateField($field, $value)
    {
        $this->data['error'] = '';
        if ($field == 'name') {
            if (mb_strlen($value) < 2 || mb_strlen($value) > 128) {
                $this->data['error'] = $this->language->get('error_name');
            }
        }
        $this->extensions->hk_ValidateData($this, [__FUNCTION__, $field, $value]);
        return $this->data['error'];
    }

    protected function _validateDelete($zone_id)
    {
        $this->data['error'] = '';
        if ($this->config->get('config_zone_id') == $zone_id) {
            $this->data['error'] = $this->language->get('error_default');
        }

        $store_total = $this->model_setting_store->getTotalStoresByZoneId($zone_id);
        if ($store_total) {
            $this->data['error'] = sprintf($this->language->get('error_store'), $store_total);
        }

        $address_total = $this->model_sale_customer->getTotalAddressesByZoneId($zone_id);
        if ($address_total) {
            $this->data['error'] = sprintf($this->language->get('error_address'), $address_total);
        }

        $zone_to_location_total = $this->model_localisation_location->getTotalZoneToLocationByZoneId($zone_id);
        if ($zone_to_location_total) {
            $this->data['error'] = sprintf($this->language->get('error_zone_to_location'), $zone_to_location_total);
        }

        $this->extensions->hk_ValidateData($this, [__FUNCTION__, $zone_id]);
        return $this->data['error'];
    }
}