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

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelExtensionCardKnox extends Model
{

    public $data = [];

    public function processRefund($data)
    {
        $this->language->load('cardknox/cardknox');

        $sql = "INSERT INTO ".$this->db->table('order_totals')." 
                    (`order_id`,`title`,`text`,`value`,`sort_order`,`type`)
                VALUES ('".(int)$data['order_id']."',
                        '".$this->db->escape($this->language->get('cardknox_refund_title'))."',
                        '-".$this->currency->format((float)$data['amount'], $data['currency'])."',
                        '-".(float)$data['amount']."',
                        '500',
                        'cardknox_refund')";
        $this->db->query($sql);

        $sql = "SELECT * 
                FROM ".$this->db->table("order_totals")." 
                WHERE type='total' AND order_id = '".(int)$data['order_id']."'";
        $res = $this->db->query($sql);
        $total = $res->row;

        $sql = "UPDATE ".$this->db->table("order_totals")." 
                SET `text` = '".$this->currency->format(($total['value'] - $data['amount']), $data['currency'])."',
                `value` = '".((float)$total['value'] - (float)$data['amount'])."'
                WHERE order_id = '".(int)$data['order_id']."'
                    AND type='total'";
        $this->db->query($sql);
    }

    public function updatePaymentMethodData($order_id, $data)
    {

        if (is_array($data)) {
            $data = serialize($data);
        }

        return $this->db->query(
            "UPDATE ".$this->db->table('orders')."
            SET payment_method_data = '".$this->db->escape($data)."'
            WHERE order_id = '".(int)$order_id."'"
        );
    }

    public function addOrderHistory($data)
    {
        $this->db->query("INSERT INTO ".$this->db->table("order_history")."
                            SET order_id = '".(int)$data['order_id']."',
                                order_status_id = '".(int)$data['order_status_id']."',
                                notify = '".(int)$data['notify']."',
                                comment = '".$this->db->escape($data['comment'])."',
                                date_added = NOW()");
    }
}
