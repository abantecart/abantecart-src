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

/** @var AController $this */
$language_list = $this->language->getAvailableLanguages();

$rm = new AResourceManager();
$rm->setType('image');

$result = copy(
    DIR_EXT.'ups/image/logo.png',
    DIR_RESOURCE.'image/logo.png'
);

$resource = [
    'language_id'   => $this->config->get('storefront_language_id'),
    'name'          => [],
    'title'         => [],
    'description'   => [],
    'resource_path' => 'logo.png',
    'resource_code' => '',
];

foreach ($language_list as $lang) {
    $resource['name'][$lang['language_id']] = 'logo.png';
    $resource['title'][$lang['language_id']] = 'UPD storefront icon';
    $resource['description'][$lang['language_id']] = 'UPS Storefront Icon';
}
$resource_id = $rm->addResource($resource);

if ($resource_id) {
    $settings['ups_shipping_storefront_icon'] = $resource_id;
}

$sql = "SELECT * 
       FROM ".$this->db->table('order_data_types')."
       WHERE name = 'shipping_data'";
$result = $this->db->query($sql);
$rows = (array)$result->rows;

$language_id = $this->language->getDefaultLanguageID();
if(!$rows){
    $sql = "INSERT INTO ".$this->db->table('order_data_types')."
    (language_id, name, date_added)
    VALUES (".(int)$language_id.", 'shipping_data', NOW())";
    $this->db->query($sql);
}
