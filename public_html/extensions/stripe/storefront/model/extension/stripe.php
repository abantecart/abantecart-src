<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
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

use Stripe\Customer;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ModelExtensionStripe
 *
 * @property ModelExtensionStripe $model_extension_stripe
 * @property ModelCheckoutOrder $model_checkout_order
 */
class ModelExtensionStripe extends Model
{
    /** @var StripeClient|false */
    protected $stripeClient = false;

    /**
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->stripeClient = grantStripeAccess($this->config);
    }

    protected function getStripeClient()
    {
        if (!$this->stripeClient) {
            $this->stripeClient = grantStripeAccess($this->config);
        }
        return $this->stripeClient;
    }

    public function getMethod($address)
    {
        $this->load->language('stripe/stripe');
        if ($this->config->get('stripe_status')) {
            $query = $this->db->query(
                "SELECT * 
					FROM `" . $this->db->table("zones_to_locations") . "` 
					WHERE location_id = '" . (int)$this->config->get('stripe_location_id') . "' 
							AND country_id = '" . (int)$address['country_id'] . "' 
							AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

            if (!$this->config->get('stripe_location_id')) {
                $status = true;
            } elseif ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        $payment_data = [];
        if ($status) {
            $payment_data = [
                'id'         => 'stripe',
                'title'      => $this->language->get('text_title'),
                'sort_order' => $this->config->get('stripe_sort_order'),
            ];
        }

        return $payment_data;
    }

    /** Record order with stripe database
     * @param array $orderInfo
     * @param array $stripeResponseData
     * @return int
     * @throws AException
     */
    public function recordOrder($orderInfo, $stripeResponseData)
    {
        $test_mode = $this->config->get('stripe_test_mode') ? 1 : 0;
        $this->db->query(
            "INSERT INTO `" . $this->db->table("stripe_orders") . "` 
			SET `order_id` = '" . (int)$orderInfo['order_id'] . "', 
				`charge_id` = '" . $this->db->escape($stripeResponseData['id']) . "', 
				`charge_id_previous` = '" . $this->db->escape($stripeResponseData['id']) . "', 
				`stripe_test_mode` = '" . (int)$test_mode . "', 
				`date_added` = now()"
        );

        return $this->db->getLastId();
    }

    /**
     * @param int $orderId
     * @return array
     * @throws AException
     */
    public function getStripeOrder(int $orderId)
    {
        $result = $this->db->query(
            "SELECT * 
            FROM `" . $this->db->table("stripe_orders") . "` 
            WHERE `order_id` = '" . $orderId . "' 
            LIMIT 1"
        );
        return $result->row;
    }

    /**
     * @param int $customerId
     *
     * @return false| string
     * @throws AException
     */
    public function getStripeCustomerID($customerId)
    {
        if (!$customerId) {
            return false;
        }

        $test_mode = $this->config->get('stripe_test_mode') ? 1 : 0;
        $query = $this->db->query("SELECT sc.customer_stripe_id
									FROM " . $this->db->table("stripe_customers") . " sc  
									WHERE sc.customer_id = '" . (int)$customerId . "' 
										AND sc.stripe_test_mode = '" . (int)$test_mode . "'"
        );

        return $query->row['customer_stripe_id'];
    }

    /**
     * @param int $customerId
     * @return false|Customer
     * @throws AException
     */
    public function getStripeCustomer($customerId)
    {
        $customer_stripe_id = $this->getStripeCustomerID($customerId);
        if (!$customer_stripe_id) {
            return false;
        }
        try {
            return $this->getStripeClient()->customers->retrieve($customer_stripe_id);
        } catch (Exception|Error $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
        }
        return false;
    }

    /**
     * @param ACustomer $customer
     *
     * @return null|Stripe\Customer
     */
    public function createStripeCustomer($customer)
    {

        try {
            $stripe_customer = $this->getStripeClient()->customers->create([
                "email"       => $customer->getEmail(),
                "description" => "Customer ID: " . $customer->getId(),
            ]);

            if ($stripe_customer['id']) {
                //create stripe customer entry
                $test_mode = $this->config->get('stripe_test_mode') ? 1 : 0;
                $this->db->query("INSERT INTO `" . $this->db->table("stripe_customers") . "` 
					SET `customer_id` = '" . (int)$customer->getId() . "', 
						`customer_stripe_id` = '" . $this->db->escape($stripe_customer['id']) . "', 
						`stripe_test_mode` = '" . (int)$test_mode . "', 
						`date_added` = now()
				");
            }

            return $stripe_customer;

        } catch (Exception|Error $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);

            return null;
        }
    }

    /**
     * @param array $data
     * @return array|PaymentIntent
     */

    public function createPaymentIntent($data, $requestOptions = [])
    {
        try {
            $response = $this->getStripeClient()->paymentIntents->create($data, $requestOptions);
            $this->session->data['stripe']['pi']['id'] = $response['id'];
            $this->session->data['stripe']['pi_id'] = $response['id'];
            return $response;
        } catch (Exception|Error $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    protected function isReusableIntentStatus(string $status): bool
    {
        return in_array(
            $status,
            [
                'requires_payment_method',
                'requires_confirmation',
                'requires_action',
                'processing',
                'requires_capture',
            ],
            true
        );
    }

    protected function doesIntentMatchRequest(PaymentIntent $intent, array $data): bool
    {
        $intentOrderId = (string)($intent['metadata']['order_id'] ?? '');
        $intentCartKey = (string)($intent['metadata']['cart_key'] ?? '');
        $reqOrderId = (string)($data['metadata']['order_id'] ?? '');
        $reqCartKey = (string)($data['metadata']['cart_key'] ?? '');
        $intentAmount = (int)($intent['amount'] ?? 0);
        $reqAmount = (int)($data['amount'] ?? 0);
        $intentCurrency = strtolower((string)($intent['currency'] ?? ''));
        $reqCurrency = strtolower((string)($data['currency'] ?? ''));

        return $intentOrderId === $reqOrderId
            && $intentCartKey === $reqCartKey
            && $intentAmount === $reqAmount
            && $intentCurrency === $reqCurrency;
    }

    public function getOrCreatePaymentIntent(array $data, array $requestOptions = [])
    {
        $existingId = $this->session->data['stripe']['pi_id'] ?: ($this->session->data['stripe']['pi']['id'] ?? '');
        if ($existingId) {
            $intent = $this->getPaymentIntent($existingId);
            if ($intent instanceof PaymentIntent
                && $this->doesIntentMatchRequest($intent, $data)
                && $this->isReusableIntentStatus((string)$intent['status'])
            ) {
                return $intent;
            }
        }
        return $this->createPaymentIntent($data, $requestOptions);
    }

    /**
     * @param string $intentId
     * @return bool
     * @throws ApiErrorException
     */
    public function isPaymentIntentSuccessful($intentId)
    {
        $intent = $this->getStripeClient()->paymentIntents->retrieve($intentId);
        $this->data['pi_statuses'][$intentId] = $intent->status;
        if (in_array($intent->status, ['succeeded', 'requires_capture'])) {
            return true;
        }
        return false;
    }

    /**
     * @param string $intentId
     * @return PaymentIntent
     */
    public function getPaymentIntent($intentId)
    {
        try {
            return $this->getStripeClient()->paymentIntents->retrieve($intentId);
        } catch (Exception|Error $e) {
            return new stdClass();
        }
    }

    /**
     * @param string $intentId
     * @param array $data
     * @throws ApiErrorException
     */
    public function updatePaymentIntent($intentId, $data)
    {
        $this->getStripeClient()->paymentIntents->update($intentId, $data);
    }
}
