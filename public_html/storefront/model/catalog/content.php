<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/** @noinspection PhpUndefinedClassInspection */
class ModelCatalogContent extends Model
{
    /**
     * @param $content_id
     *
     * @return array
     */
    public function getContent($content_id)
    {
        $content_id = (int)$content_id;
        $store_id = (int)$this->config->get('config_store_id');
        $language_id = (int)$this->config->get('storefront_language_id');
        $cache_key = 'content.'.$content_id.'.store_'.$store_id.'_lang_'.$language_id;
        $cache = $this->cache->pull($cache_key);

        if ($cache !== false) {
            return $cache;
        }

        $cache = array();
        $sql = "SELECT DISTINCT i.content_id, id.*
				FROM ".$this->db->table("contents")." i
				LEFT JOIN ".$this->db->table("content_descriptions")." id
					ON (i.content_id = id.content_id AND id.language_id = '".$language_id."')
				LEFT JOIN ".$this->db->table("contents_to_stores")." i2s
					ON (i.content_id = i2s.content_id)
				WHERE i.content_id = '".$content_id."' AND COALESCE(i2s.store_id,0) = '".$store_id."' AND i.status = '1'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {
            $cache = $query->row;
        }
        $this->cache->push($cache_key, $cache);

        return $cache;
    }

    /**
     * @return array
     */
    public function getContents()
    {
        $store_id = (int)$this->config->get('config_store_id');
        $language_id = (int)$this->config->get('storefront_language_id');
        $cache_key = 'content.all.store_'.$store_id.'_lang_'.$language_id;
        $output = $this->cache->pull($cache_key);
        if ($output === false) {
            $output = array();
            $sql = "SELECT i.*, id.*
					FROM ".$this->db->table("contents")." i
					LEFT JOIN ".$this->db->table("content_descriptions")." id
							ON (i.content_id = id.content_id
									AND id.language_id = '".(int)$this->config->get('storefront_language_id')."')";

            $sql .= "LEFT JOIN ".$this->db->table("contents_to_stores")." i2s ON (i.content_id = i2s.content_id)";
            $sql .= "WHERE i.status = '1' ";
            $sql .= " AND COALESCE(i2s.store_id,0) = '".(int)$this->config->get('config_store_id')."'";

            $sql .= "ORDER BY i.parent_content_id, i.sort_order, LCASE(id.title) ASC";
            $query = $this->db->query($sql);

            if ($query->num_rows) {
                $output = $query->rows;
            }
            $this->cache->push($cache_key, $output);
        }
        return $output;
    }
}
