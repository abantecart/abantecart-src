<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}

$controllers = array(
    'storefront' => array(),
    'admin' => array());

$models = array(
    'storefront' => array(),
    'admin' => array());

$templates = array(
    'storefront' => array(),
    'admin' => array());

$languages = array(
    'storefront' => array(),
    'admin' => array(
        'english/default_simplified_chinese/default_simplified_chinese'));

