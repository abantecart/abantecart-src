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
 * Class ModelSaleCoupon
 */
class ModelSaleCoupon extends Model
{
    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function addCoupon($data)
    {
        if (isset($data['date_start']) && $data['date_start']) {
            $data['date_start'] = "DATE('".$data['date_start']."')";
        } else {
            $data['date_start'] = "NULL";
        }

        if (isset($data['date_end']) && $data['date_end']) {
            $data['date_end'] = "DATE('".$data['date_end']."')";
        } else {
            $data['date_end'] = "NULL";
        }

        $data['condition_rule'] = $data['condition_rule'] == 'AND' ? 'AND' : 'OR';

        $this->db->query(
            "INSERT INTO ".$this->db->table("coupons")." 
            SET code = '".$this->db->escape($data['code'])."',
                discount = '".(float) $data['discount']."',
                type = '".$this->db->escape($data['type'])."',
                total = '".(float) $data['total']."',
                logged = '".(int) $data['logged']."',
                shipping = '".(int) $data['shipping']."',
                date_start = ".$data['date_start'].",
                date_end = ".$data['date_end'].",
                uses_total = '".(int) $data['uses_total']."',
                uses_customer = '".(int) $data['uses_customer']."',
                status = '".(int) $data['status']."',
                condition_rule = '".$data['condition_rule']."',
                date_added = NOW()"
        );
        $coupon_id = $this->db->getLastId();

        foreach ($data['coupon_description'] as $language_id => $value) {
            $this->language->replaceDescriptions(
                'coupon_descriptions',
                ['coupon_id' => (int) $coupon_id],
                [
                    $language_id => [
                        'name'        => $value['name'],
                        'description' => $value['description'],
                    ],
                ]
            );
        }
        if (isset($data['coupon_products'])) {
            foreach ($data['coupon_products'] as $product_id) {
                if ((int) $product_id) {
                    $this->db->query(
                        "INSERT INTO ".$this->db->table("coupons_products")." 
                        SET coupon_id = '".(int) $coupon_id."', 
                            product_id = '".(int) $product_id."'"
                    );
                }
            }
        }
        if (isset($data['coupon_categories'])) {
            foreach ($data['coupon_categories'] as $category_id) {
                if ((int) $category_id) {
                    $this->db->query(
                        "INSERT INTO ".$this->db->table("coupons_categories")." 
                        SET coupon_id = '".(int) $coupon_id."', 
                            category_id = '".(int) $category_id."'"
                    );
                }
            }
        }
        return $coupon_id;
    }

    /**
     * @param int $coupon_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function editCoupon($coupon_id, $data)
    {
        if (!(int) $coupon_id || !$data) {
            return false;
        }
        if (isset($data['date_start']) && $data['date_start']) {
            $data['date_start'] = "DATE('".$data['date_start']."')";
        } elseif (array_key_exists('date_start', $data)) {
            $data['date_start'] = 'NULL';
        }

        if (isset($data['date_end']) && $data['date_end']) {
            $data['date_end'] = "DATE('".$data['date_end']."')";
        } elseif (array_key_exists('date_end', $data)) {
            $data['date_end'] = 'NULL';
        }

        if (isset($data['condition_rule'])) {
            $data['condition_rule'] = $data['condition_rule'] == 'AND' ? 'AND' : 'OR';
        }

        $coupon_table_fields = [
            'code',
            'discount',
            'type',
            'total',
            'logged',
            'shipping',
            'date_start',
            'date_end',
            'uses_total',
            'uses_customer',
            'status',
            'condition_rule',
        ];
        $update = [];
        foreach ($coupon_table_fields as $f) {
            if (isset($data[$f])) {
                if (!in_array($f, ['date_start', 'date_end'])) {
                    $update[] = $f." = '".$this->db->escape($data[$f])."'";
                } else {
                    $update[] = $f." = ".$data[$f]."";
                }
            }
        }
        if (!empty($update)) {
            $this->db->query(
                "UPDATE ".$this->db->table("coupons")." 
                SET ".implode(',', $update)."
                WHERE coupon_id = '".(int) $coupon_id."'"
            );
        }

        if (!empty($data['coupon_description'])) {
            foreach ($data['coupon_description'] as $language_id => $value) {
                $update = [];
                if (isset($value['name'])) {
                    $update["name"] = $value['name'];
                }
                if (isset($value['description'])) {
                    $update["description"] = $value['description'];
                }
                if (!empty($update)) {
                    $this->language->replaceDescriptions(
                        'coupon_descriptions',
                        ['coupon_id' => (int) $coupon_id],
                        [
                            $language_id => [
                                'name'        => $value['name'],
                                'description' => $value['description'],
                            ],
                        ]
                    );
                }
            }
        }
        return true;
    }

    /**
     * @param int $coupon_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function editCouponProducts($coupon_id, $data)
    {
        if (!(int) $coupon_id || !$data) {
            return false;
        }
        $this->db->query(
            "DELETE FROM ".$this->db->table("coupons_products")." 
            WHERE coupon_id = '".(int) $coupon_id."'"
        );
        if (isset($data['coupon_products'])) {
            foreach ($data['coupon_products'] as $product_id) {
                if ((int) $product_id) {
                    $this->db->query(
                        "INSERT INTO ".$this->db->table("coupons_products")." 
                        SET coupon_id = '".(int) $coupon_id."',
                            product_id = '".(int) $product_id."'"
                    );
                }
            }
        }
        return true;
    }

    /**
     * @param int $coupon_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function editCouponCategories($coupon_id, $data)
    {
        if (!(int) $coupon_id || !$data) {
            return false;
        }
        $this->db->query(
            "DELETE FROM ".$this->db->table("coupons_categories")." 
            WHERE coupon_id = '".(int) $coupon_id."'"
        );
        if (isset($data['coupon_categories'])) {
            foreach ($data['coupon_categories'] as $category_id) {
                if ((int) $category_id) {
                    $this->db->query(
                        "INSERT INTO ".$this->db->table("coupons_categories")." 
                    SET coupon_id = '".(int) $coupon_id."',
                        category_id = '".(int) $category_id."'"
                    );
                }
            }
        }
        return true;
    }

    /**
     * @param int $coupon_id
     *
     * @return bool
     * @throws AException
     */
    public function deleteCoupon($coupon_id)
    {
        if (!$coupon_id) {
            return false;
        }
        $this->db->query(
            "DELETE FROM ".$this->db->table("coupons")." 
            WHERE coupon_id = '".(int) $coupon_id."'"
        );
        $this->db->query(
            "DELETE FROM ".$this->db->table("coupon_descriptions")." 
            WHERE coupon_id = '".(int) $coupon_id."'"
        );
        $this->db->query(
            "DELETE FROM ".$this->db->table("coupons_products")." 
            WHERE coupon_id = '".(int) $coupon_id."'"
        );
        return true;
    }

