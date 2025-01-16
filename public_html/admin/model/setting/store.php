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
 * Class ModelSettingStore
 *
 * @property ModelSettingSetting $model_setting_setting
 */
class ModelSettingStore extends Model
{
    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function addStore(array $data)
    {
        if (!$data) {
            return false;
        }
        if (!$data['alias']) {
            $data['alias'] = substr(str_replace(' ', '', $data['name']), 0, 15);
        }

        $this->db->query(
            "INSERT INTO " . $this->db->table("stores") . " 
            SET name = '" . $this->db->escape($data['name']) . "',
                alias = '" . $this->db->escape($data['alias']) . "',
                status = '" . $this->db->escape($data['status']) . "'"
        );
        $store_id = $this->db->getLastId();

        //Clone from selected store
        if (isset($data['clone_store'])) {
            $sql = "INSERT INTO " . $this->db->table("settings") . " (store_id, `group`, `key`, `value`)
                    SELECT '" . $store_id . "' as store_id, `group`, `key`, `value`
                    FROM " . $this->db->table("settings") . " 
                    WHERE `store_id` = '" . (int)$data['clone_store'] . "'";
            $this->db->query($sql);
            /** @var ModelDesignEmailTemplate $mdl */
            $mdl = $this->load->model('design/email_template');
            $mdl->copyToNewStore((int)$data['clone_store'], (int)$store_id);
        } else {
            // add settings of extension of default store to new store settings
            // NOTE: we do this because of extension status in settings table. It used to recognize is extension installed or not
            $extension_list = $this->extensions->getEnabledExtensions();
            $sql = "INSERT INTO " . $this->db->table("settings") . " (store_id, `group`, `key`, `value`)
                    SELECT '" . $store_id . "' as store_id, `group`, `key`, `value`
                    FROM " . $this->db->table("settings") . "
                    WHERE `group` in ('" . implode("' ,'", $extension_list) . "') AND store_id='0';";
            $this->db->query($sql);
        }

        $this->load->model('setting/setting');
        foreach ($data['store_description'] as $language_id => $value) {
            $this->language->replaceDescriptions(
                'store_descriptions',
                [
                    'store_id' => (int)$store_id,
                ],
                [
                    $language_id => [
                        'description' => $value['description'],
                    ],
                ]
            );
            $this->model_setting_setting->editSetting(
                'details',
                [
                    'config_description_' . (int)$language_id => $value['description'],
                ],
                $store_id
            );
        }
        unset($data['store_description']);

        //Copy some data to details
        $this->model_setting_setting->editSetting(
            'details',
            [
                'config_url'     => $data['config_url'],
                'store_name'     => $data['name'],
                'config_ssl'     => $data['config_ssl'],
                'config_ssl_url' => $data['config_ssl_url'],
            ],
            $store_id
        );
        $this->cache->remove(['settings', 'stores']);
        return $store_id;
    }

    /**
     * @param int $store_id
     * @param array $data
     *
     * @throws AException
     */
    public function editStore($store_id, $data)
    {
        if (!empty($data['store_description'])) {
            foreach ($data['store_description'] as $language_id => $value) {
                if (isset($value['description'])) {
                    $this->language->replaceDescriptions(
                        'store_descriptions',
                        ['store_id' => (int)$store_id],
                        [
                            $language_id => [
                                'description' => $value['description'],
                            ],
                        ]
                    );
                }
            }
        }
        unset($data['store_description']);

        $this->load->model('setting/setting');
        if (isset($data['alias'])) {
            $this->db->query(
                "UPDATE " . $this->db->table("stores") . " 
                SET  `alias`='" . $this->db->escape($data['alias']) . "'
                WHERE store_id = '" . (int)$store_id . "' "
            );
        }
        if (isset($data['status'])) {
            $this->db->query(
                "UPDATE " . $this->db->table("stores") . " 
                SET  `status`='" . $this->db->escape($data['status']) . "'
                WHERE store_id = '" . (int)$store_id . "' "
            );
        }
        if (isset($data['name'])) {
            $this->db->query(
                "UPDATE " . $this->db->table("stores") . " 
                SET  `name`='" . $this->db->escape($data['name']) . "'
                WHERE store_id = '" . (int)$store_id . "' "
            );
            $this->model_setting_setting->editSetting('details', ['config_name' => $data['name']], $store_id);
        }
        if (isset($data['config_url'])) {
            $this->model_setting_setting->editSetting('details', ['config_url' => $data['config_url']], $store_id);
        }
        if (isset($data['config_ssl'])) {
            $this->model_setting_setting->editSetting('details', ['config_ssl' => $data['config_ssl']], $store_id);
        }

        $this->cache->remove(['settings', 'stores']);
    }

    /**
     * @param int $store_id
     *
     * @throws AException
     */
    public function deleteStore($store_id)
    {
        $tables = [
            "stores",
            "settings",
            "store_descriptions",
            "categories_to_stores",
            "products_to_stores",
            "contents_to_stores",
            "manufacturers_to_stores",
            "email_templates",
            "banner_stat",
            "user_notifications",
            "custom_lists",
            "collections"
        ];

        foreach ($tables as $table) {
            $this->db->query("DELETE FROM " . $this->db->table($table) . " WHERE store_id = '" . (int)$store_id . "'");
        }

        $this->cache->remove(['settings', 'stores']);
    }

