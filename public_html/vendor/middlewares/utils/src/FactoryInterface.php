<?php
declare(strict_types = 1);

namespace Middlewares\Utils;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

interface FactoryInterface
{
    public function getRequestFactory(): RequestFactoryInterface;

    public function getResponseFactory(): ResponseFactoryInterface;

    public function getServerRequestFactory(): ServerRequestFactoryInterface;

    public function getStreamFactory(): StreamFactoryInterface;

    public function getUploadedFileFactory(): UploadedFileFactoryInterface;

    public function getUriFactory(): UriFactoryInterface;
}
