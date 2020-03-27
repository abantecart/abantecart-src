<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ModelExtensionCardConnect
 *
 * @property ModelCheckoutOrder $model_checkout_order
 */
class ModelExtensionCardConnect extends Model
{
    public $data = array();
    public $error = array();
    protected $log;
    protected $logging;
    /**
     * @var CardConnectRestClient
     */
    protected $client;

    public function __construct(Registry $registry)
    {
        parent::__construct($registry);

        $this->logging = $this->config->get('cardconnect_logging');
        if ($this->logging) {
            $this->log = new ALog(DIR_LOGS.'cardconnect.txt');
        }
        $api_endpoint = 'https://'
                        .($this->config->get('cardconnect_test_mode') ? 'fts-uat.cardconnect.com' : 'fts.cardconnect.com')
                        .'/cardconnect/rest/';
        try {
            require_once DIR_EXT.'cardconnect/core/lib/CardConnectRestClient.php';
            $this->client = new CardConnectRestClient($api_endpoint,
                $this->config->get('cardconnect_username'),
                $this->config->get('cardconnect_password'));
        } catch (AException $e) {
            $registry->get('log')->write($e->getMessage());
        }
    }

    protected function _log($text)
    {
        if (!$this->logging) {
            return;
        }
        $this->log->write($text);
    }

    public function getMethod($address)
    {
        $this->load->language('cardconnect/cardconnect');

        if ($this->config->get('cardconnect_status')) {
            $sql = "SELECT *
					FROM ".$this->db->table('zones_to_locations')."
					WHERE location_id = '".(int)$this->config->get('cardconnect_location_id')."'
						   AND country_id = '".(int)$address['country_id']."'
						   AND (zone_id = '".(int)$address['zone_id']."' OR zone_id = '0')";
            $query = $this->db->query($sql);

            if (!$this->config->get('cardconnect_location_id')) {
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
                'id'         => 'cardconnect',
                'title'      => $this->language->get('text_title'),
                'sort_order' => $this->config->get('cardconnect_sort_order'),
            );
        }
        return $payment_data;
    }

