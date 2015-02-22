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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerCommonTemplateDebug extends AController {
	public $data = array();
	public function main() {
		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		
		$args = func_get_arg(1);
		$block_details = $this->layout->getBlockDetails($args['block_id']);
		$block_tmpl = $this->layout->getBlockTemplate($args['block_id']);
		$parent_block = $this->layout->getBlockDetails($block_details['parent_instance_id']);
		$parent_tmpl = $this->layout->getBlockTemplate($block_details['parent_instance_id']);
		

		$this->data['id'] = $args['block_id'];
		$this->data['name'] = $block_details['block_txt_id'];
		$this->data['tpl_path'] = $args['block_tpl'];
		$this->data['controller'] = $block_details['controller'];
		$this->data['controller_path'] = str_replace(DIR_ROOT . '/', '', $args['block_controller']);
		$this->data['parent_block'] = $parent_block['block_txt_id'];
		$this->data['parent'] = array();
		$this->data['parent']['id'] = $parent_block['instance_id'];
		$this->data['parent']['name'] = $parent_block['block_txt_id'];
		$this->data['parent']['tpl_path'] = $args['parent_tpl'];
		$this->data['parent']['controller'] = $parent_block['controller'];
		$this->data['parent']['controller_path'] = str_replace(DIR_ROOT . '/', '', $args['parent_controller']);
		
		$this->view->batchAssign($this->data);
		
		$this->processTemplate('common/template_debug.tpl');
		//init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
?>