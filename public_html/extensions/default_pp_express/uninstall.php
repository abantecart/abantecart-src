<?php
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

// delete block
$lm = new ALayoutManager();
$lm->deleteBlock('default_pp_express_button');
$lm->deleteBlock('default_pp_express_banner');

$block_names = array('paypal_billmelater_marketing');
$custom_blocks = $lm->getBlocksList(array('subsql_filter' => 'cb.custom_block_id <> 0'));

foreach($custom_blocks as $block) {
	if(in_array($block['block_name'], $block_names)) {
		$this->db->query ("DELETE FROM " . DB_PREFIX . "block_layouts
						   WHERE custom_block_id = '" . ( int ) $block['custom_block_id'] . "'");
		$this->cache->delete ( 'layout.a.blocks' );
		$this->cache->delete ( 'layout.blocks' );

		$lm->deleteCustomBlock($block['custom_block_id']);
	}
}

$rm = new AResourceManager();
$rm->setType('image');

$resources = $rm->getResources('extensions', 'default_pp_express');

if ( is_array($resources) )
{
	foreach($resources as $resource)
	{
		$rm->deleteResource($resource['resource_id']);
	}
}