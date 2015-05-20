<?php

/*
	1.2.2 Upgrade notes:

*/

//update encrypted table if it exists
$sql = "SELECT TABLE_NAME
		FROM information_schema.TABLES
		WHERE information_schema.TABLES.table_schema = '".DB_DATABASE."'
					AND TABLE_NAME = '".$this->db->table('ac_users')."'";

$result = $this->db->query($sql);
if($result->num_rows){
	$sql  = "ALTER TABLE `".$this->db->table('customers_enc')."`  ADD `wishlist` text COLLATE utf8_general_ci;";
}






//clear cache after upgrade       					
$this->cache->delete('*');
