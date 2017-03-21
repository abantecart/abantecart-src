<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}




		
$language_code = "he";
$language_directory = "hebrew";

$query = $this->db->query(
	"SELECT language_id FROM ".$this->db->table("languages")." 
	WHERE code='".$language_code."' AND directory='".$language_directory."'");
$language_id = $query->row["language_id"];
//delete menu
$storefront_menu = new AMenu_Storefront();
$storefront_menu->deleteLanguage($language_id);

//delete all other language related tables
$lm = new ALanguageManager($this->registry, $language_code);
$lm->deleteAllLanguageEntries($language_id);

//delete language
$this->db->query("DELETE FROM ".$this->db->table("languages")." WHERE `code`='".$language_code."'");

$this->cache->remove("localization");