<?php
/*
 *   Shared USPS error parser for SDK and Guzzle exceptions.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class UspsErrorParser
{
    public function parseThrowable(\Throwable $e)
    {
        $defaultMessage = trim((string)$e->getMessage());
        $statusCode = (int)$e->getCode();
        $responseBody = $this->extractResponseBodyFromException($e, $statusCode);
        $message = $this->extractMessageFromResponseBody($responseBody, $defaultMessage);
        return $this->formatHttpError($statusCode, $message);
    }

    private function extractResponseBodyFromException(\Throwable $e, &$statusCode)
    {
        $responseBody = $this->extractSdkResponseBody($e);
        if ($responseBody !== '') {
            return $responseBody;
        }
        return $this->extractGuzzleResponseBody($e, $statusCode);
    }

    private function extractSdkResponseBody(\Throwable $e)
    {
        if (!method_exists($e, 'getResponseBody')) {
            return '';
        }
        return $this->toStringBody($e->getResponseBody());
    }

    private function extractGuzzleResponseBody(\Throwable $e, &$statusCode)
    {
        if (!method_exists($e, 'getResponse')) {
            return '';
        }
        $response = $e->getResponse();
        if (!$response) {
            return '';
        }
        $statusCode = (int)$response->getStatusCode();
        return $this->toStringBody($response->getBody());
    }

    private function toStringBody($body)
    {
        if (is_string($body)) {
            return $body;
        }
        if (is_object($body) && method_exists($body, '__toString')) {
            return (string)$body;
        }
        return '';
    }

    private function extractMessageFromResponseBody($responseBody, $fallbackMessage)
    {
        if ($responseBody === '') {
            return $fallbackMessage;
        }
        $json = json_decode($responseBody, true);
        if (!is_array($json)) {
            return (string)$responseBody;
        }
        $message = $this->extractMessageFromErrorJson($json);
        return $message !== '' ? $message : $fallbackMessage;
    }

    private function extractMessageFromErrorJson(array $json)
    {
        $errorText = $this->extractErrorFieldMessage($json['error'] ?? []);
        if ($errorText !== '') {
            return $errorText;
        }
        if (isset($json['message']) && is_scalar($json['message'])) {
            return trim((string)$json['message']);
        }
        if (isset($json['error_description']) && is_scalar($json['error_description'])) {
            return trim((string)$json['error_description']);
        }
        return '';
    }

    private function extractErrorFieldMessage($errorField)
    {
        if (is_scalar($errorField)) {
            return trim((string)$errorField);
        }
        if (!is_array($errorField)) {
            return '';
        }

        $message = trim((string)($errorField['message'] ?? $errorField['description'] ?? ''));
        if ($message !== '') {
            return $message;
        }
        if (!isset($errorField['details']) || !is_array($errorField['details'])) {
            return '';
        }
        return $this->extractDetailsMessage($errorField['details']);
    }

    private function extractDetailsMessage(array $details)
    {
        $parts = [];
        foreach ($details as $detail) {
            if (is_array($detail)) {
                $parts[] = (string)($detail['message'] ?? $detail['description'] ?? '');
                continue;
            }
            if (is_scalar($detail)) {
                $parts[] = (string)$detail;
            }
        }
        return trim(implode(' ', array_filter($parts)));
    }

    private function formatHttpError($statusCode, $message)
    {
        if ((int)$statusCode > 0) {
            return 'HTTP ' . (int)$statusCode . ($message ? ': ' . $message : '');
        }
        return $message ?: 'Unknown USPS API error.';
    }
}
