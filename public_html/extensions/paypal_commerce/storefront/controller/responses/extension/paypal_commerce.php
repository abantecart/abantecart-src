<?php /** @noinspection PhpMultipleClassDeclarationsInspection */
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

/** @noinspection PhpUndefinedClassInspection */

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
        $this->loadModel('checkout/order');
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
        $this->load->model('checkout/order');
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
        if ($this->config->get('paypal_commerce_pay_later_checkout_message_status')) {
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
        //need an order detail
        $data['order_info'] = $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $orderTotal = $taxes = $discount = $handling_fee = 0.0;

        foreach ($this->cart->getFinalTotalData() as $total) {
            if ($total['id'] == 'total') {
                $orderTotal = "" . round($total['converted'], 2);
            }

            $data['order_' . $total['id']] = $this->currency->convert(
                (float)$total['value'],
                $this->config->get('config_currency'),
                $currencyCode
            );

            if (in_array($total['total_type'], ['discount', 'promotion', 'coupon', 'balance'])) {
                $discount += abs($data['order_' . $total['id']]);
            } elseif ($total['total_type'] == 'fee' || str_ends_with($total['total_type'], '_fee')) {
                $handling_fee += abs($data['order_' . $total['id']]);
            } elseif ($total['total_type'] == 'tax') {
                $taxes += $data['order_' . $total['id']];
            }
        }
        $amountBreakdown = [
            'item_total' => [
                'value'         => "" . round($data['order_subtotal'], $decPlace),
                'currency_code' => $currencyCode,
            ],
            'tax_total'  => [
                'value'         => "" . round($taxes, $decPlace),
                'currency_code' => $currencyCode,
            ],
            'shipping'   => [
                'value'         => "" . round($data['order_shipping'], $decPlace),
                'currency_code' => $currencyCode,
            ],
            'discount'   => [
                'value'         => "" . round($discount, $decPlace),
                'currency_code' => $currencyCode,
            ],
            'handling'   => [
                'value'         => "" . round($handling_fee, $decPlace),
                'currency_code' => $currencyCode,
            ],
        ];

        $cartProducts = $this->cart->getProducts() + $this->cart->getVirtualProducts();

        $ppData['intent'] = strtoupper($this->config->get('paypal_commerce_transaction_type'));

        $ppData['payer'] = [
            'name'          => [
                'given_name' => $order_info['shipping_firstname'],
                'surname'    => $order_info['shipping_lastname'],
            ],
            'email_address' => $order_info['email'],
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
                            $currencyCode
                        ), 2),
                    'currency_code' => $currencyCode,
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
        $order_description = mb_strlen($order_description) > 127
            ? mb_substr($order_description, 0, 127)
            : $order_description;

        $ppData['purchase_units'][0] = [
            'custom_id'   => $this->session->data['order_id'] . '-' . UNIQUE_ID,
            'amount'      => [
                'value'         => $orderTotal,
                'currency_code' => $currencyCode
            ],
            'description' => $order_description
        ];
        if ($shipping) {
            $ppData['purchase_units'][0]['shipping'] = $shipping;
        }

        //allow breakdown only for store currency without enabled setting "display prices with tax"
        // to avoid conversion problems
        if ($this->config->get('config_currency') == $currencyCode && !$this->config->get('config_tax')) {
            $ppData['purchase_units'][0]['amount']['breakdown'] = $amountBreakdown;
            $ppData['purchase_units'][0]['items'] = $items;
        }

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
                    'method' => $this->config->get('paypal_commerce_3ds_policy')
                ]
            ];
        }

        try {
            $output = (array)$mdl->createPPOrder($ppData);
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
        if (!$this->csrftoken->isTokenValid()) {
            $output['error'] = $this->language->get('error_unknown');
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($output));
            return;
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

        if (!$response) {
            $output['error'] = 'Cannot establish a connection to the server OR transaction Id is unknown';
            $err = new AError(
                'Paypal Commerce: Cannot Get Order Information from Paypal. '
                . 'Paypal Transaction Id: ' . $transactionDetails['id']
            );
            $err->toLog()->toDebug();
        } //validate order info before confirmation
        elseif (
            $response->purchase_units[0]->custom_id != $order_info['order_id'] . '-' . UNIQUE_ID
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
                $this->model_checkout_order->confirm(
                    $orderId,
                    $orderStatusId ?: $this->order_status->getStatusByTextId('pending')
                );

                $this->model_checkout_order->updatePaymentMethodData(
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
    public function createTempOrder()
    {
        if (!$this->request->is_POST()) {
            http_response_code(406);
            return;
        }
        $inData = file_get_contents('php://input');
        $inData = (array)json_decode($inData, true);

        $output = [];

        $currencyCode = $this->currency->getCode();
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $this->loadModel('extension/paypal_commerce');
        $orderTotal = $inData['total']
            ? "" . round($this->currency->convert(
                $inData['total'],
                $this->config->get('config_currency'),
                $currencyCode
            ), 2)
            : $this->cart->getFinalTotal();

        $ppData['intent'] = strtoupper($this->config->get('paypal_commerce_transaction_type'));
        $ppData['purchase_units'][0] = [
            'amount'      => [
                'value'         => $orderTotal?: 0.01,
                'currency_code' => $currencyCode
            ],
            'description' => $inData['product_name'] ? substr($inData['product_name'], 0, 127) : ''
        ];

        $ppData['payment_source']['paypal'] = [
            'experience_context' => [
                'return_url'            => $inData['return_url'],
                'cancel_url'            => $inData['return_url'],
                'app_switch_preference' => [
                    'launch_paypal_app' => true
                ]
            ]
        ];

        try {
            $output = (array)$mdl->createPPOrder($ppData);
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


    public function prepareCheckout()
    {
        if (!$this->request->is_POST()) {
            http_response_code(406);
            return;
        }

        //mark paypal_commerce as the default payment method and use this mark in
        $this->session->data['paypal']['payment_method'] = [
            'id'         => 'paypal_commerce',
            'title'      => $this->language->get('text_title', 'paypal_commerce/paypal_commerce'),
            'sort_order' => $this->config->get('paypal_commerce_sort_order')
        ];

        //fill info for guest checkout
        if(!$this->customer->isLogged()) {
            $inData = file_get_contents('php://input');
            $inData = (array)json_decode($inData, true);
            $ppOrderId = $inData['orderID'];
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $this->loadModel('extension/paypal_commerce');
            $ppOrderDetails = $mdl->getOrder($ppOrderId);
            $session =& $this->session->data['guest'];
            $session['firstname'] = $ppOrderDetails->payer->name->given_name;
            $session['lastname'] = $ppOrderDetails->payer->name->surname;
            $session['email'] = $ppOrderDetails->payer->email_address;
            $session['shipping']['address_1'] = $ppOrderDetails->purchase_units[0]->shipping->address->address_line_1;
            $session['shipping']['city'] = $ppOrderDetails->purchase_units[0]->shipping->address->admin_area_2;

            /** @var ModelLocalisationCountry $cMdl */
            $cMdl = $this->loadModel('localisation/country');
            $countryInfo = $cMdl->getCountryByCode(
                (string)$ppOrderDetails->purchase_units[0]->shipping->address->country_code,
                2
            );
            $session['shipping']['country_id'] = (int)$countryInfo['country_id'];
            $session['shipping']['country'] = $countryInfo['name'];
            $session['shipping']['iso_code_2'] = $countryInfo['iso_code_2'];
            $session['shipping']['iso_code_3'] = $countryInfo['iso_code_3'];
            $session['shipping']['address_format'] = $countryInfo['address_format'];

              /** @var ModelLocalisationZone $zMdl */
            $zMdl = $this->loadModel('localisation/zone');
            $zoneInfo = $zMdl->getZoneByCode(
                (string)$ppOrderDetails->purchase_units[0]->shipping->address->admin_area_1,
                (int)$countryInfo['country_id']
            );
            $session['shipping']['zone'] = $zoneInfo['name'];
            $session['shipping']['zone_id'] = (int)$zoneInfo['zone_id'];
            $session['shipping']['postcode'] = $ppOrderDetails->purchase_units[0]->shipping->address->postal_code;

            $session += $session['shipping'];

            $shNames = explode(' ', $ppOrderDetails->purchase_units[0]->shipping->name->full_name);
            $session['shipping']['firstname'] = $shNames[0];
            $session['shipping']['lastname'] = $shNames[1];
        }
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
        if (!$this->webhookValidate($inData, $eventName)) {
            http_response_code('406');
            exit;
        }
        list($orderId,) = explode('-', $inData['parsed']['resource']['custom_id']);
        $this->data['event_name'] = $eventName;
        $this->data['order_id'] = $orderId;
        $this->data['order_status_id'] = $orderStatusId;

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->model_checkout_order->update(
            $orderId,
            $this->data['order_status_id'],
            'Order updated by Paypal webhook request.'
        );
        //save input data into comments but hide from customer
        $this->model_checkout_order->addHistory(
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

        list($orderId,) = explode('-', $inData['parsed']['resource']['custom_id']);
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
        return true;
    }

}