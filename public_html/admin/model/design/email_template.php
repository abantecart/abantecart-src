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
        $etTableName = $this->db->table('email_templates');
        $langTableName = $this->db->table('languages');

        $query = 'SELECT SQL_CALC_FOUND_ROWS '.$etTableName.'.id, 
         '.$etTableName.'.status, 
         '.$etTableName.'.language_id,
         '.$etTableName.'.text_id,
         '.$etTableName.'.headers,
         '.$etTableName.'.subject,
         '.$langTableName.'.name ';

        $query .= ' FROM '.$etTableName;

        $query .= ' LEFT JOIN '.$langTableName.' ON ('.$langTableName.'.language_id = '.$etTableName.'.language_id)';

        $allowedSearchFields = [
            'text_id'  => $etTableName.'.text_id',
            'language' => $langTableName.'.name',
            'subject'  => $etTableName.'.subject',
            'store_id' => $etTableName.'.store_id',
        ];

        $arWhere = [];
        if (isset($data['_search']) && $data['_search'] == 'true') {
            $filters = json_decode(htmlspecialchars_decode($data['filters']), true);
            foreach ((array) $filters['rules'] as $filter) {
                if (!$allowedSearchFields[$filter['field']]) {
                    continue;
                }
                $arWhere[] = $allowedSearchFields[$filter['field']]
                    .' LIKE \'%'.$this->db->escape($filter['data']).'%\'';
            }
        }

        $arWhere[] = $allowedSearchFields['store_id'].'='.($data['store_id'] ? : '0');
        $arWhere[] = $etTableName.'.language_id'.'='.$this->language->getContentLanguageID();

        if (!empty($arWhere)) {
            $query .= ' WHERE '.implode(' AND ', $arWhere);
        }

        $allowedSortFields = [
            'text_id'  => $etTableName.'.text_id',
            'language' => $langTableName.'.name',
            'subject'  => $etTableName.'.subject',
            'status'   => $etTableName.'.status',
        ];

        if (isset($data['sidx']) && isset($data['sord']) && $allowedSortFields[$data['sidx']]) {
            $query .= ' ORDER BY '.$allowedSortFields[$data['sidx']].' '.$data['sord'];
        }

        $limit = 20;
        if (isset($data['rows']) && (int) $data['rows'] <= 50) {
            $limit = (int) $data['rows'];
        }

        $page = isset($data['page']) ? (int) $data['page'] : 1;
        $start = $page * $limit - $limit;

        $query .= " LIMIT ".$start.",".$limit;
        $result = $this->db->query($query);
        $total = $this->db->query('select found_rows() as total')->row['total'];

        if ($result) {
            return [
                'items' => $result->rows,
                'total' => $total,
                'page'  => $page,
                'limit' => $limit,
            ];
        }
        return [
            'items' => [],
            'total' => 0,
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
    public function update($id, $data)
    {
        if (!(int) $id) {
            return false;
        }
        $etTableName = $this->db->table('email_templates');

        $arUpdate = [];
        foreach ($data as $key => $val) {
            $arUpdate[] = $key.'=\''.$this->db->escape($val).'\'';
        }
        if (empty($arUpdate)) {
            return false;
        }

        $query = 'UPDATE '.$etTableName.' SET '.implode(',', $arUpdate).' WHERE id='.$id;
        $this->db->query($query);
        return true;
    }

    /**
     * @param int $id
     *
     * @return false|array
     * @throws AException
     */
    public function getById($id)
    {
        if (!(int) $id) {
            return false;
        }
        $etTableName = $this->db->table('email_templates');

        $query = 'SELECT * FROM '.$etTableName.' WHERE id='.$id;
        $result = $this->db->query($query);
        if ($result) {
            return $result->row;
        }
        return false;
    }

    /**
     * @param string $textId
     * @param int $languageId
     *
     * @return false|array
     * @throws AException
     */
    public function getByTextIdAndLanguageId($textId, $languageId)
    {
        if (!$textId || !(int) $languageId) {
            return false;
        }
        $storeId = $this->config->get('current_store_id');

        $etTableName = $this->db->table('email_templates');
        $query = 'SELECT * 
                    FROM '.$etTableName.' 
                    WHERE text_id=\''.$textId.'\' 
                        AND language_id='.(int) $languageId.' 
                        AND store_id='.(int) $storeId;
        $result = $this->db->query($query);
        if ($result->num_rows) {
            return $result->row;
        }
        return false;
    }

    /**
     * @param array $data
     *
     * @return false|array
     * @throws AException
     */
    public function insert($data)
    {
        $etTableName = $this->db->table('email_templates');
        $keys = array_keys($data);
        $values = array_values($data);

        foreach ($values as &$value) {
            $value = $this->db->escape($value);
        }

        $query = 'INSERT INTO '.$etTableName.' ('.implode(',', $keys).') 
                  VALUES (\''.implode('\',\'', $values).'\')';
        if ($this->db->query($query)) {
            return $this->getByTextIdAndLanguageId($data['text_id'], $data['language_id']);
        }
        return false;
    }

    /**
     * @param int $oldStoreId
     * @param int $newStoreId
     *
     * @return bool
     * @throws AException
     */
    public function copyToNewStore($oldStoreId, $newStoreId)
    {
        if (!(int) $newStoreId) {
            return false;
        }
        $etTableName = $this->db->table('email_templates');
        $sql = 'SELECT * 
                FROM '.$etTableName.' 
                WHERE store_id='.$oldStoreId;
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            foreach ($result->rows as $row) {
                $row['store_id'] = $newStoreId;
                $this->db->query(
                    "INSERT INTO ".$etTableName." 
                        (`status`, `text_id`, `language_id`, `headers`, `subject`, `html_body`, `text_body`, `allowed_placeholders`, `store_id`) 
                        VALUES (
                        ".(int) $row['status'].", 
                        '".$row['text_id']."', 
                        ".(int) $row['language_id'].", 
                        '".$row['headers']."', 
                        '".$row['subject']."', 
                        '".$row['html_body']."', 
                        '".$row['text_body']."', 
                        '".$row['allowed_placeholders']."', 
                        ".$row['store_id'].")"
                );
            }
        }
        return true;
    }

    /**
     * @param int $id
     *
     * @return boolean
     * @throws AException
     */
    public function delete($id)
    {
        if (!(int) $id) {
            return false;
        }
        $etTableName = $this->db->table('email_templates');
        $query = 'DELETE FROM '.$etTableName.' WHERE id='.$id;
        $this->db->query($query);
        return true;
    }
}
