<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2024 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class AImage
 */
class AImage
{
    /**
     * @var string
     */
    protected $file;
    /**
     * @var null|resource|GdImage
     */
    protected $image;
    /**
     * @var array
     */
    protected $info = [];

    /** @var Registry */
    protected $registry;

    /**
     * @param string $filename
     *
     * @throws AException
     */
    public function __construct($filename)
    {
        ini_set("gd.jpeg_ignore_warning", 1);
        if (!file_exists($filename)) {
            $error = new AWarning('Error: Cannot load image '.$filename.' . File does not exist.');
            $error->toLog();
            return false;
        }

        try {
            $info = getimagesize($filename);
            $this->file = $filename;
            $this->info = [
                'width'    => $info[0],
                'height'   => $info[1],
                'bits'     => $info['bits'],
                'mime'     => $info['mime'],
                'channels' => $info['channels'],
            ];
            $this->registry = Registry::getInstance();
            $this->image = $this->get_gd_resource($filename);
        } catch (AException $e) {
            $error = new AWarning('Error: Cannot load image '.$filename.'. '.$e->getMessage());
            $error->toLog();
            return false;
        }
        return true;
    }

    private function isGDResource($obj){
        return (is_resource($obj) || $obj instanceof GdImage);
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string $filename
     *
     * @return GdImage|resource
     * @throws AException
     */
    private function get_gd_resource($filename)
    {
        $mime = $this->info['mime'];

        //some images processing can run out of original PHP memory limit size 
        //Dynamic memory allocation based on K.Tamutis solution on the php manual
        $mem_estimate = round(
            ($this->info['width']
                * $this->info['height']
                * $this->info['bits']
                * $this->info['channels'] / 8
                + Pow(2, 16)
            ) * 1.7
        );
        if (function_exists('memory_get_usage')) {
            if (memory_get_usage() + $mem_estimate > (integer) ini_get('memory_limit') * pow(1024, 2)) {
                $new_mem = (integer) ini_get('memory_limit')
                    + ceil(
                        ((memory_get_usage()
                                + $mem_estimate) - (integer) ini_get('memory_limit') * pow(
                                1024,
                                2
                            )) / pow(1024, 2)
                    ).'M';
                //TODO. Validate if memory change was in fact changed or report an error
                ini_set('memory_limit', $new_mem);
            }
        }

        $result = false;
        if ($this->_is_memory_enough($this->info['width'], $this->info['height'])) {
            if ($mime == 'image/gif') {
                $result = imagecreatefromgif($filename);
            } elseif ($mime == 'image/png') {
                $result = imagecreatefrompng($filename);
            } elseif ($mime == 'image/jpeg') {
                $result = imagecreatefromjpeg($filename);
            } elseif ($mime == 'image/webp') {
                $result = imagecreatefromwebp($filename);
            } elseif ($mime == 'image/avif') {
                $result = imagecreatefromavif($filename);
            }
        }

        if(!$result){
            throw new AException(
                AC_ERR_LOAD,
                'Unable to create internal image from file '.$filename.'. Try to decrease original image size '
                .$this->info['width'].'x'.$this->info['height']
                .'px or reduce file size or increase memory limit for PHP.'
            );
        }
        return $result;
    }

    /**
     * @param string $filename
     * @param int $width
     * @param int $height
     * @param array $options
     *
     * @return bool
     * @throws AException
     */
    public function resizeAndSave($filename, $width, $height, $options = [])
    {
        if (!$filename) {
            return false;
        }
        $width = (int) $width;
        $height = (int) $height;
        $options = (array) $options;

        $quality = !isset($options['quality']) ? 90 : (int) $options['quality'];
        $nofill = !isset($options['nofill']) ? false : $options['nofill'];

        //if size will change - resize it and save with GD2, otherwise - just copy file
        if ($this->info['width'] != $width || $this->info['height'] != $height) {
            $result_size = $this->resize($width, $height, $nofill);
            if ($result_size) {
                $result = $this->save($filename, $quality);
            } else {
                $result = false;
            }
        } else {
            $result = copy($this->file, $filename);
        }

        return $result;
    }

    /**
     * @param string $filename - full file name
     * @param int $quality - some number in range from 1 till 100
     *
     * @return bool
     * @throws AException
     */
    public function save($filename, $quality = 90)
    {
        if (is_object($this->registry) && $this->registry->has('extensions')) {
            $result = $this->registry->get('extensions')->hk_save($this, $filename, $quality);
        } else {
            $result = $this->_save($filename, $quality);
        }
        return $result;
    }

    /**
     * @param string $filename - full file name
     * @param int $quality - some number in range from 1 till 100
     *
     * @return bool
     * @throws AException
     */
    public function _save($filename, $quality = 90)
    {
        if (!$filename || !$this->image) {
            return false;
        }

        $quality = (int) $quality;
        $quality = min($quality, 100);
        $quality = max($quality, 1);

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if ($this->_is_memory_enough($this->info['width'], $this->info['height'])) {
            if ($extension == 'jpeg' || $extension == 'jpg') {
                imagejpeg($this->image, $filename, $quality);
            } elseif ($extension == 'png') {
                //use maximum compression for PNG
                imagepng($this->image, $filename, 9, PNG_ALL_FILTERS);
            } elseif ($extension == 'gif') {
                imagegif($this->image, $filename);
            } elseif ($extension == 'webp') {
                imagewebp($this->image, $filename);
            } elseif ($extension == 'avif') {
                imageavif($this->image, $filename);
            }
            if (is_file($filename)) {
                $result = chmod($filename, 0777);
                if (!$result) {
                    $error_text = "AImage: cannot to change permissions for ".$filename;
                    $warning = new AWarning($error_text);
                    $warning->toLog();
                }
            }
            imagedestroy($this->image);
        } else {
            $warning = new AWarning('Image file '.$this->file.' cannot be saved as '.$filename.'. ');
            $warning->toLog()->toDebug();
            return false;
        }

        return true;
    }

    /**
     * @param int $width
     * @param int $height
     * @param bool|false $nofill - sign for background fill
     *
     * @return bool|null
     * @throws AException
     */
    public function resize($width = 0, $height = 0, $nofill = false)
    {
        if (!$this->image || !$this->info['width'] || !$this->info['height']) {
            return false;
        }
        if ($width == 0 && $height == 0) {
            return false;
        }

        $scale = min($width / $this->info['width'], $height / $this->info['height']);
        //if no need resize - return true
        if ($scale == 1 && $this->info['mime'] != 'image/png') {
            return true;
        }

        $new_width = (int) round($this->info['width'] * $scale, 0);
        $new_height = (int) round($this->info['height'] * $scale, 0);
        $xpos = (int) (($width - $new_width) / 2);
        $ypos = (int) (($height - $new_height) / 2);

        $image_old = $this->image;
        if ($this->_is_memory_enough($this->info['width'], $this->info['height'])) {
            $this->image = imagecreatetruecolor($width, $height);

            $fillColor = $this->registry->get('config')->get('config_image_resize_fill_color');
            $fillColor = $fillColor ?: '#ffffff';
            list($r, $g, $b) = sscanf($fillColor, "#%02x%02x%02x");
            if (isset($this->info['mime']) && in_array($this->info['mime'], ['image/png', 'image/webp','image/avif'])) {
                imagealphablending($this->image, false);
                imagesavealpha($this->image, true);
                $background = imagecolorallocatealpha($this->image, $r, $g, $b, 127);
                imagefill($this->image, 0, 0, $background);
            } else {
                if (!$nofill) { // if image no transparent
                    $background = imagecolorallocate($this->image, $r, $g, $b);
                    imagefilledrectangle($this->image, 0, 0, $width, $height, $background);
                }
            }

            if ($this->isGDResource($this->image)) {

                imagecopyresampled(
                    $this->image,
                    $image_old,
                    $xpos,
                    $ypos,
                    0,
                    0,
                    $new_width,
                    $new_height,
                    $this->info['width'],
                    $this->info['height']
                );
            }
            if ($this->isGDResource($image_old)) {
                imagedestroy($image_old);
            }
        } else {
            $message = 'Image file '.$this->file
                .' cannot be resized. Try to decrease original image size '
                .$this->info['width'].'x'.$this->info['height'].'px or reduce file size.';
            $res = new AResource('image');
            $resource_id = $res->getIdFromHexPath(str_replace(DIR_RESOURCE.'image/', '', $this->file));
            if ($resource_id) {
                $message .= ' View details please visit #admin#rt=tool/rl_manager&resource_id='.$resource_id;
            }
            $GLOBALS['error_descriptions']['img'] = 'Image Resize error '.$resource_id;
            $warning = new AWarning($message, 'img');
            $warning->code = 'img';
            $warning->toLog()->toDebug();
            return false;
        }

        $this->info['width'] = $width;
        $this->info['height'] = $height;

        return true;
    }

    /**
     * @param        $filename
     * @param string $position
     *
     * @return bool
     * @throws AException
     */
    public function watermark($filename, $position = 'bottomright')
    {
        if (!$this->isGDResource($this->image)) {
            return false;
        }
        $watermark = $this->get_gd_resource($filename);
        try {
            $watermark_width = imagesx($watermark);
            $watermark_height = imagesy($watermark);
            $watermark_pos_x = $watermark_pos_y = 0;

            switch ($position) {
                case 'topleft':
                    $watermark_pos_x = 0;
                    $watermark_pos_y = 0;
                    break;
                case 'topright':
                    $watermark_pos_x = $this->info['width'] - $watermark_width;
                    $watermark_pos_y = 0;
                    break;
                case 'bottomleft':
                    $watermark_pos_x = 0;
                    $watermark_pos_y = $this->info['height'] - $watermark_height;
                    break;
                case 'bottomright':
                    $watermark_pos_x = $this->info['width'] - $watermark_width;
                    $watermark_pos_y = $this->info['height'] - $watermark_height;
                    break;
            }
            imagecopy($this->image, $watermark, $watermark_pos_x, $watermark_pos_y, 0, 0, 120, 40);
            imagedestroy($watermark);
        } catch (Exception $e) {
            $warning = new AWarning('Cannot to apply watermark to the image file '.$this->file.'. '.$e->getMessage());
            $warning->toLog()->toDebug();
            return false;
        }
        return true;
    }

    /**
     * @param int $top_x
     * @param int $top_y
     * @param int $bottom_x
     * @param int $bottom_y
     *
     * @return bool
     * @throws AException
     */
    public function crop($top_x, $top_y, $bottom_x, $bottom_y)
    {
        if (!$this->isGDResource($this->image)) {
            return false;
        }
        if ($this->_is_memory_enough($bottom_x - $top_x, $bottom_y - $top_y)) {
            $image_old = $this->image;
            $this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

            imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->info['width'], $this->info['height']);
            imagedestroy($image_old);

            $this->info['width'] = $bottom_x - $top_x;
            $this->info['height'] = $bottom_y - $top_y;
        } else {
            $warning = new AWarning('Cannot to crop image file '.$this->file);
            $warning->toLog()->toDebug();
            return false;
        }
        return true;
    }

