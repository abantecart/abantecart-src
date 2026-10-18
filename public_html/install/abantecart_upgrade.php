<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

/** @var APackageManager $this */

// SEO admin menu: parent item with "URL Aliases" and "Redirects" submenu
$menu = new AMenu('admin');
$rm = new AResourceManager();
$rm->setType('image');
$languageId = $this->language->getLanguageID() ?: 1;

$addMenuIcon = function (string $name, string $code) use ($rm, $languageId): int {
    return (int)$rm->addResource(
        [
            'resource_code' => $code,
            'name'          => [$languageId => $name],
            'title'         => [$languageId => ''],
            'description'   => [$languageId => ''],
        ]
    );
};

if (!$menu->getMenuItem('seo')) {
    // free the 4th place in the main menu, same as in the fresh install.
    // read items before any update: AMenu::getMenuItem() does not see other items after updateMenuItem()
    $mainItems = [];
    foreach (['extension', 'system', 'reports', 'help'] as $itemId) {
        $mainItems[$itemId] = $menu->getMenuItem($itemId);
    }
    foreach ($mainItems as $itemId => $item) {
        if ($item && !$item['parent_id'] && (int)$item['sort_order'] >= 4) {
            $menu->updateMenuItem($itemId, ['sort_order' => (int)$item['sort_order'] + 1]);
        }
    }
    $menu = new AMenu('admin');
    $menu->insertMenuItem(
        [
            'item_id'         => 'seo',
            'parent_id'       => '',
            'item_text'       => 'text_seo',
            'item_url'        => 'tool/seo_url',
            'item_icon_rl_id' => $addMenuIcon('Icon Menu Seo', '<i class="fa fa-rss"></i>&nbsp;'),
            'item_type'       => 'core',
            'sort_order'      => 4,
        ]
    );
}

$seoSubmenu = [
    'seo_url'  => [
        'item_text'  => 'text_seo_url',
        'item_url'   => 'tool/seo_url',
        'icon_name'  => 'Icon Menu Seo Url',
        'icon_code'  => '<i class="fa fa-key"></i>&nbsp;',
        'sort_order' => 1,
    ],
    'redirect' => [
        'item_text'  => 'text_redirect',
        'item_url'   => 'tool/redirect',
        'icon_name'  => 'Icon Menu Redirect',
        'icon_code'  => '<i class="fa fa-random"></i>&nbsp;',
        'sort_order' => 2,
    ],
];
foreach ($seoSubmenu as $itemId => $item) {
    if ($menu->getMenuItem($itemId)) {
        continue;
    }
    $menu->insertMenuItem(
        [
            'item_id'         => $itemId,
            'parent_id'       => 'seo',
            'item_text'       => $item['item_text'],
            'item_url'        => $item['item_url'],
            'item_icon_rl_id' => $addMenuIcon($item['icon_name'], $item['icon_code']),
            'item_type'       => 'core',
            'sort_order'      => $item['sort_order'],
        ]
    );
}
