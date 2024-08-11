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

function renderAllCategoriesSFMenuNv(array $menuItems, $options = [ ])
{

    $menuItems = (array) $menuItems;
    if (!$menuItems ) {
        return '';
    }

    $output = '<div class="col-3">
                <ul class="nav nav-tabs flex-column category-links mt-0" role="tablist">';

    $children = '<div class="col-9">
                    <div class="tab-content" id="megaMenuContent">';

    foreach ($menuItems as $i => $item) {
        if (!is_array($item)) {
            unset($menuItems[$i]);
            continue;
        }
        $item_title = $item['text'] ?: $item['title'] ?: $item['name'];
        $output .= '<li class="nav-item" role="presentation">
                        <a href="'.$item['href'].'" class="m-0 nav-link" id="drp-'.$item['category_id'].'-tab"
                        data-bs-toggle="tab" data-bs-target="#drp-'.$item['category_id'].'-tab-pane" type="button" role="tab"
                        aria-controls="drp-'.$item['category_id'].'-tab-pane" aria-selected="true">'.$item_title.'</a></li>';
        $cards = renderFeaturedProductsCards( $item );
        $hasChild = (bool) $item['children'];
        if ($hasChild) {
            $children .= '<div class="tab-pane fade '.(!$i ? ' active show' : '').'" id="drp-'.$item['category_id'].'-tab-pane" role="tabpanel"
                                aria-labelledby="drp-'.$item['category_id'].'-tab" data-category-id="'.$item['category_id'].'">
                                <div class="container d-flex flex-nowrap align-items-stretch">
                                <div class="col-4">
                                    <ul class="list-unstyled category-sub-links">';
            foreach($item['children'] as $child){
                $childTitle = $child['text'] ?: $child['title'] ?: $child['name'];
                $children .= '<li><a href="'.$child['href'].'"
                                    class="subcategory-link"
                                    id="child-'.$child['category_id'].'-tab">'.$childTitle.'</a></li>';

                $cards .= renderFeaturedProductsCards( $child );
            }
            $children .= '</ul></div>'.$cards.'</div></div>';
        }
    }
    $output .= '</ul></div>'.$children.'</div></div>';
    return $output;
}

function renderFeaturedProductsCards( $item )
{
    $html = Registry::getInstance()->get('html');
    $cards = '<div id="card-'.$item['category_id'].'-tab-pane" class="container featured-products col-8 tab-pane fade" role="tabpanel" >
                    <div class="row g-4">';
    $k=0;
    while( $k<2 ){
        $product = $item['featured_products'][$k];
        $cards .= '<div class="col-6 ">
                        <a href="'.$html->getSEOURL('product/product','&product_id='.$product['product_id']).'">
                            <div class="card-body d-flex rounded" style="background-image: url('.$product['thumbnail']['thumb_url'].');">
                                <div class="d-flex flex-wrap w-100 rounded align-items-end justify-content-center bg-secondary bg-opacity-50 text-white p-3">
                                    <div class="w-100 mb-1 fs-5">'.$product['name'].'</div>
                                    <div class="w-100 mb-0 fs-6">'.($product['blurb'] ? mb_substr($product['blurb'], 0, 150).'...' : '').'</div>
                                </div>
                            </div>                            
                        </a>
                    </div>';
        $k++;
    }
    $cards .= '</div></div>';
    return $cards;
}

function prepareNVCatItems($items)
{
    foreach ($items as &$cat){
        unset($cat['thumb']);
        if($cat['children']){
            $cat['children'] = prepareNVCatItems($cat['children']);
        }
    }
    return $items;
}

// recursive function!

/**
 * @param array $menuItems - [ 'id', 'text or ']
 * @param int $level
 * @param string $parentId
 * @param array $options - [
 *                          'id_key_name' => {unique item text id}
 *                          'submenu_options' => option array that will be used entire submenu
 *                          ]
 *
 * @return string
 * @throws AException
 */
