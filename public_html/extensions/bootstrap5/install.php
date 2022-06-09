<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}


$file = DIR_EXT . '/bootstrap5/layout.xml';
$layout = new ALayoutManager('default');
$layout->loadXml(['file' => $file]);
