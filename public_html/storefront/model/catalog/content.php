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
/** @noinspection PhpUndefinedClassInspection */
class ModelCatalogContent extends Model {
	/**
	 * @param $content_id
	 * @return array
	 */
	public function getContent($content_id) {
		$content_id = (int)$content_id;
		$cache = $this->cache->get('contents.content.'.$content_id, $this->config->get('storefront_language_id'), $this->config->get('config_store_id') );

		if(is_null($cache)){
			$cache = array();
			$sql = "SELECT DISTINCT i.content_id, id.*
					FROM " . $this->db->table("contents") . " i
					LEFT JOIN " . $this->db->table("content_descriptions") . " id
						ON (i.content_id = id.content_id
							AND id.language_id = '" . (int)$this->config->get('storefront_language_id') . "')";
			$sql .=	" LEFT JOIN " . $this->db->table("contents_to_stores") . " i2s ON (i.content_id = i2s.content_id)";
			$sql .=	" WHERE i.content_id = '" . (int)$content_id . "' ";
			$sql .= " AND COALESCE(i2s.store_id,0) = '" . (int)$this->config->get('config_store_id') . "'";
			$sql .= " AND i.status = '1'";
			$query = $this->db->query($sql);

			if($query->num_rows){
				$cache = $query->row;
			}
			$this->cache->set('contents.content.'.$content_id, $cache, $this->config->get('storefront_language_id'), $this->config->get('config_store_id') );
		}
		return (array)$cache;
	}

	/**
	 * @return array
	 */
	public function getContents() {

		$output = $this->cache->get('contents', $this->config->get('storefront_language_id'), $this->config->get('config_store_id') );
		if(is_null($output)){
			$sql = "SELECT i.*, id.*
					FROM " . $this->db->table("contents") . " i
					LEFT JOIN " . $this->db->table("content_descriptions") . " id
							ON (i.content_id = id.content_id
									AND id.language_id = '" . (int)$this->config->get('storefront_language_id') . "')";

			$sql .=	"LEFT JOIN " . $this->db->table("contents_to_stores") . " i2s ON (i.content_id = i2s.content_id)";
			$sql .=	"WHERE i.status = '1' ";
			$sql .= " AND COALESCE(i2s.store_id,0) = '" . (int)$this->config->get('config_store_id') . "'";

			$sql .= "ORDER BY i.parent_content_id, i.sort_order, LCASE(id.title) ASC";
			$query = $this->db->query($sql);

			if($query->num_rows){
				foreach($query->rows as $row){
						$output[] = $row;
				}
			}
			$this->cache->set('contents',$output, $this->config->get('storefront_language_id'), $this->config->get('config_store_id') );
		}
		return (array)$output;
	}
}
