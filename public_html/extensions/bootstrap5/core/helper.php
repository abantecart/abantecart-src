<?php
// recursive function!
function renderSFMenu($menuItems, $level = 0, $parentId = '', $options = [ ])
{
    $logged = Registry::getInstance()->get('customer')->isLogged();
    $output = '';
    $menuItems = (array) $menuItems;
    if (!$menuItems) {
        return '';
    }
    $idKey = $options['id_key_name'] ?: 'id';

    if ($level == 0) {
        $output .= '<ul '.($options['top_level']['attr'] ?: 'class="navbar-nav"').'>';
    } else {
        $output .= '<ul class="dropdown-menu '.($level > 1 ? 'dropdown-submenu' : '').'" aria-labelledby="'.$parentId.'" '.$options['submenu_level']['attr'].'>';
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
        $item_title = $item['text'] ?: $item['title'] ?: $item['name'];
        $hasChild = (bool) $item['children'];
        $output .= '<li class="dropup">';
        //check icon rl type html, image or none.
        $rl_id = $item['icon'] ? : $item['icon_rl_id'];
        $icon = '';
        if ($rl_id) {
            $resource = $ar->getResource($rl_id);
            if ($resource['resource_path'] && is_file(DIR_RESOURCE.'image/'.$resource['resource_path'])) {
                $icon = '<img class="menu_image" src="'.HTTPS_DIR_RESOURCE.'image/'.$resource['resource_path'].'" />&nbsp;';
            } elseif ($resource['resource_code']) {
                $icon = $resource['resource_code'];
            }
        }

        if ($hasChild) {
            $id = 'menu_'.$item[$idKey];
            $css = 'dropdown-toggle text-nowrap '. ($level ? 'dropdown-item ' : 'nav-link ');
            $output .= '<a id="'.$id.'" 
                            href="#" role="button" 
                            class="'.$css.'" 
                            data-bs-toggle="dropdown" aria-expanded="false">';
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
            $css = $level ? "dropdown-item" : "nav-item nav-link " .'text-nowrap ';
            $output .= '<a href="'.$item['href'].'" class="'.$css.'">'.$icon.$item_title.'</a>';
            if($item['thumb']){
                $popover = '<img class="menu_image" src="'.$item['thumb'].'" />&nbsp;';
            }
        }
        $output .= '</li>';
    }

    $output .= "</ul>\n";
    return $output;
}
