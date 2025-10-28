<?php
declare(strict_types = 1);

namespace Middlewares\ErrorFormatter;

use Throwable;

class ImageFormatter extends AbstractFormatter
{
    protected $contentTypes = [
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    protected function format(Throwable $error, string $contentType): string
    {
        ob_start();
        $image = $this->createImage($error);

        switch ($contentType) {
            case 'image/gif':
                imagegif($image);
                break;
            case 'image/jpeg':
                imagejpeg($image);
                break;
            case 'image/png':
                imagepng($image);
                break;
            case 'image/webp':
                imagewebp($image);
                break;
        }

        return (string) ob_get_clean();
    }

    /**
     * Create an image resource from an error
     *
     * @return resource
     */
    private function createImage(Throwable $error)
    {
        $type = get_class($error);
        $code = $error->getCode();
        $message = $error->getMessage();

        $size = 200;
        $image = imagecreatetruecolor($size, $size);
        $textColor = imagecolorallocate($image, 255, 255, 255);

        /* @phpstan-ignore-next-line */
        imagestring($image, 5, 10, 10, "$type $code", $textColor);

        /* @phpstan-ignore-next-line */
        foreach (str_split($message, intval($size / 10)) as $line => $text) {
            /* @phpstan-ignore-next-line */
            imagestring($image, 5, 10, ($line * 18) + 28, $text, $textColor);
        }

        return $image;
    }
}
