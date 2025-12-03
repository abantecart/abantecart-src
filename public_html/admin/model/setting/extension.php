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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelSettingExtension extends Model
{
    /*
    * Get installed payment extensions. Used in configuration for shipping extensions
    */
    public function getPayments()
    {
        return $this->getByType('payment');
    }

    public function getShippings()
    {
        return $this->getByType('shipping');
    }

    public function getTemplates()
    {
        return $this->getByType('template');
    }

    public function getTotals()
    {
        return $this->getByType('total');
    }

    /**
     * @param string $type
     * @return array
     * @throws AException
     */
    public function getByType(string $type)
    {
        $list = $this->extensions->getExtensionsList(
            [
                'filter' => $type,
                'status' => 1
            ]
        );
        return $list->rows;
    }

    /**
     * @deprecated
     * Get enabled payment extensions that support handler class.
     */
    public function getPaymentsWithHandler()
    {
        $query = $this->db->query(
            "SELECT *
           FROM " . $this->db->table("extensions") . "
           WHERE `type` = 'payment' and status = 1"
        );
        $output = [];
        $output[] = ['' => ''];
        foreach ($query->rows as $row) {
            if (file_exists(DIR_EXT . $row['key'] . DIR_EXT_CORE . 'lib/handler.php')) {
                $output[] = $row;
            }
        }
        return $output;
    }

    /**
     * @param string $type
     * @param string $key
     * @return void
     * @throws AException
     */
    public function install($type, $key)
    {
        $this->db->query(
            "INSERT INTO " . $this->db->table("extensions") . "
            SET
                `type` = '" . $this->db->escape($type) . "',
                `key` = '" . $this->db->escape($key) . "'"
        );
    }

    /**
     * @param string $type
     * @param string $key
     * @return void
     * @throws AException
     */
    public function uninstall($type, $key)
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table("extensions") . "
            WHERE `type` = '" . $this->db->escape($type) . "'
                    AND `key` = '" . $this->db->escape($key) . "'"
        );
    }
}
