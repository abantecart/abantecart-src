<?php
/*
Neowize Insights - analytics and actionable insights for eCommerce sites.
For more info: www.neowize.com
*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

//delete menu item
$menu = new AMenu ("admin");
$menu->deleteMenuItem("neowize_insights");