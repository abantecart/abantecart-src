<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2024 Belavier Commerce LLC

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

class ControllerBlocksStoreHours extends AController
{
    public $data = [];
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->data['heading_title'] = $this->language->get('heading_title', 'blocks/store_hours');
        $this->data['open_text'] = $this->language->get('open', 'blocks/store_hours');
        $this->data['closed_text'] = $this->language->get('closed', 'blocks/store_hours');

        //get store hours from config
        $this->data['store_hours'] = [];
        $daysOftheWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        foreach ($daysOftheWeek as $day) {
            $open = "config_opening_{$day}_opens";
            $close = "config_opening_{$day}_closes";
            $this->data['store_hours'][$day]["text"] = $this->language->get($day, 'blocks/store_hours');
            $this->data['store_hours'][$day]["open"] = $this->config->get($open);
            $this->data['store_hours'][$day]["closed"] = $this->config->get($close);
        }
        $this->view->batchAssign($this->data);
        $this->processTemplate('blocks/store_hours.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
