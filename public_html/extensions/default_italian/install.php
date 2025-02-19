<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

//before install validate it is unique
$lng_code = 'it';
$lng_name = 'Italiano';
$lng_directory = 'italian';
$lng_locale = 'it_IT.UTF-8,it_IT,italian';
$lng_flag_path = 'extensions/default_italian/storefront/language/italian/flag.png';
$lng_sort = 3; // sorting order with other languages
$lng_status = 0; // Status on installation of extension

$this->db->query(
    "INSERT INTO " . $this->db->table('languages')." 
        (`name`,`code`,`locale`,`image`,`directory`,`filename`,`sort_order`, `status`)
    VALUES (
        'Italiano', 
        'it', 
        'it_IT.UTF-8,it_IT,italian', 
        'extensions/default_italian/storefront/language/italian/flag.png',
        'italian',
        'italian',
        3,
        0
    );"
);
$new_language_id = (int)$this->db->getLastId();

$xml = simplexml_load_file(DIR_EXT . 'default_italian/menu.xml');

$routes = [
    'text_index_home_menu'        => 'index/home',
    'text_product_special_menu'   => 'product/special',
    'text_account_login_menu'     => 'account/login',
    'text_account_logout_menu'    => 'account/logout',
    'text_account_account_menu'   => 'account/account',
    'text_account_history_menu'   => 'account/history',
    'text_checkout_cart_menu'     => 'checkout/cart',
    'text_checkout_shipping_menu' => 'checkout/fast_checkout',
];

if ($xml) {
    $translates = [];
    foreach ($xml->definition as $item) {
        $translates[$routes[(string)$item->key]] = (string)$item->value;
    }

    $storefront_menu = new AMenu_Storefront();
    $storefront_menu->addLanguage($new_language_id, $translates);
}
$this->cache->remove('localization');
