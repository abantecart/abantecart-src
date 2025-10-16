<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
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

class ExtensionUps extends Extension
{

    public function onControllerPagesSaleOrder_InitData()
    {
        $that = $this->baseObject;
        $that->loadLanguage('ups/ups');
        if (isset($that->session->data['ups_success'])) {
            $that->session->data['success'] .= '<br>' . $that->session->data['ups_success'];
            unset($that->session->data['ups_success']);
        }
    }

    public function onControllerPagesSaleOrder_UpdateData()
    {
        $that = $this->baseObject;
        $order_id = $that->request->get['order_id'];
        if (!$order_id) {
            return null;
        }

        if ($this->baseObject_method == 'address') {
            $this->orderShippingHook($that, $order_id);
        } elseif ($this->baseObject_method == 'history') {
            if (isset($that->session->data['error_warning'])) {
                $that->view->assign('error_warning', $that->session->data['error_warning']);
                unset($that->session->data['error_warning']);
            }
        }
    }

    public function onControllerResponsesListingGridOrder_UpdateData()
    {
        /** @var ControllerResponsesListingGridOrder $that */
        $that = $this->baseObject;
        if ($this->baseObject_method == 'update_field') {
            $order_id = $status_id = 0;
            if (isset($that->request->post['order_status_id'])) {
                foreach ($that->request->post['order_status_id'] as $key => $value) {
                    $order_id = $key;
                    $status_id = $value;
                }
            }
            if ($order_id && $status_id) {
                $this->orderStatusChanged($order_id, $status_id);
            }
        }
    }

    public function onControllerPagesSaleOrder_ValidateData()
    {
        /** @var ControllerPagesSaleOrder $that */
        $that = $this->baseObject;
        $order_id = $that->request->get['order_id'];
        if (!$order_id) {
            return null;
        }

        if ($this->baseObject_method == 'shipping') {
            $this->orderShippingHook($that, $order_id);
        } elseif (
            $that->request->get['rt'] == 'sale/order/history'
            && !$that->error
        ) {
            $this->orderStatusChanged($that->request->get['order_id'], $that->request->post['order_status_id']);
        }
    }

    protected function orderStatusChanged($orderId, $orderStatusId)
    {
        /** @var ControllerPagesSaleOrder $that */
        $that = $this->baseObject;
        $order_data = $this->getOrderShippingData($that, $orderId);
        $data = $order_data['data'];
        if (!isset($data['ups_data'])) {
            return;
        }
        //if shipment not created
        if (!isset($data['ups_data']['shipmentId'])) {

            //and order status changed
            if ($orderStatusId == $that->config->get('ups_manifest_order_status_id')) {
                $order_info = $that->model_sale_order->getOrder($orderId);
                /** @var ModelExtensionUPS $mdl */
                $mdl = $that->loadModel('extension/ups', 'storefront');
                $result = $mdl->createShipment($order_info);
                if (!$result) {
                    $that->error = $mdl->errors;
                    $that->session->data['error_warning'] = implode("\n", $that->error);
                } else {
                    $that->session->data['ups_success'] =
                        $that->language->getAndReplace(
                            'ups_shipment_created_success_message',
                            replaces: $that->html->getSecureURL('sale/order/address', '&order_id=' . $orderId)
                        );
                }
            }
        }
    }

    public function onControllerPagesAccountOrderDetails_UpdateData()
    {

        $that = $this->baseObject;
        $order_id = null;
        if (isset($that->request->get['ot']) && $that->config->get('config_guest_checkout')) {
            //try to decrypt order token
            $order_token = $that->request->get['ot'];
            if ($order_token) {
                list($order_id,) = $that->model_account_customer->parseOrderToken($order_token);
            }
        } else {
            $order_id = $that->request->get['order_id'];
        }

        if (!$order_id) {
            return;
        }

        $that->loadLanguage('ups/ups');
        /**
         * @var ModelExtensionUPS $mdl
         */
        $mdl = $that->loadModel('extension/ups', 'storefront');
        $shipping_data = $mdl->getOrderShippingData($order_id);

        if (isset($shipping_data['data']['result']['ups']['track_urls'])) {
            foreach ($shipping_data['data']['result']['ups']['track_urls'] as $url) {
                $track_btn = $that->html->buildElement(
                    [
                        'type'  => 'button',
                        'href'  => $url,
                        'attr'  => 'target="_blank"',
                        'text'  => $that->language->get('ups_track'),
                        'icon'  => 'fa fa-search',
                        'style' => ' btn btn-info pull-right mr10',
                    ]
                );
                $that->view->addHookVar('hk_additional_buttons', $track_btn);
            }
        }
    }

