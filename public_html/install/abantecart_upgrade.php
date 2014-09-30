<?php

/*
	1.2 Upgrade notes:
	- Updated all libraries to new versions. 
	JQuery 1.10 update notes:
		- replace depricated .live() method with .on()

	Bottstrap 3 update notes:


*/


$dataset = new ADataset ('menu', 'admin');

$columns[] = array( 'name' => 'item_icon_rl_id',
					'type' => 'varchar'	);
$dataset->defineColumns($columns);

// get dataset_column_id after insert
$all_columns = $dataset->getColumnDefinitions();
foreach($all_columns as $c){
	if($c['dataset_column_name']=='item_icon_rl_id'){
		$dataset_column_id = $c['dataset_column_id'];
		break;
	}
}


$icons = array(
		array('catalog', '<i class="fa fa-folder-open"></i>&nbsp;'),
		array('sale', '<i class="fa fa-flag-o"></i>&nbsp;'),
		array('design', '<i class="fa fa-file-text"></i>&nbsp;'),
		array('extension', '<i class="fa fa-puzzle-piece"></i>&nbsp;'),
		array('system', '<i class="fa fa-wrench"></i>&nbsp;'),
		array('reports', '<i class="fa fa-bar-chart-o"></i>&nbsp;'),
		array('help', '<i class="fa fa-life-ring"></i>&nbsp;'),
		array('category', '<i class="fa fa-folder-open"></i>&nbsp;'),
		array('product', '<i class="fa fa-plus-square"></i>&nbsp;'),
		array('manufacturer', '<i class="fa fa-bookmark-o"></i>&nbsp;'),
		array('download', '<i class="fa fa-upload"></i>&nbsp;'),
		array('review', '<i class="fa fa-comment-o"></i>&nbsp;'),
		array('attributes', '<i class="fa fa-ticket"></i>&nbsp;'),
		array('extensions', '<i class="fa fa-th"></i>&nbsp;'),
		array('shipping', '<i class="fa fa-truck"></i>&nbsp;'),
		array('payment', '<i class="fa fa-university"></i>&nbsp;'),
		array('templates', '<i class="fa fa-outdent"></i>&nbsp;'),
		array('languages', '<i class="fa fa-language"></i>&nbsp;'),
		array('total', '<i class="fa fa-sign-in"></i>&nbsp;'),
		array('add_extension', '<i class="fa fa-paperclip"></i>&nbsp;'),
		array('extensions_stores', '<i class="fa fa-arrows-alt"></i>&nbsp;'),
		array('order', '<i class="fa fa-flag-checkered"></i>&nbsp;'),
		array('customer', '<i class="fa fa-child"></i>&nbsp;'),
		array('customer_group', '<i class="fa fa-book"></i>&nbsp;'),
		array('coupon', '<i class="fa fa-tags"></i>&nbsp;'),
		array('contact', '<i class="fa fa-envelope-o"></i>&nbsp;'),
		array('setting', '<i class="fa fa-cogs"></i>&nbsp;'),
		array('users', '<i class="fa fa-users"></i>&nbsp;'),
		array('localisation', '<i class="fa fa-random"></i>&nbsp;'),
		array('data', '<i class="fa fa-cubes"></i>&nbsp;'),
		array('updater', '<i class="fa fa-refresh"></i>&nbsp;'),
		array('cache', '<i class="fa fa-recycle"></i>&nbsp;'),
		array('messages', '<i class="fa fa-weixin"></i>&nbsp;'),
		array('logs', '<i class="fa fa-floppy-o"></i>&nbsp;'),
		array('report_sale', '<i class="fa fa-signal"></i>&nbsp;'),
		array('viewed', '<i class="fa fa-sort-amount-desc"></i>&nbsp;'),
		array('purchased', '<i class="fa fa-file-text-o"></i>&nbsp;'),
		array('banner_manager_stat', '<i class="fa fa-reply-all"></i>&nbsp;'),
		array('abantecart', '<i class="fa fa-external-link"></i>&nbsp;'),
		array('documentation', '<i class="fa fa-graduation-cap"></i>&nbsp;'),
		array('support', '<i class="fa fa-info"></i>&nbsp;'),
		array('template', '<i class="fa fa-files-o"></i>&nbsp;'),
		array('layout', '<i class="fa fa-bars"></i>&nbsp;'),
		array('blocks', '<i class="fa fa-qrcode"></i>&nbsp;'),
		array('menu', '<i class="fa fa-tag"></i>&nbsp;'),
		array('content', '<i class="fa fa-clipboard"></i>&nbsp;'),
		array('banner_manager', '<i class="fa fa-picture-o"></i>&nbsp;'),
		array('all_settings', '<i class="fa fa-sliders"></i>&nbsp;'),
		array('settings_details', '<i class="fa fa-list"></i>&nbsp;'),
		array('settings_general', '<i class="fa fa-folder-o"></i>&nbsp;'),
		array('settings_checkout', '<i class="fa fa-shopping-cart"></i>&nbsp;'),
		array('settings_appearance', '<i class="fa fa-pencil-square-o"></i>&nbsp;'),
		array('settings_mail', '<i class="fa fa-envelope-square"></i>&nbsp;'),
		array('settings_api', '<i class="fa fa-slack"></i>&nbsp;'),
		array('settings_system', '<i class="fa fa-tasks"></i>&nbsp;'),
		array('settings_newstore', '<i class="fa fa-sitemap"></i>&nbsp;'),
		array('user', '<i class="fa fa-male"></i>&nbsp;'),
		array('permission', '<i class="fa fa-code-fork"></i>&nbsp;'),
		array('language', '<i class="fa fa-language"></i>&nbsp;'),
		array('language_definitions', '<i class="fa fa-sort-alpha-asc"></i>&nbsp;'),
		array('currency', '<i class="fa fa-money"></i>&nbsp;'),
		array('stockstatus', '<i class="fa fa-list-alt"></i>&nbsp;'),
		array('orderstatus', '<i class="fa fa-sort-amount-asc"></i>&nbsp;'),
		array('country', '<i class="fa fa-globe"></i>&nbsp;'),
		array('zone', '<i class="fa fa-thumb-tack"></i>&nbsp;'),
		array('location', '<i class="fa fa-flag-checkered"></i>&nbsp;'),
		array('taxclass', '<i class="fa fa-briefcase"></i>&nbsp;'),
		array('lengthclass', '<i class="fa fa-arrows-h"></i>&nbsp;'),
		array('weightclass', '<i class="fa fa-angle-double-down"></i>&nbsp;'),
		array('backup', '<i class="fa fa-jsfiddle"></i>&nbsp;'),
		array('migrate', '<i class="fa fa-share-alt-square"></i>&nbsp;'),
		array('datasets', '<i class="fa fa-database"></i>&nbsp;'),
		array('import_export', '<i class="fa fa-exchange"></i>&nbsp;'),
		array('file_uploads', '<i class="fa fa-download"></i>&nbsp;'),
		array('installlog', '<i class="fa fa-history"></i>&nbsp;'),
		array('error_log', '<i class="fa fa-exclamation-triangle"></i>&nbsp;')
);

