<?php
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
        $currencyCode = $this->currency->getCode();

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

        //need an order details
        $data['order_info'] = $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $data['order_total'] = $this->currency->convert(
            $order_info['total'],
            $currencyCode,
            $this->currency->getCode()
        );

        $taxes = $discount = $handling_fee = 0.0;
        foreach ($this->cart->getFinalTotalData() as $total) {
            $data['order_' . $total['id']] = $this->currency->convert(
                $total['value'],
                $this->config->get('config_currency'),
                $currencyCode
            );

            if ($total['total_type'] == 'discount' || $total['total_type'] == 'coupon' || $total['total_type'] == 'balance') {
                $discount += abs($data['order_' . $total['id']]);
            } elseif ($total['total_type'] == 'fee') {
                $handling_fee += abs($data['order_' . $total['id']]);
            } elseif ($total['total_type'] == 'tax') {
                $taxes += $data['order_' . $total['id']];
            }
        }
        $data['amountBreakdown'] = [
            'item_total' => [
                'value' => $data['order_subtotal'],
                'currency_code' => $currencyCode,
            ],
            'tax_total' => [
                'value' => $taxes,
                'currency_code' => $currencyCode,
            ],
            'shipping' => [
                'value' => (float)$data['order_shipping'],
                'currency_code' => $currencyCode,
            ],
            'discount' => [
                'value' => (float)$discount,
                'currency_code' => $currencyCode,
            ],
            'handling' => [
                'value' => (float)$handling_fee,
                'currency_code' => $currencyCode,
            ],
        ];

        $cartProducts = $this->cart->getProducts() + $this->cart->getVirtualProducts();

        $data['intent'] = $this->config->get('paypal_commerce_transaction_type');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');

        //shipping address
        if ($order_info['shipping_country_id']) {
            $countryId = $order_info['shipping_country_id'];
            $addressType = 'shipping';
            $country = $this->model_localisation_country->getCountry($countryId);
            if ($country) {
                $data['shipping'] = [];
                $data['shipping']['name']['full_name'] = $order_info['shipping_firstname']
                    . ' '
                    . $order_info['shipping_lastname'];

                $data['shipping']['address'] = [
                    'country_code' => $country['iso_code_2'],
                ];
                $zoneInfo = $this->model_localisation_zone->getZone($order_info['shipping_zone_id']);
                if($zoneInfo && $zoneInfo['code']){
                    $data['shipping']['address']['admin_area_1'] = $zoneInfo['code'];
                }

                $flds = [
                    'address_line_1' => $addressType . '_address_1',
                    'address_line_2' => $addressType . '_address_2',
                    'admin_area_2' => $addressType . '_city',
                    'postal_code' => $addressType . '_postcode',
                ];

                foreach ($flds as $n => $alias) {
                    if ($order_info[$alias]) {
                        $data['shipping']['address'][$n] = $order_info[$alias];
                    }
                }
            }
        }
        //billing address
        $countryId = $order_info['payment_country_id'];
        $addressType = 'payment';
        $country = $this->model_localisation_country->getCountry($countryId);
        if ($country) {
            $data['address'] = [
                'country_code' => $country['iso_code_2'],
            ];
            $zoneInfo = $this->model_localisation_zone->getZone($order_info['payment_zone_id']);
            if($zoneInfo && $zoneInfo['code']){
                $data['address']['admin_area_1'] = $zoneInfo['code'];
            }

            $flds = [
                'address_line_1' => $addressType . '_address_1',
                'address_line_2' => $addressType . '_address_2',
                'admin_area_2' => $addressType . '_city',
                'postal_code' => $addressType . '_postcode',
            ];
            foreach ($flds as $n => $alias) {
                if ($order_info[$alias]) {
                    $data['address'][$n] = $order_info[$alias];
                }
            }
        }

        $zone = $this->model_localisation_zone->getZoneDescriptions($order_info['payment_zone_id']);
        if ($zone) {
            $data['payment_zone_name'] = $zone[$this->language->getLanguageID()]['name'];
        }
        $template = 'responses/paypal_commerce_confirm.tpl';

        $data['action'] = $this->html->getSecureURL('r/extension/paypal_commerce/send');

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
                            $data['amountBreakdown']['handling']['value'] += $ln['tax_amount'];
                            $data['amountBreakdown']['tax_total']['value'] -= $ln['tax_amount'];
                        }
                    }
                }
            }
        }

        $i = 0;
        $orderDescription = [];
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
            $data['items'][$i] = [
                'name' => $product['name'],
                'unit_amount' => [
                    'value' => round($product['price'], 2),
                    'currency_code' => $this->currency->getCode(),
                ],
                'quantity' => $product['quantity'],
                'item_total' => [
                    'value' => round($product['price'] * $product['quantity'], 2),
                    'currency_code' => $this->currency->getCode()
                ]
            ];

            if ($taxLines) {
                foreach ($taxLines as $extTextId => $lines) {
                    foreach ($lines as $line) {
                        if ($line['item_code'] == $product['key']) {
                            $data['items'][$i]['tax'] = [
                                'value' => round(($line['tax_amount'] / $product['quantity']), 2) + (float)$data['items'][$i]['tax']['value'],
                                'currency_code' => $this->currency->getCode()
                            ];
                        }
                    }
                }
            } else {
                $data['items'][$i]['tax'] = [
                    'value' => round(
                        $this->tax->calcTotalTaxAmount(
                            $this->currency->format($product['price']),
                            $product['tax_class_id']
                        ),
                        2),
                    'currency_code' => $this->currency->getCode()
                ];
            }

            if ($description) {
                $data['items'][$i]['description'] = $description;
            }
            if ($sku) {
                $data['items'][$i]['sku'] = $sku;
            }
            $i++;
        }

        // cut description (paypal api requirements. See Order->create->purchase_units->description)
        $charsPerItem = round(120 / count($cartProducts));
        $data['order_description'] = '';
        foreach ($orderDescription as $desc) {
            $postfix = ' x ' . $desc['quantity'];
            if (mb_strlen($desc['title']) > ($charsPerItem - strlen($postfix))) {
                $data['order_description'] .= mb_substr($desc['title'], 0, ($charsPerItem - strlen($postfix) - 3)) . '...' . $postfix . "  ";
            } else {
                $data['order_description'] .= $desc['title'] . ' ' . $postfix . "\n";
            }
        }
        //this description cannot be more than 127 chars length
        $data['order_description'] = mb_strlen($data['order_description'])>127
            ? mb_substr($data['order_description'],0,127 )
            : $data['order_description'];

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

        if ($this->request->get['rt'] == 'checkout/guest_step_3') {
            $back_url = $this->html->getSecureURL('checkout/guest_step_2', '&mode=edit', true);
        } else {
            $back_url = $this->html->getSecureURL('checkout/payment', '&mode=edit', true);
        }

        $data['cancel_url'] = isset($this->session->data['fc'])
            ? $this->html->getSecureURL('checkout/fast_checkout')
            : $this->html->getSecureURL('checkout/confirm');

        $data['back'] = $this->html->buildElement(
            [
                'type' => 'button',
                'name' => 'back',
                'text' => $this->language->get('button_back'),
                'style' => 'button',
                'href' => $back_url,
            ]
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
            $this->response->setOutput(AJson::encode($output));
            return;
        }

        $this->extensions->hk_InitData($this, __FUNCTION__);

        //swap cart
        if ($this->session->data['paypal_cart']['cart']) {
            $cartClass = get_class($this->cart);
            $this->registry->set('cart', new $cartClass($this->registry, $this->session->data['paypal_cart']));
            //forbid coupon discount for subscriptions
            $this->config->set('coupon_status', false);
            unset($this->session->data['used_balance']);
            $this->session->data['cart_key'] = 'paypal_cart';
        }

        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $this->loadModel('extension/paypal_commerce');
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
            )
            , true
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
                    $output['success'] = $this->html->getSecureURL('checkout/success');
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

                if (in_array($total['total_type'], ['tax'])) {
                    $this->data['tax_total'] += $price;
                } elseif (in_array($total['total_type'], ['shipping'])) {
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