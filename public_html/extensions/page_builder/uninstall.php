<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}
//delete menu item
$menu = new AMenu ("admin");
$menu->deleteMenuItem("page_builder");