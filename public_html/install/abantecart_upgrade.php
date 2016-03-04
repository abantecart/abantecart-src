<?php

// add new media page menu item
$rm = new AResourceManager();
$rm->setType('image');
$language_id = $this->language->getContentLanguageID();

$data = array();
$data['resource_code'] = '<i class="fa fa-photo"></i>&nbsp;';
$data['name'] = array($language_id => 'Menu Icon Media Page');
$data['title'] = array($language_id => '');
$data['description'] = array($language_id => '');
$resource_id = $rm->addResource($data);

$menu = new AMenu ( "admin" );
$menu->insertMenuItem ( array (  "item_id" => "rl_manager",
								 "parent_id"=>"catalog",
								 "item_text" => "text_rl_manager",
								 "item_url" => "tool/rl_manager",
								 "item_icon_rl_id" => $resource_id,
								 "item_type"=>"core",
								 "sort_order"=>"8")
								);

$data = array();
$data['resource_code'] = '<i class="fa fa-bullhorn"></i>&nbsp;';
$data['name'] = array($language_id => 'Icon Settings IM');
$data['title'] = array($language_id => '');
$data['description'] = array($language_id => '');
$resource_id = $rm->addResource($data);

$menu->insertMenuItem ( array (  "item_id" => "settings_im",
								 "parent_id"=>"settings",
								 "item_text" => "text_settings_im",
								 "item_url" => "setting/setting/im",
								 "item_icon_rl_id" => $resource_id,
								 "item_type"=>"core",
								 "sort_order"=>"7")
								);

//TODO:
//need to update settings with resource path value for new RL single mode (replace by uri to sf-controller)
