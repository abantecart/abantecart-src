<?php

if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}

require_once (DIR_EXT . '/bootstrap5/core/helper.php');

$file = DIR_EXT . '/bootstrap5/layout.xml';
$layout = new ALayoutManager('default');
$layout->loadXml(['file' => $file]);

//if pageBuilder installed
// replace custom_block_ids inside default presets of template
if(function_exists('preparePageBuilderPreset')) {
    $layout = new ALayoutManager('bootstrap5');
    $allBlocks = $layout->getBlocksList();
    $bs5Blocks = [];
    foreach ($allBlocks as $b) {
        if ($b['custom_block_id'] && is_int(stripos($b['block_name'], 'BS5'))) {
            $bs5Blocks[strtoupper($b['block_name'])] = $b['custom_block_id'];
        }
    }
    $presetFiles = [
        DIR_EXT.'/bootstrap5/storefront/view/bootstrap5/default_preset.json'
    ];

    foreach ($presetFiles as $preset) {
        if (!is_file($preset) || !is_readable($preset)) {
            continue;
        }
        $pbTemplateData = file_get_contents($preset);
        $pbTemplateData = json_decode($pbTemplateData, true, JSON_PRETTY_PRINT);

        $newPreset = $pbTemplateData;
        $newPreset['gjs-html'] = preparePageBuilderPreset($pbTemplateData['gjs-html'], 'html', $bs5Blocks);
        $newPreset['gjs-components'] = preparePageBuilderPreset($pbTemplateData['gjs-components'], 'components', $bs5Blocks);
        $newPreset['gjs-components'] = json_encode($newPreset['gjs-components']);
        file_put_contents($preset, json_encode($newPreset));
    }
}


$language_list = $this->model_localisation_language->getLanguages();

$rm = new AResourceManager();
$rm->setType('image');

$result = copy(
    DIR_EXT.'bootstrap5/image/abc-logo-white.png',
    DIR_RESOURCE.'image/abc-logo-white.png'
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
    $resource['name'][$lang['language_id']] = 'abc-logo-white';
    $resource['title'][$lang['language_id']] = 'abc-logo-white';
    $resource['description'][$lang['language_id']] = 'abc-logo-white.png';
}
$resource_id = $rm->addResource($resource);

if ($resource_id) {
    $settings['config_logo'] = $resource_id;
}