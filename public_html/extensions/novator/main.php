<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

require_once('core/novator_hook.php');

$controllers = [
    'storefront' => [
        'blocks/category_slides',
        'blocks/category_filter',
        'blocks/novator_header_menu'
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
        /** @see ./storefront/view/novator/template/blocks/novator_header_menu.tpl */
        'blocks/novator_header_menu.tpl',
        /** @see ./storefront/view/novator/template/blocks/novator_header_bottom_menu.tpl */
        'blocks/novator_header_bottom_menu.tpl',
    ],
    'admin'      => [],
];

$languages = [
    'storefront' => [],
    'admin'      => [
        'english/novator/novator',
    ],
];