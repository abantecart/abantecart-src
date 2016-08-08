<?php
/*
Neowize Insights - analytics and actionable insights for eCommerce sites.
For more info: www.neowize.com
*/

if (! defined ( 'DIR_CORE' )) {
header ( 'Location: static_pages/' );
}

// delete the custom block
$layout = new ALayoutManager();
$layout->deleteBlock('neowize_insights');
