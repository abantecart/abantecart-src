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
    $sql = "INSERT INTO ".$this->db->table('forms')." (`form_name`, `controller`, `success_page`, `status`, `locked`)
            VALUES ('".$frmKey."','','',1,1)";
    $this->db->query($sql);
    $forms[$frmKey] = (int)$this->db->getLastId();
    foreach ($languages as $lang) {
        $langId = (int)$lang['language_id'];
        $sql = "INSERT INTO ".$this->db->table('form_descriptions')." (`form_id`, `language_id`, `description`)
                VALUES ('".$forms[$frmKey]."','".$langId."','".$this->db->escape($frmName)."')";
        $this->db->query($sql);
    }
}

$fieldArray = [
    [
        'form_id' => $forms['AddressFrm'],
        'group_id' => null,
        'field_name' => 'country_id',
        'element_type' => 'O',
        'sort_order' => 1,
        'attributes' => 'autocomplete="country"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^[1-9]\d*$/',
        'locked' => 1,
        'descriptions' => ['name' => 'Country', 'description' => '', 'error_text' => 'Please select a country!']
    ],
    [
        'form_id' => $forms['AddressFrm'],
        'group_id' => null,
        'field_name' => 'zone_id',
        'element_type' => 'Z',
        'sort_order' => 2,
        'attributes' => 'autocomplete="address-level1"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^[1-9]\d*$/',
        'locked' => 1,
        'descriptions' => ['name' => 'Zone', 'description' => '', 'error_text' => 'Please select a region/state!']
    ],
    [
        'form_id' => $forms['AddressFrm'],
        'group_id' => null,
        'field_name' => 'company',
        'element_type' => 'I',
        'sort_order' => 3,
        'attributes' => 'autocomplete="organization"',
        'settings' => '',
        'required' => 0,
        'status' => 1,
        'regexp_pattern' => '/^.{0,32}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Company', 'description' => '', 'error_text' => 'Company Name must be less than 32 characters!']
    ],
    [
        'form_id' => $forms['AddressFrm'],
        'group_id' => null,
        'field_name' => 'address_1',
        'element_type' => 'I',
        'sort_order' => 4,
        'attributes' => 'minlength="3" maxlength="128" autocomplete="address-line1"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{3,128}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Address Line 1', 'description' => '', 'error_text' => 'Address Line 1 must be between 3 and 128 characters!']
    ],
    [
        'form_id' => $forms['AddressFrm'],
        'group_id' => null,
        'field_name' => 'address_2',
        'element_type' => 'I',
        'sort_order' => 5,
        'attributes' => 'minlength="0" maxlength="128" autocomplete="address-line2"',
        'settings' => '',
        'required' => 0,
        'status' => 1,
        'regexp_pattern' => '/^.{0,128}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Address Line 2', 'description' => '', 'error_text' => 'Address Line 2 must be less than 128 characters!']
    ],
    [
        'form_id' => $forms['AddressFrm'],
        'group_id' => null,
        'field_name' => 'city',
        'element_type' => 'I',
        'sort_order' => 6,
        'attributes' => 'maxlength="128" autocomplete="address-level2"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{1,128}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'City', 'description' => '', 'error_text' => 'City must be between 3 and 128 characters!']
    ],
    [
        'form_id' => $forms['AddressFrm'],
        'group_id' => null,
        'field_name' => 'postcode',
        'element_type' => 'I',
        'sort_order' => 7,
        'attributes' => 'autocomplete="postal-code"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^[A-Za-z0-9\- ]+$/',
        'locked' => 1,
        'descriptions' => ['name' => 'Zip/Post Code', 'description' => '', 'error_text' => 'Zip/Post Code must be less than 11 characters!']
    ],
    [
        'form_id' => $forms['AddressFrm'],
        'group_id' => null,
        'field_name' => 'firstname',
        'element_type' => 'I',
        'sort_order' => 8,
        'attributes' => 'minlength="1" maxlength="32" autocomplete="given-name"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{1,32}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'First Name', 'description' => '', 'error_text' => 'First Name must be between 1 and 32 characters!']
    ],
    [
        'form_id' => $forms['AddressFrm'],
        'group_id' => null,
        'field_name' => 'lastname',
        'element_type' => 'I',
        'sort_order' => 9,
        'attributes' => 'minlength="1" maxlength="32" autocomplete="family-name"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{1,32}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Last Name', 'description' => '', 'error_text' => 'Last Name must be between 1 and 32 characters!']
    ],
    [
        'form_id' => $forms['AddressFrm'],
        'group_id' => null,
        'field_name' => 'default',
        'element_type' => 'C',
        'sort_order' => 11,
        'attributes' => '',
        'settings' => '',
        'required' => 0,
        'status' => 1,
        'regexp_pattern' => '',
        'locked' => 1,
        'descriptions' => ['name' => 'Default Address', 'description' => '', 'error_text' => '']
    ],
    [
        'form_id' => $forms['AddressFrm'],
        'group_id' => null,
        'field_name' => 'vat_id',
        'element_type' => 'I',
        'sort_order' => 10,
        'attributes' => 'autocomplete="off"',
        'settings' => '',
        'required' => 0,
        'status' => 0,
        'regexp_pattern' => '/^.{8,14}$/u',
        'locked' => 0,
        'descriptions' => ['name' => 'VAT ID', 'description' => '', 'error_text' => 'VAT ID Code must be between 8 and 14 characters!']
    ],
    [
        'form_id' => $forms['GuestCheckoutFrm'],
        'group_id' => null,
        'field_name' => 'country_id',
        'element_type' => 'O',
        'sort_order' => 1,
        'attributes' => 'data-pair-with="zone_id" autocomplete="country"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^[1-9]\d*$/',
        'locked' => 1,
        'descriptions' => ['name' => 'Country', 'description' => '', 'error_text' => 'Please select a country!']
    ],
    [
        'form_id' => $forms['GuestCheckoutFrm'],
        'group_id' => null,
        'field_name' => 'zone_id',
        'element_type' => 'Z',
        'sort_order' => 2,
        'attributes' => 'data-pair-with="country_id" autocomplete="address-level1"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^[1-9]\d*$/',
        'locked' => 1,
        'descriptions' => ['name' => 'Region/State', 'description' => '', 'error_text' => 'Please select a region/state!']
    ],
    [
        'form_id' => $forms['GuestCheckoutFrm'],
        'group_id' => null,
        'field_name' => 'company',
        'element_type' => 'I',
        'sort_order' => 3,
        'attributes' => 'autocomplete="organization"',
        'settings' => '',
        'required' => 0,
        'status' => 1,
        'regexp_pattern' => '/^.{0,32}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Company', 'description' => '', 'error_text' => 'Company Name must be less than 32 characters!']
    ],
    [
        'form_id' => $forms['GuestCheckoutFrm'],
        'group_id' => null,
        'field_name' => 'address_1',
        'element_type' => 'I',
        'sort_order' => 4,
        'attributes' => 'minlength="3" maxlength="128" autocomplete="address-line1"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{3,128}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Address Line 1', 'description' => '', 'error_text' => 'Address Line 1 must be between 3 and 128 characters!']
    ],
    [
        'form_id' => $forms['GuestCheckoutFrm'],
        'group_id' => null,
        'field_name' => 'address_2',
        'element_type' => 'I',
        'sort_order' => 5,
        'attributes' => 'minlength="0" maxlength="128" autocomplete="address-line2"',
        'settings' => '',
        'required' => 0,
        'status' => 1,
        'regexp_pattern' => '/^.{0,128}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Address Line 2', 'description' => '', 'error_text' => 'Address Line 2 must be less than 128 characters!']
    ],
    [
        'form_id' => $forms['GuestCheckoutFrm'],
        'group_id' => null,
        'field_name' => 'city',
        'element_type' => 'I',
        'sort_order' => 6,
        'attributes' => 'maxlength="128" data-pair-with="postcode" autocomplete="address-level2"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{1,128}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'City', 'description' => '', 'error_text' => 'City must be between 3 and 128 characters!']
    ],
    [
        'form_id' => $forms['GuestCheckoutFrm'],
        'group_id' => null,
        'field_name' => 'postcode',
        'element_type' => 'I',
        'sort_order' => 7,
        'attributes' => 'data-pair-with="city" autocomplete="postal-code"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^[A-Za-z0-9\- ]+$/',
        'locked' => 1,
        'descriptions' => ['name' => 'Zip/Post Code', 'description' => '', 'error_text' => 'Zip/Post Code must be less than 11 characters!']
    ],
    [
        'form_id' => $forms['GuestCheckoutFrm'],
        'group_id' => null,
        'field_name' => 'firstname',
        'element_type' => 'I',
        'sort_order' => 8,
        'attributes' => 'minlength="1" maxlength="32" data-pair-with="lastname" autocomplete="given-name"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{1,32}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'First Name', 'description' => '', 'error_text' => 'First Name must be between 1 and 32 characters!']
    ],
    [
        'form_id' => $forms['GuestCheckoutFrm'],
        'group_id' => null,
        'field_name' => 'lastname',
        'element_type' => 'I',
        'sort_order' => 9,
        'attributes' => 'minlength="1" maxlength="32" data-pair-with="firstname" autocomplete="family-name"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{1,32}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Last Name', 'description' => '', 'error_text' => 'Last Name must be between 1 and 32 characters!']
    ],
    [
        'form_id' => $forms['GuestCheckoutFrm'],
        'group_id' => null,
        'field_name' => 'vat_id',
        'element_type' => 'I',
        'sort_order' => 10,
        'attributes' => 'autocomplete="off"',
        'settings' => '',
        'required' => 0,
        'status' => 0,
        'regexp_pattern' => '/^.{8,14}$/u',
        'locked' => 0,
        'descriptions' => ['name' => 'VAT ID', 'description' => '', 'error_text' => 'VAT ID Code must be between 8 and 14 characters!']
    ],
    [
        'form_id' => $forms['GuestCheckoutFrm'],
        'group_id' => null,
        'field_name' => 'email',
        'element_type' => 'E',
        'sort_order' => 15,
        'attributes' => 'minlength="1" maxlength="96" autocomplete="email"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '',
        'locked' => 1,
        'descriptions' => ['name' => 'Email', 'description' => '', 'error_text' => 'Your email is not provided or invalid!']
    ],
    [
        'form_id' => $forms['GuestCheckoutFrm'],
        'group_id' => null,
        'field_name' => 'telephone',
        'element_type' => 'F',
        'sort_order' => 16,
        'attributes' => 'maxlength="32" autocomplete="tel"',
        'settings' => '',
        'required' => 0,
        'status' => 1,
        'regexp_pattern' => '/^[+\- 0-9\(\)]+$/',
        'locked' => 1,
        'descriptions' => ['name' => 'Phone Number', 'description' => '', 'error_text' => 'Your contact phone number is not provided or invalid.']
    ],
    [
        'form_id' => $forms['CustomerFrm'],
        'group_id' => null,
        'field_name' => 'loginname',
        'element_type' => 'I',
        'sort_order' => 1,
        'attributes' => 'minlength="5" maxlength="64" autocomplete="username"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^[A-Za-z0-9._]{5,64}$/i',
        'locked' => 1,
        'descriptions' => ['name' => 'Login Name', 'description' => '', 'error_text' => 'Login Name must be between 5 and 65 characters!']
    ],
    [
        'form_id' => $forms['CustomerFrm'],
        'group_id' => null,
        'field_name' => 'firstname',
        'element_type' => 'I',
        'sort_order' => 2,
        'attributes' => 'minlength="1" maxlength="32" data-pair-with="lastname" autocomplete="given-name"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{1,32}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'First Name', 'description' => '', 'error_text' => 'First Name must be between 1 and 32 characters!']
    ],
    [
        'form_id' => $forms['CustomerFrm'],
        'group_id' => null,
        'field_name' => 'lastname',
        'element_type' => 'I',
        'sort_order' => 3,
        'attributes' => 'minlength="1" maxlength="32" data-pair-with="firstname" autocomplete="family-name"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{1,32}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Last Name', 'description' => '', 'error_text' => 'Last Name must be between 1 and 32 characters!']
    ],
    [
        'form_id' => $forms['CustomerFrm'],
        'group_id' => null,
        'field_name' => 'email',
        'element_type' => 'E',
        'sort_order' => 4,
        'attributes' => 'minlength="1" maxlength="96" autocomplete="email"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '',
        'locked' => 1,
        'descriptions' => ['name' => 'Email', 'description' => '', 'error_text' => 'Your email is not provided or invalid!']
    ],
    [
        'form_id' => $forms['CustomerFrm'],
        'group_id' => null,
        'field_name' => 'telephone',
        'element_type' => 'F',
        'sort_order' => 5,
        'attributes' => 'maxlength="32" autocomplete="tel"',
        'settings' => '',
        'required' => 0,
        'status' => 1,
        'regexp_pattern' => '/^[+\- 0-9\(\)]+$/',
        'locked' => 1,
        'descriptions' => ['name' => 'Phone Number', 'description' => '', 'error_text' => 'Your contact phone number is not provided or invalid.']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['details'],
        'field_name' => 'firstname',
        'element_type' => 'I',
        'sort_order' => 1,
        'attributes' => 'minlength="1" maxlength="32" data-pair-with="lastname" autocomplete="given-name"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{1,32}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'First Name', 'description' => '', 'error_text' => 'First Name must be between 1 and 32 characters!']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['details'],
        'field_name' => 'lastname',
        'element_type' => 'I',
        'sort_order' => 2,
        'attributes' => 'minlength="1" maxlength="32" data-pair-with="firstname" autocomplete="family-name"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{1,32}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Last Name', 'description' => '', 'error_text' => 'Last Name must be between 1 and 32 characters!']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['details'],
        'field_name' => 'email',
        'element_type' => 'E',
        'sort_order' => 3,
        'attributes' => 'minlength="1" maxlength="96" autocomplete="email"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '',
        'locked' => 1,
        'descriptions' => ['name' => 'Email', 'description' => '', 'error_text' => 'Your email is not provided or invalid!']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['details'],
        'field_name' => 'telephone',
        'element_type' => 'F',
        'sort_order' => 4,
        'attributes' => 'maxlength="32" autocomplete="tel"',
        'settings' => '',
        'required' => 0,
        'status' => 1,
        'regexp_pattern' => '/^[+\- 0-9\(\)]+$/',
        'locked' => 1,
        'descriptions' => ['name' => 'Phone Number', 'description' => '', 'error_text' => 'Your contact phone number is not provided or invalid.']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['address'],
        'field_name' => 'country_id',
        'element_type' => 'O',
        'sort_order' => 10,
        'attributes' => 'autocomplete="country"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^[1-9]\d*$/',
        'locked' => 1,
        'descriptions' => ['name' => 'Country', 'description' => '', 'error_text' => 'Please select a country!']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['address'],
        'field_name' => 'zone_id',
        'element_type' => 'Z',
        'sort_order' => 11,
        'attributes' => 'autocomplete="address-level1"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^[1-9]\d*$/',
        'locked' => 1,
        'descriptions' => ['name' => 'Region/State', 'description' => '', 'error_text' => 'Please select a region/state!']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['address'],
        'field_name' => 'address_1',
        'element_type' => 'I',
        'sort_order' => 13,
        'attributes' => 'minlength="3" maxlength="128" autocomplete="address-line1"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{3,128}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Address Line 1', 'description' => '', 'error_text' => 'Address Line 1 must be between 3 and 128 characters!']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['address'],
        'field_name' => 'address_2',
        'element_type' => 'I',
        'sort_order' => 14,
        'attributes' => 'minlength="0" maxlength="128" autocomplete="address-line2"',
        'settings' => '',
        'required' => 0,
        'status' => 1,
        'regexp_pattern' => '/^.{0,128}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Address Line 2', 'description' => '', 'error_text' => 'Address Line 2 must be less than 128 characters!']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['address'],
        'field_name' => 'city',
        'element_type' => 'I',
        'sort_order' => 15,
        'attributes' => 'maxlength="128" autocomplete="address-level2"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^.{1,128}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'City', 'description' => '', 'error_text' => 'City must be between 3 and 128 characters!']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['address'],
        'field_name' => 'postcode',
        'element_type' => 'I',
        'sort_order' => 16,
        'attributes' => 'autocomplete="postal-code"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^[A-Za-z0-9\- ]+$/',
        'locked' => 1,
        'descriptions' => ['name' => 'Zip/Post Code', 'description' => '', 'error_text' => 'Zip/Post Code must be less than 11 characters!']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['address'],
        'field_name' => 'company',
        'element_type' => 'I',
        'sort_order' => 12,
        'attributes' => 'autocomplete="organization"',
        'settings' => '',
        'required' => 0,
        'status' => 1,
        'regexp_pattern' => '/^.{0,32}$/u',
        'locked' => 1,
        'descriptions' => ['name' => 'Company', 'description' => '', 'error_text' => 'Company Name must be less than 32 characters!']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['login'],
        'field_name' => 'loginname',
        'element_type' => 'I',
        'sort_order' => 20,
        'attributes' => 'minlength="5" maxlength="64" autocomplete="username"',
        'settings' => '',
        'required' => 1,
        'status' => 1,
        'regexp_pattern' => '/^[A-Za-z0-9._]{5,64}$/i',
        'locked' => 1,
        'descriptions' => ['name' => 'Login Name', 'description' => '', 'error_text' => 'Login Name must be alphanumeric only and between 5 and 64 characters!']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['newsletter'],
        'field_name' => 'newsletter',
        'element_type' => 'C',
        'sort_order' => 30,
        'attributes' => '',
        'settings' => '',
        'required' => 0,
        'status' => 1,
        'regexp_pattern' => '',
        'locked' => 1,
        'descriptions' => ['name' => 'Subscribe', 'description' => '', 'error_text' => '']
    ],
    [
        'form_id' => $forms['RegisterCustomerFrm'],
        'group_id' => $groups['address'],
        'field_name' => 'vat_id',
        'element_type' => 'I',
        'sort_order' => 28,
        'attributes' => 'autocomplete="off"',
        'settings' => '',
        'required' => 0,
        'status' => 0,
        'regexp_pattern' => '/^.{8,14}$/u',
        'locked' => 0,
        'descriptions' => ['name' => 'VAT ID', 'description' => '', 'error_text' => 'VAT ID Code must be between 8 and 14 characters!']
    ],
];


