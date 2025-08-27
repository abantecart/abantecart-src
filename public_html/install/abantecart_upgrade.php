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
//todo:upgrade icon with class fa-money to fa-money-bill inside RL !!!!!
//todo: update menu item icon rl_id if template is novator/default and current icon contains fa-money class

//todo: move error messages from other languages into field's error_text!!!

$resourceManager = new AResourceManager();
$resourceManager->setType('image');
$icons = [
    'country_id' => ['name' => 'Field Icon Country', 'code' => '<i class="fa fa-globe"></i>'],
    'zone_id'    => ['name' => 'Field Icon Zone', 'code' => '<i class="fa fa-map"></i>'],
    'postcode'   => ['name' => 'Field Icon Postcode', 'code' => '<i class="fa fa-signs-post"></i>'],
    'company'    => ['name' => 'Field Icon Company', 'code' => '<i class="fa-solid fa-handshake"></i>'],
    'address_1'  => ['name' => 'Field Icon Address Line 1', 'code' => '<i class="fa-solid fa-location-pin"></i>'],
    'address_2'  => ['name' => 'Field Icon Address Line 2', 'code' => '<i class="fa-solid fa-location-dot"></i>'],
    'city'       => ['name' => 'Field Icon City', 'code' => '<i class="fa-solid fa-city"></i>'],
    'firstname'  => ['name' => 'Field Icon First Name', 'code' => '<i class="fa fa-solid fa-user"></i>'],
    'lastname'   => ['name' => 'Field Icon Last Name', 'code' => '<i class="fa fa-solid fa-user-tag"></i>'],
    'email'      => ['name' => 'Field Icon Email', 'code' => '<i class="fa-solid fa-at"></i>'],
    'telephone'  => ['name' => 'Field Icon Phone', 'code' => '<i class="fa-solid fa-phone"></i>'],
    'loginname'  => ['name' => 'Field Icon Login Name', 'code' => '<i class="fa-solid fa-fingerprint"></i>'],
];

$languages = $this->language->getAvailableLanguages();
$iconMap = [];
foreach ($icons as $fieldKey => $icon) {
    $names = $titles = $descriptions = [];
    foreach ($languages as $lang) {
        $langId = (int) $lang['language_id'];
        $names[$langId] = $icon['name'];
        $titles[$langId] = '';
        $descriptions[$langId] = '';
    }
    $iconMap[$fieldKey] = $resourceManager->addResource(
        [
            'name'          => $names,
            'title'         => $titles,
            'description'   => $descriptions,
            'resource_path' => '',
            'resource_code' => $icon['code'],
        ]
    );
}

$grp = [
    'details' => 'Your Personal Details',
    'address' => 'Your Address',
    'login' => 'Login Details',
    'newsletter' => 'Newsletter'
];

foreach($grp as $grpKey => $grpName) {
    $sql = "INSERT INTO " . $this->db->table('field_groups') . " (`group_txt_id`) VALUES ('".$grpKey."')";
    $this->db->query($sql);
    $groups[$grpKey] = (int)$this->db->getLastId();
    foreach ($languages as $lang) {
        $langId = (int)$lang['language_id'];
        $sql = "INSERT INTO ".$this->db->table('field_group_descriptions')."
                (`group_id`, `name`, `description`, `language_id`)
                VALUES (".$groups[$grpKey].", '".$this->db->escape($grpName)."','',".$langId.")";
        $this->db->query($sql);
    }
}
$frms=[
    'AddressFrm' => 'Customer Address Form',
    'GuestCheckoutFrm' => 'Guest Address and Details Form',
    'CustomerFrm' => 'Customer Details Form',
    'RegisterCustomerFrm' => 'Customer Registration Form'
];
foreach($frms as $frmKey => $frmName) {
    $sql = "INSERT INTO `ac_forms` (`form_name`, `controller`, `success_page`, `status`, `locked`)
            VALUES ('".$frmKey."','','',1,1)";
    $this->db->query($sql);
    $forms[$frmKey] = (int)$this->db->getLastId();
    foreach ($languages as $lang) {
        $langId = (int)$lang['language_id'];
        $sql = "INSERT INTO ".$this->db->table('field_group_descriptions')." (`form_id`, `language_id`, `description`)
                VALUES ('".$forms[$frmKey]."','".$langId."',".$this->db->escape($frmName).")";
        $this->db->query($sql);
    }
}


