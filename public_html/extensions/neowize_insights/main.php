<?php
/*Neowize Insights - analytics and actionable insights for eCommerce sites.
For more info: www.neowize.com*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

// include our hooks and utils
include_once('core/utils.php');
include_once('core/hooks.php');

// controllers
$controllers = array(
    'storefront' => array(),
    'admin'      => array('pages/neowize/dashboard'),
);

// models
$models = array(
    'storefront' => array(),
    'admin'      => array(),
);

// templates
$templates = array(
    'storefront' => array('common/footer_top.post.tpl'),
    'admin'      => array('pages/neowize/dashboard.tpl'),
);

//languages
$languages = array(
    'storefront' => array(),
    'admin'      => array('english/neowize_insights/neowize_insights'),
);
