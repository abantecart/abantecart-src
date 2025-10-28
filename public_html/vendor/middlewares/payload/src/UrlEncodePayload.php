<?php
declare(strict_types = 1);

namespace Middlewares;

use DomainException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;

class UrlEncodePayload extends Payload implements MiddlewareInterface
{
    /**
     * @var string[]
     */
    protected $contentType = ['application/x-www-form-urlencoded'];

    /**
     * {@inheritdoc}
     *
     * @return array<mixed>
     */
    protected function parse(StreamInterface $stream): array
    {
        $string = trim((string) $stream);
        parse_str($string, $data);

        if (strlen($string) && empty($data)) {
            throw new DomainException('Invalid url encoded string');
        }

        return $data ?: [];
    }
}
