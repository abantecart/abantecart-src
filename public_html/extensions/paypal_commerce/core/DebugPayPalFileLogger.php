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

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * PSR-3 logger for paypal/paypal-server-sdk.
 *
 * Main goal: write debug messages into a file (one JSON line per record).
 * Safe: masks Authorization-like data.
 *
 * Usage (пример):
 *   $logger = new DebugPayPalFileLogger(DIR_LOGS . '/paypal-debug.log', true);
 *   // then pass it into PaypalServerSdkClientBuilder logging configuration (see paypal_commerce_modules.php)
 */
class DebugPayPalFileLogger implements LoggerInterface
{
    use LoggerTrait;

    private bool $enabled;
    private string $logFile;

    public function __construct(?string $logFile = null, bool $enabled = false)
    {
        $this->enabled = $enabled;

        $fallback = defined('DIR_LOGS')
            ? rtrim((string)DIR_LOGS, '/\\') . '/paypal-debug.log'
            : sys_get_temp_dir() . '/paypal-debug.log';

        $this->logFile = $logFile ?: $fallback;
    }

    /**
     * @param mixed  $level
     * @param string|\Stringable $message
     * @param array<string, mixed> $context
     */
    public function log($level, $message, array $context = []): void
    {
        if (!$this->enabled) {
            return;
        }

        $record = [
            'ts'      => date('c'),
            'level'   => (string)$level,
            'message' => (string)$message,
            'context' => $this->maskSensitive($this->normalize($context)),
        ];

        $this->appendLine($record);
    }

    private function appendLine(array $data): void
    {
        // best-effort: do not break payment flow because of logging issues
        @file_put_contents(
            $this->logFile,
            json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
            FILE_APPEND
        );
    }

    /**
     * Normalize any values to plain arrays/scalars where possible.
     *
     * @param mixed $value
     * @return mixed
     */
    private function normalize($value)
    {
        if ($value === null || is_scalar($value)) {
            return $value;
        }

        // Convert objects/resources/arrays into a JSON-safe array representation.
        return json_decode(json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), true);
    }

    /**
     * Recursively mask sensitive keys/values (headers, tokens, secrets).
     */
    private function maskSensitive($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        foreach ($value as $k => $v) {
            if (is_string($k)) {
                $key = strtolower($k);

                // Typical sensitive fields for SDK logs
                if (
                    str_contains($key, 'authorization')
                    || str_contains($key, 'client_secret')
                    || str_contains($key, 'secret')
                    || str_contains($key, 'password')
                    || str_contains($key, 'access_token')
                    || str_contains($key, 'refresh_token')
                    || str_contains($key, 'token')
                ) {
                    $value[$k] = '[REDACTED]';
                    continue;
                }
            }

            $value[$k] = $this->maskSensitive($v);
        }

        return $value;
    }
}