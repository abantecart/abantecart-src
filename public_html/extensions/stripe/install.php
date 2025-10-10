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

if (! defined ( 'DIR_CORE' )) {
header ( 'Location: static_pages/' );
}
/**
 * @var AController $this
 */
//check if columns exists before adding
$sql = "SELECT *
		FROM information_schema.COLUMNS
		WHERE
		TABLE_SCHEMA = '".DB_DATABASE."'
		AND TABLE_NAME = '".$this->db->table('products')."'
		AND COLUMN_NAME = 'subscription_plan_id'";
$result = $this->db->query($sql);
if( !$result->num_rows ){
	$this->db->query("ALTER TABLE ".$this->db->table('products')." ADD COLUMN `subscription_plan_id` varchar(32) DEFAULT '';");
}

$language_list = $this->model_localisation_language->getLanguages();

$rm = new AResourceManager();
$rm->setType('image');

$result = copy(
    DIR_EXT.'stripe'.DS.'image'.DS.'stripe-logo.png',
    DIR_RESOURCE.'image'.DS.'stripe-logo.png'
);

$resource = [
    'language_id'   => $this->config->get('storefront_language_id'),
    'name'          => [],
    'title'         => [],
    'description'   => [],
    'resource_path' => 'stripe-logo.png',
    'resource_code' => '',
];

foreach ($language_list as $lang) {
    $resource['name'][$lang['language_id']] = 'stripe_icon.jpg';
    $resource['title'][$lang['language_id']] = 'stripe_payment_storefront_icon';
    $resource['description'][$lang['language_id']] = 'Stripe Storefront Icon';
}
$resource_id = $rm->addResource($resource);

if ($resource_id) {
    $settings['stripe_payment_storefront_icon'] = $resource_id;
}