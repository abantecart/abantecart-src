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

/** @noinspection PhpUndefinedClassInspection */

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * @property ModelExtensionPaypalCommerce $model_extension_paypal_commerce
 */
class ControllerResponsesExtensionPaypalCommerce extends AController
{
    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        if (isset($this->session->data['fc'])) {
            $cartClassName = get_class($this->cart);
            $this->registry->set(
                'cart',
                new $cartClassName($this->registry, $this->session->data['fc'])
            );
        }
        $this->loadModel('checkout/order');
        $this->extensions->hk_ProcessData($this, __FUNCTION__);
    }

    public function main()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->document->addStyle(
            [
                'href' => $this->view->templateResource('/css/paypal_commerce.css'),
                'rel' => 'stylesheet',
                'media' => 'screen',
            ]
        );

        $this->loadLanguage('paypal_commerce/paypal_commerce');
        $this->load->model('checkout/order');
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $this->load->model('extension/paypal_commerce');
        $data['client_token'] = $mdl->getClientToken();
        $data['bn_code'] = $mdl->getBnCode();
        if (!$data['client_token']) {
            $data['error'] = 'Cannot to obtain client token from Paypal API. Incident has been reported.';
            $this->messages->saveError('Paypal Commerce API Error', $data['error'] . "\nSee error log for details.");
        }

        $data['intent'] = $this->config->get('paypal_commerce_transaction_type');
        $template = 'responses/paypal_commerce_confirm.tpl';

        $data['action'] = $this->html->getSecureURL('r/extension/paypal_commerce/send');
        $data['create_order_url'] = $this->html->getSecureURL('r/extension/paypal_commerce/createOrder');
        $data['capture_order_url'] = $this->html->getSecureURL('r/extension/paypal_commerce/captureOrder');

        //build submit form
        $form = new AForm();
        $form->setForm(['form_name' => 'paypalFrm']);
        $data['form_open'] = $form->getFieldHtml(
            [
                'type' => 'form',
                'name' => 'paypalFrm',
                'attr' => 'class = "form-horizontal validate-creditcard"',
                'csrf' => true,
            ]
        );

        $get = $this->request->get;
        unset($get['rt']);
        if(!isset($get['product_key']) && $this->session->data['fc']['single_checkout'] && $this->session->data['fc']['product_key']){
            $get['product_key'] = $this->session->data['fc']['product_key'];
            $get['fc'] = $get['single_checkout'] = 1;
        }
        $data['cancel_url'] = $this->html->getSecureURL(
            'checkout/fast_checkout',
            $get ? '&'.http_build_query($get, '', '&') : ''
        );

        $this->view->batchAssign($data);
        $this->processTemplate($template);
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function send()
    {
        $output = [];
        if (!$this->csrftoken->isTokenValid()) {
            $output['error'] = $this->language->get('error_unknown');
            $this->load->library('json');
            $this->response->addJSONHeader();
            $this->response->setOutput(AJson::encode($output));
            return;
        }

        $this->extensions->hk_InitData($this, __FUNCTION__);

        //swap cart
        if ($this->session->data['paypal_cart']['cart']) {
            $cartClass = get_class($this->cart);
            $this->registry->set('cart', new $cartClass($this->registry, $this->session->data['paypal_cart']));
            unset($this->session->data['used_balance']);
            $this->session->data['cart_key'] = 'paypal_cart';
        }

        $output = $this->processGenericOrder();

        if (isset($output['error'])) {
            if ($output['error']) {
                $csrftoken = $this->registry->get('csrftoken');
                $output['csrfinstance'] = $csrftoken->setInstance();
                $output['csrftoken'] = $csrftoken->setToken();
            }
        }
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($output));
    }
    public function createOrder()
    {
        if(!$this->request->is_POST()){
            http_response_code(406);
            return;
        }
        $output = [];
        if (!$this->csrftoken->isTokenValid()) {
            $output['error'] = $this->language->get('error_unknown');
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($output));
            return;
        }

        $this->extensions->hk_InitData($this, __FUNCTION__);

        //swap cart
        if ($this->session->data['paypal_cart']['cart']) {
            $cartClass = get_class($this->cart);
            $this->registry->set('cart', new $cartClass($this->registry, $this->session->data['paypal_cart']));
            unset($this->session->data['used_balance']);
            $this->session->data['cart_key'] = 'paypal_cart';
        }

        $currencyCode = $this->currency->getCode();
        $decPlace = (int)$this->currency->getCurrency()['decimal_place'];
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $this->loadModel('extension/paypal_commerce');

        $data['intent'] = $this->config->get('paypal_commerce_transaction_type');
        //need an order details
        $data['order_info'] = $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $orderTotal = "" . round($this->currency->convert(
                (float)$order_info['total'],
                $this->config->get('config_currency'),
                $currencyCode
            ),
            $decPlace);

        $taxes = $discount = $handling_fee = 0.0;
        foreach ($this->cart->getFinalTotalData() as $total) {
            $data['order_' . $total['id']] = $this->currency->convert(
                (float)$total['value'],
                $this->config->get('config_currency'),
                $currencyCode
            );

            if ($total['total_type'] == 'discount' || $total['total_type'] == 'coupon' || $total['total_type'] == 'balance') {
                $discount += abs($data['order_' . $total['id']]);
            } elseif ($total['total_type'] == 'fee' || str_ends_with($total['total_type'], '_fee')) {
                $handling_fee += abs($data['order_' . $total['id']]);
            } elseif ($total['total_type'] == 'tax') {
                $taxes += $data['order_' . $total['id']];
            }
        }
        $amountBreakdown = [
            'item_total' => [
                'value' => "" . round($data['order_subtotal'],$decPlace),
                'currency_code' => $currencyCode,
            ],
            'tax_total' => [
                'value' => "" . round($taxes,$decPlace),
                'currency_code' => $currencyCode,
            ],
            'shipping' => [
                'value' => "" . round($data['order_shipping'],$decPlace),
                'currency_code' => $currencyCode,
            ],
            'discount' => [
                'value' => "" . round($discount,$decPlace),
                'currency_code' => $currencyCode,
            ],
            'handling' => [
                'value' => "" . round($handling_fee,$decPlace),
                'currency_code' => $currencyCode,
            ],
        ];

        $cartProducts = $this->cart->getProducts() + $this->cart->getVirtualProducts();

        $ppData['intent'] = strtoupper($this->config->get('paypal_commerce_transaction_type'));
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');

        //shipping address
        $shipping = [];
        if ($order_info['shipping_country_id']) {
            $countryId = $order_info['shipping_country_id'];

            $country = $this->model_localisation_country->getCountry($countryId);
            if ($country) {
                $shipping['name']['full_name'] = $order_info['shipping_firstname']
                    . ' '
                    . $order_info['shipping_lastname'];

                $shipping['address'] = [
                    'country_code' => $country['iso_code_2'],
                ];
                $zoneInfo = $this->model_localisation_zone->getZone($order_info['shipping_zone_id']);
                if($zoneInfo && $zoneInfo['code']){
                    $shipping['address']['admin_area_1'] = $zoneInfo['code'];
                }

                $addressType = 'shipping';
                $flds = [
                    'address_line_1' => $addressType . '_address_1',
                    'address_line_2' => $addressType . '_address_2',
                    'admin_area_2' => $addressType . '_city',
                    'postal_code' => $addressType . '_postcode',
                ];

                foreach ($flds as $n => $alias) {
                    if ($order_info[$alias]) {
                        $shipping['address'][$n] = $order_info[$alias];
                    }
                }
            }
        }

        $ppData['application_context']['cancel_url']
            = $ppData['application_context']['return_url']
            = $this->html->getSecureURL('checkout/fast_checkout');

        //get tax per item lines
        $taxExts = $this->extensions->getInstalled('tax');
        $enabledExts = $this->extensions->getEnabledExtensions();
        $taxLines = [];
        if ($taxExts) {
            foreach ($taxExts as $extTextId) {
                if (!in_array($extTextId, $enabledExts)) {
                    continue;
                }
                $mdl = $this->loadModel('total/' . $extTextId . '_total');
                if (!$mdl || !method_exists($mdl, 'getTaxLines')) {
                    continue;
                }
                $taxLines[$extTextId] = $mdl->getTaxLines();
                // around paypal api bug related to shipping tax. Move it into handling
                if ($taxLines[$extTextId]) {
                    foreach ($taxLines[$extTextId] as $ln) {
                        if ($ln['line_type'] == 'shipping') {
                            $amountBreakdown['handling']['value'] += $ln['tax_amount'];
                            $amountBreakdown['tax_total']['value'] -= $ln['tax_amount'];
                        }
                    }
                }
            }
        }

        $i = 0;
        $items = $orderDescription = [];

        foreach ($cartProducts as $product) {
            $sku = $product['sku'];
            $description = '';
            if ($product['option']) {
                foreach ($product['option'] as $opt) {
                    $title = strip_tags($opt['value']);
                    $title = str_replace('\r\n', "\n", $title);
                    $description .= $opt['name'] . ':' . $title . "; ";
                    if (mb_strlen($description) > 120) {
                        $description = mb_substr($description, 0, 115) . '...';
                    }
                    if ($opt['sku']) {
                        $sku .= ' ' . $opt['sku'];
                    }
                }
            }

            $orderDescription[] = [
                'title' => $product['name'] . ' ' . $description,
                'quantity' => $product['quantity'],
                'sku' => $sku
            ];


            // comment fot yet. Paypal requires amount breakdown for each line including tax amount per item (tax amount for 1 piece).
            // Then PP api calculates tax * quantity and then the compares this sum with total_tax_amount.
            //Approach for tax amount calculation of abc and pp are different.
            //We cannot to solve this issue yet.
            //Also found pp api bug with shipping tax. It's not contains it at all. Can be solved with handling fee.
            $items[$i] = [
                'name' => $product['name'],
                'unit_amount' => [
                    'value' => "".round($this->currency->convert(
                        $product['price'],
                        $this->config->get('config_currency'),
                        $currencyCode
                    ),2),
                    'currency_code' => $currencyCode,
                ],
                'quantity' => $product['quantity']
            ];


            if ($description) {
                $items[$i]['description'] = $description;
            }
            if ($sku) {
                $items[$i]['sku'] = $sku;
            }
            $i++;
        }

        // cut description (paypal api requirements. See Order->create->purchase_units->description)
        $charsPerItem = round(120 / count($cartProducts));
        $order_description = '';
        foreach ($orderDescription as $desc) {
            $postfix = ' x ' . $desc['quantity'];
            if (mb_strlen($desc['title']) > ($charsPerItem - strlen($postfix))) {
                $order_description .= mb_substr($desc['title'], 0, ($charsPerItem - strlen($postfix) - 3)) . '...' . $postfix . "  ";
            } else {
                $order_description .= $desc['title'] . ' ' . $postfix . "\n";
            }
        }
        //this description cannot be more than 127 chars length
        $order_description = mb_strlen($order_description)>127
            ? mb_substr($order_description,0,127 )
            : $order_description;

        $ppData['purchase_units'][0] = [
            'custom_id' =>  $this->session->data['order_id'].'-'.UNIQUE_ID,
            'amount' => [
                'value' => $orderTotal,
                'currency_code' => $currencyCode
            ],
            'shipping' => $shipping,
            'description' => $order_description
        ];
        //allow breakdown only for store currency to avoid conversion problems
        if($this->config->get('config_currency') == $currencyCode){
            $ppData['purchase_units'][0]['amount']['breakdown'] = $amountBreakdown;
            $ppData['purchase_units'][0]['items'] =  $items;
        }

        try {
            $output = (array)$mdl->createPPOrder($ppData);
        }catch(\PayPalHttp\HttpException|Error $e){
            $this->log->write('PaypalCommerce order creation error: '.$e->getMessage()."\n Input Data: ".var_export($ppData, true));
            $output['error'] = $e->getMessage();
        }

        if (isset($output['error'])) {
            if ($output['error']) {
                http_response_code(406);
            }
        }

        $csrftoken = $this->registry->get('csrftoken');
        $output['csrfinstance'] = $csrftoken->setInstance();
        $output['csrftoken'] = $csrftoken->setToken();

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($output));
    }
    public function captureOrder()
    {
        if(!$this->request->is_POST()){
            http_response_code(406);
            return;
        }

        $json = file_get_contents('php://input');
        $json = (array)json_decode($json, true);
        $ppOrderId = $json['orderID'];

        $output = [];
        if (!$this->csrftoken->isTokenValid()) {
            $output['error'] = $this->language->get('error_unknown');
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($output));
            return;
        }

        $this->extensions->hk_InitData($this, __FUNCTION__);

        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $this->loadModel('extension/paypal_commerce');
        try {
            if($this->config->get('paypal_commerce_transaction_type')=='capture') {
                $output = (array)$mdl->capturePPOrder($ppOrderId);
                $output = ['id' => $output->id];
            }else{
                $output = $mdl->authorizePPOrder($ppOrderId);
                $output = ['id' => $output->id];
            }
        }catch(Exception|Error $e){
            $output['error'] = $e->getMessage();
        }

        if (isset($output['error'])) {
            if ($output['error']) {
                http_response_code(406);
            }
        }

        $csrftoken = $this->registry->get('csrftoken');
        $output['csrfinstance'] = $csrftoken->setInstance();
        $output['csrftoken'] = $csrftoken->setToken();

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($output));
    }

    protected function processGenericOrder()
    {
        $output = [];
        $this->load->model('checkout/order');
        $orderId = $this->session->data['order_id'];
        $order_info = $this->model_checkout_order->getOrder($orderId);
        if (!$order_info) {
            $output['error'] = $this->language->get('error_unknown');
            $err = new AError(
                "Paypal Commerce: " . __CLASS__ . "::" . __METHOD__ . ": Order #" . $orderId . " not found"
            );
            $err->toLog()->toDebug();
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($output));
        }

        $orderTotalAmt = $this->currency->convert(
            $order_info['total'],
            $this->config->get('config_currency'),
            $order_info['currency']
        );
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $this->loadModel('extension/paypal_commerce');
        $transactionDetails = json_decode(
            html_entity_decode(
                $this->request->post['transaction_details'],
                ENT_QUOTES,
                'UTF-8'
            ),
            true
        );
        $response = $mdl->getOrder($transactionDetails['id']);

        if (!$response) {
            $output['error'] = 'Cannot establish a connection to the server';
            $err = new AError(
                'Paypal Commerce: Cannot Get Order Information from Paypal. '
                . 'Paypal Transaction Id: ' . $transactionDetails['id']
            );
            $err->toLog()->toDebug();
        } //validate order info before confirmation
        elseif (
            $response->purchase_units[0]->custom_id != $order_info['order_id'].'-'.UNIQUE_ID
            || $response->purchase_units[0]->amount->currency_code != $order_info['currency']
            || $response->purchase_units[0]->amount->value != $orderTotalAmt
        ) {
            $output['error'] = $this->language->get('error_unknown');
            $err = new AError(
                "Paypal Commerce: Suspect Order Confirmation Request: "
                . "\n Order Transaction Details:" . var_export($response, true) . "\n"
                . "Requested Order Info:\n" . var_export($order_info, true)
            );
            $err->toLog()->toDebug();
        } else {
            if ($response->status == 'COMPLETED') {
                $this->model_checkout_order->confirm(
                    $orderId,
                    $this->order_status->getStatusByTextId('pending')
                );

                $this->model_checkout_order->updatePaymentMethodData(
                    $orderId,
                    serialize($response)
                );

                $mdl->savePaypalCustomer($this->customer->getId(), $transactionDetails['payer']['payer_id']);
                $mdl->savePaypalOrder(
                    $orderId,
                    [
                        'id' => $transactionDetails['id'],
                        'transaction_id' => $transactionDetails['id'],
                    ]
                );
                try {
                    $output['success'] = $this->html->getSecureURL('checkout/finalize');
                } catch (Exception $e) {
                    $this->log->write(__FILE__ . ':' . __LINE__ . '   - ' . $e->getMessage() . "\n\n" . $e->getTraceAsString());
                    $output['error'] = 'Oops, Unexpected Application Error';
                }
            } else {
                $output['error'] = 'Oops, Unexpected Application Error';
                $this->log->write(var_export($response, true));
            }
        }
        return $output;
    }

    protected function _get_products_data($order_info)
    {
        $this->load->library('encryption');
        $encryption = new AEncryption($this->config->get('encryption_key'));

        $this->data['products'] = [];
        $this->data['items_total'] = 0.0;
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $option_data = [];

            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['value'];
                } else {
                    $filename = $encryption->decrypt($option['value']);
                    $value = mb_substr($filename, 0, mb_strrpos($filename, '.'));
                }

                $option_data[] = [
                    'name' => $option['name'],
                    'value' => (mb_strlen($value) > 20 ? mb_substr($value, 0, 20) . '..' : $value),
                ];
            }
            $price = $this->currency->format($product['price'], $order_info['currency'], $order_info['value'], false);
            $this->data['products'][] = [
                'name' => $product['name'],
                'model' => $product['model'],
                'price' => $price,
                'quantity' => $product['quantity'],
                'option' => $option_data,
                'weight' => $product['weight'],
                'weight_type' => $product['weight_type'],
            ];
            $this->data['items_total'] += $price * $product['quantity'];
        }

        //check for virtual product such as gift certificate
        $virtual_products = $this->cart->getVirtualProducts();

        if ($virtual_products) {
            foreach ($virtual_products as $virtual) {
                $this->data['products'][] = [
                    'name' => ($virtual['name'] ?: 'Virtual Product'),
                    'model' => '',
                    'price' => $this->currency->format(
                        $virtual['amount'],
                        $order_info['currency'],
                        $order_info['value'],
                        false
                    ),
                    'quantity' => ($virtual['quantity'] ?: 1),
                    'option' => [],
                    'weight' => 0,
                ];
                $this->data['items_total'] += ($virtual['quantity'] ?: 1)
                    * $this->currency->format($virtual['amount'], $order_info['currency'], $order_info['value'], false);
            }
        }

        $this->data['discount_amount_cart'] = 0;
        $totals = $this->cart->buildTotalDisplay();

        foreach ($totals['total_data'] as $total) {
            if (in_array($total['id'], ['subtotal', 'total'])) {
                continue;
            }

            if (in_array($total['id'], ['promotion', 'coupon'])) {
                $total['value'] = $total['value'] < 0 ? $total['value'] * -1 : $total['value'];
                $this->data['discount_amount_cart'] += $total['value'];
            } else {
                $price = $this->currency->format($total['value'], $order_info['currency'], $order_info['value'], false);

                if ($total['total_type'] == 'tax') {
                    $this->data['tax_total'] += $price;
                } elseif ($total['total_type'] == 'shipping') {
                    $this->data['shipping_total'] += $price;
                } elseif (in_array($total['total_type'], ['handling', 'fee'])) {
                    $this->data['handling_total'] += $price;
                } else {
                    $this->data['items_total'] += $price;
                }

                $this->data['products'][$total['id']] = [
                    'name' => $total['title'],
                    'model' => '',
                    'price' => $price,
                    'quantity' => 1,
                    'option' => [],
                    'weight' => 0,
                ];
            }
        }

        $calc_total = $this->data['items_total']
            + $this->data['shipping_total']
            + $this->data['tax_total']
            + $this->data['handling_total'];

        if (($calc_total - $order_info['order_total']) !== 0.0) {
            foreach ($totals['total_data'] as $total) {
                if (in_array($total['id'], ['subtotal', 'total', 'promotion', 'coupon'])) {
                    continue;
                }

                $price = $this->currency->format($total['value'], $order_info['currency'], $order_info['value'], false);
                $this->data['products'][$total['id']] = [
                    'name' => $total['title'],
                    'model' => '',
                    'price' => $price,
                    'quantity' => 1,
                    'option' => [],
                    'weight' => 0,
                ];
            }
        }

        if ($this->data['discount_amount_cart'] > 0) {
            $price = -1 * $this->currency->format(
                    $this->data['discount_amount_cart'], $order_info['currency'],
                    $order_info['value'], false
                );
            $this->data['products'][] = [
                'name' => $this->language->get('text_discount'),
                'model' => '',
                'price' => $price,
                'quantity' => 1,
                'option' => [],
                'weight' => 0,
            ];
            $this->data['items_total'] += $price;
        }

        return $this->data['products'];
    }

    /*
     *  WEBHOOKS SECTION
     */
    public function webhookAuthCreated()
    {
        $this->processWebHook(
            'PAYMENT.AUTHORIZATION.CREATED',
            $this->config->get('paypal_commerce_status_success_unsettled'),
        );
    }

    public function webhookAuthVoided()
    {
        $this->processWebHook(
            'PAYMENT.AUTHORIZATION.VOIDED',
            $this->order_status->getStatusByTextId('canceled'),
        );
    }

    public function webhookCaptureCompleted()
    {
        $this->processWebHook(
            'PAYMENT.CAPTURE.COMPLETED',
            $this->config->get('paypal_commerce_status_success_settled'),
        );
    }

    public function webhookCaptureDenied()
    {
        $this->processWebHook(
            'PAYMENT.CAPTURE.DENIED',
            $this->config->get('paypal_commerce_status_decline'),
        );
    }

    public function webhookCapturePending()
    {
        $this->processWebHook(
            'PAYMENT.CAPTURE.PENDING',
            $this->config->get('paypal_commerce_status_capture_pending'),
        );
    }

    public function webhookCaptureRefunded()
    {
        $this->processWebHook(
            'PAYMENT.CAPTURE.REFUNDED',
            $this->config->get('paypal_commerce_status_refund'),
        );
    }

    protected function processWebHook($eventName, $orderStatusId)
    {
        $inData = $this->parseWebhookData();
        if (!$this->webhookValidate($inData, $eventName)) {
            http_response_code('406');
            exit;
        }
        list($orderId, $uniqueId) = explode('-',$inData['parsed']['resource']['custom_id']);

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->model_checkout_order->update(
            $orderId,
            $orderStatusId,
            'Order updated by Paypal webhook request.'
        );
        //save input data into comments but hide from customer
        $this->model_checkout_order->addHistory(
            $orderId,
            $orderStatusId,
            "Paypal webhook " . $eventName . ": \n\nParsed data:\n" . var_export($inData['parsed'], true)
        );
    }

    protected function parseWebhookData()
    {
        $data = file_get_contents("php://input");
        $output = [];
        $output['parsed'] = json_decode($data, true);
        $output['raw'] = $data;
        return $output;
    }

    protected function webhookValidate($inData, $eventName)
    {
        if (!$inData['parsed']) {
            $this->log->write("Paypal webhook " . $eventName . ": incorrect incoming data! \n:" . var_export($inData['raw'], true));
            return false;
        }
        if ($inData['parsed']['event_type'] != $eventName) {
            $this->log->write("Paypal webhook processing: Wrong Event Type! Waiting for " . $eventName . "  but " . $inData['parsed']['event_type'] . " was given");
            return false;
        }

        list($orderId, $uniqueId) = explode('-',$inData['parsed']['resource']['custom_id']);
        $ppOrderId = $inData['parsed']['resource']['supplementary_data']['related_ids']['order_id'];
        $this->loadModel('checkout/order');
        $orderInfo = $this->model_checkout_order->getOrder($orderId);
        if (!$orderInfo) {
            $this->log->write(
                "Paypal webhook " . $eventName
                . ": order ID " . $orderId . " not found!\n Raw input data:\n"
                . var_export($inData['raw'], true)
                . "\n\nParsed data:\n" . var_export($inData['parsed'], true)
            );
            return false;
        }elseif(isset($ppOrderId)){
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $this->loadModel('extension/paypal_commerce');
            $ppOrderInfo = $mdl->getPaypalOrder($orderId);
            if($ppOrderInfo['charge_id'] != $ppOrderId){
                $this->log->write(
                    "Paypal webhook " . $eventName
                    . ": order ID " . $orderId
                    . " \n Paypal related OrderId: ".$ppOrderId." but not found in the database.\n"
                    . " \n Raw input data:\n"
                    . var_export($inData['raw'], true)
                    . "\n\nParsed data:\n" . var_export($inData['parsed'], true)
                );
                return false;
            }
        }
        return true;
    }
}