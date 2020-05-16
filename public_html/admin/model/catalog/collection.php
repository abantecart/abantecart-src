<?php

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelCatalogCollection extends Model
{
    public function insert($data)
    {
        $db = Registry::getInstance()->get('db');
        $language = Registry::getInstance()->get('language');

        $colTableName = $db->table('collections');

        if (isset($data['condition_object'])) {
            unset($data['condition_object']);
        }

        if (isset($data['keyword'])) {
            $keyword = $data['keyword'];
            unset($data['keyword']);
        }

        $descriptionData = [
            'language_id' => $language->getContentLanguageID(),
        ];
        if (isset($data['title'])) {
            $descriptionData['title'] = $data['title'];
            unset($data['title']);
        }

        if (isset($data['meta_keywords'])) {
            $descriptionData['meta_keywords'] = $data['meta_keywords'];
            unset($data['meta_keywords']);
        }

        if (isset($data['meta_description'])) {
            $descriptionData['meta_description'] = $data['meta_description'];
            unset($data['meta_description']);
        }

        $keys = array_keys($data);
        $values = array_values($data);

        foreach ($values as &$value) {
            if (is_array($value)) {
                $value = json_encode($value);
            } else {
                $value = $db->escape($value);
            }
        }

        $query = 'INSERT INTO '.$colTableName.' ('.implode(',', $keys).') VALUES (\''.implode('\',\'', $values).'\')';
        $result = $db->query($query);
        if ($result) {
            $lastId = $db->query('SELECT LAST_INSERT_ID() as last_id;');
            $lastId = $lastId->row['last_id'];

            $descriptionData['collection_id'] = (int)$lastId;

            $this->updateOrCreateDescription($descriptionData);

            if ($keyword) {
                $seo_key = SEOEncode($keyword, 'collection_id', $lastId);
            } else {
                //Default behavior to save SEO URL keyword from ccollection name in default language
                /**
                 * @var ALanguageManager
                 */
                $seo_key = SEOEncode($descriptionData['title'] ?: $data['name'],
                    'collection_id',
                    $lastId);
            }
            if ($seo_key) {
                $language->replaceDescriptions('url_aliases',
                    array('query' => "collection_id=".(int)$lastId),
                    array((int)$language->getContentLanguageID() => array('keyword' => $seo_key)));
            } else {
                $db->query('DELETE
							FROM '.$db->table("url_aliases")." 
							WHERE query = 'collection_id=".(int)$lastId."'
								AND language_id = '".(int)$language->getContentLanguageID()."'");
            }

            return $this->getById($lastId);
        }
    }

    public function update($id, $data)
    {
        if (!(int)$id) {
            return false;
        }
        $db = Registry::getInstance()->get('db');
        $language = Registry::getInstance()->get('language');

        $colTableName = $db->table('collections');

        $descriptionData = [
            'language_id'   => $language->getContentLanguageID(),
            'collection_id' => (int)$id,
        ];
        if (isset($data['title'])) {
            $descriptionData['title'] = $data['title'];
            unset($data['title']);
        }

        if (isset($data['meta_keywords'])) {
            $descriptionData['meta_keywords'] = $data['meta_keywords'];
            unset($data['meta_keywords']);
        }

        if (isset($data['meta_description'])) {
            $descriptionData['meta_description'] = $data['meta_description'];
            unset($data['meta_description']);
        }

        if (isset($data['condition_object'])) {
            unset($data['condition_object']);
        }

        if (isset($data['keyword'])) {
            $keyword = $data['keyword'];
            unset($data['keyword']);
        }

        $arUpdate = [];
        foreach ($data as $key => $val) {
            $arUpdate[] = $key.'=\''.(($key != 'conditions') ? $db->escape($val) : json_encode($val)).'\'';
        }


        if (!empty($arUpdate)) {
            $query = 'UPDATE '.$colTableName.' SET '.implode(',', $arUpdate).' WHERE id='.$id;
            $db->query($query);
        }

        if (!empty($descriptionData)) {
            $this->updateOrCreateDescription($descriptionData);
        }

        if (isset($keyword)) {
            $keyword = SEOEncode($keyword);
            if ($keyword) {
                $language->replaceDescriptions('url_aliases',
                    ['query' => 'collection_id='.(int)$id],
                    [(int)$language->getContentLanguageID() => array('keyword' => $keyword)]);
            } else {
                $db->query('DELETE
								FROM '.$db->table('url_aliases')." 
								WHERE query = 'collection_id=".(int)$id."'
									AND language_id = '".(int)$language->getContentLanguageID()."'");
            }

        }
        $this->cache->remove('collection');

    }

    public function delete($id)
    {
        if (!(int)$id) {
            return false;
        }
        $db = Registry::getInstance()->get('db');
        $colTableName = $db->table('collections');
        $query = 'DELETE FROM '.$colTableName.' WHERE id='.$id;
        $db->query($query);
    }

    public function getCollections(array $data)
    {
        $db = Registry::getInstance()->get('db');
        $colTableName = $db->table('collections');

        $query = 'SELECT SQL_CALC_FOUND_ROWS '.$colTableName.'.id, 
         '.$colTableName.'.status, 
         '.$colTableName.'.name,
         '.$colTableName.'.description,
         '.$colTableName.'.store_id,
         '.$colTableName.'.date_added';

        $query .= ' FROM '.$colTableName;

        $allowedSearchFields = [
            'name'     => $colTableName.'.name',
            'store_id' => $colTableName.'.store_id',
            'status'   => $colTableName.'.status',
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

        if (isset($data['status'])) {
            $arWhere[] = $allowedSearchFields['status'].'='.$data['status'];
        }

        $arWhere[] = $allowedSearchFields['store_id'].'='.($data['store_id'] ?: '0');

        if (!empty($arWhere)) {
            $query .= ' WHERE '.implode(' AND ', $arWhere);
        }

        $allowedSortFields = [
            'name'       => $colTableName.'.name',
            'date_added' => $colTableName.'.date_added',
            'status'     => $colTableName.'.status',
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

    public function updateOrCreateDescription($data)
    {
        if (!(int)$data['collection_id'] || !(int)$data['language_id']) {
            return false;
        }
        $db = Registry::getInstance()->get('db');
        $colDesTableName = $db->table('collection_descriptions');

        $exists = $db->query('SELECT id FROM '.$colDesTableName.' WHERE collection_id='.(int)$data['collection_id'].
            ' AND language_id='.(int)$data['language_id']);

        if ($exists->row['id']) {
            $arUpdate = [];
            foreach ($data as $key => $val) {
                if (in_array($key, ['collection_id', 'language_id'])) {
                    continue;
                }
                $arUpdate[] = $key.'=\''.$db->escape($val).'\'';
            }
            if (!empty($arUpdate)) {
                return $db->query('UPDATE '.$colDesTableName.' SET '.implode(',', $arUpdate).' WHERE id='.$exists->row['id']);
            }
            return false;
        }

        $keys = array_keys($data);
        $values = array_values($data);

        foreach ($values as &$value) {
            $value = $db->escape($value);
        }

        return $db->query('INSERT INTO '.$colDesTableName.' ('.implode(',', $keys).') VALUES (\''.implode('\',\'', $values).'\')');
    }

    public function getById($id)
    {
        if (!(int)$id) {
            return false;
        }
        $db = Registry::getInstance()->get('db');
        $language = Registry::getInstance()->get('language');

        $colTableName = $db->table('collections');
        $colDesTableName = $db->table('collection_descriptions');

        $arSelect = [
            $colTableName.'.*',
            $colDesTableName.'.title',
            $colDesTableName.'.meta_keywords',
            $colDesTableName.'.meta_description',
        ];

        $query = 'SELECT '.implode(',', $arSelect).' FROM '.$colTableName;
        $query .= ' LEFT JOIN '.$colDesTableName.' ON '.$colDesTableName.'.collection_id = '.$colTableName.'.id 
        AND '.$colDesTableName.'.language_id = '.$language->getContentLanguageID();
        $query .= ' WHERE '.$colTableName.'.id='.$id;

        $result = $db->query($query);
        if ($result) {
            $data = $result->row;
            $data['conditions'] = json_decode($data['conditions'], true);
            $seo_url = $db->query('SELECT *
								FROM '.$db->table('url_aliases')." 
								WHERE query = 'collection_id=".(int)$id."'
									AND language_id = '".(int)$language->getContentLanguageID()."'");
            if ($seo_url->row) {
                $data['keyword'] = $seo_url->row['keyword'];
            }
            return $data;
        }
        return false;
    }

    public function getUniqueTags()
    {
        $db = Registry::getInstance()->get('db');
        $tagsTableName = $db->table('product_tags');
        $query = 'SELECT tag FROM '.$tagsTableName.' GROUP BY tag';
        $result = $db->query($query);
        if ($result->rows) {
            return $result->rows;
        }
        return false;
    }

    public function getProducts(array $conditions, $sort, $order, $start, $limit, $collectionId)
    {
        $store_id = (int)$this->config->get('config_store_id');
        $language_id = (int)$this->config->get('storefront_language_id');
        $cache_key =
            'collection.listing.products_collection.'.(int)$collectionId.'.store_'.$store_id.'_sort_'.$sort.'_order_'.$order;
        $cache_key .= '_start_'.$start.'_limit_'.$limit.'_lang_'.$language_id;
        $result = $this->cache->pull($cache_key);

        if ($result === false) {

            $result = [
                'items' => [],
                'total' => 0,
                'start' => $start,
                'limit' => $limit,
            ];

            if (!is_array($conditions['relation']) || !is_array($conditions['conditions'])) {
                return $result;
            }

            $relation = $conditions['relation'];
            $conditions = $conditions['conditions'];

            $db = Registry::getInstance()->get('db');
            $language = Registry::getInstance()->get('language');

            $productsTable = $db->table('products');
            $categoriesTable = $db->table('categories');
            $p2sTable = $db->table('products_to_stores');
            $p2cTable = $db->table('products_to_categories');
            $productsTagsTable = $db->table('product_tags');
            $pdTable = $db->table('product_descriptions');
            $manufacturersTable = $db->table('manufacturers');

            $arSelect = [
                'SQL_CALC_FOUND_ROWS '.$productsTable.'.*',
                $this->sqlFinalPriceString(),
                $this->sqlAvgRatingString(),
                $this->sqlReviewCountString(),
                $pdTable.'.name',
                $pdTable.'.blurb',
            ];

            $arWhere = [];
            $arJoins = [
                'INNER JOIN '.$p2sTable.' ON '.$p2sTable.'.product_id='.$productsTable.'.product_id'.
                ' AND '.$p2sTable.'.store_id='.$this->config->get('config_store_id'),
                'LEFT JOIN '.$pdTable.' ON '.$pdTable.'.product_id='.$productsTable.'.product_id'.
                ' AND language_id='.(int)$this->language->getContentLanguageID(),
            ];
            foreach ($conditions as $condition) {
                //Brands filter
                if ($condition['object'] === 'brands' && is_array($condition['value']) && !empty($condition['value'])) {
                    $arWhere[] = 'manufacturer_id '.$this->gerInOperator($condition['operator'], $relation['value']).
                        ' ('.implode(',', $condition['value']).')';
                }
                //Category filter
                if ($condition['object'] === 'categories' && is_array($condition['value']) && !empty($condition['value'])) {
                    $arSelect[] = $p2cTable.'.category_id';
                    $arJoins[] = 'LEFT JOIN '.$p2cTable.' ON '.$p2cTable.'.product_id='.$productsTable.'.product_id';
                    $arWhere[] = 'category_id '.$this->gerInOperator($condition['operator'], $relation['value']).
                        ' ('.implode(',', $condition['value']).')';
                }
                //Products filter
                if ($condition['object'] === 'products' && is_array($condition['value']) && !empty($condition['value'])) {
                    $arWhere[] = $productsTable.'.product_id '.$this->gerInOperator($condition['operator'], $relation['value']).
                        ' ('.implode(',', $condition['value']).')';
                }
                //Product price filter
                if ($condition['object'] === 'product_price' && (int)$condition['value'] > 0) {
                    $arWhere[] = 'price '.$this->gerEqualOperator($condition['operator'], $relation['value']).$condition['value'];
                }
                //Tags filter
                if ($condition['object'] === 'tags' && is_array($condition['value']) && !empty($condition['value'])) {
                    $arSelect[] = $productsTagsTable.'.tag';
                    $arJoins[] = 'LEFT JOIN '.$productsTagsTable.' ON '.$productsTagsTable.'.product_id='.$productsTable.'.product_id'.
                        ' AND '.$productsTagsTable.'.language_id='.(int)$this->language->getContentLanguageID();
                    foreach ($condition['value'] as &$value) {
                        $value = "'".$value."'";
                    }
                    $arWhere[] = 'tag '.$this->gerInOperator($condition['operator'], $relation['value']).
                        ' ('.implode(',', $condition['value']).')';
                }
            }

            $query = 'SELECT '.implode(',', $arSelect).' FROM '.$productsTable;
            foreach ($arJoins as $arJoin) {
                $query .= ' '.$arJoin;
            }

            if (empty($arWhere)) {
                return $result;
            }

            $query .= ' WHERE ('.implode(($relation['if'] == 'any') ? ' OR ' : ' AND ', $arWhere).')';
            $query .= ' AND '.$productsTable.'.status=1';

            $query .= ' GROUP BY '.$productsTable.'.product_id';

            $allowedSort = array(
                'pd.name'       => 'LCASE('.$pdTable.'.name)',
                'p.sort_order'  => $productsTable.'.sort_order',
                'p.price'       => 'final_price',
                'special'       => 'final_price',
                'rating'        => 'rating',
                'date_modified' => $productsTable.'.date_modified',
                'review'        => 'review',
            );

            if ($allowedSort[$sort]) {
                $query .= ' ORDER BY '.$allowedSort[$sort].' '.($order ?: 'ASC');
            } else {
                $query .= ' ORDER BY '.$productsTable.'.date_modified '.($order ?: 'ASC');
            }

            if (isset($start) && $limit) {
                $query .= ' LIMIT '.$start.','.$limit;
            }

            $products = $db->query($query);
            $total = $db->query('select found_rows() as total')->row['total'];

            if ($products) {
                $result['items'] = $products->rows;
                $result['total'] = $total;
            }

            $this->cache->push($cache_key, $result);
        }

        return $result;
    }

    protected function sqlFinalPriceString()
    {
        //special prices
        if (is_object($this->customer) && $this->customer->isLogged()) {
            $customer_group_id = (int)$this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = (int)$this->config->get('config_customer_group_id');
        }

        $p2sp = $this->db->table('product_specials');
        $p = $this->db->table('products');

        $sql = ' ( SELECT '.$p2sp.'.price
                    FROM '.$p2sp.
            ' WHERE '.$p2sp.'.product_id = '.$p.'.product_id'.
            ' AND '.$p2sp.'.customer_group_id = \''.$customer_group_id.'\''.
            ' AND (('.$p2sp.'.date_start = \'0000-00-00\' OR '.$p2sp.'.date_start < NOW())'.
            ' AND ('.$p2sp.'.date_end = \'0000-00-00\' OR '.$p2sp.'.date_end > NOW()))'.
            ' ORDER BY '.$p2sp.'.priority ASC, '.$p2sp.'.price ASC LIMIT 1) ';
        $sql = 'COALESCE( '.$sql.', '.$p.'.price) as final_price';

        return $sql;
    }

    protected function sqlAvgRatingString()
    {
        $rw = $this->db->table('reviews');
        $p = $this->db->table('products');

        $sql = ' ( SELECT AVG('.$rw.'.rating)
                         FROM '.$this->db->table('reviews').' '.$rw.'
                         WHERE '.$p.'.product_id = '.$rw.'.product_id AND status = 1
                         GROUP BY '.$rw.'.product_id 
                 ) AS rating ';
        return $sql;
    }

    protected function sqlReviewCountString()
    {
        $rw = $this->db->table('reviews');
        $p = $this->db->table('products');

        $sql = ' ( SELECT COUNT('.$rw.'.review_id)
                         FROM '.$this->db->table('reviews').' '.$rw.'
                         WHERE '.$p.'.product_id = '.$rw.'.product_id AND status = 1
                         GROUP BY '.$rw.'.product_id 
                 ) AS review ';
        return $sql;
    }

    private function gerEqualOperator($operator, $invert)
    {
        if (($operator == 'eq' && $invert == 'true') || ($operator == 'neq' && $invert == 'false')) {
            return '=';
        }
        if (($operator == 'eq' && $invert == 'false') || ($operator == 'neq' && $invert == 'true')) {
            return '<>';
        }
        if ($operator == 'eqlt' && $invert == 'true') {
            return '<=';
        }
        if ($operator == 'eqlt' && $invert == 'false') {
            return '>';
        }
        if ($operator == 'eqgt' && $invert == 'true') {
            return '>=';
        }
        if ($operator == 'eqgt' && $invert == 'false') {
            return '<';
        }
        if ($operator == 'lt' && $invert == 'true') {
            return '<';
        }
        if ($operator == 'lt' && $invert == 'false') {
            return '>=';
        }
        if ($operator == 'gt' && $invert == 'true') {
            return '>';
        }
        if ($operator == 'gt' && $invert == 'false') {
            return '<=';
        }
        return '=';
    }

    private function gerInOperator($operator, $invert)
    {
        if (($operator == 'in' && $invert == 'true') || ($operator == 'notin' && $invert == 'false')) {
            return 'IN';
        }
        if (($operator == 'in' && $invert == 'false') || ($operator == 'notin' && $invert == 'true')) {
            return 'NOT IN';
        }
        return 'IN';
    }

    public function getCollectionStoresInfo($collection_id)
    {
        $query = $this->db->query("SELECT c.*,
											s.name as store_name,
											ss.`value` as store_url,
											sss.`value` as store_ssl_url
									FROM ".$this->db->table("collections")." c
									LEFT JOIN ".$this->db->table("stores")." s ON s.store_id = c.store_id
									LEFT JOIN ".$this->db->table("settings")." ss
										ON (ss.store_id = c.store_id AND ss.`key`='config_url')
									LEFT JOIN ".$this->db->table("settings")." sss
										ON (sss.store_id = c.store_id AND sss.`key`='config_ssl_url')
									WHERE c.id = '".(int)$collection_id."'");
        return $query->rows;
    }

}
