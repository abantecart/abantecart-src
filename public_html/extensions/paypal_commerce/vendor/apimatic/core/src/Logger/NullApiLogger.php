<?php

namespace Core\Logger;

use CoreInterfaces\Core\Logger\ApiLoggerInterface;
use CoreInterfaces\Core\Request\RequestInterface;
use CoreInterfaces\Core\Response\ResponseInterface;

class NullApiLogger implements ApiLoggerInterface
{
    /**
     * @inheritDoc
     */
    public function logRequest(RequestInterface $request): void
    {
        // noop
    }

    /**
     * @inheritDoc
     */
    public function logResponse(ResponseInterface $response): void
    {
        // noop
    }
}
