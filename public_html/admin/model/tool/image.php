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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ModelToolImage extends Model {
	function resize($filename, $width, $height) {
		if (!is_file(DIR_IMAGE . $filename)) {
			return null;
		}

		$info = pathinfo($filename);
		$extension = $info['extension'];
		if($extension=='ico'){
			$new_image = $filename;
		}else{
			$old_image = $filename;
			$new_image = 'thumbnails/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

			if (!is_file(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
				$path = '';

				$directories = explode('/', dirname(str_replace('../', '', $new_image)));

				foreach ($directories as $directory) {
					$path = $path . '/' . $directory;

					if (!file_exists(DIR_IMAGE . $path)) {
						@mkdir(DIR_IMAGE . $path, 0777);
						chmod(DIR_IMAGE . $path, 0777);
					}
				}

				$image = new AImage(DIR_IMAGE . $old_image);
				$image->resize($width, $height);
				$image->save(DIR_IMAGE . $new_image);
				unset($image);
			}
		}


		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			return HTTPS_IMAGE . $new_image;
		} else {
			return HTTP_IMAGE . $new_image;
		}	
	}
}
