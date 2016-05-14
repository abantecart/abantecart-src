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

class ModelToolImage extends Model{
	public $data = array ();

	/**
	 * @param $filename - relative file path
	 * @param int $width - in pixels
	 * @param int $height - in pixels
	 * @param null $alias - alias filename for saving
	 * @param string $mode - can be url or file.
	 * @return null|string - string is URL or abs file path
	 */
	public function resize($filename, $width = 0, $height = 0, $alias = null, $mode = 'url'){
		if (!is_file(DIR_IMAGE . $filename) && !is_file(DIR_RESOURCE . 'image/' . $filename)){
			return null;
		}

		$old_image_filepath = is_file(DIR_IMAGE . $filename) ? DIR_IMAGE . $filename : '';
		$old_image_filepath = $old_image_filepath == '' && is_file(DIR_RESOURCE . 'image/' . $filename) ? DIR_RESOURCE . 'image/' . $filename : $old_image_filepath;

		$https = $this->request->server['HTTPS'];
		if ($https == 'on' || $https == '1'){
			$http_path = HTTPS_IMAGE;
		} else{
			$http_path = HTTP_IMAGE;
		}
		//when need to get abs path of result
		if ($mode == 'path'){
			$http_path = DIR_IMAGE;
		}

		$info = pathinfo($filename);
		$extension = $info['extension'];

		$alias = !$alias ? $filename : dirname($filename) . '/' . basename($alias);

		$new_image = 'thumbnails/' . substr($alias, 0, strrpos($alias, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
		$new_image_filepath = DIR_IMAGE . $new_image;
		//retina variant
		$new_image2x = 'thumbnails/' . substr($alias, 0, strrpos($alias, '.')) . '-' . $width . 'x' . $height . '@2x.' . $extension;
		$new_image_filepath2x = DIR_IMAGE . $new_image2x;

		if (!file_exists($new_image_filepath)
				|| (filemtime($old_image_filepath) > filemtime($new_image_filepath))
				|| !file_exists($new_image_filepath2x)
				|| (filemtime($old_image_filepath) > filemtime($new_image_filepath2x))
		){
			$path = '';
			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
			foreach ($directories as $directory){
				$path = $path . '/' . $directory;
				if (!file_exists(DIR_IMAGE . $path)){
					@mkdir(DIR_IMAGE . $path, 0777);
					chmod(DIR_IMAGE . $path, 0777);
				}
			}
			$image = new AImage($old_image_filepath);
			$quality = $this->config->get('config_image_quality');
			$image->resizeAndSave($new_image_filepath,
								$width,
								$height,
								array(
										'quality' => $quality
								));

			unset($image);

			if ($this->config->get('config_retina_enable')){
				$image = new AImage($old_image_filepath);
				$image->resizeAndSave($new_image_filepath2x,
										$width * 2,
										$height * 2,
										array(
												'quality' => $quality
										));
				unset($image);
			}
		}

		if ($this->config->get('config_retina_enable') && isset($this->request->cookie['HTTP_IS_RETINA'])){
			$new_image = $new_image2x;
		}


		return $http_path . $new_image;
	}
}