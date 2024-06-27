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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ModelCatalogDownload
 *
 * @property ModelLocalisationOrderStatus $model_localisation_order_status
 */
class ModelCatalogDownload extends Model
{
    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function addDownload($data)
    {
        if ($data['activate'] != 'order_status') {
            $data['activate_order_status_id'] = 0;
        }
        if ($data['activate'] == 'before_order') {
            $data['expire_days'] = 0;
            $data['max_downloads'] = 0;
        }
        $this->db->query(
            "INSERT INTO ".$this->db->table('downloads')."
              SET filename  = '".$this->db->escape($data['filename'])."',
                  mask = '".$this->db->escape($data['mask'])."',
                  max_downloads = ".((int) $data['max_downloads'] ? "'".(int) $data['max_downloads']."'" : 'NULL').",
                  ".(isset($data['shared']) ? "shared = ".(int) $data['shared'].", " : '')."
                  expire_days = ".((int) $data['expire_days'] ? "'".(int) $data['expire_days']."'" : 'NULL').",
                  sort_order = '".(int) $data['sort_order']."',
                  activate = '".$this->db->escape($data['activate'])."',
                  activate_order_status_id = '".$this->db->escape(serialize($data['activate_order_status_id']))."',
                  status = '".(int) $data['status']."',
                  date_added = NOW()"
        );

        $download_id = $this->db->getLastId();

        $this->language->replaceDescriptions(
            'download_descriptions',
            ['download_id' => (int) $download_id],
            [
                $this->language->getContentLanguageID() => [
                        'name' => $data['name'
                    ]
                ]
            ]
        );

        $this->addDownloadAttributeValues($download_id, $data['attributes'][0]);
        // assign download to product
        if (isset($data['product_id'])) {
            $this->mapDownload($download_id, $data['product_id']);
        }

        return $download_id;
    }

    /**
     * @param int $download_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function editDownload($download_id, $data)
    {
        if (!(int) $download_id || !$data) {
            return false;
        }
        $fields = [
            'filename'                 => 'string',
            'mask'                     => 'string',
            'max_downloads'            => 'int',
            'shared'                   => 'int',
            'expire_days'              => 'int',
            'sort_order'               => 'int',
            'activate'                 => 'string',
            'activate_order_status_id' => 'string',
            'status'                   => 'int',
        ];

        if (isset($data['activate'])) {
            if ($data['activate'] != 'order_status') {
                $data['activate_order_status_id'] = 0;
            }
            if ($data['activate'] == 'before_order') {
                $data['expire_days'] = 0;
                $data['max_downloads'] = 0;
            }
        }
        $update = [];
        foreach ($fields as $field_name => $type) {
            if (isset($data[$field_name])) {
                if ($type == 'string') {
                    if($field_name == 'activate_order_status_id'){
                        $update[] = "`".$field_name."` = '".$this->db->escape(serialize($data[$field_name]))."'";
                    }else {
                        $update[] = "`".$field_name."` = '".$this->db->escape($data[$field_name])."'";
                    }
                } elseif ($type == 'int') {
                    if (in_array($field_name, ['max_downloads', 'expire_days'])) {
                        $update[] = "`".$field_name."` = ".((int) $data[$field_name]
                                                                ? "'".(int) $data[$field_name]."'"
                                                                : 'NULL');
                    } else {
                        $update[] = "`".$field_name."` = '".(int) $data[$field_name]."'";
                    }
                }
            }
        }
        if ($update) {
            $this->db->query(
                "UPDATE ".$this->db->table('downloads')."
                SET ".implode(', ', $update)."
                WHERE download_id = ".(int) $download_id
            );
        }

        if (!empty($data['name'])) {
            $this->language->replaceDescriptions(
                'download_descriptions',
                ['download_id' => (int) $download_id],
                [$this->language->getContentLanguageID() => ['name' => $data['name']]]
            );
        }

        if (!empty($data['attributes'])) {
            $this->editDownloadAttributes($download_id, $data['attributes'][$download_id]);
        }

        if (isset($data['product_id'])) {
            $this->mapDownload($download_id, $data['product_id']);
        }
        return true;
    }

    /**
     * @param int $download_id
     * @param int $product_id
     *
     * @return bool|int
     * @throws AException
     */
    public function mapDownload($download_id, $product_id)
    {
        $download_id = (int) $download_id;
        $product_id = (int) $product_id;
        if (!$product_id || !$download_id) {
            return false;
        }

        $this->db->query(
            "DELETE FROM ".$this->db->table('products_to_downloads')."
            WHERE product_id = '".(int) $product_id."'
                AND download_id = '".(int) $download_id."'"
        );

        $this->db->query(
            "INSERT INTO ".$this->db->table('products_to_downloads')."
            SET
                product_id = '".(int) $product_id."',
                download_id = '".(int) $download_id."'"
        );

        return $this->db->getLastId();
    }

