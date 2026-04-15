<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerResponsesListingGridCustomer extends AController
{
    public $error = '';

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('sale/customer');
        /** @var ModelSaleCustomer $cMdl */
        $cMdl = $this->loadModel('sale/customer');
        $this->load->library('json');

        $page = (int) $this->request->post['page'] ? : 1;
        // get how many rows we want to have into the grid
        $limit = (int) $this->request->post['rows'] ? : $this->config->get('config_admin_limit') ? : 20;
        // sort by
        $sidx = $this->request->post['sidx'];
        //order by
        $sord = $this->request->post['sord'];

        $data = [
            'sort'  => $sidx,
            'order' => $sord,
            'start' => ($page - 1) * $limit,
            'limit' => $limit,
        ];
        if (has_value($this->request->get['customer_group'])) {
            $data['filter']['customer_group_id'] = $this->request->get['customer_group'];
        }
        if (has_value($this->request->get['status'])) {
            $data['filter']['status'] = $this->request->get['status'];
        }
        if (has_value($this->request->get['approved'])) {
            $data['filter']['approved'] = $this->request->get['approved'];
        }

        $allowedFields = array_merge(
            ['customer_id', 'name', 'email'],
            (array) $this->data['allowed_fields']
        );

        if (isset($this->request->post['_search']) && $this->request->post['_search'] == 'true') {
            $searchData = AJson::decode(htmlspecialchars_decode($this->request->post['filters']), true);
            foreach ($searchData['rules'] as $rule) {
                if (!in_array($rule['field'], $allowedFields)) {
                    continue;
                }
                $data['filter'][$rule['field']] = trim($rule['data']);
            }
        }

        $orders_count = 0;
        $mode = $sidx == 'orders_count' ? '' : 'quick';

        $results = $cMdl->getCustomers($data, $mode);

        $total = $results[0]['total_num_rows'];
        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $total;

        if ($mode) {
            //get order count for customers' list by separate request to prevent slow SQL issue
            $customersIds = filterIntegerIdList(array_column($results, 'customer_id'));
            $this->loadModel('sale/order');
            $orders_count = $this->model_sale_order->getCountOrdersByCustomerIds($customersIds);
        }
        $i = 0;
        foreach ($results as $result) {
            if ($mode) {
                $order_cnt = (int) $orders_count[$result['customer_id']];
            } else {
                $order_cnt = (int) $result['orders_count'];
            }
            $response->rows[$i]['id'] = $result['customer_id'];
            $response->rows[$i]['cell'] = [
                $result['customer_id'],
                $result['name'],
                '<a href="' . $this->html->getSecureURL('sale/contact', '&email[]=' . $result['email']) . '">'
                . $result['email'] . '</a>',
                $result['customer_group'],
                $this->html->buildCheckbox(
                    [
                        'name'  => 'status[' . $result['customer_id'] . ']',
                        'value' => $result['status'],
                        'style' => 'btn_switch',
                    ]
                ),
                $this->html->buildCheckbox(
                    [
                        'name'  => 'approved[' . $result['customer_id'] . ']',
                        'value' => $result['approved'],
                        'style' => 'btn_switch',
                    ]
                ),
                ($order_cnt > 0
                    ?
                    $this->html->buildButton(
                        [
                            'name'   => 'view orders',
                            'text'   => $order_cnt,
                            'style'  => 'btn btn-default btn-xs',
                            'href'   => $this->html->getSecureURL(
                                'sale/order', '&customer_id=' . $result['customer_id']
                            ),
                            'title'  => $this->language->get('text_view') . ' ' . $this->language->get('tab_history'),
                            'target' => '_blank',
                        ]
                    )
                    : 0),
                $result['date_added'],
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

        $this->loadModel('sale/customer');
        $this->loadLanguage('sale/customer');
        if (!$this->user->canModify('listing_grid/customer')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/customer'),
                    'reset_value' => true,
                ]
            );
        }
        $ids = filterIntegerIdList(explode(',', $this->request->post['id']));
        switch ($this->request->post['oper']) {
            case 'del':
                if ($ids) {
                    foreach ($ids as $id) {
                        $this->model_sale_customer->deleteCustomer($id);
                    }
                }
                break;
            case 'save':
                if ($ids) {
                    foreach ($ids as $id) {
                        $err = $this->_validateForm('status', $this->request->post['status'][$id], $id);
                        if (!$err) {
                            $this->model_sale_customer->editCustomerField(
                                $id,
                                'status',
                                $this->request->post['status'][$id]
                            );
                        } else {
                            $error = new AError('');
                            $error->toJSONResponse(
                                'VALIDATION_ERROR_406',
                                [
                                    'error_text'  => $err,
                                    'reset_value' => false,
                                ]
                            );
                        }
                        $do_approve = $this->request->post['approved'][$id];
                        $err = $this->_validateForm('approved', $do_approve, $id);
                        if (!$err) {
                            //if customer is not subscriber, send email
                            if ($do_approve && !$this->model_sale_customer->isSubscriber($id)) {
                                //send email when customer was not approved
                                $this->model_sale_customer->sendApproveMail($id);
                            }
                            //do not change order of calls here!!!
                            $this->model_sale_customer->editCustomerField($id, 'approved', $do_approve);
                        } else {
                            $error = new AError('');
                            $error->toJSONResponse(
                                'VALIDATION_ERROR_406',
                                [
                                    'error_text'  => $err,
                                    'reset_value' => false,
                                ]
                            );
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
     * @throws AException|TransportExceptionInterface
     */
    public function update_field()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('sale/customer');
        /** @var ModelSaleCustomer $cMdl */
        $cMdl = $this->loadModel('sale/customer');

        if (!$this->user->canModify('listing_grid/customer')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/customer'),
                    'reset_value' => true,
                ]
            );
        }
        $customer_id = (int) $this->request->get['id'] ? : null;
        $address_id = (int) $this->request->get['address_id'] ? : null;
        $post_data = $this->request->post;
        if (isset($customer_id)) {
            if ($post_data['password'] || $post_data['password_confirm']) {
                $error = new AError('');
                if (mb_strlen($post_data['password']) < 4) {
                    $error->toJSONResponse(
                        'VALIDATION_ERROR_406',
                        [
                            'error_text'  => $this->language->get('error_password'),
                            'reset_value' => true,
                        ]
                    );
                }
                if ($post_data['password'] != $post_data['password_confirm']) {
                    $error->toJSONResponse(
                        'VALIDATION_ERROR_406',
                        [
                            'error_text'  => $this->language->get('error_confirm'),
                            'reset_value' => true,
                        ]
                    );
                }
                //passwords do match, save
                $cMdl->editCustomerField($customer_id, 'password', $post_data['password']);
                //destroy all active sessions
                $customer = new ACustomer($this->registry);
                $customer->deleteActiveSessionsByID($customer_id);
            } else {
                foreach ($post_data as $field => $value) {
                    $err = $this->_validateForm($field, $value, $customer_id);
                    if (!$err) {
                        if ($field == 'approved') {
                            //send email when customer was not approved
                            if ($value && !$cMdl->isSubscriber($customer_id)) {
                                $cMdl->sendApproveMail($customer_id);
                            }
                        }
                        if ($field == 'default' && $address_id) {
                            $cMdl->setDefaultAddress($customer_id, $address_id);
                        } else {
                            if (has_value($address_id)) {
                                $cMdl->editAddressField($address_id, $field, $value);
                            } else {
                                $cMdl->editCustomerField($customer_id, $field, $value);
                            }
                        }
                    } else {
                        $error = new AError('');
                        $error->toJSONResponse(
                            'VALIDATION_ERROR_406',
                            [
                                'error_text'  => $err,
                                'reset_value' => false,
                            ]
                        );
                    }
                }
            }
            //update controller data
            $this->extensions->hk_UpdateData($this, __FUNCTION__);
            return;
        }

        //request sent from jGrid. ID is the key of an array
        foreach ($this->request->post as $field => $value) {
            foreach ($value as $k => $v) {
                $err = $this->_validateForm($field, $v);
                if (!$err) {
                    if ($field == 'approved') {
                        if ($v && !$cMdl->isSubscriber($k)) {
                            //send email when customer was not approved
                            $cMdl->sendApproveMail($k);
                        }
                    }
                    $cMdl->editCustomerField($k, $field, $v);
                } else {
                    $error = new AError('');
                    $error->toJSONResponse(
                        'VALIDATION_ERROR_406',
                        [
                            'error_text'  => $err,
                            'reset_value' => false,
                        ]
                    );
                }
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _validateForm($field, $value, $customer_id = '')
    {
        switch ($field) {
            case 'loginname' :
                $login_name_pattern = '/^[\w._-]+$/i';
                $value = preg_replace('/\s+/', '', $value);
                if (mb_strlen($value) < 5 || mb_strlen($value) > 64
                    || (!preg_match($login_name_pattern, $value) && $this->config->get('prevent_email_as_login'))
                ) {
                    $this->error = $this->language->get('error_loginname');
                    //check uniqueness of loginname
                } else {
                    /** @var ModelSaleCustomer $cMdl */
                    $cMdl = $this->loadModel('sale/customer');
                    if (!$cMdl->is_unique_loginname($value, $customer_id)) {
                        $this->error = $this->language->get('error_loginname_notunique');
                    }
                }
                break;
            case 'firstname' :
                if (mb_strlen($value) < 1 || mb_strlen($value) > 32) {
                    $this->error = $this->language->get('error_firstname');
                }
                break;
            case 'lastname':
                if (mb_strlen($value) < 1 || mb_strlen($value) > 32) {
                    $this->error = $this->language->get('error_lastname');
                }
                break;
            case 'email':
                if (mb_strlen($value) > 96 || !preg_match(EMAIL_REGEX_PATTERN, $value)) {
                    $this->error = $this->language->get('error_email');
                }
                break;
            case 'telephone':
                if (mb_strlen($value) > 32) {
                    $this->error = $this->language->get('error_telephone');
                }
                break;
            case 'address_1':
                if (mb_strlen($value) < 1) {
                    $this->error = $this->language->get('error_address_1');
                }
                break;
            case 'city':
                if (mb_strlen($value) < 1) {
                    $this->error = $this->language->get('error_city');
                }
                break;
            case 'country_id':
                if (empty($value) || $value == 'FALSE') {
                    $this->error = $this->language->get('error_country');
                }
                break;
            case 'zone_id':
                if (empty($value) || $value == 'FALSE') {
                    $this->error = $this->language->get('error_zone');
                }
                break;
        }

        $this->extensions->hk_ValidateData($this);

        return $this->error;
    }

    public function customers()
    {
        $customers_data = [];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        /** @var ModelSaleCustomer $cMdl */
        $cMdl = $this->loadModel('sale/customer');
        if (isset($this->request->post['term'])) {
            $filter = [
                'limit'               => 20,
                'content_language_id' => $this->language->getContentLanguageID(),
                'filter'              => [
                    'name_email'     => $this->request->post['term'],
                    'match'          => 'any',
                    'only_customers' => 1,
                ],
            ];
            $customers = $cMdl->getCustomers($filter);
            foreach ($customers as $cdata) {
                $customers_data[] = [
                    'id'   => $cdata['customer_id'],
                    'name' => $cdata['firstname'] . ' ' . $cdata['lastname'],
                ];
            }
        }

        $this->data['customers_data'] = $customers_data;
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($this->data['customers_data']));
    }
}