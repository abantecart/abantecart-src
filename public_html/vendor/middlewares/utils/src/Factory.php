<?php
declare(strict_types = 1);

namespace Middlewares\Utils;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class to create instances of PSR-7 classes.
 */
abstract class Factory
{
    /** @var FactoryInterface */
    private static $factory;

    public static function getFactory(): FactoryInterface
    {
        if (!self::$factory) {
            static::setFactory(new FactoryDiscovery());
        }

        return self::$factory;
    }

    public static function setFactory(FactoryInterface $factory): void
    {
        self::$factory = $factory;
    }

    public static function getRequestFactory(): RequestFactoryInterface
    {
        return self::getFactory()->getRequestFactory();
    }

    /**
     * @param UriInterface|string $uri
     */
    public static function createRequest(string $method, $uri): RequestInterface
    {
        return self::getRequestFactory()->createRequest($method, $uri);
    }

    public static function getResponseFactory(): ResponseFactoryInterface
    {
        return self::getFactory()->getResponseFactory();
    }

    public static function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return self::getResponseFactory()->createResponse($code, $reasonPhrase);
    }

    public static function getServerRequestFactory(): ServerRequestFactoryInterface
    {
        return self::getFactory()->getServerRequestFactory();
    }

    /**
     * @param UriInterface|string $uri
     */
    public static function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return self::getServerRequestFactory()->createServerRequest($method, $uri, $serverParams);
    }

    public static function getStreamFactory(): StreamFactoryInterface
    {
        return self::getFactory()->getStreamFactory();
    }

    public static function createStream(string $content = ''): StreamInterface
    {
        return self::getStreamFactory()->createStream($content);
    }

    public static function getUploadedFileFactory(): UploadedFileFactoryInterface
    {
        return self::getFactory()->getUploadedFileFactory();
    }

    public static function createUploadedFile(
        StreamInterface $stream,
        ?int $size = null,
        int $error = \UPLOAD_ERR_OK,
        ?string $filename = null,
        ?string $mediaType = null
    ): UploadedFileInterface {
        return self::getUploadedFileFactory()->createUploadedFile($stream, $size, $error, $filename, $mediaType);
    }

    public static function getUriFactory(): UriFactoryInterface
    {
        return self::getFactory()->getUriFactory();
    }

    public static function createUri(string $uri = ''): UriInterface
    {
        return self::getUriFactory()->createUri($uri);
    }
}