foreach($icons as $row){
	$sql = "UPDATE ". $this->db->table('dataset_values')."
			SET value_varchar = '".$this->db->escape($row[1])."'
			WHERE dataset_column_id = ".$dataset_column_id."
				AND row_id = (SELECT DISTINCT row_id
							  FROM ". $this->db->table('dataset_values')."
							  WHERE dataset_column_id = 10 AND value_varchar = '".$this->db->escape($row[0])."'
							  )";
	$this->db->query($sql);
}

//add new menu
$m = new AMenu('admin');
$m->insertMenuItem(
		array(
			'item_id' => 'marketplace',
			'item_text' => 'text_extensions_store',
			"item_url" => 'window.open(\'http://marketplace.abantecart.com\');',
			"item_icon_rl_id" => '<i class="fa fa-puzzle-piece"></i>&nbsp;',
			"parent_id" => 'help',
			"sort_order" => 4,
			"item_type" => 'core'));


//add triggers
//select all tables with date_added
/*
$tables_sql = "
	SELECT DISTINCT TABLE_NAME 
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE COLUMN_NAME IN ('date_added')
    AND TABLE_SCHEMA='" . DB_DATABASE . "'";

$query = $this->db->query( $tables_sql);
foreach ($query->rows as $t) {
	$table_name = $t['TABLE_NAME'];
	$triger_name = $table_name . "_date_add_trg";

	$triger_checker = $this->db->query("SELECT TRIGGER_NAME
						FROM information_schema.triggers
						WHERE TRIGGER_SCHEMA = '" . DB_DATABASE . "' AND TRIGGER_NAME = '$triger_name'");
	if (!$query->row[0]) {
		$sql = "
		CREATE TRIGGER `$triger_name` BEFORE INSERT ON `$table_name` FOR EACH ROW
		BEGIN
    		SET NEW.date_added = NOW();
		END;
		";
		$this->db->query($sql);
	}	
}
*/

//clear cache after upgrade       					
$this->cache->delete('*');


