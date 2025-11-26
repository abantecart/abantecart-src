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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelLocalisationStockStatus extends Model
{

    /**
     * @param int $stockStatusId
     * @param int $languageId
     * @return array
     * @throws AException
     */
    public function getStockStatus(int $stockStatusId, int $languageId = 0)
    {
        $languageId = $languageId ?: (int)$this->config->get('storefront_language_id');
        $cacheKey = 'localization.stock_status.'.$stockStatusId.'.' . $languageId;
        $output = $this->cache->pull($cacheKey);
        if($output !== false) {
            return $output;
        }
        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("stock_statuses") . " 
            WHERE stock_status_id = '" .$stockStatusId . "'
                AND language_id = '" . $languageId . "'"
        );
        $output = $query->row;
        $this->cache->push($cacheKey, $output);
        return $output;
    }
}