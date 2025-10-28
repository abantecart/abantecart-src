<?php
declare(strict_types = 1);

namespace Middlewares\ErrorFormatter;

use Throwable;

class XmlFormatter extends AbstractFormatter
{
    protected $contentTypes = [
        'text/xml', 'application/xml', 'application/x-xml',
    ];

    protected function format(Throwable $error, string $contentType): string
    {
        $type = get_class($error);
        $code = $error->getCode();
        $message = $error->getMessage();

        return <<<XML
<?xml version="1.0" encoding="utf-8"?>
<error>
    <type>$type</type>
    <code>$code</code>
    <message>$message</message>
</error>
XML;
    }
}
