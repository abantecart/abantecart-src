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
        require_once DIR_EXT.'cardconnect/core/lib/CardConnectRestClient.php';
        $this->client = new CardConnectRestClient($api_endpoint, $this->config->get('cardconnect_username'), $this->config->get('cardconnect_password'));
    }

    protected function _log($text)
    {
        if (!$this->logging) {
            return;
        }
        $this->log->write($text);
    }

    public function getCardconnectOrder($order_id)
    {
        $qry = $this->db->query(
            "SELECT * 
				FROM `".$this->db->table("cardconnect_orders")."` 
				WHERE `order_id` = '".(int)$order_id."' LIMIT 1");

        if ($qry->num_rows) {
            $order = $qry->row;
            return $order;
        } else {
            return false;
        }
    }

    public function getCardConnectCharge($ch_id)
    {
        if (!has_value($ch_id)) {
            return array();
        }
        $this->_log('Try to inquire transaction # '.$ch_id);
        $output = $this->client->inquireTransaction($this->config->get('cardconnect_merchant_id'), $ch_id);
        $this->_log('API Response:  '."\n".var_export($output, true));
        if ($output) {
            $output['authorized'] = $this->getAuthorizedAmount($ch_id);
            $output['captured'] = $this->getTotalCaptured($ch_id);
            $output['refunded'] = $this->getRefundedAmount($ch_id);
        }
        return $output;
    }

    public function getTotalCaptured($ch_id)
    {
        $query = $this->db->query(
            "SELECT SUM(`amount`) AS total 
				FROM ".$this->db->table('cardconnect_order_transactions')." 
				WHERE `retref` = '".(int)$ch_id."' AND (`type` = 'payment' || `type` = 'capture')");
        return (float)$query->row['total'];
    }

    public function getAuthorizedAmount($ch_id)
    {
        $query = $this->db->query(
            "SELECT SUM(`amount`) AS `total` 
				FROM ".$this->db->table('cardconnect_order_transactions')." 
				WHERE `retref` = '".(int)$ch_id."' AND (`type` = 'auth')");
        return (float)$query->row['total'];
    }

    public function getRefundedAmount($ch_id)
    {
        $query = $this->db->query(
            "SELECT SUM(`amount`) AS `total` 
				FROM ".$this->db->table('cardconnect_order_transactions')." 
				WHERE `retref` = '".(int)$ch_id."' AND (`type` = 'refund')");
        return (float)$query->row['total'];
    }

    public function captureCardConnect($ch_id, $amount)
    {
        if (!has_value($ch_id)) {
            return array();
        }
        $this->_log('Try to capture amount '.$amount.' transaction # '.$ch_id);
        $response = $this->client->captureTransaction(
            array(
                "merchid" => $this->config->get('cardconnect_merchant_id'),
                "retref"  => $ch_id,
                //amount in cents!!!
                "amount"  => $amount * 100,
            )
        );

        $this->_log('API Response:  '."\n".var_export($response, true));
        return $response;
    }

    public function addTransaction($cardconnect_order_id, $type, $ch_id, $amount, $status)
    {
        $this->db->query("INSERT INTO ".$this->db->table('cardconnect_order_transactions')."
						SET `cardconnect_order_id` = '".(int)$cardconnect_order_id."', 
						`type` = '".$this->db->escape($type)."', 
						`retref` = '".$this->db->escape($ch_id)."', 
						`amount` = '".(float)$amount."', 
						`status` = '".$this->db->escape($status)."', 
						`date_modified` = NOW(), 
						`date_added` = NOW()");
    }

    public function refundCardConnect($ch_id, $amount)
    {

        if (!has_value($ch_id)) {
            return array();
        }
        $this->_log('Try to refund amount '.$amount.' transaction # '.$ch_id);
        $response = $this->client->refundTransaction(
            array(
                "merchid" => $this->config->get('cardconnect_merchant_id'),
                "retref"  => $ch_id,
                //amount in cents!!!
                "amount"  => $amount * 100,
            )
        );
        $this->_log('API Response:  '."\n".var_export($response, true));
        return $response;
    }

    public function voidCardConnect($ch_id)
    {

        if (!has_value($ch_id)) {
            return array();
        }
        $this->_log('Voiding transaction # '.$ch_id);
        $response = $this->client->voidTransaction(
            array(
                "merchid" => $this->config->get('cardconnect_merchant_id'),
                "retref"  => $ch_id,
            )
        );
        $this->_log('API Response:  '."\n".var_export($response, true));
        return $response;
    }
}
