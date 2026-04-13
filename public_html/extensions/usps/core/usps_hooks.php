<?php
/**
 * $Id$
 *
 * AbanteCart, Ideal OpenSource Ecommerce Solution
 * http://www.AbanteCart.com
 *
 * Copyright © 2011-2026 Belavier Commerce LLC
 *
 * This source file is subject to Open Software License (OSL 3.0)
 * License details are bundled with this package in the file LICENSE.txt.
 * It is also available at this URL:
 * <http://www.opensource.org/licenses/OSL-3.0>
 *
 * UPGRADE NOTE:
 *   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *   versions in the future. If you wish to customize AbanteCart for your
 *   needs, please refer to http://www.AbanteCart.com for more information.
 */
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_shipment_service.php');

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
        if ($this->baseObject_method == 'history') {
            $this->injectAesItnFieldForHistory($that);
        }
        if (isset($that->session->data['usps_success'])) {
            if (!empty($that->session->data['success'])) {
                $that->session->data['success'] .= '<br>';
            }
            $that->session->data['success'] .= $that->session->data['usps_success'];
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
            $this->persistPostedAesItnOverride($that, $that->request->get['order_id']);
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
        $uspsData = (array)($shipping_data['data']['usps_data'] ?? []);
        $packages = (array)($uspsData['packages'] ?? []);

        foreach ($packages as $idx => $package) {
            $trackUrl = UspsShipmentService::resolveTrackUrl((array)$package, $uspsData, (int)$idx);
            if ($trackUrl === '') {
                continue;
            }
            $track_btn = $that->html->buildElement(
                [
                    'type'  => 'button',
                    'href'  => $trackUrl,
                    'attr'  => 'target="_blank"',
                    'text'  => 'Tracking',
                    'icon'  => 'fa fa-search',
                    'style' => 'btn btn-default pull-right mr10',
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
            $shipmentService = new UspsShipmentService(Registry::getInstance());
            $result = $shipmentService->createShipment($order_info);
            if (!$result) {
                $that->messages->saveWarning(
                    'Order ID ' . $order_id . ' USPS Shipment Creation Warning',
                    implode("\n", (array)$shipmentService->errors)
                );
                $that->log->write(
                    'Order ID '
                    . $order_id . ' USPS Shipment Creation Warning: '
                    . implode("\n", (array)$shipmentService->errors)
                );
            }
        }
    }

    protected function orderStatusChanged($orderId, $orderStatusId)
    {
        /** @var ControllerPagesSaleOrder $that */
        $that = $this->baseObject;
        $that->loadLanguage('usps/usps');
        $order_data = $this->getOrderShippingData($that, $orderId);
        $data = $order_data['data'];
        if (!isset($data['usps_data'])) {
            return;
        }

        if (!isset($data['usps_data']['shipmentId'])) {
            if ($orderStatusId == $this->getManifestOrderStatusId($that)) {
                $order_info = $that->model_sale_order->getOrder($orderId);
                $shipmentService = new UspsShipmentService(Registry::getInstance());
                $result = $shipmentService->createShipment($order_info);
                if (!$result) {
                    $that->error = (array)$shipmentService->errors;
                    $that->session->data['error_warning'] = implode("\n", $that->error);
                } else {
                    $link = $that->html->getSecureURL('sale/order/address', '&order_id=' . $orderId);
                    $trackingNumber = ($shipmentService->lastShipment['tracking_number'] ?? '');
                    $that->session->data['usps_success'] =
                        $that->language->getAndReplace(
                            'usps_shipment_created_success_message',
                            replaces: [$trackingNumber, $link]
                        );
                    if ($that->session->data['usps_success'] === 'usps_shipment_created_success_message') {
                        $that->session->data['usps_success'] =
                            'USPS Shipment has been successfully created.'
                            . ($trackingNumber !== '' ? ' Tracking number: ' . $trackingNumber . '.' : '')
                            . ' See more info <a href="'
                            . $link
                            . '">here</a>';
                    }
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
            $labelSettingsConfigured = $this->hasUspsLabelSettingsConfigured($that);
            $suggestionText = $labelSettingsConfigured
                ? $that->language->getAndReplace(
                    'usps_text_print_label_suggestion',
                    replaces: $that->order_status->getStatusById($this->getManifestOrderStatusId($that))
                )
                : $that->language->get('usps_text_print_label_missing_settings');

            $suggestionHref = $labelSettingsConfigured
                ? $that->html->getSecureURL('sale/order/history', '&order_id=' . $orderId)
                : $that->html->getSecureURL('extension/extensions/edit', '&extension=usps');

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
                        ? '<a href="' . $suggestionHref . '" class="padding10 ">'
                            . $suggestionText . '</a>'
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
                            'r/extension/usps/label',
                            '&' . http_build_query(['order_id' => $orderId, 'tn' => $package['tracking_number']])
                        ),
                        'text'   => $that->language->get('usps_text_print_label') . ' ' . $package['tracking_number'],
                        'title'  => $that->language->get('usps_text_print_label_title'),
                        'style'  => 'btn btn-info fa fa-print',
                        'target' => '_blank',
                    ]
                );

                $trackUrl = UspsShipmentService::resolveTrackUrl((array)$package, (array)$data['usps_data'], (int)$k);
                if ($trackUrl !== '') {
                    $form['shipping_fields']['track' . $k] = $that->html->buildElement(
                        [
                            'type'   => 'button',
                            'href'   => $trackUrl,
                            'text'   => 'Tracking ' . $package['tracking_number'],
                            'title'  => 'Tracking',
                            'style'  => 'btn btn-default fa fa-search',
                            'target' => '_blank',
                        ]
                    );
                }
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

    protected function hasUspsLabelSettingsConfigured($that): bool
    {
        $requiredKeys = [
            'usps_payment_crid',
            'usps_payment_mid',
            'usps_payment_manifest_mid',
            'usps_payment_account_number',
        ];

        foreach ($requiredKeys as $key) {
            if (trim((string)$that->config->get($key)) === '') {
                return false;
            }
        }

        return true;
    }

    protected function persistPostedAesItnOverride($that, $orderId): void
    {
        if (!$orderId || !$that->request->is_POST()) {
            return;
        }
        $value = ($that->request->post['usps_aesitn'] ?? '');
        if ($value === '') {
            return;
        }

        /** @var ModelExtensionUsps $mdl */
        $mdl = $that->load->model('extension/usps', 'storefront');
        $existing = (array)$mdl->getOrderShippingData($orderId);
        $uspsData = (array)($existing['data']['usps_data'] ?? []);
        $uspsData['shipment_overrides']['aesitn'] = $value;
        $mdl->saveOrderShippingData($orderId, $uspsData);
    }

    protected function injectAesItnFieldForHistory($that): void
    {
        $orderId = ($that->request->get['order_id'] ?? '');
        if (!$orderId) {
            return;
        }
        $that->loadModel('sale/order');
        $orderInfo = (array)$that->model_sale_order->getOrder($orderId);
        $isUsps = str_contains(($orderInfo['shipping_method_key'] ?? ''), 'usps.');
        $shippingIsoCode = ($orderInfo['shipping_iso_code_2'] ?? '');
        $isInternational = strtoupper($shippingIsoCode) !== 'US';
        if (!$isUsps || !$isInternational) {
            return;
        }

        $manifestStatusId = $this->getManifestOrderStatusId($that);
        /** @var ModelExtensionUsps $mdl */
        $mdl = $that->load->model('extension/usps', 'storefront');
        $shippingData = (array)$mdl->getOrderShippingData($orderId);
        $uspsData = (array)($shippingData['data']['usps_data'] ?? []);
        $savedValue = ($uspsData['shipment_overrides']['aesitn'] ?? $uspsData['compliance']['aesitn'] ?? '');
        $inputValue = htmlspecialchars($savedValue, ENT_QUOTES, 'UTF-8');

        $html = '
<div class="form-group" id="usps_aesitn_group" style="display:none;">
    <label class="control-label col-sm-3 col-xs-12" for="orderFrm_usps_aesitn">AES/ITN</label>
    <div class="input-group afield col-sm-7 col-xs-12">
        <input type="text"
               class="form-control large-field"
               id="orderFrm_usps_aesitn"
               name="usps_aesitn"
               value="' . $inputValue . '"
               placeholder="NO EEI 30.37(a) or ITN value" />
        <span class="help-block">Required for USPS International Label. Example: NO EEI 30.37(a)</span>
    </div>
</div>
<script>
(function() {
    function toggleUspsAesItnField() {
        var sel = document.getElementById("orderFrm_order_status_id");
        var group = document.getElementById("usps_aesitn_group");
        if (!sel || !group) return;
        group.style.display = (String(sel.value) === "' . (int)$manifestStatusId . '") ? "" : "none";
    }
    document.addEventListener("DOMContentLoaded", function() {
        var sel = document.getElementById("orderFrm_order_status_id");
        toggleUspsAesItnField();
        if (sel) {
            sel.addEventListener("change", toggleUspsAesItnField);
        }
    });
})();
</script>';
        $that->view->addHookVar('hk_order_comment_pre', $html);
    }
}
