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
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}



// add new menu item
$rm = new AResourceManager();
$rm->setType('image');

$language_id = $this->language->getContentLanguageID();
$data = array();
$data['resource_code'] = '<i class="fa fa-picture-o"></i>&nbsp;';
$data['name'] = array($language_id => 'Menu Icon Banner Manager');
$data['title'] = array($language_id => '');
$data['description'] = array($language_id => '');
$resource_id = $rm->addResource($data);

$menu = new AMenu ( "admin" );
$menu->insertMenuItem ( array (  "item_id" => "banner_manager",
								 "parent_id"=>"design",
								 "item_text" => "banner_manager_name",
								 "item_url" => "extension/banner_manager",
								 "item_icon_rl_id" => $resource_id,
								 "item_type"=>"extension",
								 "sort_order"=>"6")
								);
$data = array();
$data['resource_code'] = '<i class="fa fa-reply-all"></i>&nbsp;';
$data['name'] = array($language_id => 'Menu Icon Banner Manager Stat');
$data['title'] = array($language_id => '');
$data['description'] = array($language_id => '');
$resource_id = $rm->addResource($data);

$menu->insertMenuItem ( array (  "item_id" => "banner_manager_stat",
								 "parent_id"=>"reports",
								 "item_text" => "banner_manager_name_stat",
								 "item_url" => "extension/banner_manager_stat",
								 "item_icon_rl_id" => $resource_id,
								 "item_type"=>"extension",
								 "sort_order"=>"4")
								);

$sql = "SELECT block_id FROM ".$this->db->table('blocks')." WHERE block_txt_id='banner_block'";
$result = $this->db->query($sql);
if(!$result->num_rows){
	$this->db->query("INSERT INTO ".$this->db->table('blocks')." (`block_txt_id`, `controller`, `date_added`)
					  VALUES ('banner_block', 'blocks/banner_block', NOW() );");
	$block_id = $this->db->getLastId();

	$sql = "INSERT INTO ".$this->db->table('block_templates')." (`block_id`, `parent_block_id`, `template`, `date_added`)
			VALUES
		(".$block_id.", 1, 'blocks/banner_block_header.tpl', NOW() ),
		(".$block_id.", 2, 'blocks/banner_block_content.tpl', NOW() ),
		(".$block_id.", 3, 'blocks/banner_block.tpl', NOW() ),
		(".$block_id.", 4, 'blocks/banner_block_content.tpl', NOW() ),
		(".$block_id.", 5, 'blocks/banner_block_content.tpl', NOW() ),
		(".$block_id.", 6, 'blocks/banner_block.tpl', NOW() ),
		(".$block_id.", 7, 'blocks/banner_block_content.tpl', NOW() ),
		(".$block_id.", 8, 'blocks/banner_block_header.tpl', NOW() )";
	$this->db->query($sql);
	$this->cache->delete('layout.blocks');
}