<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

require_once('core/novator_hook.php');

$controllers = [
    'storefront' => [
        'blocks/category_slides',
        'blocks/mega_menu'
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
        /** @see ./storefront/view/novator/template/blocks/mega_menu_header.tpl */
        'blocks/mega_menu_header.tpl',
        /** @see ./storefront/view/novator/template/blocks/mega_menu_header_bottom.tpl */
        'blocks/mega_menu_header_bottom.tpl',
    ],
    'admin'      => [],
];

$languages = [
    'storefront' => [],
    'admin'      => [
        'english/novator/novator',
    ],
];