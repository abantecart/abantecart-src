<?php
/*
NeoWize Insights - analytics and actionable insights for eCommerce sites.
For more info: www.neowize.com
*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

// INSTALL ADMIN DASHBOARD MENU ITEM

// add new menu item
$rm = new AResourceManager();
$rm->setType('image');

// create resource for the Neowize menu icon
$data = array();
$data['resource_code'] = '<i class="fa fa-signal"></i> ';
$data['name'] = array($language_id => 'Menu Icon NeoWize');
$data['title'] = array($language_id => '');
$data['description'] = array($language_id => '');
$resource_menu_icon_id = $rm->addResource($data);

// create NeoWize admin menu button
$menu = new AMenu ("admin");
$menu->insertMenuItem(array(
        "item_id"         => "neowize_insights",
        "parent_id"       => 'report_analytics',
        "item_icon_rl_id" => $resource_menu_icon_id,
        "item_text"       => "neowize_insights_name",
        "item_url"        => "neowize/dashboard",
        "item_type"       => "extension",
        "sort_order"      => "0",
    )
);

// clear layouts cache
if (isset($this->cache->remove)) {
    $this->cache->remove('layout');
}