function renderSFMenuNv($menuItems, $level = 0, $parentId = '', $options = [ ])
{
    $logged = Registry::getInstance()->get('customer')->isLogged();
    $output = '';
    $menuItems = (array) $menuItems;
    if (!$menuItems) {
        return '';
    }
    $idKey = $options['id_key_name'] ?: 'id';

    if ($level == 0) {
        $output .= '<div '.($options['top_level']['attr'] ?: 'class="navbar-nav ms-auto me-auto mb-2 mb-lg-0 align-items-start flex-wrap"').'>';
    } else {
        $output .= '<div class="dropdown-menu position-absolute '.($level > 1 ? 'dropdown-submenu' : '')
            .'" aria-labelledby="'.$parentId.'" '.$options['submenu_level']['attr'].'>';
    }

    foreach ($menuItems as $i => $item) {
        if ($item[$idKey] == 'home' || !is_array($item)) {
            unset($menuItems[$i]);
            continue;
        }

        if (($logged && $item[$idKey] == 'login')
            || (!$logged && $item[$idKey] == 'logout')
        ) {
            continue;
        }
        $item_title = '<span class="menu-img-caption">'.($item['text'] ?: $item['title'] ?: $item['name']).'</span>';
        $hasChild = (bool) $item['children'];
        $output .= '<div class="dropdown me-3 me-sm-0 mb-3 mb-lg-0 '. ($hasChild ? 'with-children ' : '').'" >';
        //check icon rl type html, image or none.
        $rlId = $item['icon'] ? : $item['icon_rl_id'];
        $icon = renderMenuItemIconNv($item, $rlId);

        if ($hasChild) {
            $id = 'menu_'.$item[$idKey];
            $css = 'dropdown-toggle text-nowrap mb-3 mb-md-0 nav-link '. ($level ? 'dropdown-item ' : '');
            $output .= '<a id="'.$id.'" href="'.$item['href'].'" class="'.$css.'" data-bs-toggle="dropdown" data-bs-target="dropdown" aria-expanded="false">'
                        . $icon.$item_title.'</a>';
            $chOptions = [ 'id_key_name' => $idKey ];

            // for case when pass options into deep of menu
            if($options['pass_options_recursively']){
                $chOptions = array_merge($chOptions, $options['submenu_options']);
            }
            if($item['category']){
                $output .= "\r\n".renderCategorySubMenuNV( $item['children'], $level + 1, $id, $chOptions );
            } else {
                $output .= "\r\n".renderSFMenuNv( $item['children'], $level + 1, $id, $chOptions );
            }

        } else {
            $css = $level ? "dropdown-item" : " " .'text-nowrap nav-link';
            $popoverAttr = $item['thumb']
                ? 'data-bs-toggle="popover" data-bs-content="<img src=&quot;'.$item['thumb'].'&quot;>" '
                   .' data-bs-html="true" data-bs-offset="5,5" data-bs-boundary="window"'
                   .' data-bs-placement="right" data-bs-trigger="hover"'
                : '';
            $output .= '<a href="'.$item['href'].'" class="'.$css.'" '.$popoverAttr.'>'.$icon.$item_title.'</a>';
        }
        $output .= '</div>';
    }
    $output .= "</div>\n";

    return $output;
}

/**
 * @param array $item
 * @param int $resourceId
 * @param string $imgCssClass
 * @return string
 * @throws AException
 */
function renderMenuItemIconNv($item, $resourceId, $imgCssClass = 'img-fluid')
{
    $icon = '';
    if ($resourceId) {
        $ar = new AResource('image');
        $resource = $ar->getResource($resourceId);
        if ($resource['resource_path'] && is_file(DIR_RESOURCE.'image/'.$resource['resource_path'])) {
            $icon = '<img class="'.$imgCssClass.'" src="resources/image/'.$resource['resource_path'].'" />';
        } elseif ($resource['resource_code']) {
            $icon = $resource['resource_code'];
        }
    } elseif ( $item['icon_html'] ){
        $icon = $item['icon_html'];
    }
    return $icon;
}

function renderCategorySubMenuNV($menuItems, $level = 0, $parentId = '', $options = [ ])
{
    $output = '';
    $menuItems = (array) $menuItems;
    if (!$menuItems) {
        return '';
    }
    $idKey = $options['id_key_name'] ?: 'id';

    $output .= '<div class="dropdown-menu " aria-labelledby="'.$parentId.'" '.$options['submenu_level']['attr'].'>';
    $ar = new AResource('image');
    foreach ($menuItems as $i => $item) {

        $item_title = '<span class="menu-img-caption">'.($item['text'] ?: $item['title'] ?: $item['name']).'</span>';
        $hasChild = (bool) $item['children'];
        $output .= '<div class="me-3 me-sm-0 mb-3 mb-lg-0">';
        //check icon rl type html, image or none.
        $rl_id = $item['icon'] ? : $item['icon_rl_id'];
        $icon = '';
        if ($rl_id) {
            $resource = $ar->getResource($rl_id);
            if ($resource['resource_path'] && is_file(DIR_RESOURCE.'image/'.$resource['resource_path'])) {
                $icon = '<img class="img-fluid" src="resources/image/'.$resource['resource_path'].'" />';
            } elseif ($resource['resource_code']) {
                $icon = $resource['resource_code'];
            }
        }elseif( $item['icon_html'] ){
            $icon = $item['icon_html'];
        }

        if ($hasChild) {
            $id = 'menu_'.$item[$idKey];
            $css = 'dropdown-toggle text-nowrap mb-3 mb-md-0 nav-link'. ($level ? 'dropdown-item ' : '');
            $output .= '<a id="'.$id.'" href="'.$item['href'].'" 
                           class="'.$css.'" data-bs-toggle="dropdown" data-bs-target="dropdown" aria-expanded="false">';
            $output .= $icon.$item_title;
            $output .= '</a>';
            $chOptions = [ 'id_key_name' => $idKey ];

            // for case when pass options into deep of menu
            if($options['pass_options_recursively']){
                $chOptions = array_merge($chOptions, $options['submenu_options']);
            }
             $output .= "\r\n".renderCategorySubMenuNV( $item['children'], $level + 1, $id, $chOptions );
        } else {
            $css = $level ? "dropdown-item" : " " .'text-nowrap nav-link';
            $output .= '<a href="'.$item['href'].'" class="'.$css.'">'.$icon.$item_title.'</a>';
        }
        $output .= '</div>';
    }
    $output .= "</div>\n";

    return $output;
}

