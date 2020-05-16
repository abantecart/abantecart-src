<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

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
 * Class ModelLocalisationCurrency
 *
 * @property ModelSettingSetting $model_setting_setting
 */
class ModelLocalisationCurrency extends Model
{
    /**
     * @param array $data
     *
     * @return int
     */
    public function addCurrency($data)
    {
        $this->db->query("INSERT INTO ".$this->db->table("currencies")." 
                            (`title`,
                             `code`,
                             `symbol_left`,
                             `symbol_right`,
                             `decimal_place`,
                             `value`,
                             `status`,
                             `date_modified`)
                          VALUES ('".$this->db->escape($data['title'])."',
                                  '".$this->db->escape($data['code'])."',
                                  '".$this->db->escape($data['symbol_left'])."',
                                  '".$this->db->escape($data['symbol_right'])."',
                                  '".$this->db->escape($data['decimal_place'])."',
                                  '".$this->db->escape($data['value'])."',
                                  '".(int)$data['status']."',
                                  NOW())");

        $this->cache->remove('localization');

        return $this->db->getLastId();
    }

    /**
     * @param int $currency_id
     * @param     $data
     *
     * @return bool
     */
    public function editCurrency($currency_id, $data)
    {
        // prevent disabling the only enabled currency in cart
        if (isset($data['status']) && !$data['status']) {
            $enabled = array();
            $all = $this->getCurrencies();
            foreach ($all as $c) {
                if ($c['status'] && $c['currency_id'] != $currency_id) {
                    $enabled[] = $c;
                }
            }
            if (!$enabled) {
                return false;
            }
        }

        $fields = array('title', 'code', 'symbol_left', 'symbol_right', 'decimal_place', 'value', 'status',);
        $update = array('date_modified = NOW()');
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $update[] = $f." = '".$this->db->escape($data[$f])."'";
            }
        }
        if (!empty($update)) {
            $this->db->query("UPDATE ".$this->db->table("currencies")." 
                              SET ".implode(',', $update)."
                              WHERE currency_id = '".(int)$currency_id."'");
            $this->cache->remove('localization');
        }

        return true;
    }

    /**
     * @param int $currency_id
     *
     * @return bool
     */
    public function deleteCurrency($currency_id)
    {
        // prevent deleting all currencies
        if ($this->getTotalCurrencies() < 2) {
            return false;
        }
        $this->db->query("DELETE FROM ".$this->db->table("currencies")." 
                           WHERE currency_id = '".(int)$currency_id."'");
        $this->cache->remove('localization');

        return true;
    }

    /**
     * @param int $currency_id
     *
     * @return array
     */
    public function getCurrency($currency_id)
    {
        $query = $this->db->query("SELECT DISTINCT *
                                   FROM ".$this->db->table("currencies")." 
                                   WHERE currency_id = '".(int)$currency_id."'");

        return $query->row;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getCurrencies($data = array())
    {
        if ($data) {
            $sql = "SELECT * FROM ".$this->db->table("currencies")." ";

            $sort_data = array(
                'title',
                'code',
                'value',
                'status',
                'date_modified',
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY ".$data['sort'];
            } else {
                $sql .= " ORDER BY title";
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

                $sql .= " LIMIT ".(int)$data['start'].",".(int)$data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $currency_data = $this->cache->pull('localization.currency');

            if ($currency_data === false) {
                $query = $this->db->query("SELECT *
                                            FROM ".$this->db->table("currencies")." 
                                            ORDER BY title ASC");

                foreach ($query->rows as $result) {
                    $currency_data[$result['code']] = array(
                        'currency_id'   => $result['currency_id'],
                        'title'         => $result['title'],
                        'code'          => $result['code'],
                        'symbol_left'   => $result['symbol_left'],
                        'symbol_right'  => $result['symbol_right'],
                        'decimal_place' => $result['decimal_place'],
                        'value'         => $result['value'],
                        'status'        => $result['status'],
                        'date_modified' => $result['date_modified'],
                    );
                }

                $this->cache->push('localization.currency', $currency_data);
            }

            return $currency_data;
        }
    }

    /**
     * NOTE: Update of currency values works only for default store!
     *
     * @throws AException
     */

    public function updateCurrencies()
    {
        $this->load->model('setting/setting');
        $settings = $this->model_setting_setting->getSetting('details', 0);
        $api_key = isset($settings['alphavantage_api_key']) && $settings['alphavantage_api_key'] ? $settings['alphavantage_api_key'] : 'P6WGY9G9LB22GMBJ';
        $base_currency_code = $settings['config_currency'];

        $query = $this->db->query("SELECT *
                                    FROM ".$this->db->table("currencies")." 
                                    WHERE code != '".$this->db->escape($base_currency_code)."'
                                        AND date_modified > '".date("Y-m-d H:i:s", strtotime('-1 day'))."'");

        foreach ($query->rows as $result) {
            $url = 'https://www.alphavantage.co/query?function=CURRENCY_EXCHANGE_RATE&from_currency='.$base_currency_code.'&to_currency='.$result['code'].'&apikey='.$api_key;
            $connect = new AConnect(true);
            $json = $connect->getData($url);
            if (!$json) {
                $msg = 'Currency Auto Updater Warning: Currency rate code '.$result['code'].' not updated.';
                $error = new AError($msg);
                $error->toLog()->toMessages();
                continue;
            }

            if (isset($json["Realtime Currency Exchange Rate"]["5. Exchange Rate"])) {
                $value = (float)$json["Realtime Currency Exchange Rate"]["5. Exchange Rate"];
                $this->db->query(
                    "UPDATE ".$this->db->table("currencies")." 
                            SET value = '".$value."', 
                                date_modified = NOW() 
                            WHERE code = '".$this->db->escape($result['code'])."'");
            } elseif (isset($json['Information'])) {
                $msg = 'Currency Auto Updater Info: '.$json['Information'];
                $error = new AError($msg);
                $error->toLog()->toMessages();
            }
            usleep(500);
        }

        $sql = "UPDATE ".$this->db->table("currencies")." 
                              SET value = '1.00000',
                                  date_modified = NOW()
                              WHERE code = '".$this->db->escape($base_currency_code)."'";
        $this->db->query($sql);
        $this->cache->remove('localization');

    }

    /**
     * @param string $new_currency_code
     *
     * @return bool
     */
    public function switchConfigCurrency($new_currency_code)
    {
        $new_currency_code = mb_strtoupper(trim($new_currency_code));
        $all_currencies = $this->getCurrencies();
        $new_currency = $all_currencies[$new_currency_code];
        if (!$new_currency_code || !$new_currency) {
            return false;
        }
        $scale = 1 / $new_currency['value'];
        foreach ($all_currencies as $code => $currency) {
            if ($code == $new_currency_code) {
                $new_value = 1.00000;
            } else {
                $new_value = $currency['value'] * $scale;
            }
            $this->editCurrency($currency['currency_id'], array('value' => $new_value));
        }

        return true;
    }

    /**
     * @return int
     */
    public function getTotalCurrencies()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM ".$this->db->table("currencies").";");

        return $query->row['total'];
    }
}
