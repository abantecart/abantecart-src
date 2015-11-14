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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}
/**
 * @property AConfig $config
 * @property ADB $db
 * @property ACache $cache
 * @property AResource $resource
 * @property AView $view
 * @property ALoader $load
 * @property AHtml $html
 * @property ARequest $request
 * @property AResponse $response
 * @property ASession $session
 * @property ExtensionsApi $extensions
 * @property AExtensionManager $extension_manager
 * @property ALayout $layout
 * @property ACurrency $currency
 * @property ACart $cart
 * @property ATax $tax
 * @property AUser $user
 * @property ALog $log
 * @property AMessage $messages
 * @property ACustomer $customer
 * @property ADocument $document
 * @property ALanguageManager $language
 * @property ADataEncryption $dcrypt
 * @property ModelCatalogCategory $model_catalog_category
 * @property ADownload $download
 * @property AOrderStatus $order_status
 */
abstract class Model {

	public $registry;

	/**
	 * @param $registry Registry
	 */
	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	public function __call($method, $args) {
		if (!$this->registry->has('extensions')) {
			return null;
		}
		array_unshift($args, $this);
		$return = call_user_func_array(array( $this->extensions, $method ), $args);
		return $return;
	}
}
