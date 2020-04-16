<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

$file = DIR_EXT . '/fast_checkout/layout.xml';
$layout = new ALayoutManager('default');
$layout->loadXml(array('file' => $file));




