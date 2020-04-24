<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

$file = DIR_EXT . '/fast_checkout/layout.xml';
$layout = new ALayoutManager('default');
$layout->loadXml(array('file' => $file));


$file_success = DIR_EXT . '/fast_checkout/layout_success.xml';
$layout->loadXml(array('file' => $file_success));




