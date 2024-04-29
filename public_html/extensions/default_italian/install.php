<?php
/*------------------------------------------------------------------------------
  $Id$

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2011, 2012 AlgoZone, Inc

------------------------------------------------------------------------------*/
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

$this->db->query("INSERT INTO ".DB_PREFIX."languages (`name`,`code`,`locale`,`image`,`directory`,`filename`,`sort_order`, `status`)
				  VALUES ('Italiano', 'it', 'it_IT.UTF-8,it_IT,italian', 'extensions/default_italian/storefront/language/italian/flag.png','italian','italian','3',0);");
$new_language_id = $this->db->getLastId();

$xml = simplexml_load_file(DIR_EXT.'default_italian/menu.xml');

$routes = array(
    'text_index_home_menu'        => 'index/home',
    'text_product_special_menu'   => 'product/special',
    'text_account_login_menu'     => 'account/login',
    'text_account_logout_menu'    => 'account/logout',
    'text_account_account_menu'   => 'account/account',
    'text_account_invoice_menu'   => 'account/invoice',
    'text_checkout_cart_menu'     => 'checkout/cart',
    'text_checkout_shipping_menu' => 'checkout/fast_checkout',
);

if ($xml) {
    foreach ($xml->definition as $item) {
        $translates[$routes[(string)$item->key]] = (string)$item->value;
    }

    $storefront_menu = new AMenu_Storefront();
    $storefront_menu->addLanguage($new_language_id, $translates);
}
$this->cache->remove('localization');
