<?php   
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>
  
 UPGRADE NOTE: 
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.  
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerCommonTemplateDebug extends AController {
	public function main() {
		
		$args = func_get_arg(1);
		$block_details = $this->layout->getBlockDetails($args['block_id']);
		$block_tmpl = $this->layout->getBlockTemplate($args['block_id']);
		$parent_block = $this->layout->getBlockDetails($block_details['parent_instance_id']);
		$parent_tmpl = $this->layout->getBlockTemplate($block_details['parent_instance_id']);
		
		$tmpl_data = array();
		$tmpl_data['id'] = $args['block_id'];
		$tmpl_data['name'] = $block_details['block_txt_id'];
		$tmpl_data['tpl_path'] = $args['block_tpl'];
		$tmpl_data['controller'] = $block_details['controller'];
		$tmpl_data['controller_path'] = str_replace(DIR_ROOT . '/', '', $args['block_controller']);
		$tmpl_data['parent_block'] = $parent_block['block_txt_id'];
		$tmpl_data['parent'] = array();
		$tmpl_data['parent']['id'] = $parent_block['instance_id'];
		$tmpl_data['parent']['name'] = $parent_block['block_txt_id'];
		$tmpl_data['parent']['tpl_path'] = $args['parent_tpl'];
		$tmpl_data['parent']['controller'] = $parent_block['controller'];
		$tmpl_data['parent']['controller_path'] = str_replace(DIR_ROOT . '/', '', $args['parent_controller']);
		
		$this->view->batchAssign($tmpl_data);
		
		$this->processTemplate('common/template_debug.tpl');
		
	}
}
?>