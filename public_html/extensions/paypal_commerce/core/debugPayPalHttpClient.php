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
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalHttp\HttpRequest;
use PayPalHttp\HttpResponse;

class DebugPayPalHttpClient extends PayPalHttpClient
{
    private bool $debug;
    private string $logFile;

    public function __construct($environment, bool $debug = false, string $logFile = null)
    {
        parent::__construct($environment);
        $this->debug   = $debug;
        $this->logFile = DIR_LOGS . '/paypal-debug.log';
    }

    public function execute(HttpRequest $httpRequest): HttpResponse
    {
        if ($this->debug) {
            $this->log([
                'type'    => 'request',
                'method'  => $httpRequest->verb,
                'url'     => rtrim($this->environment->baseUrl(), '/') . $httpRequest->path,
                'headers' => $this->maskHeaders($httpRequest->headers ?? []),
                'body'    => $this->normalize($httpRequest->body ?? null),
            ]);
        }

        $response = parent::execute($httpRequest);

        if ($this->debug) {
            $this->log([
                'type'     => 'response',
                'status'   => $response->statusCode,
                'debug_id' => $response->headers['PayPal-Debug-Id']
                    ?? $response->headers['paypal-debug-id']
                        ?? null,
                'headers'  => $this->maskHeaders($response->headers ?? []),
                'body'     => $this->normalize($response->result ?? null),
            ]);
        }

        return $response;
    }

    private function log(array $data): void
    {
        $data['ts'] = date('c');
        file_put_contents(
            $this->logFile,
            json_encode($data, JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
    }

    private function maskHeaders(array $headers): array
    {
        foreach ($headers as $k => &$v) {
            if (stripos($k, 'authorization') !== false) {
                $v = '[REDACTED]';
            }
        }
        return $headers;
    }

    private function normalize($value)
    {
        if ($value === null || is_string($value)) {
            return $value;
        }
        return json_decode(json_encode($value, JSON_UNESCAPED_SLASHES), true);
    }
}