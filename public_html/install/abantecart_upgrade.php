<?php
//check if columns exists before adding
$sql = "SELECT *
		FROM information_schema.COLUMNS
		WHERE
		TABLE_SCHEMA = '".DB_DATABASE."'
		AND TABLE_NAME = '".$this->db->table('products')."'
		AND COLUMN_NAME = 'settings'";
$result = $this->db->query($sql);
if( !$result->num_rows ){
	$this->db->query("ALTER TABLE ".$this->db->table('products')." ADD COLUMN `settings` LONGTEXT COLLATE utf8_general_ci;");
}

$task_api_key = $this->config->get('task_api_key');
if(!$task_api_key){
	$sql = "REPLACE INTO ".$this->db->table('settings')."
			(`store_id`, `group`, `key`, `value`);
			VALUES ( '".$store_id."', 'api', 'task_api_key', '".$this->db->escape(genToken(16))."')";
	$result = $this->db->query($sql);
}

// fix for menu
if($this->config->get('neowize_insights_status')){
	$sql = "UPDATE ".$this->db->table('extensions')." SET status=1 WHERE `key` = 'neowize_insights'";
	$result = $this->db->query($sql, true);
}

/*
 * TODO moving config_title etc multilingual settings
 */