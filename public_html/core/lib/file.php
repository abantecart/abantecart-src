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

/**
 * Class to handle file uploads
 *
 */
/**
 * @property ALanguageManager $language
 * @property ADB $db
 * @property ACache $cache
 * @property AConfig $config
 */

class AFile {

	/**
	 * @var registry - access to application registry
	 */
	protected $registry;

	public function __construct() {
		$this->registry = Registry::getInstance();
		$this->errors = array();
	}

	/**
	 * @param  $key - key to load data from registry
	 * @return mixed  - data from registry
	 */
	public function __get($key) {
		return $this->registry->get($key);
	}

	/**
	 * @param  string $key - key to save data in registry
	 * @param  mixed $value - key to save data in registry
	 * @return mixed  - data from registry
	 */
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	/**
	 * @param $settings
	 * @param array $data
	 * @internal param int $product_id
	 * @internal param int $option_id
	 * @return array
	 */
	public function validateFileOption($settings, $data) {

		$errors = array();

		if ( empty($data['name']) ) {
			$errors[] = $this->language->get('error_empty_file_name');
		}

		if ( !empty($settings['extensions']) ) {
			$allowed_extensions = explode(',', str_replace(' ', '', $settings['extensions']));
			$extension = substr(strrchr($data['name'], '.'), 1);

			if ( !in_array($extension, $allowed_extensions) ) {
				$errors[] = sprintf($this->language->get('error_file_extension'), $settings['extensions']).' ('.$data['name'].')';
			}

		}

		if ( (int) $settings['min_size'] > 0 ) {
			$min_size_kb = $settings['min_size'];
			if ( (int) $data['size'] / 1024 < $min_size_kb ) {
				$errors[] = sprintf($this->language->get('error_min_file_size'), $min_size_kb).' ('.$data['name'].')';
			}
		}

		//convert all to Kb and check the limits on abantecart and php side
		$abc_upload_limit = (int)$this->config->get('config_upload_max_size'); //comes in Kb
		$php_upload_limit = (int)ini_get('upload_max_filesize') * 1024; //comes in Mb
		$max_size_kb = $abc_upload_limit <  $php_upload_limit ? $abc_upload_limit : $php_upload_limit;

		//check limit for attribute if set
		if ( (int) $settings['max_size'] > 0 ) {
			$max_size_kb = (int) $settings['max_size'] < $max_size_kb ? (int) $settings['max_size'] : $max_size_kb;
		}

		if ( $max_size_kb < (int) $data['size'] / 1024 ) {
			$errors[] = sprintf($this->language->get('error_max_file_size'), $max_size_kb).' ('.$data['name'].')';
		}

		return $errors;

	}

	/**
	 * @param string $upload_sub_dir
	 * @param string $file_name
	 * @return array
	 */	
	public function getUploadFilePath($upload_sub_dir, $file_name) {
		if ( empty($file_name)) {
			return array();
		}
		$uplds_dir = DIR_ROOT . '/admin/system/uploads';
		make_writable_dir($uplds_dir);
		$file_path = $uplds_dir . '/' . $upload_sub_dir . '/';
		make_writable_dir($file_path);

		$ext = strrchr($file_name, '.');
		$file_name = substr($file_name, 0, strlen($file_name) - strlen($ext));

		$i = '';
		$real_path = '';
		do {
			if ( $i ) {
				$new_name = $file_name . '_' . $i . $ext;
			} else {
				$new_name = $file_name . $ext;
				$i = 0;
			}

			$real_path = $file_path . $new_name;
			$i++;
		} while (file_exists($real_path));

		return array('name' => $new_name, 'path' => $real_path);
	}

}