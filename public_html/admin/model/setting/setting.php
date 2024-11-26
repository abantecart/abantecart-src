<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
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
 * Class ModelSettingSetting
 */
class ModelSettingSetting extends Model
{
    /**
     * @return array
     * @throws AException
     */
    public function getGroups()
    {
        $data = [];
        $query = $this->db->query(
            "SELECT DISTINCT `group` 
            FROM " . $this->db->table("settings")
        );
        foreach ($query->rows as $result) {
            $data[] = $result['group'];
        }
        return $data;
    }

    /**
     * @param string $setting_key
     * @param int $store_id
     *
     * @return array
     * @throws AException
     */
    public function getSettingGroup($setting_key, $store_id = 0)
    {
        $data = [];
        $query = $this->db->query(
            "SELECT DISTINCT `group` 
            FROM " . $this->db->table("settings") . " 
            WHERE `key` = '" . $this->db->escape($setting_key) . "'
                AND `store_id` = '" . $store_id . "'"
        );

        foreach ($query->rows as $result) {
            $data[] = $result['group'];
        }
        return $data;
    }

    /**
     * @param array $data
     * @param string $mode
     *
     * @return array
     * @throws AException
     */
    public function getAllSettings($data = [], $mode = 'default')
    {

        if ($mode == 'total_only') {
            $total_sql = 'count(*) as total';
        } else {
            $total_sql = 's.*, COALESCE(st.alias, \'' . $this->language->get('text_default') . '\' ) as alias';
        }

        $sql = "SELECT $total_sql
                FROM " . $this->db->table("settings") . " s
                LEFT JOIN  " . $this->db->table("stores") . " st 
                    ON st.store_id = s.store_id
                WHERE s.group IN ('" . implode("', '", $this->config->groups) . "') ";

        if (isset($data['store_id'])) {
            $sql .= " AND s.store_id = '" . $data['store_id'] . "'";
        }

        if (!empty($data['subsql_filter'])) {
            $sql .= " AND " . $data['subsql_filter'];
        }

        //If for total, build the query
        if ($mode == 'total_only') {
            $query = $this->db->query($sql);
            return $query->row['total'];
        }

        $sort_data = ['group', 'key'];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY `" . $data['sort'] . "`";
        } else {
            $sql .= " ORDER BY `group`";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
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
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getTotalSettings($data = [])
    {
        return $this->getAllSettings($data, 'total_only');
    }

    /**
     * @param string $group
     * @param int $store_id
     *
     * @return array
     * @throws AException
     */
    public function getSetting($group, $store_id = 0)
    {
        $data = [];

        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("settings") . " 
            WHERE `group` = '" . $this->db->escape($group) . "'
                AND store_id = '" . (int)$store_id . "'"
        );
        foreach ($query->rows as $result) {
            $value = $result['value'];
            if (is_serialized($value)) {
                $value = unserialize($value);
            }
            $data[$result['key']] = $value;
        }
        return $data;
    }

