<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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
class ModelCatalogCategory extends Model {
	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT *
									FROM " . DB_PREFIX . "categories c
									LEFT JOIN " . DB_PREFIX . "category_descriptions cd ON (c.category_id = cd.category_id AND cd.language_id = '" . (int)$this->config->get('storefront_language_id') . "')
									LEFT JOIN " . DB_PREFIX . "categories_to_stores c2s ON (c.category_id = c2s.category_id)
									WHERE c.category_id = '" . (int)$category_id . "'
										AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
										AND c.status = '1'");
		
		return $query->row;
	}
	
	public function getCategories($parent_id = 0, $limit=0) {
		$cache_name = 'category.list.'. $parent_id.'.'.$limit;
		$cache = $this->cache->get($cache_name, (int)$this->config->get('storefront_language_id'), (int)$this->config->get('config_store_id'));
		if(is_null($cache)){
			$query = $this->db->query("SELECT *
										FROM " . DB_PREFIX . "categories c
										LEFT JOIN " . DB_PREFIX . "category_descriptions cd ON (c.category_id = cd.category_id AND cd.language_id = '" . (int)$this->config->get('storefront_language_id') . "')
										LEFT JOIN " . DB_PREFIX . "categories_to_stores c2s ON (c.category_id = c2s.category_id)
										WHERE ".($parent_id<0 ? "" : "c.parent_id = '" . (int)$parent_id . "' AND ")."
										     c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'
										ORDER BY c.sort_order, LCASE(cd.name)
										".((int)$limit ? "LIMIT ".(int)$limit : '')."										");
			$cache =  $query->rows;
			$this->cache->set($cache_name, $cache, (int)$this->config->get('storefront_language_id'), (int)$this->config->get('config_store_id'));
		}
		return $cache;
	}
				
	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total
									FROM " . DB_PREFIX . "categories c
									LEFT JOIN " . DB_PREFIX . "categories_to_stores c2s ON (c.category_id = c2s.category_id)
									WHERE c.parent_id = '" . (int)$parent_id . "'
										AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
										AND c.status = '1'");
		
		return $query->row['total'];
	}
}
?>