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
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ModelDesignEmailTemplate
 */
class ModelDesignEmailTemplate extends Model
{
    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getEmailTemplates(array $data)
    {
        $sql = "SELECT " . $this->db->getSqlCalcTotalRows() . " et.id, et.status, 
                            et.language_id, et.text_id, et.headers, et.subject, l.name
                 FROM " . $this->db->table('email_templates') . " as et
                 LEFT JOIN " . $this->db->table('languages') . " as l 
                    ON (l.language_id = et.language_id)";

        $allowedSearchFields = [
            'text_id'  => 'et.text_id',
            'language' => 'l.name',
            'subject'  => 'et.subject',
            'store_id' => 'et.store_id',
        ];

        $arWhere = [];
        if ($data['_search'] === 'true') {
            $filters = json_decode(htmlspecialchars_decode($data['filters']), true);
            foreach ((array)$filters['rules'] as $filter) {
                if (!$allowedSearchFields[$filter['field']]) {
                    continue;
                }
                $arWhere[] = $allowedSearchFields[$filter['field']] . " LIKE '%" . $this->db->escape($filter['data']) . "%'";
            }
        }

        $arWhere[] = $allowedSearchFields['store_id'] . '=' . (int)$data['store_id'];
        $arWhere[] = 'et.language_id' . '=' . $this->language->getContentLanguageID();

        $sql .= ' WHERE ' . implode(' AND ', $arWhere);

        $allowedSortFields = [
            'text_id'  => "et.text_id",
            'language' => "l.name",
            'subject'  => "et.subject",
            'status'   => "et.status",
        ];
        $data['sord'] = in_array(strtolower($data['sord']), ['asc', 'desc']) ? $data['sord'] : 'asc';
        if (isset($data['sidx']) && isset($data['sord']) && $allowedSortFields[$data['sidx']]) {
            $sql .= ' ORDER BY ' . $allowedSortFields[$data['sidx']] . ' ' . $data['sord'];
        }

        $limit = 20;
        if (isset($data['rows']) && (int)$data['rows'] <= 50) {
            $limit = (int)$data['rows'];
        }

        $page = (int)$data['page'] ?? 1;
        $start = $page * $limit - $limit;

        $sql .= " LIMIT " . $start . "," . $limit;
        $result = $this->db->query($sql);
        $total = (int)$this->db->getTotalNumRows();

        return [
            'items' => $result->rows,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
        ];
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function update(int $id, array $data)
    {
        if (!$id || !$data) {
            return false;
        }

        $arUpdate = [];
        foreach ($data as $key => $val) {
            $arUpdate[] = $this->db->escape($key) . " = '" . $this->db->escape($val) . "'";
        }

        $sql = "UPDATE " . $this->db->table('email_templates') . " SET " . implode(',', $arUpdate) . " WHERE id=" . $id;
        $this->db->query($sql);
        return true;
    }

    /**
     * @param int $id
     *
     * @return false|array
     * @throws AException
     */
    public function getById(int $id)
    {
        if (!$id) {
            return false;
        }

        $sql = "SELECT * 
                FROM " . $this->db->table('email_templates') . " 
                WHERE id=" . $id;
        $result = $this->db->query($sql);
        return $result->row ?: false;
    }

    /**
     * @param string $textId
     * @param int $languageId
     *
     * @return false|array
     * @throws AException
     */
    public function getByTextIdAndLanguageId(string $textId, int $languageId)
    {
        if (!$textId || !(int)$languageId) {
            return false;
        }
        $storeId = (int)$this->config->get('current_store_id');
        $sql = "SELECT * 
                  FROM " . $this->db->table('email_templates') . " 
                  WHERE text_id = '" . $this->db->escape($textId) . "' 
                        AND language_id=" . $languageId . " 
                        AND store_id=" . $storeId;
        $result = $this->db->query($sql);
        return $result->row ?: false;
    }

    /**
     * @param array $data
     *
     * @return false|array
     * @throws AException
     */
    public function insert(array $data)
    {
        if (!$data) {
            return false;
        }
        $keys = array_map([$this->db, 'escape'], array_keys($data));
        $values = array_map([$this->db, 'escape'], array_values($data));

        $sql = "INSERT INTO " . $this->db->table('email_templates') . " (" . implode(',', $keys) . ") 
                  VALUES ('" . implode("','", $values) . "')";
        if ($this->db->query($sql)) {
            return $this->getByTextIdAndLanguageId($data['text_id'], $data['language_id']);
        }
        return false;
    }

    /**
     * @param int $srcStoreId
     * @param int $dstStoreId
     *
     * @return bool
     * @throws AException
     */
    public function copyToNewStore(int $srcStoreId, int $dstStoreId)
    {
        if (!$dstStoreId) {
            return false;
        }

        $sql = "INSERT INTO " . $this->db->table('email_templates') . " 
                (`status`, `text_id`,`language_id`,`headers`,`subject`,`html_body`, 
                `text_body`, `allowed_placeholders`,`store_id`)    
            SELECT `status`, `text_id`,`language_id`,`headers`,`subject`,`html_body`,`text_body`,`allowed_placeholders`,
                            " . $dstStoreId . " as store_id            
             FROM " . $this->db->table('email_templates') . "
             WHERE store_id=" . $srcStoreId;
        $this->db->query($sql);
        return true;
    }

    /**
     * @param int $id
     *
     * @return boolean
     * @throws AException
     */
    public function delete(int $id)
    {
        if (!$id) {
            return false;
        }
        $this->db->query("DELETE FROM " . $this->db->table('email_templates') . " WHERE id=" . $id);
        return true;
    }
}
