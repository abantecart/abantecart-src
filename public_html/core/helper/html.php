<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

function renderStoreMenu($menu, $level = 0)
{
    $menu = (array)$menu;
    $result = '';
    if ($level) {
        $result .= "<ul class='dropdown-menu'>\r\n";
    }
    $registry = Registry::getInstance();
    $logged = $registry->get('customer')->isLogged();

    foreach ($menu as $item) {
        if ( ($logged && $item['id'] == 'login') || (!$logged && $item['id'] == 'logout') ) {
            continue;
        }

        $id = $item['id'] ? ' id="menu_'.$item['id'].'" ' : '';
        if ($level != 0) {
            if (!$item['children']) {
                $style = '';
            } else {
                $style = $item['icon']
                    ? ' class="parent" style="background-image:none;" '
                    : ' class="parent menu_'.$item['id'].'" ';
            }
        } else {
            $style = $item['icon']
                ? ' class="top" style="background-image:none;" '
                : ' class="top menu_'.$item['id'].'" ';
        }

        $href = $item['href'] ? ' href="'.$item['href'].'" ' : '';
        $result .= '<li'.$id.' class="dropdown">';
        $result .= '<a'.$style.$href.'>';
        $result .= $item['icon'] ? '<img src="'.HTTPS_DIR_RESOURCE.$item['icon'].'" alt="" />' : '';
        $result .= '<span>'.$item['text'].'</span></a>';

        if (!empty($item['children'])) {
            $result .= "\r\n".renderStoreMenu($item['children'], $level + 1);
        }
        $result .= "</li>\r\n";
    }
    if ($level) {
        $result .= "</ul>\r\n";
    }
    return $result;
}

function buildStoreFrontMenuTree($menu_array, $level = 0)
{
    $menu_array = (array)$menu_array;
    if (!$menu_array) {
        return '';
    }
    $result = '';
    //for submenus build new UL node
    if ($level > 0) {
        $result .= "<ul class='sub_menu dropdown-menu'>\r\n";
    }
    $registry = Registry::getInstance();
    $logged = $registry->get('customer')->isLogged();

    $ar = new AResource('image');
    foreach ($menu_array as $item) {
        if (($logged && $item['id'] == 'login') || (!$logged && $item['id'] == 'logout')
        ) {
            continue;
        }

        //build appropriate menu id and classes for css controll
        $id = (empty($item['id']) ? '' : ' data-id="menu_'.$item['id'].'" '); // li ID
        if ($level != 0) {
            if (empty($item['children'])) {
                $style = $item['icon'] ? ' class="top nobackground"' : ' class="sub menu_'.$item['id'].'" ';
            } else {
                $style = $item['icon'] ? ' class="parent nobackground" ' : ' class="parent menu_'.$item['id'].'" ';
            }
        } else {
            $style = $item['icon'] ? ' class="top nobackground"' : ' class="top menu_'.$item['id'].'" ';
        }
        $href = empty($item['href']) ? '' : ' href="'.$item['href'].'" ';
        //construct HTML
        $current = '';
        if ($item['current']) {
            $current = 'current';
        }
        $result .= '<li '.$id.' class="dropdown '.$current.'">';
        $result .= '<a '.$style.$href.'>';

        //check icon rl type html, image or none.
        $rl_id = $item['icon'] ?: $item['icon_rl_id'];
        if ($rl_id) {
            $resource = $ar->getResource($rl_id);
            if ($resource['resource_path'] && is_file(DIR_RESOURCE.'image/'.$resource['resource_path'])) {
                $result .= '<img class="menu_image" src="'.HTTPS_DIR_RESOURCE.'image/'.$resource['resource_path'].'" />';
            } elseif ($resource['resource_code']) {
                $result .= $resource['resource_code'];
            }
        }

        $result .= '<span class="menu_text">'.$item['text'].'</span></a>';

        //if children build inner child tree
        if (!empty($item['children'])) {
            $result .= "\r\n".buildStoreFrontMenuTree($item['children'], $level + 1);
        }
        $result .= "</li>\r\n";
    }
    if ($level > 0) {
        $result .= "</ul>\r\n";
    }
    return $result;
}

/**
 *
 * @param array $menu
 * @param int|null $level
 * @param string|null $currentRt
 * @return string
 */
function renderAdminMenu(array $menu, ?int $level = 0, ?string $currentRt = '')
{
    $result = '';
    if ($level) {
        $result .= '<ul class="children child'.$level.'">'."\r\n";
    }
    foreach ($menu as $item) {
        $id = $item['id'] ? ' id="menu_'.$item['id'].'" ' : '';
        $class = $level != 0 ? !$item['children'] ? '' : ' class="parent" ' : ' class="top" ';
        $href = $item['href'] ? ' href="'.$item['href'].'" ' : '';
        $onclick = $item['onclick'] ? ' onclick="'.$item['onclick'].'" ' : '';

        $childCssClass = "level".$level;
        if (!empty($item['children'])) {
            $childCssClass .= ' nav-parent ';
        }
        if ($item['rt'] && $currentRt == $item['rt']) {
            $childCssClass .= ' active ';
        }
        if ($childCssClass) {
            $childCssClass = ' class="'.$childCssClass.'" ';
        }

        $result .= '<li'.$id.$childCssClass.'>';
        $result .= '<a '.$class.$href.$onclick.'>';

        //check icon rl type html, image or none. 
        if (isHtml($item['icon'])) {
            $result .= $item['icon'];
        } else {
            if ($item['icon']) {
                $result .= '<img class="menu_image" src="'.HTTPS_DIR_RESOURCE.$item['icon'].'" alt="" />';
            } else {
                $result .= '<i class="fa fa-caret-right"></i> ';
            }
        }
        $result .= '<span class="menu_text">'.$item['text'].'</span></a>';
        //if children build inner child trees
        if ($item['children']) {
            $result .= "\r\n".renderAdminMenu((array)$item['children'], $level + 1, $currentRt);
        }
        $result .= "</li>\r\n";
    }
    if ($level) {
        $result .= "</ul>\r\n";
    }
    return $result;
}