    /**
     * @param int $download_id
     * @param int $product_id
     *
     * @return bool
     * @throws AException
     */
    public function unmapDownload($download_id, $product_id)
    {
        $download_id = (int) $download_id;
        $product_id = (int) $product_id;
        if (!$product_id || !$download_id) {
            return false;
        }

        $this->db->query(
            "DELETE FROM ".$this->db->table('products_to_downloads')."
             WHERE product_id = '".(int) $product_id."'
               AND download_id = '".(int) $download_id."'"
        );
        return true;
    }

    /**
     * @param int $product_id
     *
     * @return bool
     * @throws AException
     */
    public function unmapDownloadsFromProduct($product_id)
    {
        $product_id = (int) $product_id;
        if (!$product_id) {
            return false;
        }

        $this->db->query(
            "DELETE FROM ".$this->db->table('products_to_downloads')."
             WHERE product_id = '".(int) $product_id."'"
        );
        return true;
    }

    /**
     * @param int $download_id
     *
     * @param array $exclude_products
     *
     * @return array
     * @throws AException
     */
    public function getDownloadMapList($download_id, $exclude_products = [])
    {
        $download_id = (int) $download_id;
        if (!$download_id) {
            return [];
        }
        $output = [];

        $result = $this->db->query(
            "SELECT  pd.product_id, pd.name
            FROM ".$this->db->table('products_to_downloads')." ptd
            LEFT JOIN ".$this->db->table('product_descriptions')." pd
            ON (pd.product_id = ptd.product_id 
               AND pd.language_id = '".$this->language->getContentLanguageID()."')
            WHERE ptd.download_id = '".(int) $download_id."'"
        );
        foreach ($result->rows as $row) {
            if (!in_array($row['product_id'], $exclude_products)) {
                $output[$row['product_id']] = $row['name'];
            }
        }
        return $output;
    }

    /**
     * @param int $download_id
     *
     * @throws AException
     */
    public function deleteDownload($download_id)
    {
        $download_id = (int) $download_id;
        if (!$download_id) {
            return;
        }

        $download = $this->getDownload($download_id);
        if (!$download) {
            return;
        }

        $rm = new AResourceManager();
        $rm->setType('download');
        $rTypeDir = $rm->getTypeDir();
        $resourceId = is_numeric($download['filename'])
            ? $download['filename']
            : $rm->getIdFromHexPath(str_replace($rTypeDir, '', $download['filename']));
        if ($resourceId && !$rm->isMapped($resourceId)) {
            $rm->deleteResource($resourceId);
        }

        $this->db->query(
            "DELETE FROM ".$this->db->table("downloads")." 
            WHERE download_id = '".(int) $download_id."'"
        );
        $this->db->query(
            "DELETE FROM ".$this->db->table("download_descriptions")." 
            WHERE download_id = '".(int) $download_id."'"
        );
        $this->db->query(
            "DELETE FROM ".$this->db->table("download_attribute_values")." 
            WHERE download_id = '".(int) $download_id."'"
        );
        $this->db->query(
            "DELETE FROM ".$this->db->table("products_to_downloads")." 
            WHERE download_id = '".(int) $download_id."'"
        );
    }

