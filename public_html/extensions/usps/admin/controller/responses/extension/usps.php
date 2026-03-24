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
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerResponsesExtensionUsps extends AController
{
    public function main()
    {
    }

    public function test()
    {
        $this->loadLanguage('extension/extensions');

        if (!$this->user->canModify('extension/extensions')) {
            $this->jsonResponse([
                'error_text' => sprintf(
                    $this->language->get('error_permission_modify'),
                    'extension/extensions'
                ),
            ]);
            return;
        }

        $clientId = trim((string)($this->request->post['usps_client_id'] ?? ''));
        $clientSecret = trim((string)($this->request->post['usps_client_secret'] ?? ''));
        if ($clientId === '' || $clientSecret === '') {
            $this->jsonResponse(['error_text' => 'Please fill API Client ID and API Client Secret first.']);
            return;
        }

        try {
            $baseUrl = $this->getUspsApiBaseUrl($this->request->post['usps_api_environment'] ?? 0);
            $token = $this->requestOauthToken($baseUrl, $clientId, $clientSecret);
            if ($token === '') {
                throw new Exception('USPS OAuth token was not returned.');
            }
        } catch (\Throwable $e) {
            $message = $this->extractUspsError($e);
            $this->jsonResponse(['error_text' => $message]);
            return;
        }

        $this->jsonResponse(['message' => 'Success! USPS API connection is working.']);
    }

    public function payment_token()
    {
        $this->loadLanguage('extension/extensions');

        if (!$this->user->canModify('extension/extensions')) {
            $this->jsonResponse([
                'error_text' => sprintf(
                    $this->language->get('error_permission_modify'),
                    'extension/extensions'
                ),
            ]);
            return;
        }

        $clientId = trim((string)($this->request->post['usps_client_id'] ?? ''));
        $clientSecret = trim((string)($this->request->post['usps_client_secret'] ?? ''));
        $crid = trim((string)($this->request->post['usps_payment_crid'] ?? ''));
        $mid = trim((string)($this->request->post['usps_payment_mid'] ?? ''));
        $manifestMid = trim((string)($this->request->post['usps_payment_manifest_mid'] ?? ''));
        $accountNumber = trim((string)($this->request->post['usps_payment_account_number'] ?? ''));

        if ($clientId === '' || $clientSecret === '') {
            $this->jsonResponse(['error_text' => 'Please fill API Client ID and API Client Secret first.']);
            return;
        }
        if ($crid === '' || $mid === '' || $manifestMid === '' || $accountNumber === '') {
            $this->jsonResponse([
                'error_text' => 'Please fill Customer Registration ID (CRID), Mailer ID (MID), Manifest MID and EPS Account Number first.',
            ]);
            return;
        }

        try {
            $baseUrl = $this->getUspsApiBaseUrl($this->request->post['usps_api_environment'] ?? 0);
            $oauthToken = $this->requestOauthToken($baseUrl, $clientId, $clientSecret);
            if ($oauthToken === '') {
                throw new Exception('USPS OAuth token was not returned.');
            }

            $paymentToken = $this->requestPaymentAuthorizationToken(
                $baseUrl,
                $oauthToken,
                $crid,
                $mid,
                $manifestMid,
                $accountNumber
            );
        } catch (\Throwable $e) {
            $message = $this->extractUspsError($e);
            $this->jsonResponse(['error_text' => $message]);
            return;
        }

        if ($paymentToken === '') {
            $this->jsonResponse(['error_text' => 'USPS Payment Authorization Token was not returned by USPS API.']);
            return;
        }

        $this->jsonResponse(
            [
                'message' => 'Success! USPS Payments token generation test passed.',
            ]
        );
    }

    public function label()
    {
        $order_id = $this->request->get['order_id'];
        if (!$order_id) {
            exit('Error: Unknown order!');
        }
        $this->loadModel('sale/order');
        $order_info = $this->model_sale_order->getOrder($order_id);
        if (!$order_info) {
            exit('Error: Order #' . $order_id . ' not found!');
        }

        $tn = $this->request->get['tn'];
        if (!$tn) {
            exit('Error: Unknown tracking number!');
        }

        /** @var ModelExtensionUsps $mdl */
        $mdl = $this->loadModel('extension/usps', 'storefront');
        $order_data = $mdl->getOrderShippingData($order_id);
        $data = (array)$order_data['data'];

        if (!empty($data['usps_data']['packages'])) {
            foreach ($data['usps_data']['packages'] as $package) {
                if ($package['tracking_number'] != $tn) {
                    continue;
                }

                $filename = DIR_ROOT . DS . 'admin' . DS . 'system' . DS . 'data' . DS
                    . 'usps_labels' . DS . 'order_label_' . $order_id . '.' . $tn . '.pdf';
                if (!is_file($filename) || !is_readable($filename)) {
                    echo 'File ' . $filename . ' is not readable or does not exist!';
                } else {
                    if (ob_get_level()) {
                        ob_end_clean();
                    }
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: attachment; filename="order_label_' . $order_id . '.' . $tn . '.pdf"');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . filesize($filename));
                    readfile($filename);
                }
                exit;
            }
        }

        exit('Label not found or cannot be shown.');
    }

    private function isDeveloperEnvironment($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int)$value === 1;
        }
        $normalized = strtolower(trim((string)$value));
        return in_array($normalized, ['1', 'true', 'yes', 'on', 'tem', 'developer', 'test'], true);
    }

    private function getUspsApiBaseUrl($apiEnvironment)
    {
        return $this->isDeveloperEnvironment($apiEnvironment)
            ? 'https://apis-tem.usps.com'
            : 'https://apis.usps.com';
    }

    private function requestOauthToken($baseUrl, $clientId, $clientSecret)
    {
        $config = \USPS\OAuthClientCredentials\Configuration::getDefaultConfiguration()
            ->setHost($baseUrl . '/oauth2/v3');
        $api = new \USPS\OAuthClientCredentials\Api\ResourcesApi(
            new \GuzzleHttp\Client(['timeout' => 20]),
            $config
        );
        $request = new \USPS\OAuthClientCredentials\Model\ClientCredentials([
            'grant_type'    => 'client_credentials',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
        ]);
        $response = $api->postToken($request);
        return (string)$response->getAccessToken();
    }

    private function requestPaymentAuthorizationToken(
        $baseUrl,
        $oauthToken,
        $crid,
        $mid,
        $manifestMid,
        $accountNumber
    ) {
        $payload = [
            'roles' => [
                [
                    'roleName'      => 'PAYER',
                    'CRID'          => $crid,
                    'MID'           => $mid,
                    'manifestMID'   => $manifestMid,
                    'accountType'   => 'EPS',
                    'accountNumber' => $accountNumber,
                ],
                [
                    'roleName'      => 'LABEL_OWNER',
                    'CRID'          => $crid,
                    'MID'           => $mid,
                    'manifestMID'   => $manifestMid,
                    'accountType'   => 'EPS',
                    'accountNumber' => $accountNumber,
                ],
            ],
        ];

        $response = (new \GuzzleHttp\Client(['timeout' => 20]))->post(
            $baseUrl . '/payments/v3/payment-authorization',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $oauthToken,
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ],
                'json'    => $payload,
            ]
        );

        $body = (string)$response->getBody();
        $json = json_decode($body, true);
        if (!is_array($json)) {
            return '';
        }

        return (string)($json['paymentAuthorizationToken'] ?? '');
    }

    private function extractUspsError(\Throwable $e)
    {
        $message = trim((string)$e->getMessage());
        $statusCode = (int)$e->getCode();
        $responseBody = '';

        if (method_exists($e, 'getResponseBody')) {
            $responseBody = $e->getResponseBody();
            if (is_object($responseBody) && method_exists($responseBody, '__toString')) {
                $responseBody = (string)$responseBody;
            }
        }

        // Guzzle RequestException path (Payments API call uses Guzzle directly).
        if ($responseBody === '' && method_exists($e, 'getResponse')) {
            $response = $e->getResponse();
            if ($response) {
                $statusCode = (int)$response->getStatusCode();
                $body = $response->getBody();
                if (is_object($body) && method_exists($body, '__toString')) {
                    $responseBody = (string)$body;
                }
            }
        }

        if ($responseBody !== '') {
            $json = json_decode($responseBody, true);
            if (is_array($json)) {
                $err = $json['error'] ?? [];
                $errorText = '';
                if (is_array($err)) {
                    $errorText = trim((string)($err['message'] ?? $err['description'] ?? ''));
                    if ($errorText === '' && isset($err['details']) && is_array($err['details'])) {
                        $parts = [];
                        foreach ($err['details'] as $detail) {
                            if (is_array($detail)) {
                                $parts[] = (string)($detail['message'] ?? $detail['description'] ?? '');
                            } elseif (is_scalar($detail)) {
                                $parts[] = (string)$detail;
                            }
                        }
                        $errorText = trim(implode(' ', array_filter($parts)));
                    }
                } elseif (is_scalar($err)) {
                    $errorText = trim((string)$err);
                }

                if ($errorText === '' && isset($json['message']) && is_scalar($json['message'])) {
                    $errorText = trim((string)$json['message']);
                }
                if ($errorText === '' && isset($json['error_description']) && is_scalar($json['error_description'])) {
                    $errorText = trim((string)$json['error_description']);
                }

                $message = $errorText ?: $message;
            } else {
                $message = $responseBody;
            }
        }

        if ($statusCode > 0) {
            return 'HTTP ' . $statusCode . ($message ? ': ' . $message : '');
        }
        return $message ?: 'Unknown USPS API error.';
    }

    private function jsonResponse(array $payload)
    {
        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($payload));
    }
}