    public function processPayment($pd)
    {
        $response = array();
        $this->load->model('checkout/order');
        $this->load->language('cardconnect/cardconnect');

        $order_info = $this->model_checkout_order->getOrder($pd['order_id']);
        if (!$order_info) {
            $this->_log('Order ID '.$pd['order_id'].' not found');
            return array('error' => 'Order not found');
        }

        $account = $expiry = $cvv2 = $profile_id = $capture = $bankaba = '';
        $existing_card = false;

        $customer_id = (int)$this->customer->getId();
        if ($customer_id) {
            $profile_id = $this->getProfileID($customer_id);
            if ($profile_id) {
                $this->_log("Located profile ID {$profile_id} for customer ID {$customer_id}");
            } else {
                $this->_log("No profile located for customer ID {$customer_id}");
            }
        }

        if (!isset($pd['method']) || $pd['method'] == 'card') {
            $this->_log('Method is card');

            if (isset($pd['save_cc']) && $pd['save_cc']
                && $this->config->get('cardconnect_save_cards_limit')
                && $customer_id
            ) {

                //create profile if now yet created
                if (!$profile_id) {
                    $profile_id = $this->createProfile(
                        array(
                            'customer_id'      => $customer_id,
                            'cc_number'        => $pd['cc_number'],
                            'cc_expire_month'  => $pd['cc_expire_month'],
                            'cc_expire_year'   => $pd['cc_expire_year'],
                            'cc_name'          => $order_info['payment_firstname'].' '.$order_info['payment_lastname'],
                            'cc_address_line1' => $order_info['payment_address_1'],
                            'cc_address_line2' => $order_info['payment_address_2'],
                            'cc_city'          => $order_info['payment_city'],
                            'cc_state'         => $order_info['payment_zone'],
                            'cc_country_code'  => $order_info['payment_iso_code_2'],
                            'cc_postcode'      => $order_info['payment_postcode'],
                        )
                    );
                    if ($profile_id) {
                        $this->_log("Created new profile ID {$profile_id} for customer ID {$customer_id}");
                    } else {
                        $this->_log("Failed to create profile for customer ID {$customer_id}");
                    }
                }
            } else {
                if ($pd['use_saved_cc'] && $customer_id) {
                    $existing_card = $this->getCard($pd['use_saved_cc'], $customer_id);
                }
            }

            if ($existing_card) {
                $account = $existing_card['token'];
                $expiry = $existing_card['expiry'];
                $cvv2 = '';
            } else {
                $account = $pd['cc_number'];
                $expiry = $pd['cc_expire_month'].$pd['cc_expire_year'];
                $cvv2 = $pd['cc_cvv2'];
            }
        } //echeck method
        else {
            $this->_log('Method is Echeck');
            $account = $this->request->post['account_number'];
            $bankaba = $this->request->post['routing_number'];
        }

        if ($this->config->get('cardconnect_settlement') == 'payment') {
            $capture = 'Y';
            $type = 'payment';
            $status = 'New';
            $order_status_id = $this->config->get('cardconnect_status_success_settled');
        } else {
            $capture = 'N';
            $type = 'auth';
            $status = 'New';
            $order_status_id = $this->config->get('cardconnect_status_success_unsettled');
        }

        $data = array(
            'merchid'  => $this->config->get('cardconnect_merchant_id'),
            'account'  => $account,
            'expiry'   => $expiry,
            'cvv2'     => $cvv2,
            'amount'   => $pd['amount'],
            'currency' => $pd['currency'],
            'orderid'  => $order_info['order_id'],
            'name'     => $order_info['payment_firstname'].' '.$order_info['payment_lastname'],
            'address'  => $order_info['payment_address_1'],
            'city'     => $order_info['payment_city'],
            'region'   => $order_info['payment_zone'],
            'country'  => $order_info['payment_iso_code_2'],
            'postal'   => $order_info['payment_postcode'],
            'email'    => $order_info['email'],
            'phone'    => $order_info['telephone'],
            'ecomind'  => 'E',
            'tokenize' => 'Y',
            'profile'  => $profile_id,
            'capture'  => $capture,
            'bankaba'  => $bankaba,
        );

        $this->_log('CardConnect '.($capture == 'Y' ? 'capture' : 'authorize').' transaction. Request: '.var_export($data, true));
        try {
            $response_data = $this->client->authorizeTransaction($data);
        } catch (Pest_Exception $e) {
            $this->_log('CardConnect Rest Library Error! '.$e->getMessage());
            $response_data = array();
        }

        $this->_log('CardConnect response: '.var_export($response_data, true));

        if (isset($response_data['respstat']) && $response_data['respstat'] == 'A') {
            $this->load->model('checkout/order');
            $payment_method = 'card';

            $this->model_checkout_order->addOrderHistory($order_info['order_id'], $order_status_id);
            $order_info = array_merge($order_info, $response_data);
            $cardconnect_order_id = $this->addOrder($order_info, $payment_method);
            $this->addTransaction($cardconnect_order_id, $type, $status, $order_info);

            if (isset($response_data['profileid'])
                && isset($pd['save_cc'])
                && $pd['save_cc']
                && $this->config->get('cardconnect_save_cards_limit')
                && $this->customer->isLogged()
            ) {
                $this->_log('Saving card reference.');
                $this->addCard(
                    $this->customer->getId(),
                    $response_data['profileid'],
                    $response_data['token'],
                    substr($response_data['account'], -4),
                    $expiry
                );
            }

            //auto complete the order in settled mode
            $this->model_checkout_order->confirm(
                $pd['order_id'],
                $order_status_id
            );
            $this->_log("Update order {$pd['order_id']} with Status ID: {$order_status_id}");

            $response['paid'] = true;
            $response['success'] = $this->html->getSecureURL('checkout/success', '', true);

        } else {
            //stay in status incomplete, as order not yet paid
            $this->model_checkout_order->addHistory(
                $pd['order_id'],
                0,
                "Payment status: ".(isset($response_data['resptext']) ? $response_data['resptext'] : 'unknown').", 
				Transaction Number: ".(isset($response_data['retref']) ? $response_data['retref'] : 'unknown')
            );
            $this->_log("Update order {$pd['order_id']} with: ".(isset($response_data['resptext']) ? $response_data['resptext'] : 'unknown'));

            $response['error'] = (isset($response_data['resptext']) ? $response_data['resptext'] : 'Transaction declined!');
        }
        return $response;
    }

    public function getYears()
    {
        $years = array();

        $today = getdate();

        for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
            $years[] = array(
                'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
                'value' => strftime('%y', mktime(0, 0, 0, 1, 1, $i)),
            );
        }

