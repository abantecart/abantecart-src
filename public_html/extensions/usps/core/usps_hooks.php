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
 */

class ExtensionUsps extends Extension
{
    protected function getManifestOrderStatusId($that): int
    {
        $statusId = (int)$that->config->get('usps_manifest_order_status_id');
        if ($statusId > 0) {
            return $statusId;
        }

        // Match admin selectbox behavior: first status ordered by name.
        $languageId = (int)($that->language->getContentLanguageID() ?: $that->language->getLanguageID());
        $query = $that->db->query(
            "SELECT order_status_id
             FROM " . $that->db->table('order_statuses') . "
             WHERE language_id = '" . $languageId . "'
             ORDER BY `name` ASC
             LIMIT 1"
        );

        return (int)($query->row['order_status_id'] ?? 3);
    }

    public function onControllerPagesSaleOrder_InitData()
    {
        $that = $this->baseObject;
        $that->loadLanguage('usps/usps');
        if (isset($that->session->data['usps_success'])) {
            $that->session->data['success'] .= '<br>' . $that->session->data['usps_success'];
            unset($that->session->data['usps_success']);
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

    public function onControllerPagesAccountOrderDetails_UpdateData()
    {
        $that = $this->baseObject;
        $order_id = null;
        if (isset($that->request->get['ot']) && $that->config->get('config_guest_checkout')) {
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

        $that->loadLanguage('usps/usps');
        /** @var ModelExtensionUsps $mdl */
        $mdl = $that->loadModel('extension/usps', 'storefront');
        $shipping_data = $mdl->getOrderShippingData($order_id);
        $packages = (array)$shipping_data['data']['usps_data']['packages'];

        foreach ($packages as $package) {
            if (empty($package['tracking_number'])) {
                continue;
            }
            $trackUrl = 'https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=' . urlencode($package['tracking_number']);
            $track_btn = $that->html->buildElement(
                [
                    'type'  => 'button',
                    'href'  => $trackUrl,
                    'attr'  => 'target="_blank"',
                    'text'  => $that->language->get('usps_track'),
                    'icon'  => 'fa fa-search',
                    'style' => ' btn btn-info pull-right mr10',
                ]
            );
            $that->view->addHookVar('hk_additional_buttons', $track_btn);
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
            || !str_contains($order_info['shipping_method_key'], 'usps.')) {
            return null;
        }

        /** @var ModelExtensionUsps $mdl */
        $mdl = $that->load->model('extension/usps');
        if ($that->session->data['usps_parcel_data']) {
            $saveData = array_merge(
                (array)$that->session->data['usps_data'],
                ['usps_parcel_data' => $that->session->data['usps_parcel_data']]
            );
            $mdl->saveOrderShippingData($order_id, $saveData);
        }

        if ($this->getManifestOrderStatusId($that) == $order_status_id) {
            $result = $mdl->createShipment($order_info);
            if (!$result) {
                $that->messages->saveWarning(
                    'Order ID ' . $order_id . ' USPS Shipment Creation Warning',
                    implode("\n", (array)$mdl->errors)
                );
                $that->log->write(
                    'Order ID '
                    . $order_id . ' USPS Shipment Creation Warning: '
                    . implode("\n", (array)$mdl->errors)
                );
            }
        }
    }

    protected function orderStatusChanged($orderId, $orderStatusId)
    {
        /** @var ControllerPagesSaleOrder $that */
        $that = $this->baseObject;
        $order_data = $this->getOrderShippingData($that, $orderId);
        $data = $order_data['data'];
        if (!isset($data['usps_data'])) {
            return;
        }

        if (!isset($data['usps_data']['shipmentId'])) {
            if ($orderStatusId == $this->getManifestOrderStatusId($that)) {
                $order_info = $that->model_sale_order->getOrder($orderId);
                /** @var ModelExtensionUsps $mdl */
                $mdl = $that->loadModel('extension/usps', 'storefront');
                $result = $mdl->createShipment($order_info);
                if (!$result) {
                    $that->error = (array)$mdl->errors;
                    $that->session->data['error_warning'] = implode("\n", $that->error);
                } else {
                    $that->session->data['usps_success'] =
                        $that->language->getAndReplace(
                            'usps_shipment_created_success_message',
                            replaces: $that->html->getSecureURL('sale/order/address', '&order_id=' . $orderId)
                        );
                }
            }
        }
    }

    protected function orderShippingHook($that, $orderId)
    {
        $form = $that->view->getData('form');
        $order_data = $this->getOrderShippingData($that, $orderId);
        $data = $order_data['data'];

        if (!$data) {
            return;
        }

        $pData = (array)$data['usps_data']['usps_parcel_data'];
        if ($pData) {
            $that->view->assign('entry_parcel_info', $that->language->get('usps_entry_parcel_info'));
            $form['shipping_fields']['parcel_info'] = $that->html->buildElement(
                [
                    'type' => 'label',
                    'name' => 'parcel_info',
                    'text' => '<div class="list-group">
                            <ul style="padding:0; margin: 0;">
                                <li class="list-group-item">' . $that->language->get('usps_entry_height') . ' ' . $pData['height'] . ' ' . $pData['dimension_unit'] . '</li>
                                <li class="list-group-item">' . $that->language->get('usps_entry_width') . ' ' . $pData['width'] . ' ' . $pData['dimension_unit'] . '</li>
                                <li class="list-group-item">' . $that->language->get('usps_entry_depth') . ' ' . $pData['depth'] . ' ' . $pData['dimension_unit'] . '</li>
                                <li class="list-group-item">' . $that->language->get('usps_entry_weight') . ' ' . round((float)$pData['weight'], 4) . ' ' . $pData['weight_unit'] . '</li>
                            </ul>
                        </div>' .
                        (empty($data['usps_data']['shipmentId'])
                        ? '<a href="' . $that->html->getSecureURL('sale/order/history', '&order_id=' . $orderId) . '" class="padding10 ">'
                            . $that->language->getAndReplace(
                                'usps_text_print_label_suggestion',
                                replaces: $that->order_status->getStatusById($this->getManifestOrderStatusId($that))
                            ) . '</a>'
                            : ''),
                ]
            );
        }

        if (!empty($data['usps_data']['shipmentId'])) {
            foreach ((array)$data['usps_data']['packages'] as $k => $package) {
                $form['shipping_fields']['label' . $k] = $that->html->buildElement(
                    [
                        'type'   => 'button',
                        'href'   => $that->html->getSecureUrl(
                            'extension/usps/label',
                            '&' . http_build_query(['order_id' => $orderId, 'tn' => $package['tracking_number']])
                        ),
                        'text'   => $that->language->get('usps_text_print_label') . ' ' . $package['tracking_number'],
                        'title'  => $that->language->get('usps_text_print_label_title'),
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
        /** @var ModelExtensionUsps $mdl */
        $mdl = $that->load->model('extension/usps', 'storefront');
        return $mdl->getOrderShippingData($order_id);
    }
}
