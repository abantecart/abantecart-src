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

if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}

class ModelExtensionCardknox extends Model {
    public function getMethod($address)
    {
        $this->load->language('cardknox/cardknox');

        if ($this->config->get('cardknox_status')) {
            $sql = "SELECT *
                    FROM ".$this->db->table('zones_to_locations')."
                    WHERE location_id = '".(int)$this->config->get('cardknox_location_id')."'
                           AND country_id = '".(int)$address['country_id']."'
                           AND (zone_id = '".(int)$address['zone_id']."' OR zone_id = '0')";
            $query = $this->db->query($sql);

            if (!$this->config->get('cardknox_location_id')) {
                $status = true;
            } elseif ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        $method_data = [];

        if ($status) {
            $method_data = [
                'id'         => 'cardknox',
                'title'      => $this->language->get('text_title'),
                'sort_order' => $this->config->get('cardknox_sort_order'),
            ];
        }

        return $method_data;
    }

    public function getCreditCardTypes()
    {
        return [
            'Visa'       => 'Visa',
            'MasterCard' => 'MasterCard',
            'Discover'   => 'Discover',
            'Amex'       => 'American Express',
        ];
    }
 }
