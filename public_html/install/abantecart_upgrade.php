<?php
/**
 * @var AController $this
 */
// todo: default_flat_rate setting upgrade

$result = $this->db->query("SELECT * FROM ".$this->db->table('settings')." WHERE `key`='config_logo';");

$sql = "INSERT INTO ".$this->db->table('settings')." (`group`,`key`,`value`) 
		VALUES ('appearance','config_mail_logo','".$this->db->escape($result->row['value'])."');";
$this->db->query($sql);