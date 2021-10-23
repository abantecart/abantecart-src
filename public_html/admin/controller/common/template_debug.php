<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>
  
 UPGRADE NOTE: 
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.  
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerCommonTemplateDebug extends AController
{
    public function main($instance_id = 0, $details = [])
    {
        $block_details = $this->layout->getBlockDetails($details['block_id']);
        $parent_block = $this->layout->getBlockDetails($block_details['parent_instance_id']);

        $tmpl_data = [];
        $tmpl_data['id'] = $details['block_id'];
        $tmpl_data['name'] = $block_details['block_txt_id'];
        $tmpl_data['tpl_path'] = $details['block_tpl'];
        $tmpl_data['controller'] = $block_details['controller'];
        $tmpl_data['controller_path'] = str_replace(DIR_ROOT.'/', '', $details['block_controller']);
        $tmpl_data['parent_block'] = $parent_block['block_txt_id'];
        $tmpl_data['parent'] = [];
        $tmpl_data['parent']['id'] = $parent_block['instance_id'];
        $tmpl_data['parent']['name'] = $parent_block['block_txt_id'];
        $tmpl_data['parent']['tpl_path'] = $details['parent_tpl'];
        $tmpl_data['parent']['controller'] = $parent_block['controller'];
        $tmpl_data['parent']['controller_path'] = str_replace(DIR_ROOT.'/', '', $details['parent_controller']);

        $this->view->batchAssign($tmpl_data);

        $this->processTemplate('common/template_debug.tpl');

    }
}