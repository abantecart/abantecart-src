<?php
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelCatalogCollection extends Model
{
    public static function getById($id)
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
        AND '.$colDesTableName.'.language_id = '.$language->getLanguageID();
        $query .= ' WHERE '.$colTableName.'.id='.$id.' AND '.$colTableName.'.status=1';

        $result = $db->query($query);
        if ($result) {
            $data = $result->row;
            $data['conditions'] = json_decode($data['conditions'], true);
            return $data;
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
            $p2sTable = $db->table('products_to_stores');
            $categoriesTable = $db->table('categories');
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
                ' AND language_id='.(int)$this->config->get('storefront_language_id'),
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
                        ' AND '.$productsTagsTable.'.language_id='.(int)$this->config->get('storefront_language_id');
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

            $query .= ' WHERE '.implode(($relation['if'] == 'any') ? ' OR ' : ' AND ', $arWhere);
            $query .= ' AND '.$productsTable.'.status=1';

            $query .= ' GROUP BY '.$productsTable.'.product_id';

            $allowedSort = array(
                'pd.name'       => 'LCASE('.$pdTable.'.name)',
                'name'          => 'LCASE('.$pdTable.'.name)',
                'p.sort_order'  => $productsTable.'.sort_order',
                'sort_order'  => $productsTable.'.sort_order',
                'p.price'       => 'final_price',
                'price'         => 'final_price',
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

    private static function gerEqualOperator($operator, $invert)
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

    private static function gerInOperator($operator, $invert)
    {
        if (($operator == 'in' && $invert == 'true') || ($operator == 'notin' && $invert == 'false')) {
            return 'IN';
        }
        if (($operator == 'in' && $invert == 'false') || ($operator == 'notin' && $invert == 'true')) {
            return 'NOT IN';
        }
        return 'IN';
    }

    public function getListingBlockProducts($collectionId, $limit)
    {
        if (!(int)$collectionId) {
            return false;
        }
        $collection = self::getById($collectionId);
        if ($collection && $collection['conditions']) {
            $sortOrder = $this->config->get('config_product_default_sort_order');
            list ($sort, $order) = explode('-', $sortOrder);
            $result = $this->getProducts($collection['conditions'], $sort ?: 'date_modified', $order ?: 'DESC', 0, $limit, $collectionId);
            return $result['items'];
        }
    }
}
