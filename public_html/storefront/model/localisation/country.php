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
class ModelLocalisationCountry extends Model {

	public function getCountry($country_id) {
		$language_id = $this->language->getLanguageID();
		$default_lang_id = $this->language->getDefaultLanguageID();	

		$query = $this->db->query("SELECT *, COALESCE( cd1.name,cd2.name) as name
										FROM " . $this->db->table("countries") . " c
										LEFT JOIN " . $this->db->table("country_descriptions") . " cd1
											ON (c.country_id = cd1.country_id AND cd1.language_id = '" . (int)$language_id . "')
			    						LEFT JOIN " . $this->db->table("country_descriptions") . " cd2
			    							ON (c.country_id = cd2.country_id AND cd2.language_id = '" . (int)$default_language_id . "')
										WHERE c.country_id = '" . (int)$country_id . "' AND status = '1'");
		return $query->row;
	}
	
	public function getCountries() {
		$language_id = $this->language->getLanguageID();
		$default_language_id = $this->language->getDefaultLanguageID();		
		$country_data = $this->cache->get('country', $language_id);
		
		if (is_null($country_data)) {
			if ($language_id == $default_language_id) {
			    $query = $this->db->query( "SELECT *
			    						FROM " . $this->db->table("countries") . " c
			    						LEFT JOIN " . $this->db->table("country_descriptions") . " cd 
			    							ON (c.country_id = cd.country_id AND cd.language_id = '" . (int)$language_id . "') 
			    						WHERE c.status = '1'	
			    						ORDER BY cd.name ASC");
			    		
			} else {
			    //merge text for missing country translations. 
			    $query = $this->db->query("SELECT *, COALESCE( cd1.name,cd2.name) as name
			    		FROM " . $this->db->table("countries") . " c
			    		LEFT JOIN " . $this->db->table("country_descriptions") . " cd1
			    			ON (c.country_id = cd1.country_id AND cd1.language_id = '" . (int)$language_id . "')
			    		LEFT JOIN " . $this->db->table("country_descriptions") . " cd2
			    			ON (c.country_id = cd2.country_id AND cd2.language_id = '" . (int)$default_language_id . "')
			    		WHERE c.status = '1'
			    		ORDER BY cd1.name,cd2.name ASC");	
			}
	
			$country_data = $query->rows;
		
			$this->cache->set('country', $country_data, $language_id);
		}

		return $country_data;
	}
}
?>