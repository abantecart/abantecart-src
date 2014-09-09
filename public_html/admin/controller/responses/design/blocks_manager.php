<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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

class ControllerResponsesDesignBlocksManager extends AController {

  public function Main() {
    $this->extensions->hk_InitData($this,__FUNCTION__);

    $section_id = $this->request->get['section_id'];
    $layout = new ALayoutManager();
    $installedBlocks = $layout->getInstalledBlocks();
    $availableBlocks = [];

    foreach ($installedBlocks as $k => $block) {
      if ($block['parent_block_id'] == $section_id) {
        $availableBlocks[] = [
          'id' => $block['block_id'] . '_' . $block['custom_block_id'],
          'block_id' => $block['block_id'],
          'block_txt_id' => $block['block_txt_id'],
          'block_name' => $block['block_name'],
          'custom_block_id' => $block['custom_block_id'],
          'controller' => $block['controller'],
          'template' => $block['template'],
        ];
      }
    }

    $view = new AView($this->registry, 0);

    $view->assign('blocks', $availableBlocks);
    
    $blocks = $view->fetch('responses/design/blocks_manager.tpl');

    //update controller data
    $this->extensions->hk_UpdateData($this,__FUNCTION__);

    $this->response->setOutput($blocks);

  }

}