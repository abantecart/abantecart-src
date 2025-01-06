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
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

/** @noinspection PhpMultipleClassDeclarationsInspection */

use JetBrains\PhpStorm\NoReturn;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

/**
 * Class ControllerResponsesExtensionStripe
 *
 * @property ModelExtensionStripe $model_extension_stripe
 */
class ControllerResponsesExtensionStripe extends AController
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
    }

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('stripe/stripe');
        $this->data['action'] = $this->html->getSecureURL(
            'extension/stripe/success',
            '&order_id=' . $this->session->data['order_id']
        );
        //build submit form
        $form = new AForm();
        $form->setForm(
            [
                'form_name' => 'stripe',
            ]
        );

        $this->data['form_open'] = $form->getFieldHtml(
            [
                'type' => 'form',
                'name' => 'stripe',
                'attr' => 'class = "stripe-form"',
                'csrf' => true,
            ]
        );

        //need an order details
        $this->loadModel('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $pAddress = [
            'postcode'  => $order_info['payment_postcode'],
            'address_1' => $order_info['payment_address_1'],
            'address_2' => $order_info['payment_address_2'],
            'city'      => $order_info['payment_city'],
            'zone'      => $order_info['payment_zone'],
            'country'   => $order_info['payment_country']
        ];

        $this->data['payment_address'] = array_filter($pAddress);

        $this->data['edit_address'] = $this->html->getSecureURL('checkout/address/payment');
        $this->data['email'] = $order_info['email'];
        $this->data['telephone'] = $order_info['telephone'];
        $this->data['payment_country'] = $order_info['payment_iso_code_2'];
        $this->data['payment_zone'] = $order_info['payment_zone_code'];

        $this->data['payment_address_1'] = trim($order_info['payment_address_1']);
        $this->data['payment_address_2'] = trim($order_info['payment_address_2']);
        $this->data['payment_city'] = trim($order_info['payment_city']);
        $this->data['payment_postcode'] = trim($order_info['payment_postcode']);

        $this->data['text_credit_card'] = $this->language->get('text_credit_card');
        $this->data['text_wait'] = $this->language->get('text_wait');

        $this->data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
        $this->data['payer_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_back'] = $this->language->get('button_back');

        $this->data['submit'] = $this->html->buildElement(
            [
                'type'  => 'button',
                'name'  => 'stripe_button',
                'text'  => $this->language->get('button_confirm'),
                'style' => 'button btn-orange pull-right',
                'icon'  => 'icon-ok icon-white',
            ]
        );
        $this->buildCardForm($order_info);

        $this->view->batchAssign($this->data);

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->processTemplate('responses/stripe.tpl');
    }

    protected function buildCardForm($order_info)
    {
        $this->data['stripe_rt'] = 'extension/stripe';
        $this->data['public_key'] = $this->config->get('stripe_test_mode')
            ? $this->config->get('stripe_pk_test')
            : $this->config->get('stripe_pk_live');
        $currency = $this->currency->getCode();
        $this->data['currency'] = strtolower($currency);

        $customer_stripe_id = null;
        if ($this->customer->getId() > 0) {
            $customer_stripe_id = $this->model_extension_stripe->getStripeCustomerID($this->customer->getId());
            if (!$customer_stripe_id) {
                $customer_stripe_id = $this->model_extension_stripe->createStripeCustomer($this->customer);
            }
        }

        $this->data['total_amount'] = round(
                $this->currency->convert(
                    $this->cart->getFinalTotal(),
                    $this->config->get('config_currency'),
                    $currency
                ),
                2
            )
            * 100;

        $piDetails = [
            'capture_method' => $this->config->get('stripe_settlement'),
            'amount'         => $this->data['total_amount'],
            'currency'       => $currency,
            'customer'       => $customer_stripe_id,
            'receipt_email'  => $this->customer->getEmail(),
            'shipping'       => [
                'address' =>
                    [
                        'line1'       => $order_info['shipping_address_1'],
                        'city'        => $order_info['shipping_city'],
                        'country'     => $order_info['shipping_iso_code_2'],
                        'line2'       => $order_info['shipping_address_2'],
                        'postal_code' => $order_info['shipping_postcode'],
                        'state'       => $order_info['shipping_zone'],
                    ],
                'name'    => $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'],
                'carrier' => $order_info['shipping_method'],
                'phone'   => $order_info['telephone'],
            ],
            "metadata"       => [
                "order_id" => $order_info['order_id'],
            ],
        ];

        $paymentMethods = unserialize($this->config->get('stripe_payment_method_list'));
        if ($paymentMethods) {
            $piDetails['payment_method_types'] = $paymentMethods;
        } else {
            $piDetails['automatic_payment_methods'] = ['enabled' => true];
        }

        $paymentIntent = $this->model_extension_stripe->createPaymentIntent($piDetails);
        if ($paymentIntent['error']) {
            $this->data['error'] = $paymentIntent['error'];
            $this->messages->saveWarning(
                'Stripe Error',
                $paymentIntent['error'] . ' OrderID:' . $order_info['order_id']
            );
        } else {
            $this->session->data['stripe']['pi_id_secret'] = $this->data['client_secret'] = $paymentIntent['client_secret'];
            $this->session->data['stripe']['pi_id'] = $paymentIntent['id'];
        }
    }

    public function success()
    {
        if ($this->session->data['fc']) {
            $aCart = get_class($this->cart);
            $this->registry->set('cart', new $aCart($this->registry, $this->session->data['fc']));
        }
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('stripe/stripe');

        //validate input
        $order_id = $this->session->data['order_id'] ?: $this->request->get['order_id'];
        /** @var ModelExtensionStripe $mdl */
        $mdl = $this->loadModel('extension/stripe');
        $this->loadLanguage('stripe/stripe');
        $pi_id = $this->request->get['payment_intent'];
        //compare payment intents from session and request
        if ($pi_id != $this->session->data['stripe']['pi_id']
            || $this->session->data['stripe']['pi_id_secret'] != $this->request->get['payment_intent_client_secret']
        ) {
            redirect($this->html->getSecureURL('index/home'));
        }

        $p_result = $this->finalizeOrder($pi_id, (int)$order_id);

        ADebug::variable('Processing payment result: ', $p_result);
        if ($p_result['error']) {
            // transaction failed
            $output['error'] = (string)$p_result['error'];
            if ($p_result['code']) {
                $output['error'] .= ' (' . $p_result['code'] . ')';
            }
        } else {
            if ($p_result['paid']) {
                $output['success'] = $this->html->getSecureURL('checkout/finalize');
            } else {
                //Unexpected result
                $pi = $mdl->getPaymentIntent($pi_id);
                $output['error'] = $pi?->last_payment_error['message'] . ' (' . $pi?->last_payment_error['code'] . ') '
                    . $this->language->get('error_payment_method');
                $this->log->write("Payment attempt failed: \n" . var_export($pi->toArray(), true));
                $this->log->write("Response: \n" . var_export($p_result, true));
            }
        }

        if ($output['error']) {
            $this->session->data['error_warning'] = $output['error'];
            $pKey = $this->session->data['fc']['product_key'];
            redirect($this->html->getSecureURL('checkout/fast_checkout', $pKey ? '&fc=1&product_key=' . $pKey : ''));
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        redirect($this->html->getSecureURL('checkout/finalize', '&order_id=' . $order_id));
    }

    protected function finalizeOrder(string $piId, int $orderId)
    {
        /** @var ModelCheckoutOrder $mdlOrder */
        $mdlOrder = $this->loadModel('checkout/order');
        /** @var ModelExtensionStripe $mdl */
        $mdl = $this->loadModel('extension/stripe');
        $output = [];

        try {
            $paymentStatus = $mdl->getPaymentIntent($piId)->status;
            if (in_array($paymentStatus, ['processing', 'succeeded', 'requires_capture'])) {
                $output['paid'] = true;
                $orderInfo = $mdlOrder->getOrder($orderId);
                $mdl->recordOrder($orderInfo, ['id' => $piId]);
                $orderStatus = ($paymentStatus == 'processing'
                    ? $this->order_status->getStatusByTextId('processing')
                    : ($this->config->get('stripe_settlement') == 'automatic'
                        ? $this->config->get('stripe_status_success_settled')
                        : $this->config->get('stripe_status_success_unsettled')));
                $output['order_status_id'] = $orderStatus;
                $mdlOrder->confirm($orderId, $orderStatus);
            } else {
                // Some other error, assume payment declined
                $orderStatus = $this->config->get('stripe_status_decline');
                $messageText = 'Unsuccessful payment Intent. ID ' . $piId . '.';
                $mdlOrder->update($orderId, $orderStatus, $messageText);
                $mdlOrder->addHistory($orderId, $orderStatus, $messageText);
                $output['order_status_id'] = $orderStatus;
            }
        } catch (Exception|Error $e) {
            $output['error'] = $e->getMessage();
        }
        return $output;
    }

    #[NoReturn] public function webhookPaymentIntentSucceeded()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $parsed = $this->parseWebhookRequest(__FUNCTION__, "payment_intent_succeeded");
        //if script still alive
        /**
         * @var int $orderId
         * @var string $invoiceId
         * @var array $payload
         * @var Event $event
         *
         */
        extract($parsed);

        $result = $this->finalizeOrder(
            $payload['data']['object']['id'],
            (int)$payload['data']['object']['metadata']['order_id']
        );
        if ($result['error']) {
            http_response_code(406);
            exit($result['error']);
        }
        $orderStatusId = $result['order_status_id'];

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        exit(
            'Stripe Webhook ' . __FUNCTION__ . ':' . 'Order #' . $orderId . ' status changed to '
            . $this->order_status->getStatusById($orderStatusId)
        );
    }

    protected function checkWebhookSecretKey($eventName)
    {
        $endpoint_secret = $this->config->get('stripe_webhook_secret_' . $eventName);
        if (!$endpoint_secret) {
            $error_text = "Unknown webhook secret key for event '" . $eventName
                . "'! Please check settings table, key 'stripe_webhook_secret_" . $eventName . "'";
            $this->messages->saveError(__FUNCTION__, $error_text);
            return false;
        }
        return true;
    }

    protected function parseWebhookRequest($method, $eventName)
    {
        if (!$this->config->get('stripe_status')) {
            $this->messages->saveNotice(
                'Stripe Webhook action notice. Calling method ' . $method,
                'Stripe extension is disabled, but webhook from API still sends requests! '
                . 'Please disabled webhooks to prevent this notice in the future'
            );
            http_response_code(403);
            exit('Error: Stripe Extension disabled!');
        }

        if (!$this->checkWebhookSecretKey($eventName)) {
            // Invalid signature
            http_response_code(401);
            exit('Error: Stripe Extension. Wrong webhook secret key!');
        }

        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        if (!$payload) {
            http_response_code(404);
            exit('Stripe Webhook ' . $method . '. Forbidden! ');
        }

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $this->config->get('stripe_webhook_secret_' . $eventName)
            );
            $payload = json_decode($payload, true);
        } catch (UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(406);
            exit('Stripe Webhook ' . $method . ' Error1:' . $e->getMessage());
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(401);
            exit('Stripe Webhook ' . $method . ' Error2:' . $e->getMessage());
        }

        if ($payload['data']['object']['object'] != 'payment_intent') {
            http_response_code(400);
            exit('Stripe Webhook ' . $method . ' Error3: ' . 'event object is not Payment Intent');
        }
        $pId = $payload['data']['object']['id'];
        $orderId = (int)$payload['data']['object']['metadata']['order_id'];

        if (!$orderId) {
            http_response_code(400);
            exit('Stripe Webhook ' . $method . ' Error: order ID is empty');
        }

        /** @var ModelExtensionStripe $mdl */
        $mdl = $this->loadModel('extension/stripe');
        $stripeOrderInfo = $mdl->getStripeOrder($orderId);

        if ($stripeOrderInfo && $pId != $stripeOrderInfo['charge_id']) {
            $this->messages->saveWarning(
                'Stripe ' . $method,
                'Order by Intent ' . $pId . ' not found in the table Stripe Orders!'
            );
            http_response_code(404);
            exit('Stripe Webhook ' . $method . ' Error: Order Intent does not match');
        }

        return compact('orderId', 'pId', 'payload', 'event');
    }
}