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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerResponsesSaleInvoice extends AController
{

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('sale/order');

        $this->data['title'] = $this->language->get('heading_title');
        $this->data['css_url'] = RDIR_TEMPLATE . 'stylesheet/invoice.css';

        $this->data['base'] = HTTPS_SERVER;
        $this->data['direction'] = $this->language->get('direction');
        $this->data['language'] = $this->language->get('code');

        $this->data['text_invoice'] = $this->language->get('text_invoice');

        $this->data['text_order_id'] = $this->language->get('text_order_id');
        $this->data['text_invoice_id'] = $this->language->get('text_invoice_id');
        $this->data['text_date_added'] = $this->language->get('text_date_added');
        $this->data['text_telephone'] = $this->language->get('text_telephone');
        $this->data['text_fax'] = $this->language->get('text_fax');
        $this->data['text_to'] = $this->language->get('text_to');
        $this->data['text_ship_to'] = $this->language->get('text_ship_to');

        $this->data['column_product'] = $this->language->get('column_product');
        $this->data['column_model'] = $this->language->get('column_model');
        $this->data['column_quantity'] = $this->language->get('column_quantity');
        $this->data['column_price'] = $this->language->get('column_price');
        $this->data['column_total'] = $this->language->get('column_total');
        $this->data['column_comment'] = $this->language->get('column_comment');

        $logo = $this->config->get('config_logo_' . $this->language->getLanguageID())
            ?: $this->config->get('config_logo');
        $result = getMailLogoDetails($logo);
        $this->data['logo'] = $result['html'] ?: HTTPS_DIR_RESOURCE . $logo;

        $this->loadModel('sale/order');
        $orders = $this->data['orders'] = [];

        if (isset($this->request->post['selected'])) {
            $orders = $this->request->post['selected'];
        } elseif (isset($this->request->get['order_id'])) {
            $orders[] = $this->request->get['order_id'];
        }

        foreach ($orders as $order_id) {
            $orderInfo = $this->model_sale_order->getOrder($order_id);
            if (!$orderInfo) {
                continue;
            }

            $invoice_id = $orderInfo['invoice_id'] ? $orderInfo['invoice_prefix'] . $orderInfo['invoice_id'] : '';

            $customer = new ACustomer($this->registry);
            $shAddressArray = $this->extractAddressOrderData($orderInfo, 'shipping');
            $shAddressArray['ext_fields'] = $this->extractAddressExtendedData((array)$orderInfo['ext_fields'], 'shipping');
            $shippingFormattedAddress = $customer->getFormattedAddress(
                $shAddressArray,
                $orderInfo['shipping_address_format']
            );

            $pmAddressArray = $this->extractAddressOrderData($orderInfo, 'payment');
            $pmAddressArray['ext_fields'] = $this->extractAddressExtendedData((array)$orderInfo['ext_fields'], 'payment');
            $paymentFormattedAddress = $customer->getFormattedAddress(
                $pmAddressArray,
                $orderInfo['payment_address_format']
            );

            $product_data = [];
            $orderProducts = $this->model_sale_order->getOrderProducts($order_id);
            $this->loadModel('setting/setting');
            $storeSettings = $this->model_setting_setting->getSetting('details', $orderInfo['store_id']);

            $orderProductIds = array_column($orderProducts, 'product_id');
            $resource = new AResource('image');
            $thumbnails = $resource->getMainThumbList(
                'products',
                $orderProductIds,
                $this->config->get('config_image_cart_width'),
                $this->config->get('config_image_cart_height')
            );

            foreach ($orderProducts as $product) {
                $option_data = [];
                $options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);
                foreach ($options as $option) {
                    $option_data[] = [
                        'name'  => $option['name'],
                        'value' => $option['value'],
                    ];
                }

                $imgFile = str_replace(AUTO_SERVER, DIR_ROOT . DS, $thumbnails[(int)$product['product_id']]['thumb_url']);
                $thumbnailUrl = is_file($imgFile)
                    ? 'data:' . mime_content_type($imgFile) . ';base64,' . base64_encode(file_get_contents($imgFile))
                    : '';

                $product_data[] = [
                    'name'          => $product['name'],
                    'thumbnail_url' => $thumbnailUrl,
                    'model'         => $product['model'],
                    'option'        => $option_data,
                    'quantity'      => $product['quantity'],
                    'price'         => $this->currency->format(
                        $product['price'],
                        $orderInfo['currency'],
                        $orderInfo['value']
                    ),
                    'total'         => $this->currency->format_total(
                        $product['price'],
                        $product['quantity'],
                        $orderInfo['currency'],
                        $orderInfo['value']
                    ),
                ];
            }

            $total_data = $this->model_sale_order->getOrderTotals($order_id);
            $zoneName = '';
            if ($storeSettings['config_zone_id']) {
                $this->loadModel('localisation/zone');
                $zone = $this->model_localisation_zone->getZone($storeSettings['config_zone_id']);
                if ($zone) {
                    $zoneName = $zone['name'];
                }
            }
            $countryName = '';
            if ($storeSettings['config_country_id']) {
                $this->loadModel('localisation/country');
                $country = $this->model_localisation_country->getCountry($storeSettings['config_country_id']);
                if ($country) {
                    $countryName = $country['name'];
                }
            }

            $this->data['orders'][] = array_merge(
                $orderInfo,
                [
                    'order_id'           => $order_id,
                    'invoice_id'         => $invoice_id,
                    'date_added'         => dateISO2Display(
                        $orderInfo['date_added'],
                        $this->language->get('date_format_short')
                    ),
                    'store_url'          => rtrim($orderInfo['store_url'], '/'),
                    'address'            => nl2br($storeSettings['config_address']),
                    'city'               => nl2br($storeSettings['config_city']),
                    'postcode'           => nl2br($storeSettings['config_postcode']),
                    'zone'               => $zoneName,
                    'country'            => $countryName,
                    'telephone'          => $storeSettings['config_telephone'],
                    'fax'                => $storeSettings['config_fax'],
                    'email'              => $storeSettings['store_main_email'],
                    'shipping_address'   => $shippingFormattedAddress,
                    'payment_address'    => $paymentFormattedAddress,
                    'customer_email'     => $orderInfo['email'],
                    'customer_telephone' => $orderInfo['telephone'],
                    'product'            => $product_data,
                    'total'              => $total_data,
                ]);

        }

        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/sale/order_invoice.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * @param array $orderData
     * @param string $prefix
     * @return array
     */
    protected function extractAddressOrderData(array $orderData, string $prefix = 'shipping')
    {
        $output = [];
        foreach ($orderData as $key => $value) {
            if (str_starts_with($key, $prefix . '_')) {
                $output[str_replace($prefix . '_', '', $key)] = $value;
            }
        }
        return $output;
    }

    /**
     * Extracts extended address data based on a specific type prefix.
     *
     * @param array $extFields An associative array of extended fields containing keys and values.
     * @param string $prefix The prefix type that determines which keys to extract, default value is 'shipping'.
     * @return array An associative array containing the extracted extended address data with the type prefix removed from keys.
     */
    protected function extractAddressExtendedData(array $extFields, string $prefix = 'shipping')
    {
        $output = [];
        foreach ($extFields as $key => $value) {
            if (str_starts_with($key, $prefix . '_')) {
                $output[str_replace($prefix . '_', '', $key)] = $value;
            }
        }
        return $output;
    }

    public function generate()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->user->canModify('sale/invoice')) {
            $error = new AError('');
            $error->toJSONResponse(
                'NO_PERMISSIONS_402',
                [
                    'error_text'  => sprintf($this->language->get('error_permission_modify'), 'sale/invoice'),
                    'reset_value' => true,
                ]
            );
            return;
        }

        $this->loadModel('sale/order');

        $json = [];

        if (isset($this->request->get['order_id'])) {
            $json['invoice_id'] = $this->model_sale_order->generateInvoiceId($this->request->get['order_id']);
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }
}