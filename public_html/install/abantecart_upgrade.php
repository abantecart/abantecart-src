<?php
//alter tables

//check if exist
$sql = "SELECT * FROM ".$this->db->table('banner_descriptions');
$result = $this->db->query($sql,true);
if($result){
	$this->db->query(
			"ALTER TABLE ".$this->db->table('banner_descriptions')."
			CHANGE COLUMN `description` `description` LONGTEXT NULL DEFAULT NULL"
			, true);
}

//TODO move all resources with type archives to resources/archive directory!
//