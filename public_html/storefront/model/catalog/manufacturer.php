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

class ModelCatalogManufacturer extends Model
{
    /**
     * @param $manufacturer_id
     *
     * @return array
     */
    public function getManufacturer($manufacturer_id)
    {
        $manufacturer_id = (int)$manufacturer_id;
        $store_id = (int)$this->config->get('config_store_id');
        $cache_key = 'manufacturer.'.$manufacturer_id.'.store_'.$store_id;
        $output = $this->cache->pull($cache_key);
        if ($output !== false) {
            return $output;
        }
        $query = $this->db->query("SELECT *
									FROM ".$this->db->table("manufacturers")." m
									LEFT JOIN ".$this->db->table("manufacturers_to_stores")." m2s
										ON (m.manufacturer_id = m2s.manufacturer_id)
									WHERE m.manufacturer_id = '".$manufacturer_id."'
										AND m2s.store_id = '".$store_id."'");
        $output = $query->row;
        $this->cache->push($cache_key, $output);
        return $output;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getManufacturers($data = [])
    {
        $store_id = (int)$this->config->get('config_store_id');
        $data['filter']['manufacturer_id'] = filterIntegerIdList((array)$data['filter']['manufacturer_id']);

        if (isset($data['start']) || isset($data['limit'])) {
            $data['start'] = max((int)$data['start'],0);
            $data['limit'] = max((int)$data['limit'],(int)$this->config->get('config_catalog_limit'));

            $cache_key = 'manufacturer.list.'.md5(var_export($data, true)).'.store_'.$store_id;
        } else {
            $cache_key = 'manufacturer.list.store_'.$store_id;
        }

        $output = $this->cache->pull($cache_key);

        if ($output !== false) {
            return $output;
        }

        $sql = "SELECT *
				FROM ".$this->db->table("manufacturers")." m
				LEFT JOIN ".$this->db->table("manufacturers_to_stores")." m2s
					ON (m.manufacturer_id = m2s.manufacturer_id)";

        if($data['filter']['manufacturer_id']){
            $sql .= " AND m.manufacturer_id IN (" . implode(',',$data['filter']['manufacturer_id']) . ")";
        }
        $sql .= " WHERE m2s.store_id = '".$store_id."'"
				  ." ORDER BY sort_order, LCASE(m.name) ASC";
        $sql .= " LIMIT ".$data['start'].", ".$data['limit'];

        $query = $this->db->query($sql);
        $output = $query->rows;
        $this->cache->push($cache_key, $output);
        return $output;
    }

    /**
     * @param $product_id
     *
     * @return array
     */
    public function getManufacturerByProductId($product_id)
    {
        $query = $this->db->query("SELECT *
										FROM ".$this->db->table("manufacturers")." m
										RIGHT JOIN ".$this->db->table("products")." p ON (m.manufacturer_id = p.manufacturer_id)
										WHERE p.product_id = '".(int)$product_id."'");
        return $query->rows;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getManufacturersData($data = [])
    {
        $cacheKey = 'manufacturer.data.'.md5(var_export($data, true));
        $output = $this->cache->pull($cacheKey);
        if ($output !== false) {
            return $output;
        }
        $storeId = (int)$this->config->get('config_store_id');
        $sql = "SELECT *,
                    (SELECT count(*) as cnt
                    FROM ".$this->db->table('products')." p
                    INNER JOIN " . $this->db->table('products_to_stores') . " s 
                        ON (p.product_id = s.product_id AND s.store_id=".$storeId.")
                    WHERE p.manufacturer_id = m.manufacturer_id 
                        AND p.status=1                    
                        AND COALESCE(p.date_available,'1970-01-01')< NOW() 
                    ) as product_count
                FROM ".$this->db->table("manufacturers")." m
                LEFT JOIN ".$this->db->table("manufacturers_to_stores")." m2s 
                ON (m.manufacturer_id = m2s.manufacturer_id)";

        $sql .= " WHERE m2s.store_id = '".$storeId."' ";
        if (!empty($data['subsql_filter'])) {
            $sql .= ' AND '.$data['subsql_filter'];
        }
        $sql .= " ORDER BY sort_order, LCASE(m.name) ASC";

        $query = $this->db->query($sql);
        $output = $query->rows;
        $this->cache->push($cacheKey, $output);
        return $output;
    }

    /**
     * @param array $data
     * @return array
     * @throws AException
     */
    public function getCategories(array $data = [])
    {
        $manufacturerIds = filterIntegerIdList($data['filter']['manufacturer_id']);
        if(!$manufacturerIds){
            return [];
        }

        $rates = filterIntegerIdList($data['filter']['rating']);

        $store_id = (int)$this->config->get('config_store_id');
        $cache_key = 'manufacturer.category_ids.' . md5(var_export($data, true)). '.store_' . $store_id;
        $categories = $this->cache->pull($cache_key);
        if ($categories !== false) {
            return $categories;
        }

        $sql = "SELECT DISTINCT p2c.category_id
                FROM " . $this->db->table('products') . " p
                INNER JOIN " . $this->db->table('products_to_categories') . " p2c 
                    ON p.product_id = p2c.product_id ";
        if($rates){
            $sql .= " INNER JOIN ".$this->db->table('reviews')." r
                    ON (r.product_id = p.product_id AND r.status = 1
                        AND r.rating IN (".implode(',',(array)$data['filter']['rating'])."))" ;
        }
        $sql .= " WHERE p.status = '1'
                    AND COALESCE(p.date_available,'1970-01-01')< NOW() 
                    AND p.manufacturer_id IN (" . implode(', ', $manufacturerIds) . ")";
        $query = $this->db->query($sql);
        $output = array_map('intval', array_column($query->rows,'category_id'));
        $this->cache->push($cache_key, $output);
        return $output;
    }
}