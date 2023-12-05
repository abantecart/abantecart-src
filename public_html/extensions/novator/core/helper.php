<?php

function renderAllCategoriesSFMenuNv(array $menuItems, $level = 0, $parentId = '', $options = [ ]){

    $menuItems = (array) $menuItems;
    if (!$menuItems || $level>1 //only 2 levels of category tree
    ) {
        return '';
    }
    $idKey = $options['id_key_name'] ?: 'id';

    if($level==0) {
        $output = '<div class="dropdown list-unstyled category-links" aria-labelledby="' . $parentId . '" ' . $options['submenu_level']['attr'] . '>';
    }else{
        $output = '<div class="dropdown-menu dropdown-submenu list-unstyled category-sub-links" aria-labelledby="' . $parentId . '" ' . $options['submenu_level']['attr'] . '>';
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
            $output .= '<div class="dropend d-flex flex-column col-6">
                            <a id="'.$id.'" href="'.$item['href'].'" 
                                class="dropdown-toggle text-nowrap '. ($level ? 'dropdown-item ' : '').'" 
                                data-bs-toggle="dropdown" data-bs-target="dropdown"
                                aria-expanded="false" data-bs-offset="10,20">'
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

            $output .= call_user_func_array('renderAllCategoriesSFMenuNv',$params).'</div>';
        } else {
            $output .= '<a href="'.$item['href'].'" class="'.$css.'" >'.$icon.$item_title.'</a>';
        }
    }

    $output .= "</div>\n";
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
                $output .= '&nbsp; <i class="fa fa-caret-down"></i>';
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
        $output .= '<i class="fa-star '.($i<=$value ? 'fa-solid' : 'fa-regular').'"></i>';
        $i++;
    }
    return $output.'</div>';
}

function noRatingStarsNv($text){
    $i = 1;
    $output = '<div title="'.htmlspecialchars($text, ENT_QUOTES, 'UTF-8').'">';
    while($i < 6){
        $output .= '<i class="fa-star fa-regular"></i>';
        $i++;
    }
    return $output.'</div>';
}
