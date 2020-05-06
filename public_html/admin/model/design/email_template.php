<?php

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelDesignEmailTemplate extends Model
{
    public static function getEmailTemplates(array $data)
    {
        $db = Registry::getInstance()->get('db');
        $language = Registry::getInstance()->get('language');

        $etTableName = $db->table('email_templates');
        $langTableName = $db->table('languages');

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
            foreach ((array)$filters['rules'] as $filter) {
                if (!$allowedSearchFields[$filter['field']]) {
                    continue;
                }
                $arWhere[] = $allowedSearchFields[$filter['field']].' LIKE \'%'.$db->escape($filter['data']).'%\'';

            }
        }


        $arWhere[] = $allowedSearchFields['store_id'].'='.($data['store_id'] ?: '0');
        $arWhere[] = $etTableName.'.language_id'.'='.$language->getContentLanguageID();


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
        if (isset($data['rows']) && (int)$data['rows'] <= 50) {
            $limit = (int)$data['rows'];
        }

        $page = isset($data['page']) ? (int)$data['page'] : 1;
        $start = $page * $limit - $limit;

        $query .= " LIMIT ".$start.",".$limit;

        $result = $db->query($query);

        $total = $db->query('select found_rows() as total')->row['total'];

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

    public static function update($id, $data)
    {
        if (!(int)$id) {
            return false;
        }
        $db = Registry::getInstance()->get('db');
        $etTableName = $db->table('email_templates');

        $arUpdate = [];
        foreach ($data as $key => $val) {
            $arUpdate[] = $key.'=\''.$db->escape($val).'\'';
        }
        if (empty($arUpdate)) {
            return false;
        }

        $query = 'UPDATE '.$etTableName.' SET '.implode(',', $arUpdate).' WHERE id='.$id;
        $db->query($query);

    }

    public static function getById($id)
    {
        if (!(int)$id) {
            return false;
        }
        $db = Registry::getInstance()->get('db');
        $etTableName = $db->table('email_templates');

        $query = 'SELECT * FROM '.$etTableName.' WHERE id='.$id;
        $result = $db->query($query);
        if ($result) {
            return $result->rows[0];
        }
        return false;
    }

    public static function getByTextIdAndLanguageId($textId, $languageId)
    {
        if (!$textId || !(int)$languageId) {
            return false;
        }
        $db = Registry::getInstance()->get('db');
        $storeId = Registry::getInstance()->get('config')->get('config_store_id');

        $etTableName = $db->table('email_templates');
        $query = 'SELECT * FROM '.$etTableName.' WHERE text_id=\''.$textId.'\' AND language_id='.(int)$languageId.' AND store_id='.$storeId;
        $result = $db->query($query);
        if ($result && $result->num_rows > 0) {
            return $result->rows[0];
        }
        return false;
    }

    public static function insert($data)
    {
        $db = Registry::getInstance()->get('db');
        $etTableName = $db->table('email_templates');
        $keys = array_keys($data);
        $values = array_values($data);

        foreach ($values as &$value) {
            $value = $db->escape($value);
        }

        $query = 'INSERT INTO '.$etTableName.' ('.implode(',', $keys).') VALUES (\''.implode('\',\'', $values).'\')';
        if ($db->query($query)) {
            return self::getByTextIdAndLanguageId($data['text_id'], $data['language_id']);
        }
    }

    public function copyToNewStore($oldStoreId, $newStoreId) {
        if (!(int)$newStoreId) {
            return false;
        }
        $etTableName = $this->db->table('email_templates');
        $sql = 'SELECT * FROM '.$etTableName.' WHERE store_id='.$oldStoreId;
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            foreach ($result->rows as $row) {
                $row['store_id'] = $newStoreId;
                $this->db->query('INSERT INTO '.$etTableName.'(`status`, `text_id`, `language_id`, `headers`, `subject`, `html_body`, `text_body`, `allowed_placeholders`, `store_id`)'.
                    ' VALUES ('.$row['status'].', \''.$row['text_id'].'\', '.$row['language_id'].', \''.$row['headers'].'\', \''.$row['subject'].'\', \''.$row['html_body'].'\', \''.$row['text_body'].'\', \''.$row['allowed_placeholders'].'\', '.$row['store_id'].')');
            }
        }
    }

    public static function delete($id)
    {
        if (!(int)$id) {
            return false;
        }
        $db = Registry::getInstance()->get('db');
        $etTableName = $db->table('email_templates');
        $query = 'DELETE FROM '.$etTableName.' WHERE id='.$id;
        $db->query($query);
    }

}
