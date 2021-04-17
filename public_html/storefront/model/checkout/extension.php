<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

class ModelCheckoutExtension extends Model
{
    /**
     * @param string $type
     *
     * @return array
     * @throws AException
     */
    public function getExtensions($type)
    {
        $store_id = (int) $this->config->get('config_store_id');
        $cache_key = 'extensions.list.type.'.md5($type).'.store_'.$store_id;

        $output = $this->cache->pull($cache_key);
        if ($output !== false) {
            return $output;
        }
        $output = [];
        $sql = "SELECT e.*, s.value as status
                FROM ".$this->db->table("extensions")." e
                LEFT JOIN ".$this->db->table("settings")." s
                    ON ( s.`group` = e.`key` AND s.`key` = CONCAT(e.`key`,'_status') )
                WHERE e.`type` = '".$this->db->escape($type)."'
                    AND s.`value`='1' AND s.store_id = '".$store_id."'";
        $query = $this->db->query($sql);
        if ($query->rows) {
            foreach ($query->rows as $row) {
                $sort_order = $this->config->get($row['key'].'_sort_order');
                $sort_order = empty($sort_order) ? 1000 : (int) $sort_order;
                while (isset($output[$sort_order])) {
                    $sort_order++;
                }
                $output[$sort_order] = $row;
            }
        }
        ksort($output, SORT_NUMERIC);
        $this->cache->push($cache_key, $output);
        return $output;
    }

    /**
     * @param string $type
     * @param int $position
     *
     * @return array
     * @throws AException
     */
    public function getExtensionsByPosition($type, $position)
    {
        $extension_data = [];
        $query = $this->db->query(
            "SELECT e.*, s.value as status
            FROM ".$this->db->table("extensions")." e
            LEFT JOIN ".$this->db->table("settings")." s 
                ON ( TRIM(s.`group`) = TRIM(e.`key`) AND TRIM(s.`key`) = CONCAT(TRIM(e.`key`),'_status') )
            WHERE e.`type` = '".$this->db->escape($type)."'
                AND s.`value`='1' 
                AND s.store_id = '".$this->config->get('config_store_id')."'"
        );
        foreach ($query->rows as $result) {
            if ($this->config->get($result['key'].'_status')
                && ($this->config->get($result['key'].'_position') == $position)
            ) {
                $extension_data[] = [
                    'code'       => $result['key'],
                    'sort_order' => $this->config->get($result['key'].'_sort_order'),
                ];
            }
        }

        $sort_order = [];
        foreach ($extension_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }
        array_multisort($sort_order, SORT_ASC, $extension_data);
        return $extension_data;
    }

    /**
     * @param string $extension_name
     * @param int $store_id
     *
     * @return array
     * @throws AException
     */
    public function getSettings($extension_name, $store_id = 0)
    {
        $data = [];
        if ($store_id == 0) {
            $store_id = $this->config->get('config_store_id');
        }

        $query = $this->db->query(
            "SELECT *
            FROM ".$this->db->table("settings")." 
            WHERE `group` = '".$this->db->escape($extension_name)."'
                AND store_id = '".(int) $store_id."'"
        );
        foreach ($query->rows as $result) {
            $value = $result['value'];
            if (is_serialized($value)) {
                $value = unserialize($value);
            }
            $data[$result['key']] = $value;
        }
        return $data;
    }

    /**
     *   Function to get image details based on RL path or RL ID
     *
     * @param int|string $resourceImage
     *
     * @return array
     * @throws AException
     */
    public function getSettingImage($resourceImage)
    {
        if (!has_value($resourceImage)) {
            return [];
        }
        $resource = new AResource('image');
        if (is_numeric($resourceImage)) {
            // consider this is a pure image resource ID
            $image_data = $resource->getResource($resourceImage);
        } else {
            $image_data = $resource->getResource(
                $resource->getIdFromHexPath(
                    str_replace('image/', '', $resourceImage)
                )
            );
        }
        return $image_data;
    }
}