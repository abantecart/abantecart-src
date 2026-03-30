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
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_token_service.php');
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_api_context.php');
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_error_parser.php');

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
            $baseUrl = UspsApiContext::getApiBaseUrl($this->request->post['usps_api_environment'] ?? 0);
            $tokenData = $this->getOauthToken(
                $baseUrl,
                $clientId,
                $clientSecret,
                $this->request->post['usps_api_environment'] ?? 0
            );
            if ($tokenData['token'] === '') {
                throw new Exception('USPS OAuth token was not returned.');
            }
        } catch (\Throwable $e) {
            $message = (new UspsErrorParser())->parseThrowable($e);
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

        $oauthFromCache = false;
        $paymentFromCache = false;
        try {
            $baseUrl = UspsApiContext::getApiBaseUrl($this->request->post['usps_api_environment'] ?? 0);
            $apiEnvironment = $this->request->post['usps_api_environment'] ?? 0;
            $oauthData = $this->getOauthToken($baseUrl, $clientId, $clientSecret, $apiEnvironment);
            $oauthToken = $oauthData['token'];
            $oauthFromCache = (bool)($oauthData['from_cache'] ?? false);
            if ($oauthToken === '') {
                throw new Exception('USPS OAuth token was not returned.');
            }

            $paymentData = $this->getPaymentAuthorizationToken(
                $baseUrl,
                $oauthToken,
                $apiEnvironment,
                $clientId,
                $crid,
                $mid,
                $manifestMid,
                $accountNumber
            );
            $paymentToken = $paymentData['token'];
            $paymentFromCache = (bool)($paymentData['from_cache'] ?? false);
            $this->verifyLabelsApiAccess($baseUrl, $oauthToken, $paymentToken);
        } catch (\Throwable $e) {
            $message = (new UspsErrorParser())->parseThrowable($e);
            $this->jsonResponse(['error_text' => $message]);
            return;
        }

        if ($paymentToken === '') {
            $this->jsonResponse(['error_text' => 'USPS Payment Authorization Token was not returned by USPS API.']);
            return;
        }

        $mode = ($oauthFromCache && $paymentFromCache)
            ? 'used cached OAuth and payment tokens'
            : 'generated/refreshed token(s) as needed';

        $this->jsonResponse([
            'message' => 'Success! USPS Labels API test passed; ' . $mode . '.',
        ]);
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

    private function getOauthToken($baseUrl, $clientId, $clientSecret, $apiEnvironment)
    {
        return $this->getTokenService()->getOauthToken(
            $baseUrl,
            $clientId,
            $clientSecret,
            $this->getOauthTokenCacheKey($apiEnvironment, $clientId)
        );
    }

    private function getPaymentAuthorizationToken(
        $baseUrl,
        $oauthToken,
        $apiEnvironment,
        $clientId,
        $crid,
        $mid,
        $manifestMid,
        $accountNumber
    ) {
        $cacheKey = $this->getPaymentTokenCacheKey(
            $apiEnvironment,
            $clientId,
            $crid,
            $mid,
            $manifestMid,
            $accountNumber
        );

        return $this->getTokenService()->getPaymentAuthorizationToken(
            $baseUrl,
            $oauthToken,
            $crid,
            $mid,
            $manifestMid,
            $accountNumber,
            $cacheKey
        );
    }

    private function verifyLabelsApiAccess($baseUrl, $oauthToken, $paymentToken)
    {
        $config = \USPS\Labels\Configuration::getDefaultConfiguration()
            ->setHost($baseUrl . '/labels/v3')
            ->setAccessToken($oauthToken);
        $api = new \USPS\Labels\Api\ResourcesApi(
            new \GuzzleHttp\Client(['timeout' => 20]),
            $config
        );

        // Non-destructive labels endpoint to verify token pair really works for labels operations.
        $api->getListLabelBranding($paymentToken, '1', '0', 'desc', 'createdDateTime');
    }

    private function getOauthTokenCacheKey($apiEnvironment, $clientId)
    {
        return UspsApiContext::buildHashKey(
            'usps.admin.oauth_token.',
            [
                UspsApiContext::getEnvironmentCode($apiEnvironment),
                $clientId,
            ]
        );
    }

    private function getPaymentTokenCacheKey(
        $apiEnvironment,
        $clientId,
        $crid,
        $mid,
        $manifestMid,
        $accountNumber
    ) {
        $storeId = isset($this->request->post['store_id'])
            ? (int)$this->request->post['store_id']
            : (int)$this->config->get('config_store_id');
        return UspsApiContext::buildHashKey(
            'usps.payment_token.',
            [
                $storeId,
                UspsApiContext::getEnvironmentCode($apiEnvironment),
                $clientId,
                $crid,
                $mid,
                $manifestMid,
                $accountNumber,
            ]
        );
    }

    private function getTokenService()
    {
        return new UspsTokenService($this->cache, 20);
    }

    private function jsonResponse(array $payload)
    {
        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($payload));
    }
}
