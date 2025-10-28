<?php
declare(strict_types = 1);

namespace Middlewares;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use SimpleXMLElement;

class XmlPayload extends Payload implements MiddlewareInterface
{
    /**
     * @var string[]
     */
    protected $contentType = ['text/xml', 'application/xml', 'application/x-xml'];

    /**
     * {@inheritdoc}
     */
    protected function parse(StreamInterface $stream): ?SimpleXMLElement
    {
        $string = trim((string) $stream);

        if ($string === '') {
            return null;
        }

        return new SimpleXMLElement($string);
    }
}