    /**
     * @param int $download_id
     *
     * @return array
     * @throws AException
     */
    public function getDownload($download_id)
    {
        $download_id = (int) $download_id;
        if (!$download_id) {
            return [];
        }

        $query = $this->db->query(
            "SELECT d.download_id,
                  dc.name,
                  filename,
                  mask,
                  max_downloads,
                  expire_days,
                  sort_order,
                  activate,
                  activate_order_status_id,
                  status,
                  shared,
                  date_added,
                  date_modified
            FROM ".$this->db->table('downloads')." d
            LEFT JOIN ".$this->db->table('download_descriptions')." dc
                ON d.download_id=dc.download_id 
                    AND dc.language_id = '".(int) $this->language->getContentLanguageID()."'
            WHERE d.download_id = '".(int) $download_id."'"
        );
        return $query->row;
    }

    /**
     * @param int $product_id
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getProductDownloadsDetails($product_id, $data = [])
    {
        if (!(int) $product_id) {
            return [];
        }
        $sql = "SELECT dd.*, d.*, p2d.*
                 FROM ".$this->db->table("products_to_downloads")." p2d
                 LEFT JOIN ".$this->db->table("downloads")." d ON (p2d.download_id = d.download_id)
                 LEFT JOIN ".$this->db->table("download_descriptions")." dd
                    ON (d.download_id = dd.download_id
                            AND dd.language_id = '".(int) $this->language->getContentLanguageID()."')
                 WHERE p2d.product_id = '".(int) $product_id."'";
        if (!empty($data['subsql_filter'])) {
            $sql .= " ".$data['subsql_filter'];
        }
        $sql .= " ORDER BY d.sort_order ASC";
        $query = $this->db->query($sql);

        foreach ($query->rows as &$row) {
            $row['map_list'] = $this->getDownloadMapList($row['download_id'], [$row['product_id']]);
        }

        return $query->rows;
    }

    /**
     * @param array $data
     * @param string $mode
     *
     * @return array
     * @throws AException
     */
    public function getDownloads($data = [], $mode = 'default')
    {
        if (!empty($data['content_language_id'])) {
            $language_id = ( int ) $data['content_language_id'];
        } else {
            $language_id = (int) $this->language->getContentLanguageID();
        }

        if ($mode == 'total_only') {
            $total_sql = 'count(*) as total';
        } else {
            $total_sql = 'dd.*, d.*, 
                            (SELECT COUNT(*) as cnt 
                            FROM '.$this->db->table("products_to_downloads").' ptd 
                            WHERE ptd.download_id = d.download_id) as product_count';
        }

        $sql = "SELECT $total_sql
                FROM ".$this->db->table("downloads")." d
                LEFT JOIN ".$this->db->table("download_descriptions")." dd
                    ON (d.download_id = dd.download_id AND dd.language_id = '".$language_id."')";

        if (!empty($data['subsql_filter'])) {
            $sql .= " WHERE ".$data['subsql_filter'];
        }

        //If for total, we done building the query
        if ($mode == 'total_only') {
            $query = $this->db->query($sql);
            return $query->row['total'];
        }

        $sort_data = [
            'name'          => 'dd.name',
            'product_count' => 'product_count',
        ];

        if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
            $sql .= " ORDER BY ".$data['sort'];
        } else {
            $sql .= " ORDER BY dd.name";
        }

