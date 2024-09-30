<?php

if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}
if(!defined('DIR_PB_TEMPLATES')) {
    define('DIR_PB_TEMPLATES', DIR_SYSTEM.'page_builder'.DIRECTORY_SEPARATOR);
    if(!is_dir(DIR_PB_TEMPLATES)) {
        @mkdir(DIR_PB_TEMPLATES,0775);
    }
    define('DIR_PB_PRESETS', DIR_SYSTEM.'page_builder'.DIRECTORY_SEPARATOR.'presets'.DIRECTORY_SEPARATOR);
    if(!is_dir(DIR_PB_PRESETS)) {
        @mkdir(DIR_PB_PRESETS,0775);
    }
}

if(!class_exists('ExtensionPageBuilder')){
    include_once('core/page_builder_hooks.php');
    include_once('core/lib/PBRender.php');
}
$controllers = [
    'storefront' => [
        'responses/extension/page_builder',
        'pages/extension/generic'
    ],
    'admin' => [
        'pages/design/page_builder',
        'responses/design/page_builder',
        'responses/design/edit_block',
    ]
];

$models = [
    'storefront' => [],
    'admin' => []
];

$templates = [
    'storefront' => [],
    'admin' => [
        'pages/design/page_builder.tpl',
        'responses/design/proto_page.tpl',
        'responses/design/gjs_blocks_plugin.php.js'
    ]
];

$languages = [
    'storefront' => [],
    'admin' => [
        'english/page_builder/page_builder'
    ]
];

