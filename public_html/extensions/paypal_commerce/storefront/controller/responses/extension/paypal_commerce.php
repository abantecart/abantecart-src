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
        if(isset($this->session->data['fc'])){
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
                'href'  => $this->view->templateResource('/css/paypal_commerce.css'),
                'rel'   => 'stylesheet',
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
            $this->messages->saveError('Paypal Commerce API Error', $data['error']."\nSee error log for details.");
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
            $data['order_'.$total['id']] = $this->currency->convert(
                $total['value'],
                $this->config->get('config_currency'),
                $currencyCode
            );

            if ($total['total_type'] == 'discount' || $total['total_type'] == 'coupon' || $total['total_type'] == 'balance' ) {
                $discount += abs($data['order_'.$total['id']]);
            } elseif ($total['total_type'] == 'fee') {
                $handling_fee += abs($data['order_'.$total['id']]);
            } elseif ($total['total_type'] == 'tax') {
                $taxes += $data['order_'.$total['id']];
            }
        }
        $data['amountBreakdown'] = [
            'item_total' => [
                'value'         => $data['order_subtotal'],
                'currency_code' => $currencyCode,
            ],
            'tax_total'  => [
                'value'         => $taxes,
                'currency_code' => $currencyCode,
            ],
            'shipping'   => [
                'value'         => (float) $data['order_shipping'],
                'currency_code' => $currencyCode,
            ],
            'discount'   => [
                'value'         => (float) $discount,
                'currency_code' => $currencyCode,
            ],
            'handling'   => [
                'value'         => (float) $handling_fee,
                'currency_code' => $currencyCode,
            ],
        ];

        $cartProducts = $this->cart->getProducts() + $this->cart->getVirtualProducts();

        $data['intent'] = $this->config->get('paypal_commerce_transaction_type');
        $this->load->model('localisation/country');
        //shipping address
        if ($order_info['shipping_country_id']) {
            $countryId = $order_info['shipping_country_id'];
            $addressType = 'shipping';
            $country = $this->model_localisation_country->getCountry($countryId);
            if ($country) {
                $data['shipping'] = [];
                $data['shipping']['name']['full_name'] = $order_info['shipping_firstname']
                    .' '
                    .$order_info['shipping_lastname'];

                $data['shipping']['address'] = [
                    'country_code' => $country['iso_code_2'],
                ];
                $flds = [
                    'address_line_1' => $addressType.'_address_1',
                    'address_line_2' => $addressType.'_address_2',
                    'admin_area_2'   => $addressType.'_city',
                    'postal_code'    => $addressType.'_postcode',
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
            $flds = [
                'address_line_1' => $addressType.'_address_1',
                'address_line_2' => $addressType.'_address_2',
                'admin_area_2'   => $addressType.'_city',
                'postal_code'    => $addressType.'_postcode',
            ];
            foreach ($flds as $n => $alias) {
                if ($order_info[$alias]) {
                    $data['address'][$n] = $order_info[$alias];
                }
            }
        }

        $this->load->model('localisation/zone');
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
        if($taxExts){
            foreach($taxExts as $extTextId) {
                if(!in_array($extTextId, $enabledExts)){
                    continue;
                }
                $mdl = $this->loadModel('total/'.$extTextId.'_total');
                if (!$mdl || !method_exists($mdl, 'getTaxLines')) {
                    continue;
                }
                $taxLines[$extTextId] = $mdl->getTaxLines();
                // around paypal api bug related to shipping tax. Move it into handling
                if($taxLines[$extTextId]){
                    foreach($taxLines[$extTextId] as $ln){
                        if($ln['line_type'] == 'shipping'){
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
                    $description .= $opt['name'].':'.$title."; ";
                    if (mb_strlen($description) > 120) {
                        $description = mb_substr($description, 0, 115).'...';
                    }
                    if ($opt['sku']) {
                        $sku .= ' '.$opt['sku'];
                    }
                }
            }

            $orderDescription[] = [
                'title' => $product['name'].' '. $description,
                'quantity' => $product['quantity'],
                'sku' => $sku
            ];






            // comment fot yet. Paypal requires amount breakdown for each line including tax amount per item (tax amount for 1 piece).
            // Then PP api calculates tax * quantity and then the compares this sum with total_tax_amount.
            //Approach for tax amount calculation of abc and pp are different.
            //We cannot to solve this issue yet.
            //Also found pp api bug with shipping tax. It's not contains it at all. Can be solved with handling fee.
            $data['items'][$i] = [
                'name'        => $product['name'],
                'unit_amount' => [
                    'value'         => round($product['price'],2),
                    'currency_code' => $this->currency->getCode(),
                ],
                'quantity'    => $product['quantity'],
                'item_total' => [
                    'value' => round($product['price']*$product['quantity'],2),
                    'currency_code' => $this->currency->getCode()
                ]
            ];

            if($taxLines){
                foreach($taxLines as $extTextId => $lines){
                    foreach($lines as $line) {
                        if ($line['item_code'] == $product['key']) {
                            $data['items'][$i]['tax'] = [
                                'value'         => round(($line['tax_amount']/$product['quantity']), 2) + (float)$data['items'][$i]['tax']['value'],
                                'currency_code' => $this->currency->getCode()
                            ];
                        }
                    }
                }
            }else{
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

        $charsPerItem = round(126/count($cartProducts));
        $data['order_description'] = '';
        foreach($orderDescription as $desc){
            $postfix = $desc['sku']. ' x '.$desc['quantity'];
            if(mb_strlen($desc['title']) > ($charsPerItem - strlen($postfix))){
                $data['order_description'] .= mb_substr($desc['title'], 0, ($charsPerItem - strlen($postfix)-3)).'...'.$postfix."  ";
            }else{
                $data['order_description'] .= $desc['title'] .' '.$postfix."\n";
            }
        }

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
            :$this->html->getSecureURL('checkout/confirm');

        $data['back'] = $this->html->buildElement(
            [
                'type'  => 'button',
                'name'  => 'back',
                'text'  => $this->language->get('button_back'),
                'style' => 'button',
                'href'  => $back_url,
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
                "Paypal Commerce: ".__CLASS__."::".__METHOD__.": Order #".$orderId." not found"
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
                .'Paypal Transaction Id: '.$transactionDetails['id']
            );
            $err->toLog()->toDebug();
        } //validate order info before confirmation
        elseif (
            $response->purchase_units[0]->custom_id != $order_info['order_id']
            || $response->purchase_units[0]->amount->currency_code != $order_info['currency']
            || $response->purchase_units[0]->amount->value != $orderTotalAmt
        ) {
            $output['error'] = $this->language->get('error_unknown');
            $err = new AError(
                "Paypal Commerce: Suspect Order Confirmation Request: "
                ."\n Order Transaction Details:".var_export($response, true)."\n"
                ."Requested Order Info:\n".var_export($order_info, true)
            );
            $err->toLog()->toDebug();
        } else {
            if ($response->status == 'COMPLETED') {
                $this->model_checkout_order->confirm(
                    $orderId,
                    $this->config->get('paypal_commerce_status_success_settled')
                );

                $this->model_checkout_order->updatePaymentMethodData(
                    $orderId,
                    serialize($response)
                );

                $mdl->savePaypalCustomer($this->customer->getId(), $transactionDetails['payer']['payer_id']);
                $mdl->savePaypalOrder(
                    $orderId,
                    [
                        'id'             => $transactionDetails['id'],
                        'transaction_id' => $transactionDetails['id'],
                    ]
                );
                try {
                    $output['success'] = $this->html->getSecureURL('checkout/success');
                } catch (Exception $e) {
                    $this->log->write(__FILE__.':'.__LINE__.'   - '.$e->getMessage()."\n\n".$e->getTraceAsString());

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
                    'name'  => $option['name'],
                    'value' => (mb_strlen($value) > 20 ? mb_substr($value, 0, 20).'..' : $value),
                ];
            }
            $price = $this->currency->format($product['price'], $order_info['currency'], $order_info['value'], false);
            $this->data['products'][] = [
                'name'        => $product['name'],
                'model'       => $product['model'],
                'price'       => $price,
                'quantity'    => $product['quantity'],
                'option'      => $option_data,
                'weight'      => $product['weight'],
                'weight_type' => $product['weight_type'],
            ];
            $this->data['items_total'] += $price * $product['quantity'];
        }

        //check for virtual product such as gift certificate
        $virtual_products = $this->cart->getVirtualProducts();

        if ($virtual_products) {
            foreach ($virtual_products as $virtual) {
                $this->data['products'][] = [
                    'name'     => ($virtual['name'] ? : 'Virtual Product'),
                    'model'    => '',
                    'price'    => $this->currency->format(
                        $virtual['amount'],
                        $order_info['currency'],
                        $order_info['value'],
                        false
                    ),
                    'quantity' => ($virtual['quantity'] ? : 1),
                    'option'   => [],
                    'weight'   => 0,
                ];
                $this->data['items_total'] += ($virtual['quantity'] ? : 1)
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
                    'name'     => $total['title'],
                    'model'    => '',
                    'price'    => $price,
                    'quantity' => 1,
                    'option'   => [],
                    'weight'   => 0,
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
                    'name'     => $total['title'],
                    'model'    => '',
                    'price'    => $price,
                    'quantity' => 1,
                    'option'   => [],
                    'weight'   => 0,
                ];
            }
        }

        if ($this->data['discount_amount_cart'] > 0) {
            $price = -1 * $this->currency->format(
                    $this->data['discount_amount_cart'], $order_info['currency'],
                    $order_info['value'], false
                );
            $this->data['products'][] = [
                'name'     => $this->language->get('text_discount'),
                'model'    => '',
                'price'    => $price,
                'quantity' => 1,
                'option'   => [],
                'weight'   => 0,
            ];
            $this->data['items_total'] += $price;
        }

        return $this->data['products'];
    }

    public function webhookPaymentRefund()
    {
        if (!$this->request->is_POST()) {
            http_response_code('404');
            return;
        }

        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->log->write(
            "WEBHOOK CALLED ".__METHOD__.": \n post data: ".var_export($this->request->post, true)
            ."\n get data: ".var_export($this->request->get, true)
            ."\ninput: ".var_export(file_get_contents("php://input"), true)
        );
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function webhookPaymentCaptured()
    {
        if (!$this->request->is_POST()) {
            return http_response_code('404');
        }
        $data = file_get_contents("php://input");
        $inData = json_decode($data, true);
$this->log->write(var_export($inData, true)); exit;
        if (!$inData) {
            $this->log->write("Paypal webhook ".__METHOD__.": incorrect incoming data! \n:".var_export($data, true));
            return http_response_code('406');
        }
        if ($inData['event_type'] != 'PAYMENT.SALE.COMPLETED') {
            $this->log->write("Paypal webhook ".__METHOD__.": Wrong Event Type! Waiting for PAYMENT.SALE.COMPLETED  but " . $inData['event_type'] ." was given");
            return http_response_code('406');
        }

        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('checkout/order');
        $this->loadModel('account/order');
        $this->loadModel('catalog/product');
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $this->loadModel('extension/paypal_commerce');
        $orderInfo = $this->model_checkout_order->getOrder($parentOrderId);

        if (!$orderInfo) {
            $this->log->write(
                "Paypal webhook "
                .__METHOD__
                .": Original order ID ".$parentOrderId." not found!\n Raw input data:\n"
                .var_export($data, true)
                ."\n\nParsed data:\n".var_export($inData, true)
            );
            http_response_code('406');
            exit('order not found');
        } else {
            //if next billing circle. check for data creation. must be older than 24hours
            // otherwise just adds data into order history
            if( time() - dateISO2Int( $orderInfo['date_added'] ) < 86400 ){
                $this->model_checkout_order->addHistory(
                    $orderInfo['order_id'],
                    $orderInfo['order_status_id'],
                    "Paypal webhook ".__METHOD__.": \n\nParsed data:\n".var_export($inData, true)
                );
                http_response_code('200');
                exit('Ok');
            }

        }

        $invoiceId = $inData['resource']['id'];
        if ($inData['resource']['state'] != 'completed') {
            $this->log->write(
                "Paypal webhook "
                .__METHOD__
                .": Automatic Payment is not completed!\n Raw input data:\n".var_export($data, true)
            );
            http_response_code('406');
            exit('transaction incomplete');
        }

        $this->session->data['customer_id'] = $orderInfo['customer_id'];
        $this->registry->set('customer', new ACustomer(Registry::getInstance()));
        $this->registry->set('currency', new ACurrency(Registry::getInstance()));
        $this->currency->set($inData['resource']['amount']['currency']);

        $addresses = [];
        if ($this->customer->isLogged()) {
            $this->loadModel('account/address');
            $allAddresses = $this->model_account_address->getAddresses();
            if (!$allAddresses) {
                $msg = 'Cannot to recreate order of Customer #'.$orderInfo['customer_id'].'. Address List is empty.';
                $this->messages->saveWarning(
                    'New order does not created by Paypal subscription '.$subscriptionId,
                    $msg,
                    false
                );
                http_response_code(400);
                exit($msg);
            }

            foreach ($allAddresses as $address) {
                $addresses[$address['address_id']] = $address;
                if ($address['address_1'] == $orderInfo['payment_address_1']
                    && $address['address_2'] == $orderInfo['payment_address_2']
                    && $address['zone_id'] == $orderInfo['payment_zone_id']
                    && $address['country_id'] == $orderInfo['payment_country_id']
                ) {
                    $this->session->data['payment_address_id'] = $address['address_id'];
                }
                if ($address['address_1'] == $orderInfo['shipping_address_1']
                    && $address['address_2'] == $orderInfo['shipping_address_2']
                    && $address['zone_id'] == $orderInfo['shipping_zone_id']
                    && $address['country_id'] == $orderInfo['shipping_country_id']) {
                    $this->session->data['shipping_address_id'] = $address['address_id'];
                }
            }

            if (!$this->session->data['payment_address_id']) {
                $this->session->data['payment_address_id'] = $this->customer->getAddressId();
            }
            if (!$this->session->data['shipping_address_id'] && $orderInfo['shipping_address_1']) {
                $this->session->data['shipping_address_id'] = $this->customer->getAddressId();
            }

            if ($this->session->data['shipping_address_id']) {
                $this->session->data['country_id'] =
                    $addresses[$this->session->data['shipping_address_id']]['country_id'];
                $this->session->data['zone_id'] = $addresses[$this->session->data['shipping_address_id']]['zone_id'];
            } else {
                $this->session->data['country_id'] =
                    $addresses[$this->session->data['payment_address_id']]['country_id'];
                $this->session->data['zone_id'] = $addresses[$this->session->data['payment_address_id']]['zone_id'];
            }
        } else {
            //if guest order
            $this->session->data['country_id'] = $orderInfo['payment_country_id'];
            $this->session->data['zone_id'] = $orderInfo['payment_zone_id'];
            $this->session->data['guest'] = [
                'firstname'      => $orderInfo['payment_firstname'],
                'lastname'       => $orderInfo['payment_lastname'],
                'email'          => $orderInfo['email'],
                'telephone'      => $orderInfo['telephone'],
                'fax'            => $orderInfo['fax'],
                'company'        => $orderInfo['payment_company'],
                'address_1'      => $orderInfo['payment_address_1'],
                'address_2'      => $orderInfo['payment_address_2'],
                'city'           => $orderInfo['payment_city'],
                'postcode'       => $orderInfo['payment_postcode'],
                'zone'           => $orderInfo['payment_zone'],
                'zone_id'        => $orderInfo['payment_zone_id'],
                'country'        => $orderInfo['payment_country'],
                'country_id'     => $orderInfo['payment_country_id'],
                'address_format' => $orderInfo['payment_address_format'],
            ];
        }
        $langId = $orderInfo['language_id'];
        $this->loadModel('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        $orderLanguage = $languages[$langId];
        if (!$orderLanguage) {
            $orderLanguage = current($languages);
        }

        //set order language
        $this->registry->set('language', new ALanguage($this->registry, $orderLanguage['code'], 0));
        $this->request->get['language'] = $orderLanguage['language_id'];
        $this->config->set('storefront_language_id', $orderLanguage['language_id']);
        $this->language->setCurrentLanguage();
        $this->tax->setZone($this->session->data['country_id'], $this->session->data['zone_id']);
        $this->session->data['payment_method'] = [
            'id'    => 'paypal_commerce',
            'title' => 'Paypal',
        ];
        $orderProduct = current($this->model_account_order->getOrderProducts($parentOrderId));
        if ($orderProduct) {
            $orderOptions = $this->model_account_order->getOrderOptions(
                $parentOrderId,
                $orderProduct['order_product_id']
            );

            $product_options = [];
            if ($orderOptions) {
                foreach ($orderOptions as $oo) {
                    $ov = $this->model_catalog_product->getProductOptionValue(
                        $orderProduct['product_id'],
                        $oo['product_option_value_id']
                    );
                    if (isset($product_options[$ov['product_option_id']])) {
                        $product_options[$ov['product_option_id']][] = $oo['product_option_value_id'];
                    } else {
                        $product_options[$ov['product_option_id']] = $oo['product_option_value_id'];
                    }
                }
            }
            $this->cart->clear();
            $this->cart->add(
                $orderProduct['product_id'],
                $orderProduct['quantity'],
                $product_options
            );
        }

        if (!$this->cart->hasProducts()) {
            $error_text = 'Cannot to create order of Customer #'.$this->customer->getId()
                .'. Product ID #'.$orderProduct['product_id'].' cannot be added into the cart.'
                .' Probably Product disabled or have no stock. '
                .'Subscription '.$subscriptionId.' paused!';
            $this->messages->saveWarning(
                'Order does not created by Paypal Webhook of subscription '.$subscriptionId,
                $error_text,
                false
            );

            $mdl->pauseSubscription($subscriptionId);
            http_response_code(406);
            exit($error_text);
        }

        if (!$this->customer->isLogged()) {
            $cols = [
                'firstname',
                'lastname',
                'company',
                'address_1',
                'address_2',
                'city',
                'postcode',
                'zone',
                'zone_id',
                'country',
                'country_id',
                'address_format',
            ];
            if ($this->cart->hasShipping()) {
                foreach ($cols as $col) {
                    $this->session->data['guest']['shipping'][$col] = $orderInfo['shipping_'.$col];
                }
            } else {
                foreach ($cols as $col) {
                    if ($orderInfo['shipping_'.$col]) {
                        $this->session->data['guest'][$col] = $orderInfo['shipping_'.$col];
                    }
                }
            }
        }

        if ($orderInfo['shipping_method']) {
            list($shipping_key, $shipping_method) = explode('.', $orderInfo['shipping_method_key']);
            $this->loadModel('extension/'.$shipping_key);
            /** @var ModelExtensionDefaultFlatRateShipping $mdl */
            $mdl = $this->{'model_extension_'.$shipping_key};
            $quote = $mdl->getQuote(
                $addresses[$this->session->data['shipping_address_id']]
            );
            $this->session->data['shipping_method'] = $quote['quote'][$shipping_method];
        }

        //when requires shipping, but method not found
        if (!$this->session->data['shipping_method'] && $this->cart->hasShipping()) {
            $quote_data = [];
            $this->load->model('checkout/extension');
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
            $results = $this->model_checkout_extension->getExtensions('shipping');
            foreach ($results as $result) {
                $method_name = $result['key'];
                $this->loadModel('extension/'.$method_name);
                $quote = $this->{'model_extension_'.$result['key']}->getQuote($shipping_address);
                if ($quote) {
                    $ext_config = $this->model_checkout_extension->getSettings($method_name);
                    $autoselect = $ext_config[$method_name."_autoselect"];
                    if ($autoselect) {
                        $this->session->data['shipping_method'] = current($quote['quote']);
                    }
                }
            }
            $sort_order = [];
            foreach ($quote_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }
            array_multisort($sort_order, SORT_ASC, $quote_data);
            $this->session->data['shipping_methods'] = $quote_data;
        }

        $order = new AOrder($this->registry);
        //mark order as repeated
        $this->registry->set('paypal_repeated_order', true);
        $this->data = $order->buildOrderData($this->session->data);
        $order_id = $order->saveOrder();
        if (!$order_id) {
            $error_text = 'Cannot to create order of Customer #'.$orderInfo['customer_id'].' based on session data';
            $this->messages->saveWarning(
                'Order does not created by Paypal webhook of subscription '.$subscriptionId,
                $error_text,
                false
            );
            $this->log->write($error_text."\n Session Data:\n".var_export($this->session->data, true));
            $this->session->clear();
            http_response_code(400);
            exit($error_text);
        }
        $this->session->data['order_id'] = $order_id;
        $this->loadModel('checkout/order');
        $order_status_id = $this->config->get('paypal_commerce_next_cycle_order_status');
        if (!$order_status_id) {
            $order_status_id = $this->order_status->getStatusByTextId('processing');
        }

        //when total amount of new order not equal of parent order total
        if (round($this->cart->getFinalTotal(), 2) != round((float)$orderInfo['total'], 2)) {
            $orderTotals = $this->model_account_order->getOrderTotals($parentOrderId);
            $sql = "DELETE FROM ".$this->db->table('order_totals')."
                    WHERE order_id = ".(int) $order_id."; ";
            $this->db->query($sql);
            foreach ($orderTotals as $t) {
                $t['order_id'] = $order_id;
                unset($t['order_total_id']);
                $sql = " INSERT INTO ".$this->db->table('order_totals')
                    ." (`".implode("`, `", array_keys($t))."`) "
                    ." VALUES ('".implode("', '", $t)."');";
                $this->db->query($sql);
            }

            $sql = "UPDATE ".$this->db->table('orders')."
                    SET `total` = '".$orderInfo['total']."'
                    WHERE order_id = ".(int) $order_id;
            $this->db->query($sql);
        }

        $this->model_checkout_order->confirm(
            $order_id, $order_status_id,
            'Autogenerated order by Paypal New Subscription Invoice. Origin subscription order #'.$parentOrderId."\n\n"
        );
        //save input data into comments but hide from customer
        $this->model_checkout_order->addHistory(
            $order_id,
            $order_status_id,
            "Paypal webhook ".__METHOD__.": \n\nParsed data:\n".var_export($inData, true)
        );

        $orderData = $order->getOrderData();
        $mdl->savePaypalOrder(
            $order_id,
            [
                'id'             => null,
                'transaction_id' => $invoiceId,
            ]
        );
        $hasShipping = false;
        foreach ($orderData['totals'] as $total) {
            if (in_array($total['id'], ['subtotal', 'total'])) {
                continue;
            }

            if ($total['total_type'] == 'shipping') {
                $hasShipping = true;
            }
        }

        if ($this->cart->hasShipping() && !$hasShipping) {
            $this->session->clear();
            $this->messages->saveError(
                'Newly generated order #'.$order_id.' have product with shipping, but shipping method is unknown.',
                'Please check original order #'.$parentOrderId.' of Subscription '.$subscriptionId
                .'</a>. Looks like It contains disabled shipping method!'
            );
            http_response_code(400);
            exit(
                'New order #'.$order_id.' has been created. Invoice "'.
                $invoiceId.'" has been updated But it contains errors. 
                See Messages on admin-side of your AbanteCart'
            );
        }
        $this->session->clear();
        $this->data['subscription_id'] = $subscriptionId;
        $this->data['parent_order_id'] = $parentOrderId;
        $this->data['order_id'] = $order_id;
        $this->data['order_data'] = $orderData;
        $this->data['pp_invoice_id'] = $invoiceId;
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        exit('New order #'.$order_id.' has been created. Invoice "'.$invoiceId.'" has been updated.');
    }

}