        return $years;
    }

    public function getCard($token, $customer_id)
    {
        $query = $this->db->query(
            "SELECT * 
				FROM ".$this->db->table('cardconnect_cards')."  
				WHERE `token` = '".$this->db->escape($token)."' 
					AND `customer_id` = '".(int)$customer_id."'");

        if ($query->num_rows) {
            return $query->row;
        } else {
            return false;
        }
    }

    public function getCards($customer_id)
    {
        $query = $this->db->query(
            "SELECT * 
				FROM ".$this->db->table('cardconnect_cards')." 
				WHERE `customer_id` = '".(int)$customer_id."'");

        return $query->rows;
    }

    public function addCard($customer_id, $profileid, $token, $account, $expiry, $type = '')
    {
        $sql = "REPLACE INTO ".$this->db->table('cardconnect_cards')."
				SET `customer_id` = '".(int)$customer_id."', 
					`profileid` = '".$this->db->escape($profileid)."', 
					`token` = '".$this->db->escape($token)."', 
					`type` = '".$this->db->escape($type)."', 
					`account` = '".$this->db->escape($account)."', 
					`expiry` = '".$this->db->escape($expiry)."', 
					`date_added` = NOW()";
        $this->_log($sql);
        $this->db->query($sql);
    }

    /**
     * @param string $card_token
     * @param int    $customer_id
     */
    public function deleteCard($card_token, $customer_id)
    {
        $this->db->query(
            "DELETE FROM ".$this->db->table('cardconnect_cards')." 
					WHERE `token` = '".$this->db->escape($card_token)."' 
						AND `customer_id` = '".(int)$customer_id."'");
    }

    /**
     * @param array  $order_info
     * @param string $payment_method
     *
     * @return int
     */
    public function addOrder($order_info, $payment_method)
    {
        $this->db->query(
            "INSERT INTO ".$this->db->table('cardconnect_orders')." 
					SET `order_id` = '".(int)$order_info['order_id']."', 
						`cardconnect_test_mode` = '".(int)$this->config->get('cardconnect_test_mode')."',
						`customer_id` = '".(int)$this->customer->getId()."', 
						`payment_method` = '".$this->db->escape($payment_method)."', 
						`retref` = '".$this->db->escape($order_info['retref'])."', 
						`authcode` = '".$this->db->escape($order_info['authcode'])."', 
						`currency_code` = '".$this->db->escape($order_info['currency'])."', 
						`total` = '".$this->currency->format($order_info['total'], $order_info['currency'], false, false)."', 
						`date_added` = NOW()");
        return $this->db->getLastId();
    }

    public function addTransaction($cardconnect_order_id, $type, $status, $order_info)
    {
        $this->db->query(
            "INSERT INTO ".$this->db->table('cardconnect_order_transactions')." 
					SET `cardconnect_order_id` = '".(int)$cardconnect_order_id."', 
						`type` = '".$this->db->escape($type)."', 
						`retref` = '".$this->db->escape($order_info['retref'])."', 
						`amount` = '".(float)$this->currency->format($order_info['total'], $order_info['currency'], false, false)."', 
						`status` = '".$this->db->escape($status)."', 
						`date_modified` = NOW(), 
						`date_added` = NOW()"
        );
    }

    public function updateTransactionStatusByRetref($retref, $status)
    {
        $this->db->query(
            "UPDATE ".$this->db->table('cardconnect_order_transactions')." 
					SET `status` = '".$this->db->escape($status)."', 
						`date_modified` = NOW() 
					WHERE `retref` = '".$this->db->escape($retref)."'");
    }

    /**
     * @param int $customer_id
     *
     * @return string
     */
    public function getProfileID($customer_id)
    {
        if (!(int)$customer_id) {
            return '';
        }
        $test_mode = $this->config->get('cardconnect_test_mode') ? 1 : 0;
        $query = $this->db->query("SELECT profileid
									FROM ".$this->db->table("cardconnect_customers")."  
									WHERE customer_id = '".(int)$customer_id."' 
										AND test_mode = '".(int)$test_mode."'"
        );
        return $query->row['profileid'];
    }

    public function createProfile($customer_data)
    {
        // Merchant ID
        $request = array(
            'merchid'     => $this->config->get('cardconnect_merchant_id'),
            'defaultacct' => "Y",
            'account'     => $customer_data['cc_number'],
            'expiry'      => $customer_data['card_expiry_month'].$customer_data['card_expiry_year'],
            'name'        => $customer_data['cc_name'],
            'address'     => $customer_data['cc_address_line1'].' '.$customer_data['cc_address_line2'],
            'city'        => $customer_data['cc_city'],
            'region'      => $customer_data['cc_state'],
            'country'     => $customer_data['cc_country_code'],
            'postal'      => $customer_data['cc_postcode'],
        );
        $this->_log('Request profile data : '.var_export($request, true));
        $response = $this->client->profileCreate($request);
        $this->_log('Response profile data : '.var_export($response, true));
        if ((int)$customer_data['customer_id']) {
            $this->db->query("REPLACE INTO ".$this->db->table("cardconnect_customers")."
						(profileid, customer_id, test_mode, date_added)
						VALUES ('".$this->db->escape($response['profileid'])."', 
								".(int)$customer_data['customer_id'].", 
								'".($this->config->get('cardconnect_test_mode') ? 1 : 0)."',
								NOW())");
        }
        return $response['profileid'];
    }
}