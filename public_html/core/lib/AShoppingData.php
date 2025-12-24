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

class AShoppingData
{
    protected $customerId = null;
    /** @var AConfig */
    protected $config;
    /** @var ACache */
    protected $cache;
    /** @var ADB */
    protected $db;
    /** @var ARequest */
    protected $request;
    /** @var ASession */
    protected $session;
    /** @var ExtensionsApi */
    protected $extensions;

    /**
     * @param Registry $registry
     * @param int|null $customerId
     */
    public function __construct(Registry $registry, ?int $customerId = 0)
    {
        $this->cache = $registry->get('cache');
        $this->config = $registry->get('config');
        $this->db = $registry->get('db');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');
        $this->extensions = $registry->get('extensions');
        $this->customerId = $customerId;
    }

    /**
     * @param int $customerId
     * @return void
     * @throws AException
     */
    public function setCustomerId(int $customerId)
    {
        if (!$customerId) {
            throw new AException(AC_ERR_USER_ERROR, 'Customer Id must be set');
        }
        //case when a guest becomes registered
        if ($this->customerId) {
            $this->db->query(
                "UPDATE " . $this->db->table("shopping_sessions") . "
                SET `customer_id` = " . $this->db->intOrNull($customerId) . "
                WHERE `customer_id` = " . $this->db->intOrNull($this->customerId)
            );
        }
        $this->extensions->hk_ProcessData($this, __FUNCTION__, func_get_args());
        $this->customerId = $customerId;
    }

    /**
     * @param string $type
     * @param string $key
     * @return array
     * @throws AException
     */
    public function get(string $type, string $key)
    {
        $output = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table("shopping_sessions") . "
            WHERE `type` = '" . $this->db->escape($type) . "'
                AND `key` = '" . $this->db->escape($key) . "'"
        );
        if($output->row) {
            $output->row['data'] = json_decode($output->row['data'], true, JSON_PRETTY_PRINT);
        }
        return $output->row;
    }

    /**
     * @param string $type
     * @param string $key
     * @param array $data
     * @param int|null $orderId
     * @return bool|db_result_meta
     * @throws AException
     */
    public function save(string $type, string $key, array $data = [], int $orderId = null)
    {
        if (!$type || !$key || (!$data && !$orderId)) {
            return false;
        }
        $exists = $this->get($type, $key);
        if (!$exists) {
            $sql = "INSERT INTO " . $this->db->table("shopping_sessions") . "
                (`customer_id`, `order_id`, `type`, `key`, `data`)
                VALUES (
                " . $this->db->intOrNull($this->customerId) . ", 
                " . $this->db->intOrNull($orderId) . ", 
                '" . $this->db->escape($type) . "', 
                '" . $this->db->escape($key) . "', 
                '" . $this->db->escape(json_encode($data)) . "')";
        } else {
            $updateArr = [];
            if ($data) {
                $updateArr['data'] = "`data` = '" . $this->db->escape(json_encode($data)) . "'";
            }
            if ($orderId) {
                $updateArr['order_id'] = "`order_id` = " . $this->db->intOrNull($orderId);
            }
            $sql = "UPDATE " . $this->db->table("shopping_sessions") . "
                    SET " . implode(', ', $updateArr) . "
                    WHERE `type` = '" . $this->db->escape($type) . "'
                        AND `key` = '" . $this->db->escape($key) . "'";
        }
        $this->extensions->hk_ProcessData($this, __FUNCTION__, func_get_args());
        return $this->db->query($sql);
    }

    /**
     * @param string $type
     * @param string $key
     * @return false|void
     * @throws AException
     */
    public function remove(string $type, string $key)
    {
        if (!$type || !$key) {
            return false;
        }
        $this->db->query(
            "DELETE FROM " . $this->db->table("shopping_sessions") . "
            WHERE `type` = '" . $this->db->escape($type) . "'
                AND `key` = '" . $this->db->escape($key) . "'"
        );
        $this->extensions->hk_ProcessData($this, __FUNCTION__, func_get_args());
    }

    public function search(array $searchData, string $type, string $key = null, array $options = [])
    {
        if (!$searchData || !$type) {
            return [];
        }

        $conditions = ["`type` = '" . $this->db->escape($type) . "'"];

        if ($key) {
            $conditions[] = "`key` = '" . $this->db->escape($key) . "'";
        }

        foreach ($searchData as $jsonKey => $value) {
            $jsonPath = '$.' . str_replace('.', '.', $jsonKey);
            if (is_array($value)) {
                $jsonPath = $this->buildJsonPath($jsonKey);
                foreach ($value as $nestedKey => $nestedValue) {
                    $fullPath = $jsonPath . '.' . $nestedKey;
                    if (is_scalar($nestedValue)) {
                        $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(`data`, '" . $this->db->escape($fullPath) . "')) = '"
                            . $this->db->escape($nestedValue) . "'";
                    }
                }
            } elseif (is_scalar($value)) {
                $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(`data`, '" . $this->db->escape($jsonPath) . "')) = '"
                    . $this->db->escape($value) . "'";
            }
        }

        $sql = "SELECT * 
            FROM " . $this->db->table("shopping_sessions") . "
            WHERE " . implode(' AND ', $conditions);

        if(in_array($options['sort'], ['customer_id', 'order_id', 'type','key','date_added', 'date_modified'])){
            $sql .= " ORDER BY " . $options['sort'].' '.(strtolower($options['order'])=='desc'?'desc':'asc');
        }

        $result = $this->db->query($sql);
        $output = [];

        if ($result->num_rows) {
            foreach ($result->rows as $row) {
                $row['data'] = json_decode($row['data'], true);
                $output[] = $row;
            }
        }
        return $output;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function buildJsonPath(string $key): string
    {
        return '$.' . str_replace('.', '.', $key);

    }
}