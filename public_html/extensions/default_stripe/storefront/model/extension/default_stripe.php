<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ModelExtensionDefaultStripe
 *
 * @property ModelCheckoutOrder $model_checkout_order
 */
class ModelExtensionDefaultStripe extends Model
{

    public function getMethod($address)
    {
        $this->load->language('default_stripe/default_stripe');
        if ($this->config->get('default_stripe_status') && $this->config->get('default_stripe_published_key')) {
            $query = $this->db->query(
                "SELECT * 
					FROM `".$this->db->table("zones_to_locations")."` 
					WHERE location_id = '".(int)$this->config->get('default_stripe_location_id')."' 
							AND country_id = '".(int)$address['country_id']."' 
							AND (zone_id = '".(int)$address['zone_id']."' OR zone_id = '0')");

            if (!$this->config->get('default_stripe_location_id')) {
                $status = true;
            } elseif ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        $payment_data = array();
        if ($status) {
            $payment_data = array(
                'id'         => 'default_stripe',
                'title'      => $this->language->get('text_title'),
                'sort_order' => $this->config->get('default_stripe_sort_order'),
            );
        }

        return $payment_data;
    }

    public function processPayment($pd, $customer_stripe_id = '')
    {
        $response = '';
        $this->load->model('checkout/order');
        $this->load->language('default_stripe/default_stripe');
        $order_info = $this->model_checkout_order->getOrder($pd['order_id']);

        try {
            require_once(DIR_EXT.'default_stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);

            //build charge data array
            $charge_data = array();
            $charge_data['amount'] = $pd['amount'];
            $charge_data['currency'] = $pd['currency'];
            $charge_data['description'] = $this->config->get('store_name').' Order #'.$pd['order_id'];
            $charge_data['statement_descriptor'] = 'Order #'.$pd['order_id'];
            $charge_data['receipt_email'] = $order_info['email'];
            $charge_data['source'] = $pd['cc_token'];

            if ($this->config->get('default_stripe_settlement') == 'delayed') {
                $charge_data['capture'] = false;
            } else {
                $charge_data['capture'] = true;
            }

            if ($order_info['shipping_method']) {
                $shipping_name = $order_info['shipping_firstname'] ? $order_info['shipping_firstname'] : $order_info['firstname'];
                $shipping_name .= '  '.($order_info['shipping_lastname'] ? $order_info['shipping_lastname'] : $order_info['lastname']);
                $charge_data['shipping'] = array(
                    'name'    => $shipping_name,
                    'phone'   => $order_info['telephone'],
                    'address' => array(
                        'line1'       => $order_info['shipping_address_1'],
                        'line2'       => $order_info['shipping_address_2'],
                        'city'        => $order_info['shipping_city'],
                        'postal_code' => $order_info['shipping_postcode'],
                        'state'       => $order_info['shipping_zone'],
                        'country'     => $order_info['shipping_iso_code_2'],
                    ),
                );
            }

            $charge_data['metadata'] = array();
            $charge_data['metadata']['order_id'] = $pd['order_id'];
            if ($this->customer->getId() > 0) {
                $charge_data['metadata']['customer_id'] = (int)$this->customer->getId();
            }

            ADebug::variable('Processing stripe payment request: ', $charge_data);
            $response = \Stripe\Charge::create($charge_data);

        } catch (\Stripe\Error\Card $e) {
            // card errors
            $body = $e->getJsonBody();
            $response = array(
                'error' => $body['error']['message'],
                'code'  => $body['error']['code'],
            );

            return $response;
        } catch (\Stripe\Error\InvalidRequest $e) {
            $response = array();
            // Invalid parameters were supplied to Stripe's API
            $body = $e->getJsonBody();
            $msg = new AMessage();
            $msg->saveError(
                'Stripe payment failed with invalid parameters!',
                'Stripe payment failed. '.$body['error']['message']
            );
            $response['error'] = $this->language->get('error_system').' ('.$e->getMessage().')';

            return $response;
        } catch (\Stripe\Error\Authentication $e) {
            $response = array();
            // Authentication with Stripe's API failed
            $body = $e->getJsonBody();
            $msg = new AMessage();
            $msg->saveError(
                'Stripe payment failed to authenticate!',
                'Stripe payment failed to authenticate to the server. '.$body['error']['message']
            );
            $response['error'] = $this->language->get('error_system').' ('.$e->getMessage().')';

            return $response;
        } catch (\Stripe\Error\ApiConnection $e) {
            $response = array();
            // Network communication with Stripe failed
            $body = $e->getJsonBody();
            $msg = new AMessage();
            $msg->saveError(
                'Stripe payment connection has failed!',
                'Stripe payment failed connecting to the server. '.$body['error']['message']
            );
            $response['error'] = $this->language->get('error_system').' ('.$e->getMessage().')';

            return $response;
        } catch (\Stripe\Error\Base $e) {
            $response = array();
            // Display a very generic error to the user, and maybe send
            $body = $e->getJsonBody();
            $msg = new AMessage();
            $msg->saveError(
                'Stripe payment has failed!',
                'Stripe processing failed. '.$body['error']['message']
            );
            $response['error'] = $this->language->get('error_system').' ('.$e->getMessage().')';

            return $response;
        } catch (\Exception $e) {
            $response = array();
            // Something else happened, completely unrelated to Stripe
            $msg = new AMessage();
            $msg->saveError(
                'Unexpected error in stripe payment!',
                'Stripe processing failed. '.$e->getMessage()." (".$e->getCode().")"
            );
            $response['error'] = $this->language->get('error_system');
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);

            return $response;
        }

        //we still have no result. something unexpected happen
        if (empty($response)) {
            $response = array();
            $response['error'] = $this->language->get('error_system');

            return $response;
        }

        ADebug::variable('Processing stripe payment response: ', $response);

        //Do we have an error? exit with no records
        if ($response['failure_message'] || $response['failure_code']) {
            $response['error'] = $response['failure_message'];
            $response['code'] = $response['failure_code'];

            return $response;
        }

        $message = 'Order id: '.(string)$pd['order_id']."\n";
        $message .= 'Charge id: '.(string)$response['id']."\n";
        $message .= 'Transaction Timestamp: '.(string)date('m/d/Y H:i:s', $response['created']);

        if ($response['paid']) {
            //finalize order only if payment is a success
            $this->model_checkout_order->addHistory(
                $pd['order_id'],
                $this->config->get('config_order_status_id'),
                $message
            );

            if ($this->config->get('default_stripe_settlement') == 'auto') {
                //auto complete the order in settled mode
                $this->model_checkout_order->confirm(
                    $pd['order_id'],
                    $this->config->get('default_stripe_status_success_settled')
                );
            } else {
                //complete the order in unsettled mode
                $this->model_checkout_order->confirm(
                    $pd['order_id'],
                    $this->config->get('default_stripe_status_success_unsettled')
                );
            }
        } else {
            // Some other error, assume payment declined
            $this->model_checkout_order->addHistory(
                $pd['order_id'],
                $this->config->get('default_stripe_status_decline'),
                $message
            );
            $response['error'] = "Payment has failed! ".$response['failure_message'];
            $response['code'] = $response['failure_code'];
        }

        return $response;
    }
}