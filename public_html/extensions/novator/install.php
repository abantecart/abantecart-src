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
if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}
if(!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once (DIR_EXT . 'novator'.DS.'core'.DS.'helper.php');

$file = DIR_EXT . 'novator'.DS.'layout.xml';
$layout = new ALayoutManager('novator');
$layout->loadXml(
    [
        'file' => $file
    ]
);

//if pageBuilder installed
// replace custom_block_ids inside default presets of template
/*if(function_exists('preparePageBuilderPreset')) {
    $layout = new ALayoutManager('novator');
    $allBlocks = $layout->getBlocksList();
    $bs5Blocks = [];
    foreach ($allBlocks as $b) {
        if ($b['custom_block_id'] && is_int(stripos($b['block_name'], 'BS5'))) {
            $bs5Blocks[strtoupper($b['block_name'])] = $b['custom_block_id'];
        }
    }
    $presetFiles = [
        DIR_EXT
        .'novator'.DS
        .'storefront'.DS
        .'view'.DS
        .'novator'.DS
        .'default_preset.json'
    ];

    foreach ($presetFiles as $preset) {
        if (!is_file($preset) || !is_readable($preset)) {
            continue;
        }
        $pbTemplateData = file_get_contents($preset);
        $pbTemplateData = json_decode($pbTemplateData, true, JSON_PRETTY_PRINT);

        $newPreset = $pbTemplateData;
        $newPreset['pageHtml']['html'] = preparePageBuilderPreset($pbTemplateData['pageHtml']['html'], 'html', $bs5Blocks);
        $newPreset['gjs-components'] = preparePageBuilderPreset($pbTemplateData['gjs-components'], 'components', $bs5Blocks);
        $newPreset['gjs-components'] = json_encode($newPreset['gjs-components']);
        file_put_contents($preset, json_encode($newPreset));
    }

    $presaved_sets = glob(DIR_EXT.'novator'.DS.'system'.DS.'page_builder'.DS.'novator'.DS.'presets'.DS.'*');
    foreach($presaved_sets as $item){
        @copy(
            $item,
            DIR_SYSTEM.'page_builder'.DS.'presets'.DS.basename($item)
        );
    }
}*/

/** @var ModelLocalisationLanguage $mdl */
$mdl = $this->load->model('localisation_language');
$language_list = $mdl->getLanguages();

$rm = new AResourceManager();
$rm->setType('image');

$result = copy(
    DIR_EXT.'novator'.DS.'image'.DS.'abc-logo.png',
    DIR_RESOURCE.'image'.DS.'abc-logo.png'
);

$resource = [
    'language_id'   => $this->config->get('storefront_language_id'),
    'name'          => [],
    'title'         => [],
    'description'   => [],
    'resource_path' => 'abc-logo.png',
    'resource_code' => '',
];

foreach ($language_list as $lang) {
    $resource['name'][$lang['language_id']] = 'abc-logo';
    $resource['title'][$lang['language_id']] = 'abc-logo';
    $resource['description'][$lang['language_id']] = 'abc-logo.png';
}


try {
    $resource_id = $rm->addResource($resource);
}catch (Exception $e)
{}
if ($resource_id) {
    $settings['config_logo'] = $resource_id;
}

$settings['config_image_product_width'] = 312;
$settings['config_image_product_height'] = 400;

$settings['config_image_additional_width'] = 90;
$settings['config_image_additional_height'] = 90;

$settings['viewed_products_image_height'] = 400;
$settings['viewed_products_image_width'] = 312;
$settings['viewed_products_limit'] = 8;
$settings['config_image_related_width'] = 312;
$settings['config_image_related_height'] = 400;
$settings['config_image_thumb_width'] = 420;
$settings['config_image_thumb_height'] = 534;
$settings['config_image_popup_width'] = 500;
$settings['config_image_popup_height'] = 636;
$settings['config_bestseller_limit'] = 4;
$settings['config_featured_limit'] = 4;
$settings['config_latest_limit'] = 4;
$settings['config_special_limit'] = 4;
$settings['config_catalog_limit'] = 20;

/* @var ModelSettingSetting $mdlSetting */
$mdlSetting = $this->load->model('setting/setting');
$mdlSetting->editSetting('checkout', ['fast_checkout_buy_now_status'=>1,'fast_checkout_create_account'=>1]);
