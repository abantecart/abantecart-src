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
require_once (DIR_EXT.'page_builder'.DS.'core'.DS.'helper.php');

// add new menu item
$rm = new AResourceManager();
$rm->setType('image');

$language_id = $this->language->getContentLanguageID();
$data = [];
$data['resource_code'] = '<i class="fa fa-object-group"></i>&nbsp;';
$data['name'] = [$language_id => 'Menu Icon Page Builder'];
$data['title'] = [$language_id => ''];
$data['description'] = [$language_id => ''];
$resource_id = $rm->addResource($data);

$menu = new AMenu ("admin");
$menu->insertMenuItem(
    [
        "item_id"         => "page_builder",
        "parent_id"       => "design",
        "item_text"       => "page_builder_name",
        "item_url"        => "design/page_builder",
        "item_icon_rl_id" => $resource_id,
        "item_type"       => "extension",
        "sort_order"      => 2
    ]
);

if(!is_dir(DIR_SYSTEM.'page_builder')){
    mkdir(DIR_SYSTEM.'page_builder',0775);
}

$dirs = glob(DIR_EXT.'*'.DS.'system'.DS.'page_builder');
foreach($dirs as $dir){
    recurseCopy($dir, DIR_SYSTEM.'page_builder');
}