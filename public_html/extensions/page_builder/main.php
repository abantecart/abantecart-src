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

require_once(DIR_EXT.'page_builder'.DS.'core'.DS.'helper.php');
require_once(DIR_EXT.'page_builder'.DS.'core'.DS.'page_builder_hooks.php');
require_once(DIR_EXT.'page_builder'.DS.'core'.DS.'lib'.DS.'PBRender.php');

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

