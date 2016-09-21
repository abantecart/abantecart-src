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