<?php
/*------------------------------------------------------------------------------
  $Id$

  For AbanteCart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2011 - 2013 Belavier Commerce LLC

------------------------------------------------------------------------------*/

if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

$language_code = 'es';
$language_directory = 'spanish';

$this->db->query("SELECT language_id FROM ".DB_PREFIX."languages WHERE code='".$language_code."' AND directory='".$language_directory."'");
$language_id = $this->db->row['language_id'];
//delete menu
$storefront_menu = new AMenu_Storefront();
$storefront_menu->deleteLanguage($language_id);

//delete all other langauge related tables
$lm = new ALanguageManager($this->registry, $language_code);
$ml_tables = $lm->deleteAllLanguageEntries($language_id);

//delete langauge
$this->db->query("DELETE FROM ".DB_PREFIX."languages WHERE `code`='".$language_code."'");

$this->cache->delete('language');
$this->cache->delete('lang.'.$language_code);