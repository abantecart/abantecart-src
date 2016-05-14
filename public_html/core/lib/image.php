<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')){
	header('Location: static_pages/');
}

/**
 * Class AImage
 */
final class AImage{
	/**
	 * @var string
	 */
	private $file;
	/**
	 * @var resource
	 */
	private $image;
	/**
	 * @var array
	 */
	private $info = array();

	/**
	 * @var
	 */
	private $registry;

	/**
	 * @param string $filename
	 * @throws AException
	 */
	public function __construct($filename){
		if (file_exists($filename) && ($info = getimagesize($filename))){
			$this->file = $filename;
			$this->info = array (
					'width'    => $info[0],
					'height'   => $info[1],
					'bits'     => $info['bits'],
					'mime'     => $info['mime'],
					'channels' => $info['channels']
			);
			$this->registry = Registry::getInstance();
			$this->image = $this->get_gd_resource($filename);
		} else{
			$error = new AError('Error: Cannot load image ' . $filename);
			$error->toMessages()->toDebug()->toLog();
			return false;
		}
		return true;
	}

	/**
	 * @return array
	 */
	public function getInfo(){
		return $this->info;
	}

	/**
	 * @param string $filename
	 * @return resource|string
	 */
	private function get_gd_resource($filename){
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
		if (function_exists('memory_get_usage')){
			if (memory_get_usage() + $mem_estimate > (integer)ini_get('memory_limit') * pow(1024, 2)){
				$new_mem = (integer)ini_get('memory_limit') + ceil(((memory_get_usage() + $mem_estimate) - (integer)ini_get('memory_limit') * pow(1024, 2)) / pow(1024, 2)) . 'M';
				//TODO. Validate if memory change was in fact changed or report an error 
				ini_set('memory_limit', $new_mem);
			}
		}

		$res_img = '';
		if ($mime == 'image/gif'){
			$res_img = imagecreatefromgif($filename);
		} elseif ($mime == 'image/png'){
			$res_img = imagecreatefrompng($filename);
		} elseif ($mime == 'image/jpeg'){
			$res_img = imagecreatefromjpeg($filename);
		}
		return $res_img;
	}


	public function resizeAndSave($filename, $width, $height, $options = array()){
		if (!$filename){
			return false;
		}
		$width = (int)$width;
		$height = (int)$height;
		$options = (array)$options;

		$quality  = !isset($options['quality']) ? 90 : (int)$options['quality'];
		$nofill  = !isset($options['nofill']) ? false : $options['nofill'];

		//if size will change - resize it and save with GD2, otherwise - just copy file
		if($this->info['width'] != $width && $this->info['height'] != $height ){
			$this->resize($width,$height,$nofill);
			$result = $this->save($filename,$quality);
		}else{
			$result = copy($this->file,$filename);
		}

		return $result;
	}

	/**
	 * @param string $filename - full file name
	 * @param int $quality - some number in range from 1 till 100
	 * @return bool
	 */
	public function save($filename, $quality = 90){
		if (is_object($this->registry)&& $this->registry->has('extensions')){
			$result = $this->registry->get('extensions')->hk_save($this, $filename, $quality);
		} else{
			$result = $this->_save($filename, $quality);
		}
		return $result;
	}

	/**
	 * @param string $filename - full file name
	 * @param int $quality - some number in range from 1 till 100
	 * @return bool
	 */
	public function _save($filename, $quality = 90){
		if (!$filename || !$this->image){
			return false;
		}

		$quality = (int)$quality;
		$quality = $quality > 100 ? 100 : $quality;
		$quality = $quality < 1 ? 1 : $quality;

		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		if ($extension == 'jpeg' || $extension == 'jpg'){
			imagejpeg($this->image, $filename, $quality);
		} elseif ($extension == 'png'){
			//use maximum compression for PNG
			imagepng($this->image, $filename, 9, PNG_ALL_FILTERS);
		} elseif ($extension == 'gif'){
			imagegif($this->image, $filename);
		}
		if (is_file($filename)){
			chmod($filename, 0777);
		}
		imagedestroy($this->image);
		return true;
	}