    public function beforeModelCheckoutOrder_confirm()
    {
        $order_id = func_get_arg(0);
        $order_status_id = func_get_arg(1);
        /** @var ModelCheckoutOrder $that */
        $that = $this->baseObject;

        $order_info = $that->getOrder($order_id);
        if (!$order_info
            || $order_info['order_status_id'] != 0
            || !str_contains($order_info['shipping_method_key'], 'ups.')) {
            return null;
        }

        /** @var ModelExtensionUps $mdl */
        $mdl = $that->load->model('extension/ups');
        if ($that->session->data['ups_parcel_data']) {
            $saveData = array_merge(
                $that->session->data['ups_data'],
                ['ups_parcel_data' => $that->session->data['ups_parcel_data']]
            );

            $mdl->saveOrderShippingData($order_id, $saveData);
        }

        if ($that->config->get('ups_manifest_order_status_id') == $order_status_id) {
            $result = $mdl->createShipment($order_info);
            if (!$result) {
                $that->messages->saveWarning('Order ID ' . $order_id . ' Shipment Creation Warning',
                    implode("\n", $mdl->errors)
                );
                $that->log->write(
                    'Order ID '
                    . $order_id . ' Shipment Creation Warning: '
                    . implode("\n", $mdl->errors)
                );
            }
        }

        $that->session->data['fc']['ups_pickup_added'] = false;
    }

    /**
     * @param AController $that
     * @param $orderId
     * @return void
     * @throws AException
     */
    protected function orderShippingHook($that, $orderId)
    {
        $form = $that->view->getData('form');
        $order_data = $this->getOrderShippingData($that, $orderId);
        $data = $order_data['data'];

        if (!$data) {
            return;
        }

        $pData = $data['ups_data']['ups_parcel_data'];
        if ($pData) {
            $that->view->assign('entry_parcel_info', $that->language->get('ups_entry_parcel_info'));
            $form['shipping_fields']['parcel_info'] = $that->html->buildElement(
                [
                    'type' => 'label',
                    'name' => 'parcel_info',
                    'text' => '<div class="list-group">
                            <ul style="padding:0; margin: 0;">
                                <li class="list-group-item">'. $that->language->get('ups_entry_height'). ' ' . $pData['height'] . ' ' . $pData['dimension_unit'] . '</li>
                                <li class="list-group-item">'. $that->language->get('ups_entry_width'). ' ' . $pData['width'] . ' ' . $pData['dimension_unit'] . '</li>
                                <li class="list-group-item">'. $that->language->get('ups_entry_depth'). ' ' . $pData['depth'] . ' ' . $pData['dimension_unit'] . '</li>
                                <li class="list-group-item">'. $that->language->get('ups_entry_weight'). ' ' . round($pData['weight'], 4) . ' ' . $pData['weight_unit'] . '</li>
                            </ul>
                        </div>'.
                        (!$data['ups_data']['shipmentId']
                        ? '<a href="'.$that->html->getSecureURL('sale/order/history','&order_id='.$orderId).'" class="padding10 ">'
                            . $that->language->getAndReplace(
                                'ups_text_print_label_suggestion',
                                replaces: $that->order_status->getStatusById($that->config->get('ups_manifest_order_status_id'))
                            ).'</a>'
                            : ''),
                ]
            );
        }

        if ($data['ups_data']['shipmentId']){
            foreach ($data['ups_data']['packages'] as $k => $package) {
                $form['shipping_fields']['label' . $k] = $that->html->buildElement(
                    [
                        'type'   => 'button',
                        'href'   => $that->html->getSecureUrl(
                            'extension/ups/label',
                            '&' . http_build_query(['order_id' => $orderId, 'tn' => $package['tracking_number']])
                        ),
                        'text'   => $that->language->get('ups_text_print_label') . ' ' . $package['tracking_number'],
                        'title'  => $that->language->get('ups_text_print_label_title'),
                        'style'  => 'btn btn-info fa fa-print',
                        'target' => '_new',
                    ]
                );
            }
        }
        $that->view->assign('form', $form);
    }


    public function getOrderShippingData($that, $order_id)
    {
        /**
         * @var ModelExtensionUPS $mdl
         */
        $mdl = $that->load->model('extension/ups', 'storefront');
        return $mdl->getOrderShippingData($order_id);
    }
}