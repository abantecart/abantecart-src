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

class ControllerResponsesListingGridCustomer extends AController
{
    public $error = '';
    public $data = array();

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('sale/customer');
        $this->loadModel('sale/customer');
        $this->load->library('json');

        $approved = array(
            1 => $this->language->get('text_yes'),
            0 => $this->language->get('text_no'),
        );

        $page = $this->request->post['page'];  // get the requested page
        $limit = $this->request->post['rows']; // get how many rows we want to have into the grid
        $sidx = $this->request->post['sidx'];  // get index row - i.e. user click to sort
        $sord = $this->request->post['sord'];  // get the direction

        $data = array(
            'sort'  => $sidx,
            'order' => $sord,
            'start' => ($page - 1) * $limit,
            'limit' => $limit,
        );
        if (has_value($this->request->get['customer_group'])) {
            $data['filter']['customer_group_id'] = $this->request->get['customer_group'];
        }
        if (has_value($this->request->get['status'])) {
            $data['filter']['status'] = $this->request->get['status'];
        }
        if (has_value($this->request->get['approved'])) {
            $data['filter']['approved'] = $this->request->get['approved'];
        }

        $allowedFields = array_merge(array('customer_id', 'name', 'email'), (array)$this->data['allowed_fields']);

        if (isset($this->request->post['_search']) && $this->request->post['_search'] == 'true') {
            $searchData = AJson::decode(htmlspecialchars_decode($this->request->post['filters']), true);

            foreach ($searchData['rules'] as $rule) {
                if (!in_array($rule['field'], $allowedFields)) {
                    continue;
                }
                $data['filter'][$rule['field']] = $rule['data'];
            }
        }

        $total = $this->model_sale_customer->getTotalCustomers($data);
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
        $orders_count = 0;

        if ($sidx == 'orders_count') {
            $mode = '';
        } else {
            $mode = 'quick';
        }

        $results = $this->model_sale_customer->getCustomers($data, $mode);
        if ($mode) {
            //get orders count for customers list by separate request to prevent slow sql issue
            $customers_ids = array();
            foreach ($results as $result) {
                $customers_ids[] = $result['customer_id'];
            }
            $this->loadModel('sale/order');
            $orders_count = $this->model_sale_order->getCountOrdersByCustomerIds($customers_ids);
        }
        $i = 0;
        foreach ($results as $result) {
            if ($mode) {
                $order_cnt = (int)$orders_count[$result['customer_id']];
            } else {
                $order_cnt = (int)$result['orders_count'];
            }
            $response->rows[$i]['id'] = $result['customer_id'];
            $response->rows[$i]['cell'] = array(
                $result['customer_id'],
                $result['name'],
                '<a href="'.$this->html->getSecureURL('sale/contact', '&email[]='.$result['email']).'">'.$result['email'].'</a>',
                $result['customer_group'],
                $this->html->buildCheckbox(array(
                    'name'  => 'status['.$result['customer_id'].']',
                    'value' => $result['status'],
                    'style' => 'btn_switch',
                )),
                $this->html->buildSelectBox(array(
                    'name'    => 'approved['.$result['customer_id'].']',
                    'value'   => $result['approved'],
                    'options' => $approved,
                )),
                ($order_cnt > 0 ?
                    $this->html->buildButton(array(
                        'name'   => 'view orders',
                        'text'   => $order_cnt,
                        'style'  => 'btn btn-default btn-xs',
                        'href'   => $this->html->getSecureURL('sale/order', '&customer_id='.$result['customer_id']),
                        'title'  => $this->language->get('text_view').' '.$this->language->get('tab_history'),
                        'target' => '_blank',
                    ))
                    : 0),
                $result['date_added'],
            );
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
            return $error->toJSONResponse('NO_PERMISSIONS_402',
                array(
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/customer'),
                    'reset_value' => true,
                ));
        }

