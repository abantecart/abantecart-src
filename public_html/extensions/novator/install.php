<?php

if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}
if(!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once (DIR_EXT . 'novator'.DS.'core'.DS.'helper.php');

$file = DIR_EXT . 'novator'.DS.'layout.xml';
$layout = new ALayoutManager('default');
$layout->loadXml(
    [
        'file' => $file
    ]
);

//if pageBuilder installed
// replace custom_block_ids inside default presets of template
if(function_exists('preparePageBuilderPreset')) {
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
}


$language_list = $this->model_localisation_language->getLanguages();

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
    'resource_path' => 'abc-logo-white.png',
    'resource_code' => '',
];

foreach ($language_list as $lang) {
    $resource['name'][$lang['language_id']] = 'abc-logo';
    $resource['title'][$lang['language_id']] = 'abc-logo';
    $resource['description'][$lang['language_id']] = 'abc-logo.png';
}
$resource_id = $rm->addResource($resource);

if ($resource_id) {
    $settings['config_logo'] = $resource_id;
}

$settings['config_image_product_width'] = 312;
$settings['config_image_product_height'] = 400;

$settings['config_image_additional_width'] = 90;
$settings['config_image_additional_height'] = 90;

$settings['viewed_products_image_height'] = 400;
$settings['viewed_products_image_width'] = 312;
$settings['viewed_products_limit'] = 4;
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