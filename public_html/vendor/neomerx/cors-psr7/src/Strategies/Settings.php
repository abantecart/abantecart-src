<?php

declare(strict_types=1);

namespace Neomerx\Cors\Strategies;

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

use Neomerx\Cors\Contracts\AnalysisStrategyInterface;
use Neomerx\Cors\Contracts\Constants\SimpleResponseHeaders;
use Neomerx\Cors\Log\LoggerAwareTrait;
use Psr\Http\Message\RequestInterface;

/**
 * Implements strategy as a simple set of setting identical for all resources and requests.
 */
class Settings implements AnalysisStrategyInterface
{
    use LoggerAwareTrait;

    /**
     * @var string[]
     */
    protected const SIMPLE_LC_RESPONSE_HEADERS = [
        SimpleResponseHeaders::LC_ACCEPT_LANGUAGE,
        SimpleResponseHeaders::LC_CACHE_CONTROL,
        SimpleResponseHeaders::LC_CONTENT_LANGUAGE,
        SimpleResponseHeaders::LC_CONTENT_TYPE,
        SimpleResponseHeaders::LC_EXPIRES,
        SimpleResponseHeaders::LC_LAST_MODIFIED,
        SimpleResponseHeaders::LC_PRAGMA,
    ];

    private string $serverOriginScheme;

    private string $serverOriginHost;

    private ?int $serverOriginPort;

    private bool $isPreFlightCanBeCached;

    private int $preFlightCacheMaxAge;

    private bool $isForceAddMethods;

    private bool $isForceAddHeaders;

    private bool $isUseCredentials;

    private bool $areAllOriginsAllowed;

    private array $allowedOrigins;

    private bool $areAllMethodsAllowed;

    private array $allowedLcMethods;

    private string $allowedMethodsList;

    private bool $areAllHeadersAllowed;

    private array $allowedLcHeaders;

    private string $allowedHeadersList;

    private string $exposedHeadersList;

    private bool $isCheckHost;

    /**
     * Sort of default constructor, though made separate to be used optionally when no cached data available.
     */
    public function init(string $scheme, string $host, int $port): self
    {
        return $this
            ->setServerOrigin($scheme, $host, $port)
            ->setPreFlightCacheMaxAge(0)
            ->setCredentialsNotSupported()
            ->setAllowedOrigins([]) // see enableAllOriginsAllowed() as an alternative
            ->setAllowedMethods([]) // see enableAllMethodsAllowed() as an alternative
            ->setAllowedHeaders([]) // see enableAllHeadersAllowed() as an alternative
            ->setExposedHeaders([])
            ->disableAddAllowedMethodsToPreFlightResponse()
            ->disableAddAllowedHeadersToPreFlightResponse()
            ->disableCheckHost();
    }

    /**
     * Get internal data state. Can be used for data caching.
     */
    public function getData(): array
    {
        return [
            $this->serverOriginScheme,
            $this->serverOriginHost,
            $this->serverOriginPort,
            $this->isPreFlightCanBeCached,
            $this->preFlightCacheMaxAge,
            $this->isForceAddMethods,
            $this->isForceAddHeaders,
            $this->isUseCredentials,
            $this->areAllOriginsAllowed,
            $this->allowedOrigins,
            $this->areAllMethodsAllowed,
            $this->allowedLcMethods,
            $this->allowedMethodsList,
            $this->areAllHeadersAllowed,
            $this->allowedLcHeaders,
            $this->allowedHeadersList,
            $this->exposedHeadersList,
            $this->isCheckHost,
        ];
    }