    /**
     * @param int $store_id
     *
     * @return array
     * @throws AException
     */
    public function getStore($store_id)
    {
        $query = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table("stores") . " 
            WHERE store_id = '" . (int)$store_id . "'"
        );
        $output = $query->rows[0];

        $query = $this->db->query(
            "SELECT DISTINCT * 
            FROM " . $this->db->table("settings") . " 
            WHERE store_id = '" . (int)$store_id . "'"
        );
        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $output[$row['key']] = $row['value'];
            }
        }
        return $output;
    }

    /**
     * checks if current selected store from store switcher is default. Note: this check needed only for default store admin side.
     *
     * @return bool
     * @throws AException
     */
    public function isDefaultStore()
    {
        $store_settings = $this->getStore((int)($this->session->data['current_store_id'] ?? 0));
        return ($this->config->get('config_url') == $store_settings['config_url']);
    }

    /**
     * @param int $store_id
     *
     * @return string mixed
     * @throws AException
     */
    public function getStoreURL($store_id)
    {
        $store_settings = $this->getStore($store_id);
        return $store_settings['config_url'];
    }

    /**
     * @param int $store_id
     *
     * @return array
     * @throws AException
     */
    public function getStoreDescriptions($store_id)
    {
        $store_description_data = [];
        $query = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table("store_descriptions") . " 
            WHERE store_id = '" . (int)$store_id . "'"
        );
        foreach ($query->rows as $result) {
            $store_description_data[$result['language_id']] = [
                'description' => $result['description']
            ];
        }
        return $store_description_data;
    }

    /**
     * @return array
     * @throws AException
     */
    public function getStores()
    {
        $store_data = $this->cache->pull('stores');
        if ($store_data === false) {
            $query = $this->db->query(
                "SELECT *
                FROM " . $this->db->table("stores") . " 
                ORDER BY store_id"
            );
            $store_data = $query->rows;
            $this->cache->push('stores', $store_data);
        }
        return (array)$store_data;
    }

    /**
     * @return int
     * @throws AException
     */
    public function getTotalStores()
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total 
            FROM " . $this->db->table("stores") . " "
        );
        return $query->row['total'];
    }

    /**
     * @param string $language
     *
     * @return int
     * @throws AException
     */
    public function getTotalStoresByLanguage($language)
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total
            FROM " . $this->db->table("settings") . " 
            WHERE `key` = 'config_storefront_language' 
                AND  `value` = '" . $this->db->escape($language) . "'"
        );
        return $query->row['total'];
    }

    /**
     * @param string $currency
     *
     * @return int
     * @throws AException
     */
    public function getTotalStoresByCurrency($currency)
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total
            FROM " . $this->db->table("settings") . " 
            WHERE `key` = 'config_currency' 
                AND `value` = '" . $this->db->escape($currency) . "'"
        );
        return $query->row['total'];
    }

    /**
     * @param int $country_id
     *
     * @return int
     * @throws AException
     */
    public function getTotalStoresByCountryId($country_id)
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total
            FROM " . $this->db->table("settings") . " 
            WHERE `key` = 'config_country_id' 
                AND  `value` = '" . (int)$country_id . "'"
        );
        return $query->row['total'];
    }

    /**
     * @param int $zone_id
     *
     * @return int
     * @throws AException
     */
    public function getTotalStoresByZoneId($zone_id)
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total
            FROM " . $this->db->table("settings") . " 
            WHERE `key` = 'config_zone_id' 
                AND  `value` = '" . (int)$zone_id . "'"
        );
        return $query->row['total'];
    }

    /**
     * @param int $customer_group_id
     *
     * @return int
     * @throws AException
     */
    public function getTotalStoresByCustomerGroupId($customer_group_id)
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total
            FROM " . $this->db->table("settings") . " 
            WHERE `key` = 'config_customer_group_id' 
                AND `value` = '" . (int)$customer_group_id . "'"
        );
        return $query->row['total'];
    }

    /**
     * @param int $information_id
     *
     * @return int
     * @throws AException
     */
    public function getTotalStoresByInformationId($information_id)
    {
        $account_query = $this->db->query(
            "SELECT COUNT(*) AS total
            FROM " . $this->db->table("settings") . " 
            WHERE `key` = 'config_account_id' 
                AND `value` = '" . (int)$information_id . "'"
        );
        $checkout_query = $this->db->query(
            "SELECT COUNT(*) AS total
            FROM " . $this->db->table("settings") . " 
            WHERE `key` = 'config_checkout_id' 
                AND `value` = '" . (int)$information_id . "'"
        );
        return ($account_query->row['total'] + $checkout_query->row['total']);
    }

    /**
     * @param int $order_status_id
     *
     * @return int
     * @throws AException
     */
    public function getTotalStoresByOrderStatusId($order_status_id)
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total
            FROM " . $this->db->table("settings") . " 
            WHERE `key` = 'config_order_status_id' 
                AND `value` = '" . (int)$order_status_id . "'"
        );
        return $query->row['total'];
    }
}
