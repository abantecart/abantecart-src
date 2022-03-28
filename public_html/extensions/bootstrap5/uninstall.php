<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}




$extension_id = 'bootstrap5';
// delete template layouts
try{
$layout = new ALayoutManager($extension_id);
$layout->deleteTemplateLayouts();
}catch(AException $e){}
