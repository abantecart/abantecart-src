<?php
/**
 * $Id$
 *
 * AbanteCart, Ideal OpenSource Ecommerce Solution
 * http://www.AbanteCart.com
 *
 * Copyright © 2011-2026 Belavier Commerce LLC
 *
 * This source file is subject to Open Software License (OSL 3.0)
 * License details are bundled with this package in the file LICENSE.txt.
 * It is also available at this URL:
 * <http://www.opensource.org/licenses/OSL-3.0>
 *
 * UPGRADE NOTE:
 *   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *   versions in the future. If you wish to customize AbanteCart for your
 *   needs, please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}
require_once(DIR_EXT . 'usps' . DS . 'core' . DS . 'usps_error_parser.php');

use GuzzleHttp\Client;
use USPS\OAuthClientCredentials\Api\ResourcesApi as OAuthResourcesApi;
use USPS\OAuthClientCredentials\Configuration as OAuthConfiguration;
use USPS\OAuthClientCredentials\Model\ClientCredentials;

class UspsTokenService
{
    /** @var mixed */
    private $cache;
    /** @var int */
    private $timeout;
    /** @var UspsErrorParser|null */
    private $errorParser;

    public function __construct($cache, $timeout = 20)
    {
        $this->cache = $cache;
        $this->timeout = (int)$timeout;
        $this->errorParser = null;
    }

    public function getOauthToken($baseUrl, $clientId, $clientSecret, $cacheKey)
    {
        $cached = $this->cache->pull($cacheKey);
        if (is_array($cached) && !empty($cached['access_token']) && !empty($cached['expires_at'])) {
            if ((int)$cached['expires_at'] > (time() + 120)) {
                return ['token' => (string)$cached['access_token'], 'from_cache' => true];
            }
        }

        try {
            $config = OAuthConfiguration::getDefaultConfiguration()
                ->setHost($baseUrl . '/oauth2/v3');
            $api = new OAuthResourcesApi(
                new Client(['timeout' => $this->timeout]),
                $config
            );
            $request = new ClientCredentials([
                'grant_type'    => 'client_credentials',
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
            ]);
            $response = $api->postToken($request);
        } catch (\Throwable $e) {
            throw new \RuntimeException($this->extractErrorMessage($e), 0, $e);
        }

        $token = (string)$response->getAccessToken();
        if ($token === '') {
            throw new \RuntimeException('USPS OAuth token was not returned.');
        }

        $expiresIn = (int)$response->getExpiresIn();
        if ($expiresIn <= 0) {
            $expiresIn = 8 * 3600;
        }
        $this->cache->push(
            $cacheKey,
            [
                'access_token' => $token,
                'expires_at'   => time() + $expiresIn,
            ]
        );

        return ['token' => $token, 'from_cache' => false];
    }

    public function getPaymentAuthorizationToken(
        $baseUrl,
        $oauthToken,
        $crid,
        $mid,
        $manifestMid,
        $accountNumber,
        $cacheKey
    ) {
        $cached = $this->cache->pull($cacheKey);
        if (is_array($cached) && !empty($cached['payment_token']) && !empty($cached['expires_at'])) {
            if ((int)$cached['expires_at'] > (time() + 120)) {
                return ['token' => (string)$cached['payment_token'], 'from_cache' => true];
            }
        }

        $payload = $this->buildPaymentAuthorizationPayload(
            $crid,
            $mid,
            $manifestMid,
            $accountNumber
        );

        try {
            $response = (new Client(['timeout' => $this->timeout]))->post(
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
        } catch (\Throwable $e) {
            throw new \RuntimeException($this->extractErrorMessage($e), 0, $e);
        }

        $json = json_decode((string)$response->getBody(), true);
        $token = is_array($json) ? (string)($json['paymentAuthorizationToken'] ?? '') : '';
        if ($token === '') {
            throw new \RuntimeException('USPS Payment Authorization Token was not returned by USPS API.');
        }

        $this->cache->push(
            $cacheKey,
            [
                'payment_token' => $token,
                'expires_at'    => time() + (8 * 3600),
            ]
        );

        return ['token' => $token, 'from_cache' => false];
    }

    private function buildPaymentAuthorizationPayload($crid, $mid, $manifestMid, $accountNumber)
    {
        return [
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
    }

    private function extractErrorMessage(\Throwable $e)
    {
        return $this->getErrorParser()->parseThrowable($e);
    }

    private function getErrorParser()
    {
        if ($this->errorParser === null) {
            $this->errorParser = new UspsErrorParser();
        }
        return $this->errorParser;
    }
}
