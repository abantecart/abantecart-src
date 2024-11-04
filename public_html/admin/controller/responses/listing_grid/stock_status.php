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

class ControllerResponsesListingGridStockStatus extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('localisation/stock_status');
        $this->loadModel('localisation/stock_status');

        $page = $this->request->post['page']; // get the requested page
        $limit = $this->request->post['rows']; // get how many rows we want to have into the grid
        $sord = $this->request->post['sord']; // get the direction

        // process jGrid search parameter
        $allowedDirection = ['asc', 'desc'];

        if (!in_array($sord, $allowedDirection)) {
            $sord = $allowedDirection[0];
        }

        $data = [
            'order'               => strtoupper($sord),
            'start'               => ($page - 1) * $limit,
            'limit'               => $limit,
            'content_language_id' => $this->session->data['content_language_id'],
        ];

        $total = $this->model_localisation_stock_status->getTotalStockStatuses();
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

        $results = $this->model_localisation_stock_status->getStockStatuses($data);
        $i = 0;
        $languageId = $this->language->getContentLanguageID();
        foreach ($results as $result) {
            $id = $result['stock_status_id'];
            $response->rows[$i]['id'] = $id;
            $response->rows[$i]['cell'] = [
                $this->html->buildInput(
                    [
                        'name'  => 'stock_status[' . $id . '][' . $languageId . '][name]',
                        'value' => $result['name'],
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

        $this->loadModel('localisation/stock_status');
        $this->loadModel('setting/store');
        $this->loadModel('catalog/product');
        $this->loadLanguage('localisation/stock_status');
        if (!$this->user->canModify('listing_grid/stock_status')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/stock_status'
                    ),
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
                            ['stock_status_id' => $id, 'error_text' => $errorText]
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
                        $this->model_localisation_stock_status->deleteStockStatus($id);
                    }
                    break;
                case 'save':
                    foreach ($ids as $id) {
                        if (isset($this->request->post['stock_status'][$id])) {
                            foreach ($this->request->post['stock_status'][$id] as $value) {
                                if (mb_strlen($value['name']) < 2 || mb_strlen($value['name']) > 32) {
                                    $errorText = $this->language->get('error_name');
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
                            $this->model_localisation_stock_status->editStockStatus(
                                $id,
                                ['stock_status' => $this->request->post['stock_status'][$id]]
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
     */
    public function update_field()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('localisation/stock_status');
        if (!$this->user->canModify('listing_grid/stock_status')) {
            $error = new AError('');
            $error->toJSONResponse('NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/stock_status'),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadModel('localisation/stock_status');
        if (isset($this->request->get['id']) && !empty($this->request->post['stock_status'])) {
            //request sent from edit form. ID in url
            foreach ($this->request->post['stock_status'] as $value) {
                if (mb_strlen($value['name']) < 2 || mb_strlen($value['name']) > 32) {
                    $errorText = $this->language->get('error_name');
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
            $this->model_localisation_stock_status->editStockStatus(
                $this->request->get['id'],
                $this->request->post
            );
            return;
        }

        //request sent from jGrid. ID is key of array
        if (isset($this->request->post['stock_status'])) {
            foreach ($this->request->post['stock_status'] as $id => $v) {
                foreach ($v as $value) {
                    if (mb_strlen($value['name']) < 2 || mb_strlen($value['name']) > 32) {
                        $errorText = $this->language->get('error_name');
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
                $this->model_localisation_stock_status->editStockStatus($id, ['stock_status' => $v]);
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _validateDelete($stock_status_id)
    {
        if ($this->config->get('config_stock_status_id') == $stock_status_id) {
            return $this->language->get('error_default');
        }
        $product_total = $this->model_catalog_product->getTotalProductsByStockStatusId($stock_status_id);
        if ($product_total) {
            return sprintf($this->language->get('error_product'), $product_total);
        }
    }
}
