<?php
/** @noinspection PhpUndefinedClassInspection */

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Payments\AuthorizationsCaptureRequest;
use PayPalCheckoutSdk\Payments\AuthorizationsVoidRequest;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;
use PayPalCheckoutSdk\Webhooks\WebhooksCreateRequest;
use PayPalCheckoutSdk\Webhooks\WebhooksDeleteRequest as WebhooksDeleteRequestAlias;
use PayPalCheckoutSdk\Webhooks\WebhooksGetList;
use PayPalCheckoutSdk\Webhooks\WebhooksPatchRequest;
use PayPalHttp\HttpResponse;

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ModelExtensionPaypalCommerce
 *
 * @property ModelCatalogProduct $model_catalog_product
 * @property ModelSettingSetting $model_setting_setting
 */
class ModelExtensionPaypalCommerce extends Model
{
    /** @var PayPalHttpClient */
    protected $paypal;
    protected $supportedEvents = [
        'PAYMENT.AUTHORIZATION.CREATED' => 'webhookAuthCreated',
        'PAYMENT.AUTHORIZATION.VOIDED' => 'webhookAuthVoided',
        'PAYMENT.CAPTURE.COMPLETED' => 'webhookCaptureCompleted',
        'PAYMENT.CAPTURE.DENIED' => 'webhookCaptureDenied',
        'PAYMENT.CAPTURE.PENDING' => 'webhookCapturePending',
        'PAYMENT.CAPTURE.REFUNDED' => 'webhookCaptureRefunded',
    ];

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->paypal = getPaypalClient(
            $this->config->get('paypal_commerce_client_id'),
            $this->config->get('paypal_commerce_client_secret'),
            $this->config->get('paypal_commerce_test_mode')
        );
    }

    /**
     * @param int $order_id
     *
     * @return bool
     * @throws AException
     */
    public function getPaypalOrder($order_id)
    {
        $qry = $this->db->query(
            "SELECT * 
            FROM `" . $this->db->table("paypal_orders") . "` 
            WHERE `order_id` = '" . (int)$order_id . "' 
            LIMIT 1"
        );
        if ($qry->num_rows) {
            return $qry->row;
        } else {
            return false;
        }
    }

    /**
     * @param string $ch_id
     *
     * @return stdClass|null
     */
    public function getPaypalCharge($ch_id)
    {
        if (!has_value($ch_id)) {
            return null;
        }

        try {
            $request = new OrdersGetRequest($ch_id);
            /** @var stdClass $result */
            $result = $this->paypal->execute($request);
            return $result->result;
        } catch (Exception $e) {
            //log in AException
            $this->log->write('Paypal Error: ' . __METHOD__ . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * @param string $captureId
     * @param float $amount
     * @param string $currencyCode - upper case Currency Code
     *
     * @return stdClass|HttpResponse
     * @throws AException
     */
    public function refund($captureId, $amount, $currencyCode)
    {
        if (!has_value($captureId)) {
            return null;
        }
        try {
            $request = new CapturesRefundRequest($captureId);
            $request->body = [
                'amount' =>
                    [
                        'value' => $amount,
                        'currency_code' => strtoupper($currencyCode),
                    ],
            ];
            /** @var stdClass $result */
            $result = $this->paypal->execute($request);
            return $result->result;
        } catch (Exception $e) {
            throw new AException($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param string $captureId
     * @param float $amount
     * @param string $currencyCode - upper case Currency Code
     *
     * @return array|HttpResponse|stdClass|string
     * @throws AException
     */
    public function capture($captureId, $amount, $currencyCode)
    {
        if (!has_value($captureId)) {
            return null;
        }
        try {
            $request = new AuthorizationsCaptureRequest($captureId);
            $request->body = [
                'amount' =>
                    [
                        'value' => $amount,
                        'currency_code' => strtoupper($currencyCode),
                    ],
            ];
            $result = $this->paypal->execute($request);
            return $result->result;
        } catch (Exception $e) {
            throw new AException($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param string $authorizeId
     *
     * @return stdClass|HttpResponse
     * @throws AException
     */
    public function void($authorizeId)
    {
        if (!has_value($authorizeId)) {
            return null;
        }
        try {
            $request = new AuthorizationsVoidRequest($authorizeId);
            $result = $this->paypal->execute($request);
            return $result->result;
        } catch (Exception $e) {
            throw new AException($e->getCode(), $e->getMessage());
        }
    }

    public function getOrdersByInvoiceIds($invoiceIds)
    {
        if (!is_array($invoiceIds)) {
            return [];
        }
        $sql = 'SELECT order_id, transaction_id 
                FROM ' . $this->db->table('paypal_orders') . ' 
                WHERE transaction_id in (' . implode(
                ',', array_map(
                    function ($el) {
                        return sprintf("'%s'", $el);
                    }, $invoiceIds
                )
            ) . ')';
        $query = $this->db->query($sql);
        $result = [];
        foreach ($query->rows as $row) {
            $result[$row['transaction_id']] = $row['order_id'];
        }
        return $result;
    }

    /**
     * @throws Exception
     */
    public function updateWebHooks()
    {

        $request = new WebhooksGetList();
        /** @var stdClass $result */
        $result = $this->paypal->execute($request);
        $list = $result->result->webhooks;
        $we = [];

        foreach ($list as $wh) {
            $we[$wh->event_types[0]->name] = $wh;
        }

        foreach ($this->supportedEvents as $eventName => $method) {
            $whUrl = $this->html->getCatalogURL('r/extension/paypal_commerce/' . $method, '', '', true);
            if (isset($we[$eventName])) {
                $whId = $we[$eventName]->id;
                if ($we[$eventName]->url == $whUrl) {
                    //nothing to change
                    continue;
                }
                try {
                    $request = new WebhooksPatchRequest($whId);
                    $request->body = [
                        [
                            "op" => "replace",
                            "path" => "/url",
                            "value" => $whUrl,
                        ],
                    ];
                    $this->paypal->execute($request);
                } catch (Exception $e) {
                    $error_message = 'Webhook "' . $eventName . '" Patch Request Error: ' . $e->getMessage()
                        . "\n Data sent:\n" . var_export($request->body, true);
                    $this->log->write($error_message);
                    $error_message = 'Cannot to update webhook ' . $eventName . '.';
                    if (substr($whUrl, 0, 7) == 'http://') {
                        $error_message .= ' Webhook endpoint URL is not secure! Please set up SSL URL of store! ';
                    }
                    $error_message .= ' See error log for details';
                    throw new Exception($error_message);
                }
            } else {
                try {
                    $body = [
                        'url' => $whUrl,
                        'event_types' => [
                            [
                                'name' => $eventName,
                            ],
                        ],
                    ];
                    $request = new WebhooksCreateRequest($body);
                    $this->paypal->execute($request);
                } catch (Exception $e) {
                    $error_message = 'Webhook "' . $eventName . '" Create Request Error: ' . $e->getMessage()
                        . "\n Data sent:\n" . var_export($body, true);
                    $this->log->write($error_message);
                    $error_message = 'Cannot to create webhook ' . $eventName . '.';
                    if (substr($whUrl, 0, 7) == 'http://') {
                        $error_message .= ' Webhook endpoint URL is not secure! Please set up SSL URL of store! ';
                    }
                    $error_message .= ' See error log for details';
                    throw new Exception($error_message);
                }
            }
        }
    }
    /**
     * @throws Exception
     */
    public function deleteWebHooks()
    {

        $request = new WebhooksGetList();
        /** @var stdClass $result */
        $result = $this->paypal->execute($request);
        $list = $result->result->webhooks;
        $we = [];

        foreach ($list as $wh) {
            $we[$wh->event_types[0]->name] = $wh;
        }

        foreach ($this->supportedEvents as $eventName => $method) {
            $whUrl = $this->html->getCatalogURL('r/extension/paypal_commerce/' . $method, '', '', true);
            if (isset($we[$eventName])) {
                $whId = $we[$eventName]->id;
                try {
                    $request = new WebhooksDeleteRequestAlias($whId);
                    $this->paypal->execute($request);
                } catch (Exception $e) {
                    $error_message = 'Webhook "' . $eventName . '" Remove Request Error: ' . $e->getMessage()
                        . "\n Data sent:\n" . var_export($request->body, true);
                    $this->log->write($error_message);
                    $error_message = 'Cannot to remove webhook ' . $eventName . '.';
                    if (substr($whUrl, 0, 7) == 'http://') {
                        $error_message .= ' Webhook endpoint URL is not secure! Please set up SSL URL of store! ';
                    }
                    $this->log->write($error_message);
                }
            }
        }
    }
}
