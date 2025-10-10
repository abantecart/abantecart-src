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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

if (!class_exists('ExtensionFormsManager')) {
    include('core/forms_manager.php');
}

$controllers = [
    'storefront' => [
        'blocks/custom_form_block',
        'pages/forms_manager/default_email',
        'responses/forms_manager/validate',
    ],
    'admin'      => [
        'pages/tool/forms_manager',
        'responses/listing_grid/form',
        'responses/forms_manager/fields',
        'responses/forms_manager/groups',
    ],
];

$models = [
    'storefront' => ['tool/forms_manager'],
    'admin'      => ['tool/forms_manager'],
];

$languages = [
    'storefront' => [
        'forms_manager/forms_manager',
        'forms_manager/default_email'
    ],
    'admin'      => ['forms_manager/forms_manager'],
];

$templates = [
    'storefront' => [
        'blocks/custom_form_block.tpl',
        'blocks/custom_form_block_content.tpl',
        'blocks/custom_form_block_header.tpl',
        'pages/default_email.tpl',
        'pages/default_email_success.tpl',
    ],
    'admin'      => [
        'pages/tool/forms_manager_list.tpl',
        'pages/tool/forms_manager_form.tpl',
        'pages/tool/forms_manager_block_form.tpl',
        'responses/forms_manager/field_values.tpl',
        'responses/forms_manager/field_values_country_zone.tpl',
        'responses/forms_manager/field_value_row.tpl',
        'responses/forms_manager/file_upload_settings.tpl',
    ],
];