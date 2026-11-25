<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
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

class ModelCatalogCollection extends Model
{
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->data['description_column_list'] = ['title', 'meta_keywords', 'meta_description', 'content'];
    }

    /**
     * @param array $inData
     *
     * @return bool
     * @throws AException
     */
    public function insert(array $inData)
    {
        if (!$inData) {
            return false;
        }
        $languageId = (int) $inData['language_id'] ? : $this->language->getContentLanguageID();

        $stores = array_map('intval', array_map('trim', (array) $inData['stores']));
        unset($inData['stores']);

        if (isset($inData['condition_object'])) {
            unset($inData['condition_object']);
        }

        $keyword = $inData['keyword'] ?? '';
        unset($inData['keyword']);

        $descriptionData = [
            'language_id' => $languageId,
        ];

        foreach ($this->data['description_column_list'] as $field) {
            if (isset($inData[$field])) {
                $descriptionData[$field] = $inData[$field];
                unset($inData[$field]);
            }
        }

        $values = array_values($inData);
        foreach ($values as &$value) {
            $value = is_array($value) ? json_encode($value) : $value;
        }
        $keys = array_map([$this->db, 'escape'], array_keys($inData));
        $values = array_map([$this->db, 'escape'], $values);

        $query = "INSERT INTO " . $this->db->table("collections") . " 
                    (" . implode(',', $keys) . ") 
                VALUES ('" . implode("','", $values) . "')";
        $this->db->query($query);

        $collectionId = $descriptionData['collection_id'] = (int) $this->db->getLastId();
        $this->updateOrCreateDescription($collectionId, $languageId, $descriptionData);

        if ($keyword) {
            $seo_key = SEOEncode((string) $keyword, 'collection_id', $collectionId);
        } else {
            //Default behavior to save SEO URL keyword from collection name in default language
            $seo_key = SEOEncode(
                (string) ($descriptionData['title'] ? : $inData['name']),
                'collection_id',
                $collectionId
            );
        }
        if ($seo_key) {
            $this->language->replaceDescriptions(
                'url_aliases',
                ['query' => "collection_id=" . $collectionId],
                [$languageId => ['keyword' => $seo_key]]
            );
        } else {
            $this->db->query(
                "DELETE FROM " . $this->db->table("url_aliases") . " 
                WHERE query = 'collection_id=" . $collectionId . "' 
                    AND language_id = " . $languageId
            );
        }

        if ($stores) {
            foreach ($stores as $store_id) {
                $this->db->query(
                    "INSERT INTO " . $this->db->table("collections_to_stores") . " 
                        SET collection_id = '" . $collectionId . "', 
                            store_id = " . $store_id
                );
            }
        }
        $this->cache->remove(['collection', 'url_aliases']);
        return $this->getById($collectionId);
    }

    /**
     * @param int $collectionId
     * @param array $inData
     *
     * @return bool
     * @throws AException
     */
    public function update(int $collectionId, array $inData)
    {
        if (!$collectionId || !$inData) {
            return false;
        }
        $languageId = (int) ($inData['language_id'] ? : $this->language->getContentLanguageID());
        $stores = array_map('intval', array_map('trim', (array) $inData['stores']));
        unset($inData['stores']);

        $descriptionData = [
            'language_id'   => $languageId,
            'collection_id' => $collectionId,
        ];

        foreach ($this->data['description_column_list'] as $field) {
            if (isset($inData[$field])) {
                $descriptionData[$field] = $inData[$field];
                unset($inData[$field]);
            }
        }

        if (isset($inData['condition_object'])) {
            unset($inData['condition_object']);
        }

        $keyword = $inData['keyword'];
        unset($inData['keyword']);

        $arUpdate = [];
        foreach ($inData as $key => $val) {
            $arUpdate[] = $key . " = '" . $this->db->escape(is_array($val) ? json_encode($val) : $val) . "'";
        }

        if (!empty($arUpdate)) {
            $query = "UPDATE " . $this->db->table('collections') . " 
                    SET " . implode(',', $arUpdate) . " 
                    WHERE id=" . $collectionId;
            $this->db->query($query);
        }

        if ($descriptionData) {
            $this->updateOrCreateDescription($collectionId, $languageId, $descriptionData);
        }

        if ($stores) {
            $this->db->query(
                "DELETE FROM " . $this->db->table("collections_to_stores") . " 
                WHERE collection_id = '" . $collectionId . "'"
            );
            foreach ($stores as $storeId) {
                $this->db->query(
                    "INSERT INTO " . $this->db->table("collections_to_stores") . " 
                    SET collection_id = '" . $collectionId . "', 
                        store_id = '" . (int) $storeId . "'"
                );
            }
        }

        if (isset($keyword)) {
            $keyword = SEOEncode((string) $keyword);
            if ($keyword) {
                $this->language->replaceDescriptions(
                    'url_aliases',
                    [
                        'query' => 'collection_id=' . $collectionId,
                    ],
                    [
                        $languageId => [
                            'keyword' => $keyword,
                        ],
                    ]
                );
            } else {
                $this->db->query(
                    "DELETE
                    FROM " . $this->db->table("url_aliases") . " 
                    WHERE query = 'collection_id=" . $collectionId . "'
                        AND language_id = '" . $languageId . "'"
                );
            }
        }
        $this->cache->remove(['collection', 'url_aliases']);
        return true;
    }

    /**
     * @param int $collectionId
     *
     * @return bool
     * @throws AException
     */
    public function delete(int $collectionId)
    {
        if (!$collectionId) {
            return false;
        }

        $this->db->query(
            "DELETE FROM " . $this->db->table("url_aliases") . " 
            WHERE query = 'collection_id=" . $collectionId . "'"
        );

        $this->db->query(
            "DELETE FROM " . $this->db->table("collections_to_stores") . " 
            WHERE collection_id = '" . $collectionId . "'"
        );
        $this->db->query(
            "DELETE FROM " . $this->db->table("collection_descriptions") . " 
            WHERE collection_id = '" . $collectionId . "'"
        );
        $this->db->query(
            "DELETE FROM " . $this->db->table('collections') . " 
            WHERE id=" . $collectionId
        );
        $this->cache->remove(['collection', 'url_aliases']);
        return true;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getCollections(array $data)
    {
        $query = "SELECT " . $this->db->getSqlCalcTotalRows() . " c.* 
                  FROM " . $this->db->table('collections') . " c 
                  INNER JOIN " . $this->db->table('collections_to_stores') . " c2s 
                       ON c.id = c2s.collection_id AND c2s.store_id = " . (int) $data['store_id'];

        $allowedSearchFields = [
            'name'     => 'c.name',
            'store_id' => 'c.store_id',
            'status'   => 'c.status',
        ];

        $allowedSortFields = [
            'name'       => 'c.name',
            'date_added' => 'c.date_added',
            'status'     => 'c.status',
        ];

        $arWhere = [];
        if (isset($data['_search']) && $data['_search'] == 'true') {
            $filters = json_decode(htmlspecialchars_decode($data['filters']), true);
            foreach ((array) $filters['rules'] as $filter) {
                $fldName = $filter['field'];
                if (!$allowedSearchFields[$fldName]) {
                    continue;
                }
                $arWhere[] = $allowedSearchFields[$fldName] . " LIKE '%" . $this->db->escape($filter['data']) . "%'";
            }
        }

        if (isset($data['status'])) {
            $arWhere[] = $allowedSearchFields['status'] . " = " . (int) $data['status'];
        }

        if (!empty($arWhere)) {
            $query .= " WHERE " . implode(' AND ', $arWhere);
        }

        if (isset($data['sidx']) && isset($data['sord']) && $allowedSortFields[$data['sidx']]) {
            $query .= " ORDER BY " . $allowedSortFields[$data['sidx']] . " " . $data['sord'];
        }

        $limit = min((int) $data['rows'] ? : 20, 50);
        $page = (int) $data['page'] ? : 1;
        $start = $page * $limit - $limit;
        $query .= " LIMIT " . $start . "," . $limit;
        $result = $this->db->query($query);
        $total = $this->db->getTotalNumRows();

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
     * @param int $collectionId
     * @param int $languageId
     * @param array $inData
     *
     * @return bool
     * @throws AException
     */
    public function updateOrCreateDescription(int $collectionId, int $languageId, array $inData)
    {
        if (!$collectionId || !$languageId) {
            return false;
        }

        $exists = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table('collections') . " 
            WHERE id=" . $collectionId
        );

        if ($exists->num_rows) {
            $arUpdate = [];
            foreach ($inData as $key => $val) {
                if (!in_array($key, $this->data['description_column_list'])) {
                    continue;
                }
                $arUpdate[$key] = $val;
            }
            if ($arUpdate) {
                $this->language->replaceDescriptions(
                    'collection_descriptions',
                    ['collection_id' => $collectionId],
                    [
                        $languageId => $arUpdate,
                    ]
                );
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * @param int $collectionId
     * @param int $languageId
     *
     * @return array|false
     * @throws AException
     */
    public function getById(int $collectionId, int $languageId = 0)
    {
        if (!$collectionId) {
            return false;
        }

        $languageId = $languageId ? : $this->language->getContentLanguageID();
        $sql = "SELECT c.*, cd.*, 
                    (SELECT keyword
                    FROM " . $this->db->table("url_aliases") . " 
                    WHERE query = 'collection_id=" . $collectionId . "'
                        AND language_id = '" . $languageId . "') as keyword,
                    c.id
                  FROM " . $this->db->table('collections') . " c 
                  LEFT JOIN " . $this->db->table('collection_descriptions') . " cd 
                      ON (cd.collection_id = c.id AND cd.language_id = " . $languageId . ")
                  WHERE c.id=" . $collectionId;

        $result = $this->db->query($sql);
        if ($result->num_rows) {
            $output = $result->row;
            $output['conditions'] = json_decode($output['conditions'], true);
            if ($output) {
                $sql = "SELECT store_id 
                        FROM " . $this->db->table('collections_to_stores') . " 
                        WHERE collection_id=" . $collectionId;
                $output['stores'] = array_column($this->db->query($sql)->rows, 'store_id');
            }
            return $output;
        }
        return [];
    }

    /**
     * Returns collections of the store which contain the product.
     * Conditions are processed the same way as in storefront ModelCatalogProduct::getCollectionProducts().
     * NOTE: "product_price" condition is checked by base product price (without specials, discounts etc.)
     *
     * @param int $productId
     * @param int $storeId
     *
     * @return array
     * @throws AException
     */
    public function getCollectionsWithProduct(int $productId, int $storeId)
    {
        $result = $this->db->query(
            "SELECT c.id, c.name, c.conditions
            FROM " . $this->db->table('collections') . " c
            INNER JOIN " . $this->db->table('collections_to_stores') . " c2s
                ON (c2s.collection_id = c.id AND c2s.store_id = " . $storeId . ")
            ORDER BY c.name"
        );
        $output = [];
        foreach ($result->rows as $row) {
            $conditions = json_decode((string) $row['conditions'], true);
            if (!is_array($conditions) || !$this->isProductMatchConditions($productId, $storeId, $conditions)) {
                continue;
            }
            $output[] = [
                'id'   => (int) $row['id'],
                'name' => $row['name'],
            ];
        }
        return $output;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @param array $conditions
     *
     * @return bool
     * @throws AException
     */
    protected function isProductMatchConditions(int $productId, int $storeId, array $conditions)
    {
        if (!is_array($conditions['relation']) || !is_array($conditions['conditions'])) {
            return false;
        }
        $relation = $conditions['relation'];
        $languageId = (int) $this->language->getContentLanguageID();
        $arWhere = [];
        $arJoins = [
            "INNER JOIN " . $this->db->table('products_to_stores') . " pts 
                ON (pts.product_id = p.product_id AND pts.store_id=" . $storeId . ")",
        ];
        foreach ($conditions['conditions'] as $k => $condition) {
            if (is_array($condition['value']) && $condition['value']) {
                if ($condition['object'] === 'brands') {
                    $values = filterIntegerIdList($condition['value']);
                    if ($values) {
                        $arWhere[] = "p.manufacturer_id "
                            . $this->getInOperator($condition['operator'], $relation['value'])
                            . " (" . implode(',', $values) . ")";
                    }
                }
                if ($condition['object'] === 'categories') {
                    $alias = " p2c" . $k;
                    $arJoins[] = "LEFT JOIN " . $this->db->table('products_to_categories') . " " . $alias . " 
                                  ON " . $alias . ".product_id = p.product_id";
                    /** @var ModelCatalogCategory $mdl */
                    $mdl = $this->load->model('catalog/category');
                    $allCategoryChildren = $condition['value'];
                    foreach ($condition['value'] as $cId) {
                        $allCategoryChildren = array_merge($allCategoryChildren, $mdl->getChildrenIDs((int) $cId));
                    }
                    $allCategoryChildren = filterIntegerIdList($allCategoryChildren);
                    if ($allCategoryChildren) {
                        $arWhere[] = $alias . ".category_id "
                            . $this->getInOperator($condition['operator'], $relation['value'])
                            . " (" . implode(',', $allCategoryChildren) . ")";
                    }
                }
                if ($condition['object'] === 'products') {
                    $values = filterIntegerIdList($condition['value']);
                    if ($values) {
                        $arWhere[] = "p.product_id "
                            . $this->getInOperator($condition['operator'], $relation['value'])
                            . " (" . implode(',', $values) . ")";
                    }
                }
                if ($condition['object'] === 'tags') {
                    $alias = ' pt' . $k;
                    $arJoins[] = "LEFT JOIN " . $this->db->table('product_tags') . $alias . "
                                 ON (" . $alias . ".product_id = p.product_id
                                    AND " . $alias . ".language_id =" . $languageId . ')';
                    $values = [];
                    foreach ($condition['value'] as $value) {
                        $values[] = "'" . $this->db->escape($value) . "'";
                    }
                    $arWhere[] = $alias . ".tag "
                        . $this->getInOperator($condition['operator'], $relation['value'])
                        . " (" . implode(',', $values) . ")";
                }
            }
            if ($condition['object'] === 'product_price' && (float) $condition['value'] > 0) {
                $arWhere[] = "p.price "
                    . $this->getEqualOperator($condition['operator'], $relation['value'])
                    . (float) $condition['value'];
            }
        }
        if (!$arWhere) {
            return false;
        }

        $sql = "SELECT 1 
                FROM " . $this->db->table('products') . " p "
            . implode(PHP_EOL, $arJoins)
            . " WHERE p.product_id = " . $productId
            . " AND (" . implode(($relation['if'] == 'any') ? ' OR ' : ' AND ', $arWhere) . ")"
            . " LIMIT 1";
        return (bool) $this->db->query($sql)->num_rows;
    }

    /**
     * @see ModelCatalogProduct::gerEqualOperator() (storefront)
     */
    protected function getEqualOperator($operator, $invert)
    {
        $invert = is_bool($invert) ? ($invert ? 'true' : 'false') : (string) $invert;
        $map = [
            'eq'   => ['true' => '=', 'false' => '<>'],
            'neq'  => ['true' => '<>', 'false' => '='],
            'eqlt' => ['true' => '<=', 'false' => '>'],
            'eqgt' => ['true' => '>=', 'false' => '<'],
            'lt'   => ['true' => '<', 'false' => '>='],
            'gt'   => ['true' => '>', 'false' => '<='],
        ];
        return $map[$operator][$invert] ?? '=';
    }

    /**
     * @see ModelCatalogProduct::gerInOperator() (storefront)
     */
    protected function getInOperator($operator, $invert)
    {
        $invert = is_bool($invert) ? ($invert ? 'true' : 'false') : (string) $invert;
        if (($operator == 'in' && $invert == 'false') || ($operator == 'notin' && $invert == 'true')) {
            return 'NOT IN';
        }
        return 'IN';
    }
}
