<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
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
 * @property AIMManager $im
 * @property AShoppingData $shopping_data
 */
abstract class Model
{

    public $registry;
    public $data;

    /**
     * @param $registry Registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->registry->get($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed|null
     */
    public function __call($method, $args)
    {
        if (!$this->registry->has('extensions')) {
            return null;
        }
        array_unshift($args, $this);
        return call_user_func_array([$this->extensions, $method], $args);
    }
}
