<?php

declare(strict_types=1);

namespace Neomerx\Cors\Contracts;

/**
 * Copyright 2015-2020 info@neomerx.com.
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
interface AnalysisResultInterface
{
    /** Request is out of CORS specification */
    public const TYPE_REQUEST_OUT_OF_CORS_SCOPE = 0;

    /** Request is pre-flight */
    public const TYPE_PRE_FLIGHT_REQUEST = 1;

    /** Actual request */
    public const TYPE_ACTUAL_REQUEST = 2;

    /** Request origin is not allowed */
    public const ERR_ORIGIN_NOT_ALLOWED = 3;

    /** Request method is not supported */
    public const ERR_METHOD_NOT_SUPPORTED = 4;

    /** Request headers are not supported */
    public const ERR_HEADERS_NOT_SUPPORTED = 5;

    /** No Host header in request */
    public const ERR_NO_HOST_HEADER = 6;

    /**
     * Get request type.
     */
    public function getRequestType(): int;

    /**
     * Get CORS headers to be added to response.
     */
    public function getResponseHeaders(): array;
}
