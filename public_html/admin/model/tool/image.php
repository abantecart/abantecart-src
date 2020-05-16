<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelToolImage extends Model
{
    /**
     * @param string $filename
     * @param int    $width
     * @param int    $height
     *
     * @return null|string
     */
    function resize($filename, $width, $height)
    {
        $orig_image_filepath = is_file(DIR_IMAGE.$filename) ? DIR_IMAGE.$filename : '';
        $orig_image_filepath = $orig_image_filepath == '' && is_file(DIR_RESOURCE.'image/'.$filename) ? DIR_RESOURCE.'image/'.$filename : $orig_image_filepath;

        $info = pathinfo($filename);
        $extension = $info['extension'];
        if (in_array($extension, array('ico', 'svg', 'svgz'))) {
            $new_image = $filename;
        } else {
            $new_image = 'thumbnails/'.substr($filename, 0, strrpos($filename, '.')).'-'.$width.'x'.$height.'.'.$extension;
            if (!check_resize_image($orig_image_filepath, $new_image, $width, $height, $this->config->get('config_image_quality'))) {
                $err = new AWarning('Resize image error. File: '.$orig_image_filepath.'. Try to increase memory limit for PHP or decrease image size.');
                $err->toLog()->toDebug();
                return false;
            }
        }

        if (isset($this->request->server['HTTPS'])
            && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))
        ) {
            return HTTPS_IMAGE.$new_image;
        } else {
            return HTTP_IMAGE.$new_image;
        }
    }
}