// Insert via loop using $forms and $groups
foreach ($fieldArray as $r) {
    // Prefer auto-created icon resource if available
    $resourceId = null;
    if (isset($iconMap[$r['field_name']])) {
        $resourceId = (int)$iconMap[$r['field_name']];
    }elseif (isset($iconMap[ str_replace("_","",$r['field_name'])])) {
        $resourceId = (int)$iconMap[str_replace("_","",$r['field_name'])];
    }

    $fieldName      = $this->db->escape($r['field_name']);
    $elementType    = $this->db->escape($r['element_type']);
    $sortOrder      = (int)$r['sort_order'];
    $attributes     = $this->db->escape($r['attributes']);
    $settings       = $this->db->escape($r['settings']);
    $required       = (int)$r['required'];
    $status         = (int)$r['status'];
    $regexpPattern  = $r['regexp_pattern'] === '' ? '' : $this->db->escape($r['regexp_pattern']);
    $locked         = (int)$r['locked'];

    $sql = "INSERT INTO " . $this->db->table('fields') . "
        (`form_id`, `group_id`, `field_name`, `element_type`, `sort_order`, `attributes`, `settings`, `required`, `status`, `regexp_pattern`, `resource_id`, `locked`)
        VALUES (
            " . $r['form_id'] . ",
            " . $this->db->intOrNull($r['group_id']) . ",
            '" . $fieldName . "',            
            '" . $elementType . "',
            " . $sortOrder . ",
            '" . $attributes . "',
            '" . $settings . "',
            " . $required . ",
            " . $status . ",
            " . ($regexpPattern === '' ? "''" : "'".$regexpPattern."'") . ",
            " . $this->db->intOrNull($resourceId) . ",
            ".$locked.")";
    $this->db->query($sql);
    $fieldId = (int)$this->db->getLastId();
    foreach ($languages as $lang) {
        $langId = (int)$lang['language_id'];
        $sql = "INSERT INTO ".$this->db->table('field_descriptions')." 
                (`field_id`, `name`, `description`, `language_id`, `error_text`)
                VALUES (
                '".$fieldId."', 
                '".$this->db->escape($r['descriptions']['name'])."', 
                '".$this->db->escape($r['descriptions']['description'])."', 
                '".$langId."',
                '".$this->db->escape($r['descriptions']['error_text'])."'
                )";
        $this->db->query($sql);
    }
}
