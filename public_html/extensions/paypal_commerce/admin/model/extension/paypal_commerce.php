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

use PaypalServerSdkLib\ApiHelper;
use PaypalServerSdkLib\Models\CapturedPayment;
use PaypalServerSdkLib\Models\CaptureRequest;
use PaypalServerSdkLib\Models\Money;
use PaypalServerSdkLib\Models\Order;
use PaypalServerSdkLib\Models\PaymentAuthorization;
use PaypalServerSdkLib\Models\Refund;
use PaypalServerSdkLib\Models\RefundRequest;
use PaypalServerSdkLib\PaypalServerSdkClient;
use Unirest\Configuration;
use Unirest\HttpClient;

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
    /** @var PaypalServerSdkClient */
    protected $paypal;
    protected $supportedEvents = [
        'PAYMENT.AUTHORIZATION.CREATED' => 'webhookAuthCreated',
        'PAYMENT.AUTHORIZATION.VOIDED'  => 'webhookAuthVoided',
        'PAYMENT.CAPTURE.COMPLETED'     => 'webhookCaptureCompleted',
        'PAYMENT.CAPTURE.DENIED'        => 'webhookCaptureDenied',
        'PAYMENT.CAPTURE.PENDING'       => 'webhookCapturePending',
        'PAYMENT.CAPTURE.REFUNDED'      => 'webhookCaptureRefunded',
    ];

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->paypal = getPaypalClient(
            (string)$this->config->get('paypal_commerce_client_id'),
            (string)$this->config->get('paypal_commerce_client_secret'),
            (int)$this->config->get('paypal_commerce_test_mode')
        );
    }

    /**
     * @param int $order_id
     *
     * @return array|false
     * @throws AException
     */
    public function getPaypalOrder($order_id)
    {
        $qry = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table("paypal_orders") . " 
            WHERE order_id = '" . (int)$order_id . "' 
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
     * @return Order|null
     *
     * If API result comes as array, it is mapped to Order.
     */
    public function getPaypalCharge($ch_id): ?Order
    {
        if (!has_value($ch_id)) {
            return null;
        }

        $apiResponse = $this->paypal->getOrdersController()->getOrder(['id'=>$ch_id]);
        $result = $apiResponse->getResult();
        if ($result instanceof Order) {
            return $result;
        }
        if (is_array($result)) {
            try {
                $mappedResult = ApiHelper::getJsonHelper()->mapClass($result, Order::class);
                return $mappedResult instanceof Order ? $mappedResult : null;
            } catch (Exception|Error) {
                return null;
            }
        }

        return null;
    }

    /**
     * @param string $captureId
     * @param float $amount
     * @param string $currencyCode - upper case Currency Code
     *
     * @return Refund|null
     * @throws AException
     */
    public function refund(string $captureId, float $amount, string $currencyCode): ?Refund
    {
        if (!has_value($captureId)) {
            return null;
        }
        try {
            $refundRequest = new RefundRequest();
            $refundRequest->setAmount(
                new Money(strtoupper($currencyCode), (string)$amount)
            );

            $apiResponse = $this->paypal
                ->getPaymentsController()
                ->refundCapturedPayment(
                    [
                        'captureId' => $captureId,
                        'body'      => $refundRequest,
                    ]
                );

            return $apiResponse->getResult();
        } catch (Exception $e) {
            throw new AException($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param string $captureId
     * @param float $amount
     * @param string $currencyCode - upper case Currency Code
     *
     * @return CapturedPayment|null
     * @throws AException
     */
    public function capture(string $captureId, float $amount, string $currencyCode): ?CapturedPayment
    {
        if (!has_value($captureId)) {
            return null;
        }

        try {
            $captureRequest = new CaptureRequest();
            $captureRequest->setAmount(
                new Money(strtoupper($currencyCode), (string)$amount)
            );

            $apiResponse = $this->paypal
                ->getPaymentsController()
                ->captureAuthorizedPayment(
                    [
                        'authorizationId' => $captureId,
                        'body'            => $captureRequest,
                    ]
                );

            return $apiResponse->getResult();
        } catch (Exception $e) {
            throw new AException($e->getCode(), $e->getMessage());
        }
    }


    /**
     * @param string $authorizeId
     *
     * @return PaymentAuthorization|null
     * @throws AException
     */
    public function void(string $authorizeId): ?PaymentAuthorization
    {
        if (!has_value($authorizeId)) {
            return null;
        }

        try {
            $apiResponse = $this->paypal
                ->getPaymentsController()
                ->voidPayment(
                    [
                        'authorizationId' => $authorizeId,
                    ]
                );

            return $apiResponse->getResult();
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
        $existingByEventName = $this->getExistingWebhooksByEventName();

        foreach ($this->supportedEvents as $eventName => $method) {
            $whUrl = $this->buildWebhookUrl((string)$method);

            try {
                if (isset($existingByEventName[$eventName])) {
                    $this->patchWebhookUrlIfNeeded($existingByEventName[$eventName], $eventName, $whUrl);
                } else {
                    $this->createWebhook($eventName, $whUrl);
                }
            } finally {
                // to avoid rate limit blocking
                sleep(1);
            }
        }
    }

    /**
     * @return array<string, stdClass> event_name => webhook
     * @throws Exception
     */
    private function getExistingWebhooksByEventName(): array
    {
        // PayPal REST: GET /v1/notifications/webhooks
        // Current PHP-SDK (paypal/paypal-server-sdk) does not have WebhooksController,
        // do direct HTTP request

        $token = $this->paypal->getClientCredentialsAuth()->fetchToken();
        $accessToken = (string)$token->getAccessToken();

        $baseUri = rtrim($this->paypal->getBaseUri(), '/');
        $url = $baseUri . '/v1/notifications/webhooks';

        $ch = curl_init($url);
        if ($ch === false) {
            return [];
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $raw = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!is_string($raw) || $raw === '' || $httpCode < 200 || $httpCode >= 300) {
            return [];
        }

        $decoded = json_decode($raw);
        if (!is_object($decoded)) {
            return [];
        }

        /** @var array<int, object> $webhooks */
        $webhooks = (isset($decoded->webhooks) && is_array($decoded->webhooks)) ? $decoded->webhooks : [];

        $indexed = [];
        foreach ($webhooks as $wh) {
            if (!is_object($wh) || empty($wh->event_types) || !is_array($wh->event_types)) {
                continue;
            }

            foreach ($wh->event_types as $eventType) {
                $name = $eventType->name ?? null;
                if (is_string($name) && $name !== '') {
                    $indexed[$name] = $wh;
                }
            }
        }

        return $indexed;
    }

    private function buildWebhookUrl(string $method): string
    {
        return (string)$this->html->getCatalogURL(
            'r/extension/paypal_commerce/' . $method,
            '',
            '',
            true
        );
    }

    /**
     * @throws Exception
     */
    private function patchWebhookUrlIfNeeded(stdClass $webhook, string $eventName, string $whUrl): void
    {
        $currentUrl = isset($webhook->url) ? (string)$webhook->url : '';
        if ($currentUrl === $whUrl) {
            // nothing to change
            return;
        }

        $whId = isset($webhook->id) ? (string)$webhook->id : '';
        if ($whId === '') {
            throw new Exception('Cannot update webhook ' . $eventName . ': missing webhook id.');
        }

        // PayPal REST: PATCH /v1/notifications/webhooks/{webhook_id}
        // paypal/paypal-server-sdk currently has no WebhooksController => do direct HTTP request,
        // but reuse auth/baseUri from $this->paypal (PaypalServerSdkClient)
        $payload = [
            [
                'op'    => 'replace',
                'path'  => '/url',
                'value' => $whUrl,
            ],
        ];

        try {
            $token = $this->paypal->getClientCredentialsAuth()->fetchToken();
            $accessToken = (string)$token->getAccessToken();

            $baseUri = rtrim($this->paypal->getBaseUri(), '/');
            $url = $baseUri . '/v1/notifications/webhooks/' . rawurlencode($whId);

            $ch = curl_init($url);
            if ($ch === false) {
                throw new Exception('Cannot init HTTP client for PayPal webhook update request.');
            }

            $bodyJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
            if (!is_string($bodyJson) || $bodyJson === '') {
                throw new Exception('Cannot encode PayPal webhook update payload.');
            }

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => 'PATCH',
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_POSTFIELDS     => $bodyJson,
                CURLOPT_TIMEOUT        => 30,
            ]);

            $raw = curl_exec($ch);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErrNo = (int)curl_errno($ch);
            $curlErr = (string)curl_error($ch);
            curl_close($ch);

            if ($curlErrNo !== 0) {
                throw new Exception('PayPal webhook update request failed: ' . $curlErr);
            }

            if (!is_string($raw) || $raw === '' || $httpCode < 200 || $httpCode >= 300) {
                throw new Exception(
                    'PayPal webhook update request failed with HTTP ' . $httpCode
                    . '. Response: ' . (is_string($raw) ? $raw : '')
                );
            }
        } catch (Exception $e) {
            $this->logAndThrowWebhookException(
                action: 'update',
                eventName: $eventName,
                endpointUrl: $whUrl,
                exception: $e,
                payload: $payload
            );
        }
    }

    /**
     * @throws Exception
     */
    private function createWebhook(string $eventName, string $whUrl): void
    {
        $payload = [
            'url' => $whUrl,
            'event_types' => [
                [
                    'name' => $eventName,
                ],
            ],
        ];

        try {
            $token = $this->paypal->getClientCredentialsAuth()->fetchToken();
            $accessToken = (string)$token->getAccessToken();

            $baseUri = rtrim($this->paypal->getBaseUri(), '/');
            $url = $baseUri . '/v1/notifications/webhooks';

            $bodyJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
            if (!is_string($bodyJson) || $bodyJson === '') {
                throw new Exception('Cannot encode PayPal webhook create payload.');
            }

            $ch = curl_init($url);
            if ($ch === false) {
                throw new Exception('Cannot init HTTP client for PayPal webhook create request.');
            }

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_POSTFIELDS     => $bodyJson,
                CURLOPT_TIMEOUT        => 30,
            ]);

            $raw = curl_exec($ch);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErrNo = (int)curl_errno($ch);
            $curlErr = (string)curl_error($ch);
            curl_close($ch);

            if ($curlErrNo !== 0) {
                throw new Exception('PayPal webhook create request failed: ' . $curlErr);
            }

            if (!is_string($raw) || $raw === '' || $httpCode < 200 || $httpCode >= 300) {
                throw new Exception(
                    'PayPal webhook create request failed with HTTP ' . $httpCode
                    . '. Response: ' . (is_string($raw) ? $raw : '')
                );
            }
        } catch (Exception $e) {
            $this->logAndThrowWebhookException(
                action: 'create',
                eventName: $eventName,
                endpointUrl: $whUrl,
                exception: $e,
                payload: $payload
            );
        }
    }

    /**
     * @param string $action
     * @param string $eventName
     * @param string $endpointUrl
     * @param Exception $exception
     * @param array $payload
     *
     * @throws Exception
     */
    private function logAndThrowWebhookException(
        string $action,
        string $eventName,
        string $endpointUrl,
        Exception $exception,
        array $payload
    ): void {
        $this->log->write(
            'Webhook "' . $eventName . '" ' . ucfirst($action) . ' Request Error: ' . $exception->getMessage()
            . "\n Data sent:\n" . var_export($payload, true)
        );

        $message = 'Cannot to ' . $action . ' webhook ' . $eventName . '.';
        if ($this->isInsecureHttpUrl($endpointUrl)) {
            $message .= ' Webhook endpoint URL is not secure! Please set up SSL URL of store! ';
        }
        $message .= ' See error log for details';

        throw new Exception($message, (int)$exception->getCode(), $exception);
    }

    private function isInsecureHttpUrl(string $url): bool
    {
        return str_starts_with($url, 'http://');
    }

    /**
     * @throws Exception
     */
    public function deleteWebHooks()
    {
        $existingByEventName = $this->getExistingWebhooksByEventName();

        foreach ($this->supportedEvents as $eventName => $method) {
            $whUrl = $this->buildWebhookUrl((string)$method);

            try {
                if (!isset($existingByEventName[$eventName])) {
                    continue;
                }

                $webhook = $existingByEventName[$eventName];
                $whId = isset($webhook->id) ? (string)$webhook->id : '';
                if ($whId === '') {
                    throw new Exception('Cannot delete webhook ' . $eventName . ': missing webhook id.');
                }

                $this->deleteWebhookById($whId, $eventName, $whUrl);
            } finally {
                // to avoid of rate limit blocking
                sleep(1);
            }
        }
    }

    /**
     * PayPal REST: DELETE /v1/notifications/webhooks/{webhook_id}
     *
     * Uses HTTP client configured from $this->paypal (timeouts/proxy/env/auth).
     *
     * @throws Exception
     */
    private function deleteWebhookById(string $webhookId, string $eventName, string $endpointUrl): void
    {
        try {
            $token = $this->paypal->getClientCredentialsAuth()->fetchToken();
            $accessToken = (string)$token->getAccessToken();

            $baseUri = rtrim($this->paypal->getBaseUri(), '/');
            $url = $baseUri . '/v1/notifications/webhooks/' . rawurlencode($webhookId);

            $ch = curl_init($url);
            if ($ch === false) {
                throw new Exception('Cannot init HTTP client for PayPal webhook delete request.');
            }

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => 'DELETE',
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $accessToken,
                    'Accept: application/json',
                ],
                CURLOPT_TIMEOUT        => 30,
            ]);

            $rawBody = curl_exec($ch);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErrNo = (int)curl_errno($ch);
            $curlErr = (string)curl_error($ch);
            curl_close($ch);

            if ($curlErrNo !== 0) {
                throw new Exception('PayPal webhook delete request failed: ' . $curlErr);
            }

            if ($httpCode < 200 || $httpCode >= 300) {
                throw new Exception(
                    'PayPal webhook delete request failed with HTTP ' . $httpCode
                    . '. Response: ' . (is_string($rawBody) ? $rawBody : '')
                );
            }
        } catch (Exception $e) {
            $this->logAndThrowWebhookException(
                action: 'remove',
                eventName: $eventName,
                endpointUrl: $endpointUrl,
                exception: $e,
                payload: [
                    'webhook_id' => $webhookId,
                ]
            );
        }
    }
}
