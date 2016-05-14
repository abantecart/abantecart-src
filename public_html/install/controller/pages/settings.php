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

/**
 * Class ControllerPagesSettings
 */
class ControllerPagesSettings extends AController {
	private $error = array();

	public function main() {
		$template_data = array();
		if ($this->request->is_POST() && ($this->validate())) {
			$this->redirect(HTTP_SERVER . 'index.php?rt=install');
		}

		if (isset($this->error['warning'])) {
			$template_data['error_warning'] = $this->error['warning'];
		} else {
			$template_data['error_warning'] = '';
		}

		//show warning about opcache and apc but do not block installation
		if (ini_get('opcache.enable')) {
			if($template_data['error_warning']){
				$template_data['error_warning'] .= '<br>';
			}
			$template_data['error_warning'] .= 'Warning: Your server have opcache php module enabled. Please disable it before installation!';
		}
		if (ini_get('apc.enabled')) {
			if($template_data['error_warning']){
				$template_data['error_warning'] .= '<br>';
			}
			$template_data['error_warning'] .= 'Warning: Your server have APC (Alternative PHP Cache) php module enabled. Please disable it before installation!';
		}

		$template_data['action'] = HTTP_SERVER . 'index.php?rt=settings';
		$template_data['config_catalog'] = DIR_ABANTECART . 'system/config.php';
		$template_data['system'] = DIR_SYSTEM;
		$template_data['cache'] = DIR_SYSTEM . 'cache';
		$template_data['logs'] = DIR_SYSTEM . 'logs';
		$template_data['image'] = DIR_ABANTECART . 'image';
		$template_data['image_thumbnails'] = DIR_ABANTECART . 'image/thumbnails';
		$template_data['download'] = DIR_ABANTECART . 'download';
		$template_data['extensions'] = DIR_ABANTECART . 'extensions';
		$template_data['resources'] = DIR_ABANTECART . 'resources';
		$template_data['admin_system'] = DIR_ABANTECART . 'admin/system';

		$this->addChild('common/header', 'header', 'common/header.tpl');
		$this->addChild('common/footer', 'footer', 'common/footer.tpl');	

		$this->view->assign('back', HTTP_SERVER . 'index.php?rt=license');
		$this->view->batchAssign($template_data);
		$this->processTemplate('pages/settings.tpl');
	}

	/**
	 * @return bool
	 */
	private function validate() {
		if ( version_compare(phpversion(), MIN_PHP_VERSION, '<') == TRUE ) {
			$this->error['warning'] = 'Warning: You need to use PHP '.MIN_PHP_VERSION.' or above for AbanteCart to work!';
		}

		if (!ini_get('file_uploads')) {
			$this->error['warning'] = 'Warning: file_uploads needs to be enabled in PHP!';
		}

		if (ini_get('session.auto_start')) {
			$this->error['warning'] = 'Warning: AbanteCart will not work with session.auto_start enabled!';
		}

		if (!extension_loaded('mysql') && !extension_loaded('mysqli') && !extension_loaded('pdo_mysql')) {
			$this->error['warning'] = 'Warning: MySQL extension needs to be loaded for AbanteCart to work!';
		}

		if (!function_exists('simplexml_load_file')) {
			$this->error['warning'] = 'Warning: SimpleXML functions needs to be available in PHP!';
		}

		if (!extension_loaded('gd')) {
			$this->error['warning'] = 'Warning: GD extension needs to be loaded for AbanteCart to work!';
		}

		if (!extension_loaded('mbstring') || !function_exists('mb_internal_encoding')) {
			$this->error['warning'] = 'Warning: MultiByte String extension needs to be loaded for AbanteCart to work!';
		}
		if (!extension_loaded('zlib')) {
			$this->error['warning'] = 'Warning: ZLIB extension needs to be loaded for AbanteCart to work!';
		}

		if (!is_writable(DIR_ABANTECART . 'system/config.php')) {
			$this->error['warning'] = 'Warning: config.php needs to be writable for AbanteCart to be installed!';
		}

		if (!is_writable(DIR_SYSTEM)) {
			$this->error['warning'] = 'Warning: System directory and all its children files/directories need to be writable for AbanteCart to work!';
		}

		if (!is_writable(DIR_SYSTEM . 'cache')) {
			$this->error['warning'] = 'Warning: Cache directory needs to be writable for AbanteCart to work!';
		}

		if (!is_writable(DIR_SYSTEM . 'logs')) {
			$this->error['warning'] = 'Warning: Logs directory needs to be writable for AbanteCart to work!';
		}

		if (!is_writable(DIR_ABANTECART . 'image')) {
			$this->error['warning'] = 'Warning: Image directory and all its children files/directories need to be writable for AbanteCart to work!';
		}

		if (!is_writable(DIR_ABANTECART . 'image/thumbnails')) {
			if (file_exists(DIR_ABANTECART . 'image/thumbnails') && is_dir(DIR_ABANTECART . 'image/thumbnails')) {
				$this->error['warning'] = 'Warning: image/thumbnails directory needs to be writable for AbanteCart to work!';
			} else {
				$result = mkdir(DIR_ABANTECART . 'image/thumbnails', 0777, true);
				if ($result) {
					chmod(DIR_ABANTECART . 'image/thumbnails', 0777);
					chmod(DIR_ABANTECART . 'image', 0777);
				} else {
					$this->error['warning'] = 'Warning: image/thumbnails does not exists!';
				}
			}
		}

		if (!is_writable(DIR_ABANTECART . 'download')) {
			$this->error['warning'] = 'Warning: Download directory needs to be writable for AbanteCart to work!';
		}

		if (!is_writable(DIR_ABANTECART . 'extensions')) {
			$this->error['warning'] = 'Warning: Extensions directory needs to be writable for AbanteCart to work!';
		}

		if (!is_writable(DIR_ABANTECART . 'resources')) {
			$this->error['warning'] = 'Warning: Resources directory needs to be writable for AbanteCart to work!';
		}

		if (!is_writable(DIR_ABANTECART . 'admin/system')) {
			$this->error['warning'] = 'Warning: Admin/system directory needs to be writable for AbanteCart to work!';
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
