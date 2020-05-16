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
    array(
        'type'     => 'total',
        'key'      => 'avatax_integration_total',
        'status'   => 1,
        'priority' => 10,
        'version'  => '1.0',
    )
);
// edit settings
$this->load->model('setting/setting');
//insert avatax_integration_total before total
$sort = $this->config->get('total_sort_order');
$calc = $this->config->get('total_calculation_order');
$this->model_setting_setting->editSetting('total',
    array('total_sort_order' => ($sort + 1), 'total_calculation_order' => ($calc + 1)));
$this->model_setting_setting->editSetting(
    'avatax_integration_total',
    array(
        'avatax_integration_total_status'            => 0,
        'avatax_integration_total_sort_order'        => $sort,
        'avatax_integration_total_calculation_order' => $calc,
        'avatax_integration_total_total_type'        => 'avatax_integration',
    )
);

$this->extension_manager->addDependant('avatax_integration_total', 'avatax_integration');
