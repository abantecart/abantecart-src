<?php
/*
NeoWize Insights - analytics and actionable insights for eCommerce sites.
For more info: www.neowize.com
*/

if (! defined ( 'DIR_CORE' )) {
header ( 'Location: static_pages/' );
}


// INSTALL ADMIN DASHBOARD MENU ITEM

// add new menu item
$rm = new AResourceManager();
$rm->setType('image');

// create resource for the Neowize menu icon
$data = array();
$data['resource_code'] = '<i class="fa fa-signal"></i> ';
$data['name'] = array($language_id => 'Menu Icon NeoWize');
$data['title'] = array($language_id => '');
$data['description'] = array($language_id => '');
$resource_menu_icon_id = $rm->addResource($data);

// create NeoWize admin menu button
$menu = new AMenu ( "admin" );
$menu->insertMenuItem ( array (
						 "item_id" => "neowize_insights",
						 "parent_id" => null,
						 "item_icon_rl_id" => $resource_menu_icon_id,
						 "item_text" => "Analytics & Insights",
						 "item_url" => "neowize/dashboard",
						 "item_type"=>"extension",
						 "sort_order"=>"0"
						)
					);


// INSTALL FOOTER BLOCK

// get layout manager
$layout_manager = new ALayoutManager();

// add custom block to several places
// note: we try several places because some templates don't contain all blocks. for example, I've seen a template
// that don't have footer_top. also, some pages may not contain all root blocks. so we try few and one of them should work.
$block_data = array(
	'block_txt_id' => 'neowize_insights',
	'controller' => 'blocks/neowize_insights',
	'templates' => array(
		array(
			'parent_block_txt_id' => 'footer_top',
			'template' => 'blocks/neowize_insights.tpl',
		),
		array(
			'parent_block_txt_id' => 'content_top',
			'template' => 'blocks/neowize_insights.tpl',
		),
		array(
			'parent_block_txt_id' => 'content_bottom',
			'template' => 'blocks/neowize_insights.tpl',
		),
	)
);

// save block and store its id
$block_id = $layout_manager->saveBlock($block_data);

// get all layout ids
$sql = "SELECT layout_id FROM " . $this->db->table("layouts") . " ORDER BY layout_id ASC";
$result = $this->db->query($sql);
$layouts = $result->rows;

// get the block ids for all the root blocks we want to add NeoWize to (footer_top, content_top, content_bottom...)
$sql = "SELECT block_id FROM " . $this->db->table("blocks") . " WHERE block_txt_id IN ('footer_top', 'content_top', 'content_bottom')";
$result = $this->db->query($sql);
$parent_block_ids = array();
foreach ($result->rows as $row)
{
	array_push($parent_block_ids, $row['block_id']);
}
$parent_block_ids = implode(", ", $parent_block_ids);

// add our block to all layouts (in 'block_layouts' table)
foreach($layouts as $layout){

    // get layout id
    $layout_id = $layout['layout_id'];

    // set default position
    $position = 10;

    // get parent instance id for this layout - the root block that has no parent_instance_id is the instance_id we take as our own parent.
    $sql = "SELECT instance_id FROM " . $this->db->table("block_layouts") . " WHERE layout_id='" . (int)$layout_id . "' AND parent_instance_id='0' AND block_id IN (" . $parent_block_ids . ")";
    $result = $this->db->query($sql);
    $parent_instance_id = $result->rows[0]['instance_id'];

    // add out block to this layout with the parent instance we found
    $sql = "INSERT INTO " . $this->db->table("block_layouts") . " (" .
                                         "layout_id, " .
                                         "block_id, " .
                                         "parent_instance_id, " .
                                         "position, " .
                                         "status, " .
                                         "date_added, " .
                                         "date_modified) " .
        "VALUES ('" . ( int )$layout_id . "', " .
                "'" . ( int )$block_id . "', " .
                "'" . ( int )$parent_instance_id . "', " .
                "'" . ( int )$position . "', " .
                "'1', " .
              "NOW(), " .
              "NOW())";
    $this->db->query($sql);
}

// create dataset with neowize installation data
$neowize_data = new ADataset();
$neowize_data->createDataset('neowize','neowize_install_data');
$neowize_data->setDatasetProperties(array('block_id' => $block_id));

// clear layouts cache
if (isset($this->cache->remove))
{
	$this->cache->remove('layout');
}