function renderRatingStarsNv($value, $text){
    if(!$value){
        return '';
    }
    $i = 1;
    $output = '<div title="'.htmlspecialchars($text, ENT_QUOTES, 'UTF-8').'">';
    while($i < 6){
        $output .= '<i class="bi '.($i<=$value ? 'bi-star-fill' : 'bi-star').' text-warning me-1"></i>';
        $i++;
    }
    return $output.'</div>';
}

function noRatingStarsNv($text){
    $i = 1;
    $output = '<div title="'.htmlspecialchars($text, ENT_QUOTES, 'UTF-8').'">';
    while($i < 6){
        $output .= '<i class="bi bi-star text-warning"></i>';
        $i++;
    }
    return $output.'</div>';
}

function renderProductRatingStars( int $productId){
    if(!$productId){
        return false;
    }
    /** @var ModelCatalogReview $mdl */
    $mdl = Registry::getInstance()->get('load')->load->model('catalog/review');
    $ratings = $mdl->getProductAVGRatings($productId);
    $totalRate = array_sum($ratings);
    if(!$totalRate){
        return '';
    }

    $output = '';
    foreach($ratings as $stars => $count){
        $prc = round(($count*100/$totalRate));
        $output .= '<div class="row align-items-center my-2">
        <div class="col">
            <div class="progress" style="height: 5px">
                <div class="progress-bar bg-success"
                     style="width: '.$prc.'%"></div>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex align-items-center gap-1 text-warning">';
        $i = 1;
        while ($i < 6) {
            $output .= '<i class="fa-star ' . ($i <= $stars ? 'fa-solid' : 'fa-regular') . '"></i>';
            $i++;
        }
        $output .= '      <p class="mb-0 text-primary text-end" style="width: 50px;">'.$prc.'%</p></div>
        </div>
    </div>';
    }

    return $output;
}

function renderFilterCategoryTreeNV($tree, $level = 0, int|array|null $currentId = 0, ?array $extra = [])
{
    if(!$tree || !is_array($tree)){
        return false;
    }
    $output = '';
    foreach($tree as $cat){
        $cat['name'] = ($level ? ' - ' : '') .$cat['name'];
        $checked = in_array($cat['category_id'], (array)$currentId);
        $checkedChildren = 0;
        foreach((array)$cat['children'] as $ch){
            if(in_array($ch['category_id'], (array)$currentId)){
                $checkedChildren++;
            }
        }
        if( ($extra['lock_one_category'] || $checkedChildren > 1) && $checked) {
            $readonly = 'onclick="return false"';
        }
        // when show only parent categories need to pass path parameter by click.
        // It will show preselected parent with children
        if($extra['root_level']){
            $fldName = 'path';
            $fldValue = $cat['path'];
        }else{
            $fldName = 'category_id[]';
            $fldValue = $cat['category_id'];
        }

        $output .=
            '<div class="row g-3 align-items-center my-0">
                  <div class="d-flex flex-nowrap m-0">
                    <input id="filter_cat'.$cat['category_id'].'"
                           class="form-check-input product-filter me-2" 
                           type="checkbox" name="'.$fldName.'" value="'.$fldValue.'" '
                        .($checked ? 'checked' : '') . ' ' . $readonly.'>
                    <label for="filter_cat'.$cat['category_id'].'" 
                        class="w-100 ms-'.$level.' link '.($checked ? 'fw-bolder link-primary' : 'link-secondary').' d-block ms-'.$level.'" >'. str_repeat('&nbsp;', $level ).$cat['name'].'
                        '. ( $cat['product_count'] ? '<span class="float-end">('. $cat['product_count'].')</span>' : '').'
                    </label>
                </div>
            </div>';

        if(!$cat['children']){ continue; }
        $output .= renderFilterCategoryTreeNV($cat['children'], $level+1, $currentId, $extra);
    }
    return $output;
}