<?php
declare(strict_types = 1);

namespace Middlewares\ErrorFormatter;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

interface FormatterInterface
{
    /**
     * Check whether the error can be handled by this formatter
     */
    public function isValid(Throwable $error, ServerRequestInterface $request): bool;

    /**
     * Create a response with this error
     */
    public function handle(Throwable $error, ServerRequestInterface $request): ResponseInterface;
}
