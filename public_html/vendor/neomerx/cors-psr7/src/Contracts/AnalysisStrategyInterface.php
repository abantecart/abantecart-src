<?php

declare(strict_types=1);

namespace Neomerx\Cors\Contracts;

/*
 * Copyright 2015-2020 info@neomerx.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerAwareInterface;

interface AnalysisStrategyInterface extends LoggerAwareInterface
{
    /**
     * Get server Origin URL scheme.
     */
    public function getServerOriginScheme(): string;

    /**
     * Get server Origin URL host.
     */
    public function getServerOriginHost(): string;

    /**
     * Get server Origin URL port.
     */
    public function getServerOriginPort(): ?int;

    /**
     * If pre-flight request result should be cached by user agent.
     */
    public function isPreFlightCanBeCached(RequestInterface $request): bool;

    /**
     * Get pre-flight cache max period in seconds.
     */
    public function getPreFlightCacheMaxAge(RequestInterface $request): int;

    /**
     * If allowed methods should be added to pre-flight response when 'simple' method is requested (see #6.2.9 CORS).
     *
     * @see http://www.w3.org/TR/cors/#resource-preflight-requests
     */
    public function isForceAddAllowedMethodsToPreFlightResponse(): bool;

    /**
     * If allowed headers should be added when request headers are 'simple' and
     * non of them is 'Content-Type' (see #6.2.10 CORS).
     *
     * @see http://www.w3.org/TR/cors/#resource-preflight-requests
     */
    public function isForceAddAllowedHeadersToPreFlightResponse(): bool;

    /**
     * If access with credentials is supported by the resource.
     */
    public function isRequestCredentialsSupported(RequestInterface $request): bool;

    /**
     * If request origin is allowed.
     */
    public function isRequestOriginAllowed(string $requestOrigin): bool;

    /**
     * If method is supported for actual request (case-sensitive compare).
     */
    public function isRequestMethodSupported(string $method): bool;

    /**
     * If requests headers are allowed (case-insensitive compare).
     *
     * @param string[] $lcHeaders lower-cased headers
     */
    public function isRequestAllHeadersSupported(array $lcHeaders): bool;

    /**
     * Get methods allowed for request. May return originally requested method ($requestMethod) or
     * comma separated method list (#6.2.9 CORS).
     *
     * @see http://www.w3.org/TR/cors/#resource-preflight-requests
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Methods
     */
    public function getRequestAllowedMethods(RequestInterface $request): string;

    /**
     * Get headers allowed for request (comma-separated list).
     *
     * @see http://www.w3.org/TR/cors/#resource-preflight-requests
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS#Access-Control-Allow-Headers
     */
    public function getRequestAllowedHeaders(RequestInterface $request): string;

    /**
     * Get headers other than the simple ones that might be exposed to user agent.
     */
    public function getResponseExposedHeaders(RequestInterface $request): string;

    /**
     * If request 'Host' header should be checked against server's origin.
     * Check of Host header is strongly encouraged by #6.3 CORS.
     * Header 'Host' must present for all requests rfc2616 14.23.
     */
    public function isCheckHost(): bool;
}
