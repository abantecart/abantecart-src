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

/** @var AController $this */
//prior upgrades fix
$sqlSelect = "SELECT * 
              FROM " . $this->db->table('dataset_definition') . "
              WHERE dataset_id=1 
                AND dataset_column_name = 'settings'";
$result = $this->db->query($sqlSelect);

if (!$result->num_rows) {
    $sqlAlter = "INSERT INTO " . $this->db->table('dataset_definition') . "
        (dataset_id, dataset_column_name, dataset_column_type,dataset_column_sort_order)
    VALUES (1,'settings','text',8)";
    $this->db->query($sqlAlter, true);
}