        if (isset($data['order']) && (strtoupper($data['order']) == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT ".(int) $data['start'].",".(int) $data['limit'];
        }
        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * @return array
     * @throws AException
     */
    public function getSharedDownloads()
    {
        return $this->getDownloads(['subsql_filter' => ' shared=1 ']);
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getTotalDownloads($data = [])
    {
        return $this->getDownloads($data, 'total_only');
    }

    /**
     * @param int $download_id
     *
     * @return array
     * @throws AException
     */
    public function getDownloadDescriptions($download_id)
    {
        $download_id = (int) $download_id;
        $download_description_data = [];

        $query = $this->db->query(
            "SELECT *
            FROM ".$this->db->table("download_descriptions")." 
            WHERE download_id = '".(int) $download_id."'"
        );

        foreach ($query->rows as $result) {
            $download_description_data[$result['language_id']] = ['name' => $result['name']];
        }

        return $download_description_data;
    }

    /**
     * @param int $download_id
     * @param array $data
     *
     * @throws AException
     */
    public function addDownloadAttributeValues($download_id, $data)
    {
        $download_id = (int) $download_id;
        $attr_mngr = new AAttribute_Manager('download_attribute');
        $attribute_info = $attr_mngr->getAttributeTypeInfo('download_attribute');
        $attributes = $attr_mngr->getAttributes(
            [
                'attribute_type_id' => $attribute_info['attribute_type_id'],
                'limit'             => null,
            ]
        );

        foreach ($attributes as $attribute) {
            if (isset($data[$attribute['attribute_id']])) {
                $value = serialize($data[$attribute['attribute_id']]);
                $this->db->query(
                    "INSERT INTO ".$this->db->table('download_attribute_values')." (attribute_id, download_id, attribute_value_ids)
                     VALUES ('".$attribute['attribute_id']."', '".$download_id."', '".$value."')"
                );
            }
        }
    }

    /**
     * @param int $download_id
     * @param array $data
     *
     * @throws AException
     */
    public function editDownloadAttributes($download_id, $data)
    {
        $download_id = (int) $download_id;
        $attr_mngr = new AAttribute_Manager('download_attribute');
        $attribute_info = $attr_mngr->getAttributeTypeInfo('download_attribute');
        $attributes = $attr_mngr->getAttributes(
            [
                'attribute_type_id' => $attribute_info['attribute_type_id'],
                'limit'             => null,
            ]
        );

        foreach ($attributes as $attribute) {
            if (isset($data[$attribute['attribute_id']])) {
                $value = serialize($data[$attribute['attribute_id']]);

                $this->db->query(
                    "DELETE
                   FROM ".$this->db->table('download_attribute_values')."
                   WHERE attribute_id = '".$attribute['attribute_id']."' 
                        AND download_id = '".$download_id."'"
                );

                $this->db->query(
                    "INSERT INTO ".$this->db->table('download_attribute_values')."
                    SET attribute_value_ids = '".$this->db->escape($value)."',
                        attribute_id = '".$attribute['attribute_id']."',
                        download_id = '".$download_id."'"
                );
            }
        }
    }

    /**
     * @param int $download_id
     *
     * @return array
     * @throws AException
     */
    public function getDownloadAttributes($download_id)
    {
        $download_id = (int) $download_id;
        $attr_mngr = new AAttribute_Manager('download_attribute');
        $attribute_info = $attr_mngr->getAttributeTypeInfo('download_attribute');
        $attributes = $attr_mngr->getAttributes(
            [
                'attribute_type_id' => $attribute_info['attribute_type_id'],
                'sort'              => 'sort_order',
                'order'             => 'ASC',
                'limit'             => null,
            ]
        );

        $output = $ids = [];
        foreach ($attributes as $attribute) {
            $ids[] = (int) $attribute['attribute_id'];
            $attribute['values'] = $attr_mngr->getAttributeValues($attribute['attribute_id']);
            $output[$attribute['attribute_id']] = $attribute;
        }
        if ($ids) {
            $result = $this->db->query(
                "SELECT attribute_id, attribute_value_ids as value
                FROM ".$this->db->table('download_attribute_values')."
                WHERE attribute_id IN (".implode(',', $ids).") 
                    AND download_id = '".$download_id."'"
            );

            foreach ($result->rows as $row) {
                if (isset($output[$row['attribute_id']])) {
                    $output[$row['attribute_id']]['selected_values'] = unserialize($row['value']);
                }
            }
        }
        return $output;
    }

    /**
     * @param int $order_download_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function editOrderDownload($order_download_id, $data)
    {
        if (!(int) $order_download_id) {
            return false;
        }
        $update = [];

        if (isset($data['expire_date'])) {
            if ($data['expire_date']) {
                $expire = "'".$this->db->escape($data['expire_date'])."'";
            } else {
                $expire = 'NULL';
            }
            $update[] = "`expire_date` = ".$expire;
        }
        if (isset($data['remaining_count'])) {
            if ($data['remaining_count'] != '') {
                $update[] = "`remaining_count` = '".(int) $data['remaining_count']."'";
            } else {
                $update[] = "`remaining_count` = NULL";
            }
        }
        if (has_value($data['status'])) {
            $update[] = "`status` = '".(int) $data['status']."'";
        }
        if ($update) {
            $this->db->query(
                "UPDATE ".$this->db->table("order_downloads")." 
                SET ".implode(', ', $update)."
                WHERE order_download_id='".(int) $order_download_id."'"
            );
        }
        return true;
    }

    /**
     * @param  int       $product_id
     * @param int|string $download_id (optional)
     *
     * @return array
     * @throws AException
     */
    public function getOrdersWithProduct($product_id, $download_id = '')
    {
        if (!(int) $product_id) {
            return [];
        }
        if ($download_id) {
            $sql = "SELECT DISTINCT op.order_id, op.order_product_id
                    FROM ".$this->db->table('order_products')." op, 
                         ".$this->db->table('order_downloads')." od
                    WHERE  od.order_id = op.order_id 
                        AND op.product_id = '".(int) $product_id."' 
                        AND od.download_id='".(int) $download_id."'";
        } else {
            $sql = "SELECT DISTINCT op.order_id, op.order_product_id
                    FROM ".$this->db->table('order_products')." op 
                    WHERE  op.product_id = '".(int) $product_id."'";
        }

        $result = $this->db->query($sql);
        return $result->rows;
    }

    /**
     * @param int $product_id
     * @param int|string $download_id
     *
     * @return int
     * @throws AException
     */
    public function getTotalOrdersWithProduct($product_id, $download_id = '')
    {
        $product_id = (int) $product_id;
        $download_id = (int) $download_id;
        return sizeof($this->getOrdersWithProduct($product_id, $download_id));
    }

    /**
     * @param $download_info
     *
     * @return array
     * @throws AException
     */
    public function getTextStatusForOrderDownload($download_info)
    {
        $text_status = [];

        if (dateISO2Int($download_info['expire_date']) < time()) {
            $text_status[] = $this->language->get('text_download_expired');
        }

        if ($download_info['remaining_count'] == '0') {
            $text_status[] = $this->language->get('text_download_remaining_count').': 0';
        }
        //exclude "immediate" order status id (0)
        $activateStatuses = array_filter((array) unserialize($download_info['activate_order_status_id']));
        if ($activateStatuses) {
            $names = [];
            if (!in_array((int) $download_info['order_status_id'], $activateStatuses)) {
                $this->load->model('localisation/order_status');
                foreach ($activateStatuses as $id){
                    $names[] = $this->model_localisation_order_status->getOrderStatus($id)['name'];
                }
                $text_status[] = sprintf(
                    $this->language->get('text_order_status_required'),
                    implode(',',$names)
                );
            }
        }

        // check is file exists
        $download_info['filename'] = trim($download_info['filename']);
        if (!$this->download->isFileAvailable($download_info['filename'])) {
            $text_status[] = $this->language->get('text_missing_file');
        }

        return $text_status;
    }

}
