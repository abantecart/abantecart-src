<?php

declare(strict_types=1);

namespace Neomerx\Cors\Contracts\Constants;

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
interface CorsResponseHeaders
{
    /**
     * CORS Header.
     *
     * @see http://www.w3.org/TR/cors/#access-control-allow-origin-response-header
     */
    public const ALLOW_ORIGIN = 'Access-Control-Allow-Origin';

    /**
     * CORS Header.
     *
     * @see http://www.w3.org/TR/cors/#access-control-allow-credentials-response-header
     */
    public const ALLOW_CREDENTIALS = 'Access-Control-Allow-Credentials';

    /**
     * CORS Header.
     *
     * @see http://www.w3.org/TR/cors/#access-control-expose-headers-response-header
     */
    public const EXPOSE_HEADERS = 'Access-Control-Expose-Headers';

    /**
     * CORS Header.
     *
     * @see http://www.w3.org/TR/cors/#access-control-max-age-response-header
     */
    public const MAX_AGE = 'Access-Control-Max-Age';

    /**
     * CORS Header.
     *
     * @see http://www.w3.org/TR/cors/#access-control-allow-methods-response-header
     */
    public const ALLOW_METHODS = 'Access-Control-Allow-Methods';

    /**
     * CORS Header.
     *
     * @see http://www.w3.org/TR/cors/#access-control-allow-headers-response-header
     */
    public const ALLOW_HEADERS = 'Access-Control-Allow-Headers';

    /**
     * CORS Header.
     *
     * @see http://www.w3.org/TR/cors/#resource-implementation
     */
    public const VARY = 'Vary';

    /**
     * 'All' value for 'Access-Control-Allow-Origin' header.
     *
     * @see http://www.w3.org/TR/cors/#access-control-allow-origin-response-header
     */
    public const VALUE_ALLOW_ORIGIN_ALL = '*';

    /**
     * 'null' value for 'Access-Control-Allow-Origin' header.
     *
     * @see http://www.w3.org/TR/cors/#access-control-allow-origin-response-header
     */
    public const VALUE_ALLOW_ORIGIN_NULL = 'null';

    /**
     * 'null' value for 'Access-Control-Allow-Origin' header.
     *
     * @see http://www.w3.org/TR/cors/#access-control-allow-credentials-response-header
     */
    public const VALUE_ALLOW_CREDENTIALS_TRUE = 'true';
}
