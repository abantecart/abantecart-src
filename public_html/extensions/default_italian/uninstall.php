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

$language_id = $this->db->query("SELECT language_id FROM ".DB_PREFIX."languages WHERE code='it'");
$language_id = $this->db->row['language_id'];
$storefront_menu = new AMenu_Storefront();
$storefront_menu->deleteLanguage($language_id);
$this->cache->delete('language');
$this->cache->delete('lang.it');