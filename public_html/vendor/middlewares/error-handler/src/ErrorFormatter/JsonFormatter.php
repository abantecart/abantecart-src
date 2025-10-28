<?php
declare(strict_types = 1);

namespace Middlewares\ErrorFormatter;

use Throwable;

class JsonFormatter extends AbstractFormatter
{
    protected $contentTypes = [
        'application/json',
    ];

    protected function format(Throwable $error, string $contentType): string
    {
        $json = [
            'type' => get_class($error),
            'code' => $error->getCode(),
            'message' => $error->getMessage(),
        ];

        return (string) json_encode($json);
    }
}
