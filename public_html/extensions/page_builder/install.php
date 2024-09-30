<?php

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

$tmpl_id = $this->config->get('config_storefront_template');
$src = DIR_EXT.$tmpl_id.DS.'storefront'.DS.'base.html';
if(!is_file($src)){
    $src = DIR_EXT.'page_builder'.DS.'base.html';
}
$def = DIR_SYSTEM.'page_builder'.DS.$tmpl_id;
mkdir($def,0775);
copy(
    $src,
    $def.DS.'base.html'
);

$dirs = glob(DIR_EXT.'*'.DS.'system'.DS.'page_builder');
foreach($dirs as $dir){
    recurseCopy($dir, DIR_SYSTEM.'page_builder');
}