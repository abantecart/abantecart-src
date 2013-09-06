<div class="sidewidt">
	<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
				 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
	<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
	<ul id="main_menu" class="nav nav-list side_list main_menu">
		<?php echo buildMenuTree_generic( $storemenu ); ?>
	</ul>
	</div>
</div>
<?php
function buildMenuTree_generic( $menu, $level = 0 ){
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

        $id = ( empty($item['id']) ? '' : ' data-id="menu_'.$item['id'].'" ' ); // li ID

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

        $result .= '<li ' . $id . ' class="dropdown hover">';
        $result .= '<a ' . $class . $href . '>';
	    $result .= $item['icon'] ? '<img src="'. HTTP_DIR_RESOURCE . $item['icon'].'" alt="" />' : '';
		$result .= '<span>' . $item['text'] . '</span></a>';

        if ( !empty($item['children']) ) $result .= "\r\n" . buildMenuTree_generic($item['children'], $level+1) ;
        $result .= "</li>\r\n";
    }
    if ( $level ) $result .= "</ul>\r\n";
    return $result;
}
?>