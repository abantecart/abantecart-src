<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

require_once('core/novator_hook.php');

$controllers = [
    'storefront' => [
        'blocks/category_slides',
        'blocks/category_filter'
    ],
    'admin'      => [],
];

$models = [
    'storefront' => [],
    'admin'      => [],
];

$templates = [
    'storefront' => [
        'blocks/banner_block_content.tpl',
        'blocks/listing_block/popular_brands_content_bottom.tpl',
        'blocks/product_cell_grid.tpl',
        'blocks/product_list.tpl',
        'blocks/fast_checkout_cart_btn.tpl',
    ],
    'admin'      => [],
];

$languages = [
    'storefront' => [],
    'admin'      => [
        'english/novator/novator',
    ],
];

