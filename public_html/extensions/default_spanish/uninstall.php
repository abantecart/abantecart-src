<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/

if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

$language_code = 'es';
$language_directory = 'spanish';

$query = $this->db->query("SELECT language_id FROM ".DB_PREFIX."languages WHERE code='".$language_code."' AND directory='".$language_directory."'");
$language_id = $query->row['language_id'];
//delete menu
$storefront_menu = new AMenu_Storefront();
$storefront_menu->deleteLanguage($language_id);

//delete all other langauge related tables
$lm = new ALanguageManager($this->registry, $language_code);
$lm->deleteAllLanguageEntries($language_id);

//delete langauge
$this->db->query("DELETE FROM ".DB_PREFIX."languages WHERE `code`='".$language_code."'");

$this->cache->delete('language');
$this->cache->delete('lang.'.$language_code);