    /**
     * @param string $group
     * @param array $data
     * @param int|null $store_id
     *
     * @throws AException
     */
    public function editSetting($group, $data, $store_id = null)
    {
        $store_id = (int)$store_id ?: ($this->config->get('current_store_id'));
        $translate_override_existing = $this->config->get('translate_override_existing');
        //do not override when content language is not a source!
        if ($translate_override_existing
            &&
            $this->config->get('translate_src_lang_code') != $this->language->getContentLanguageCode()
        ) {
            $translate_override_existing = false;
        }

        $languages = $this->language->getAvailableLanguages();
        // check what is it - update or insert of setting

        $edit_type = 'insert';
        foreach ($languages as $language) {
            if (
                $this->config->get('config_description_' . $language['language_id']) !== null
            ) {
                $edit_type = 'update';
                break;
            }
        }
        $src_lang_id = $this->language->getLanguageIdByCode($this->config->get('translate_src_lang_code'));
        // if override - edit type is insert
        if ($translate_override_existing
            && (isset($data['config_description_' . $src_lang_id])
                || isset($data['config_title_' . $src_lang_id])
                || isset($data['config_meta_description_' . $src_lang_id])
                || isset($data['config_meta_keywords_' . $src_lang_id]))
        ) {
            $edit_type = 'insert';
        }

        $locales = [];
        foreach ($languages as $language) {
            // if update and not override - skip
            if (!$translate_override_existing && $edit_type == 'update') {
                continue;
            }
            $locale = $this->language->getLanguageCodeByLocale($language['locale']);
            if ($locale != $this->config->get('translate_src_lang_code')
                && $edit_type == 'insert'
            ) {
                $locales[$language['language_id']] = $locale;
            }
        }

        // need translate
        if ($locales) {
            if ($src_lang_id) {
                $arr = [
                    'config_description',
                    'config_title',
                    'config_meta_description',
                    'config_meta_keywords'
                ];

                foreach ($arr as $n) {
                    $key = $n . '_' . $src_lang_id;
                    $src_text = $data[$key] ?? $this->config->get($key);
                    $src_text = trim($src_text);
                    foreach ($locales as $dst_lang_id => $dst_code) {
                        if ($src_text) {
                            $data[$n . '_' . $dst_lang_id] = $this->language->translate(
                                $this->config->get('translate_src_lang_code'),
                                $src_text,
                                $dst_code
                            );
                        }
                    }
                }
            }
        }

        $url_protocol = $ssl_url_protocol = '';
        if (has_value($data['config_url'])) {
            $url_protocol = preg_match("/^(https):\/\//", $data['config_url']) ? 'https' : 'http';
        }
        if (has_value($data['config_ssl_url'])) {
            $ssl_url_protocol = preg_match("/^(https):\/\//", $data['config_ssl_url']) ? 'https' : 'http';
        }

        //need to add slash at the end because browsers do it too automatically
        if (has_value($data['config_url']) && substr($data['config_url'], -1) != '/') {
            $data['config_url'] .= '/';
        }
        if (has_value($data['config_ssl_url']) && substr($data['config_ssl_url'], -1) != '/') {
            $data['config_ssl_url'] .= '/';
        }
        //need to set ssl_mode setting to use it in AHtml class for building correct URLs
        if (!has_value($data['config_url']) xor !has_value($data['config_ssl_url'])) {
            $saved_settings = $this->getSetting($group, $store_id);
            if (!has_value($data['config_url']) && has_value($data['config_ssl_url'])) {
                $url_protocol = preg_match("/^(https):\/\//", $saved_settings['config_url']) ? 'https' : 'http';
            }
            if (has_value($data['config_url']) && !has_value($data['config_ssl_url'])) {
                $ssl_url_protocol = preg_match("/^(https):\/\//", $saved_settings['config_ssl_url']) ? 'https' : 'http';
            }
        }

        //remove session parameter if maintenance mode is off. Effect on message on sf-side
        if (isset($data['config_maintenance']) && !$data['config_maintenance']) {
            unset($this->session->data['merchant']);
        }

        if ($url_protocol && $ssl_url_protocol) {
            if ($ssl_url_protocol == 'https' && $url_protocol == 'http') {
                $data['config_ssl'] = 1;
            } elseif ($ssl_url_protocol == 'https' && $url_protocol == 'https') {
                $data['config_ssl'] = 2;
            } else {
                $data['config_ssl'] = 0;
            }
        }

        foreach ($data as $key => $value) {
            if ($key == 'one_field') {
                continue;
            } //is a sign for displaying one setting for quick edit form. ignore it!

            //check if setting is multi-value (array) and save serialized value.
            if (is_array($value)) {
                //validate values in array. If setting is array of all members = 0 save only single value of 0
                //This is to match standard post format in regular form submit
                $concat = implode('', $value);
                if (preg_match('/[^0]/', $concat)) {
                    $value = serialize($value);
                } else {
                    $value = 0;
                }
            }

            $sql = "DELETE FROM " . $this->db->table("settings") . " 
                    WHERE `group` = '" . $this->db->escape($group) . "'
                            AND `key` = '" . $this->db->escape($key) . "'
                        AND `store_id` = '" . $store_id . "'";
            $this->db->query($sql);

            $sql = "INSERT INTO " . $this->db->table("settings") . " 
                        ( `store_id`, `group`, `key`, `value`, `date_added`)
                    VALUES (  '" . $store_id . "',
                              '" . $this->db->escape($group) . "',
                              '" . $this->db->escape($key) . "',
                              '" . $this->db->escape($value) . "',
                              NOW())";
            $this->db->query($sql);
        }
        // if change cache status - flush cache
        if (isset($data['config_cache_enable'])) {
            $this->cache->remove('*');
        }

        $this->cache->remove('settings');
        $this->cache->remove('extensions');
        $this->cache->remove('stores');
        $this->cache->remove('admin_menu');
        $this->cache->remove('storefront_menu');
    }

    /**
     * @param string $group
     * @param int $store_id
     * @throws AException
     */
    public function deleteSetting($group, $store_id = 0)
    {
        $store_id = (int)$store_id;
        $this->db->query(
            "DELETE FROM " . $this->db->table("settings") . " 
            WHERE `group` = '" . $this->db->escape($group) . "'
                AND `store_id` = '" . $store_id . "'"
        );

        $this->cache->remove('settings');
        $this->cache->remove('extensions');
        $this->cache->remove('stores');
        $this->cache->remove('admin_menu');
        $this->cache->remove('storefront_menu');
    }
}
