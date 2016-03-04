<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>
  
 UPGRADE NOTE: 
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.  
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

//Possibly legacy and only for old template. Remove in 1.2
function renderStoreMenu( $menu, $level = 0 ){
	$menu = (array)$menu;
    $result = '';
    if ( $level ) $result .= "<ul class='dropdown-menu'>\r\n";
	$registry = Registry::getInstance();
	$logged = $registry->get('customer')->isLogged();

    foreach( $menu as $item ) {
		if(($logged && $item['id']=='login')
			||	(!$logged && $item['id']=='logout')){
			continue;
		}

        $id = ( empty($item['id']) ? '' : ' id="menu_'.$item['id'].'" ' ); // li ID

		if($level != 0){
			if(empty($item['children'])){
				$class='';
			}else{
				$class = $item['icon']? ' class="parent" style="background-image:none;" ' : ' class="parent menu_'.$item['id'].'" ';
			}
		}else{
			$class = $item['icon'] ? ' class="top" style="background-image:none;" ' : ' class="top menu_'.$item['id'].'" ';
		}

		$href = empty($item['href']) ? '' : ' href="'.$item['href'].'" '; //a href

        $result .= '<li' . $id . ' class="dropdown">';
        $result .= '<a' . $class . $href . '>';
	    $result .= $item['icon'] ? '<img src="'. HTTPS_DIR_RESOURCE . $item['icon'].'" alt="" />' : '';
		$result .= '<span>' . $item['text'] . '</span></a>';

        if ( !empty($item['children']) ) $result .= "\r\n" . renderStoreMenu($item['children'], $level+1) ;
        $result .= "</li>\r\n";
    }
    if ( $level ) $result .= "</ul>\r\n";
    return $result;
}


//New menu tree builder (1.2+) 
function buildStoreFrontMenuTree( $menu_array, $level = 0 ){
    $menu_array = (array)$menu_array;
    if (!$menu_array) {
    	return '';
    }
    $result = '';
    //for submenus build new UL node
    if ( $level > 0 ) $result .= "<ul class='sub_menu dropdown-menu'>\r\n";
    $registry = Registry::getInstance();
    $logged = $registry->get('customer')->isLogged();

	$ar = new AResource('image');
    foreach( $menu_array as $item ) {
    	if(($logged && $item['id']=='login')
    		||	(!$logged && $item['id']=='logout')){
    		continue;
    	}

		//build appropriate menu id and classes for css controll
    	$id = ( empty($item['id']) ? '' : ' data-id="menu_'.$item['id'].'" ' ); // li ID
    	if($level != 0){
    		if(empty($item['children'])){
    			$class = $item['icon'] ? ' class="top nobackground"' : ' class="sub menu_'.$item['id'].'" ';
    		}else{
    			$class = $item['icon']? ' class="parent nobackground" ' : ' class="parent menu_'.$item['id'].'" ';
    		}
    	}else{
    		$class = $item['icon'] ? ' class="top nobackground"' : ' class="top menu_'.$item['id'].'" ';
    	}
    	$href = empty($item['href']) ? '' : ' href="'.$item['href'].'" ';
    	//construct HTML
    	$current = ''; 
    	if ($item['current']) {
    		$current = 'current'; 
    	}
    	$result .= '<li ' . $id . ' class="dropdown '.$current.'">';
    	$result .= '<a ' . $class . $href . '>';
    	
    	//check icon rl type html, image or none.
		$rl_id = $item['icon_rl_id'];
		if($rl_id){
			$resource = $ar->getResource($rl_id);
			if($resource['resource_path'] && is_file(DIR_RESOURCE . 'image/'.$resource['resource_path'])){
				$result .= '<img class="menu_image" src="'. HTTPS_DIR_RESOURCE . 'image/'.$resource['resource_path'].'" alt="" />';
			}elseif($resource['resource_code']){
				$result .= $resource['resource_code'];
			}
		}

    	$result .= '<span class="menu_text">' . $item['text'] . '</span></a>';

		//if children build inner clild tree
    	if ( !empty($item['children']) ) {
    		$result .= "\r\n" . buildStoreFrontMenuTree($item['children'], $level+1);
    	}
    	$result .= "</li>\r\n";
    }
    if ( $level > 0 ) $result .= "</ul>\r\n";
    return $result;
}


function renderAdminMenu( $menu, $level = 0, $current_rt = ''){
    $result = '';
    if ( $level ) $result .= "<ul class=\"children child$level\">\r\n";
    foreach( $menu as $item ) {
        $id = ( empty($item['id']) ? '' : ' id="menu_'.$item['id'].'" ' ); // li ID
        $class = $level != 0 ? empty($item['children']) ? '' : ' class="parent" ' : ' class="top" '; //a class
        $href = empty($item['href']) ? '' : ' href="'.$item['href'].'" '; //a href
        $onclick = empty($item['onclick']) ? '' : ' onclick="'.$item['onclick'].'" '; //a href
       
        $child_class = "level$level ";
       	if ( !empty($item['children']) ) $child_class .= 'nav-parent ';
       	if ( $item['rt'] && $current_rt == $item['rt'] ) $child_class .= 'active ';
       	if ( $child_class ) $child_class = ' class="'.$child_class.'"';
       	
        $result .= '<li' . $id .  $child_class .  '>';
        $result .= '<a ' . $class . $href . $onclick . '>';

    	//check icon rl type html, image or none. 
    	if ( is_html( $item['icon'] ) ) {
    		$result .= $item['icon'];
    	} else if ($item['icon']) {
    		$result .= '<img class="menu_image" src="'. HTTPS_DIR_RESOURCE . $item['icon'].'" alt="" />';
    	} else {
    		$result .= '<i class="fa fa-caret-right"></i> ';
    	}
        $result .= '<span class="menu_text">' . $item['text'] . '</span></a>';
        //if children build inner clild trees
        if ( !empty($item['children']) ) $result .= "\r\n" . renderAdminMenu($item['children'], $level+1, $current_rt);
        $result .= "</li>\r\n";            
    }
    if ( $level ) $result .= "</ul>\r\n";
    return $result;
}