    /**
     * @param int $coupon_id
     *
     * @return array
     * @throws AException
     */
    public function getCouponByID($coupon_id)
    {
        $query = $this->db->query(
            "SELECT DISTINCT * 
            FROM ".$this->db->table("coupons")." 
            WHERE coupon_id = '".(int) $coupon_id."'"
        );
        return $query->row;
    }

    /**
     * @param array $data
     * @param string $mode
     *
     * @return array|int
     * @throws AException
     */
    public function getCoupons($data = [], $mode = 'default')
    {
        if (!empty($data['content_language_id'])) {
            $language_id = ( int ) $data['content_language_id'];
        } else {
            $language_id = (int) $this->config->get('storefront_language_id');
        }
        $sqlDateRange = "(CASE WHEN c.date_start < NOW() AND c.date_end > NOW() THEN 1 ELSE 0 END)";

        if ($mode == 'total_only') {
            $total_sql = 'count(*) as total';
        } else {
            $total_sql = "
            c.coupon_id, 
            cd.name, 
            c.code, 
            c.discount, 
            c.date_start, 
            c.date_end, 
            ".$sqlDateRange." as status ";
        }

        $sql = "SELECT ".$total_sql." 
                FROM ".$this->db->table("coupons")." c
                LEFT JOIN ".$this->db->table("coupon_descriptions")." cd
                    ON (c.coupon_id = cd.coupon_id AND cd.language_id = '".$language_id."')
                WHERE 1=1 ";

        if (!empty($data['search'])) {
            $sql .= " AND ".$data['search'];
        }

        if (isset($data['filter']['status'])) {
            $sql .= " AND ".$sqlDateRange." = ".$data['filter']['status'];
        }

        if (!empty($data['subsql_filter'])) {
            $sql .= " AND ".$data['subsql_filter'];
        }

        //If for total, we're done building the query
        if ($mode == 'total_only') {
            $query = $this->db->query($sql);
            return $query->row['total'];
        }

        $sort_data = [
            'name'       => 'cd.name',
            'code'       => 'c.code',
            'discount'   => 'c.discount',
            'date_start' => 'c.date_start',
            'date_end'   => 'c.date_end',
            'status'     => $sqlDateRange,
        ];

        if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
            $sql .= " ORDER BY ".$sort_data[$data['sort']];
        } else {
            $sql .= " ORDER BY cd.name";
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
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function getTotalCoupons($data)
    {
        return $this->getCoupons($data, 'total_only');
    }

    /**
     * @param int $coupon_id
     *
     * @return array
     * @throws AException
     */
    public function getCouponDescriptions($coupon_id)
    {
        $coupon_description_data = [];

        $query = $this->db->query(
            "SELECT *
            FROM ".$this->db->table("coupon_descriptions")." 
            WHERE coupon_id = '".(int) $coupon_id."'"
        );

        foreach ($query->rows as $result) {
            $coupon_description_data[$result['language_id']] = [
                'name'        => $result['name'],
                'description' => $result['description'],
            ];
        }

        return $coupon_description_data;
    }

    /**
     * @param int $coupon_id
     *
     * @return array
     * @throws AException
     */
    public function getCouponProducts($coupon_id)
    {
        $query = $this->db->query(
            "SELECT *
            FROM ".$this->db->table("coupons_products")." 
            WHERE coupon_id = '".(int) $coupon_id."'"
        );
        return array_map('intval', array_column($query->rows, 'product_id'));
    }

    /**
     * @param int $coupon_id
     *
     * @return array
     * @throws AException
     */
    public function getCouponCategories($coupon_id)
    {
        $query = $this->db->query(
            "SELECT *
            FROM ".$this->db->table("coupons_categories")." 
            WHERE coupon_id = '".(int) $coupon_id."'"
        );
        return array_map('intval', array_column($query->rows, 'category_id'));
    }
}