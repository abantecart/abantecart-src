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
$coreDir = DIR_EXT . 'page_builder' . DS . 'core' . DS;
require_once($coreDir . 'helper.php');
require_once($coreDir . 'page_builder_hooks.php');
require_once($coreDir . 'lib' . DS . 'PBRender.php');

if (!defined('DIR_PB_TEMPLATES')) {

    $pbDirs = [
        DIR_SYSTEM . 'page_builder' . DS,
        DIR_SYSTEM . 'page_builder' . DS.'public' . DS,
        DIR_SYSTEM . 'page_builder' . DS.'presets' . DS,
        DIR_SYSTEM . 'page_builder' . DS.'savepoints' . DS
    ];
    $result = false;
    foreach($pbDirs as $subDir) {
        if (!is_dir($subDir)) {
            if (!mkdir($subDir, 0775)) {
                Registry::getInstance()?->get('log')->write(
                    'Page Builder Error: Cannot to create directory ' . $subDir . ' . Please check permissions!'
                );
                break;
            }
        } elseif (!is_readable($subDir) || !is_writable($subDir)) {
            if (!chmod($subDir, 0775)) {
                Registry::getInstance()?->get('log')->write(
                    'Page Builder Error: Have no access to directory ' . $subDir . ' . Please check permissions!'
                );
                break;
            }
        }
        $result = true;
    }

    if($result){
        define('DIR_PB_TEMPLATES', DIR_SYSTEM . 'page_builder' . DS );
    }
}

$controllers = [
    'storefront' => [
        'responses/extension/page_builder',
        'pages/extension/generic'
    ],
    'admin'      => [
        'pages/design/page_builder',
        'responses/design/page_builder',
        'responses/design/edit_block',
    ]
];

$models = [
    'storefront' => [],
    'admin'      => []
];

$templates = [
    'storefront' => [],
    'admin'      => [
        'pages/design/page_builder.tpl',
        'responses/design/proto_page.tpl',
        'responses/design/gjs_blocks_plugin.php.js'
    ]
];

$languages = [
    'storefront' => [],
    'admin'      => [
        'english/page_builder/page_builder'
    ]
];

