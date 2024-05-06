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

if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}


$language_list = $this->model_localisation_language->getLanguages();

$rm = new AResourceManager();
$rm->setType('image');

$result = copy(DIR_EXT.'cardknox/image/cardknox_logo.png', DIR_RESOURCE.'image/cardknox_logo.png');

$resource = [
    'language_id'   => $this->config->get('storefront_language_id'),
    'name'          => [],
    'title'         => [],
    'description'   => [],
    'resource_path' => 'cardknox_logo.png',
    'resource_code' => '',
];

foreach ($language_list as $lang) {
    $resource['name'][$lang['language_id']] = 'cardknox_logo.png';
    $resource['title'][$lang['language_id']] = 'cardknox_payment_storefront_icon';
    $resource['description'][$lang['language_id']] = 'CardKnox Storefront Icon';
}
$resource_id = $rm->addResource($resource);

if ($resource_id) {
    // get hexpath of resource (RL moved given file from rl-image-directory in own dir tree)
    $resource_info = $rm->getResource($resource_id, $this->config->get('admin_language_id'));
    // write it path in settings (array from parent method "install" of extension manager)
    $settings['cardknox_payment_storefront_icon'] = 'image/'.$resource_info['resource_path'];
}

$settings['cardknox_custom_logo'] = $this->config->get('config_logo');



