<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2024 Belavier Commerce LLC

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

class ModelCatalogCollection extends Model
{
    /**
     * @param int $id
     *
     * @return array|false
     * @throws AException
     */
    public function getById($id)
    {
        if (!(int)$id) {
            return false;
        }
        $db = $this->db;
        $language = $this->language;

        $colTableName = $db->table('collections');
        $colDesTableName = $db->table('collection_descriptions');

        $arSelect = [
            $colTableName . '.*',
            $colDesTableName . '.title',
            $colDesTableName . '.meta_keywords',
            $colDesTableName . '.meta_description',
        ];

        $query = 'SELECT ' . implode(',', $arSelect) . ' FROM ' . $colTableName;
        $query .= ' LEFT JOIN ' . $colDesTableName . ' 
                        ON ' . $colDesTableName . '.collection_id = ' . $colTableName . '.id 
                            AND ' . $colDesTableName . '.language_id = ' . $language->getLanguageID();
        $query .= ' WHERE ' . $colTableName . '.id=' . $id . ' AND ' . $colTableName . '.status=1';

        $result = $db->query($query);
        if ($result) {
            $data = $result->row;
            if ($data) {
                $data['conditions'] = json_decode($data['conditions'], true);
            }
            return $data;
        }
        return false;
    }

    /**
     * @param array $conditions
     * @param string $sort
     * @param string $order
     * @param int $start
     * @param int $limit
     * @param int $collectionId
     *
     * @return array|false|mixed
     * @throws AException
     */
    public function getProducts(array $conditions, $sort, $order, $start, $limit, $collectionId)
    {
        $store_id = (int)$this->config->get('config_store_id');
        $language_id = (int)$this->config->get('storefront_language_id');
        $cache_key = 'collection.listing.products_collection.' . (int)$collectionId
            . '.store_' . $store_id
            . '_sort_' . $sort
            . '_order_' . $order
            . '_start_' . $start
            . '_limit_' . $limit
            . '_lang_' . $language_id;
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

            $db = $this->db;
            $productsTable = $db->table('products');
            $p2sTable = $db->table('products_to_stores');
            $p2cTable = $db->table('products_to_categories');
            $productsTagsTable = $db->table('product_tags');
            $pdTable = $db->table('product_descriptions');

            $arSelect = [
                $db->getSqlCalcTotalRows() . ' ' . $productsTable . '.*',
                $this->sqlFinalPriceString(),
                $this->sqlAvgRatingString(),
                $this->sqlReviewCountString(),
                $pdTable . '.name',
                $pdTable . '.blurb',
            ];

            $arWhere = [];
            $arJoins = [
                'INNER JOIN ' . $p2sTable . ' ON ' . $p2sTable . '.product_id=' . $productsTable . '.product_id' .
                ' AND ' . $p2sTable . '.store_id=' . $store_id,
                'LEFT JOIN ' . $pdTable . ' ON ' . $pdTable . '.product_id=' . $productsTable . '.product_id' .
                ' AND language_id=' . (int)$this->config->get('storefront_language_id'),
            ];
            foreach ($conditions as $k => $condition) {
                //Brands filter
                if ($condition['object'] === 'brands' && is_array($condition['value']) && !empty($condition['value'])) {
                    $arWhere[] = 'manufacturer_id ' . $this->gerInOperator($condition['operator'], $relation['value']) .
                        ' (' . implode(',', $condition['value']) . ')';
                }
                //Category filter
                if ($condition['object'] === 'categories' && is_array($condition['value']) && $condition['value'])
                {
                    $arSelect[] = "cat" . $k . ".category_id";
                    $arJoins[] = "LEFT JOIN " . $p2cTable . " cat" . $k. " 
                        ON cat" . $k . ".product_id=" . $productsTable . ".product_id";
                    /** @var ModelCatalogCategory $mdl */
                    $mdl = $this->load->model('catalog/category');
                    $allCategoryChildren = [(int)$condition['value']];
                    foreach($condition['value'] as $cId){
                        $allCategoryChildren = array_merge($allCategoryChildren, $mdl->getChildrenIDs((int)$cId));
                    }
                    $allCategoryChildren = array_unique(array_map('intval',$allCategoryChildren));
                    $arWhere[] = " cat" . $k . ".category_id "
                        . $this->gerInOperator($condition['operator'], $relation['value'])
                        . " (" . implode(',', $allCategoryChildren) . ")";
                }
                //Products filter
                if ($condition['object'] === 'products'
                    && is_array($condition['value'])
                    && !empty($condition['value'])
                ) {
                    $arWhere[] = $productsTable . '.product_id '
                        . $this->gerInOperator($condition['operator'], $relation['value'])
                        . ' (' . implode(',', $condition['value']) . ')';
                }
                //Product price filter
                if ($condition['object'] === 'product_price' && (int)$condition['value'] > 0) {
                    $arWhere[] = 'price ' . $this->gerEqualOperator($condition['operator'], $relation['value'])
                        . $condition['value'];
                }
                //Tags filter
                if ($condition['object'] === 'tags' && is_array($condition['value']) && $condition['value']) {
                    $arSelect[] = ' tag' . $k . '.tag';
                    $arJoins[] = 'LEFT JOIN ' . $productsTagsTable . ' tag' . $k . '
                                    ON tag' . $k . '.product_id=' . $productsTable . '.product_id' .
                        ' AND tag' . $k . '.language_id=' . (int)$this->config->get('storefront_language_id');
                    foreach ($condition['value'] as &$value) {
                        $value = "'" . $value . "'";
                    }
                    $arWhere[] = 'tag' . $k . '.tag ' . $this->gerInOperator($condition['operator'], $relation['value']) .
                        ' (' . implode(',', $condition['value']) . ')';
                }
            }

            $query = 'SELECT ' . implode(',', $arSelect) . ' FROM ' . $productsTable;
            foreach ($arJoins as $arJoin) {
                $query .= ' ' . $arJoin;
            }

            if (empty($arWhere)) {
                return $result;
            }

            $query .= ' WHERE ' . implode(($relation['if'] == 'any') ? ' OR ' : ' AND ', $arWhere);
            $query .= ' AND ' . $productsTable . '.status=1';

            $query .= ' GROUP BY ' . $productsTable . '.product_id';

            $allowedSort = [
                'pd.name'       => 'LCASE(' . $pdTable . '.name)',
                'name'          => 'LCASE(' . $pdTable . '.name)',
                'p.sort_order'  => $productsTable . '.sort_order',
                'sort_order'    => $productsTable . '.sort_order',
                'p.price'       => 'final_price',
                'price'         => 'final_price',
                'special'       => 'final_price',
                'rating'        => 'rating',
                'date_modified' => $productsTable . '.date_modified',
                'review'        => 'review',
            ];

            if ($allowedSort[$sort]) {
                $query .= ' ORDER BY ' . $allowedSort[$sort] . ' ' . ($order ?: 'ASC');
            } else {
                $query .= ' ORDER BY ' . $productsTable . '.date_modified ' . ($order ?: 'ASC');
            }

            if (isset($start) && $limit) {
                $query .= ' LIMIT ' . $start . ',' . $limit;
            }

            $products = $db->query($query);
            $total = $db->getTotalNumRows();

            if ($products) {
                $result['items'] = $products->rows;
                $result['total'] = $total;
            }

            $this->cache->push($cache_key, $result);
        }

