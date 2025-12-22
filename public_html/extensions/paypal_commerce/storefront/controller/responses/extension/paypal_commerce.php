<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUndefinedClassInspection */
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

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerResponsesExtensionPaypalCommerce extends AController
{
    public function swapCart()
    {

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
        $this->swapCart();
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->document->addStyle(
            [
                'href'  => $this->view->templateResource('/css/paypal_commerce.css'),
                'rel'   => 'stylesheet',
                'media' => 'screen',
            ]
        );

        $this->loadLanguage('paypal_commerce/paypal_commerce');
        /** @var ModelCheckoutOrder $oMdl */
        $oMdl = $this->loadModel('checkout/order');
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $this->load->model('extension/paypal_commerce');
        $data['client_token'] = $mdl->getClientToken();
        $data['bn_code'] = ExtensionPaypalCommerce::getBnCode();
        if (!$data['client_token']) {
            $data['error'] = 'Cannot to obtain client token from Paypal API. Incident has been reported.';
            $this->messages->saveError('Paypal Commerce API Error', $data['error'] . "\nSee error log for details.");
        }

        $data['intent'] = $this->config->get('paypal_commerce_transaction_type');

        $icon = $this->config->get("paypal_commerce_payment_storefront_icon");
        if (has_value($icon)) {
            $icon_data = $this->model_checkout_extension->getSettingImage($icon);
            if ($icon_data['resource_path']) {
                $data['icon'] = $this->html->buildResourceImage(
                    [
                        'url' => HTTPS_DIR_RESOURCE . $icon_data['type_dir'] . $icon_data['resource_path'],
                    ]
                );
            } elseif ($icon_data['resource_code']) {
                $data['icon'] = $icon_data['resource_code'];
            }
        }

        $template = 'responses/paypal_commerce_confirm.tpl';

        $data['enabled_components'] = unserialize($this->config->get('paypal_commerce_enabled_components')) ?: ['buttons'];
        $data['enabled_funding'] = unserialize($this->config->get('paypal_commerce_enabled_funding')) ?: [];

        //AVS validation
        if (in_array('card-fields', $data['enabled_components'])) {
            if (!$this->customer->isLogged()) {
                $address = $this->session->data['fc']['guest'];
            } else {
                /** @var ModelAccountAddress $addrMdl */
                $addrMdl = $this->load->model('account/address');
                $address = $addrMdl->getAddress($this->session->data['fc']['payment_address_id']);
            }
            $data['billing_address'] = [
                'name'         => $address['firstname'] . ' ' . $address['lastname'],
                'address_1'    => $address['address_1'],
                'address_2'    => $address['address_2'],
                'zone_name'    => $address['zone'],
                'city'         => $address['city'],
                'postcode'     => $address['postcode'],
                'country_code' => $address['iso_code_2'],
            ];
        }

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
        if (!isset($get['product_key']) && $this->session->data['fc']['single_checkout'] && $this->session->data['fc']['product_key']) {
            $get['product_key'] = $this->session->data['fc']['product_key'];
            $get['fc'] = $get['single_checkout'] = 1;
        }
        $data['cancel_url'] = $this->html->getSecureURL(
            'checkout/fast_checkout',
            $get ? '&' . http_build_query($get, '', '&') : ''
        );

        $data['button_confirm'] = $this->html->buildElement(
            [
                'type'  => 'button',
                'name'  => 'checkout',
                'style' => 'button btn-primary',
                'text'  => $this->language->get('button_confirm'),
            ]
        );

        //PayLater message
        $plConfig = json_decode(
            html_entity_decode($this->config->get('paypal_commerce_pay_later_message_config'), ENT_QUOTES, 'UTF-8'),
            true
        );

        if ($plConfig && $plConfig['checkout']['status'] == 'enabled') {
            $payLaterMessage = html_entity_decode($this->config->get('paypal_commerce_pay_later_checkout_message'));
            $payLaterMessage = str_replace('ENTER_VALUE_HERE', '%s', $payLaterMessage);
            if (str_contains($payLaterMessage, '%s')) {
                $payLaterMessage = sprintf(
                    $payLaterMessage,
                    $this->cart->getFinalTotal()
                );
                $data['pay_later_message'] = $payLaterMessage;
            }
        }

        $data['pageType'] = "checkout";
        $this->view->batchAssign($data);
        $this->processTemplate($template);
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function send()
    {
        $this->swapCart();
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
        if (!$this->request->is_POST()) {
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

        $this->swapCart();
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->session->data['reference_id'] = 'abc_'.randomWord(10);
        //swap cart if we have paypal_cart
        if ($this->session->data['paypal_cart']['cart']) {
            $cartClass = get_class($this->cart);
            $this->registry->set('cart', new $cartClass($this->registry, $this->session->data['paypal_cart']));
            unset($this->session->data['used_balance']);
            $this->session->data['cart_key'] = 'paypal_cart';
        }
        $this->data = [
            'currencyCode' => $this->currency->getCode(),
            'decPlace' => (int)$this->currency->getCurrency()['decimal_place']
        ];
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $this->loadModel('extension/paypal_commerce');

        $this->data['pp']['intent'] = $this->config->get('paypal_commerce_transaction_type');
        $ppData['intent'] = strtoupper($this->data['pp']['intent']);
        //need an order detail
        /** @var ModelCheckoutOrder $oMdl */
        $oMdl = $this->loadModel('checkout/order');
        $order_info = $oMdl->getOrder((int)$this->session->data['order_id']);

        $this->prepareOrderData($order_info);

        $ppData['payer'] = $this->data['pp']['payer'];
        $ppData['purchase_units'] = $this->data['pp']['purchase_units'];
        $this->buildPPItems();
        $ppData['purchase_units'][0]['items'] = $this->data['pp']['items'];
        $ppData['payment_source']['paypal'] = [
            'experience_context' => [
                'return_url'            => $this->html->getSecureURL('checkout/fast_checkout'),
                'cancel_url'            => $this->html->getSecureURL('checkout/fast_checkout'),
                'app_switch_preference' => [
                    'launch_paypal_app' => true
                ]
            ]
        ];
        $_3ds_policy = $this->config->get('paypal_commerce_3ds_policy');
        if ($_3ds_policy && $this->request->get['card'] == 'true') {
            $ppData['payment_source']['card']['attributes'] = [
                'verification' => [
                    'method' => $_3ds_policy
                ]
            ];
        }

        try {
            $output = (array)$mdl->createPPOrder($ppData);
            $this->shopping_data->save(
                'paypal_data',
                $this->cart->getCartKey(),
                [
                    'reference_id' => $ppData['purchase_units'][0]['reference_id'],
                    'order_id' => $output['id'],
                    'status' => $output['status'],
                    'payment_source' => $output['payment_source'],
                ],
                $this->session->data['order_id']
            );
        } catch (\PayPalHttp\HttpException|Error $e) {
            $this->log->write('PaypalCommerce order creation error: ' . $e->getMessage() . "\n Input Data: " . var_export($ppData, true));
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

    protected function buildPPItems()
    {
        $i = 0;
        $items = $orderDescription = [];
        $cartProducts = $this->cart->getProducts() + $this->cart->getVirtualProducts();
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
                'title'    => html_entity_decode($product['name'] . ' ' . $description),
                'quantity' => $product['quantity'],
                'sku'      => html_entity_decode($sku)
            ];


            // comment for yet. PayPal requires amount breakdown for each line including tax amount per item (tax amount for 1 piece).
            // Then PP api calculates tax * quantity, and then the compares this sum with total_tax_amount.
            //Approach for tax amount calculation of abc and pp are different.
            //We cannot solve this issue yet.
            //Also found pp api bug with shipping tax. It does not contain it at all. Can be solved with handling fee.
            $items[$i] = [
                'name'        => substr($product['name'], 0, 127),
                'unit_amount' => [
                    'value'         => "" . round($this->currency->convert(
                            $product['price'],
                            $this->config->get('config_currency'),
                            $this->data['currencyCode']
                        ), 2),
                    'currency_code' => $this->data['currencyCode'],
                ],
                'quantity'    => $product['quantity']
            ];

            if ($description) {
                $items[$i]['description'] = $description;
            }
            if ($sku) {
                $items[$i]['sku'] = substr($sku, 0, 127);
            }
            $i++;
        }
        $this->data['pp']['items'] = $items;

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
        //this description cannot be more than 127 char lengths
        $this->data['pp']['order_description'] = mb_strlen($order_description) > 127
            ? mb_substr($order_description, 0, 127)
            : $order_description;
    }

    protected function prepareOrderData($order_info)
    {
        $this->data['pp']['orderTotal'] = $taxes = $discount = $handling_fee = 0.0;

        foreach ($this->cart->getFinalTotalData() as $total) {
            if ($total['id'] == 'total') {
                $this->data['pp']['orderTotal'] = "" . round($total['converted'], 2);
            }

            $this->data['pp']['order_' . $total['id']] = $this->currency->convert(
                (float)$total['value'],
                $this->config->get('config_currency'),
                $this->data['currencyCode']
            );

            if (in_array($total['total_type'], ['discount', 'promotion', 'coupon', 'balance'])) {
                $discount += abs($this->data['pp']['order_' . $total['id']]);
            } elseif ($total['total_type'] == 'fee' || str_ends_with($total['total_type'], '_fee')) {
                $handling_fee += abs($this->data['pp']['order_' . $total['id']]);
            } elseif ($total['total_type'] == 'tax') {
                $taxes += $this->data['pp']['order_' . $total['id']];
            }
        }
        $this->data['pp']['amountBreakdown'] = [
            'item_total' => [
                'currency_code' => $this->data['currencyCode'],
                'value'         => (string)round($this->data['pp']['order_subtotal'], 2),
            ],
            'tax_total'  => [
                'currency_code' => $this->data['currencyCode'],
                'value'         => (string)round($taxes, 2)
            ],
            'shipping'   => [
                'currency_code' => $this->data['currencyCode'],
                'value'         => (string)round($this->data['pp']['order_shipping'], 2),
            ],
            'discount'   => [
                'currency_code' => $this->data['currencyCode'],
                'value'         => (string)round($discount, 2)
            ],
            'handling'   => [
                'currency_code' => $this->data['currencyCode'],
                'value'         => (string)round($handling_fee, 2)
            ],
        ];

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
                if ($zoneInfo && $zoneInfo['code']) {
                    $shipping['address']['admin_area_1'] = $zoneInfo['code'];
                }

                $addressType = 'shipping';
                $commonShipPayFields = [
                    'address_line_1' => $addressType . '_address_1',
                    'address_line_2' => $addressType . '_address_2',
                    'admin_area_2'   => $addressType . '_city',
                    'postal_code'    => $addressType . '_postcode',
                ];

                foreach ($commonShipPayFields as $n => $alias) {
                    if ($order_info[$alias]) {
                        $shipping['address'][$n] = $order_info[$alias];
                    }
                }
            }
        }
        $this->data['pp']['shipping'] = $shipping;

        //get tax per item lines
        $taxExts = $this->extensions->getInstalled('tax');
        $enabledExts = $this->extensions->getEnabledExtensions();
        $taxLines = [];
        if ($taxExts) {
            foreach ($taxExts as $extTextId) {
                if (!in_array($extTextId, $enabledExts)) {
                    continue;
                }
                /** @var ModelTotalAvataxIntegrationTotal $mdl */
                $mdl = $this->loadModel('total/' . $extTextId . '_total');
                if (!$mdl || !method_exists($mdl, 'getTaxLines')) {
                    continue;
                }
                $taxLines[$extTextId] = $mdl->getTaxLines();
                // around paypal api bug related to shipping tax. Move it into handling
                if ($taxLines[$extTextId]) {
                    foreach ($taxLines[$extTextId] as $ln) {
                        if ($ln['line_type'] == 'shipping') {
                            $this->data['pp']['amountBreakdown']['handling']['value'] += $ln['tax_amount'];
                            $this->data['pp']['amountBreakdown']['tax_total']['value'] -= $ln['tax_amount'];
                        }
                    }
                }
            }
        }

        $this->data['pp']['payer'] = [
            'name'          => [
                'given_name' => $order_info['shipping_firstname'],
                'surname'    => $order_info['shipping_lastname'],
            ],
            'email_address' => $order_info['email'],
        ];
        $ppOrderData = $this->shopping_data->get('paypal_data', (string)$this->cart->getCartKey());
        $this->data['pp']['purchase_units'][0] = [
            'reference_id' => $ppOrderData['data']['reference_id'] ?: $this->session->data['reference_id'],
            'amount'      => [
                'value'         => $this->data['pp']['orderTotal'],
                'currency_code' => $this->data['currencyCode']
            ],
        ];
        if($this->data['pp']['order_description']){
            $this->data['pp']['purchase_units'][0]['description'] = $this->data['pp']['order_description'];
        }

        //allow breakdown only for store currency without enabled setting "display prices with tax"
        // to avoid conversion problems
        if ($this->config->get('config_currency') == $this->data['currencyCode'] && !$this->config->get('config_tax')) {
            $this->data['pp']['purchase_units'][0]['amount']['breakdown'] = $this->data['pp']['amountBreakdown'];
        }

    }

    public function captureOrder()
    {
        if (!$this->request->is_POST()) {
            http_response_code(406);
            return;
        }

        $json = file_get_contents('php://input');
        $json = (array)json_decode($json, true);
        $ppOrderId = $json['orderID'];
        $output = [];

        $orderId = $this->session->data['order_id'];
        //"buy-now" process
        if(!$orderId) {
            $cartKey = $this->cart->getCartKey();
            $cartData = $this->shopping_data->get('cart',$cartKey);
            if(!$cartData['data']) {
                $error = new AError('Cart data not found!');
                $error->toJSONResponse(
                    406,
                    [
                        'error' => true,
                        'message' => 'Cart data not found'
                    ]
                );
            }

            if($cartData['order_id']) {
                $orderId = $this->session->data['order_id'] = $cartData['order_id'];
            }
            $fcSession =& $this->session->data['fc'];
            $fcSession['cart_key'] = $cartKey;
            $fcSession['cart'] = $cartData['data'];
            $cartClassName = get_class($this->cart);
            $this->registry->set(
                'cart',
                new $cartClassName($this->registry, $fcSession)
            );
        }else {
            if (!$this->csrftoken->isTokenValid()) {
                $output['error'] = $this->language->get('error_unknown');
                $this->load->library('json');
                $this->response->setOutput(AJson::encode($output));
                return;
            }
        }

        $this->swapCart();
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('paypal_commerce/paypal_commerce');
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $this->loadModel('extension/paypal_commerce');
        try {
            if ($this->config->get('paypal_commerce_transaction_type') == 'capture') {
                $result = $mdl->capturePPOrder($ppOrderId);
                if ($result->purchase_units[0]->payments->captures[0]->status == 'DECLINED') {
                    throw new Exception(
                        $this->language->get('paypal_commerce_error_declined') . '. '
                        . $result->purchase_units[0]->payments->captures[0]->seller_protection->status
                    );
                }
            } else {
                $result = $mdl->authorizePPOrder($ppOrderId);
                if ($result->purchase_units[0]->payments->authorizations[0]->status == 'DENIED') {
                    throw new Exception(
                        $this->language->get('paypal_commerce_error_denied') . '. '
                        . $result->purchase_units[0]->payments->authorizations[0]->seller_protection->status
                    );
                }
            }

            $output = ['id' => $result->id];
            $order = new AOrder($this->registry, $orderId);
            $orderInfo = $order->loadOrderData($this->session->data['order_id'],'any',($this->customer->getId()?:'guest'));
            if($orderInfo && !$orderInfo['email']){
                $this->session->data['fc'] += $order->data;
                /** @see ControllerResponsesCheckoutPay::select_shipping() */
                $dd = new ADispatcher( 'responses/checkout/pay/select_shipping');
                $dd->dispatch();
                //resave an order into a database
                $this->session->data['fc']['email'] = $result->payer->email_address;
                $this->session->data['fc']['guest']['email'] = $result->payer->email_address;
                $this->session->data['fc']['guest']['firstname'] = $result->payer->name->given_name;
                $this->session->data['fc']['guest']['lastname'] = $result->payer->name->surname;
                $this->session->data['fc']['guest']['company'] = $result->payment_source->paypal->business_name;
                //take the correct shipping address from order
                $ppO = $mdl->getOrder($ppOrderId);
                list($fName, $lName) = explode(' ', $ppO->purchase_units[0]->shipping->name->full_name);
                $this->session->data['fc']['guest']['shipping']['firstname'] = $fName;
                $this->session->data['fc']['guest']['shipping']['lastname'] = $lName;
                $this->session->data['fc']['guest']['shipping']['company'] = $ppO->payment_source->paypal->business_name;
                $this->session->data['fc']['guest']['shipping']['address_1'] = $ppO->purchase_units[0]->shipping->address->address_line_1;
                $this->session->data['fc']['guest']['shipping']['address_2'] = $ppO->purchase_units[0]->shipping->address->address_line_2;
                $this->session->data['fc']['guest']['shipping']['city'] = $ppO->purchase_units[0]->shipping->address->admin_area_2;
                $this->session->data['fc']['guest']['shipping']['postcode'] = $ppO->purchase_units[0]->shipping->address->postal_code;
                $order->buildOrderData( $this->session->data['fc'] );
                $order->saveOrder();
            }
        } catch (Exception|Error $e) {
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
        /** @var ModelCheckoutOrder $oMdl */
        $oMdl = $this->loadModel('checkout/order');
        $orderId = $this->session->data['order_id'];
        $order_info = $oMdl->getOrder((int)$orderId);
        if (!$order_info) {
            $output['error'] = $this->language->get('error_unknown');
            $err = new AError(
                "Paypal Commerce: " . __CLASS__ . "::" . __METHOD__ . ": Order #" . $orderId . " not found"
            );
            $err->toLog()->toDebug();
            return $output;
        }

        $orderTotalAmt = "" . round($order_info['total'] * $order_info['value'], 2);
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

        $ppData = $this->shopping_data->get('paypal_data',$this->cart->getCartKey());

        if (!$response) {
            $output['error'] = 'Cannot establish a connection to the server OR transaction Id is unknown';
            $err = new AError(
                'Paypal Commerce: Cannot Get Order Information from Paypal. '
                . 'Paypal Transaction Id: ' . $transactionDetails['id']
            );
            $err->toLog()->toDebug();
        } //validate order info before confirmation
        elseif (
            $response->purchase_units[0]->reference_id != $ppData['data']['reference_id']
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
                $orderStatusId = $this->config->get('paypal_commerce_transaction_type') == 'capture'
                    ? $this->config->get('paypal_commerce_status_success_settled')
                    : $this->config->get('paypal_commerce_status_success_unsettled');
                $oMdl->confirm(
                    $orderId,
                    $orderStatusId ?: $this->order_status->getStatusByTextId('pending')
                );

                $oMdl->updatePaymentMethodData(
                    $orderId,
                    serialize(json_decode(json_encode($response), true))
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
                    $output['success'] = $this->html->getSecureURL('checkout/finalize');
                } catch (Exception $e) {
                    $this->log->write(__FILE__ . ':' . __LINE__ . '   - ' . $e->getMessage() . "\n\n" . $e->getTraceAsString());
                    $output['error'] = 'Oops, Unexpected Application Error';
                }
            } else {
                $output['error'] = "Oops, Unexpected Application Error\n(" . $response->status . ' ' . $response->status_detail->reason . ")";
                $this->log->write(var_export($response, true));
            }
        }
        return $output;
    }


    /**
     * Handles the creation of a temporary PayPal order for an express-checkout process.
     *
     * This method is triggered through a POST request to initiate the process of creating a PayPal order.
     * It captures input data, configures the order details, and interacts with the PayPal API to create the order.
     * In the event of an error during the process, appropriate error responses are logged and returned.
     * Additionally, CSRF tokens are generated and included in the output.
     *
     */
    public function createQuickOrder()
    {
        if (!$this->request->is_POST()) {
            http_response_code(406);
            return;
        }
        $inData = file_get_contents('php://input');
        $inData = (array)json_decode($inData, true);

        //short name
        $fcSession =& $this->session->data['fc'];
        if(!$fcSession['cart'] && $this->session->data['cart']) {
            $fcSession =& $this->session->data;
        }

        if(!$fcSession['cart']){
            $error = new AError('Cart data not found!');
            $error->toJSONResponse(
                406,
                [
                    'error' => true,
                    'message' => 'Cart data not found'
                ]
            );
        }
        $cartClassName = get_class($this->cart);
        $this->registry->set(
            'cart',
            new $cartClassName($this->registry, $fcSession)
        );

        //cleanup cart
        foreach($this->cart->getProducts() as $key=>$cartProduct) {
            if($cartProduct['stock']<=0){
                $this->cart->remove($key);
            }
        }

        //save cart_key into cookie to check on js-side if pp-checkout failed and customer goes to FastCheckout process
        setCookieOrParams(
            'fc_cart_key',
            $fcSession['cart_key'],
            [
                'path'     => dirname($this->request->server['PHP_SELF']),
                'domain'   => null,
                'secure'   => (defined('HTTPS') && HTTPS),
                'httponly' => false,
                'samesite' => ((defined('HTTPS') && HTTPS) ? 'None' : 'lax')
            ]
        );

        $this->session->data['reference_id'] = 'abc_'.randomWord(10);
        $output = [];

        $this->data['currencyCode'] = $this->currency->getCode();
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $this->loadModel('extension/paypal_commerce');
        $orderTotal = "" . round(
            $inData['total']
                ? $this->currency->convert(
                    $inData['total'],
                    $this->config->get('config_currency'),
                    $this->data['currencyCode']
                )
                : $this->cart->getFinalTotal()
            ,2);

        $ppData['intent'] = strtoupper($this->config->get('paypal_commerce_transaction_type'));

        $ppData['purchase_units'][0] = [
            'reference_id' => $this->session->data['reference_id'],
            'amount'      => [
                'value'         => $orderTotal?: 0.01,
                'currency_code' => $this->data['currencyCode']
            ],
            'description' => $inData['product_name'] ? substr($inData['product_name'], 0, 127) : ''
        ];

        $ppData['payment_source']['paypal'] = [
            'experience_context' => [
                'return_url'            => $inData['return_url'],
                'cancel_url'            => $inData['return_url'],
                'shipping_preference'   => 'GET_FROM_FILE',
                'order_update_callback_config' => [
                    'callback_url' => $this->html->getSecureURL(
                        'r/extension/paypal_commerce/orderUpdate',
                        '&'.http_build_query(
                            [
                                'ck' => $fcSession['cart_key'],
                                'currency_code' => $this->data['currencyCode'],
                                //uncomment for debug
                                'XDEBUG_TRIGGER' => 1,
                                'XDEBUG_SESSION' => 'PHPSTORM'
                            ]
                        )
                    ),
                    'callback_events' => ["SHIPPING_ADDRESS","SHIPPING_OPTIONS"]
                ],
                'app_switch_preference' => [
                    'launch_paypal_app' => true
                ]
            ]
        ];

        try {
            $output = (array)$mdl->createPPOrder($ppData);
            $this->shopping_data->save(
                'paypal_data',
                $fcSession['cart_key'],
                [
                    'reference_id' => $this->session->data['reference_id'],
                    'order_id' => $output['id'],
                    'status' => $output['status'],
                    'payment_source' => $output['payment_source'],
                ]
            );
        } catch (\PayPalHttp\HttpException|Error $e) {
            $this->log->write('PaypalCommerce order creation error: ' . $e->getMessage() . "\n Input Data: " . var_export($ppData, true));
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

    public function orderUpdate()
    {

        if (!$this->request->is_POST()) {
            http_response_code(406);
            return;
        }

        $inData = file_get_contents('php://input');
        $inData = (array)json_decode($inData, true);

        $this->currency->set($this->request->get['currency_code']);
        $cartKey = (string)$this->request->get['ck'];
        $cartData = $this->shopping_data->get('cart',$cartKey);
        if(!$cartData['data']) {
            $error = new AError('Cart data not found!');
            $error->toJSONResponse(
                406,
                [
                    'error' => true,
                    'message' => 'Cart data not found'
                ]
            );
        }

        if($cartData['order_id']) {
            $this->session->data['order_id'] = $cartData['order_id'];
        }
        $fcSession =& $this->session->data['fc'];
        $fcSession['cart_key'] = $cartKey;
        $fcSession['cart'] = $cartData['data'];
        $cartClassName = get_class($this->cart);
        $this->registry->set(
            'cart',
            new $cartClassName($this->registry, $fcSession)
        );

        $ppOrderId = $inData['id'];
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $this->loadModel('extension/paypal_commerce');
        $ppOrderDetails = $mdl->getOrder($ppOrderId);
        if($ppOrderDetails === false) {
            $error = new AError('Paypal Order '.$ppOrderId.' not found!');
            $error->toJSONResponse(
                406,
                [
                    'error' => true,
                    'message' => 'Paypal Order '.$ppOrderId.' not found!'
                ]
            );
        }

        $fcSession['guest']['firstname'] = 'guest';
        $fcSession['guest']['lastname'] = 'guest';
        $fcSession['guest']['email'] = $ppOrderDetails->payer->email_address;
        $fcSession['guest']['shipping']['city'] = $inData['shipping_address']['city'];

        /** @var ModelLocalisationCountry $cMdl */
        $cMdl = $this->loadModel('localisation/country');
        $countryInfo = $cMdl->getCountryByCode((string)$inData['shipping_address']['country_code'],2);
        $fcSession['guest']['shipping']['country_id'] = (int)$countryInfo['country_id'];
        $fcSession['guest']['shipping']['country'] = $countryInfo['name'];
        $fcSession['guest']['shipping']['iso_code_2'] = $countryInfo['iso_code_2'];
        $fcSession['guest']['shipping']['iso_code_3'] = $countryInfo['iso_code_3'];
        $fcSession['guest']['shipping']['address_format'] = $countryInfo['address_format'];

          /** @var ModelLocalisationZone $zMdl */
        $zMdl = $this->loadModel('localisation/zone');
        $zoneInfo = $zMdl->getZoneByCode((string)$inData['shipping_address']['admin_area_1'],(int)$countryInfo['country_id']);
        $fcSession['guest']['shipping']['zone'] = $zoneInfo['name'];
        $fcSession['guest']['shipping']['zone_id'] = (int)$zoneInfo['zone_id'];
        $fcSession['guest']['shipping']['postcode'] = $inData['shipping_address']['postal_code'];

        $fcSession['guest'] += $fcSession['guest']['shipping'];

        //$shNames = explode(' ', $ppOrderDetails->purchase_units[0]->shipping->name->full_name);
        $fcSession['guest']['shipping']['firstname'] = 'guest';
        $fcSession['guest']['shipping']['lastname'] = 'guest';

        if($inData['shipping_option']){
            //update order
            /** @see ControllerResponsesCheckoutPay::select_shipping() */
            $dd = new ADispatcher(
                'responses/checkout/pay/select_shipping',
                [
                    'selected' => $inData['shipping_option']['id'],
                    'selectFirst' => true
                ]
            );
            $dd->dispatch();
        }else {
            //create new order in the session
            /** @see ControllerResponsesCheckoutPay::updateOrderData() */
            $dd = new ADispatcher('responses/checkout/pay/updateOrderData');
            $dd->dispatch();
            /** @see ControllerResponsesCheckoutPay::select_shipping() */
            $dd = new ADispatcher(
                'responses/checkout/pay/select_shipping',
                [
                    'selected' => '',
                    'selectFirst' => true
                ]
            );
            $dd->dispatch();
        }

        $abcOrderId = $this->session->data['order_id'];
        if(!$abcOrderId) {
            throw new AException(AC_ERR_USER_ERROR, 'PayPal Express: Order not created after customer login into PP account!');
        }
        $this->shopping_data->save('cart',$cartKey, orderId: $abcOrderId);
        $this->shopping_data->save('paypal_data',$cartKey, orderId: $abcOrderId);
        //collect all data from order for response
        /** @var ModelCheckoutOrder $oMdl */
        $oMdl = $this->loadModel('checkout/order');
        $order_info = $oMdl->getOrder($abcOrderId);
        $this->data = [
            'currencyCode' => $this->currency->getCode(),
            'decPlace' => (int)$this->currency->getCurrency()['decimal_place']
        ];


        $this->prepareOrderData($order_info);
        $output = [
            'id' => randomWord(10),
            'purchase_units' => $this->data['pp']['purchase_units']
        ];

        foreach((array)$this->session->data['fc']['shipping_methods'] as $sMethod){
            foreach($sMethod['quote'] as $quote) {
                $output['purchase_units'][0]['shipping_options'][] = [
                    'id'     => $quote['id'],
                    'label'  => $quote['title'],
                    'amount' => [
                        'currency_code' => $this->data['currencyCode'],
                        'value'         => (string)preformatFloat(round((float)$quote['cost'], 2)),
                    ],
                    'type' => 'SHIPPING',
                    'selected' => ($this->session->data['fc']['shipping_method']['id'] == $quote['id'])
                ];
            }
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($output));
    }


    /*
     *  WEBHOOKS SECTION
     */
    public function webhookAuthCreated()
    {
        $this->swapCart();
        $this->processWebHook(
            'PAYMENT.AUTHORIZATION.CREATED',
            $this->config->get('paypal_commerce_status_success_unsettled'),
        );
    }

    public function webhookAuthVoided()
    {
        $this->swapCart();
        $this->processWebHook(
            'PAYMENT.AUTHORIZATION.VOIDED',
            $this->order_status->getStatusByTextId('canceled'),
        );
    }

    public function webhookCaptureCompleted()
    {
        $this->swapCart();
        $this->processWebHook(
            'PAYMENT.CAPTURE.COMPLETED',
            $this->config->get('paypal_commerce_status_success_settled'),
        );
    }

    public function webhookCaptureDenied()
    {
        $this->swapCart();
        $this->processWebHook(
            'PAYMENT.CAPTURE.DENIED',
            $this->config->get('paypal_commerce_status_decline'),
        );
    }

    public function webhookCapturePending()
    {
        $this->swapCart();
        $this->processWebHook(
            'PAYMENT.CAPTURE.PENDING',
            $this->config->get('paypal_commerce_status_capture_pending'),
        );
    }

    public function webhookCaptureRefunded()
    {
        $this->swapCart();
        $this->processWebHook(
            'PAYMENT.CAPTURE.REFUNDED',
            $this->config->get('paypal_commerce_status_refund'),
        );
    }

    protected function processWebHook($eventName, $orderStatusId)
    {
        $inData = $this->parseWebhookData();
        $orderId = $this->webhookValidate($inData, $eventName);
        if (!$orderId) {
            http_response_code('406');
            exit;
        }

        $this->data['event_name'] = $eventName;
        $this->data['order_id'] = $orderId;
        $this->data['order_status_id'] = $orderStatusId;

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        /** @var ModelCheckoutOrder $oMdl */
        $oMdl = $this->loadModel('checkout/order');
        $oMdl->update(
            $orderId,
            $this->data['order_status_id'],
            'Order updated by Paypal webhook request.'
        );
        //save input data into comments but hide from customer
        $oMdl->addHistory(
            $orderId,
            $this->data['order_status_id'],
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

        $ppOrderId = $inData['parsed']['resource']['supplementary_data']['related_ids']['order_id'];
        $orderId = $this->getOrderIdByPaypalOrderId($ppOrderId);
        /** @var ModelCheckoutOrder $oMdl */
        $oMdl = $this->loadModel('checkout/order');
        $orderInfo = $oMdl->getOrder($orderId);
        if (!$orderInfo) {
            $this->log->write(
                "Paypal webhook " . $eventName
                . ": order ID " . $orderId . " not found!\n Raw input data:\n"
                . var_export($inData['raw'], true)
                . "\n\nParsed data:\n" . var_export($inData['parsed'], true)
            );
            return false;
        } elseif (isset($ppOrderId)) {
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $this->loadModel('extension/paypal_commerce');
            $ppOrderInfo = $mdl->getPaypalOrder($orderId);
            if ($ppOrderInfo['charge_id'] != $ppOrderId) {
                $this->log->write(
                    "Paypal webhook " . $eventName
                    . ": order ID " . $orderId
                    . " \n Paypal related OrderId: " . $ppOrderId . " but not found in the database.\n"
                    . " \n Raw input data:\n"
                    . var_export($inData['raw'], true)
                    . "\n\nParsed data:\n" . var_export($inData['parsed'], true)
                );
                return false;
            }
        }
        return $orderId;
    }

    /**
     * Retrieves the order ID associated with a given reference ID.
     *
     * @param string $referenceId The reference ID used to search for the order ID.
     * @return int|false Returns the order ID as an integer if found, or false if the reference ID is invalid or not found.
     */
    function getOrderIdByReferenceId(string $referenceId)
    {
        if(!$referenceId){
            return false;
        }
        $result = $this->shopping_data->search(
            ['reference_id' => $referenceId],
            'paypal_data',
            options: ['sort' => 'order_id', 'order' => 'desc']
        );
        return (int)$result[0]['order_id'];
    }

    function getOrderIdByPaypalOrderId(string $ppOrderId)
    {
        if(!$ppOrderId){
            return false;
        }
        $result = $this->shopping_data->search(
            ['order_id' => $ppOrderId],
            'paypal_data',
            options: ['sort' => 'order_id', 'order' => 'desc']
        );
        return (int)$result[0]['order_id'];
    }

}