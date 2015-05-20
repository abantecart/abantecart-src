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
class ModelToolImage extends Model {
	public $data = array();

	public function resize($filename, $width = 0, $height = 0) {
		if (!is_file(DIR_IMAGE . $filename)) {
			return null;
		}

		$https = $this->request->server['HTTPS'];
		if( $https == 'on' || $https == '1'){
			$http_path = HTTPS_IMAGE;
		} else{
			$http_path = HTTP_IMAGE;
		}

		$info = pathinfo($filename);
		$extension = $info['extension'];

		$old_image = $filename;
		$new_image = 'thumbnails/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
		
		if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
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

		return $http_path . $new_image;

	}
}
