<?php

use Stripe\Customer;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;
use Stripe\PaymentIntent;

if ( ! defined( 'DIR_CORE' ) ) {
    header( 'Location: static_pages/' );
}

/**
 * Class ModelExtensionStripe
 *
 * @property ModelExtensionStripe $model_extension_stripe
 * @property ModelCheckoutOrder   $model_checkout_order
 */
class ModelExtensionStripe extends Model
{
    /**
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        parent::__construct($registry);
        grantStripeAccess($this->config);
    }
    public function getMethod( $address )
    {
        $this->load->language( 'stripe/stripe' );
        if ( $this->config->get( 'stripe_status' ) ) {
            $query = $this->db->query(
                "SELECT * 
					FROM `".$this->db->table( "zones_to_locations" )."` 
					WHERE location_id = '".(int)$this->config->get( 'stripe_location_id' )."' 
							AND country_id = '".(int)$address['country_id']."' 
							AND (zone_id = '".(int)$address['zone_id']."' OR zone_id = '0')" );

            if ( ! $this->config->get( 'stripe_location_id' ) ) {
                $status = true;
            } elseif ( $query->num_rows ) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        $payment_data = [];
        if ( $status ) {
            $payment_data = [
                'id'         => 'stripe',
                'title'      => $this->language->get( 'text_title' ),
                'sort_order' => $this->config->get( 'stripe_sort_order' ),
            ];
        }

        return $payment_data;
    }

    /**
     * @param $paymentData
     * @param $customer_stripe_id
     * @return array
     * @throws AException
     */
    public function processPayment($paymentData, $customer_stripe_id = '' )
    {

        $this->load->model( 'checkout/order' );
        $this->load->language( 'stripe/stripe' );
        $order_info = $this->model_checkout_order->getOrder($paymentData['order_id'] );

        try {
            //build charge data array
            $charge_data = [];
            $charge_data['amount'] = $paymentData['amount'];
            $charge_data['currency'] = $paymentData['currency'];
            $charge_data['description'] = $this->config->get( 'store_name' ).' Order #'. $paymentData['order_id'];
            $charge_data['receipt_email'] = $order_info['email'];

            if ( $this->config->get( 'stripe_settlement' ) == 'delayed' ) {
                $charge_data['capture'] = false;
            } else {
                $charge_data['capture'] = true;
            }

            //build cc details
            $cc_details = [
                'id' => $paymentData['cc_token'],
            ];

            if ( !$paymentData['use_saved_cc'] ) {
                if ( ! $cc_details['id'] ) {
                    $msg = new AMessage();
                    $msg->saveError(
                        'Stripe failed to get card token for order_id '. $paymentData['order_id'],
                        'Unable to use card for customer'.$customer_stripe_id
                    );
                    return ['error' => $this->language->get( 'error_system' )];
                }
            }

            $charge_data['card'] = $cc_details['id'];

            if ( $order_info['shipping_method'] ) {
                $shipping_name = $order_info['shipping_firstname'] ? : $order_info['firstname'];
                $shipping_name .= ' '.$order_info['shipping_lastname'] ? $order_info['shipping_lastname'] : $order_info['lastname'];
                $charge_data['shipping'] = [
                    'name'    => $shipping_name,
                    'phone'   => $order_info['telephone'],
                    'address' => [
                        'line1'       => $order_info['shipping_address_1'],
                        'line2'       => $order_info['shipping_address_2'],
                        'city'        => $order_info['shipping_city'],
                        'postal_code' => $order_info['shipping_postcode'],
                        'state'       => $order_info['shipping_zone'],
                        'country'     => $order_info['shipping_iso_code_2'],
                    ],
                ];
            }

            $charge_data['metadata'] = [];
            $charge_data['metadata']['order_id'] = $paymentData['order_id'];
            if ( $this->customer->getId() > 0 ) {
                $charge_data['metadata']['customer_id'] = (int)$this->customer->getId();
            }
            ADebug::variable( 'Processing stripe payment request: ', $charge_data );
            $response = Stripe\Charge::create( $charge_data )->toArray();
        } catch ( CardException $e ) {
            $response = [];
            // card errors
            $body = $e->getJsonBody();
            $response['error'] = $body['error']['message'];
            $response['code'] = $body['error']['code'];
            return $response;
        } catch ( InvalidRequestException $e ) {
            $response = [];
            // Invalid parameters were supplied to Stripe's API
            $body = $e->getJsonBody();
            $msg = new AMessage();
            $msg->saveError(
                'Stripe payment failed with invalid parameters!',
                'Stripe payment failed. '.$body['error']['message']
            );
            $response['error'] = $this->language->get( 'error_system' );

            return $response;
        } catch ( AuthenticationException $e ) {
            $response = [];
            // Authentication with Stripe's API failed
            $body = $e->getJsonBody();
            $msg = new AMessage();
            $msg->saveError(
                'Stripe payment failed to authenticate!',
                'Stripe payment failed to authenticate to the server. '.$body['error']['message']
            );
            $response['error'] = $this->language->get( 'error_system' );

            return $response;
        } catch ( ApiConnectionException $e ) {
            $response = [];
            // Network communication with Stripe failed
            $body = $e->getJsonBody();
            $msg = new AMessage();
            $msg->saveError(
                'Stripe payment connection has failed!',
                'Stripe payment failed connecting to the server. '.$body['error']['message']
            );
            $response['error'] = $this->language->get( 'error_system' );

            return $response;
        } catch ( ApiErrorException $e ) {
            $response = [];
            // Display a very generic error to the user, and maybe send
            $body = $e->getJsonBody();
            $msg = new AMessage();
            $msg->saveError(
                'Stripe payment has failed!',
                'Stripe processing failed. '.$body['error']['message']
            );
            $response['error'] = $this->language->get( 'error_system' );

            return $response;
        } catch ( Exception|Error $e ) {
            $response = [];
            // Something else happened, completely unrelated to Stripe
            $msg = new AMessage();
            $msg->saveError(
                'Unexpected error in stripe payment!',
                'Stripe processing failed. '.$e->getMessage()."(".$e->getCode().")"
            );
            $response['error'] = $this->language->get( 'error_system' );
            //log in AException
            $ae = new AException( $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
            ac_exception_handler( $ae );
            return $response;
        }

        //we still have no result. something unexpected happen
        if ( empty( $response ) ) {
            $response['error'] = $this->language->get( 'error_system' );

            return $response;
        }

        ADebug::variable( 'Processing stripe payment response: ', $response );

        //Do we have an error? exit with no records
        if ( $response['failure_message'] || $response['failure_code'] ) {
            $response['error'] = $response['failure_message'];
            $response['code'] = $response['failure_code'];

            return $response;
        }

        $message = 'Order id: '. $paymentData['order_id']."\n";
        $message .= 'Charge id: '.$response['id']."\n";
        $message .= 'Transaction Timestamp: '.date( 'm/d/Y H:i:s', $response['created'] );

        if ( $response['paid'] ) {
            //finalize order only if payment is a success
            $this->recordOrder( $order_info, $response );

            if ( $this->config->get( 'stripe_settlement' ) == 'automatic' ) {
                //auto complete the order in settled mode
                $this->model_checkout_order->confirm(
                    $paymentData['order_id'],
                    $this->config->get( 'stripe_status_success_settled' )
                );
            } else {
                //complete the order in unsettled mode
                $this->model_checkout_order->confirm(
                    $paymentData['order_id'],
                    $this->config->get( 'stripe_status_success_unsettled' )
                );
            }
        } else {
            // Some other error, assume payment declined
            $this->model_checkout_order->addHistory(
                $paymentData['order_id'],
                $this->config->get( 'stripe_status_decline' ),
                $message
            );
            $response['error'] = "Payment has failed! ".$response['failure_message'];
            $response['code'] = $response['failure_code'];
        }

        return $response;
    }

    /** Record order with stripe database
     * @param array $orderInfo
     * @param array $stripeResponseData
     * @return int
     * @throws AException
     */
    public function recordOrder($orderInfo, $stripeResponseData )
    {
        $test_mode = $this->config->get( 'stripe_test_mode' ) ? 1 : 0;
        $this->db->query(
            "INSERT INTO `".$this->db->table( "stripe_orders" )."` 
			SET `order_id` = '".(int)$orderInfo['order_id']."', 
				`charge_id` = '".$this->db->escape($stripeResponseData['id'] )."', 
				`charge_id_previous` = '".$this->db->escape($stripeResponseData['id'] )."', 
				`stripe_test_mode` = '".(int)$test_mode."', 
				`date_added` = now()"
        );

        return $this->db->getLastId();
    }

    /**
     * @param int $customerId
     *
     * @return false| string
     * @throws AException
     */
    public function getStripeCustomerID($customerId )
    {
        if ( !$customerId ) {
            return false;
        }

        $test_mode = $this->config->get( 'stripe_test_mode' ) ? 1 : 0;
        $query = $this->db->query( "SELECT sc.customer_stripe_id
									FROM ".$this->db->table( "stripe_customers" )." sc  
									WHERE sc.customer_id = '".(int)$customerId."' 
										AND sc.stripe_test_mode = '".(int)$test_mode."'"
        );

        return $query->row['customer_stripe_id'];
    }

    /**
     * @param int $customerId
     * @return false|Customer
     * @throws AException
     */
    public function getStripeCustomer($customerId )
    {
        $customer_stripe_id = $this->getStripeCustomerID( $customerId );
        if ( !$customer_stripe_id) {
            return false;
        }
        try {
            return Stripe\Customer::retrieve( $customer_stripe_id );
        } catch ( Exception|Error $e ) {
            //log in AException
            $ae = new AException( $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
            ac_exception_handler( $ae );
        }
        return false;
    }

    /**
     * @param ACustomer $customer
     *
     * @return null|Stripe\Customer
     */
    public function createStripeCustomer( $customer )
    {

        try {
            require_once( DIR_EXT.'stripe/core/stripe_modules.php' );
            grantStripeAccess( $this->config );

            $stripe_customer = Stripe\Customer::create( [
                "email"       => $customer->getEmail(),
                "description" => "Customer ID: ".$customer->getId(),
            ]);

            if ( $stripe_customer['id'] ) {
                //create stripe customer entry
                $test_mode = $this->config->get( 'stripe_test_mode' ) ? 1 : 0;
                $this->db->query( "INSERT INTO `".$this->db->table( "stripe_customers" )."` 
					SET `customer_id` = '".(int)$customer->getId()."', 
						`customer_stripe_id` = '".$this->db->escape( $stripe_customer['id'] )."', 
						`stripe_test_mode` = '".(int)$test_mode."', 
						`date_added` = now()
				" );
            }

            return $stripe_customer;

        } catch ( Exception|Error $e ) {
            //log in AException
            $ae = new AException( $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
            ac_exception_handler( $ae );

            return null;
        }
    }

    /**
     * @param array $data
     * @return array|PaymentIntent
     */

    public function createPaymentIntent($data)
    {
        try {
            $response = PaymentIntent::create( $data );
            $this->session->data['stripe']['pi']['id'] = $response['id'];
            return $response;
        } catch (Exception|Error $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * @param string $intentId
     * @return bool
     * @throws ApiErrorException
     */
    public function isPaymentIntentSuccessful($intentId)
    {
        $intent = PaymentIntent::retrieve($intentId);
        $this->data['pi_statuses'][$intentId] = $intent->status;
        if( in_array($intent->status, ['succeeded', 'requires_capture'])){
            return true;
        }
        return false;
    }

    /**
     * @param string $intentId
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function getPaymentIntent($intentId)
    {
        try {
            return PaymentIntent::retrieve($intentId);
        }catch ( Exception|Error $e ) {
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
        PaymentIntent::update($intentId, $data);
    }
}