    /**
     * Set internal data state. Can be used for setting cached data.
     */
    public function setData(array $data): self
    {
        [
            $this->serverOriginScheme,
            $this->serverOriginHost,
            $this->serverOriginPort,
            $this->isPreFlightCanBeCached,
            $this->preFlightCacheMaxAge,
            $this->isForceAddMethods,
            $this->isForceAddHeaders,
            $this->isUseCredentials,
            $this->areAllOriginsAllowed,
            $this->allowedOrigins,
            $this->areAllMethodsAllowed,
            $this->allowedLcMethods,
            $this->allowedMethodsList,
            $this->areAllHeadersAllowed,
            $this->allowedLcHeaders,
            $this->allowedHeadersList,
            $this->exposedHeadersList,
            $this->isCheckHost,
        ] = $data;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerOriginScheme(): string
    {
        return $this->serverOriginScheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerOriginHost(): string
    {
        return $this->serverOriginHost;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerOriginPort(): ?int
    {
        return $this->serverOriginPort;
    }

    /**
     * Set server Origin URL.
     */
    public function setServerOrigin(string $scheme, string $host, int $port): self
    {
        \assert(false === empty($scheme));
        \assert(false === empty($host));
        \assert(0 < $port && $port <= 0xFFFF);

        $this->serverOriginScheme = $scheme;
        $this->serverOriginHost   = $host;

        if (0 === \strcasecmp($scheme, 'http') && 80 === $port) {
            $port = null;
        } elseif (0 === \strcasecmp($scheme, 'https') && 443 === $port) {
            $port = null;
        }
        $this->serverOriginPort = $port;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isPreFlightCanBeCached(RequestInterface $request): bool
    {
        return $this->isPreFlightCanBeCached;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreFlightCacheMaxAge(RequestInterface $request): int
    {
        return $this->preFlightCacheMaxAge;
    }

    /**
     * Set pre-flight cache max period in seconds.
     */
    public function setPreFlightCacheMaxAge(int $cacheMaxAge): self
    {
        \assert($cacheMaxAge >= 0);

        $this->preFlightCacheMaxAge   = $cacheMaxAge;
        $this->isPreFlightCanBeCached = $cacheMaxAge > 0;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isForceAddAllowedMethodsToPreFlightResponse(): bool
    {
        return $this->isForceAddMethods;
    }

    /**
     * If allowed headers should be added when request headers are 'simple' and
     * non of them is 'Content-Type' (see #6.2.10 CORS).
     *
     * @see http://www.w3.org/TR/cors/#resource-preflight-requests
     */
    public function enableAddAllowedMethodsToPreFlightResponse(): self
    {
        $this->isForceAddMethods = true;

        return $this;
    }

    /**
     * If allowed headers should be added when request headers are 'simple' and
     * non of them is 'Content-Type' (see #6.2.10 CORS).
     *
     * @see http://www.w3.org/TR/cors/#resource-preflight-requests
     */
    public function disableAddAllowedMethodsToPreFlightResponse(): self
    {
        $this->isForceAddMethods = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isForceAddAllowedHeadersToPreFlightResponse(): bool
    {
        return $this->isForceAddHeaders;
    }

    /**
     * If allowed headers should be added when request headers are 'simple' and
     * non of them is 'Content-Type' (see #6.2.10 CORS).
     *
     * @see http://www.w3.org/TR/cors/#resource-preflight-requests
     */
    public function enableAddAllowedHeadersToPreFlightResponse(): self
    {
        $this->isForceAddHeaders = true;

        return $this;
    }

    /**
     * If allowed headers should be added when request headers are 'simple' and
     * non of them is 'Content-Type' (see #6.2.10 CORS).
     *
     * @see http://www.w3.org/TR/cors/#resource-preflight-requests
     */
    public function disableAddAllowedHeadersToPreFlightResponse(): self
    {
        $this->isForceAddHeaders = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequestCredentialsSupported(RequestInterface $request): bool
    {
        return $this->isUseCredentials;
    }

    /**
     * If access with credentials is supported by the resource.
     */
    public function setCredentialsSupported(): self
    {
        $this->isUseCredentials = true;

        return $this;
    }

    /**
     * If access with credentials is supported by the resource.
     */
    public function setCredentialsNotSupported(): self
    {
        $this->isUseCredentials = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequestOriginAllowed(string $requestOrigin): bool
    {
        return
            true === $this->areAllOriginsAllowed
            || true === isset($this->allowedOrigins[\strtolower($requestOrigin)]);
    }

    /**
     * Enable all origins allowed.
     */
    public function enableAllOriginsAllowed(): self
    {
        $this->areAllOriginsAllowed = true;

        return $this;
    }

    /**
     * Set allowed origins.
     */
    public function setAllowedOrigins(array $origins): self
    {
        $this->allowedOrigins = [];

        foreach ($origins as $origin) {
            $this->allowedOrigins[\strtolower($origin)] = true;
        }

        $this->areAllOriginsAllowed = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequestMethodSupported(string $method): bool
    {
        return true === $this->areAllMethodsAllowed || true === isset($this->allowedLcMethods[\strtolower($method)]);
    }

    /**
     * Enable all methods allowed.
     */
    public function enableAllMethodsAllowed(): self
    {
        $this->areAllMethodsAllowed = true;

        return $this;
    }

    /**
     * Set allowed methods.
     *
     * Security Note: you have to remember CORS is not access control system and you should not expect all
     * cross-origin requests will have pre-flights. For so-called 'simple' methods with so-called 'simple'
     * headers request will be made without pre-flight. Thus, you can not restrict such requests with CORS
     * and should use other means.
     * For example method 'GET' without any headers or with only 'simple' headers will not have pre-flight
     * request so disabling it will not restrict access to resource(s).
     * You can read more on 'simple' methods at http://www.w3.org/TR/cors/#simple-method
     */
    public function setAllowedMethods(array $methods): self
    {
        $this->allowedMethodsList = \implode(', ', $methods);

        $this->allowedLcMethods = [];
        foreach ($methods as $method) {
            $this->allowedLcMethods[\strtolower($method)] = true;
        }

        $this->areAllMethodsAllowed = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequestAllHeadersSupported(array $lcHeaders): bool
    {
        return true === $this->areAllHeadersAllowed
            || \count(\array_intersect($this->allowedLcHeaders, $lcHeaders)) === \count($lcHeaders);
    }

    /**
     * Enable all headers allowed.
     */
    public function enableAllHeadersAllowed(): self
    {
        $this->areAllHeadersAllowed = true;

        return $this;
    }

    /**
     * Set allowed headers.
     *
     * Security Note: you have to remember CORS is not access control system, and you should not expect all
     * cross-origin requests will have pre-flights. For so-called 'simple' methods with so-called 'simple'
     * headers request will be made without pre-flight. Thus, you can not restrict such requests with CORS
     * and should use other means.
     * For example method 'GET' without any headers or with only 'simple' headers will not have pre-flight
     * request so disabling it will not restrict access to resource(s).
     * You can read more on 'simple' headers at http://www.w3.org/TR/cors/#simple-header
     */
    public function setAllowedHeaders(array $headers): self
    {
        $this->allowedHeadersList = \implode(', ', $headers);

        $this->allowedLcHeaders = [];
        foreach ($headers as $header) {
            $this->allowedLcHeaders[] = \strtolower($header);
        }

        $this->areAllHeadersAllowed = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestAllowedMethods(RequestInterface $request): string
    {
        return $this->allowedMethodsList;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestAllowedHeaders(RequestInterface $request): string
    {
        return $this->allowedHeadersList;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseExposedHeaders(RequestInterface $request): string
    {
        return $this->exposedHeadersList;
    }

    /**
     * Set headers other than the simple ones that might be exposed to user agent.
     */
    public function setExposedHeaders(array $headers): self
    {
        // Optional: from #7.1.1 'simple response headers' will be available in any case so it does not
        // make sense to include those headers to exposed.
        $filtered = [];
        foreach ($headers as $header) {
            if (false === \in_array(\strtolower($header), static::SIMPLE_LC_RESPONSE_HEADERS)) {
                $filtered[] = $header;
            }
        }

        $this->exposedHeadersList = \implode(', ', $filtered);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isCheckHost(): bool
    {
        return $this->isCheckHost;
    }

    /**
     * If request 'Host' header should be checked against server's origin.
     * Check of Host header is strongly encouraged by #6.3 CORS.
     * Header 'Host' must present for all requests rfc2616 14.23.
     */
    public function enableCheckHost(): self
    {
        $this->isCheckHost = true;

        return $this;
    }

    /**
     * If request 'Host' header should be checked against server's origin.
     * Check of Host header is strongly encouraged by #6.3 CORS.
     * Header 'Host' must present for all requests rfc2616 14.23.
     */
    public function disableCheckHost(): self
    {
        $this->isCheckHost = false;

        return $this;
    }
}
