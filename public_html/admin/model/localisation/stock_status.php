<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelLocalisationStockStatus extends Model
{
    /**
     * @param array $data
     * @return int
     * @throws AException
     */
    public function addStockStatus(array $data)
    {
        if (!$data['stock_status']['name']) {
            return false;
        }
        $data['language_id'] = (int)$data['language_id'] ?: $this->language->getContentLanguageID();
        $result = $this->db->query(
            "SELECT MAX(stock_status_id) as max_id 
            FROM " . $this->db->table("stock_statuses")
        );
        $stockStatusId = (int)$result->row['max_id'] + 1;
        $this->language->replaceDescriptions('stock_statuses',
            [
                'stock_status_id' => $stockStatusId,
                'language_id'     => $data['language_id'],
            ],
            [
                $data['language_id'] => [
                    'name' => $data['stock_status']['name'],
                ],
            ]);
        $this->cache->remove('localization');

        return $stockStatusId;
    }

    public function editStockStatus($stockStatusId, $data)
    {
        $data['language_id'] = (int)$data['language_id'] ?: $this->language->getContentLanguageID();
        $this->language->replaceDescriptions('stock_statuses',
            [
                'stock_status_id' => (int)$stockStatusId,
                'language_id'     => $data['language_id'],
            ],
            [
                $data['language_id'] => [
                    'name' => $data['stock_status']['name'],
                ],
            ]
        );

        $this->cache->remove('localization');
    }

    /**
     * @param int $stockStatusId
     * @return void
     * @throws AException
     */
    public function deleteStockStatus($stockStatusId = 0)
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table("stock_statuses") . " 
			WHERE stock_status_id = '" . (int)$stockStatusId . "'"
        );
        $this->cache->remove('localization');
    }

    /**
     * @param int $stockStatusId
     * @param int $languageId
     * @return array
     * @throws AException
     */
    public function getStockStatus($stockStatusId, int $languageId = 0)
    {
        $languageId = $languageId ?: (int)$this->config->get('storefront_language_id');
        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("stock_statuses") . " 
            WHERE stock_status_id = '" . (int)$stockStatusId . "'
                AND language_id = '" . $languageId . "'"
        );

        return $query->row;
    }

    public function getStockStatuses($data = [])
    {

        $language_id = (int)$data['content_language_id'] ?: $this->language->getContentLanguageID();

        if ($data) {
            $sql = "SELECT * FROM " . $this->db->table("stock_statuses") . " WHERE language_id = '" . $language_id . "'";

            $sql .= " ORDER BY name";

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
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

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }
            $query = $this->db->query($sql);
            return $query->rows;
        } else {
            $cache_key = 'localization.stock_status.lang_' . $language_id;
            $stock_status_data = $this->cache->pull($cache_key);

            if ($stock_status_data === false) {
                $query = $this->db->query("SELECT stock_status_id, name
											FROM " . $this->db->table("stock_statuses") . " 
											WHERE language_id = '" . $language_id . "'
											ORDER BY name");
                $stock_status_data = $query->rows;
                $this->cache->push($cache_key, $stock_status_data);
            }

            return $stock_status_data;
        }
    }

    /**
     * @param int $stock_status_id
     * @param int $language_id
     * @return array
     * @throws AException
     */
    public function getStockStatusDescriptions(int $stock_status_id, int $language_id)
    {
        $query = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table("stock_statuses") . " 
            WHERE stock_status_id = '" . $stock_status_id . "' AND language_id = '" . $language_id . "' "
        );
        return $query->row;
    }

    public function getTotalStockStatuses()
    {
        $query = $this->db->query(
            "SELECT COUNT(DISTINCT stock_status_id) AS total 
            FROM " . $this->db->table("stock_statuses")
        );
        return (int)$query->row['total'];
    }
}