    /**
     * @param float $degree
     * @param string $color
     *
     * @return bool
     * @throws AException
     */
    public function rotate($degree, $color = 'FFFFFF')
    {
        if (!$this->isGDResource($this->image)) {
            return false;
        }
        try {
            $rgb = $this->html2rgb($color);
            $this->image = imagerotate(
                $this->image,
                $degree,
                imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2])
            );
            $this->info['width'] = imagesx($this->image);
            $this->info['height'] = imagesy($this->image);
        } catch (Exception $e) {
            $warning = new AWarning('Cannot to rotate image file '.$this->file.'. '.$e->getMessage());
            $warning->toLog()->toDebug();
            return false;
        }
        return true;
    }

    /**
     * @param int $filter
     *
     * @return bool
     * @throws AException
     */
    public function filter($filter)
    {
        if (!$this->isGDResource($this->image)) {
            return false;
        }
        try {
            imagefilter($this->image, $filter);
        } catch (Exception $e) {
            $warning = new AWarning('Cannot to apply filter to the image file '.$this->file.'. '.$e->getMessage());
            $warning->toLog()->toDebug();
            return false;
        }
        return true;
    }

    /**
     * @param string $text
     * @param int $x
     * @param int $y
     * @param int $size
     * @param string $color
     *
     * @return bool
     * @throws AException
     */
    public function text($text, $x = 0, $y = 0, $size = 5, $color = '000000')
    {
        if (!$this->isGDResource($this->image)) {
            return false;
        }
        $rgb = $this->html2rgb($color);
        try {
            imagestring(
                $this->image, $size, $x, $y, $text,
                imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2])
            );
        } catch (Exception $e) {
            $warning = new AWarning('Cannot to add text into image file '.$this->file.'. '.$e->getMessage());
            $warning->toLog()->toDebug();
            return false;
        }
        return true;
    }

    /**
     * @param string $filename
     * @param int $x
     * @param int $y
     * @param int $opacity
     *
     * @return bool
     * @throws AException
     */
    public function merge($filename, $x = 0, $y = 0, $opacity = 100)
    {
        $merge = $this->get_gd_resource($filename);
        if (!$this->isGDResource($this->image) || $merge) {
            return false;
        }

        try {
            $merge_width = imagesx($merge);
            $merge_height = imagesy($merge);
            imagecopymerge($this->image, $merge, $x, $y, 0, 0, $merge_width, $merge_height, $opacity);
        } catch (Exception $e) {
            $warning = new AWarning('Cannot to merge image files '.$this->file.' and '.$filename.'. '.$e->getMessage());
            $warning->toLog()->toDebug();
            return false;
        }
        return true;
    }

    /**
     * @param string $color
     *
     * @return array|bool
     */
    private function html2rgb($color)
    {
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        if (strlen($color) == 6) {
            list($r, $g, $b) = [$color[0].$color[1], $color[2].$color[3], $color[4].$color[5]];
        } elseif (strlen($color) == 3) {
            list($r, $g, $b) = [$color[0].$color[0], $color[1].$color[1], $color[2].$color[2]];
        } else {
            return false;
        }

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return [$r, $g, $b];
    }

    protected function _is_memory_enough($x, $y, $rgb = 4)
    {
        $memory_limit = trim(ini_get('memory_limit'));
        $last = strtolower($memory_limit[strlen($memory_limit) - 1]);
        switch ($last) {
            case 'g':
                $memory_limit = (int) $memory_limit * (1024 * 1024 * 1024);
                break;
            case 'm':
                $memory_limit = (int) $memory_limit * (1024 * 1024);
                break;
            case 'k':
                $memory_limit = (int) $memory_limit * 1024;
                break;
        }
        return ($x * $y * $rgb * 1.7 < $memory_limit - memory_get_usage());
    }

    public function __destruct()
    {
        if ($this->isGDResource($this->image)) {
            imagedestroy($this->image);
        } else {
            $this->image = null;
        }
    }
}