        switch ($this->request->post['oper']) {
            case 'del':
                $ids = explode(',', $this->request->post['id']);
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $this->model_sale_customer->deleteCustomer($id);
                    }
                }
                break;
            case 'save':
                $ids = explode(',', $this->request->post['id']);
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $err = $this->_validateForm('status', $this->request->post['status'][$id], $id);
                        if (!$err) {
                            $this->model_sale_customer->editCustomerField($id, 'status', $this->request->post['status'][$id]);
                        } else {
                            $error = new AError('');
                            return $error->toJSONResponse('VALIDATION_ERROR_406',
                                array(
                                    'error_text'  => $err,
                                    'reset_value' => false,
                                ));
                        }
                        $do_approve = $this->request->post['approved'][$id];
                        $err = $this->_validateForm('approved', $do_approve, $id);
                        if (!$err) {
                            //if customer is not subscriber - send email
                            if ($do_approve && !$this->model_sale_customer->isSubscriber($id)) {
                                //send email when customer was not approved
                                $this->model_sale_customer->sendApproveMail($id);
                            }
                            //do not change order of calls here!!!
                            $this->model_sale_customer->editCustomerField($id, 'approved', $do_approve);
                        } else {
                            $error = new AError('');
                            return $error->toJSONResponse('VALIDATION_ERROR_406',
                                array(
                                    'error_text'  => $err,
                                    'reset_value' => false,
                                ));
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
     * @return null
     */
    public function update_field()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('sale/customer');
        $this->loadModel('sale/customer');

        if (!$this->user->canModify('listing_grid/customer')) {
            $error = new AError('');
            return $error->toJSONResponse('NO_PERMISSIONS_402',
                array(
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/customer'),
                    'reset_value' => true,
                ));

        }
        $customer_id = $this->request->get['id'];
        $address_id = $this->request->get['address_id'];
        $post_data = $this->request->post;
        if (isset($customer_id)) {
            if ($post_data['password'] || $post_data['password_confirm']) {
                $error = new AError('');
                if (mb_strlen($post_data['password']) < 4) {
                    return $error->toJSONResponse('VALIDATION_ERROR_406',
                        array(
                            'error_text'  => $this->language->get('error_password'),
                            'reset_value' => true,
                        ));
                }
                if ($post_data['password'] != $post_data['password_confirm']) {
                    return $error->toJSONResponse('VALIDATION_ERROR_406',
                        array(
                            'error_text'  => $this->language->get('error_confirm'),
                            'reset_value' => true,
                        ));
                }
                //passwords do match, save
                $this->model_sale_customer->editCustomerField($customer_id, 'password', $post_data['password']);
            } else {
                foreach ($post_data as $field => $value) {
                    $err = $this->_validateForm($field, $value, $customer_id);
                    if (!$err) {
                        if ($field == 'approved') {
                            //send email when customer was not approved
                            if ($value && !$this->model_sale_customer->isSubscriber($customer_id)) {
                                $this->model_sale_customer->sendApproveMail($customer_id);
                            }
                        }
                        if ($field == 'default' && $address_id) {
                            $this->model_sale_customer->setDefaultAddress($customer_id, $address_id);
                        } else {
                            if (has_value($address_id)) {
                                $this->model_sale_customer->editAddressField($address_id, $field, $value);
                            } else {
                                $this->model_sale_customer->editCustomerField($customer_id, $field, $value);
                            }
                        }

                    } else {
                        $error = new AError('');
                        return $error->toJSONResponse('VALIDATION_ERROR_406',
                            array(
                                'error_text'  => $err,
                                'reset_value' => false,
                            ));

                    }
                }
            }
            //update controller data
            $this->extensions->hk_UpdateData($this, __FUNCTION__);
            return null;
        }

        //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $field => $value) {
            foreach ($value as $k => $v) {
                $err = $this->_validateForm($field, $v);
                if (!$err) {
                    if ($field == 'approved') {
                        if ($v && !$this->model_sale_customer->isSubscriber($k)) {
                            //send email when customer was not approved
                            $this->model_sale_customer->sendApproveMail($k);
                        }
                    }
                    $this->model_sale_customer->editCustomerField($k, $field, $v);
                } else {
                    $error = new AError('');
                    return $error->toJSONResponse('VALIDATION_ERROR_406',
                        array(
                            'error_text'  => $err,
                            'reset_value' => false,
                        ));
                }
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function _validateForm($field, $value, $customer_id = '')
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
                    if (!$this->model_sale_customer->is_unique_loginname($value, $customer_id)) {
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
        $customers_data = array();
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadModel('sale/customer');
        if (isset($this->request->post['term'])) {
            $filter = array(
                'limit'               => 20,
                'content_language_id' => $this->language->getContentLanguageID(),
                'filter'              => array(
                    'name_email'     => $this->request->post['term'],
                    'match'          => 'any',
                    'only_customers' => 1,
                ),
            );
            $customers = $this->model_sale_customer->getCustomers($filter);
            foreach ($customers as $cdata) {
                $customers_data[] = array(
                    'id'   => $cdata['customer_id'],
                    'name' => $cdata['firstname'].' '.$cdata['lastname'],
                );
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