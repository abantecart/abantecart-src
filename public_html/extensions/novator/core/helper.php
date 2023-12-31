<?php

function renderAllCategoriesSFMenuNv(array $menuItems, $options = [ ]){

    $menuItems = (array) $menuItems;
    if (!$menuItems ) {
        return '';
    }

    $idKey = $options['id_key_name'] ?: 'id';

    $output = '
                                    <div class="col-3">
                                        <ul class="nav nav-tabs flex-column category-links mt-0" id="myTab" role="tablist">';

    $children = '<div class="col-9">
                    <div class="tab-content" id="myTabContent">';

    $cards = '<div class="col-5">
                <div class="row h-100 g-4">
                    <div class="col-6">
                        <div class="card bg-secondary h-100">
                            <div class="card-body" style="height: 400px">
                                <div class="bulb-icon d-flex align-items-center justify-content-center">

                                </div>
                                <p class="mb-0 text-white">Choose some<br>treatment and you’ll<br>see the solutions!</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-secondary h-100">
                            <div class="card-body" style="height: 400px">
                                <div class="bulb-icon d-flex align-items-center justify-content-center"></div>
                                <p class="mb-0 text-white">Choose some<br>treatment and you’ll<br>see the solutions!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';

    //$ar = new AResource('image');
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

        $hasChild = (bool) $item['children'];
        if ($hasChild) {
            $children .= '<div class="tab-pane fade" id="drp-'.$item['category_id'].'-tab-pane" role="tabpanel"
                                aria-labelledby="drp-'.$item['category_id'].'-tab">
                                <div class="d-flex flex-nowrap h-100">
                                <div class="col-4">
                                    <ul class="list-unstyled category-sub-links">';
            foreach($item['children'] as $child){
                $childTitle = $child['text'] ?: $child['title'] ?: $child['name'];
                $children .= '<li><a href="'.$child['href'].'">'.$childTitle.'</a></li>';
            }
            $children .= '</ul></div>'.$cards.'</div></div>';

        }
    }



    $output .= '</ul></div>'.$children.'</div></div>';


    return $output;
}


function prepareNVCatItems($items)
{
    foreach ($items as &$cat){
        unset($cat['thumb']);
        if($cat['level'] == 0){
            unset($cat['icon']);
        }
        if($cat['children']){
            $cat['children'] = prepareNVCatItems($cat['children']);
        }
    }
    return $items;
}

function renderCategoryNavbarSFMenuNv(array $menuItems, $level = 0, $parentId = '', $options = [ ]){

    $menuItems = (array) $menuItems;
    if (!$menuItems || $level>1 //only 2 levels of category tree
    ) {
        return '';
    }
    $idKey = $options['id_key_name'] ?: 'id';

    if($level==0) {
        $output = '<div class="dropdown mega-menu me-3 me-sm-0 mb-3 mb-lg-0">';
    }else{
        $output = '<div class="dropdown-menu list-unstyled category-sub-links" aria-labelledby="' . $parentId . '" ' . $options['submenu_level']['attr'] . '>';
    }


    //$ar = new AResource('image');
    foreach ($menuItems as $i => $item) {

        if (!is_array($item)) {
            unset($menuItems[$i]);
            continue;
        }
        $item_title = $item['text'] ?: $item['title'] ?: $item['name'];

        $hasChild = (bool) $item['children'];
        if ($hasChild) {
            $id = 'menu_'.$item[$idKey];
            $css = 'dropdown-toggle text-nowrap '. ($level ? 'dropdown-item ' : '');
            $output .= '<a id="'.$id.'" href="'.$item['href'].'" 
                                class="nav-link dropdown-toggle text-nowrap " 
                                data-bs-toggle="dropdown" data-bs-target="dropdown"
                                aria-expanded="false">'
                . $item_title. '</a>';

            $params = [
                'menuItems' => $item['children'],
                'level' => $level + 1,
                'parentId' => $id,
                'options' => [
                    'id_key_name' => $idKey
                ]
            ];

            // for case when pass options into deep of menu
            if($options['pass_options_recursively']){
                $params['options'] = array_merge($params['options'], $options['submenu_options']);
            }

            $output .= call_user_func_array('renderCategoryNavbarSFMenuNv',$params);
        } else {
            $output .= '<a href="'.$item['href'].'" class="'.$css.'" >'.$icon.$item_title.'</a>';
        }
    }

    $output .= "</div>\n";
    return $output;
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
        // Hello AbanteCart team you need to check here Starts
        $output .= '<div '.($options['top_level']['attr'] ?: 'class="navbar-nav ms-auto me-auto mb-2 mb-lg-0 align-items-start"').'>';
        // Hello AbanteCart team you need to check here ends
    } else {
        $output .= '<div class="dropdown-menu dropdown-mega-menu'.($level > 1 ? 'dropdown-submenu' : '').'" aria-labelledby="'.$parentId.'" '.$options['submenu_level']['attr'].'>';
    }

    $ar = new AResource('image');
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
        $item_title = '<span class="menu-img-captipn">'.($item['text'] ?: $item['title'] ?: $item['name']).'</span>';
        $hasChild = (bool) $item['children'];
        $output .= '<div class="dropdown mega-menu me-3 me-sm-0 mb-3 mb-lg-0">';
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
            $output .= '<a id="'.$id.'" 
                            href="'.$item['href'].'" 
                            class="'.$css.'" 
                            data-bs-toggle="dropdown" 
                            data-bs-target="dropdown" 
                            aria-expanded="false">';
            $output .= $icon.$item_title;
            if(!isset($options['without_caret'])) {
                $output .= '&nbsp; <i class="bi bi-caret-down"></i>';
            }
            $output .= '</a>';

            $params = [
                'menuItems' => $item['children'],
                'level' => $level + 1,
                'parentId' => $id,
                'options' => [
                    'id_key_name' => $idKey
                ]
            ];

            // for case when pass options into deep of menu
            if($options['pass_options_recursively']){
                $params['options'] = array_merge($params['options'], $options['submenu_options']);
            }

            $output .= "\r\n".call_user_func_array('renderSFMenu',$params);
        } else {
            $css = $level ? "dropdown-item" : " " .'text-nowrap nav-link';
            $popoverAttr = $item['thumb'] ? 'data-bs-toggle="popover" 
                        data-bs-content="<img src=&quot;'.$item['thumb'].'&quot;>" 
                        data-bs-html="true" data-bs-offset="5,5"
                        data-bs-boundary="window" data-bs-placement="right" data-bs-trigger="hover"'
                : '';
            $output .= '<a href="'.$item['href'].'" class="'.$css.'" '.$popoverAttr.'>'.$icon.$item_title.'</a>';
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
        $output .= '<i class="bi '.($i<=$value ? 'bi-star-fill' : 'bi-star').'"></i>';
        $i++;
    }
    return $output.'</div>';
}

function noRatingStarsNv($text){
    $i = 1;
    $output = '<div title="'.htmlspecialchars($text, ENT_QUOTES, 'UTF-8').'">';
    while($i < 6){
        $output .= '<i class="bi bi-star"></i>';
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
