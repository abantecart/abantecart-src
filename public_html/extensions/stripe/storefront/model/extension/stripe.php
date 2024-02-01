<?php

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

    public function processPayment( $pd, $customer_stripe_id = '' )
    {

        $this->load->model( 'checkout/order' );
        $this->load->language( 'stripe/stripe' );
        $order_info = $this->model_checkout_order->getOrder( $pd['order_id'] );

        try {
            require_once( DIR_EXT.'stripe/core/stripe_modules.php' );
            grantStripeAccess( $this->config );

            //build charge data array
            $charge_data = [];
            $charge_data['amount'] = $pd['amount'];
            $charge_data['currency'] = $pd['currency'];
            $charge_data['description'] = $this->config->get( 'store_name' ).' Order #'.$pd['order_id'];
            $charge_data['receipt_email'] = $order_info['email'];

            if ( $this->config->get( 'stripe_settlement' ) == 'delayed' ) {
                $charge_data['capture'] = false;
            } else {
                $charge_data['capture'] = true;
            }

            //build cc details
            $cc_details = [
                'id' => $pd['cc_token'],
            ];

            if ( ! $pd['use_saved_cc'] ) {
                if ( ! $cc_details['id'] ) {
                    $msg = new AMessage();
                    $msg->saveError(
                        'Stripe failed to get card token for order_id '.$pd['order_id'],
                        'Unable to use card for customer'.$customer_stripe_id
                    );
                    $response = ['error' => $this->language->get( 'error_system' )];

                    return $response;
                }
            }

            //if saved card or new card used
            if ( $pd['use_saved_cc'] || $pd['save_cc'] ) {
                //first save customer and card if requested.
                if ( $pd['save_cc'] ) {
                    //we save customer only if saving credit card is requested.
                    if ( ! $customer_stripe_id ) {
                        $custom_response = $this->model_extension_stripe->createStripeCustomer( $this->customer );
                        if ( $custom_response['id'] ) {
                            $customer_stripe_id = $custom_response['id'];
                        }
                    }

                    $cc_id = $this->saveCreditCard( $cc_details['id'], $customer_stripe_id );
                    if ( ! $cc_id ) {
                        $msg = new AMessage();
                        $msg->saveError(
                            'Stripe failed to save credit card for order_id '.$pd['order_id'],
                            'Unable to save card for customer'.$customer_stripe_id
                        );
                        $response = ['error' => $this->language->get( 'error_system' )];

                        return $response;
                    }
                    $pd['use_saved_cc'] = $cc_id;
                }

                //prepare details
                $charge_data['customer'] = $customer_stripe_id;
                $charge_data['card'] = $pd['use_saved_cc'];

            } else {
                $charge_data['card'] = $cc_details['id'];
            }

            if ( $order_info['shipping_method'] ) {
                $shipping_name = $order_info['shipping_firstname'] ? $order_info['shipping_firstname'] : $order_info['firstname'];
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
            $charge_data['metadata']['order_id'] = $pd['order_id'];
            if ( $this->customer->getId() > 0 ) {
                $charge_data['metadata']['customer_id'] = (int)$this->customer->getId();
            }

            ADebug::variable( 'Processing stripe payment request: ', $charge_data );

            $response = Stripe\Charge::create( $charge_data );

        } catch ( Stripe\Error\Card $e ) {
            $response = [];
            // card errors
            $body = $e->getJsonBody();
            $response['error'] = $body['error']['message'];
            $response['code'] = $body['error']['code'];
            return $response;
        } catch ( Stripe\Error\InvalidRequest $e ) {
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
        } catch ( Stripe\Error\Authentication $e ) {
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
        } catch ( Stripe\Error\ApiConnection $e ) {
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
        } catch ( Stripe\Error\Base $e ) {
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
        } catch ( Exception $e ) {
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

        $message = 'Order id: '.(string)$pd['order_id']."\n";
        $message .= 'Charge id: '.(string)$response['id']."\n";
        $message .= 'Transaction Timestamp: '.(string)date( 'm/d/Y H:i:s', $response['created'] );

        if ( $response['paid'] ) {
            //finalize order only if payment is a success
            $this->recordOrder( $order_info, $response );

            if ( $this->config->get( 'stripe_settlement' ) == 'automatic' ) {
                //auto complete the order in settled mode
                $this->model_checkout_order->confirm(
                    $pd['order_id'],
                    $this->config->get( 'stripe_status_success_settled' )
                );
            } else {
                //complete the order in unsettled mode
                $this->model_checkout_order->confirm(
                    $pd['order_id'],
                    $this->config->get( 'stripe_status_success_unsettled' )
                );
            }
        } else {
            // Some other error, assume payment declined
            $this->model_checkout_order->addHistory(
                $pd['order_id'],
                $this->config->get( 'stripe_status_decline' ),
                $message
            );
            $response['error'] = "Payment has failed! ".$response['failure_message'];
            $response['code'] = $response['failure_code'];
        }

        return $response;
    }

    public function processSubscription( $pd, $plan_id, $customer_stripe_id = '' )
    {

        $response = [];
        $this->load->model( 'checkout/order' );
        $this->load->language( 'stripe/stripe' );
        $order_info = $this->model_checkout_order->getOrder( $pd['order_id'] );
        try {
            require_once( DIR_EXT.'stripe/core/stripe_modules.php' );
            grantStripeAccess( $this->config );
            $subscription_data = [];
            $subscription_data['items'] = [
                [
                    'plan' => $plan_id,
                ],
            ];
            //build cc details
            $cc_details = [
                'id'              => $pd['cc_token'],
                'name'            => $pd['cc_owner'],
                'address_line1'   => $order_info['payment_address_1'],
                'address_line2'   => $order_info['payment_address_2'],
                'address_city'    => $order_info['payment_city'],
                'address_zip'     => $order_info['payment_postcode'],
                'address_state'   => $order_info['payment_zone'],
                'address_country' => $order_info['payment_iso_code_2'],
            ];

            //we save customer if not yet saved.
            if ( ! has_value( $customer_stripe_id ) ) {
                $custom_response = $this->model_extension_stripe->createStripeCustomer( $this->customer );
                if ( $custom_response['id'] ) {
                    $customer_stripe_id = $custom_response['id'];
                }
            }
            $subscription_data['customer'] = $customer_stripe_id;
            $subscription_data['source'] = $pd['use_saved_cc'] ? $pd['use_saved_cc'] : $cc_details['id'];
            $subscription_data['metadata'] = [];
            $subscription_data['metadata']['order_id'] = $pd['order_id'];
            if ( $this->customer->getId() > 0 ) {
                $subscription_data['metadata']['customer_id'] = (int)$this->customer->getId();
            }
            ADebug::variable( 'Processing stripe subscription request: ', $subscription_data );
            Stripe\Subscription::create( $subscription_data );
            $response['result'] = true;
        } catch ( Stripe\Error\Card $e ) {
            $response = [];
            // card errors
            $body = $e->getJsonBody();
            $response['error'] = $body['error']['message'];
            $response['code'] = $body['error']['code'];

            return $response;
        } catch ( Stripe\Error\InvalidRequest $e ) {
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
        } catch ( Stripe\Error\Authentication $e ) {
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
        } catch ( Stripe\Error\ApiConnection $e ) {
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
        } catch ( Stripe\Error\Base $e ) {
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
        } catch ( Exception $e ) {
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

        return $response;
    }

    //record order with stripe database
    public function recordOrder( $order_info, $response )
    {
        if ($this->config->get('stripe_settlement') == 'automatic') {
            $settle_status = 1;
        } else {
            $settle_status = 0;
        }
        $test_mode = $this->config->get( 'stripe_test_mode' ) ? 1 : 0;
        $this->db->query( "INSERT INTO `".$this->db->table( "stripe_orders" )."` 
			SET `order_id` = '".(int)$order_info['order_id']."', 
				`charge_id` = '".$this->db->escape( $response['id'] )."', 
				`charge_id_previous` = '".$this->db->escape( $response['id'] )."', 
				`stripe_test_mode` = '".(int)$test_mode."', 
				`date_added` = now()
		" );

        return $this->db->getLastId();
    }

    /**
     * @param $customer_id
     *
     * @return bool| Stripe\Customer
     */
    public function getStripeCustomerID( $customer_id )
    {
        if ( ! has_value( $customer_id ) ) {
            return false;
        }

        $test_mode = $this->config->get( 'stripe_test_mode' ) ? 1 : 0;
        $query = $this->db->query( "SELECT sc.customer_stripe_id
									FROM ".$this->db->table( "stripe_customers" )." sc  
									WHERE sc.customer_id = '".(int)$customer_id."' 
										AND sc.stripe_test_mode = '".(int)$test_mode."'"
        );

        return $query->row['customer_stripe_id'];
    }

    public function getStripeCustomer( $customer_id )
    {
        $customer_stripe_id = $this->getStripeCustomerID( $customer_id );
        if ( ! has_value( $customer_stripe_id ) ) {
            return [];
        }
        try {
            require_once( DIR_EXT.'stripe/core/stripe_modules.php' );
            grantStripeAccess( $this->config );

            return Stripe\Customer::retrieve( $customer_stripe_id );
        } catch ( Exception $e ) {
            //log in AException
            $ae = new AException( $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
            ac_exception_handler( $ae );

            return null;
        }
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

        } catch ( Exception $e ) {
            //log in AException
            $ae = new AException( $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
            ac_exception_handler( $ae );

            return null;
        }
    }

    public function getStripeCustomerCCs( $customer_stripe_id )
    {
        if ( ! has_value( $customer_stripe_id ) ) {
            return [];
        }
        $cc_limit = $this->config->get( 'stripe_save_cards_limit' );
        if ( ! $cc_limit ) {
            return [];
        }

        try {
            require_once( DIR_EXT.'stripe/core/stripe_modules.php' );
            grantStripeAccess( $this->config );

            $cc_list = Stripe\Customer::retrieve( $customer_stripe_id )->cards->all( ['limit' => $cc_limit]);

            return $cc_list['data'];
        } catch ( Exception $e ) {
            //log in AException
            $ae = new AException( $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
            ac_exception_handler( $ae );

            return null;
        }
    }
    public function getStripeCustomerSources( $customer_stripe_id )
    {
        if ( ! has_value( $customer_stripe_id ) ) {
            return [];
        }
        $cc_limit = $this->config->get( 'stripe_save_cards_limit' );
        if ( ! $cc_limit ) {
            return [];
        }

        try {
            require_once( DIR_EXT.'stripe/core/stripe_modules.php' );
            grantStripeAccess( $this->config );

            $cc_list = Stripe\Customer::retrieve( $customer_stripe_id )->sources->all( ['limit' => $cc_limit]);

            return $cc_list['data'];
        } catch ( Exception $e ) {
            //log in AException
            $ae = new AException( $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
            ac_exception_handler( $ae );

            return null;
        }
    }

    public function saveCreditCard( $cc_token, $customer_stripe_id )
    {

        if ( ! has_value( $customer_stripe_id ) || ! has_value( $cc_token ) ) {
            return null;
        }

        try {
            require_once( DIR_EXT.'stripe/core/stripe_modules.php' );
            grantStripeAccess( $this->config );

            $cu = Stripe\Customer::retrieve( $customer_stripe_id );
            $cc = $cu->cards->create( ["source" => $cc_token]);

            return $cc['id'];
        } catch ( Exception $e ) {
            //log in AException
            $ae = new AException( $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
            ac_exception_handler( $ae );

            return null;
        }
    }

    public function deleteSource( $source_id, $customer_stripe_id )
    {
        if ( ! has_value( $customer_stripe_id ) || ! has_value( $source_id ) ) {
            return null;
        }

        try {
            require_once( DIR_EXT.'stripe/core/stripe_modules.php' );
            grantStripeAccess( $this->config );

            $customer = \Stripe\Customer::retrieve($customer_stripe_id);
            $cc = $customer->sources->retrieve($source_id)->detach();

            return $cc['status'];
        } catch ( Exception $e ) {
            //log in AException
            $ae = new AException( $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
            ac_exception_handler( $ae );

            return null;
        }
    }

    /**
     * for subscriptions
     *
     * @param int $product_id
     *
     * @return bool
     */

    public function getProductSubscription( $product_id )
    {
        $product_id = (int)$product_id;
        if ( ! $product_id ) {
            return false;
        }

        $result = $this->db->query(
            "SELECT subscription_plan_id
				FROM `".$this->db->table( "products`" )."
				WHERE product_id = '".(int)$product_id."'" );

        return $result->row['subscription_plan_id'];
    }

    public function createPaymentIntent($data)
    {
        $this->load->model('checkout/order');
        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);

            $response = PaymentIntent::create( $data );
            $this->session->data['stripe']['pi']['id'] = $response['id'];
            return $response;
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }

    }
    public function isPaymentIntentSuccessful($pi_id)
    {

        require_once(DIR_EXT.'stripe/core/stripe_modules.php');
        grantStripeAccess($this->config);

        $intent = PaymentIntent::retrieve($pi_id);
        $this->data['pi_statuses'][$pi_id] = $intent->status;
        if( in_array($intent->status, ['succeeded', 'requires_capture'])){
            return true;
        }
        return false;
    }

    public function getPaymentIntentStatus($pi_id)
    {
        if(isset($this->data['pi_statuses'][$pi_id])){
            return $this->data['pi_statuses'][$pi_id];
        }
        require_once(DIR_EXT.'stripe/core/stripe_modules.php');
        grantStripeAccess($this->config);

        $intent = PaymentIntent::retrieve($pi_id);
        return $intent->status;
    }

    /**
     * @param string $pi_id
     * @param array $data
     */
    public function updatePaymentIntent($pi_id, $data)
    {
        require_once(DIR_EXT.'stripe/core/stripe_modules.php');
        grantStripeAccess($this->config);

        PaymentIntent::update($pi_id, $data);
    }

}