        return $result;
    }

    /**
     *
     * @return string
     */
    protected function sqlFinalPriceString()
    {
        $customer_group_id = (int)$this?->customer->getCustomerGroupId()
            ?: (int)$this?->config->get('config_customer_group_id');
        $p = $this->db->table('products');
        $sql = " ( SELECT CASE WHEN p2sp.price_prefix='%' THEN " . $p . ".price - (p2sp.price * (" . $p . ".price/100)) 
                            ELSE p2sp.price END as special_price
                    FROM " . $this->db->table("product_specials") . " p2sp
                    WHERE p2sp.product_id = " . $p . ".product_id
                            AND p2sp.customer_group_id = '" . $customer_group_id . "'
                            AND ((p2sp.date_start = '0000-00-00' OR p2sp.date_start < NOW())
                            AND (p2sp.date_end = '0000-00-00' OR p2sp.date_end > NOW()))
                    ORDER BY p2sp.priority ASC, special_price ASC LIMIT 1
                 ) ";
        return "COALESCE( " . $sql . ", " . $p . ".price) as final_price";
    }

    /**
     * @return string
     */
    protected function sqlAvgRatingString()
    {
        $rw = $this->db->table('reviews');
        $p = $this->db->table('products');

        return ' ( SELECT FLOOR(AVG(' . $rw . '.rating))
                         FROM ' . $this->db->table('reviews') . ' ' . $rw . '
                         WHERE ' . $p . '.product_id = ' . $rw . '.product_id AND status = 1
                         GROUP BY ' . $rw . '.product_id 
                 ) AS rating ';
    }

    /**
     * @return string
     */
    protected function sqlReviewCountString()
    {
        $rw = $this->db->table('reviews');
        $p = $this->db->table('products');

        return ' ( SELECT COUNT(' . $rw . '.review_id)
                         FROM ' . $this->db->table('reviews') . ' ' . $rw . '
                         WHERE ' . $p . '.product_id = ' . $rw . '.product_id AND status = 1
                         GROUP BY ' . $rw . '.product_id 
                 ) AS review ';
    }

    /**
     * @param string $operator
     * @param string $invert
     *
     * @return string
     */
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

    /**
     * @param $operator
     * @param $invert
     *
     * @return string
     */
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

    /**
     * @param $collectionId
     * @param $limit
     *
     * @return false|array
     * @throws AException
     */
    public function getListingBlockProducts($collectionId, $limit)
    {
        if (!(int)$collectionId) {
            return false;
        }
        $collection = self::getById($collectionId);
        if ($collection && $collection['conditions']) {
            $sortOrder = $this->config->get('config_product_default_sort_order');
            list ($sort, $order) = explode('-', $sortOrder);
            $result = $this->getProducts(
                $collection['conditions'], $sort ?: 'date_modified', $order ?: 'DESC', 0, $limit, $collectionId
            );
            return $result['items'];
        }
        return false;
    }
}
