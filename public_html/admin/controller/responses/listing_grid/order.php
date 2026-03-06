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

class ControllerResponsesListingGridOrder extends AController
{
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('sale/order');
        /** @var ModelSaleOrder $mdl */
        $mdl = $this->loadModel('sale/order');

        $page = (int)$this->request->post['page'] ?: 1;
        $limit = (int)$this->request->post['rows'] ?: 20;
        $sidx = $this->request->post['sidx'];
        $sord = $this->request->post['sord'];

        // process jGrid search parameter
        $allowedFields = array_merge(
            [
                'name',
                'order_id',
                'date_added',
                'total'
            ],
            (array) $this->data['allowed_fields']
        );
        $allowedSortFields = array_merge(
            [
                'customer_id',
                'order_id',
                'name',
                'status',
                'date_added',
                'total'
            ],
            (array) $this->data['allowed_sort_fields']
        );

        $allowedDirection = ['asc', 'desc'];

        if (!in_array($sidx, $allowedSortFields)) {
            $sidx = $allowedSortFields[0];
        }
        if (!in_array($sord, $allowedDirection)) {
            $sord = $allowedDirection[0];
        }

        if (in_array($sidx, ['customer_id', 'order_id', 'date_added', 'total'])) {
            $sidx = 'o.'.$sidx;
        }

        $data = [
            'sort'  => $sidx,
            'order' => $sord,
            'start' => ($page - 1) * $limit,
            'limit' => $limit,
        ];
        if (isset($this->request->get['store_id'])) {
            $data['store_id'] = $this->request->get['store_id'];
            $data['store_id'] = $data['store_id'] != 'all' ? (int) $data['store_id'] : 'all';
        }

        if (isset($this->request->get['status']) && $this->request->get['status'] != '') {
            $data['filter_order_status_id'] = $this->request->get['status'];
        }
        if (has_value($this->request->get['customer_id'])) {
            $data['filter_customer_id'] = $this->request->get['customer_id'];
        }
        if (has_value($this->request->get['product_id'])) {
            $data['filter_product_id'] = $this->request->get['product_id'];
        }

        if (isset($this->request->post['_search']) && $this->request->post['_search'] == 'true') {
            $searchData = json_decode(htmlspecialchars_decode($this->request->post['filters']), true);
            if ($searchData['rules']) {
                foreach ($searchData['rules'] as $rule) {
                    if (!in_array($rule['field'], $allowedFields)) {
                        continue;
                    }
                    $data['filter_'.$rule['field']] = trim($rule['data']);
                    if ($rule['field'] == 'date_added') {
                        $data['filter_'.$rule['field']] = dateDisplay2ISO($rule['data']);
                    }
                }
            }
        }

        $this->loadModel('localisation/order_status');
        $results = $this->model_localisation_order_status->getOrderStatuses();
        $statuses = [
            '' => $this->language->get('text_select_status')
            ]
            + array_column($results,'name','order_status_id');
        $results = $mdl->getOrders($data);
        $total = $results[0]['total_num_rows'];
        $total_pages = $total > 0 ? ceil($total / $limit) : 0;

        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $total;

        $i = 0;
        foreach ($results as $result) {
            $response->rows[$i]['id'] = $result['order_id'];
            $response->rows[$i]['cell'] = [
                $result['order_id'],
                $result['name'],
                $this->html->buildSelectBox(
                    [
                        'name'    => 'order_status_id['.$result['order_id'].']',
                        'value'   => array_search($result['status'], $statuses),
                        'options' => $statuses,
                    ]
                ),
                dateISO2Display($result['date_added'], $this->language->get('date_format_short')),
                $this->currency->format($result['total'], $result['currency'], $result['value']),
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

        /** @var ModelSaleOrder $mdl */
        $mdl = $this->loadModel('sale/order');
        $this->loadLanguage('sale/order');
        if (!$this->user->canModify('listing_grid/order')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/order'),
                    'reset_value' => true,
                ]
            );
        }
        $ids = filterIntegerIdList(explode(',', $this->request->post['id']));
        switch ($this->request->post['oper']) {
            case 'del':
                if($ids){
                    foreach ($ids as $id) {
                        $mdl->deleteOrder($id);
                    }
                }
                break;
            case 'save':
                if($ids){
                    foreach ($ids as $id) {
                         $mdl->addOrderHistory(
                            $id,
                            [
                                'order_status_id' => $this->request->post['order_status_id'][$id],
                            ]
                        );
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
     * @void
     * @throws AException
     * @throws TransportExceptionInterface
     */
    public function update_field()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('sale/order');
        /** @var ModelSaleOrder $mdl */
        $mdl = $this->loadModel('sale/order');

        if (!$this->user->canModify('listing_grid/order')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf(
                        $this->language->get('error_permission_modify'),
                        'listing_grid/order'
                    ),
                    'reset_value' => true,
                ]
            );
        }

        if (has_value($this->request->post['downloads'])) {
            $data = (array)$this->request->post['downloads'];
            /** @var ModelCatalogDownload $dMdl */
            $dMdl = $this->loadModel('catalog/download');
            foreach ($data as $orderDownloadId => $item) {
                if (isset($item['expire_date'])) {
                    $item['expire_date'] = $item['expire_date']
                        ? dateDisplay2ISO($item['expire_date'], $this->language->get('date_format_short'))
                        : '';
                }
                $dMdl->editOrderDownload((int)$orderDownloadId, $item);
            }
            //update controller data
            $this->extensions->hk_UpdateData($this, __FUNCTION__);
            return;
        }

        if (isset($this->request->get['id'])) {
            $orderId = (int)$this->request->get['id'];
            $mdl->addOrderHistory($orderId, $this->request->post);
        }else {
            //request sent from jGrid. ID is a key of an array
            foreach ($this->request->post as $field => $value) {
                foreach ($value as $k => $v) {
                    $orderId = (int) $k;
                    if ($orderId) {
                        $mdl->addOrderHistory($orderId, [$field => $v]);
                    }
                }
            }
        }
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function summary()
    {
        //update controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('sale/order');
        $this->loadModel('sale/order');

        $response = new stdClass();

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }

        $order_info = $this->model_sale_order->getOrder($order_id);

        if (empty($order_info)) {
            $response->error = $this->language->get('error_order_load');
        } else {
            $response->order = [
                'order_id'        => '#'.$order_info['order_id'],
                'name'            => $order_info['firstname'].''.$order_info['lastname'],
                'email'           => $order_info['email'],
                'telephone'       => $order_info['telephone'],
                'date_added'      => dateISO2Display(
                    $order_info['date_added'],
                    $this->language->get('date_format_short')
                ),
                'total'           => $this->currency->format(
                    $order_info['total'], $order_info['currency'], $order_info['value']
                ),
                'order_status'    => $order_info['order_status_id'],
                'shipping_method' => $order_info['shipping_method'],
                'payment_method'  => $order_info['payment_method'],
            ];

            if ($order_info['customer_id']) {
                $response->order['name'] = '<a href="'
                    .$this->html->getSecureURL('sale/customer/update','&customer_id='.$order_info['customer_id']).'">'
                    .$response->order['name'].'</a>';
            }

            $this->loadModel('localisation/order_status');
            $status = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);
            if ($status) {
                $response->order['order_status'] = $status['name'];
            }
        }
        $this->data['response'] = $response;
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['response']));
    }

}