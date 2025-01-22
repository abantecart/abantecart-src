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

class ControllerResponsesListingGridCustomerGroup extends AController
{
    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('sale/customer_group');
        $this->loadModel('sale/customer_group');

        //Prepare filter config
        $grid_filter_params = array_merge(['name', 'tax_exempt'], (array)$this->data['grid_filter_params']);
        $filter = new AFilter(['method' => 'post', 'grid_filter_params' => $grid_filter_params]);
        $total = $this->model_sale_customer_group->getTotalCustomerGroups($filter->getFilterData());

        $response = new stdClass();
        $response->page = $filter->getParam('page');
        $response->total = $filter->calcTotalPages($total);
        $response->records = $total;
        $results = $this->model_sale_customer_group->getCustomerGroups($filter->getFilterData());

        $i = 0;
        $yesNo = [
            1 => $this->language->get('text_yes'),
            0 => $this->language->get('text_no'),
        ];

        foreach ($results as $result) {

            $response->rows[$i]['id'] = $result['customer_group_id'];
            $response->rows[$i]['cell'] = [
                $result['name']
                . ($result['customer_group_id'] == $this->config->get('config_customer_group_id')
                    ? $this->language->get('text_default')
                    : null),
                $yesNo[(int)$result['tax_exempt']],
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

        if (!$this->user->canModify('listing_grid/customer_group')) {
            $error = new AError('');
            $error->toJSONResponse('NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/customer_group'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('sale/customer_group');
        $this->loadModel('sale/customer_group');
        $this->loadModel('setting/store');
        $this->loadModel('sale/customer');

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
                        if ($this->config->get('config_customer_group_id') == $id) {
                            $errorText = $this->language->get('error_default');
                        }

                        $store_total = $this->model_setting_store->getTotalStoresByCustomerGroupId($id);
                        if ($store_total) {
                            $errorText .= sprintf($this->language->get('error_store'), $store_total);
                        }

                        $customer_total = $this->model_sale_customer->getTotalCustomersByCustomerGroupId($id);
                        if ($customer_total) {
                            $errorText .= sprintf($this->language->get('error_customer'), $customer_total);
                        }
                        $this->extensions->hk_ProcessData(
                            $this,
                            __FUNCTION__,
                            ['customer_group_id' => $id, 'error_text' => $errorText]
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

                        $this->model_sale_customer_group->deleteCustomerGroup($id);
                    }
                    break;
                case 'save':
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

        if (!$this->user->canModify('listing_grid/customer_group')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/customer_group'
                    ),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadLanguage('sale/customer_group');
        $this->loadModel('sale/customer_group');

        if (isset($this->request->get['id'])) {
            $this->model_sale_customer_group->editCustomerGroup($this->request->get['id'], $this->request->post);
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}