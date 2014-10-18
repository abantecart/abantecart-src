<?php
/*------------------------------------------------------------------------------
  $Id$

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2011, 2012 AlgoZone, Inc

------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
header ( 'Location: static_pages/' );
}

// add new menu item
$rm = new AResourceManager();
$rm->setType('image');

$language_id = $this->language->getContentLanguageID();
$data = array();
$data['resource_code'] = '<i class="fa fa-list"></i>&nbsp;';
$data['name'] = array($language_id => 'Menu Icon Forms Manager');
$data['title'] = array($language_id => '');
$data['description'] = array($language_id => '');
$resource_id = $rm->addResource($data);

$menu = new AMenu ( "admin" );
$menu->insertMenuItem ( array (  "item_id" => "forms_manager",
								 "parent_id"=>"design",
								 "item_text" => "forms_manager_name",
								 "item_url" => "tool/forms_manager",
								 "item_icon_rl_id" => $resource_id,
								 "item_type"=>"extension",
								 "sort_order"=>"7")
								);

$sql = "SELECT block_id
		FROM ".$this->db->table('blocks')."
		WHERE block_txt_id='custom_form_block'";
$result = $this->db->query($sql);
if(!$result->num_rows){
	$this->db->query("INSERT INTO ".$this->db->table('blocks')." (`block_txt_id`, `controller`, `date_added`)
					  VALUES ('custom_form_block', 'blocks/custom_form_block', NOW() );");
	$block_id = $this->db->getLastId();

	$sql = "
		INSERT INTO ".$this->db->table('block_templates')."
			(`block_id`, `parent_block_id`, `template`, `date_added`)
		VALUES
			(".$block_id.", 1, 'blocks/custom_form_block_header.tpl', NOW() ),
			(".$block_id.", 2, 'blocks/custom_form_block_content.tpl', NOW() ),
			(".$block_id.", 3, 'blocks/custom_form_block.tpl', NOW() ),
			(".$block_id.", 4, 'blocks/custom_form_block_content.tpl', NOW() ),
			(".$block_id.", 5, 'blocks/custom_form_block_content.tpl', NOW() ),
			(".$block_id.", 6, 'blocks/custom_form_block.tpl', NOW() ),
			(".$block_id.", 7, 'blocks/custom_form_block_content.tpl', NOW() ),
			(".$block_id.", 8, 'blocks/custom_form_block_header.tpl', NOW() )";
	$this->db->query($sql);
	$this->cache->delete('layout.blocks');
}