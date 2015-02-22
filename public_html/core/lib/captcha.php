<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class Captcha {
	protected $code;
	protected $width  = 35;
	protected $height = 150;

	function __construct() { 
		$this->code = substr(sha1(mt_rand()), 17, 6); 
	}

	function getCode(){
		return $this->code;
	}

	function showImage() {
        $image = imagecreatetruecolor($this->height, $this->width);

        $width = imagesx($image); 
        $height = imagesy($image);
		
        $black = imagecolorallocate($image, 0, 0, 0); 
        $white = imagecolorallocate($image, 255, 255, 255); 
        $red = imagecolorallocatealpha($image, 255, 0, 0, 75); 
        $green = imagecolorallocatealpha($image, 0, 255, 0, 75); 
        $blue = imagecolorallocatealpha($image, 0, 0, 255, 75); 
         
        imagefilledrectangle($image, 0, 0, $width, $height, $white); 
         
        imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $red); 
        imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $green); 
        imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $blue); 

        imagefilledrectangle($image, 0, 0, $width, 0, $black); 
        imagefilledrectangle($image, $width - 1, 0, $width - 1, $height - 1, $black); 
        imagefilledrectangle($image, 0, 0, 0, $height - 1, $black); 
        imagefilledrectangle($image, 0, $height - 1, $width, $height - 1, $black); 
         
        imagestring($image, 10, intval(($width - (strlen($this->code) * 9)) / 2),  intval(($height - 15) / 2), $this->code, $black);
	
		header('Content-type: image/jpeg');


		imagejpeg($image);
		imagedestroy($image);
		exit;
	}
}