	/**
	 * @param int $width
	 * @param int $height
	 * @param bool|false $nofill - sign for background fill
	 * @return bool|null
	 */
	public function resize($width = 0, $height = 0, $nofill = false){
		if(!$this->image || !$this->info['width'] || !$this->info['height']){
			return false;
		}
		if ($width == 0 && $height == 0){
			return false;
		}

		$scale = min($width / $this->info['width'], $height / $this->info['height']);

		if ($scale == 1 && $this->info['mime'] != 'image/png'){
			return false;
		}

		$new_width = (int)round($this->info['width'] * $scale, 0);
		$new_height = (int)round($this->info['height'] * $scale, 0);
		$xpos = (int)(($width - $new_width) / 2);
		$ypos = (int)(($height - $new_height) / 2);


		$image_old = $this->image;

		$this->image = imagecreatetruecolor($width, $height);

		if (isset($this->info['mime']) && $this->info['mime'] == 'image/png'){
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagefill($this->image, 0, 0, $background);
		} else{
			if (!$nofill){ // if image no transparant
				$background = imagecolorallocate($this->image, 255, 255, 255);
				imagefilledrectangle($this->image, 0, 0, $width, $height, $background);
			}
		}

		if (is_resource($this->image)){
			imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->info['width'], $this->info['height']);
		}
		if (is_resource($image_old)){
			imagedestroy($image_old);
		}

		$this->info['width'] = $width;
		$this->info['height'] = $height;

		return true;
	}

	/**
	 * @param $filename
	 * @param string $position
	 */
	public function watermark($filename, $position = 'bottomright'){
		if(!is_resource($this->image)){
			return false;
		}
		$watermark = $this->get_gd_resource($filename);

		$watermark_width = imagesx($watermark);
		$watermark_height = imagesy($watermark);
		$watermark_pos_x = $watermark_pos_y = 0;

		switch($position){
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
	}

	/**
	 * @param int $top_x
	 * @param int $top_y
	 * @param int $bottom_x
	 * @param int $bottom_y
	 */
	public function crop($top_x, $top_y, $bottom_x, $bottom_y){
		if(!is_resource($this->image)){
			return false;
		}

		$image_old = $this->image;
		$this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

		imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->info['width'], $this->info['height']);
		imagedestroy($image_old);

		$this->info['width'] = $bottom_x - $top_x;
		$this->info['height'] = $bottom_y - $top_y;
	}

	/**
	 * @param float $degree
	 * @param string $color
	 */
	public function rotate($degree, $color = 'FFFFFF'){
		if(!is_resource($this->image)){
			return false;
		}

		$rgb = $this->html2rgb($color);
		$this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
		$this->info['width'] = imagesx($this->image);
		$this->info['height'] = imagesy($this->image);
	}

	/**
	 * @param int $filter
	 */
	public function filter($filter){
		if(!is_resource($this->image)){
			return false;
		}
		imagefilter($this->image, $filter);
		return true;
	}

	/**
	 * @param string $text
	 * @param int $x
	 * @param int $y
	 * @param int $size
	 * @param string $color
	 */
	public function text($text, $x = 0, $y = 0, $size = 5, $color = '000000'){
		if(!is_resource($this->image)){
			return false;
		}
		$rgb = $this->html2rgb($color);
		imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
		return true;
	}

	/**
	 * @param string $filename
	 * @param int $x
	 * @param int $y
	 * @param int $opacity
	 */
	public function merge($filename, $x = 0, $y = 0, $opacity = 100){

		$merge = $this->get_gd_resource($filename);

		if(!is_resource($this->image) || $merge){
			return false;
		}

		$merge_width = imagesx($merge);
		$merge_height = imagesy($merge);

		imagecopymerge($this->image, $merge, $x, $y, 0, 0, $merge_width, $merge_height, $opacity);
		return false;
	}

	/**
	 * @param string $color
	 * @return array|bool
	 */
	private function html2rgb($color){
		if ($color[0] == '#'){
			$color = substr($color, 1);
		}

		if (strlen($color) == 6){
			list($r, $g, $b) = array ($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
		} elseif (strlen($color) == 3){
			list($r, $g, $b) = array ($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
		} else{
			return false;
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		return array ($r, $g, $b);
	}

	public function __destruct(){
		if (is_resource($this->image)){
			imagedestroy($this->image);
		} else{
			$this->image = null;
		}
	}
}