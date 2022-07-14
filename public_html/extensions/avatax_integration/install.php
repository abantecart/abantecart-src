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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

//add avatax_integration total
/**
 * @var AController $this
 */
$child_extension_id = $this->extension_manager->add(
    [
        'type'     => 'total',
        'key'      => 'avatax_integration_total',
        'status'   => 1,
        'priority' => 10,
        'version'  => '1.1',
    ]
);
// edit settings
$this->load->model('setting/setting');
//insert avatax_integration_total before total
/**
 * NOTE! order calculation must be lower than 999 (hardcoded for "balance").
 * It affect on balance application during fastCheckout process
 */

$this->model_setting_setting->editSetting(
    'avatax_integration_total',
    [
        'avatax_integration_total_status'            => 1,
        'avatax_integration_total_sort_order'        => 500,
        'avatax_integration_total_calculation_order' => 500,
        'avatax_integration_total_total_type'        => 'tax',
    ]
);

$this->extension_manager->addDependant('avatax_integration_total', 'avatax_integration');
